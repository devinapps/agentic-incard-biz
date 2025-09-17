<?php

namespace App\Extensions\SocialMedia\System\Http\Controllers\Oauth;

use App\Extensions\SocialMedia\System\Enums\PlatformEnum;
use App\Extensions\SocialMedia\System\Helpers\Facebook;
use App\Extensions\SocialMedia\System\Models\SocialMediaPlatform;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class FacebookController extends Controller
{
    private function cacheKey(): string
    {
        return 'platforms.' . Auth::id() . '.facebook';
    }

    public function redirect(Request $request): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with([
                'type'    => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        if (setting('FACEBOOK_APP_ID') && setting('FACEBOOK_APP_SECRET')) {

            if ($request->has('platform_id') && $request->get('platform_id')) {
                cache()->remember($this->cacheKey(), 60, function () use ($request) {
                    return $request->get('platform_id');
                });
            }

            return Facebook::authRedirect([
                'pages_manage_posts',
                'pages_show_list',
                'pages_read_user_content',
                'pages_read_engagement',
                'read_insights',
            ]);
        }

        return back()->with([
            'type'    => 'error',
            'message' => 'Facebook app id and secret not set. Please contact the administrator.',
        ]);
    }

    public function callback(Request $request): RedirectResponse
    {

        $fb = new Facebook;

        $code = $request->get('code');

        if (! $code) {
            return redirect()->route('dashboard.user.social-media.platforms')
                ->with([
                    'type'    => 'error',
                    'message' => 'Something went wrong, please try again.',
                ]);
        }

        try {

            $token = $fb->getAccessToken($code)->throw()->json('access_token');

            $fb->setToken($token);

            $page = $fb->getPagesInfo(['name,username,picture,access_token'])->throw()->json('data');

            $page = Arr::first($page);

            if (! $page) {
                return redirect()->route('dashboard.user.social-media.platforms')
                    ->with([
                        'type'    => 'error',
                        'message' => 'Something went wrong, please try again.',
                    ]);
            }

        } catch (Exception $exception) {
            return redirect()->route('dashboard.user.social-media.platforms')
                ->with([
                    'type'    => 'error',
                    'message' => 'Something went wrong, please try again.',
                ]);
        }

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
                        'platform_id'             => data_get($page, 'id'),
                        'name'                    => data_get($page, 'name'),
                        'username'                => data_get($page, 'username'),
                        'picture'                 => data_get($page, 'picture.data.url'),

                        'access_token'           => data_get($page, 'access_token'),
                        'access_token_expire_at' => config('social-media.facebook.access_token_expire_at', now()->addDay()),

                        'refresh_token'           => data_get($page, 'access_token'),
                        'refresh_token_expire_at' => config('social-media.facebook.access_token_expire_at', now()->addDay()),
                    ],
                    'connected_at' => now(),
                    'expires_at'   => config('social-media.facebook.access_token_expire_at', now()->addDay()),
                ]);
            }

            cache()->forget($this->cacheKey());

        } else {
            SocialMediaPlatform::query()->create([
                'user_id'     => Auth::id(),
                'platform'    => PlatformEnum::facebook->value,
                'credentials' => [
                    'type'                    => 'user',
                    'platform_id'             => data_get($page, 'id'),
                    'name'                    => data_get($page, 'name'),
                    'username'                => data_get($page, 'username'),
                    'picture'                 => data_get($page, 'picture.data.url'),

                    'access_token'           => data_get($page, 'access_token'),
                    'access_token_expire_at' => config('social-media.facebook.access_token_expire_at', now()->addDay()),

                    'refresh_token'           => data_get($page, 'access_token'),
                    'refresh_token_expire_at' => config('social-media.facebook.access_token_expire_at', now()->addDay()),
                ],
                'connected_at' => now(),
                'expires_at'   => config('social-media.facebook.access_token_expire_at', now()->addDay()),
            ]);
        }

        return redirect()->route('dashboard.user.social-media.platforms')
            ->with([
                'type'    => 'success',
                'message' => trans('Facebook account connected successfully.'),
            ]);
    }

    public function webhook(Request $request)
    {
        $verify_token = setting('FACEBOOK_WEBHOOK_SECRET', 'default-password');

        if ($request->get('hub_mode') === 'subscribe' && $request->get('hub_verify_token') === $verify_token) {
            echo $request->get('hub_challenge');
            exit;
        }

        http_response_code(403);
        echo 'Token invalid';
        exit;
    }
}
