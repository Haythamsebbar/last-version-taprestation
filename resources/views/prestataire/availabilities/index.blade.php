@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Gérer les disponibilités pour le service : {{ $service->title }}</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Ajouter un nouveau créneau</div>
        <div class="card-body">
            <form action="{{ route('prestataire.availabilities.store', $service) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="start_time">Heure de début</label>
                    <input type="datetime-local" name="start_time" id="start_time" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="end_time">Heure de fin</label>
                    <input type="datetime-local" name="end_time" id="end_time" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Créneaux disponibles</div>
        <div class="card-body">
            @if($availabilities->isEmpty())
                <p>Aucun créneau disponible pour le moment.</p>
            @else
                <ul class="list-group">
                    @foreach($availabilities as $availability)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            De {{ $availability->start_time->format('d/m/Y H:i') }} à {{ $availability->end_time->format('d/m/Y H:i') }}
                            <form action="{{ route('prestataire.availabilities.destroy', $availability) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection