@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100">
    <!-- Contenu principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- En-tête de la page -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Toutes mes demandes</h1>
                    <p class="mt-2 text-gray-600">Gérez toutes vos demandes de services, matériel et ventes urgentes</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('client.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour au tableau de bord
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-blue-200">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-list text-blue-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $allUnifiedRequests->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-briefcase text-blue-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Services</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $groupedRequests['service']->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-tools text-green-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Matériel</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $groupedRequests['equipment']->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-bolt text-red-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Ventes urgentes</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $groupedRequests['urgent_sale']->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglets de filtrage -->
        <div class="bg-white shadow-lg rounded-lg border border-blue-200">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <button onclick="showTab('all')" id="tab-all" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm active">
                        Toutes ({{ $allUnifiedRequests->count() }})
                    </button>
                    <button onclick="showTab('service')" id="tab-service" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Services ({{ $groupedRequests['service']->count() }})
                    </button>
                    <button onclick="showTab('equipment')" id="tab-equipment" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Matériel ({{ $groupedRequests['equipment']->count() }})
                    </button>
                    <button onclick="showTab('urgent_sale')" id="tab-urgent_sale" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Ventes urgentes ({{ $groupedRequests['urgent_sale']->count() }})
                    </button>
                </nav>
            </div>

            <!-- Contenu des onglets -->
            <div class="p-6">
                <!-- Toutes les demandes -->
                <div id="content-all" class="tab-content">
                    @if($allUnifiedRequests->count() > 0)
                        <div class="space-y-4">
                            @foreach($allUnifiedRequests as $request)
                                <div class="border border-blue-200 rounded-lg p-4 hover:shadow-lg transition-shadow duration-200 bg-white">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $request['badge_color'] }}">
                                                    {{ $request['badge_text'] }}
                                                </span>
                                                <h3 class="text-lg font-medium text-gray-900">{{ $request['title'] }}</h3>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-600">{{ $request['prestataire'] }}</p>
                                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                                <span class="flex items-center">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    {{ $request['date']->format('d/m/Y H:i') }}
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Statut: {{ ucfirst($request['status']) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if($request['type'] === 'service')
                                                <a href="{{ route('client.bookings.show', $request['id']) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <i class="fas fa-eye mr-1"></i>
                                                    Voir
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune demande trouvée</h3>
                            <p class="text-gray-500">Vous n'avez encore effectué aucune demande.</p>
                        </div>
                    @endif
                </div>

                <!-- Services -->
                <div id="content-service" class="tab-content hidden">
                    @if($groupedRequests['service']->count() > 0)
                        <div class="space-y-4">
                            @foreach($groupedRequests['service'] as $request)
                                <div class="border border-blue-200 rounded-lg p-4 hover:shadow-lg transition-shadow duration-200 bg-white">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $request['badge_color'] }}">
                                                    {{ $request['badge_text'] }}
                                                </span>
                                                <h3 class="text-lg font-medium text-gray-900">{{ $request['title'] }}</h3>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-600">{{ $request['prestataire'] }}</p>
                                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                                <span class="flex items-center">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    {{ $request['date']->format('d/m/Y H:i') }}
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Statut: {{ ucfirst($request['status']) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('client.bookings.show', $request['id']) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-eye mr-1"></i>
                                                Voir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-briefcase text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune demande de service</h3>
                            <p class="text-gray-500">Vous n'avez encore effectué aucune demande de service.</p>
                        </div>
                    @endif
                </div>

                <!-- Matériel -->
                <div id="content-equipment" class="tab-content hidden">
                    @if($groupedRequests['equipment']->count() > 0)
                        <div class="space-y-4">
                            @foreach($groupedRequests['equipment'] as $request)
                                <div class="border border-blue-200 rounded-lg p-4 hover:shadow-lg transition-shadow duration-200 bg-white">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $request['badge_color'] }}">
                                                    {{ $request['badge_text'] }}
                                                </span>
                                                <h3 class="text-lg font-medium text-gray-900">{{ $request['title'] }}</h3>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-600">{{ $request['prestataire'] }}</p>
                                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                                <span class="flex items-center">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    {{ $request['date']->format('d/m/Y H:i') }}
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Statut: {{ ucfirst($request['status']) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-tools text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune demande de matériel</h3>
                            <p class="text-gray-500">Vous n'avez encore effectué aucune demande de location de matériel.</p>
                        </div>
                    @endif
                </div>

                <!-- Ventes urgentes -->
                <div id="content-urgent_sale" class="tab-content hidden">
                    @if($groupedRequests['urgent_sale']->count() > 0)
                        <div class="space-y-4">
                            @foreach($groupedRequests['urgent_sale'] as $request)
                                <div class="border border-blue-200 rounded-lg p-4 hover:shadow-lg transition-shadow duration-200 bg-white">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $request['badge_color'] }}">
                                                    {{ $request['badge_text'] }}
                                                </span>
                                                <h3 class="text-lg font-medium text-gray-900">{{ $request['title'] }}</h3>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-600">{{ $request['prestataire'] }}</p>
                                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                                <span class="flex items-center">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    {{ $request['date']->format('d/m/Y H:i') }}
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Statut: {{ ucfirst($request['status']) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-bolt text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun contact de vente urgente</h3>
                            <p class="text-gray-500">Vous n'avez encore effectué aucun contact pour une vente urgente.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.tab-button.active {
    border-color: #3B82F6;
    color: #3B82F6;
}
</style>

<script>
function showTab(tabName) {
    // Masquer tous les contenus
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Désactiver tous les onglets
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Afficher le contenu sélectionné
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Activer l'onglet sélectionné
    document.getElementById('tab-' + tabName).classList.add('active');
}

// Initialiser avec l'onglet "Toutes"
document.addEventListener('DOMContentLoaded', function() {
    showTab('all');
});
</script>
@endsection