@extends('layouts.app')

@section('title', 'Actualités - TaPrestation')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-12">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">Actualités & Conseils</h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Découvrez nos derniers articles, conseils et tendances pour optimiser vos projets et collaborations.
                </p>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <form method="GET" action="{{ route('articles.index') }}" class="flex flex-col md:flex-row gap-4">
                <!-- Search Input -->
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Rechercher dans les articles..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <!-- Tag Filter -->
                @if($availableTags && count($availableTags) > 0)
                <div class="md:w-64">
                    <select name="tag" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous les sujets</option>
                        @foreach($availableTags as $tag)
                            <option value="{{ $tag }}" {{ request('tag') === $tag ? 'selected' : '' }}>
                                {{ ucfirst($tag) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <!-- Search Button -->
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Rechercher
                </button>
            </form>
        </div>
    </div>

    <!-- Articles Grid -->
    <div class="container mx-auto px-4 pb-16">
        @if($articles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($articles as $article)
                    <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                        <!-- Featured Image -->
                        @if($article->featured_image)
                            <div class="h-48 bg-cover bg-center" style="background-image: url('{{ asset('storage/' . $article->featured_image) }}');"></div>
                        @else
                            <div class="h-48 bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center">
                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Article Content -->
                        <div class="p-6">
                            <!-- Meta Information -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    @if($article->tags && count($article->tags) > 0)
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-2">
                                            {{ $article->tags[0] }}
                                        </span>
                                    @endif
                                    <span class="text-sm text-gray-500">
                                        📅 {{ $article->published_at->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Title -->
                            <h2 class="text-xl font-semibold text-gray-800 mb-3 line-clamp-2">
                                <a href="{{ route('articles.show', $article->slug) }}" class="hover:text-blue-600 transition duration-300">
                                    {{ $article->title }}
                                </a>
                            </h2>
                            
                            <!-- Excerpt -->
                            <p class="text-gray-600 mb-4 line-clamp-3">{{ $article->formatted_excerpt }}</p>
                            
                            <!-- Read More Link -->
                            <a href="{{ route('articles.show', $article->slug) }}" 
                               class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800 transition duration-300">
                                Lire l'article
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($articles->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $articles->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <!-- No Articles Found -->
            <div class="text-center py-16">
                <svg class="w-24 h-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
                <h3 class="text-2xl font-semibold text-gray-600 mb-4">
                    @if(request('search') || request('tag'))
                        Aucun article trouvé
                    @else
                        Aucune actualité disponible
                    @endif
                </h3>
                <p class="text-gray-500 mb-6">
                    @if(request('search') || request('tag'))
                        Essayez de modifier vos critères de recherche.
                    @else
                        Les dernières actualités apparaîtront ici prochainement.
                    @endif
                </p>
                @if(request('search') || request('tag'))
                    <a href="{{ route('articles.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-300">
                        Voir tous les articles
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection