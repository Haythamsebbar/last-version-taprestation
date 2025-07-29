<?php

namespace Database\Seeders;

use App\Models\Prestataire;
use App\Models\Video;
use App\Models\VideoComment;
use App\Models\VideoLike;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        Video::truncate();
        VideoLike::truncate();
        VideoComment::truncate();

        Schema::enableForeignKeyConstraints();

        $prestataire = Prestataire::first();

        if ($prestataire) {
            for ($i = 0; $i < 10; $i++) {
                Video::create([
                    'prestataire_id' => $prestataire->id,
                    'title' => 'Vidéo de test ' . ($i + 1),
                    'description' => 'Description de la vidéo de test ' . ($i + 1),
                    'video_path' => 'videos/test_video.mp4', // Chemin de la vidéo de test
                    'is_public' => true,
                    'status' => 'approved',
                    'created_at' => now(),
                    'duration' => 15, // Mettez une durée approximative
                ]);
            }
        }
    }
}