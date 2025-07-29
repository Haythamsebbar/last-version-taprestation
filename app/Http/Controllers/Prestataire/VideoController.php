<?php

namespace App\Http\Controllers\Prestataire;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use App\Jobs\ProcessVideo;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use getID3;


class VideoController extends Controller
{
    public function index()
    {
        // Redirection vers la vue manage qui est plus complète
        return redirect()->route('prestataire.videos.manage');
    }

    public function manage()
    {
        $videos = collect(); // Initialise une collection vide
        if (Auth::user()->prestataire) {
            $videos = Auth::user()->prestataire->videos()->orderBy('created_at', 'desc')->get();
        }
        return view('prestataire.videos.manage', compact('videos'));
    }

    public function create()
    {
        return view('prestataire.videos.create');
    }

    public function store(StoreVideoRequest $request)
    {
        $path = null;

        try {
            if ($request->has('recorded_video_data') && !empty($request->input('recorded_video_data'))) {
                $videoData = $request->input('recorded_video_data');
                list($type, $videoData) = explode(';', $videoData);
                list(, $videoData)      = explode(',', $videoData);
                $videoData = base64_decode($videoData);
                $fileName = 'recorded_videos/' . uniqid() . '.webm';
                Storage::disk('public')->put($fileName, $videoData);
                $path = $fileName;
            } elseif ($request->hasFile('video')) {
                $path = $request->file('video')->store('temp_videos', 'public');
            }

            if ($path) {
                $title = $request->input('title_record') ?: $request->input('title_upload');
                $description = $request->input('description_record') ?: $request->input('description_upload');

                $video = Auth::user()->prestataire->videos()->create([
                    'title' => $title,
                    'description' => $description,
                    'video_path' => $path,
                    'duration' => 0, // Placeholder, will be updated by ProcessVideo job
                    'status' => 'approved',
                    'is_public' => true
                ]);

                ProcessVideo::dispatch($video);

                return redirect()->route('prestataire.videos.manage')->with('success', 'Vidéo enregistrée avec succès.');
            }

            return redirect()->back()->with('error', 'Aucune vidéo n\'a été fournie.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l’envoi de la vidéo: ' . $e->getMessage());
        }
    }

    public function edit(Video $video)
    {
        $this->authorize('update', $video);
        return view('prestataire.videos.edit', compact('video'));
    }

    public function update(UpdateVideoRequest $request, Video $video)
    {
        $video->update($request->validated());

        return redirect()->route('prestataire.videos.manage')->with('success', 'Vidéo mise à jour avec succès.');
    }

    public function destroy(Video $video)
    {
        $this->authorize('delete', $video);

        Storage::disk('public')->delete($video->video_path);
        $video->delete();

        return redirect()->route('prestataire.videos.manage')->with('success', 'Vidéo supprimée avec succès.');
    }
}