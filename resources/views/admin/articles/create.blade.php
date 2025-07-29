@extends('layouts.admin-modern')

@section('title', 'Créer un Article')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Créer un Article</h1>
        <a href="{{ route('administrateur.articles.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour à la liste
        </a>
    </div>

    <form method="POST" action="{{ route('administrateur.articles.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Contenu de l'Article</h6>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   required 
                                   maxlength="255">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug (URL)</label>
                            <input type="text" 
                                   class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" 
                                   name="slug" 
                                   value="{{ old('slug') }}" 
                                   maxlength="255">
                            <div class="form-text">Laissez vide pour générer automatiquement à partir du titre.</div>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Excerpt -->
                        <div class="mb-3">
                            <label for="excerpt" class="form-label">Extrait</label>
                            <textarea class="form-control @error('excerpt') is-invalid @enderror" 
                                      id="excerpt" 
                                      name="excerpt" 
                                      rows="3" 
                                      maxlength="500">{{ old('excerpt') }}</textarea>
                            <div class="form-text">Résumé court de l'article (max 500 caractères).</div>
                            @error('excerpt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label for="content" class="form-label">Contenu <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" 
                                      name="content" 
                                      rows="15" 
                                      required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Meta Description -->
                        <div class="mb-3">
                            <label for="meta_description" class="form-label">Meta Description (SEO)</label>
                            <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                      id="meta_description" 
                                      name="meta_description" 
                                      rows="2" 
                                      maxlength="160">{{ old('meta_description') }}</textarea>
                            <div class="form-text">Description pour les moteurs de recherche (max 160 caractères).</div>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Publication Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Publication</h6>
                    </div>
                    <div class="card-body">
                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Brouillon</option>
                                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Publié</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Published Date -->
                        <div class="mb-3">
                            <label for="published_at" class="form-label">Date de publication</label>
                            <input type="datetime-local" 
                                   class="form-control @error('published_at') is-invalid @enderror" 
                                   id="published_at" 
                                   name="published_at" 
                                   value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                            <div class="form-text">Laissez vide pour publier immédiatement.</div>
                            @error('published_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="save" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer
                            </button>
                            <button type="submit" name="action" value="save_and_continue" class="btn btn-outline-primary">
                                <i class="fas fa-save"></i> Enregistrer et continuer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Image à la une</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="file" 
                                   class="form-control @error('featured_image') is-invalid @enderror" 
                                   id="featured_image" 
                                   name="featured_image" 
                                   accept="image/*">
                            <div class="form-text">Formats acceptés : JPG, PNG, GIF (max 2MB)</div>
                            @error('featured_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Image Preview -->
                        <div id="image-preview" class="d-none">
                            <img id="preview-img" src="" alt="Aperçu" class="img-fluid rounded">
                            <button type="button" id="remove-image" class="btn btn-sm btn-outline-danger mt-2">
                                <i class="fas fa-times"></i> Supprimer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tags -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tags</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" 
                                   class="form-control @error('tags') is-invalid @enderror" 
                                   id="tags" 
                                   name="tags" 
                                   value="{{ old('tags') }}" 
                                   placeholder="conseil, guide, tendance...">
                            <div class="form-text">Séparez les tags par des virgules.</div>
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Popular Tags -->
                        <div class="mb-3">
                            <label class="form-label">Tags populaires :</label>
                            <div id="popular-tags">
                                @php
                                    $popularTags = ['conseil', 'guide', 'tendance', 'freelance', 'digital', 'business', 'startup', 'marketing'];
                                @endphp
                                @foreach($popularTags as $tag)
                                    <button type="button" class="btn btn-outline-secondary btn-sm me-1 mb-1 tag-btn" data-tag="{{ $tag }}">
                                        {{ $tag }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Auto-generate slug from title
document.getElementById('title').addEventListener('input', function() {
    const title = this.value;
    const slug = title.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
    document.getElementById('slug').value = slug;
});

// Image preview
document.getElementById('featured_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
});

// Remove image
document.getElementById('remove-image').addEventListener('click', function() {
    document.getElementById('featured_image').value = '';
    document.getElementById('image-preview').classList.add('d-none');
});

// Tag management
document.querySelectorAll('.tag-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const tag = this.dataset.tag;
        const tagsInput = document.getElementById('tags');
        const currentTags = tagsInput.value.split(',').map(t => t.trim()).filter(t => t);
        
        if (!currentTags.includes(tag)) {
            currentTags.push(tag);
            tagsInput.value = currentTags.join(', ');
        }
    });
});

// Character count for meta description
document.getElementById('meta_description').addEventListener('input', function() {
    const length = this.value.length;
    const maxLength = 160;
    const remaining = maxLength - length;
    
    // Update or create character counter
    let counter = this.parentNode.querySelector('.char-counter');
    if (!counter) {
        counter = document.createElement('div');
        counter.className = 'char-counter form-text';
        this.parentNode.appendChild(counter);
    }
    
    counter.textContent = `${length}/${maxLength} caractères`;
    counter.className = `char-counter form-text ${remaining < 20 ? 'text-warning' : ''} ${remaining < 0 ? 'text-danger' : ''}`;
});

// Auto-update published_at when status changes to published
document.getElementById('status').addEventListener('change', function() {
    const publishedAtInput = document.getElementById('published_at');
    if (this.value === 'published' && !publishedAtInput.value) {
        publishedAtInput.value = new Date().toISOString().slice(0, 16);
    }
});
</script>
@endpush