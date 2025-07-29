<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use getID3;

class ProcessVideo implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $videoPath = $this->video->video_path;
        $fullPath = Storage::disk('public')->path($videoPath);

        // Déplacer la vidéo vers le dossier final
        $newPath = 'videos/' . basename($videoPath);
        Storage::disk('public')->move($videoPath, $newPath);

        // Mettre à jour le chemin dans la base de données
        $this->video->video_path = $newPath;

        $getID3 = new getID3();
        $fileInfo = $getID3->analyze(Storage::disk('public')->path($newPath));
        $duration = $fileInfo['playtime_seconds'] ?? 0;

        $this->video->duration = $duration;
        $this->video->save();
    }
}
