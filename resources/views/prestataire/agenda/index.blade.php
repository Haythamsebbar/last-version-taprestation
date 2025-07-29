@extends('layouts.app')

@section('title', 'Mon Agenda - Prestataire')

@section('content')
<div class="container mx-auto py-8 px-4">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Mon Agenda</h1>
                <p class="text-gray-600 mt-2">Gérez vos réservations et planifiez vos prestations</p>
            </div>
            <div class="flex space-x-3">
                <!-- Boutons de vue -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-1 flex">
                    <button onclick="changeView('month')" class="view-btn px-3 py-2 text-sm font-medium rounded-md {{ $view === 'month' ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">
                        Mois
                    </button>
                    <button onclick="changeView('week')" class="view-btn px-3 py-2 text-sm font-medium rounded-md {{ $view === 'week' ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">
                        Semaine
                    </button>
                    <button onclick="changeView('list')" class="view-btn px-3 py-2 text-sm font-medium rounded-md {{ $view === 'list' ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">
                        Liste
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a2 2 0 100-4 2 2 0 000 4zm6-6V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-3" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Confirmées</dt>
                            <dd class="text-2xl font-bold text-green-600">{{ $stats['confirmed'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">En attente</dt>
                            <dd class="text-2xl font-bold text-orange-600">{{ $stats['pending'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Terminées</dt>
                            <dd class="text-2xl font-bold text-indigo-600">{{ $stats['completed'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section supprimée : Informations financières -->
        <!-- Les revenus ont été supprimés pour des raisons de confidentialité -->
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Recherche -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                <div class="relative">
                    <input type="text" id="search" name="search" value="{{ $search }}" 
                           placeholder="Client, service, numéro..." 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Filtre par service -->
            <div>
                <label for="service_filter" class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                <select id="service_filter" name="service" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous les services</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ $serviceFilter == $service->id ? 'selected' : '' }}>
                            {{ $service->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filtre par statut -->
            <div>
                <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select id="status_filter" name="status" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="confirmed" {{ $statusFilter === 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                    <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>Terminé</option>
                    <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end space-x-2">
                <button onclick="applyFilters()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    Filtrer
                </button>
                <button onclick="clearFilters()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50 transition-colors">
                    Effacer
                </button>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @if($view === 'list')
            <!-- Vue liste -->
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Prochaines réservations</h3>
                @if($bookings->count() > 0)
                    <div class="space-y-4">
                        @foreach($bookings as $booking)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer" onclick="showBookingDetails({{ $booking->id }})">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <h4 class="text-lg font-medium text-gray-900">{{ $booking->service->title ?? 'Service' }}</h4>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                                @elseif($booking->status === 'pending') bg-orange-100 text-orange-800
                                                @elseif($booking->status === 'completed') bg-indigo-100 text-indigo-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </div>
                                        <div class="mt-2 space-y-1">
                                            <p class="text-sm text-gray-600">
                                                <svg class="inline h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                Client: {{ $booking->client->user->name ?? 'N/A' }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <svg class="inline h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a2 2 0 100-4 2 2 0 000 4zm6-6V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-3" />
                                                </svg>
                                                {{ $booking->start_datetime->format('d/m/Y à H:i') }} - {{ $booking->end_datetime->format('H:i') }}
                                            </p>
                                            <!-- Informations financières supprimées -->
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">#{{ $booking->booking_number }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $booking->start_datetime->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a2 2 0 100-4 2 2 0 000 4zm6-6V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-3" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune réservation</h3>
                        <p class="mt-1 text-sm text-gray-500">Aucune réservation trouvée pour les critères sélectionnés.</p>
                    </div>
                @endif
            </div>
        @else
            <!-- Vue calendrier -->
            <div class="p-6">
                <div id="calendar"></div>
            </div>
        @endif
    </div>
</div>

<!-- Modal pour les détails de réservation -->
<div id="bookingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Détails de la réservation</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="modalContent">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<style>
    .fc-event {
        cursor: pointer;
    }
    .fc-event:hover {
        opacity: 0.8;
    }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script>
    let calendar;
    let currentView = '{{ $view }}';
    
    document.addEventListener('DOMContentLoaded', function() {
        @if($view !== 'list')
            initCalendar();
        @endif
    });
    
    function initCalendar() {
        const calendarEl = document.getElementById('calendar');
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: currentView === 'week' ? 'timeGridWeek' : 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            locale: 'fr',
            firstDay: 1,
            height: 'auto',
            events: '{{ route("prestataire.agenda.events") }}',
            eventClick: function(info) {
                showBookingDetails(info.event.id);
            },
            eventDidMount: function(info) {
                // Ajouter des tooltips
                info.el.setAttribute('title', 
                    info.event.title + '\n' +
                    'Client: ' + info.event.extendedProps.client + '\n' +
                    'Statut: ' + info.event.extendedProps.status
                );
            }
        });
        
        calendar.render();
    }
    
    function changeView(view) {
        currentView = view;
        
        // Mettre à jour les boutons
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.classList.remove('bg-blue-100', 'text-blue-700');
            btn.classList.add('text-gray-500', 'hover:text-gray-700');
        });
        
        event.target.classList.remove('text-gray-500', 'hover:text-gray-700');
        event.target.classList.add('bg-blue-100', 'text-blue-700');
        
        // Rediriger avec le nouveau paramètre de vue
        const url = new URL(window.location);
        url.searchParams.set('view', view);
        window.location.href = url.toString();
    }
    
    function applyFilters() {
        const url = new URL(window.location);
        
        // Récupérer les valeurs des filtres
        const search = document.getElementById('search').value;
        const service = document.getElementById('service_filter').value;
        const status = document.getElementById('status_filter').value;
        
        // Mettre à jour les paramètres URL
        if (search) url.searchParams.set('search', search);
        else url.searchParams.delete('search');
        
        if (service) url.searchParams.set('service', service);
        else url.searchParams.delete('service');
        
        if (status) url.searchParams.set('status', status);
        else url.searchParams.delete('status');
        
        window.location.href = url.toString();
    }
    
    function clearFilters() {
        const url = new URL(window.location);
        url.searchParams.delete('search');
        url.searchParams.delete('service');
        url.searchParams.delete('status');
        url.searchParams.delete('client');
        window.location.href = url.toString();
    }
    
    function showBookingDetails(bookingId) {
        fetch(`/prestataire/agenda/booking/${bookingId}`)
            .then(response => response.json())
            .then(data => {
                const booking = data.booking;
                const modalContent = document.getElementById('modalContent');
                
                modalContent.innerHTML = `
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Service</label>
                                <p class="mt-1 text-sm text-gray-900">${booking.service?.title || 'N/A'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Numéro de réservation</label>
                                <p class="mt-1 text-sm text-gray-900">#${booking.booking_number}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Client</label>
                                <p class="mt-1 text-sm text-gray-900">${booking.client?.user?.name || 'N/A'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Statut</label>
                                <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                    booking.status === 'confirmed' ? 'bg-green-100 text-green-800' :
                                    booking.status === 'pending' ? 'bg-orange-100 text-orange-800' :
                                    booking.status === 'completed' ? 'bg-indigo-100 text-indigo-800' :
                                    'bg-red-100 text-red-800'
                                }">
                                    ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date et heure</label>
                                <p class="mt-1 text-sm text-gray-900">${new Date(booking.start_datetime).toLocaleString('fr-FR')}</p>
                            </div>
                            <!-- Informations financières supprimées -->
                        </div>
                        
                        ${booking.client_notes ? `
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Notes du client</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md">${booking.client_notes}</p>
                            </div>
                        ` : ''}
                        
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            ${data.canConfirm ? `
                                <button onclick="updateBookingStatus(${booking.id}, 'confirmed')" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                    Confirmer
                                </button>
                            ` : ''}
                            
                            ${data.canCancel ? `
                                <button onclick="cancelBooking(${booking.id})" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                    Annuler
                                </button>
                            ` : ''}
                            
                            ${data.canComplete ? `
                                <button onclick="updateBookingStatus(${booking.id}, 'completed')" 
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                    Marquer terminé
                                </button>
                            ` : ''}
                            
                            <button onclick="closeModal()" 
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md text-sm font-medium">
                                Fermer
                            </button>
                        </div>
                    </div>
                `;
                
                document.getElementById('bookingModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors du chargement des détails');
            });
    }
    
    function updateBookingStatus(bookingId, status) {
        fetch(`/prestataire/agenda/booking/${bookingId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                closeModal();
                location.reload();
            } else {
                alert(data.message || 'Erreur lors de la mise à jour');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la mise à jour');
        });
    }
    
    function cancelBooking(bookingId) {
        const reason = prompt('Raison de l\'annulation (optionnel):');
        if (reason === null) return; // Utilisateur a annulé
        
        fetch(`/prestataire/agenda/booking/${bookingId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                status: 'cancelled',
                reason: reason
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                closeModal();
                location.reload();
            } else {
                alert(data.message || 'Erreur lors de l\'annulation');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'annulation');
        });
    }
    
    function closeModal() {
        document.getElementById('bookingModal').classList.add('hidden');
    }
    
    // Fermer le modal en cliquant à l'extérieur
    document.getElementById('bookingModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endpush