@extends('layouts.admin-modern')

@section('title', 'Détails de l\'Article')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Détails de l'Article</h1>
        <div>
            @if($article->status === 'published')
                <a href="{{ route('articles.show', $article->slug) }}" 
                   class="btn btn-info btn-sm shadow-sm me-2" 
                   target="_blank">
                    <i class="fas fa-eye fa-sm text-white-50"></i> Voir l'article
                </a>
            @endif
            <a href="{{ route('administrateur.articles.edit', $article) }}" class="btn btn-warning btn-sm shadow-sm me-2">
                <i class="fas fa-edit fa-sm text-white-50"></i> Modifier
            </a>
            <a href="{{ route('administrateur.articles.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Article Content -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Contenu de l'Article</h6>
                </div>
                <div class="card-body">
                    <!-- Title -->
                    <div class="mb-4">
                        <h2 class="h4 text-gray-800 mb-2">{{ $article->title }}</h2>
                        <div class="text-muted small">
                            <strong>Slug :</strong> {{ $article->slug }}<br>
                            <strong>URL :</strong> 
                            @if($article->status === 'published')
                                <a href="{{ route('articles.show', $article->slug) }}" target="_blank" class="text-primary">
                                    {{ route('articles.show', $article->slug) }}
                                </a>
                            @else
                                <span class="text-muted">{{ route('articles.show', $article->slug) }} (non publié)</span>
                            @endif
                        </div>
                    </div>

                    <!-- Excerpt -->
                    @if($article->excerpt)
                        <div class="mb-4">
                            <h6 class="text-gray-800">Extrait</h6>
                            <div class="bg-light p-3 rounded">
                                {{ $article->excerpt }}
                            </div>
                        </div>
                    @endif

                    <!-- Featured Image -->
                    @if($article->featured_image)
                        <div class="mb-4">
                            <h6 class="text-gray-800">Image à la une</h6>
                            <img src="{{ asset('storage/' . $article->featured_image) }}" 
                                 alt="{{ $article->title }}" 
                                 class="img-fluid rounded shadow-sm" 
                                 style="max-height: 300px; object-fit: cover;">
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="mb-4">
                        <h6 class="text-gray-800">Contenu</h6>
                        <div class="content-preview bg-light p-4 rounded" style="max-height: 400px; overflow-y: auto;">
                            {!! nl2br(e($article->content)) !!}
                        </div>
                    </div>

                    <!-- Meta Description -->
                    @if($article->meta_description)
                        <div class="mb-4">
                            <h6 class="text-gray-800">Meta Description (SEO)</h6>
                            <div class="bg-light p-3 rounded">
                                {{ $article->meta_description }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Publication Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations de Publication</h6>
                </div>
                <div class="card-body">
                    <!-- Status -->
                    <div class="mb-3">
                        <strong>Statut :</strong>
                        @switch($article->status)
                            @case('published')
                                <span class="badge bg-success ms-2">Publié</span>
                                @break
                            @case('draft')
                                <span class="badge bg-warning ms-2">Brouillon</span>
                                @break
                            @case('archived')
                                <span class="badge bg-secondary ms-2">Archivé</span>
                                @break
                            @default
                                <span class="badge bg-light text-dark ms-2">{{ ucfirst($article->status) }}</span>
                        @endswitch
                    </div>

                    <!-- Dates -->
                    <div class="mb-3">
                        <strong>Créé le :</strong><br>
                        <span class="text-muted">{{ $article->created_at->format('d/m/Y à H:i') }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Modifié le :</strong><br>
                        <span class="text-muted">{{ $article->updated_at->format('d/m/Y à H:i') }}</span>
                    </div>

                    @if($article->published_at)
                        <div class="mb-3">
                            <strong>Publié le :</strong><br>
                            <span class="text-muted">{{ $article->published_at->format('d/m/Y à H:i') }}</span>
                        </div>
                    @endif

                    <!-- Author -->
                    @if($article->author)
                        <div class="mb-3">
                            <strong>Auteur :</strong><br>
                            <span class="text-muted">{{ $article->author->name }}</span>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="d-grid gap-2">
                        @if($article->status === 'draft')
                            <form method="POST" action="{{ route('administrateur.articles.publish', $article) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="fas fa-check"></i> Publier
                                </button>
                            </form>
                        @elseif($article->status === 'published')
                            <form method="POST" action="{{ route('administrateur.articles.archive', $article) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-secondary btn-sm w-100">
                                    <i class="fas fa-archive"></i> Archiver
                                </button>
                            </form>
                        @endif
                        
                        <form method="POST" action="{{ route('administrateur.articles.destroy', $article) }}" class="d-inline" 
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tags -->
            @if($article->tags && count($article->tags) > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tags</h6>
                    </div>
                    <div class="card-body">
                        @foreach($article->tags as $tag)
                            <span class="badge bg-secondary me-1 mb-1">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h5 class="text-primary mb-1">{{ str_word_count($article->content) }}</h5>
                                <small class="text-muted">Mots</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="text-primary mb-1">{{ strlen($article->content) }}</h5>
                            <small class="text-muted">Caractères</small>
                        </div>
                    </div>
                    
                    @if($article->excerpt)
                        <hr>
                        <div class="text-center">
                            <small class="text-muted">
                                <strong>Extrait :</strong> {{ strlen($article->excerpt) }} caractères
                            </small>
                        </div>
                    @endif
                    
                    @if($article->meta_description)
                        <div class="text-center mt-2">
                            <small class="text-muted">
                                <strong>Meta description :</strong> {{ strlen($article->meta_description) }}/160 caractères
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- SEO Preview -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aperçu SEO</h6>
                </div>
                <div class="card-body">
                    <div class="seo-preview">
                        <div class="seo-title text-primary" style="font-size: 18px; line-height: 1.2;">
                            {{ $article->title }}
                        </div>
                        <div class="seo-url text-success small mt-1">
                            {{ route('articles.show', $article->slug) }}
                        </div>
                        <div class="seo-description text-muted small mt-1" style="line-height: 1.4;">
                            {{ $article->meta_description ?: $article->formatted_excerpt }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.content-preview {
    font-family: Georgia, serif;
    line-height: 1.6;
    color: #333;
}

.content-preview p {
    margin-bottom: 1rem;
}

.seo-preview {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    padding: 1rem;
    background-color: #f8f9fc;
}

.seo-title {
    font-weight: 400;
    text-decoration: none;
}

.seo-title:hover {
    text-decoration: underline;
}

.seo-url {
    font-weight: 400;
}

.seo-description {
    max-width: 600px;
}
</style>
@endpush