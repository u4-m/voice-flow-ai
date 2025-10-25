<?php

namespace Tests\Feature;

use App\Jobs\ProcessTranscription;
use App\Models\Transcriptions;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TranscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Queue::fake();
    }

    /** @test */
    public function it_creates_transcription_with_authenticated_user()
    {
        // Create a test user and get auth token
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        // Simulate file upload
        $file = UploadedFile::fake()->create('test.mp3', 1000); // 1MB file
        
        // Make API request with authentication
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/transcriptions', [
            'title' => 'Test Transcription',
            'audio_file' => $file,
        ]);

        // Assert the response
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'title',
                    'status',
                    'created_at',
                    'updated_at',
                ]
            ]);
        
        // Get the created transcription
        $transcription = Transcriptions::latest()->first();
        
        // Assert the transcription was created with the correct user
        $this->assertDatabaseHas('transcriptions', [
            'title' => 'Test Transcription',
            'user_id' => $user->id,
            'status' => Transcriptions::STATUS_PENDING,
        ]);

        // Assert the file was stored
        Storage::disk('public')->assertExists($transcription->audio_file_path);
    }

    /** @test */
    public function it_requires_authentication_to_create_transcription()
    {
        // Simulate file upload without authentication
        $file = UploadedFile::fake()->create('test.mp3', 1000);
        
        // Make API request without authentication
        $response = $this->postJson('/api/transcriptions', [
            'title' => 'Unauthorized Test',
            'audio_file' => $file,
        ]);

        // Assert unauthorized response
        $response->assertStatus(401);
        $this->assertDatabaseCount('transcriptions', 0);
    }

    /** @test */
    public function it_processes_transcription_successfully()
    {
        // Create a test user and transcription
        $user = User::factory()->create();
        $transcription = Transcriptions::factory()->create([
            'user_id' => $user->id,
            'status' => Transcriptions::STATUS_PENDING,
            'transcription' => null,
        ]);
        
        // Create a fake audio file
        Storage::fake('public');
        $file = UploadedFile::fake()->create('test.mp3', 1000);
        $path = $file->store('transcription-audios', 'public');
        
        // Update the transcription with the file path
        $transcription->update(['audio_file_path' => $path]);
        
        // Process the transcription
        $job = new ProcessTranscription($transcription);
        $job->handle();
        
        // Refresh the model from the database
        $transcription->refresh();

        // Assert the status was updated
        $this->assertEquals(Transcriptions::STATUS_COMPLETED, $transcription->status);
        $this->assertNotNull($transcription->transcription);
    }
}
