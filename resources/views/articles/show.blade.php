@extends('layouts.app')

@section('title', $article->title . ' - TaPrestation')
@section('meta_description', $article->meta_description ?: $article->formatted_excerpt)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Article Header -->
    <div class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-8">
            <!-- Breadcrumb -->
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm text-gray-500">
                    <li><a href="{{ route('home') }}" class="hover:text-blue-600">Accueil</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li><a href="{{ route('articles.index') }}" class="hover:text-blue-600">Actualités</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-gray-800">{{ $article->title }}</li>
                </ol>
            </nav>
            
            <!-- Article Meta -->
            <div class="mb-6">
                <div class="flex items-center mb-4">
                    @if($article->tags && count($article->tags) > 0)
                        @foreach($article->tags as $tag)
                            <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full mr-2">
                                {{ $tag }}
                            </span>
                        @endforeach
                    @endif
                </div>
                
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4 leading-tight">
                    {{ $article->title }}
                </h1>
                
                @if($article->excerpt)
                    <p class="text-xl text-gray-600 mb-6 leading-relaxed">
                        {{ $article->excerpt }}
                    </p>
                @endif
                
                <div class="flex items-center text-gray-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Publié le {{ $article->published_at->format('d F Y') }}</span>
                    
                    @if($article->author)
                        <span class="mx-3">•</span>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Par {{ $article->author->name }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Article Content -->
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Featured Image -->
                @if($article->featured_image)
                    <div class="w-full h-64 md:h-96 bg-cover bg-center" style="background-image: url('{{ asset('storage/' . $article->featured_image) }}');"></div>
                @endif
                
                <!-- Article Body -->
                <div class="p-8 md:p-12">
                    <div class="prose prose-lg max-w-none">
                        {!! nl2br(e($article->content)) !!}
                    </div>
                    
                    <!-- Article Tags -->
                    @if($article->tags && count($article->tags) > 0)
                        <div class="mt-12 pt-8 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Sujets abordés :</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($article->tags as $tag)
                                    <a href="{{ route('articles.index', ['tag' => $tag]) }}" 
                                       class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm hover:bg-blue-100 hover:text-blue-800 transition duration-300">
                                        #{{ $tag }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Share Section -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Partager cet article :</h3>
                        <div class="flex space-x-4">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" 
                               target="_blank" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                                <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                Facebook
                            </a>
                            
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($article->title) }}" 
                               target="_blank" 
                               class="bg-blue-400 text-white px-4 py-2 rounded-lg hover:bg-blue-500 transition duration-300">
                                <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                                Twitter
                            </a>
                            
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" 
                               target="_blank" 
                               class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition duration-300">
                                <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                                LinkedIn
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Articles -->
    @if($relatedArticles && $relatedArticles->count() > 0)
        <div class="bg-white py-16">
            <div class="container mx-auto px-4">
                <div class="max-w-6xl mx-auto">
                    <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Articles similaires</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($relatedArticles as $relatedArticle)
                            <article class="bg-gray-50 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                                @if($relatedArticle->featured_image)
                                    <div class="h-48 bg-cover bg-center" style="background-image: url('{{ asset('storage/' . $relatedArticle->featured_image) }}');"></div>
                                @else
                                    <div class="h-48 bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                        </svg>
                                    </div>
                                @endif
                                
                                <div class="p-6">
                                    <div class="flex items-center mb-3">
                                        @if($relatedArticle->tags && count($relatedArticle->tags) > 0)
                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-2">
                                                {{ $relatedArticle->tags[0] }}
                                            </span>
                                        @endif
                                        <span class="text-sm text-gray-500">
                                            {{ $relatedArticle->published_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    
                                    <h3 class="text-lg font-semibold text-gray-800 mb-3 line-clamp-2">
                                        <a href="{{ route('articles.show', $relatedArticle->slug) }}" class="hover:text-blue-600 transition duration-300">
                                            {{ $relatedArticle->title }}
                                        </a>
                                    </h3>
                                    
                                    <p class="text-gray-600 mb-4 line-clamp-2">{{ $relatedArticle->formatted_excerpt }}</p>
                                    
                                    <a href="{{ route('articles.show', $relatedArticle->slug) }}" 
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
                    
                    <div class="text-center mt-8">
                        <a href="{{ route('articles.index') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-300">
                            Voir toutes les actualités
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.prose {
    color: #374151;
    line-height: 1.75;
}

.prose p {
    margin-bottom: 1.25em;
}

.prose h2 {
    font-size: 1.5em;
    font-weight: 600;
    margin-top: 2em;
    margin-bottom: 1em;
    color: #1f2937;
}

.prose h3 {
    font-size: 1.25em;
    font-weight: 600;
    margin-top: 1.6em;
    margin-bottom: 0.6em;
    color: #1f2937;
}

.prose ul, .prose ol {
    margin-bottom: 1.25em;
    padding-left: 1.625em;
}

.prose li {
    margin-bottom: 0.5em;
}

.prose strong {
    font-weight: 600;
    color: #1f2937;
}

.prose em {
    font-style: italic;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection