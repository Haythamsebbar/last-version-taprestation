@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Ajouter une disponibilité</h1>
            <p class="text-gray-600">Pour le service: "{{ $service->title }}"</p>
        </div>

        <div class="bg-white shadow-md rounded-lg p-8">
            <form action="{{ route('prestataire.services.availabilities.store', $service) }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Heure de début</label>
                    <input type="datetime-local" name="start_time" id="start_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                </div>

                <div class="mb-6">
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">Heure de fin</label>
                    <input type="datetime-local" name="end_time" id="end_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                </div>

                <div class="flex items-center justify-end mt-8">
                    <a href="{{ route('prestataire.services.availabilities.index', $service) }}" class="text-gray-600 hover:text-gray-900 mr-4">Annuler</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection