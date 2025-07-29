@extends('layouts.app')

@section('title', 'À propos - TaPrestation')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">À propos de TaPrestation</h1>
            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                La plateforme qui connecte les clients avec les meilleurs prestataires de services
            </p>
        </div>
    </div>

    <!-- Mission Section -->
    <div class="py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Notre Mission</h2>
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div>
                        <p class="text-lg text-gray-600 mb-6">
                            TaPrestation a été créée avec une vision simple : faciliter la rencontre entre les clients ayant des besoins spécifiques et les prestataires qualifiés capables de les satisfaire.
                        </p>
                        <p class="text-lg text-gray-600 mb-6">
                            Nous croyons que chaque projet mérite d'être réalisé par le bon professionnel, et que chaque prestataire mérite de trouver les clients qui valorisent son expertise.
                        </p>
                    </div>
                    <div class="bg-white p-8 rounded-lg shadow-lg">
                        <h3 class="text-xl font-semibold mb-4 text-blue-600">Nos Valeurs</h3>
                        <ul class="space-y-3">
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-blue-600 rounded-full mr-3"></span>
                                Qualité et professionnalisme
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-blue-600 rounded-full mr-3"></span>
                                Transparence et confiance
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-blue-600 rounded-full mr-3"></span>
                                Innovation et simplicité
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-blue-600 rounded-full mr-3"></span>
                                Support et accompagnement
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="bg-white py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Pourquoi choisir TaPrestation ?</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Prestataires Vérifiés</h3>
                    <p class="text-gray-600">Tous nos prestataires sont soigneusement sélectionnés et vérifiés pour garantir la qualité de leurs services.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Tarifs Transparents</h3>
                    <p class="text-gray-600">Pas de frais cachés. Les prestataires affichent leurs tarifs clairement et vous savez exactement ce que vous payez.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Communication Facilitée</h3>
                    <p class="text-gray-600">Notre système de messagerie intégré vous permet de communiquer facilement avec les prestataires.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-gray-100 py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">TaPrestation en chiffres</h2>
            <div class="grid md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold text-blue-600 mb-2">500+</div>
                    <div class="text-gray-600">Prestataires actifs</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-green-600 mb-2">1000+</div>
                    <div class="text-gray-600">Projets réalisés</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-purple-600 mb-2">50+</div>
                    <div class="text-gray-600">Catégories de services</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-orange-600 mb-2">4.8/5</div>
                    <div class="text-gray-600">Note moyenne</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="py-16">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-8 text-gray-800">Une question ? Contactez-nous</h2>
            <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                Notre équipe est là pour vous accompagner dans votre expérience sur TaPrestation.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="mailto:contact@taprestation.com" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-300">
                    Nous contacter
                </a>
                <a href="{{ route('register') }}" class="border border-blue-600 text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition duration-300">
                    Rejoindre TaPrestation
                </a>
            </div>
        </div>
    </div>
</div>
@endsection