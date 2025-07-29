@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Prestataires suivis</h1>
                <p class="mt-1 text-sm text-gray-600">Retrouvez tous les prestataires que vous suivez.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('client.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Retour au tableau de bord
                </a>
            </div>
        </div>
        
        <!-- Services récents des prestataires suivis -->
        <div class="mb-10">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Nouveautés des prestataires suivis</h2>
            
            @if($recentServices->isEmpty())
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-600">Aucun nouveau service n'a été publié récemment par les prestataires que vous suivez.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($recentServices as $service)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200">
                            <div class="p-6">
                                <!-- Prestataire info -->
                                <div class="flex items-center mb-4">
                                    <div class="mr-3 relative">
                                        @if($service->prestataire->photo)
                                            <img src="{{ asset('storage/' . $service->prestataire->photo) }}" alt="{{ $service->prestataire->user->name }}" 
                                                class="w-10 h-10 rounded-full object-cover">
                                        @elseif($service->prestataire->user->avatar)
                                            <img src="{{ asset('storage/' . $service->prestataire->user->avatar) }}" alt="{{ $service->prestataire->user->name }}" 
                                                class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        @endif
                                        @if($service->prestataire->isVerified())
                                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full flex items-center justify-center">
                                                <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <div class="flex items-center gap-1">
                                            <h3 class="text-sm font-medium text-gray-800">{{ $service->prestataire->user->name }}</h3>
                                            @if($service->prestataire->isVerified())
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    Vérifié
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500">{{ $service->prestataire->sector }}</p>
                                    </div>
                                </div>
                                
                                <!-- Service info -->
                                <h4 class="text-lg font-semibold text-gray-800 mb-2">{{ $service->title }}</h4>
                                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $service->description }}</p>
                                
                                <div class="flex justify-between items-center mb-3">
                                    <!-- Prix supprimé pour des raisons de confidentialité -->
                                    <div class="text-sm text-gray-500">Délai: {{ $service->delivery_time }}</div>
                                </div>
                                
                                <!-- Catégories -->
                                <div class="mb-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($service->categories as $category)
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">{{ $category->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <!-- Boutons d'action -->
                                <div class="mt-4 flex space-x-2">
                                    <a href="{{ route('client.browse.prestataire', $service->prestataire->id) }}" class="flex-1 text-center bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-200">
                                        Voir profil
                                    </a>
                                    <a href="{{ route('client.browse.prestataire', $service->prestataire->id) }}#contact" class="flex-1 text-center bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-200">
                                        Contacter
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
        <!-- Prestataires suivis -->
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Tous les prestataires suivis</h2>
            <div class="flex justify-between items-center mb-4">
                <p class="text-gray-600">{{ $prestataires->total() }} prestataire(s) suivi(s)</p>
            </div>
            
            @if($prestataires->isEmpty())
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-700 mb-2">Vous ne suivez aucun prestataire</h2>
                    <p class="text-gray-600 mb-6">Explorez notre catalogue de prestataires et suivez ceux qui vous intéressent.</p>
                    <a href="{{ route('client.browse.prestataires') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Découvrir des prestataires
                    </a>
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
                                
                                <!-- Boutons d'action -->
                                <div class="mt-4 flex space-x-2">
                                    <a href="{{ route('client.browse.prestataire', $prestataire->id) }}" class="flex-1 text-center bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-200">
                                        Voir le profil
                                    </a>
                                    <form action="{{ route('client.follows.destroy', $prestataire->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-8">
                    {{ $prestataires->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection