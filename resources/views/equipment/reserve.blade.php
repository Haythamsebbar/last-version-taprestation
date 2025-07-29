@extends('layouts.app')

@section('title', 'Réserver ' . $equipment->name)

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_green.css">
<style>
    .flatpickr-day.unavailable {
        background-color: #f3f4f6;
        border-color: #f3f4f6;
        color: #d1d5db;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('equipment.index') }}" class="text-gray-700 hover:text-green-600">
                        Location de matériel
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('equipment.show', $equipment) }}" class="ml-1 text-gray-700 hover:text-green-600 md:ml-2">{{ $equipment->name }}</a>
                    </div>
                </li>
                 <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-gray-500 md:ml-2">Réservation</span>
                    </div>
                </li>
            </ol>
        </nav>

        <h1 class="text-3xl font-bold text-gray-900 mb-6">Réserver "{{ $equipment->name }}"</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Colonne de gauche : Formulaire de réservation -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <form action="{{ route('equipment.rent', $equipment) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Date de début</label>
                        <input type="text" id="start_date" name="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="Sélectionnez une date">
                    </div>

                    <div class="mb-4">
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Date de fin</label>
                        <input type="text" id="end_date" name="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="Sélectionnez une date">
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg my-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Résumé de la location</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Prix par jour:</span>
                                <span class="font-medium text-gray-900">{{ number_format($equipment->price_per_day, 2) }} €</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nombre de jours:</span>
                                <span id="rental_days" class="font-medium text-gray-900">0</span>
                            </div>
                            <div class="flex justify-between border-t pt-2 mt-2">
                                <span class="text-lg font-bold text-gray-800">Total estimé:</span>
                                <span id="total_price" class="text-lg font-bold text-gray-900">0.00 €</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-700 transition-colors">
                        Envoyer la demande de location
                    </button>
                </form>
            </div>

            <!-- Colonne de droite : Infos sur l'équipement -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-start">
                        <img src="{{ $equipment->main_photo ? Storage::url($equipment->main_photo) : 'https://via.placeholder.com/150' }}" alt="{{ $equipment->name }}" class="w-32 h-32 object-cover rounded-lg mr-6">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $equipment->name }}</h2>
                            @if($equipment->prestataire && $equipment->prestataire->user)
<p class="text-sm text-gray-500">Loué par <a href="#" class="text-green-600 hover:underline">{{ $equipment->prestataire->user->name }}</a></p>
@endif
                            <div class="flex items-center mt-2">
                                <span class="text-yellow-400">⭐</span>
                                <span class="text-gray-700 font-semibold ml-1">{{ number_format($equipment->average_rating, 1) }}</span>
                                <span class="text-gray-500 text-sm ml-2">({{ $equipment->reviews_count }} avis)</span>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Calendrier des disponibilités</h3>
                    
                    <!-- Affichage des dates de disponibilité -->
                    @if($availabilityPeriod['available_from'] || $availabilityPeriod['available_until'])
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                        <h4 class="text-sm font-medium text-green-800 mb-2">Période de disponibilité :</h4>
                        <div class="text-sm text-green-700">
                            @if($availabilityPeriod['available_from'])
                                <div class="flex items-center mb-1">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span><strong>Disponible à partir du :</strong> {{ \Carbon\Carbon::parse($availabilityPeriod['available_from'])->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            @if($availabilityPeriod['available_until'])
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span><strong>Disponible jusqu'au :</strong> {{ \Carbon\Carbon::parse($availabilityPeriod['available_until'])->format('d/m/Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    <div id="availability-calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const unavailableDates = @json($unavailableDates);
        const availabilityPeriod = @json($availabilityPeriod);
        const pricePerDay = {{ $equipment->price_per_day }};
        
        // Déterminer les dates min et max basées sur la disponibilité de l'équipement
        let minDate = "today";
        let maxDate = null;
        
        if (availabilityPeriod.available_from) {
            const availableFrom = new Date(availabilityPeriod.available_from);
            const today = new Date();
            minDate = availableFrom > today ? availabilityPeriod.available_from : "today";
        }
        
        if (availabilityPeriod.available_until) {
            maxDate = availabilityPeriod.available_until;
        }

        const calendar = flatpickr("#availability-calendar", {
            inline: true,
            mode: "range",
            dateFormat: "Y-m-d",
            minDate: minDate,
            maxDate: maxDate,
            disable: unavailableDates,
            onChange: function(selectedDates, dateStr, instance) {
                updatePrice(selectedDates);
            }
        });

        const startDatePicker = flatpickr("#start_date", {
            dateFormat: "Y-m-d",
            minDate: minDate,
            maxDate: maxDate,
            disable: unavailableDates,
            onChange: function(selectedDates, dateStr, instance) {
                endDatePicker.set('minDate', dateStr);
                updatePrice([selectedDates[0], endDatePicker.selectedDates[0]]);
            }
        });

        const endDatePicker = flatpickr("#end_date", {
            dateFormat: "Y-m-d",
            minDate: minDate,
            maxDate: maxDate,
            disable: unavailableDates,
            onChange: function(selectedDates, dateStr, instance) {
                updatePrice([startDatePicker.selectedDates[0], selectedDates[0]]);
            }
        });

        function updatePrice(dates) {
            if (dates.length === 2 && dates[0] && dates[1]) {
                const start = new Date(dates[0]);
                const end = new Date(dates[1]);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                
                document.getElementById('rental_days').textContent = diffDays;
                document.getElementById('total_price').textContent = (diffDays * pricePerDay).toFixed(2) + ' €';
            } else {
                 document.getElementById('rental_days').textContent = 0;
                document.getElementById('total_price').textContent = '0.00 €';
            }
        }
    });
</script>
@endpush