<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AvailabilityException extends Model
{
    use HasFactory;

    protected $fillable = [
        'prestataire_id',
        'date',
        'start_time',
        'end_time',
        'type',
        'reason',
        'is_recurring',
        'recurrence_pattern',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
        'recurrence_pattern' => 'array',
    ];

    /**
     * Exception types
     */
    const TYPE_UNAVAILABLE = 'unavailable';
    const TYPE_HOLIDAY = 'holiday';
    const TYPE_VACATION = 'vacation';
    const TYPE_SICK_LEAVE = 'sick_leave';
    const TYPE_CUSTOM_HOURS = 'custom_hours';
    const TYPE_BLOCKED = 'blocked';

    /**
     * Relationships
     */
    public function prestataire(): BelongsTo
    {
        return $this->belongsTo(Prestataire::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeForPrestataire($query, $prestataireId)
    {
        return $query->where('prestataire_id', $prestataireId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeUnavailable($query)
    {
        return $query->where('type', self::TYPE_UNAVAILABLE);
    }

    public function scopeHolidays($query)
    {
        return $query->where('type', self::TYPE_HOLIDAY);
    }

    public function scopeVacations($query)
    {
        return $query->where('type', self::TYPE_VACATION);
    }

    public function scopeCustomHours($query)
    {
        return $query->where('type', self::TYPE_CUSTOM_HOURS);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', today());
    }

    public function scopePast($query)
    {
        return $query->where('date', '<', today());
    }

    /**
     * Status checks
     */
    public function isFullDayException(): bool
    {
        return !$this->start_time && !$this->end_time;
    }

    public function isPartialDayException(): bool
    {
        return $this->start_time && $this->end_time;
    }

    public function isUnavailable(): bool
    {
        return $this->type === self::TYPE_UNAVAILABLE;
    }

    public function isHoliday(): bool
    {
        return $this->type === self::TYPE_HOLIDAY;
    }

    public function isVacation(): bool
    {
        return $this->type === self::TYPE_VACATION;
    }

    public function isCustomHours(): bool
    {
        return $this->type === self::TYPE_CUSTOM_HOURS;
    }

    public function appliesTo(Carbon $date, ?Carbon $startTime = null, ?Carbon $endTime = null): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Check if date matches
        if (!$this->date->isSameDay($date)) {
            // For recurring exceptions, check if pattern matches
            if ($this->is_recurring && $this->matchesRecurrencePattern($date)) {
                // Continue to time check
            } else {
                return false;
            }
        }

        // If it's a full day exception
        if ($this->isFullDayException()) {
            return true;
        }

        // If no time specified, just check date
        if (!$startTime || !$endTime) {
            return true;
        }

        // Check time overlap for partial day exceptions
        $exceptionStart = $date->copy()->setTimeFromTimeString($this->start_time);
        $exceptionEnd = $date->copy()->setTimeFromTimeString($this->end_time);

        return $startTime < $exceptionEnd && $endTime > $exceptionStart;
    }

    public function conflictsWith(Carbon $startDateTime, Carbon $endDateTime): bool
    {
        return $this->appliesTo($startDateTime, $startDateTime, $endDateTime);
    }

    /**
     * Recurrence pattern matching
     */
    public function matchesRecurrencePattern(Carbon $date): bool
    {
        if (!$this->is_recurring || !$this->recurrence_pattern) {
            return false;
        }

        $pattern = $this->recurrence_pattern;
        $type = $pattern['type'] ?? null;

        switch ($type) {
            case 'weekly':
                return $this->matchesWeeklyPattern($date, $pattern);
            case 'monthly':
                return $this->matchesMonthlyPattern($date, $pattern);
            case 'yearly':
                return $this->matchesYearlyPattern($date, $pattern);
            default:
                return false;
        }
    }

    private function matchesWeeklyPattern(Carbon $date, array $pattern): bool
    {
        $daysOfWeek = $pattern['days_of_week'] ?? [];
        return in_array($date->dayOfWeek, $daysOfWeek);
    }

    private function matchesMonthlyPattern(Carbon $date, array $pattern): bool
    {
        $dayOfMonth = $pattern['day_of_month'] ?? null;
        $weekOfMonth = $pattern['week_of_month'] ?? null;
        $dayOfWeek = $pattern['day_of_week'] ?? null;

        if ($dayOfMonth) {
            return $date->day === $dayOfMonth;
        }

        if ($weekOfMonth && $dayOfWeek) {
            // Calculate which week of the month this date falls in
            $firstDayOfMonth = $date->copy()->startOfMonth();
            $weekNumber = ceil(($date->day + $firstDayOfMonth->dayOfWeek) / 7);
            
            return $weekNumber === $weekOfMonth && $date->dayOfWeek === $dayOfWeek;
        }

        return false;
    }

    private function matchesYearlyPattern(Carbon $date, array $pattern): bool
    {
        $month = $pattern['month'] ?? null;
        $day = $pattern['day'] ?? null;

        if ($month && $day) {
            return $date->month === $month && $date->day === $day;
        }

        return false;
    }

    /**
     * Utility methods
     */
    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_UNAVAILABLE => 'Indisponible',
            self::TYPE_HOLIDAY => 'Jour férié',
            self::TYPE_VACATION => 'Congés',
            self::TYPE_SICK_LEAVE => 'Arrêt maladie',
            self::TYPE_CUSTOM_HOURS => 'Horaires personnalisés',
            self::TYPE_BLOCKED => 'Bloqué',
            default => 'Autre',
        };
    }

    public function getTypeBadgeClass(): string
    {
        return match($this->type) {
            self::TYPE_UNAVAILABLE => 'bg-red-100 text-red-800',
            self::TYPE_HOLIDAY => 'bg-purple-100 text-purple-800',
            self::TYPE_VACATION => 'bg-blue-100 text-blue-800',
            self::TYPE_SICK_LEAVE => 'bg-orange-100 text-orange-800',
            self::TYPE_CUSTOM_HOURS => 'bg-green-100 text-green-800',
            self::TYPE_BLOCKED => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getFormattedTimeRange(): ?string
    {
        if ($this->isFullDayException()) {
            return 'Toute la journée';
        }

        return Carbon::parse($this->start_time)->format('H:i') . ' - ' . Carbon::parse($this->end_time)->format('H:i');
    }

    public function getFormattedDate(): string
    {
        return $this->date->format('d/m/Y');
    }

    /**
     * Static methods
     */
    public static function getExceptionsForDate(Prestataire $prestataire, Carbon $date)
    {
        return static::active()
            ->forPrestataire($prestataire->id)
            ->where(function ($query) use ($date) {
                $query->forDate($date)
                      ->orWhere(function ($subQuery) use ($date) {
                          $subQuery->recurring()
                                   ->where(function ($recurQuery) use ($date) {
                                       // This would need custom logic for recurrence patterns
                                       // For now, we'll keep it simple
                                   });
                      });
            })
            ->get()
            ->filter(function ($exception) use ($date) {
                return $exception->appliesTo($date);
            });
    }

    public static function hasExceptionForDateTime(Prestataire $prestataire, Carbon $startDateTime, Carbon $endDateTime): bool
    {
        $exceptions = static::getExceptionsForDate($prestataire, $startDateTime);
        
        return $exceptions->some(function ($exception) use ($startDateTime, $endDateTime) {
            return $exception->conflictsWith($startDateTime, $endDateTime);
        });
    }

    public static function createHoliday(Prestataire $prestataire, Carbon $date, string $reason): static
    {
        return static::create([
            'prestataire_id' => $prestataire->id,
            'date' => $date,
            'type' => self::TYPE_HOLIDAY,
            'reason' => $reason,
            'is_active' => true,
        ]);
    }

    public static function createVacation(Prestataire $prestataire, Carbon $startDate, Carbon $endDate, string $reason): array
    {
        $exceptions = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $exceptions[] = static::create([
                'prestataire_id' => $prestataire->id,
                'date' => $currentDate->copy(),
                'type' => self::TYPE_VACATION,
                'reason' => $reason,
                'is_active' => true,
            ]);
            
            $currentDate->addDay();
        }
        
        return $exceptions;
    }

    public static function getAvailableTypes(): array
    {
        return [
            self::TYPE_UNAVAILABLE => 'Indisponible',
            self::TYPE_HOLIDAY => 'Jour férié',
            self::TYPE_VACATION => 'Congés',
            self::TYPE_SICK_LEAVE => 'Arrêt maladie',
            self::TYPE_CUSTOM_HOURS => 'Horaires personnalisés',
            self::TYPE_BLOCKED => 'Bloqué',
        ];
    }
}