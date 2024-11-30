<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use FFMpeg;

class TranscodeVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $videoPath;
    public $outputBasePath;

    /**
     * Create a new job instance.
     */
    public function __construct($videoPath)
    {
        $this->videoPath = $videoPath;
        $this->outputBasePath = pathinfo($videoPath, PATHINFO_FILENAME);
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $resolutions = [
            '720p' => 720,
            '480p' => 480,
            '360p' => 360,
        ];

        foreach ($resolutions as $label => $height) {
            $outputPath = "videos/{$this->outputBasePath}-{$label}.mp4";

            // Use FFmpeg to transcode the video
            $command = "ffmpeg -i " . escapeshellarg(storage_path("app/{$this->videoPath}")) .
                " -vf scale=-1:{$height} -c:v libx264 -crf 23 -preset veryfast -c:a aac -b:a 128k " .
                escapeshellarg(storage_path("app/{$outputPath}"));

            shell_exec($command);

            // Verify the file was created and store it
            if (Storage::exists($outputPath)) {
                // Log or handle any additional processing if needed
            }
        }
    }
}