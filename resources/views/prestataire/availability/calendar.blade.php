@extends('layouts.app')

@section('title', 'Calendrier des disponibilités - Prestataire')

@section('styles')
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css' />
<style>
    .fc-event {
        cursor: pointer;
    }
    .fc-event.availability {
        background-color: rgba(52, 152, 219, 0.7);
        border-color: #3498db;
    }
    .fc-event.exception {
        background-color: rgba(231, 76, 60, 0.7);
        border-color: #e74c3c;
    }
    .fc-event.booking {
        background-color: rgba(46, 204, 113, 0.7);
        border-color: #2ecc71;
    }
    .fc-event.custom-hours {
        background-color: rgba(241, 196, 15, 0.7);
        border-color: #f1c40f;
    }
    .legend-item {
        display: flex;
        align-items: center;
        margin-right: 15px;
    }
    .legend-color {
        width: 12px;
        height: 12px;
        margin-right: 5px;
        border-radius: 2px;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto py-8 px-4">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Calendrier des disponibilités</h1>
                <p class="text-gray-600 mt-2">Visualisez vos disponibilités, exceptions et réservations</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('prestataire.availability.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Paramètres de disponibilité
                </a>
                <a href="{{ route('prestataire.agenda.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Agenda des réservations
                </a>
            </div>
        </div>
    </div>

    <!-- Légende du calendrier -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <h3 class="text-sm font-medium text-gray-700 mb-3">Légende</h3>
        <div class="flex flex-wrap">
            <div class="legend-item">
                <div class="legend-color" style="background-color: rgba(52, 152, 219, 0.7);"></div>
                <span class="text-sm text-gray-600">Disponibilité hebdomadaire</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: rgba(231, 76, 60, 0.7);"></div>
                <span class="text-sm text-gray-600">Indisponibilité</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: rgba(241, 196, 15, 0.7);"></div>
                <span class="text-sm text-gray-600">Horaires spéciaux</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: rgba(46, 204, 113, 0.7);"></div>
                <span class="text-sm text-gray-600">Réservation</span>
            </div>
        </div>
    </div>

    <!-- Calendrier -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div id="calendar" class="p-4"></div>
    </div>
</div>

<!-- Modal pour les détails d'un événement -->
<div id="eventModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 id="eventTitle" class="text-lg font-medium text-gray-900">Détails de l'événement</h3>
                <button id="closeEventModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="eventDetails" class="space-y-4">
                <!-- Les détails de l'événement seront injectés ici par JavaScript -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/locales/fr.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const eventModal = document.getElementById('eventModal');
        const closeEventModal = document.getElementById('closeEventModal');
        const eventTitle = document.getElementById('eventTitle');
        const eventDetails = document.getElementById('eventDetails');

        // Initialisation du calendrier
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: 'fr',
            timeZone: 'local',
            slotMinTime: '06:00:00',
            slotMaxTime: '22:00:00',
            allDaySlot: false,
            height: 'auto',
            expandRows: true,
            slotDuration: '00:30:00',
            slotLabelInterval: '01:00:00',
            dayMaxEvents: true,
            nowIndicator: true,
            businessHours: {
                daysOfWeek: [1, 2, 3, 4, 5], // Lundi au vendredi
                startTime: '09:00',
                endTime: '18:00',
            },
            eventSources: [
                // Source pour les disponibilités hebdomadaires
                {
                    url: '{{ route("prestataire.availability.events", ["type" => "weekly"]) }}',
                    color: 'rgba(52, 152, 219, 0.7)',
                    textColor: 'white',
                    className: 'availability'
                },
                // Source pour les exceptions (indisponibilités et horaires spéciaux)
                {
                    url: '{{ route("prestataire.availability.events", ["type" => "exceptions"]) }}',
                    textColor: 'white',
                    className: 'exception'
                },
                // Source pour les réservations
                {
                    url: '{{ route("prestataire.availability.events", ["type" => "bookings"]) }}',
                    color: 'rgba(46, 204, 113, 0.7)',
                    textColor: 'white',
                    className: 'booking'
                }
            ],
            eventClick: function(info) {
                showEventDetails(info.event);
            },
            eventDidMount: function(info) {
                // Ajouter des tooltips aux événements
                const tooltip = new Tooltip(info.el, {
                    title: info.event.title,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        });

        calendar.render();

        // Fonction pour afficher les détails d'un événement
        function showEventDetails(event) {
            const eventType = event.extendedProps.type;
            const startTime = event.start ? formatTime(event.start) : '';
            const endTime = event.end ? formatTime(event.end) : '';
            
            eventTitle.textContent = event.title;
            
            let detailsHTML = '';
            
            if (eventType === 'weekly') {
                detailsHTML = `
                    <div>
                        <p class="text-sm text-gray-600">Disponibilité hebdomadaire récurrente</p>
                        <p class="text-sm font-medium mt-2">${startTime} - ${endTime}</p>
                    </div>
                `;
            } else if (eventType === 'exception') {
                const exceptionType = event.extendedProps.exceptionType;
                const reason = event.extendedProps.reason || 'Non spécifiée';
                
                let typeLabel = '';
                switch (exceptionType) {
                    case 'unavailable': typeLabel = 'Non disponible'; break;
                    case 'holiday': typeLabel = 'Jour férié'; break;
                    case 'vacation': typeLabel = 'Vacances'; break;
                    case 'sick_leave': typeLabel = 'Congé maladie'; break;
                    case 'custom_hours': typeLabel = 'Horaires personnalisés'; break;
                    default: typeLabel = 'Exception';
                }
                
                detailsHTML = `
                    <div>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${exceptionType === 'custom_hours' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'} mr-2">
                                ${typeLabel}
                            </span>
                            <span class="text-sm text-gray-600">${formatDate(event.start)}</span>
                        </div>
                        ${exceptionType === 'custom_hours' ? `<p class="text-sm font-medium mt-2">${startTime} - ${endTime}</p>` : ''}
                        <p class="text-sm text-gray-600 mt-2">Raison: ${reason}</p>
                    </div>
                `;
            } else if (eventType === 'booking') {
                const bookingId = event.extendedProps.bookingId;
                const clientName = event.extendedProps.clientName || 'Client';
                const serviceName = event.extendedProps.serviceName || 'Service';
                const status = event.extendedProps.status || 'pending';
                // Prix supprimé pour des raisons de confidentialité
                
                let statusLabel = '';
                let statusClass = '';
                switch (status) {
                    case 'confirmed':
                        statusLabel = 'Confirmée';
                        statusClass = 'bg-green-100 text-green-800';
                        break;
                    case 'pending':
                        statusLabel = 'En attente';
                        statusClass = 'bg-yellow-100 text-yellow-800';
                        break;
                    case 'completed':
                        statusLabel = 'Terminée';
                        statusClass = 'bg-blue-100 text-blue-800';
                        break;
                    case 'cancelled':
                        statusLabel = 'Annulée';
                        statusClass = 'bg-red-100 text-red-800';
                        break;
                    default:
                        statusLabel = 'Inconnue';
                        statusClass = 'bg-gray-100 text-gray-800';
                }
                
                detailsHTML = `
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                ${statusLabel}
                            </span>
                            <span class="text-sm text-gray-600">${formatDate(event.start)}</span>
                        </div>
                        <p class="text-sm font-medium mt-2">${startTime} - ${endTime}</p>
                        <p class="text-sm text-gray-600 mt-2">Service: ${serviceName}</p>
                        <p class="text-sm text-gray-600">Client: ${clientName}</p>
                        <!-- Prix supprimé pour des raisons de confidentialité -->
                        <div class="mt-4">
                            <a href="{{ url('prestataire/agenda') }}/${bookingId}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Voir les détails
                            </a>
                        </div>
                    </div>
                `;
            }
            
            eventDetails.innerHTML = detailsHTML;
            eventModal.classList.remove('hidden');
        }

        // Fermer le modal
        closeEventModal.addEventListener('click', function() {
            eventModal.classList.add('hidden');
        });

        // Fermer le modal en cliquant en dehors
        window.addEventListener('click', function(event) {
            if (event.target === eventModal) {
                eventModal.classList.add('hidden');
            }
        });

        // Fonction pour formater l'heure
        function formatTime(date) {
            return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        }

        // Fonction pour formater la date
        function formatDate(date) {
            return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }
    });
</script>
@endpush