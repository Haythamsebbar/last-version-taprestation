<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'category',
        'budget',
        'due_date',
        'status',
        'location',
        'coordinates',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'budget' => 'decimal:2',
        'coordinates' => 'array',
    ];

    /**
     * Get the client that owns the request.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the offers for this request.
     */
    public function offers()
    {
        return $this->hasMany(Offer::class, 'client_request_id');
    }

    /**
     * Get the messages related to this request.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'client_request_id');
    }

    /**
     * Get the booking for this request.
     */
    public function booking()
    {
        return $this->hasOne(Booking::class, 'client_request_id');
    }

    /**
     * Scope a query to only include active requests.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include completed requests.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}