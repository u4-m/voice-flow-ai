<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transcriptions>
 */
class TranscriptionsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => $this->faker->sentence(),
            'audio_file_path' => 'transcription-audios/' . $this->faker->uuid() . '.mp3',
            'transcription' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement([
                \App\Models\Transcriptions::STATUS_PENDING,
                \App\Models\Transcriptions::STATUS_PROCESSING,
                \App\Models\Transcriptions::STATUS_COMPLETED,
                \App\Models\Transcriptions::STATUS_FAILED,
            ]),
            'error_message' => $this->faker->boolean(20) ? $this->faker->sentence() : null,
        ];
    }
}
