@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white mr-3">
                    <i class="fas fa-heart"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-blue-800">Prestataires suivis</h1>
                    <p class="mt-1 text-sm text-gray-600">Retrouvez tous les prestataires que vous suivez.</p>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('client.dashboard') }}" class="bg-blue-100 hover:bg-blue-200 text-blue-800 font-bold px-4 py-2 rounded-lg text-sm transition duration-200 flex items-center border border-blue-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour au tableau de bord
                </a>
            </div>
        </div>
        

        
        <!-- Prestataires suivis -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white mr-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-blue-800">Tous les prestataires suivis</h2>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-gray-600">{{ $prestataires->total() }} prestataire(s) suivi(s)</div>
                    <div class="flex items-center space-x-2">
                        <label for="sort" class="text-sm text-gray-600">Trier par:</label>
                        <select id="sort" name="sort" class="border border-gray-300 rounded-md px-3 py-1 text-sm" onchange="window.location.href='{{ route('client.prestataire-follows.index') }}?sort=' + this.value">
                            <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Plus récents</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Plus anciens</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg border border-blue-200">
            @if($prestataires->isEmpty())
                <div class="empty-state p-10">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-heart text-3xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Aucun abonnement pour le moment</h3>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto">Découvrez et suivez vos prestataires préférés pour rester informé de leurs dernières activités.</p>
                    <a href="{{ route('client.prestataires.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-lg transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i>
                        Découvrir des prestataires
                    </a>
                </div>
            @else
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($prestataires as $prestataire)
                            <div class="bg-blue-50 rounded-lg border border-blue-200 overflow-hidden hover:shadow-lg transition duration-200">
                            <div class="p-6">
                                <div class="flex items-center mb-4">
                                    <div class="mr-4 relative">
                                        @if($prestataire->photo)
                                            <img src="{{ asset('storage/' . $prestataire->photo) }}" alt="{{ $prestataire->user->name }}" 
                                                class="w-16 h-16 rounded-full object-cover">
                                        @elseif($prestataire->user->avatar)
                                            <img src="{{ asset('storage/' . $prestataire->user->avatar) }}" alt="{{ $prestataire->user->name }}" 
                                                class="w-16 h-16 rounded-full object-cover">
                                        @else
                                            <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        @endif
                                        @if($prestataire->isVerified())
                                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full flex items-center justify-center">
                                                <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-lg font-semibold text-gray-800">{{ $prestataire->user->name }}</h3>
                                            @if($prestataire->isVerified())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    Vérifié
                                                </span>
                                            @endif
                                        </div>
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
                                <div class="mt-4 flex items-center justify-between">
                                    <a href="{{ route('client.browse.prestataire', $prestataire->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors duration-200">
                                        Voir profil
                                    </a>
                                    <form action="{{ route('client.prestataire-follows.unfollow', $prestataire->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors duration-200" onclick="return confirm('Êtes-vous sûr de vouloir vous désabonner ?')">
                                            Se désabonner
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-8 px-6 pb-6">
                        {{ $prestataires->links() }}
                    </div>
                </div>
            @endif
            </div>
        </div>
    </div>
</div>
@endsection