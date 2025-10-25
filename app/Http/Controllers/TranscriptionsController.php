<?php

namespace App\Http\Controllers;

use App\Models\Transcriptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TranscriptionsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'audio_file' => 'required|file|mimes:mp3,wav,m4a,ogg|max:10240',
        ]);

        // Store the uploaded file
        $path = $request->file('audio_file')->store('transcription-audios', 'public');

        // Create the transcription record
        $transcription = Transcriptions::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'audio_file_path' => $path,
            'status' => Transcriptions::STATUS_PENDING,
        ]);

        return response()->json([
            'message' => 'Transcription created successfully',
            'data' => $transcription
        ], 201);
    }

    /**
     * Display a listing of the authenticated user's transcriptions.
     */
    public function index()
    {
        $transcriptions = auth()->user()->transcriptions()
            ->latest()
            ->paginate(10);

        return response()->json($transcriptions);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transcriptions $transcription)
    {
        $this->authorize('view', $transcription);
        
        return response()->json($transcription);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transcriptions $transcription)
    {
        $this->authorize('delete', $transcription);
        
        // Delete the audio file
        Storage::disk('public')->delete($transcription->audio_file_path);
        
        // Delete the transcription record
        $transcription->delete();

        return response()->json(['message' => 'Transcription deleted successfully']);
    }
}
