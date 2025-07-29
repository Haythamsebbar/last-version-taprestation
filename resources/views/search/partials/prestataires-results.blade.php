@if($prestataires->count() > 0)
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">
                {{ $prestataires->total() }} prestataire(s) trouvé(s)
            </h2>
            <div class="text-sm text-gray-600">
                Page {{ $prestataires->currentPage() }} sur {{ $prestataires->lastPage() }}
            </div>
        </div>
    </div>
    
    <div class="space-y-6 mt-6">
        @foreach($prestataires as $prestataire)
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition duration-300 overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row">
                        <!-- Photo et infos principales -->
                        <div class="flex-shrink-0 mb-4 sm:mb-0 sm:mr-6">
                            <div class="relative w-24 h-24 bg-gray-300 rounded-full mx-auto sm:mx-0 flex items-center justify-center overflow-hidden">
                                @if($prestataire->photo)
                                    <img src="{{ asset('storage/' . $prestataire->photo) }}" alt="{{ $prestataire->user->name }}" class="w-full h-full object-cover">
                                @elseif($prestataire->user->avatar)
                                    <img src="{{ asset('storage/' . $prestataire->user->avatar) }}" alt="{{ $prestataire->user->name }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                @endif
                                @if($prestataire->isVerified())
                                    <div class="absolute -top-1 -right-1 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
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
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
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
    @if($prestataires->hasPages())
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <div class="flex justify-center">
                {{ $prestataires->links() }}
            </div>
        </div>
    @endif
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