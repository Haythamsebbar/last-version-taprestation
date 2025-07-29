@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Gestion de vos disponibilités</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            Disponibilités hebdomadaires
        </div>
        <div class="card-body">
                        <form action="{{ route('prestataire.availability.updateWeekly') }}" method="POST">
                @csrf
                @method('PUT')

                <table class="table">
                    <thead>
                        <tr>
                            <th>Jour</th>
                            <th>Actif</th>
                            <th>Heure de début</th>
                            <th>Heure de fin</th>
                            <th>Durée du slot (minutes)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $daysMap = [
                                0 => 'Dimanche',
                                1 => 'Lundi',
                                2 => 'Mardi',
                                3 => 'Mercredi',
                                4 => 'Jeudi',
                                5 => 'Vendredi',
                                6 => 'Samedi',
                            ];
                        @endphp
                        @foreach($weeklyAvailability as $day)
                            <tr>
                                <td>{{ $daysMap[$day->day_of_week] ?? 'Jour inconnu' }}</td>
                                <td>
                                    <input type="checkbox" name="days[{{ $day->day_of_week }}][is_active]" {{ $day->is_active ? 'checked' : '' }}>
                                </td>
                                <td>
                                    <input type="time" name="days[{{ $day->day_of_week }}][start_time]" value="{{ $day->start_time }}" class="form-control">
                                </td>
                                <td>
                                    <input type="time" name="days[{{ $day->day_of_week }}][end_time]" value="{{ $day->end_time }}" class="form-control">
                                </td>
                                <td>
                                    <input type="number" name="days[{{ $day->day_of_week }}][slot_duration]" value="{{ $day->slot_duration }}" class="form-control">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </form>
        </div>
    </div>
</div>
@endsection