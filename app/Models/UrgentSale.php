<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class UrgentSale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'prestataire_id',
        'title',
        'description',
        'price',
        'condition',
        'photos',
        'quantity',
        'location',
        'latitude',
        'longitude',
        'is_urgent',
        'status',
        'slug',
        'views_count',
        'contact_count'
    ];

    protected $casts = [
        'photos' => 'array',
        'is_urgent' => 'boolean',
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'views_count' => 'integer',
        'contact_count' => 'integer'
    ];

    protected $dates = [
        'deleted_at'
    ];

    // Statuts possibles
    const STATUS_ACTIVE = 'active';
    const STATUS_SOLD = 'sold';
    const STATUS_WITHDRAWN = 'withdrawn';
    const STATUS_REPORTED = 'reported';
    const STATUS_BLOCKED = 'blocked';

    // États possibles
    const CONDITION_NEW = 'new';
    const CONDITION_GOOD = 'good';
    const CONDITION_USED = 'used';
    const CONDITION_FAIR = 'fair';

    // Options de condition pour les formulaires
    const CONDITION_OPTIONS = [
        self::CONDITION_NEW => 'Neuf',
        self::CONDITION_GOOD => 'Bon état',
        self::CONDITION_USED => 'Usagé',
        self::CONDITION_FAIR => 'État correct'
    ];

    /**
     * Relation avec le prestataire
     */
    public function prestataire()
    {
        return $this->belongsTo(Prestataire::class);
    }

    /**
     * Relation avec les signalements
     */
    public function reports()
    {
        return $this->hasMany(UrgentSaleReport::class);
    }

    /**
     * Relation avec les contacts
     */
    public function contacts()
    {
        return $this->hasMany(UrgentSaleContact::class);
    }

    /**
     * Scope pour les ventes actives
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope pour les ventes urgentes
     */
    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    /**
     * Scope pour les ventes récentes
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Générer un slug unique
     */
    public function generateSlug()
    {
        $slug = Str::slug($this->title);
        $originalSlug = $slug;
        $counter = 1;

        while (self::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Boot method pour générer automatiquement le slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($urgentSale) {
            if (empty($urgentSale->slug)) {
                $urgentSale->slug = $urgentSale->generateSlug();
            }
        });

        static::updating(function ($urgentSale) {
            if ($urgentSale->isDirty('title')) {
                $urgentSale->slug = $urgentSale->generateSlug();
            }
        });
    }

    /**
     * Obtenir la première photo
     */
    public function getFirstPhotoAttribute()
    {
        if ($this->photos && is_array($this->photos) && count($this->photos) > 0) {
            return $this->photos[0];
        }
        return null;
    }

    /**
     * Obtenir l'URL de la première photo
     */
    public function getFirstPhotoUrlAttribute()
    {
        if ($this->first_photo) {
            return asset('storage/' . $this->first_photo);
        }
        return asset('images/default-product.jpg');
    }

    /**
     * Obtenir le libellé de l'état
     */
    public function getConditionLabelAttribute()
    {
        $conditions = [
            self::CONDITION_NEW => 'Neuf',
            self::CONDITION_GOOD => 'Bon état',
            self::CONDITION_USED => 'Usagé',
            self::CONDITION_FAIR => 'État correct'
        ];

        return $conditions[$this->condition] ?? 'Non spécifié';
    }

    /**
     * Obtenir le libellé du statut
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            self::STATUS_ACTIVE => 'Actif',
            self::STATUS_SOLD => 'Vendu',
            self::STATUS_WITHDRAWN => 'Retiré',
            self::STATUS_REPORTED => 'Signalé',
            self::STATUS_BLOCKED => 'Bloqué'
        ];

        return $statuses[$this->status] ?? 'Inconnu';
    }

    /**
     * Incrémenter le nombre de vues
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Incrémenter le nombre de contacts
     */
    public function incrementContacts()
    {
        $this->increment('contact_count');
    }

    /**
     * Vérifier si la vente peut être modifiée
     */
    public function canBeEdited()
    {
        return in_array($this->status, [self::STATUS_ACTIVE]);
    }

    /**
     * Vérifier si la vente peut être contactée
     */
    public function canBeContacted()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Vérifier si la vente est active
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}