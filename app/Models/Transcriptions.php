<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transcriptions extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'title',
        'audio_file_path',
        'transcription',
        'status',
        'error_message',
    ];

    /**
     * Get all available status options.
     *
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
        ];
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        // Set the authenticated user's ID when creating a new transcription
        static::creating(function ($transcription) {
            if (auth()->check()) {
                $transcription->user_id = auth()->id();
            }
        });

        // Dispatch the processing job after creation
        static::created(function ($transcription) {
            if ($transcription->status === self::STATUS_PENDING) {
                ProcessTranscription::dispatch($transcription);
            }
        });
    }

    /**
     * Get the user that owns the transcription.
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'System',
            'email' => 'system@example.com',
        ]);
    }

    /**
     * Get the project that owns the transcription.
     */
    public function project()
    {
        return $this->belongsTo(Project::class)->withDefault([
            'name' => 'No Project',
        ]);
    }

    /**
     * Mark the transcription as processing.
     */
    public function markAsProcessing()
    {
        $this->update(['status' => self::STATUS_PROCESSING]);
    }

    /**
     * Mark the transcription as completed with the given transcription text.
     */
    public function markAsCompleted(string $transcriptionText)
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'transcription' => $transcriptionText,
            'error_message' => null,
        ]);
    }

    /**
     * Mark the transcription as failed with the given error message.
     */
    public function markAsFailed(string $error)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $error,
        ]);
    }
}
