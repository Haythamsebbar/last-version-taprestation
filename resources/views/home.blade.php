@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white overflow-hidden">
    <!-- Élément graphique décoratif -->
    <div class="absolute top-0 right-0 w-1/3 h-full opacity-10 hero-decoration">
        <svg viewBox="0 0 400 400" class="w-full h-full">
            <defs>
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-width="1"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)" />
            <circle cx="100" cy="100" r="30" fill="currentColor" opacity="0.3"/>
            <circle cx="300" cy="200" r="20" fill="currentColor" opacity="0.2"/>
            <circle cx="200" cy="300" r="25" fill="currentColor" opacity="0.25"/>
        </svg>
    </div>
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
        <div class="text-center relative z-10">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Trouvez le <span class="text-yellow-400">prestataire parfait</span><br>
                pour vos projets
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-blue-100 max-w-3xl mx-auto">
                Mise en relation sécurisée et efficace entre clients et prestataires de services professionnels
            </p>
            
            <!-- Barre de recherche -->
            <div class="max-w-4xl mx-auto mb-12">
                <!-- Label au-dessus de la recherche -->
                <div class="text-center mb-4">
                    <p class="text-lg text-blue-100 font-medium">Rechercher un service près de chez vous</p>
                </div>
                
                <form action="{{ route('search.index') }}" method="GET" class="bg-white rounded-xl shadow-2xl p-6 md:p-8">
                    <!-- Champs de recherche avec plus d'importance -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="search-query" class="sr-only">Service recherché</label>
                            <input type="text" id="search-query" name="q" placeholder="Quel service recherchez-vous ?" 
                                   class="w-full px-5 py-4 text-gray-900 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                                   aria-label="Quel service recherchez-vous ?">
                        </div>
                        <div>
                            <label for="search-location" class="sr-only">Localisation</label>
                            <input type="text" id="search-location" name="location" placeholder="Ville ou code postal" 
                                   class="w-full px-5 py-4 text-gray-900 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                                   aria-label="Ville ou code postal">
                        </div>
                    </div>
                    
                    <!-- Boutons redessinés - plus plats et moins massifs -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Bouton de recherche supprimé -->
                        <a href="{{ route('client.requests.create') }}" class="hero-button flex-1 bg-green-600 hover:bg-green-500 hover:shadow-lg text-white font-medium py-3 px-8 rounded-xl transition-all duration-300 text-center focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 border-b-2 border-green-700 hover:border-green-600 transform hover:-translate-y-0.5">
                            <i class="fas fa-plus mr-2" aria-hidden="true"></i>Publier une demande
                        </a>
                    </div>
                </form>
            </div>
            
            {{-- <!-- Statistiques avec icônes et animation -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="animate-fade-in-up" style="animation-delay: 0.1s">
                    <div class="flex items-center justify-center mb-2">
                        <i class="fas fa-user-check text-yellow-400 text-xl mr-2" aria-hidden="true"></i>
                        <div class="text-4xl font-bold text-yellow-400 counter" data-target="{{ $stats['total_providers'] ?? '500' }}">{{ $stats['total_providers'] ?? '500+' }}</div>
                    </div>
                    <div class="text-blue-100 font-medium">Prestataires vérifiés</div>
                </div>
                <div class="animate-fade-in-up" style="animation-delay: 0.2s">
                    <div class="flex items-center justify-center mb-2">
                        <i class="fas fa-tools text-yellow-400 text-xl mr-2" aria-hidden="true"></i>
                        <div class="text-4xl font-bold text-yellow-400 counter" data-target="{{ $stats['total_services'] ?? '1200' }}">{{ $stats['total_services'] ?? '1200+' }}</div>
                    </div>
                    <div class="text-blue-100 font-medium">Services disponibles</div>
                </div>
                <div class="animate-fade-in-up" style="animation-delay: 0.3s">
                    <div class="flex items-center justify-center mb-2">
                        <i class="fas fa-folder-open text-yellow-400 text-xl mr-2" aria-hidden="true"></i>
                        <div class="text-4xl font-bold text-yellow-400 counter" data-target="{{ $stats['total_categories'] ?? '50' }}">{{ $stats['total_categories'] ?? '50+' }}</div>
                    </div>
                    <div class="text-blue-100 font-medium">Catégories de métiers</div>
                </div>
            </div> --}}
        </div>
    </div>
