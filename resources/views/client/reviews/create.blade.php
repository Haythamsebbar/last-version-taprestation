@extends('layouts.app')

@section('content')
<div class="py-10">
    <header>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold leading-tight text-gray-900">Évaluer la mission</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Mission : {{ $clientRequest->title }}
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('client.requests.show', $clientRequest) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                        </svg>
                        Retour à la mission
                    </a>
                </div>
            </div>
        </div>
    </header>
    <main>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <!-- Timeline de la mission -->
                <x-mission-timeline :clientRequest="$clientRequest" currentStep="4" />
                
                <!-- Formulaire d'évaluation -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Évaluer le prestataire</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Partagez votre expérience avec {{ $prestataire->user->name }}
                        </p>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                        <form action="{{ route('client.reviews.store', $clientRequest) }}" method="POST" class="space-y-6">
                            @csrf
                            <input type="hidden" name="prestataire_id" value="{{ $prestataire->id }}">
                            
                            <!-- Note globale -->
                            <div>
                                <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">Note globale</label>
                                <div class="flex items-center">
                                    <div class="flex space-x-1" id="star-rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <button type="button" data-rating="{{ $i }}" class="star-btn text-2xl text-gray-300 hover:text-yellow-400 focus:outline-none">★</button>
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-sm text-gray-500" id="rating-text">Sélectionnez une note</span>
                                    <input type="hidden" name="rating" id="rating-input" value="{{ old('rating') }}">
                                </div>
                                @error('rating')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Commentaire -->
                            <div>
                                <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                                    Votre commentaire
                                    <span class="text-gray-500 font-normal">(minimum 30 caractères)</span>
                                </label>
                                <textarea 
                                    id="comment" 
                                    name="comment" 
                                    rows="4" 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                    placeholder="Partagez votre expérience avec ce prestataire..."
                                >{{ old('comment') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">
                                    <span id="char-count">0</span>/30 caractères minimum
                                </p>
                                @error('comment')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Bouton d'envoi -->
                            <div class="pt-5">
                                <div class="flex justify-end">
                                    <a href="{{ route('client.requests.show', $clientRequest) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Annuler
                                    </a>
                                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Envoyer l'évaluation
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion des étoiles pour la notation
        const stars = document.querySelectorAll('.star-btn');
        const ratingInput = document.getElementById('rating-input');
        const ratingText = document.getElementById('rating-text');
        const ratingLabels = ['Très insatisfait', 'Insatisfait', 'Correct', 'Satisfait', 'Très satisfait'];
        
        // Initialiser la note si elle existe déjà
        if (ratingInput.value) {
            updateStars(parseInt(ratingInput.value));
        }
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.dataset.rating);
                ratingInput.value = rating;
                updateStars(rating);
            });
        });
        
        function updateStars(rating) {
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
            
            if (rating > 0 && rating <= 5) {
                ratingText.textContent = ratingLabels[rating - 1];
            } else {
                ratingText.textContent = 'Sélectionnez une note';
            }
        }
        
        // Compteur de caractères pour le commentaire
        const commentTextarea = document.getElementById('comment');
        const charCount = document.getElementById('char-count');
        
        commentTextarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count;
            
            if (count < 30) {
                charCount.classList.remove('text-green-500');
                charCount.classList.add('text-gray-500');
            } else {
                charCount.classList.remove('text-gray-500');
                charCount.classList.add('text-green-500');
            }
        });
        
        // Initialiser le compteur si le commentaire existe déjà
        if (commentTextarea.value) {
            const count = commentTextarea.value.length;
            charCount.textContent = count;
            
            if (count >= 30) {
                charCount.classList.remove('text-gray-500');
                charCount.classList.add('text-green-500');
            }
        }
    });
</script>
@endpush
@endsection