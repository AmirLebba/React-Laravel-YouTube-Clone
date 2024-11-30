<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Video; // Ensure you have a Video model created
use App\Jobs\TranscodeVideo;

class VideoController extends Controller
{
    /**
     * Display a listing of all videos.
     */
    public function index()
{
    $videos = Video::all()->map(function ($video) {
        $video->url = asset('storage/' . $video->path); // Generate the full URL
        return $video;
    });

    return response()->json($videos);
}

    /**
     * Store a newly uploaded video.
     */
    public function store(Request $request)
    {
        $request->validate([
            'video' => 'required|mimes:mp4,mkv,avi|max:51200', // Max size: 50MB
        ]);

        // Save the uploaded video
        $file = $request->file('video');
        $path = $file->store('videos');

        // Create a new video record
        $video = Video::create([
            'title' => $file->getClientOriginalName(),
            'path' => $path,
        ]);

        // Dispatch the transcoding job
        TranscodeVideo::dispatch($video->path);

        return response()->json([
            'message' => 'Video uploaded successfully and queued for processing.',
            'video' => $video,
        ]);
    }

    /**
     * Display a specific video (streaming).
     */
    public function show($id)
    {
        // Find the video by its ID
        $video = Video::find($id);

        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        // Return video metadata, including a full URL to the video file
        return response()->json([
            'id' => $video->id,
            'title' => $video->title,
            'url' => asset('storage/' . $video->path), // Generates full URL for the video
            'created_at' => $video->created_at,
            'updated_at' => $video->updated_at,
        ]);
    }

    /**
     * Remove the specified video from storage and database.
     */
    public function destroy($id)
    {
    $video = Video::findOrFail($id);

    // Delete video file(s) from storage
    $baseFilename = pathinfo($video->path, PATHINFO_FILENAME);
    $resolutions = ['720p', '480p', '360p'];
    foreach ($resolutions as $resolution) {
        $filename = "videos/{$baseFilename}-{$resolution}.mp4";
        if (Storage::exists($filename)) {
            Storage::delete($filename);
        }
    }

    // Delete original video
    if (Storage::exists($video->path)) {
        Storage::delete($video->path);
    }

    // Delete the database record
    $video->delete();

    return response()->json(['message' => 'Video deleted successfully'], 200);
    }

}