</section>

<!-- Section Raccourcis visuels -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Que recherchez-vous ?
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Découvrez nos trois univers pour répondre à tous vos besoins
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <!-- Services -->
            <a href="{{ route('services.index') }}" class="group block">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 text-center hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-blue-200">
                    <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-briefcase text-3xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 group-hover:text-blue-600 transition-colors">
                        🔧 Besoin d'un service ?
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Trouvez le prestataire parfait pour vos projets professionnels
                    </p>
                    <div class="inline-flex items-center text-blue-600 font-semibold group-hover:text-blue-700">
                        Voir les prestations disponibles
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>
            
            <!-- Matériel à louer -->
            <a href="{{ route('equipment.index') }}" class="group block">
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8 text-center hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-green-200">
                    <div class="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-tools text-3xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 group-hover:text-green-600 transition-colors">
                        🛠️ Louer un outil ?
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Accédez à du matériel professionnel de qualité
                    </p>
                    <div class="inline-flex items-center text-green-600 font-semibold group-hover:text-green-700">
                        Parcourir les matériels à louer
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>
            
            <!-- Vente urgente -->
            <a href="{{ route('urgent-sales.index') }}" class="group block">
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-8 text-center hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-red-200">
                    <div class="w-20 h-20 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-bolt text-3xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 group-hover:text-red-600 transition-colors">
                        💸 Trouver une bonne affaire ?
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Découvrez des produits et outils en vente rapide
                    </p>
                    <div class="inline-flex items-center text-red-600 font-semibold group-hover:text-red-700">
                        Découvrir les ventes urgentes
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Section Pourquoi choisir TaPrestation -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Pourquoi choisir TaPrestation ?
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Une plateforme de confiance pour tous vos besoins en services professionnels
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-2xl text-blue-600" aria-hidden="true"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Sécurité garantie</h3>
                <p class="text-gray-600">Tous nos prestataires sont vérifiés et leurs identités confirmées</p>
            </div>
            
            <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-check text-2xl text-green-600" aria-hidden="true"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Réservation en ligne</h3>
                <p class="text-gray-600">Planifiez et réservez vos services directement en ligne</p>
            </div>
            
            <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-comments text-2xl text-purple-600" aria-hidden="true"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Messagerie intégrée</h3>
                <p class="text-gray-600">Communiquez facilement avec vos prestataires</p>
            </div>
            
            <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-star text-2xl text-yellow-600" aria-hidden="true"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Avis vérifiés</h3>
                <p class="text-gray-600">Consultez les avis authentiques de nos clients</p>
            </div>
            
            <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-2xl text-red-600" aria-hidden="true"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Support 7j/7</h3>
                <p class="text-gray-600">Notre équipe vous accompagne à chaque étape</p>
            </div>
            
            <div class="text-center p-6 bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bolt text-2xl text-indigo-600" aria-hidden="true"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Réponse rapide</h3>
                <p class="text-gray-600">Recevez des devis en moins de 24h</p>
            </div>
        </div>
    </div>
</section>

