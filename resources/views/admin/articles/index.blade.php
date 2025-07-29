@extends('layouts.admin-modern')

@section('title', 'Gestion des Articles')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des Articles</h1>
        <a href="{{ route('administrateur.articles.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nouvel Article
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('administrateur.articles.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Rechercher</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Titre, contenu...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publié</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archivé</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="author" class="form-label">Auteur</label>
                    <select class="form-select" id="author" name="author">
                        <option value="">Tous les auteurs</option>
                        @foreach($authors as $author)
                            <option value="{{ $author->id }}" {{ request('author') == $author->id ? 'selected' : '' }}>
                                {{ $author->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">Filtrer</button>
                    <a href="{{ route('administrateur.articles.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Articles Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Articles ({{ $articles->total() }})</h6>
        </div>
        <div class="card-body">
            @if($articles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Titre</th>
                                <th>Auteur</th>
                                <th>Statut</th>
                                <th>Date de publication</th>
                                <th>Créé le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($articles as $article)
                                <tr>
                                    <td>
                                        @if($article->featured_image)
                                            <img src="{{ asset('storage/' . $article->featured_image) }}" 
                                                 alt="{{ $article->title }}" 
                                                 class="img-thumbnail" 
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" 
                                                 style="width: 60px; height: 60px; border-radius: 0.375rem;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ Str::limit($article->title, 50) }}</strong>
                                            @if($article->tags && count($article->tags) > 0)
                                                <br>
                                                @foreach(array_slice($article->tags, 0, 2) as $tag)
                                                    <span class="badge bg-secondary me-1">{{ $tag }}</span>
                                                @endforeach
                                                @if(count($article->tags) > 2)
                                                    <span class="text-muted">+{{ count($article->tags) - 2 }}</span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $article->author ? $article->author->name : 'N/A' }}</td>
                                    <td>
                                        @switch($article->status)
                                            @case('published')
                                                <span class="badge bg-success">Publié</span>
                                                @break
                                            @case('draft')
                                                <span class="badge bg-warning">Brouillon</span>
                                                @break
                                            @case('archived')
                                                <span class="badge bg-secondary">Archivé</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">{{ ucfirst($article->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($article->published_at)
                                            {{ $article->published_at->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-muted">Non publié</span>
                                        @endif
                                    </td>
                                    <td>{{ $article->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- View -->
                                            @if($article->status === 'published')
                                                <a href="{{ route('articles.show', $article->slug) }}" 
                                                   class="btn btn-outline-info btn-sm" 
                                                   target="_blank" 
                                                   title="Voir l'article">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                            
                                            <!-- Show -->
                                            <a href="{{ route('administrateur.articles.show', $article) }}" 
                                               class="btn btn-outline-primary btn-sm" 
                                               title="Détails">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                            
                                            <!-- Edit -->
                                            <a href="{{ route('administrateur.articles.edit', $article) }}" 
                                               class="btn btn-outline-warning btn-sm" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <!-- Publish/Archive -->
                                            @if($article->status === 'draft')
                                                <form method="POST" action="{{ route('administrateur.articles.publish', $article) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-success btn-sm" title="Publier">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @elseif($article->status === 'published')
                                                <form method="POST" action="{{ route('administrateur.articles.archive', $article) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-secondary btn-sm" title="Archiver">
                                                        <i class="fas fa-archive"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <!-- Delete -->
                                            <form method="POST" action="{{ route('administrateur.articles.destroy', $article) }}" 
                                                  class="d-inline" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $articles->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun article trouvé</h5>
                    <p class="text-muted">Commencez par créer votre premier article.</p>
                    <a href="{{ route('administrateur.articles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Créer un article
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-submit form on select change
    document.querySelectorAll('select[name="status"], select[name="author"]').forEach(function(select) {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
</script>
@endpush