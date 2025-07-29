@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Mon Profil Professionnel</h1>
        
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <form action="{{ route('prestataire.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Photo de profil -->
                        <div class="sm:col-span-2 flex items-start space-x-6">
                            <div class="flex-shrink-0">
                                @if (Auth::user()->profile_photo_url)
                        <img class="h-24 w-24 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="Photo de profil">
                    @else
                                    <div class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center">
                                        <svg class="h-12 w-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <label for="photo" class="block text-sm font-medium text-gray-700">Photo professionnelle</label>
                                <div class="mt-1 flex items-center">
                                    <input type="file" id="photo" name="photo" class="mt-1 block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-blue-50 file:text-blue-700
                                        hover:file:bg-blue-100
                                    ">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">JPG, PNG ou GIF. 2 Mo maximum.</p>
                                @error('photo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Informations de base -->
                        <div class="sm:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de base</h3>
                        </div>
                        
                        <!-- Nom -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nom complet</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Adresse email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Informations professionnelles -->
                        <div class="sm:col-span-2 mt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations professionnelles</h3>
                        </div>
                        
                        <!-- Secteur d'activité -->
                        <div>
                            <label for="secteur_activite" class="block text-sm font-medium text-gray-700">Secteur d'activité</label>
                            <input type="text" name="secteur_activite" id="secteur_activite" value="{{ old('secteur_activite', $prestataire->secteur_activite ?? '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('secteur_activite')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Zone de service -->
                        <div>
                            <label for="service_area" class="block text-sm font-medium text-gray-700">Zone de service</label>
                            <input type="text" name="service_area" id="service_area" value="{{ old('service_area', $prestataire->service_area ?? '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <p class="mt-1 text-sm text-gray-500">Ex: Paris et sa banlieue, Région PACA, France entière...</p>
                            @error('service_area')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Section tarifs supprimée pour des raisons de confidentialité -->
                        
                        <!-- Délai moyen de livraison -->
                        <div>
                            <label for="average_delivery_time" class="block text-sm font-medium text-gray-700">Délai moyen de livraison</label>
                            <input type="text" name="average_delivery_time" id="average_delivery_time" value="{{ old('average_delivery_time', $prestataire->average_delivery_time ?? '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <p class="mt-1 text-sm text-gray-500">Ex: 48h, 1 semaine, Variable selon projet...</p>
                            @error('average_delivery_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Portfolio -->
                        <div class="sm:col-span-2">
                            <label for="portfolio" class="block text-sm font-medium text-gray-700">Lien vers portfolio/site web</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">https://</span>
                                <input type="text" name="portfolio" id="portfolio" value="{{ old('portfolio', $prestataire->portfolio ? str_replace('https://', '', $prestataire->portfolio) : '') }}" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300">
                            </div>
                            @error('portfolio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Compétences -->
                        <div class="sm:col-span-2">
                            <label for="competences" class="block text-sm font-medium text-gray-700">Compétences</label>
                            <textarea id="competences" name="competences" rows="3" class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('competences', $prestataire->competences ?? '') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Listez vos compétences principales, séparées par des virgules.</p>
                            @error('competences')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Description -->
                        <div class="sm:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" name="description" rows="5" class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('description', $prestataire->description ?? '') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Décrivez votre activité, votre expérience et ce qui vous distingue.</p>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
        
        <div class="mt-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Sécurité du compte</h2>
            
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Changer de mot de passe</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester sécurisé.</p>
                    </div>
                    <div class="mt-5">
                        <a href="{{ route('password.request') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Modifier le mot de passe
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection