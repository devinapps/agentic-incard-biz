<?php

namespace App\Extensions\SocialMedia\System\Http\Controllers\Oauth;

use App\Extensions\SocialMedia\System\Enums\PlatformEnum;
use App\Extensions\SocialMedia\System\Helpers\Tiktok;
use App\Extensions\SocialMedia\System\Models\SocialMediaPlatform;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TiktokController extends Controller
{
    public function __construct(public Tiktok $api) {}

    private function cacheKey(): string
    {
        return 'platforms.' . Auth::id() . '.facebook';
    }

    public function redirect(Request $request)
    {
        if (Helper::appIsDemo()) {
            return back()->with([
                'type'    => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        if ($request->has('platform_id') && $request->get('platform_id')) {
            cache()->remember($this->cacheKey(), 60, function () use ($request) {
                return $request->get('platform_id');
            });
        }

        return $this->api->authRedirect();
    }

    public function callback(Request $request)
    {

        $code = $request->get('code');

        if (! $code) {
            return back()->with([
                'type'    => 'error',
                'message' => trans('Something went wrong, please try again.'),
            ]);
        }

        $response = $this->api->getAccessToken($code)
            ->throw();

        if ($response->json('error')) {
            echo $response->status();
            exit();
        }

        $tokenData = $response->object();

        $platformId = cache($this->cacheKey());

        if ($platformId && is_numeric($platformId)) {

            $item = SocialMediaPlatform::query()
                ->where('user_id', Auth::id())
                ->where('platform', PlatformEnum::tiktok->value)
                ->where('id', $platformId)
                ->first();

            if ($item) {
                $item->update([
                    'credentials' => [
                        'platform_id'            => $tokenData->open_id,
                        'access_token'           => $tokenData->access_token ?? '',
                        'access_token_expire_at' => now()->addSeconds($tokenData->expires_in ?? 0),

                        'refresh_token'           => $tokenData->refresh_token ?? '',
                        'refresh_token_expire_at' => now()->addSeconds($tokenData->refresh_expires_in ?? 0),
                    ],
                    'expires_at'   => now()->addSeconds($tokenData->expires_in ?? 0),
                ]);

                $this->api->setToken($tokenData->access_token);

                $this->setProfileInfo($item);
            }

            cache()->forget($this->cacheKey());
        } else {
            $item = SocialMediaPlatform::query()->create([
                'user_id'     => Auth::id(),
                'platform'    => PlatformEnum::tiktok->value,
                'credentials' => [
                    'platform_id'            => $tokenData->open_id,
                    'access_token'           => $tokenData->access_token ?? '',
                    'access_token_expire_at' => now()->addSeconds($tokenData->expires_in ?? 0),

                    'refresh_token'           => $tokenData->refresh_token ?? '',
                    'refresh_token_expire_at' => now()->addSeconds($tokenData->refresh_expires_in ?? 0),
                ],
                'connected_at' => null,
                'expires_at'   => now()->addSeconds($tokenData->expires_in ?? 0),
            ]);

            $this->api->setToken($tokenData->access_token);

            $this->setProfileInfo($item);
        }

        return $this->redirectToPlatforms('success', 'Linkedin account connected successfully.');
    }

    protected function setProfileInfo(SocialMediaPlatform $item): void
    {
        $userData = $this->api->getAccountInfo([
            'open_id',
        ])
            ->throw()
            ->json('data.user');

        $creatorInfoData = $this->api->getCreatorInfo();

        $creatorInfo = [];

        if (isset($creatorInfoData['error']['code']) && $creatorInfoData['error']['code'] === 'ok') {
            $creatorInfo = $creatorInfoData['data'] ?? [];
        }

        $item->update([
            'credentials' => array_merge($item->credentials, [
                'name'     => $creatorInfo['creator_nickname'] ?? '',
                'username' => $creatorInfo['creator_username'] ?? '',
                'picture'  => $creatorInfo['creator_avatar_url'] ?? '',
                'meta'     => $creatorInfo ?? [],
            ]),
        ]);
    }

    public function redirectToPlatforms(string $type = 'success', string $message = 'Linkedin account connected successfully.'): RedirectResponse
    {
        return to_route('dashboard.user.social-media.platforms')->with([
            'type'    => $type,
            'message' => trans($message),
        ]);
    }

    public function verify()
    {
        return setting('TIKTOK_OAUTH_VERIFY', 'tiktok-developers-site-verification=U4IyiClYTw8yPBShtWnQkY01ncYucsC3');
    }
}
