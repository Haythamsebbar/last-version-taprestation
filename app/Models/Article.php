<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'excerpt',
        'featured_image',
        'status',
        'published_at',
        'author_id',
        'slug',
        'meta_description',
        'tags'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'tags' => 'array'
    ];

    protected $dates = [
        'published_at',
        'deleted_at'
    ];

    // Statuts possibles pour les articles
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';

    /**
     * Relation avec l'auteur (utilisateur admin)
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope pour les articles publiés
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope pour les articles récents
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('published_at', 'desc')->limit($limit);
    }

    /**
     * Génère automatiquement un slug à partir du titre
     */
    public function generateSlug()
    {
        $slug = \Str::slug($this->title);
        $count = static::where('slug', 'LIKE', "{$slug}%")->count();
        
        return $count ? "{$slug}-{$count}" : $slug;
    }

    /**
     * Mutateur pour générer automatiquement le slug
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = $this->generateSlug();
        }
    }

    /**
     * Accesseur pour l'URL de l'article
     */
    public function getUrlAttribute()
    {
        return route('articles.show', $this->slug);
    }

    /**
     * Accesseur pour l'extrait formaté
     */
    public function getFormattedExcerptAttribute()
    {
        return $this->excerpt ?: \Str::limit(strip_tags($this->content), 150);
    }
}