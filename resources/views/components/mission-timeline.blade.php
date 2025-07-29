@props(['clientRequest', 'currentStep' => null])

@php
    // Déterminer l'étape actuelle si non spécifiée
    if (!$currentStep) {
        if ($clientRequest->status === 'completed') {
            // Vérifier si une évaluation existe
            $hasReview = $clientRequest->client->reviews()
                ->where('prestataire_id', $clientRequest->offers()->where('status', 'accepted')->first()->prestataire_id ?? null)
                ->exists();
            
            $currentStep = $hasReview ? 5 : 4;
        } elseif ($clientRequest->status === 'in_progress') {
            $currentStep = 3;
        } elseif ($clientRequest->status === 'pending' && $clientRequest->offers()->where('status', 'accepted')->exists()) {
            $currentStep = 2;
        } else {
            $currentStep = 1;
        }
    }
    
    // Dates pour chaque étape
    $createdDate = $clientRequest->created_at;
    $offerAcceptedDate = $clientRequest->offers()->where('status', 'accepted')->first()->updated_at ?? null;
    $inProgressDate = $clientRequest->status === 'in_progress' ? $clientRequest->updated_at : null;
    $completedDate = $clientRequest->status === 'completed' ? $clientRequest->updated_at : null;
    
    // Vérifier si une évaluation existe et obtenir sa date
    $review = null;
    $reviewDate = null;
    if ($clientRequest->status === 'completed') {
        $acceptedOffer = $clientRequest->offers()->where('status', 'accepted')->first();
        if ($acceptedOffer) {
            $review = $clientRequest->client->reviews()->where('prestataire_id', $acceptedOffer->prestataire_id)->first();
            $reviewDate = $review ? $review->created_at : null;
        }
    }
@endphp

<div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Suivi de la mission</h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">Progression de votre demande</p>
    </div>
    
    <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <!-- Étape 1: Demande créée -->
            <div class="flex flex-col items-center mb-4 md:mb-0">
                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $currentStep >= 1 ? 'bg-indigo-600' : 'bg-gray-200' }}">
                    <svg class="w-6 h-6 {{ $currentStep >= 1 ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div class="text-center mt-2">
                    <p class="text-sm font-medium {{ $currentStep >= 1 ? 'text-indigo-600' : 'text-gray-500' }}">Demande créée</p>
                    @if($createdDate)
                        <p class="text-xs text-gray-500">{{ $createdDate->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>
            
            <!-- Ligne de connexion -->
            <div class="hidden md:block w-full max-w-[60px] h-0.5 {{ $currentStep >= 2 ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>
            
            <!-- Étape 2: Offre acceptée -->
            <div class="flex flex-col items-center mb-4 md:mb-0">
                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $currentStep >= 2 ? 'bg-indigo-600' : 'bg-gray-200' }}">
                    <svg class="w-6 h-6 {{ $currentStep >= 2 ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-center mt-2">
                    <p class="text-sm font-medium {{ $currentStep >= 2 ? 'text-indigo-600' : 'text-gray-500' }}">Offre acceptée</p>
                    @if($offerAcceptedDate)
                        <p class="text-xs text-gray-500">{{ $offerAcceptedDate->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>
            
            <!-- Ligne de connexion -->
            <div class="hidden md:block w-full max-w-[60px] h-0.5 {{ $currentStep >= 3 ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>
            
            <!-- Étape 3: Mission en cours -->
            <div class="flex flex-col items-center mb-4 md:mb-0">
                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $currentStep >= 3 ? 'bg-indigo-600' : 'bg-gray-200' }}">
                    <svg class="w-6 h-6 {{ $currentStep >= 3 ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-center mt-2">
                    <p class="text-sm font-medium {{ $currentStep >= 3 ? 'text-indigo-600' : 'text-gray-500' }}">Mission en cours</p>
                    @if($inProgressDate)
                        <p class="text-xs text-gray-500">{{ $inProgressDate->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>
            
            <!-- Ligne de connexion -->
            <div class="hidden md:block w-full max-w-[60px] h-0.5 {{ $currentStep >= 4 ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>
            
            <!-- Étape 4: Mission terminée -->
            <div class="flex flex-col items-center mb-4 md:mb-0">
                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $currentStep >= 4 ? 'bg-indigo-600' : 'bg-gray-200' }}">
                    <svg class="w-6 h-6 {{ $currentStep >= 4 ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="text-center mt-2">
                    <p class="text-sm font-medium {{ $currentStep >= 4 ? 'text-indigo-600' : 'text-gray-500' }}">Mission terminée</p>
                    @if($completedDate)
                        <p class="text-xs text-gray-500">{{ $completedDate->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>
            
            <!-- Ligne de connexion -->
            <div class="hidden md:block w-full max-w-[60px] h-0.5 {{ $currentStep >= 5 ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>
            
            <!-- Étape 5: Évaluation déposée -->
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $currentStep >= 5 ? 'bg-indigo-600' : 'bg-gray-200' }}">
                    <svg class="w-6 h-6 {{ $currentStep >= 5 ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <div class="text-center mt-2">
                    <p class="text-sm font-medium {{ $currentStep >= 5 ? 'text-indigo-600' : 'text-gray-500' }}">Évaluation déposée</p>
                    @if($reviewDate)
                        <p class="text-xs text-gray-500">{{ $reviewDate->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Version mobile: affichage vertical -->
        <div class="md:hidden mt-4">
            <div class="space-y-4">
                @for ($i = 1; $i < 5; $i++)
                    <div class="w-0.5 h-6 bg-gray-200 mx-auto {{ $currentStep > $i ? 'bg-indigo-600' : '' }}"></div>
                @endfor
            </div>
        </div>
    </div>
</div>