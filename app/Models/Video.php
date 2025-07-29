<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Video extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'prestataire_id',
        'title',
        'description',
        'video_path',
        'is_public',
        'duration',
        'status',
        'views_count',
        'likes_count',
        'comments_count',
        'shares_count',
    ];

    public function prestataire()
    {
        return $this->belongsTo(Prestataire::class);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(VideoLike::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(VideoComment::class);
    }

    public function isLikedBy($user): bool
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function getMimeType()
    {
        $extension = pathinfo($this->video_path, PATHINFO_EXTENSION);
        switch (strtolower($extension)) {
            case 'mp4':
                return 'video/mp4';
            case 'webm':
                return 'video/webm';
            case 'ogv':
                return 'video/ogg';
            case 'mov':
                return 'video/quicktime';
            default:
                return 'video/mp4'; // Fallback
        }
    }
}