<!-- Section Prestataires en vedette -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Nos prestataires à la une
            </h2>
            <p class="text-xl text-gray-600">
                Découvrez des professionnels talentueux et vérifiés
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredPrestataires as $prestataire)
            <div class="bg-white rounded-xl shadow-lg p-6 text-center transform hover:-translate-y-2 transition-transform duration-300">
                <div class="mb-4">
                    <div class="w-24 h-24 rounded-full mx-auto bg-gray-200 flex items-center justify-center overflow-hidden relative">
                        @if($prestataire->photo)
                            <img src="{{ asset('storage/' . $prestataire->photo) }}" alt="{{ $prestataire->user->name }}" class="w-full h-full object-cover">
                        @elseif($prestataire->user->avatar)
                            <img src="{{ asset('storage/' . $prestataire->user->avatar) }}" alt="{{ $prestataire->user->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl font-bold text-gray-600">{{ strtoupper(substr($prestataire->user->name, 0, 1)) }}</span>
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
                <div class="flex items-center justify-center gap-2 mb-1">
                    <h3 class="text-xl font-bold text-gray-900">{{ $prestataire->user->name }}</h3>
                    @if($prestataire->isVerified())
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Vérifié
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mb-4">{{ $prestataire->speciality ?? 'Spécialité non définie' }}</p>
                <a href="{{ route('prestataires.show', $prestataire) }}" class="inline-block bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Voir le profil
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Section Catégories -->
{{-- <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Explorez nos catégories
            </h2>
            <p class="text-xl text-gray-600">
                Trouvez le service dont vous avez besoin
            </p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            @foreach($categories as $category)
            <!-- Lien de recherche par catégorie supprimé -->
            <div class="group p-6 bg-gray-50 rounded-xl transition duration-300 text-center">
                <div class="w-12 h-12 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center mx-auto mb-3 transition duration-300">
                    @switch($category->name)
                        @case('Développement Web')
                            <i class="fas fa-code text-blue-600" aria-hidden="true"></i>
                            @break
                        @case('Design Graphique')
                            <i class="fas fa-palette text-blue-600" aria-hidden="true"></i>
                            @break
                        @case('Marketing Digital')
                            <i class="fas fa-bullhorn text-blue-600" aria-hidden="true"></i>
                            @break
                        @case('Rédaction & Traduction')
                            <i class="fas fa-pen text-blue-600" aria-hidden="true"></i>
                            @break
                        @case('Vidéo & Audio')
                            <i class="fas fa-video text-blue-600" aria-hidden="true"></i>
                            @break
                        @default
                            <i class="fas fa-briefcase text-blue-600" aria-hidden="true"></i>
                    @endswitch
                </div>
                <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition duration-300 text-sm">
                    {{ $category->name }}
                </h3>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $category->services_count ?? 0 }} services
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section> --}}


{{-- <!-- Section Avis clients -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Ce que disent nos clients
            </h2>
            <p class="text-xl text-gray-600">
                Découvrez les témoignages de satisfaction
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @if($clientReviews->count() > 0)
                @foreach($clientReviews as $review)
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center mb-4" role="img" aria-label="{{ $review->rating }} étoiles sur 5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                            <span class="sr-only">{{ $review->rating }} sur 5 étoiles</span>
                        </div>
                        <p class="text-gray-600 mb-4">
                            "{{ Str::limit($review->comment, 150) }}"
                        </p>
                        
                        @if($review->hasPhotos())
                        <div class="mb-4">
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($review->photos as $index => $photo)
                                    @if($index < 3) <!-- Limiter à 3 photos maximum -->
                                    <div class="relative aspect-square overflow-hidden rounded-lg cursor-pointer" onclick="openPhotoModal('{{ asset('storage/' . $photo) }}', 'Photo de l\'avis')">
                                        <img src="{{ asset('storage/' . $photo) }}" alt="Photo de l'avis" class="w-full h-full object-cover">
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                            @if(count($review->photos) > 3)
                                <p class="text-xs text-gray-500 mt-1">+{{ count($review->photos) - 3 }} autres photos</p>
                            @endif
                        </div>
                        @endif
                        
                    </div>
                @endforeach
            @else
                <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                    <div class="flex justify-center mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucun avis disponible</h3>
                    <p class="text-gray-600 mb-4">
                        Nous n'avons pas encore d'avis à afficher. Soyez le premier à partager votre expérience avec nos services !                        
                    </p>
                    <a href="{{ route('reviews.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-700 transition">
                        Voir les avis
                    </a>
                </div>
            @endif
        </div>
        
        <!-- Bouton Voir plus d'avis -->
        <div class="text-center mt-10">
            <a href="{{ route('reviews.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-700 transition">
                <span>Voir plus d'avis</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </div>
</section> --}}

