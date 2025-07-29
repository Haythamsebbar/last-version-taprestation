@extends('layouts.app')

@section('content')
<div class="bg-blue-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-extrabold text-blue-900 mb-2">Nouvelle Réservation</h1>
                <p class="text-lg text-blue-700">Réservez un créneau pour le service sélectionné</p>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6 shadow-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Informations du service -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-6 sticky top-8">
                        <h2 class="text-2xl font-bold text-blue-800 mb-5 border-b-2 border-blue-200 pb-3">Détails du service</h2>
                        
                        <div class="space-y-5">
                            <div>
                                <h3 class="font-bold text-lg text-gray-800">{{ $service->name }}</h3>
                                <p class="text-gray-600 text-sm mt-1">{{ Str::limit($service->description, 100) }}</p>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4 space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 font-medium">Prix :</span>
                                    <span class="font-bold text-2xl text-blue-600">{{ number_format($service->price, 2) }} €</span>
                                </div>
                                
                                @if($service->duration)
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 font-medium">Durée :</span>
                                        <span class="text-gray-800 font-semibold">{{ $service->duration }} min</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="font-bold text-gray-800 mb-3">Prestataire</h4>
                                <a href="{{ route('prestataires.show', $prestataire) }}" class="flex items-center gap-4 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition duration-200">
                                    @if($prestataire->user->profile_photo)
                                        <img src="{{ asset('storage/' . $prestataire->user->profile_photo) }}" 
                                             alt="{{ $prestataire->user->name }}" 
                                             class="w-12 h-12 rounded-full object-cover border-2 border-blue-200">
                                    @else
                                        <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center border-2 border-blue-300">
                                            <span class="text-blue-800 font-bold text-xl">{{ substr($prestataire->user->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-blue-900">{{ $prestataire->user->name }}</div>
                                        @if($prestataire->location)
                                            <div class="text-sm text-blue-700 flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                                                {{ $prestataire->location }}
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Formulaire de réservation -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-8">
                        <h2 class="text-2xl font-bold text-blue-800 mb-6 border-b-2 border-blue-200 pb-3">Sélectionner un créneau</h2>
                        
                        <form action="{{ route('bookings.store') }}" method="POST" x-data="bookingForm()">
                            @csrf
                            <input type="hidden" name="service_id" value="{{ $service->id }}">
                            <input type="hidden" name="prestataire_id" value="{{ $prestataire->id }}">
                            
                            @if(count($availableSlots) > 0 && collect($availableSlots)->where('is_booked', false)->count() > 0)
                                <div class="mb-6">
                                    <label class="block text-md font-semibold text-gray-800 mb-4">Créneaux disponibles</label>
                                    <div class="space-y-3">
                                        @php
                                            $groupedSlots = collect($availableSlots)->groupBy(function($slot) {
                                                return $slot['datetime']->format('Y-m-d');
                                            });
                                        @endphp

                                        @foreach($groupedSlots as $date => $slots)
                                            <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }" class="border-2 border-gray-200 rounded-lg overflow-hidden">
                                                <button type="button" @click="open = !open" class="w-full flex justify-between items-center p-4 bg-blue-50 hover:bg-blue-100 focus:outline-none transition duration-200">
                                                    <span class="font-bold text-blue-900">{{ \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
                                                    <svg class="w-6 h-6 text-blue-600 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" x-transition class="p-4 bg-white">
                                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                                        @foreach($slots as $slot)
                                                            @if($slot['is_booked'])
                                                                <!-- Créneau réservé - grisé et non cliquable -->
                                                                <div class="relative">
                                                                    <div class="border-2 border-gray-200 rounded-lg p-3 text-center font-semibold text-gray-400 bg-gray-100 cursor-not-allowed opacity-60">
                                                                        {{ $slot['datetime']->format('H:i') }}
                                                                        <div class="text-xs mt-1 text-gray-500">Réservé</div>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <!-- Créneau disponible - cliquable -->
                                                                <label class="relative">
                                                                    <input type="radio" name="start_datetime" value="{{ $slot['datetime']->toDateTimeString() }}" 
                                                                           class="sr-only peer" required
                                                                           @change="setSelectedSlot('{{ \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM') }}', '{{ $slot['datetime']->format('H:i') }}')">
                                                                    <div class="border-2 border-gray-300 rounded-lg p-3 cursor-pointer transition-all duration-200 text-center font-semibold text-gray-700
                                                                                peer-checked:border-blue-600 peer-checked:bg-blue-100 peer-checked:text-blue-900 peer-checked:shadow-lg peer-checked:scale-105
                                                                                hover:border-blue-400 hover:bg-blue-50">
                                                                        {{ $slot['datetime']->format('H:i') }}
                                                                    </div>
                                                                </label>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div x-show="selectedSlot.date && selectedSlot.time" class="my-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-md text-center shadow-sm">
                                    <p class="font-semibold text-green-800">Créneau sélectionné : <span x-text="selectedSlot.date + ' à ' + selectedSlot.time" class="font-bold"></span></p>
                                </div>

                            @else
                                <div class="text-center py-10 px-6 bg-blue-100 rounded-lg border-2 border-dashed border-blue-300">
                                    <div class="text-blue-500 mb-4">
                                        <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-blue-900 mb-2">Aucun créneau disponible</h3>
                                    <p class="text-blue-800">Ce prestataire n'a pas de créneaux disponibles pour les 30 prochains jours.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('prestataires.show', $prestataire) }}" class="text-white bg-blue-600 hover:bg-blue-700 font-bold py-2 px-5 rounded-lg transition duration-300">
                                            Contacter le prestataire
                                        </a>
                                    </div>
                                </div>
                            @endif
                            
                            @if(count($availableSlots) > 0)
                                <div class="mb-6">
                                    <label for="client_notes" class="block text-md font-semibold text-gray-800 mb-2">
                                        Notes ou demandes particulières (optionnel)
                                    </label>
                                    <textarea id="client_notes" name="client_notes" rows="4" 
                                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                              placeholder="Décrivez vos besoins spécifiques, questions ou demandes particulières..."></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Maximum 1000 caractères</p>
                                </div>
                                
                                <div class="border-t-2 border-blue-200 pt-6">
                                    <div class="flex justify-between items-center mb-5">
                                        <span class="text-xl font-bold text-blue-900">Total à payer :</span>
                                        <span class="text-3xl font-extrabold text-blue-600">{{ number_format($service->price, 2) }} €</span>
                                    </div>
                                    
                                    <div class="flex gap-4">
                                        <a href="{{ url()->previous() }}" 
                                           class="flex-1 bg-blue-100 hover:bg-blue-200 text-blue-800 font-bold px-6 py-3 rounded-lg text-center transition duration-200">
                                            Retour
                                        </a>
                                        <button type="submit" 
                                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-lg transition duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:shadow-none disabled:transform-none"
                                                :disabled="!selectedSlot.time">
                                            Confirmer la réservation
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="border-t-2 border-blue-200 pt-6">
                                    <a href="{{ route('services.index') }}" 
                                       class="w-full bg-blue-100 hover:bg-blue-200 text-blue-800 font-bold px-6 py-3 rounded-lg text-center transition duration-200 block">
                                        Retour aux services
                                    </a>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bookingForm', () => ({
        selectedSlot: {
            date: '',
            time: ''
        },
        setSelectedSlot(date, time) {
            this.selectedSlot.date = date;
            this.selectedSlot.time = time;
        }
    }))
})
</script>
@endsection