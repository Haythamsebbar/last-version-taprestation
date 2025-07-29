@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Trouver un prestataire</h1>
    
    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Filtrer les prestataires</h2>
        
        <form action="{{ route('client.browse.prestataires') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Filtre par secteur d'activité -->
                <div>
                    <label for="sector" class="block text-sm font-medium text-gray-700 mb-1">Secteur d'activité</label>
                    <select name="sector" id="sector" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les secteurs</option>
                        @foreach($sectors as $sector)
                            <option value="{{ $sector }}" {{ isset($filters['sector']) && $filters['sector'] == $sector ? 'selected' : '' }}>{{ $sector }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filtre par compétence -->
                <div>
                    <label for="skill" class="block text-sm font-medium text-gray-700 mb-1">Compétence</label>
                    <select name="skill" id="skill" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Toutes les compétences</option>
                        @foreach($skills as $skill)
                            <option value="{{ $skill->id }}" {{ isset($filters['skill']) && $filters['skill'] == $skill->id ? 'selected' : '' }}>{{ $skill->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filtre par catégorie de service -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Catégorie de service</label>
                    <select name="category" id="category" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ isset($filters['category']) && $filters['category'] == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200">
                    Filtrer
                </button>
            </div>
        </form>
    </div>
    
    <!-- Résultats -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <p class="text-gray-600">{{ $prestataires->total() }} prestataire(s) trouvé(s)</p>
            
            @if(count(array_filter($filters)) > 0)
                <a href="{{ route('client.browse.prestataires') }}" class="text-blue-500 hover:text-blue-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Réinitialiser les filtres
                </a>
            @endif
        </div>
        
        @if($prestataires->isEmpty())
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Aucun prestataire trouvé</h2>
                <p class="text-gray-600 mb-6">Essayez de modifier vos critères de recherche.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($prestataires as $prestataire)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="mr-4">
                                    @if($prestataire->profile_photo)
                                        <img src="{{ asset('storage/' . $prestataire->profile_photo) }}" alt="{{ $prestataire->user->name }}" 
                                            class="w-16 h-16 rounded-full object-cover">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $prestataire->user->name }}</h3>
                                    <p class="text-gray-600">{{ $prestataire->sector }}</p>
                                </div>
                            </div>
                            
                            <!-- Compétences -->
                            <div class="mb-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($prestataire->skills->take(3) as $skill)
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ $skill->name }}</span>
                                    @endforeach
                                    
                                    @if($prestataire->skills->count() > 3)
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">+{{ $prestataire->skills->count() - 3 }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Description courte -->
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                {{ Str::limit($prestataire->description, 150) }}
                            </p>
                            
                            <!-- Services -->
                            @if($prestataire->services->isNotEmpty())
                                <div class="mb-4">
                                    <p class="text-sm text-gray-500">{{ $prestataire->services->count() }} service(s) proposé(s)</p>
                                </div>
                            @endif
                            
                            
                            <div class="mt-4">
                                <a href="{{ route('client.browse.prestataire', $prestataire->id) }}" class="block w-full text-center bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-200">
                                    Voir le profil
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $prestataires->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection