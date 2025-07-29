@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/video-create.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endpush

@section('content')
<div class="card-container">
    <div class="card-header">
        <i class="fas fa-camera"></i>
        <h1>Ajouter une vidéo</h1>
    </div>

    <div class="tabs">
        <div class="tab active" onclick="openTab(event, 'record-video')">🎥 Enregistrer une vidéo</div>
        <div class="tab" onclick="openTab(event, 'upload-video')">📁 Importer une vidéo</div>
    </div>

    <div id="record-video" class="tab-content active">
        <form action="{{ route('prestataire.videos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="video-recorder-container">
                <div style="position: relative;">
                    <video id="live-video" width="480" height="270" autoplay muted></video>
                    <div id="recording-timer" class="recording-timer" style="display:none;">0s</div>
                    <div id="camera-permission-error" class="alert alert-danger" style="display:none;"></div>
                </div>
                <video id="recorded-video-preview" width="480" height="270" controls style="display: none;"></video>
                <div class="action-buttons">
                    <button type="button" id="start-record-btn" class="btn btn-success"><i class="fas fa-play"></i> Démarrer</button>
                    <button type="button" id="stop-record-btn" class="btn btn-danger" disabled><i class="fas fa-stop"></i> Arrêter</button>
                    <button type="button" id="use-video-btn" class="btn btn-info" style="display: none;"><i class="fas fa-check"></i> Utiliser cette vidéo</button>
                    <button type="button" id="diagnoseCameras" class="btn btn-info btn-sm ml-2">Diagnostiquer les caméras</button>
                </div>
            </div>
            <div class="form-group">
                <label for="title_record">Titre</label>
                <input type="text" name="title_record" id="title_record" class="form-control" required placeholder="Ex: Présentation de mon service de plomberie">
            </div>
            <div class="form-group">
                <label for="description_record">Description</label>
                <textarea name="description_record" id="description_record" class="form-control" required placeholder="Une brève description de ce que les clients verront dans la vidéo." maxlength="300"></textarea>
            </div>
            <input type="hidden" name="recorded_video_data" id="recorded_video_data">
            <button type="submit" class="btn btn-primary">Enregistrer la vidéo</button>
        </form>
    </div>

    <div id="upload-video" class="tab-content">
        <form action="{{ route('prestataire.videos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="video-uploader-container">
                <div class="file-upload-area" id="drop-area">
                    <input type="file" name="video" id="video" accept=".mp4,.webm,.mov" style="display: none;">
                    <i class="fas fa-upload"></i>
                    <p>Choisir une vidéo depuis vos fichiers ou glissez-déposez ici.</p>
                    <p id="file-name"></p>
                </div>
                <video id="video-preview" controls style="display:none; max-width: 480px; margin-top: 10px;"></video>
            </div>
            <div class="form-group">
                <label for="title_upload">Titre</label>
                <input type="text" name="title_upload" id="title_upload" class="form-control" required placeholder="Ex: Présentation de mon service de plomberie">
            </div>
            <div class="form-group">
                <label for="description_upload">Description</label>
                <textarea name="description_upload" id="description_upload" class="form-control" required placeholder="Une brève description de ce que les clients verront dans la vidéo." maxlength="300"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Importer la vidéo</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/video-recorder.js'])
@endpush