<!-- Footer -->
<footer class="bg-gradient-to-br from-gray-900 via-gray-800 to-blue-900 text-white">
    <!-- Section principale -->
    <div class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
                <!-- À propos -->
                <div class="lg:col-span-2">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-handshake text-white text-lg"></i>
                        </div>
                        <h3 class="text-2xl font-bold">TaPrestation</h3>
                    </div>
                    <p class="text-gray-300 mb-6 leading-relaxed">
                        La plateforme de référence pour connecter clients et prestataires de services professionnels. 
                        Trouvez facilement le professionnel qu'il vous faut ou développez votre activité.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-blue-600 hover:bg-blue-700 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110" aria-label="Facebook">
                            <i class="fab fa-facebook-f text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-blue-400 hover:bg-blue-500 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110" aria-label="Twitter">
                            <i class="fab fa-twitter text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-blue-700 hover:bg-blue-800 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in text-white"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-pink-600 hover:bg-pink-700 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110" aria-label="Instagram">
                            <i class="fab fa-instagram text-white"></i>
                        </a>
                    </div>
                </div>
            
               
                
                <!-- Support -->
                <div>
                    <h3 class="text-lg font-semibold mb-6 flex items-center">
                        <i class="fas fa-life-ring text-green-400 mr-2"></i>
                        Support
                    </h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('about') }}" class="text-gray-300 hover:text-white hover:translate-x-1 transition-all duration-200 flex items-center group">
                                <i class="fas fa-info-circle text-green-400 mr-3 group-hover:text-green-300"></i>
                                À propos
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('contact') }}" class="text-gray-300 hover:text-white hover:translate-x-1 transition-all duration-200 flex items-center group">
                                <i class="fas fa-envelope text-green-400 mr-3 group-hover:text-green-300"></i>
                                Contact
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-300 hover:text-white hover:translate-x-1 transition-all duration-200 flex items-center group">
                                <i class="fas fa-question-circle text-green-400 mr-3 group-hover:text-green-300"></i>
                                Centre d'aide
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-300 hover:text-white hover:translate-x-1 transition-all duration-200 flex items-center group">
                                <i class="fas fa-comments text-green-400 mr-3 group-hover:text-green-300"></i>
                                FAQ
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Légal -->
                <div>
                    <h3 class="text-lg font-semibold mb-6 flex items-center">
                        <i class="fas fa-gavel text-yellow-400 mr-2"></i>
                        Légal
                    </h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="#" class="text-gray-300 hover:text-white hover:translate-x-1 transition-all duration-200 flex items-center group">
                                <i class="fas fa-file-contract text-yellow-400 mr-3 group-hover:text-yellow-300"></i>
                                Conditions d'utilisation
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-300 hover:text-white hover:translate-x-1 transition-all duration-200 flex items-center group">
                                <i class="fas fa-shield-alt text-yellow-400 mr-3 group-hover:text-yellow-300"></i>
                                Politique de confidentialité
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-300 hover:text-white hover:translate-x-1 transition-all duration-200 flex items-center group">
                                <i class="fas fa-balance-scale text-yellow-400 mr-3 group-hover:text-yellow-300"></i>
                                Mentions légales
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-300 hover:text-white hover:translate-x-1 transition-all duration-200 flex items-center group">
                                <i class="fas fa-cookie-bite text-yellow-400 mr-3 group-hover:text-yellow-300"></i>
                                Cookies
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Section copyright -->
    <div class="border-t border-gray-700/50 bg-gray-900/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="flex items-center space-x-2 text-gray-400">
                    <i class="fas fa-copyright"></i>
                    <span>{{ date('Y') }} TaPrestation. Tous droits réservés.</span>
                </div>
                <div class="flex items-center space-x-6 text-sm text-gray-400">
                    <span class="flex items-center">
                        <i class="fas fa-map-marker-alt mr-2 text-blue-400"></i>
                        France
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-phone mr-2 text-green-400"></i>
                        Support 24/7
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-lock mr-2 text-yellow-400"></i>
                        Paiements sécurisés
                    </span>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Modal pour les photos -->
<div id="photoModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 hidden">
    <div class="max-w-4xl max-h-full p-4">
        <button onclick="closePhotoModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 focus:outline-none">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <img id="modalImage" src="" alt="Photo agrandie" class="max-w-full max-h-[80vh] object-contain">
        <div id="modalCaption" class="text-white text-center mt-4"></div>
    </div>
</div>

<script>
// Modal pour les photos
function openPhotoModal(imageSrc, caption) {
    const modal = document.getElementById('photoModal');
    const modalImage = document.getElementById('modalImage');
    const modalCaption = document.getElementById('modalCaption');
    
    modalImage.src = imageSrc;
    modalCaption.textContent = caption;
    modal.classList.remove('hidden');
    
    // Fermer avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePhotoModal();
        }
    });
}

function closePhotoModal() {
    const modal = document.getElementById('photoModal');
    modal.classList.add('hidden');
}

// Fermer le modal en cliquant à l'extérieur
document.getElementById('photoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePhotoModal();
    }
});
</script>

@endsection