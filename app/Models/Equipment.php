<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'prestataire_id',
        'name',
        'slug',
        'description',
        'technical_specifications',
        'photos',
        'main_photo',
        'price_per_hour',
        'price_per_day',
        'price_per_week',
        'price_per_month',
        'security_deposit',
        'delivery_fee',
        'delivery_included',
        'condition',
        'status',
        'is_available',
        'available_from',
        'available_until',
        'minimum_rental_duration',
        'maximum_rental_duration',
        'address',
        'city',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'delivery_radius',
        'rental_conditions',
        'usage_instructions',
        'safety_instructions',
        'included_accessories',
        'optional_accessories',
        'requires_license',
        'required_license_type',
        'minimum_age',
        'average_rating',
        'total_reviews',
        'total_rentals',
        'view_count',
        'last_rented_at',
        'metadata',
        'featured',
        'sort_order',
        'verified_at',
        'verified_by',
        'brand',
        'model',
        'weight',
        'dimensions',
        'power_requirements',
        'serial_number'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'photos' => 'array',
        'included_accessories' => 'array',
        'optional_accessories' => 'array',
        'metadata' => 'array',
        'is_available' => 'boolean',
        'delivery_included' => 'boolean',
        'requires_license' => 'boolean',
        'featured' => 'boolean',
        'price_per_hour' => 'decimal:2',
        'price_per_day' => 'decimal:2',
        'price_per_week' => 'decimal:2',
        'price_per_month' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'average_rating' => 'decimal:2',
        'available_from' => 'date',
        'available_until' => 'date',
        'last_rented_at' => 'datetime',
        'verified_at' => 'datetime',
        'minimum_rental_duration' => 'integer',
        'maximum_rental_duration' => 'integer',
        'delivery_radius' => 'integer',
        'minimum_age' => 'integer',
        'total_reviews' => 'integer',
        'total_rentals' => 'integer',
        'view_count' => 'integer',
        'sort_order' => 'integer',
        'verified_by' => 'integer'
    ];

    /**
     * Relation avec le prestataire propriétaire
     */
    public function prestataire(): BelongsTo
    {
        return $this->belongsTo(Prestataire::class);
    }

    /**
     * Relation avec les demandes de location
     */
    public function rentalRequests(): HasMany
    {
        return $this->hasMany(EquipmentRentalRequest::class);
    }

    /**
     * Relation avec les locations actives
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(EquipmentRental::class);
    }

    /**
     * Relation avec les avis sur le matériel
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(EquipmentReview::class);
    }

    /**
     * Relation avec les catégories
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(EquipmentCategory::class, 'equipment_category_equipment')
                    ->withTimestamps();
    }

    /**
     * Relation avec les signalements
     */
    public function reports(): HasMany
    {
        return $this->hasMany(EquipmentReport::class);
    }

    /**
     * Scope pour les équipements actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope pour les équipements en vedette
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope pour filtrer par ville
     */
    public function scopeInSameCity($query, $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Vérifie si l'équipement est actif
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Vérifie si l'équipement est disponible
     */
    public function isAvailable()
    {
        return $this->is_available && $this->status === 'active';
    }

    /**
     * Scope pour les équipements disponibles
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')->where('is_available', true);
    }

    /**
     * Scope pour filtrer par catégorie
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('equipment_categories.id', $categoryId);
        });
    }

    /**
     * Scope pour filtrer par localisation
     */
    public function scopeNearLocation($query, $location, $radius = 50)
    {
        return $query->where('city', 'like', "%{$location}%")
                    ->orWhere('address', 'like', "%{$location}%");
    }

    /**
     * Scope pour filtrer par prix
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price_per_day', [$minPrice, $maxPrice]);
    }

    /**
     * Vérifie si l'équipement est disponible pour une période donnée
     */
    public function isAvailableForPeriod($startDate, $endDate)
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        // Vérifier s'il y a des locations qui se chevauchent
        $overlappingRentals = $this->rentals()
            ->whereIn('status', ['active', 'confirmed'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                      ->orWhereBetween('end_date', [$start, $end])
                      ->orWhere(function ($q) use ($start, $end) {
                          $q->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                      });
            })
            ->exists();

        return !$overlappingRentals;
    }

    /**
     * Calcule le prix pour une période donnée
     */
    public function calculatePrice($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $days = $start->diffInDays($end) + 1;

        // Calcul du prix selon la durée
        if ($days >= 30 && $this->price_per_month) {
            $months = ceil($days / 30);
            return $this->price_per_month * $months;
        } elseif ($days >= 7 && $this->price_per_week) {
            $weeks = ceil($days / 7);
            return $this->price_per_week * $weeks;
        } else {
            return $this->price_per_day * $days;
        }
    }

    /**
     * Obtient la note moyenne
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('overall_rating') ?? 0;
    }

    /**
     * Obtient le nombre total d'avis
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * Obtient la première photo disponible (main_photo ou première du tableau photos)
     */
    public function getFirstPhotoAttribute()
    {
        if ($this->attributes['main_photo']) {
            return $this->attributes['main_photo'];
        }
        return $this->photos && count($this->photos) > 0 ? $this->photos[0] : null;
    }

    /**
     * Obtient les statistiques détaillées des avis
     */
    public function getDetailedRatingStats()
    {
        $reviews = $this->reviews();

        $stats = [
            'total_reviews' => $reviews->count(),
            'average_rating' => round($reviews->avg('overall_rating'), 1),
            'detailed_averages' => [
                'condition' => round($reviews->avg('condition_rating'), 1),
                'performance' => round($reviews->avg('performance_rating'), 1),
                'value' => round($reviews->avg('value_rating'), 1),
                'service' => round($reviews->avg('service_rating'), 1),
            ],
            'rating_counts' => [
                '5_stars' => $reviews->clone()->where('overall_rating', 5)->count(),
                '4_stars' => $reviews->clone()->where('overall_rating', 4)->count(),
                '3_stars' => $reviews->clone()->where('overall_rating', 3)->count(),
                '2_stars' => $reviews->clone()->where('overall_rating', 2)->count(),
                '1_star' => $reviews->clone()->where('overall_rating', 1)->count(),
            ]
        ];

        // Calculer les pourcentages
        if ($stats['total_reviews'] > 0) {
            foreach ($stats['rating_counts'] as $key => $count) {
                $stats['rating_percentages'][$key] = round(($count / $stats['total_reviews']) * 100);
            }
        } else {
            foreach ($stats['rating_counts'] as $key => $count) {
                $stats['rating_percentages'][$key] = 0;
            }
        }

        return $stats;
    }

    /**
     * Obtient le statut de disponibilité formaté
     */
    public function getFormattedAvailabilityStatusAttribute()
    {
        $statuses = [
            'active' => 'Disponible',
            'rented' => 'Loué',
            'maintenance' => 'En maintenance',
            'inactive' => 'Inactif',
            'unavailable' => 'Indisponible'
        ];

        return $statuses[$this->status] ?? 'Inconnu';
    }

    /**
     * Obtient l'état formaté
     */
    public function getFormattedConditionAttribute()
    {
        $conditions = [
            'new' => 'Neuf',
            'excellent' => 'Excellent',
            'good' => 'Bon',
            'fair' => 'Correct',
            'poor' => 'Usagé'
        ];

        return $conditions[$this->condition] ?? 'Non spécifié';
    }

    /**
     * Vérifie si l'équipement nécessite une livraison
     */
    public function requiresDelivery($clientLocation)
    {
        if ($this->delivery_included || $this->delivery_radius == 0) {
            return false;
        }

        // Logique simple de distance (à améliorer avec une vraie API de géolocalisation)
        return $this->city !== $clientLocation;
    }

    /**
     * Obtient les prochaines disponibilités
     */
    public function getUnavailableDates()
    {
        $unavailableDates = [];
        $rentals = $this->rentals()->whereIn('status', ['active', 'confirmed'])->get();

        foreach ($rentals as $rental) {
            $period = Carbon::parse($rental->start_date)->toPeriod($rental->end_date);
            foreach ($period as $date) {
                $unavailableDates[] = $date->format('Y-m-d');
            }
        }

        return array_unique($unavailableDates);
    }

    public function getNextAvailableDates($limit = 10)
    {
        $dates = [];
        $currentDate = Carbon::now();
        $count = 0;

        while ($count < $limit) {
            if ($this->isAvailableForPeriod($currentDate, $currentDate)) {
                $dates[] = $currentDate->copy();
                $count++;
            }
            $currentDate->addDay();
        }

        return $dates;
    }

    /**
     * Accesseur pour le tarif journalier
     */
    public function getDailyRateAttribute()
    {
        return $this->price_per_day;
    }

    /**
     * Accesseur pour le tarif hebdomadaire
     */
    public function getWeeklyRateAttribute()
    {
        return $this->price_per_week;
    }

    /**
     * Accesseur pour la disponibilité de livraison
     */
    public function getDeliveryAvailableAttribute()
    {
        return $this->delivery_included || $this->delivery_fee > 0;
    }
}