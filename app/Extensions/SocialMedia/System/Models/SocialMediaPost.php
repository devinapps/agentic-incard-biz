<?php

namespace App\Extensions\SocialMedia\System\Models;

use App\Extensions\SocialMedia\System\Enums\PlatformEnum;
use App\Extensions\SocialMedia\System\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialMediaPost extends Model
{
    protected $table = 'ext_social_media_posts';

    protected $fillable = [
        'user_id',
        'post_id',
        'company_id',
        'campaign_id',
        'social_media_platform_id',
        'social_media_platform',
        'is_personalized_content',
        'tone',
        'content',
        'link',
        'image',
        'video',
        'is_repeated',
        'has_replicate',
        'repeat_period',
        'repeat_start_date',
        'repeat_time',
        'status',
        'scheduled_at',
        'posted_at',
    ];

    protected $casts = [
        'social_media_platform' => PlatformEnum::class,
        'has_replicate'         => 'boolean',
        'status'                => StatusEnum::class,
        'scheduled_at'          => 'datetime',
        'repeat_start_date'     => 'datetime:Y-m-d',
        'posted_at'             => 'datetime',
    ];

    protected $appends = [
        'link',
    ];

    public function getPlatformEnum()
    {
        if ($this->social_media_platform) {
            return $this->social_media_platform;
        }

        if ($this->platform->platform) {
            return PlatformEnum::from($this->platform->platform);
        }

        return null;
    }

    public function link(): Attribute
    {
        $status = $this->status;

        $platform = $this->social_media_platform;

        if ($status !== StatusEnum::published || is_null($this->post_id)) {
            return Attribute::make(function () {
                return null;
            });
        }

        $link = match ($platform) {
            //            PlatformEnum::facebook  => "https://www.facebook.com/share/p/{$this->post_id}",
            PlatformEnum::x         => "https://x.com/i/web/status/{$this->post_id}",
            PlatformEnum::linkedin  => "https://www.linkedin.com/feed/update/urn:li:activity:{$this->post_id}",
            //            PlatformEnum::instagram => "https://www.instagram.com/p/{$this->post_id}",
            default                 => null,
        };

        return Attribute::make(function () use ($link) {
            return $link;
        });
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(SocialMediaPlatform::class, 'social_media_platform_id', 'id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(SocialMediaSharedLog::class, 'social_media_post_id');
    }
}
