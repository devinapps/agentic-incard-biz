<?php

namespace App\Extensions\SocialMedia\System\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialMediaPlatform extends Model
{
    protected $table = 'ext_social_media_platforms';

    protected $fillable = [
        'user_id',
        'platform',
        'credentials',
        'connected_at',
        'expires_at',
    ];

    protected $casts = [
        'credentials'  => 'array',
        'connected_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    public function scopeConnected(Builder $builder)
    {
        return $builder->where('expires_at', '>=', now());
    }

    public function username(): string
    {
        return $this->credentials['name'] ?? ($this->credentials['username'] ?? 'John Doe');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isConnected(): bool
    {
        return $this->connected_at && $this->expires_at && $this->expires_at->gt(now());
    }
}
