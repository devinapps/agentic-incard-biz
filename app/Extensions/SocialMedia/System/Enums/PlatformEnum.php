<?php

namespace App\Extensions\SocialMedia\System\Enums;

use App\Extensions\SocialMedia\System\Models\SocialMediaPlatform;
use Illuminate\Support\Facades\Auth;

enum PlatformEnum: string
{
    case facebook = 'facebook';

    case x = 'x';

    case instagram = 'instagram';

    case linkedin = 'linkedin';

    case tiktok = 'tiktok';

    public function contentCharacterLength(): int
    {
        return match ($this) {
            self::facebook  => 63206,
            self::instagram => 2200,
            self::x         => 280,
            self::linkedin  => 2900,
            self::tiktok    => 2900,
        };
    }

    public static function toArray(): array
    {
        return [
            self::facebook->value,
            self::instagram->value,
            self::x->value,
            self::linkedin->value,
            self::tiktok->value,
        ];
    }

    public static function all(): array
    {
        return [
            self::facebook,
            self::instagram,
            self::x,
            self::linkedin,
            self::tiktok,
        ];
    }

    public function platform()
    {
        $platforms = SocialMediaPlatform::query()
            ->where('user_id', Auth::id())
            ->whereIn('platform', array_values(self::all()))
            ->orderByDesc('expires_at')
            ->get();

        return match ($this) {
            self::facebook  => $platforms->where('platform', self::facebook->value)->first(),
            self::instagram => $platforms->where('platform', self::instagram->value)->first(),
            self::x         => $platforms->where('platform', self::x->value)->first(),
            self::linkedin  => $platforms->where('platform', self::linkedin->value)->first(),
            self::tiktok    => $platforms->where('platform', self::tiktok->value)->first(),
        };
    }

    public function credentials(): array
    {
        return match ($this) {
            self::facebook => [
                'facebook_app_id'         => setting('FACEBOOK_APP_ID'),
                'facebook_app_secret'     => setting('FACEBOOK_APP_SECRET'),
                //                'facebook_webhook_secret' => setting('FACEBOOK_WEBHOOK_SECRET', 'default-password'),
            ],
            self::instagram => [
                'instagram_app_id'         => setting('INSTAGRAM_APP_ID'),
                'instagram_app_secret'     => setting('INSTAGRAM_APP_SECRET'),
                'instagram_webhook_secret' => setting('INSTAGRAM_WEBHOOK_SECRET', 'default-password'),
            ],
            self::x => [
                //                'x_app_id'              => setting('X_APP_ID'),
                'x_api_key'             => setting('X_API_KEY'),
                'x_api_secret'          => setting('X_API_SECRET'),
                'x_access_token'        => setting('X_ACCESS_TOKEN'),
                'x_access_token_secret' => setting('X_ACCESS_TOKEN_SECRET'),
                'x_client_id'           => setting('X_CLIENT_ID'),
                'x_client_secret'       => setting('X_CLIENT_SECRET'),
            ],
            self::linkedin => [
                'linkedin_app_id'     => setting('LINKEDIN_APP_ID'),
                'linkedin_app_secret' => setting('LINKEDIN_APP_SECRET'),
            ],
            self::tiktok => [
                'tiktok_app_id'       => setting('TIKTOK_APP_ID'),
                'tiktok_app_key'      => setting('TIKTOK_APP_KEY'),
                'tiktok_app_secret'   => setting('TIKTOK_APP_SECRET'),
            ],
        };
    }
}
