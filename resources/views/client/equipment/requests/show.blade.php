@extends('layouts.app')

@section('title', 'Confirmation de votre demande de location')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p class="font-bold">Succès</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Détails de votre demande</h1>
                <p class="text-gray-600 mb-6">Votre demande de location a bien été enregistrée. Le prestataire va l'examiner et vous recevrez une notification dès qu'elle sera traitée.</p>

                <div class="border-t border-gray-200 pt-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Numéro de demande</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $request->request_number }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Statut</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($request->status) }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Équipement</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $request->equipment->name }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Date de début</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $request->start_date->format('d/m/Y') }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Date de fin</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $request->end_date->format('d/m/Y') }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Durée</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $request->duration_days }} jour(s)</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Montant total estimé</dt>
                            <dd class="mt-1 text-sm font-bold text-gray-900">{{ number_format($request->final_amount, 2) }} €</dd>
                        </div>
                        @if($request->delivery_required)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Livraison demandée</dt>
                            <dd class="mt-1 text-sm text-gray-900">Oui</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Adresse de livraison</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $request->delivery_address }}</dd>
                        </div>
                        @endif
                        @if($request->message)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Votre message</dt>
                            <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $request->message }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4">
                <a href="{{ route('client.equipment-rental-requests.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">Retour à mes demandes</a>
            </div>
        </div>
    </div>
</div>
@endsection