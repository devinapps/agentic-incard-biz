<?php

namespace App\Extensions\SocialMedia\System\Http\Controllers\Oauth;

use App\Extensions\SocialMedia\System\Enums\PlatformEnum;
use App\Extensions\SocialMedia\System\Helpers\Instagram;
use App\Extensions\SocialMedia\System\Models\SocialMediaPlatform;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstagramController extends Controller
{
    private function cacheKey(): string
    {
        return 'platforms.' . Auth::id() . '.instagram';
    }

    public function redirect(Request $request)
    {
        if (Helper::appIsDemo()) {
            return back()->with([
                'type'    => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        if (setting('INSTAGRAM_APP_ID') && setting('INSTAGRAM_APP_SECRET')) {
            if ($request->has('platform_id') && $request->get('platform_id')) {
                cache()->remember($this->cacheKey(), 60, function () use ($request) {
                    return $request->get('platform_id');
                });
            }

            return Instagram::authRedirect([
                'ads_management',
                'business_management',
                'instagram_basic',
                'instagram_content_publish',
                'pages_read_engagement',
            ]);
        }

        return back()->with([
            'type'    => 'error',
            'message' => 'Instagram app id and secret not set. Please contact the administrator.',
        ]);
    }

    public function callback(Request $request)
    {
        $code = $request->get('code');

        if (! $code) {
            return redirect()->route('dashboard.user.social-media.platforms')
                ->with([
                    'type'    => 'error',
                    'message' => trans('Something went wrong, please try again.'),
                ]);
        }

        $instagram = new Instagram;

        try {

            $token = $instagram->getAccessToken($code)->throw()->json('access_token');
            $instagram->setToken($token);

            $page = $instagram->getAccountInfo(['connected_instagram_account,name,access_token'])
                ->throw()
                ->json('data.0');
        } catch (Exception $exception) {
            return redirect()->route('dashboard.user.social-media.platforms')
                ->with([
                    'type'    => 'error',
                    'message' => 'Something went wrong, please try again.',
                ]);
        }

        if (! isset($page['connected_instagram_account'])) {
            return redirect()->route('dashboard.user.social-media.platforms')
                ->with([
                    'type'    => 'error',
                    'message' => trans('Something went wrong, please try again.'),
                ]);
        }

        $igAccount = $instagram->getInstagramInfo($page['connected_instagram_account']['id'], ['id,name,username,profile_picture_url'])
            ->throw()
            ->json();

        $platformId = cache($this->cacheKey());

        if ($platformId && is_numeric($platformId)) {

            $platform = SocialMediaPlatform::query()
                ->where('id', $platformId)
                ->where('user_id', Auth::id())
                ->where('platform', PlatformEnum::instagram->value)
                ->first();

            if ($platform) {
                $platform->update([
                    'credentials' => [
                        'type'                    => 'user',
                        'id'                      => $igAccount['id'],
                        'platform_id'             => $igAccount['id'],
                        'name'                    => $igAccount['name'],
                        'username'                => $igAccount['username'],
                        'picture'                 => $igAccount['profile_picture_url'],
                        'access_token'            => $igAccount['access_token'] ?? $token,
                    ],
                    'expires_at' => now()->addMonths(2),
                ]);
            }

            cache()->forget($this->cacheKey());
        } else {
            SocialMediaPlatform::query()->create([
                'user_id'     => Auth::id(),
                'platform'    => PlatformEnum::instagram->value,
                'credentials' => [
                    'type'                    => 'user',
                    'id'                      => $igAccount['id'],
                    'platform_id'             => $igAccount['id'],
                    'name'                    => $igAccount['name'],
                    'username'                => $igAccount['username'],
                    'picture'                 => $igAccount['profile_picture_url'],
                    'access_token'            => $igAccount['access_token'] ?? $token,
                ],
                'expires_at' => now()->addMonths(2),
            ]);
        }

        return to_route('dashboard.user.social-media.platforms')->with([
            'type'    => 'success',
            'message' => trans('Instagram account connected successfully.'),
        ]);
    }

    public function webhook(Request $request)
    {
        $verify_token = setting('INSTAGRAM_WEBHOOK_SECRET', 'default-password');

        if ($request->get('hub_mode') === 'subscribe' && $request->get('hub_verify_token') === $verify_token) {
            echo $request->get('hub_challenge');
            exit;
        }

        http_response_code(403);
        echo 'Token invalid';
        exit;
    }
}
