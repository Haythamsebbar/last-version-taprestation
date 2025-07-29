@extends('layouts.app')

@section('title', 'Résultats de recherche - Prestataires')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header des résultats -->
    <div class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Résultats de recherche</h1>
                    <p class="text-gray-600 mt-1">
                        {{ $prestataires->total() }} prestataire(s) trouvé(s)
                        @if(request('keyword'))
                            pour "{{ request('keyword') }}"
                        @endif
                    </p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <!-- Bouton de recherche supprimé -->
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Filtres rapides -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Affiner la recherche</h3>
                    
                    <!-- Filtres actifs -->
                    @if(request()->hasAny(['keyword', 'category_id', 'skills', 'min_price', 'max_price', 'min_rating']))
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Filtres actifs</h4>
                            <div class="space-y-2">
                                @if(request('keyword'))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Mot-clé: {{ request('keyword') }}
                                        <a href="{{ request()->fullUrlWithQuery(['keyword' => null]) }}" class="ml-2 text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                
                                @if(request('category_id'))
                                    @php
                                        $category = $categories->where('id', request('category_id'))->first();
                                    @endphp
                                    @if($category)
                                        <span>
                                            {{ $category->name }}
                                            <a href="{{ request()->fullUrlWithQuery(['category_id' => null]) }}">
                                                (supprimer)
                                            </a>
                                        </span>
                                    @endif
                                @endif
                                
                                <!-- Filtres de prix supprimés -->
                            </div>
                        </div>
                    @endif
                    
                    <!-- Tri rapide -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Trier par</h4>
                        <div class="space-y-2">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'relevance']) }}" 
                               class="block px-3 py-2 text-sm rounded {{ request('sort_by', 'relevance') === 'relevance' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:bg-gray-100' }}">
                                Pertinence
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'distance']) }}" 
                               class="block px-3 py-2 text-sm rounded {{ request('sort_by') === 'distance' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:bg-gray-100' }}">
                                Distance
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'rating']) }}" 
                               class="block px-3 py-2 text-sm rounded {{ request('sort_by') === 'rating' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:bg-gray-100' }}">
                                Note
                            </a>
                            <!-- Option de tri par prix supprimée -->
                        </div>
                    </div>
                    
                    <!-- Statistiques -->
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Statistiques</h4>
                        <div class="space-y-1 text-sm text-gray-600">
                            <div>{{ $prestataires->total() }} prestataires</div>
                            @if(request('latitude') && request('longitude'))
                                <div>Dans un rayon de {{ request('radius', 50) }} km</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Résultats -->
            <div class="lg:col-span-3">
                @if($prestataires->count() > 0)
                    <div class="space-y-6">
                        @foreach($prestataires as $prestataire)
                            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition duration-300 overflow-hidden">
                                <div class="p-6">
                                    <div class="flex flex-col sm:flex-row">
                                        <!-- Photo et infos principales -->
                                        <div class="flex-shrink-0 mb-4 sm:mb-0 sm:mr-6">
                                            <div class="relative w-24 h-24 mx-auto sm:mx-0">
                                                @if($prestataire->photo)
                                                    <img src="{{ asset('storage/' . $prestataire->photo) }}" alt="{{ $prestataire->user->name }}" class="w-full h-full object-cover rounded-full">
                                                @elseif($prestataire->user->avatar)
                                                    <img src="{{ asset('storage/' . $prestataire->user->avatar) }}" alt="{{ $prestataire->user->name }}" class="w-full h-full object-cover rounded-full">
                                                @elseif($prestataire->user->profile_photo_url)
                                                    <img src="{{ $prestataire->user->profile_photo_url }}" alt="{{ $prestataire->user->name }}" class="w-full h-full object-cover rounded-full">
                                                @else
                                                    <div class="w-full h-full bg-gray-300 rounded-full flex items-center justify-center">
                                                        <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                @if($prestataire->isVerified())
                                                    <div class="absolute -top-1 -right-1 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center border-2 border-white">
                                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Contenu principal -->
                                        <div class="flex-1">
                                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center mb-2">
                                                        <h3 class="text-xl font-semibold text-gray-900 mr-3">
                                                            <a href="{{ route('prestataires.show', $prestataire) }}" class="hover:text-blue-600 transition duration-300">
                                                                {{ $prestataire->user->name }}
                                                            </a>
                                                        </h3>
                                                        @if($prestataire->isVerified())
                                                            <span class="inline-flex items-center bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                                </svg>
                                                                Vérifié
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    <p class="text-gray-600 mb-2">
                                                        <i class="fas fa-briefcase mr-2"></i>{{ $prestataire->secteur_activite }}
                                                    </p>
                                                    
                                                    @if($prestataire->city)
                                                        <p class="text-gray-600 mb-2">
                                                            <i class="fas fa-map-marker-alt mr-2"></i>{{ $prestataire->city }}
                                                            @if(isset($prestataire->distance))
                                                                <span class="text-sm text-blue-600 ml-2">
                                                                    ({{ number_format($prestataire->distance, 1) }} km)
                                                                </span>
                                                            @endif
                                                        </p>
                                                    @endif
                                                    
                                                    @if($prestataire->description)
                                                        <p class="text-gray-700 mb-3 line-clamp-2">
                                                            {{ Str::limit($prestataire->description, 150) }}
                                                        </p>
                                                    @endif
                                                    
                                                    <!-- Compétences -->
                                                    @if($prestataire->skills->count() > 0)
                                                        <div class="mb-3">
                                                            <div class="flex flex-wrap gap-1">
                                                                @foreach($prestataire->skills->take(5) as $skill)
                                                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                                                        {{ $skill->name }}
                                                                    </span>
                                                                @endforeach
                                                                @if($prestataire->skills->count() > 5)
                                                                    <span class="inline-block bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">
                                                                        +{{ $prestataire->skills->count() - 5 }} autres
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <!-- Évaluations et prix -->
                                                <div class="text-right mt-4 sm:mt-0 sm:ml-6">
                                                    @if($prestataire->reviews->count() > 0)
                                                        <div class="flex items-center justify-end mb-2">
                                                            <div class="flex text-yellow-400 mr-2">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    @if($i <= $prestataire->reviews->avg('rating'))
                                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                                        </svg>
                                                                    @else
                                                                        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                                        </svg>
                                                                    @endif
                                                                @endfor
                                                            </div>
                                                            <span class="text-sm text-gray-600">
                                                                {{ number_format($prestataire->reviews->avg('rating'), 1) }} ({{ $prestataire->reviews->count() }} avis)
                                                            </span>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Informations financières supprimées -->
                                                    
                                                    @if($prestataire->average_delivery_time)
                                                        <div class="text-sm text-gray-600 mb-3">
                                                            <i class="fas fa-clock mr-1"></i>{{ $prestataire->average_delivery_time }} jours
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Actions -->
                                            <div class="flex flex-col sm:flex-row gap-3 mt-4 pt-4 border-t">
                                                <a href="{{ route('prestataires.show', $prestataire) }}" 
                                                   class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300">
                                                    <i class="fas fa-eye mr-2"></i>Voir le profil
                                                </a>
                                                
                                                @auth
                                                    @if(auth()->user()->role === 'client')
                                                        <!-- Boutons supprimés -->
                                                    @endif
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $prestataires->links() }}
                    </div>
                @else
                    <!-- Aucun résultat -->
                    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun prestataire trouvé</h3>
                        <p class="text-gray-600 mb-6">Essayez de modifier vos critères de recherche ou d'élargir votre zone géographique.</p>
                        
                        <div class="space-y-3">
                            <!-- Boutons "Nouvelle recherche" et "Voir tous les prestataires" supprimés -->
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush