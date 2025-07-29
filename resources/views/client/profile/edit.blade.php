@extends('layouts.app')

@section('content')
<div class="py-10">
    <header>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold leading-tight text-gray-900">Mon Profil</h1>
            <p class="mt-2 text-sm text-gray-600">Gérez vos informations personnelles et vos préférences</p>
        </div>
    </header>
    <main>
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @if (session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative">
                        {{ session('success') }}
                    </div>
                @endif
                
                <form action="{{ route('client.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <!-- Informations de base -->
                        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                            <div class="md:grid md:grid-cols-3 md:gap-6">
                                <div class="md:col-span-1">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">Informations personnelles</h3>
                                    <p class="mt-1 text-sm text-gray-500">Ces informations seront visibles par les prestataires.</p>
                                </div>
                                <div class="mt-5 md:mt-0 md:col-span-2">
                                    <div class="grid grid-cols-6 gap-6">
                                        <!-- Avatar -->
                                        <div class="col-span-6">
                                            <label class="block text-sm font-medium text-gray-700">Photo de profil</label>
                                            <div class="mt-1 flex items-center space-x-5">
                                                @if(auth()->user()->client && auth()->user()->client->avatar)
                                                    <img class="h-20 w-20 rounded-full" src="{{ Storage::url(auth()->user()->client->avatar) }}" alt="Avatar actuel">
                                                @else
                                                    <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-xl font-medium text-gray-700">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                                <div class="flex flex-col space-y-2">
                                                    <input type="file" name="avatar" id="avatar" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                                    @if(auth()->user()->client && auth()->user()->client->avatar)
                                                        <button type="button" onclick="deleteAvatar()" class="text-sm text-red-600 hover:text-red-500">Supprimer la photo</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Nom -->
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="name" class="block text-sm font-medium text-gray-700">Nom complet</label>
                                            <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                                        </div>
                                        
                                        <!-- Email -->
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                            <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                                        </div>
                                        
                                        <!-- Téléphone -->
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                            <input type="tel" name="phone" id="phone" value="{{ old('phone', auth()->user()->client->phone ?? '') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                        
                                        <!-- Localisation -->
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="location" class="block text-sm font-medium text-gray-700">Localisation</label>
                                            <input type="text" name="location" id="location" value="{{ old('location', auth()->user()->client->location ?? '') }}" placeholder="Ville, Région" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                        
                                        <!-- Bio -->
                                        <div class="col-span-6">
                                            <label for="bio" class="block text-sm font-medium text-gray-700">Présentation</label>
                                            <textarea name="bio" id="bio" rows="4" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Présentez-vous en quelques mots...">{{ old('bio', auth()->user()->client->bio ?? '') }}</textarea>
                                            <p class="mt-2 text-sm text-gray-500">Décrivez vos besoins habituels, vos préférences ou votre secteur d'activité.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Préférences -->
                        <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
                            <div class="md:grid md:grid-cols-3 md:gap-6">
                                <div class="md:col-span-1">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">Préférences</h3>
                                    <p class="mt-1 text-sm text-gray-500">Configurez vos préférences de communication et de notifications.</p>
                                </div>
                                <div class="mt-5 md:mt-0 md:col-span-2">
                                    <div class="space-y-6">
                                        <!-- Notifications -->
                                        <fieldset>
                                            <legend class="text-base font-medium text-gray-900">Notifications</legend>
                                            <div class="mt-4 space-y-4">
                                                <div class="flex items-start">
                                                    <div class="flex items-center h-5">
                                                        <input id="email_notifications" name="email_notifications" type="checkbox" {{ old('email_notifications', auth()->user()->client->email_notifications ?? true) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                                    </div>
                                                    <div class="ml-3 text-sm">
                                                        <label for="email_notifications" class="font-medium text-gray-700">Notifications par email</label>
                                                        <p class="text-gray-500">Recevoir des notifications pour les nouveaux messages et offres.</p>
                                                    </div>
                                                </div>
                                                
                                                <div class="flex items-start">
                                                    <div class="flex items-center h-5">
                                                        <input id="sms_notifications" name="sms_notifications" type="checkbox" {{ old('sms_notifications', auth()->user()->client->sms_notifications ?? false) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                                    </div>
                                                    <div class="ml-3 text-sm">
                                                        <label for="sms_notifications" class="font-medium text-gray-700">Notifications SMS</label>
                                                        <p class="text-gray-500">Recevoir des SMS pour les messages urgents.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        
                                        <!-- Visibilité du profil -->
                                        <fieldset>
                                            <legend class="text-base font-medium text-gray-900">Visibilité</legend>
                                            <div class="mt-4 space-y-4">
                                                <div class="flex items-start">
                                                    <div class="flex items-center h-5">
                                                        <input id="public_profile" name="public_profile" type="checkbox" {{ old('public_profile', auth()->user()->client->public_profile ?? true) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                                    </div>
                                                    <div class="ml-3 text-sm">
                                                        <label for="public_profile" class="font-medium text-gray-700">Profil public</label>
                                                        <p class="text-gray-500">Permettre aux prestataires de voir votre profil et vos avis.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Boutons d'action -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('client.dashboard') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Annuler
                            </a>
                            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Enregistrer les modifications
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
function deleteAvatar() {
    if (confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')) {
        fetch('{{ route('client.profile.delete-avatar') }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la suppression de la photo.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la suppression de la photo.');
        });
    }
}
</script>
@endsection