@extends('layouts.app')
@use('Illuminate\Support\Facades\Storage')
@section('title', 'Tableau de bord - Prestataire')

@section('content')
<div class="min-h-screen" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Zone de bienvenue -->
        <div class="mb-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-2 flex items-center justify-center"> 
    <span>Bienvenue Prestataire Approuvé</span>
    @if(auth()->user()->is_verified)
        <span class="ml-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
            <svg class="-ml-0.5 mr-1.5 h-4 w-4 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            Vérifié
        </span>
    @endif
</h1>
                <p class="text-xl text-gray-600">Gérez toutes vos activités depuis votre espace personnel</p>
            </div>
            
            <!-- Statistiques rapides centrées -->
            <div class="flex justify-center mb-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl">
                    <!-- Réservations -->
                    <a href="{{ route('prestataire.bookings.index') }}" class="dashboard-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center hover:shadow-lg hover:border-purple-200 transition-all duration-300">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 bg-purple-50 rounded-xl">
                            <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a2 2 0 100-4 2 2 0 000 4zm6-6V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-3" />
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1">{{ $bookingsCount ?? 0 }}</div>
                        <div class="text-sm font-medium text-gray-600">Réservations</div>
                    </a>

                    <!-- QR Code -->
                    <a href="{{ route('prestataire.qrcode.show') }}" class="dashboard-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center hover:shadow-lg hover:border-gray-200 transition-all duration-300">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 bg-gray-50 rounded-xl">
                            <svg class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1">QR</div>
                        <div class="text-sm font-medium text-gray-600">Mon QR Code</div>
                    </a>

                    <!-- Mes Vidéos -->
                    <a href="{{ route('prestataire.videos.create') }}" class="dashboard-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center hover:shadow-lg hover:border-red-200 transition-all duration-300">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 bg-red-50 rounded-xl">
                            <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1">+</div>
                        <div class="text-sm font-medium text-gray-600">Créer une vidéo</div>
                    </a>

                    <!-- Revenus générés -->
                    <div class="dashboard-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center hover:shadow-lg hover:border-green-200 transition-all duration-300">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 bg-green-50 rounded-xl">
                            <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($monthlyRentalRevenue ?? 0, 0, ',', ' ') }} €</div>
                        <div class="text-sm font-medium text-gray-600">Revenus ce mois</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bloc principal : Mes services, Mon matériel, Mes ventes urgentes -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Mes services -->
            <div class="dashboard-primary-card bg-white rounded-xl shadow-lg border border-gray-100 p-8 hover:shadow-xl transition-all duration-300 flex flex-col min-h-[400px]">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-12 h-12 bg-orange-50 rounded-xl">
                            <svg class="h-6 w-6 text-bleu-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8zM8 14v.01M12 14v.01M16 14v.01" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold text-gray-900">Mes services</h3>
                            <p class="text-base text-gray-600">{{ $totalServices ?? 0 }} service(s) actif(s)</p>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600 mb-1">{{ $activeServices ?? 0 }}</div>
                        <div class="text-sm font-medium text-gray-600">Services en cours</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600 mb-1">{{ $totalServices ?? 0 }}</div>
                        <div class="text-sm font-medium text-gray-600">Total services</div>
                    </div>
                </div>
                
                <div class="flex-grow"></div>
                <div class="space-y-4 mt-auto">
                    <a href="{{ route('prestataire.services.index') }}" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition-all duration-300 shadow-sm hover:shadow-md">
                        <svg class="-ml-1 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Gérer mes services
                    </a>
                    <a href="{{ route('prestataire.services.create') }}" class="w-full inline-flex items-center justify-center px-6 py-3 border border-blue-200 text-base font-semibold rounded-xl text-blue-700 bg-blue-50 hover:bg-blue-100 transition-all duration-300">
                        <svg class="-ml-1 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Créer un service
                    </a>
                </div>
            </div>

            <!-- Mon matériel -->
            <div class="dashboard-primary-card bg-white rounded-xl shadow-lg border border-gray-100 p-8 hover:shadow-xl transition-all duration-300 flex flex-col min-h-[400px]">
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 bg-green-50 rounded-xl">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold text-gray-900">Mon matériel</h3>
                        <p class="text-base text-gray-600">{{ $equipmentCount ?? 0 }} équipement(s) disponible(s)</p>
                    </div>
                </div>
                
                <!-- Statistiques rapides -->
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600 mb-1">{{ $equipmentRentalRequestsCount ?? 0 }}</div>
                        <div class="text-sm font-medium text-gray-600">Demandes en attente</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600 mb-1">{{ $activeRentalsCount ?? 0 }}</div>
                        <div class="text-sm font-medium text-gray-600">Locations en cours</div>
                    </div>
                </div>
                
                <div class="flex-grow"></div>
                <div class="space-y-4 mt-auto">
                    <a href="{{ route('prestataire.equipment.index') }}" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl text-white bg-green-600 hover:bg-green-700 transition-all duration-300 shadow-sm hover:shadow-md">
                        <svg class="-ml-1 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Gérer mon matériel
                    </a>
                    <a href="{{ route('prestataire.equipment.create') }}" class="w-full inline-flex items-center justify-center px-6 py-3 border border-green-200 text-base font-semibold rounded-xl text-green-700 bg-green-50 hover:bg-green-100 transition-all duration-300">
                        <svg class="-ml-1 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Ajouter un équipement
                    </a>
                </div>
            </div>

            <!-- Mes ventes urgentes -->
            <div class="dashboard-primary-card bg-white rounded-xl shadow-lg border border-gray-100 p-8 hover:shadow-xl transition-all duration-300 flex flex-col min-h-[400px]">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-12 h-12 bg-red-50 rounded-xl">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold text-gray-900">Mes ventes urgentes</h3>
                            <p class="text-base text-gray-600">Offres spéciales et promotions</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex-grow"></div>
                <div class="space-y-4 mt-auto">
                    <a href="#" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl text-white bg-red-600 hover:bg-red-700 transition-all duration-300 shadow-sm hover:shadow-md">
                        <svg class="-ml-1 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Gérer mes ventes urgentes
                    </a>
                    <a href="#" class="w-full inline-flex items-center justify-center px-6 py-3 border border-red-200 text-base font-semibold rounded-xl text-red-700 bg-red-50 hover:bg-red-100 transition-all duration-300">
                        <svg class="-ml-1 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Ajouter une vente urgente
                    </a>
                </div>
            </div>
        </div>

        <!-- Bloc secondaire : Carte fusionnée et profil/vérification -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Colonne 1 : Mes disponibilités et Agenda du jour (fusionnés) -->
            <div class="dashboard-primary-card bg-white rounded-xl shadow-lg border border-gray-100 p-8 hover:shadow-xl transition-all duration-300 flex flex-col min-h-[400px]">
                <!-- Section Mes disponibilités -->
                <div class="mb-3">
                    <div class="flex items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-8 h-8 bg-green-50 rounded-lg">
                                <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-bold text-gray-900">Mes disponibilités</h3>
                            <p class="text-sm text-gray-600">Planning de travail</p>
                        </div>
                    </div>
                    
                    <div class="text-center py-2">
                        @if($prestataire->is_available ?? true)
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Disponible
                            </div>
                        @else
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                Non disponible
                            </div>
                        @endif
                    </div>
                    
                    <div class="mb-2">
                        <a href="{{ route('prestataire.availability.index') }}" class="w-full inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-green-600 hover:bg-green-700 transition-all duration-300 shadow-sm hover:shadow-md">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a2 2 0 100-4 2 2 0 000 4zm6-6V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-3" />
                            </svg>
                            Gérer mes disponibilités
                        </a>
                    </div>
                </div>
                
                <!-- Séparateur visuel -->
                <div class="border-t border-gray-200 my-3"></div>
                
                <!-- Section Agenda du jour -->
                <div class="flex-grow">
                    <div class="flex items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-8 h-8 bg-green-50 rounded-lg">
                                <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a2 2 0 100-4 2 2 0 000 4zm6-6V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-3" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-bold text-gray-900">Agenda du jour</h3>
                            <p class="text-sm text-gray-600">{{ now()->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    
                    @if(isset($todayBookings) && count($todayBookings) > 0)
                        <div class="space-y-2 mb-3">
                            @foreach($todayBookings as $booking)
                                <div class="border border-gray-200 rounded-lg p-2 hover:border-green-200 transition-all duration-300">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900 mb-1">{{ $booking->service->title ?? 'Prestation' }}</h4>
                                            <p class="text-xs text-gray-600">{{ $booking->start_datetime->format('H:i') }} - {{ $booking->client->user->name ?? 'Client' }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <div class="flex items-center justify-center w-10 h-10 mx-auto mb-2 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-1">Aucune prestation prévue aujourd'hui</h3>
                            <p class="text-xs text-gray-500">Vos prochaines prestations apparaîtront ici</p>
                        </div>
                    @endif
                    
                    <a href="{{ route('prestataire.agenda.index') }}" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-green-600 hover:bg-green-700 transition-all duration-300 shadow-sm hover:shadow-lg mt-auto">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a2 2 0 100-4 2 2 0 000 4zm6-6V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-3" />
                        </svg>
                        Voir l'agenda complet
                    </a>
                </div>
            </div>

            <!-- Colonne 2 : Mon profil -->
            <div class="dashboard-primary-card bg-white rounded-xl shadow-lg border border-gray-100 p-8 hover:shadow-xl transition-all duration-300 flex flex-col min-h-[400px]">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Succès</p>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Erreur</p>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-12 h-12 bg-purple-50 rounded-xl">
                                <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold text-gray-900">Mon profil</h3>
                            <p class="text-base text-gray-600">Progression de complétion</p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-600">Profil complété</span>
                        <span class="text-sm font-bold text-purple-600">{{ $profileCompletion['percentage'] }}%</span>
                    </div>
                    <div class="w-full bg-purple-100 rounded-full h-2.5">
                        <div class="bg-purple-600 h-2.5 rounded-full" style="width: {{ $profileCompletion['percentage'] }}%"></div>
                    </div>
                </div>

                <ul class="space-y-3 mb-6 text-sm text-gray-600">
                    @foreach($profileCompletion['missing_fields'] as $field)
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>{{ $field }}</span>
                        </li>
                    @endforeach
                    @if(empty($profileCompletion['missing_fields']))
                        <li class="flex items-center text-green-600">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Votre profil est complet !</span>
                        </li>
                    @endif
                </ul>
                <a href="{{ route('prestataire.profile.edit') }}" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl text-white bg-purple-600 hover:bg-purple-700 transition-all duration-300 shadow-sm hover:shadow-md mt-auto">
                    <svg class="-ml-1 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z" />
                    </svg>
                    Compléter mon profil
                </a>
                <!-- Section Gestion des vidéos -->
                <div class="border-t border-gray-200 pt-6 mb-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-8 h-8 bg-purple-50 rounded-lg">
                                <svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <!-- Séparateur visuel -->
                
                        <div class="ml-3">
                            <div class="border-t border-gray-200 my-4"></div>
                            <h4 class="text-lg font-semibold text-gray-900">Mes vidéos</h4>
                            <p class="text-sm text-gray-600">Gestion de vos contenus vidéo</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <a href="#" class="inline-flex items-center justify-center px-3 py-2 border border-purple-200 text-sm font-medium rounded-lg text-purple-700 bg-purple-50 hover:bg-purple-100 transition-all duration-300">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Ajouter
                        </a>
                        <a href="#" class="inline-flex items-center justify-center px-3 py-2 border border-purple-200 text-sm font-medium rounded-lg text-purple-700 bg-purple-50 hover:bg-purple-100 transition-all duration-300">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Gérer
                        </a>
                    </div>
                    
                    <div class="text-center py-2">
                        <p class="text-xs text-gray-500">
                            @if(isset($videosCount) && $videosCount > 0)
                                {{ $videosCount }} vidéo(s) publiée(s)
                            @else
                                Aucune vidéo publiée
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex-grow"></div>
                
            </div>

            <!-- Colonne 3 : Vérification de compte -->
            <div class="dashboard-primary-card bg-white rounded-xl shadow-lg border border-gray-100 p-8 hover:shadow-xl transition-all duration-300 flex flex-col min-h-[400px]">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-12 h-12 bg-blue-50 rounded-xl">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold text-gray-900">Vérification de compte</h3>
                            <p class="text-base text-gray-600">
                                @if(auth()->user()->prestataire->isVerified())
                                    <span class="text-orange-600 font-medium">✓ Compte vérifié</span>
                                @else
                                    <span class="text-orange-600 font-medium">En attente de vérification</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @if(auth()->user()->prestataire->isVerified())
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Vérifié
                            </span>
                        </div>
                    @endif
                </div>
                
                @if(auth()->user()->prestataire->isVerified())
                    <div class="text-center py-6">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-orange-50 rounded-xl">
                            <svg class="w-8 h-8 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900 mb-1">Félicitations !</h3>
                        <p class="text-sm text-gray-500 mb-4">Votre compte est vérifié et bénéficie du badge "Prestataire Vérifié"</p>
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-orange-50 rounded-xl">
                            <svg class="w-8 h-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900 mb-1">Vérifiez votre compte</h3>
                        <p class="text-sm text-gray-500 mb-4">Obtenez le badge "Prestataire Vérifié" pour gagner la confiance des clients</p>
                    </div>
                @endif
                
                <div class="flex-grow"></div>
                <a href="{{ route('prestataire.verification.index') }}" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl text-white bg-orange-600 hover:bg-orange-700 transition-all duration-300 shadow-sm hover:shadow-md mt-auto">
                    <svg class="-ml-1 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    @if(auth()->user()->prestataire->isVerified())
                        Gérer ma vérification
                    @else
                        Vérifier mon compte
                    @endif
                </a>
            </div>
        </div>
        
    </div>
</div>


@push('scripts')
<script>
    document.getElementById('open-verification-modal').addEventListener('click', function() {
        document.getElementById('verificationModal').classList.remove('hidden');
    });
</script>
@endpush
@endsection