<?php

use App\Models\Transcriptions;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/transcriptions/{transcription}/download', function (Transcriptions $transcription) {
    // Check if the transcription exists and is completed
    if ($transcription->status !== Transcriptions::STATUS_COMPLETED) {
        abort(404, 'Transcription not available for download.');
    }

    // Get the transcription content
    $content = $transcription->transcription;
    
    if (empty($content)) {
        abort(404, 'No transcription content available.');
    }

    // Create a temporary file in-memory
    $filename = str_replace(' ', '_', $transcription->title) . '_transcript.txt';
    
    // Return the file as a download response
    return response()->streamDownload(
        function () use ($content) {
            echo $content;
        },
        $filename,
        [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]
    );
})->name('transcriptions.download');
