@extends('layouts.app')

@section('title', $urgentSale->title)

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-6">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('urgent-sales.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <i class="fas fa-bolt mr-2"></i>
                        Ventes urgentes
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-gray-500 truncate">{{ $urgentSale->title }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Colonne principale -->
            <div class="lg:col-span-2">
                <!-- En-tête -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $urgentSale->title }}</h1>
                                
                                <div class="flex flex-wrap items-center gap-3 mb-4">
                                    @if($urgentSale->is_urgent)
                                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                            <i class="fas fa-bolt mr-1"></i>VENTE URGENTE
                                        </span>
                                    @endif
                                    
                                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-semibold">
                                        {{ $urgentSale->condition_label }}
                                    </span>
                                    
                                    @if($urgentSale->created_at->isToday())
                                        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                            <i class="fas fa-star mr-1"></i>NOUVEAU
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center text-gray-600 space-x-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        <span>{{ $urgentSale->location }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-eye mr-1"></i>
                                        <span>{{ $urgentSale->views_count }} vue{{ $urgentSale->views_count > 1 ? 's' : '' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-clock mr-1"></i>
                                        <span>{{ $urgentSale->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <div class="text-4xl font-bold text-blue-600 mb-2">
                                    {{ number_format($urgentSale->price, 0, ',', ' ') }}€
                                </div>
                                <div class="text-gray-600">
                                    Quantité disponible: {{ $urgentSale->quantity }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Photos -->
                @if($urgentSale->photos && count($urgentSale->photos) > 0)
                    <div class="bg-white rounded-lg shadow mb-6">
                        <div class="p-6">
                            <!-- Photo principale -->
                            <div class="mb-4">
                                <img id="main-image" src="{{ Storage::url($urgentSale->photos[0]) }}" alt="{{ $urgentSale->title }}" class="w-full h-96 object-cover rounded-lg cursor-pointer" onclick="openImageModal('{{ Storage::url($urgentSale->photos[0]) }}')">
                            </div>
                            
                            <!-- Miniatures -->
                            @if(count($urgentSale->photos) > 1)
                                <div class="grid grid-cols-4 md:grid-cols-6 gap-2">
                                    @foreach($urgentSale->photos as $index => $photo)
                                        <img src="{{ Storage::url($photo) }}" alt="Photo {{ $index + 1 }}" class="w-full h-16 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity {{ $index === 0 ? 'ring-2 ring-blue-500' : '' }}" onclick="changeMainImage('{{ Storage::url($photo) }}', this)">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                
                <!-- Description -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Description</h2>
                        <div class="prose max-w-none text-gray-700">
                            {!! nl2br(e($urgentSale->description)) !!}
                        </div>
                    </div>
                </div>
                
                <!-- Ventes similaires -->
                @if($similarSales->count() > 0)
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Ventes similaires</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($similarSales as $similar)
                                    <a href="{{ route('urgent-sales.show', $similar) }}" class="block border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex">
                                            @if($similar->photos && count($similar->photos) > 0)
                                                <img src="{{ Storage::url($similar->photos[0]) }}" alt="{{ $similar->title }}" class="w-20 h-20 object-cover rounded mr-4">
                                            @else
                                                <div class="w-20 h-20 bg-gray-200 rounded mr-4 flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            @endif
                                            
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2">{{ $similar->title }}</h3>
                                                <div class="text-blue-600 font-bold mb-1">{{ number_format($similar->price, 0, ',', ' ') }}€</div>
                                                <div class="text-sm text-gray-600">{{ $similar->location }}</div>
                                                @if($similar->is_urgent)
                                                    <span class="inline-block bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-semibold mt-1">
                                                        <i class="fas fa-bolt mr-1"></i>URGENT
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Informations du vendeur -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Vendeur</h2>
                        
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user text-gray-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $urgentSale->prestataire->user->name }}</h3>
                                <p class="text-gray-600 text-sm">Prestataire événementiel</p>
                                @if($urgentSale->prestataire->user->email_verified_at)
                                    <span class="inline-flex items-center text-green-600 text-sm">
                                        <i class="fas fa-check-circle mr-1"></i>Vérifié
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        @auth
                            @if($urgentSale->canBeContacted())
                                <button onclick="openContactModal()" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition duration-200 mb-3">
                                    <i class="fas fa-envelope mr-2"></i>Contacter le vendeur
                                </button>
                            @else
                                <div class="w-full bg-gray-300 text-gray-600 py-3 rounded-lg font-semibold text-center mb-3">
                                    <i class="fas fa-times mr-2"></i>Non disponible
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold text-center transition duration-200 mb-3">
                                <i class="fas fa-sign-in-alt mr-2"></i>Se connecter pour contacter
                            </a>
                        @endauth
                        
                        @auth
                            <button onclick="openReportModal()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded-lg text-sm transition duration-200">
                                <i class="fas fa-flag mr-2"></i>Signaler cette vente
                            </button>
                        @endauth
                    </div>
                </div>
                
                <!-- Informations détaillées -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Détails</h2>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Prix</span>
                                <span class="font-semibold text-gray-900">{{ number_format($urgentSale->price, 0, ',', ' ') }}€</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">État</span>
                                <span class="font-semibold text-gray-900">{{ $urgentSale->condition_label }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">Quantité</span>
                                <span class="font-semibold text-gray-900">{{ $urgentSale->quantity }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">Localisation</span>
                                <span class="font-semibold text-gray-900">{{ $urgentSale->location }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">Publié le</span>
                                <span class="font-semibold text-gray-900">{{ $urgentSale->created_at->format('d/m/Y') }}</span>
                            </div>
                            
                            @if($urgentSale->is_urgent)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Type</span>
                                    <span class="font-semibold text-red-600">
                                        <i class="fas fa-bolt mr-1"></i>Vente urgente
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Partage -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Partager</h2>
                        
                        <div class="flex space-x-3">
                            <button onclick="shareOnFacebook()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm transition duration-200">
                                <i class="fab fa-facebook-f mr-1"></i>Facebook
                            </button>
                            
                            <button onclick="shareOnTwitter()" class="flex-1 bg-blue-400 hover:bg-blue-500 text-white py-2 rounded-lg text-sm transition duration-200">
                                <i class="fab fa-twitter mr-1"></i>Twitter
                            </button>
                            
                            <button onclick="copyLink()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 rounded-lg text-sm transition duration-200">
                                <i class="fas fa-link mr-1"></i>Copier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@auth
<!-- Modal de contact -->
<div id="contact-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <form action="{{ route('urgent-sales.contact', $urgentSale) }}" method="POST" id="contact-form">
                @csrf
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Contacter le vendeur</h3>
                        <button type="button" onclick="closeContactModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="mb-4">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Message <span class="text-red-500">*</span>
                        </label>
                        <textarea id="message" name="message" required rows="4" maxlength="500" placeholder="Bonjour, je suis intéressé(e) par votre article..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-gray-500 text-sm">Ce message sera envoyé au vendeur.</span>
                            <span id="message-count" class="text-sm text-gray-500">0/500</span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Téléphone
                            </label>
                            <input type="tel" id="phone" name="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input type="email" id="email" name="email" value="{{ auth()->user()->email }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3 rounded-b-lg">
                    <button type="button" onclick="closeContactModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition duration-200">
                        Annuler
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de signalement -->
<div id="report-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <form action="{{ route('urgent-sales.report', $urgentSale) }}" method="POST" id="report-form">
                @csrf
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Signaler cette vente</h3>
                        <button type="button" onclick="closeReportModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Raison du signalement <span class="text-red-500">*</span>
                        </label>
                        <select id="reason" name="reason" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Sélectionner une raison</option>
                            <option value="inappropriate_content">Contenu inapproprié</option>
                            <option value="fake_listing">Annonce factice</option>
                            <option value="spam">Spam</option>
                            <option value="fraud">Fraude</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description (optionnel)
                        </label>
                        <textarea id="description" name="description" rows="3" maxlength="500" placeholder="Décrivez le problème..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3 rounded-b-lg">
                    <button type="button" onclick="closeReportModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition duration-200">
                        Annuler
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-flag mr-2"></i>Signaler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth

<!-- Modal d'image -->
<div id="image-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50" onclick="closeImageModal()">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative max-w-4xl max-h-full">
            <img id="modal-image" src="" alt="" class="max-w-full max-h-full object-contain">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Gestion des images
function changeMainImage(src, element) {
    document.getElementById('main-image').src = src;
    
    // Retirer la bordure de toutes les miniatures
    document.querySelectorAll('.grid img').forEach(img => {
        img.classList.remove('ring-2', 'ring-blue-500');
    });
    
    // Ajouter la bordure à la miniature cliquée
    element.classList.add('ring-2', 'ring-blue-500');
}

function openImageModal(src) {
    document.getElementById('modal-image').src = src;
    document.getElementById('image-modal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('image-modal').classList.add('hidden');
}

@auth
// Gestion du modal de contact
function openContactModal() {
    document.getElementById('contact-modal').classList.remove('hidden');
    document.getElementById('message').focus();
}

function closeContactModal() {
    document.getElementById('contact-modal').classList.add('hidden');
}

// Gestion du modal de signalement
function openReportModal() {
    document.getElementById('report-modal').classList.remove('hidden');
}

function closeReportModal() {
    document.getElementById('report-modal').classList.add('hidden');
}

// Compteur de caractères pour le message
const messageTextarea = document.getElementById('message');
const messageCount = document.getElementById('message-count');

if (messageTextarea && messageCount) {
    function updateMessageCount() {
        const count = messageTextarea.value.length;
        messageCount.textContent = `${count}/500`;
        
        if (count > 450) {
            messageCount.classList.add('text-red-500');
            messageCount.classList.remove('text-gray-500');
        } else {
            messageCount.classList.add('text-gray-500');
            messageCount.classList.remove('text-red-500');
        }
    }
    
    messageTextarea.addEventListener('input', updateMessageCount);
}

// Fermer les modals avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeContactModal();
        closeReportModal();
        closeImageModal();
    }
});
@endauth

// Fonctions de partage
function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
}

function shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent('{{ $urgentSale->title }} - {{ number_format($urgentSale->price, 0, ",", " ") }}€');
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        // Afficher une notification de succès
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check mr-1"></i>Copié!';
        button.classList.remove('bg-gray-600', 'hover:bg-gray-700');
        button.classList.add('bg-green-600');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('bg-green-600');
            button.classList.add('bg-gray-600', 'hover:bg-gray-700');
        }, 2000);
    });
}
</script>
@endpush

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.prose {
    line-height: 1.6;
}
</style>
@endpush