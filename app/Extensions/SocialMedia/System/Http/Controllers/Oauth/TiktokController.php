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
use Illuminate\Support\Facades\Log;

class TiktokController extends Controller
{
    public Tiktok $api;

    public function __construct(Tiktok $api)
    {
        $this->api = $api;
    }

    private function cacheKey(): string
    {
        return 'platforms.' . Auth::id() . '.tiktok';
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
        Log::info('TikTok OAuth callback started:', [
            'query_params' => $request->all(),
            'user_id' => Auth::id()
        ]);

        $code = $request->get('code');

        if (! $code) {
            Log::error('TikTok OAuth callback: No code provided', [
                'query_params' => $request->all()
            ]);
            
            return back()->with([
                'type'    => 'error',
                'message' => trans('Something went wrong, please try again.'),
            ]);
        }

        try {
            $response = $this->api->getAccessToken($code);
            
            if (!$response->successful()) {
                Log::error('TikTok getAccessToken failed:', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                    'headers' => $response->headers()
                ]);
                
                return back()->with([
                    'type'    => 'error',
                    'message' => trans('Failed to get access token from TikTok.'),
                ]);
            }

            // Clear code verifier from session after use
            session()->forget('tiktok_code_verifier');

            if ($response->json('error')) {
                Log::error('TikTok API returned error:', [
                    'error' => $response->json(),
                    'status' => $response->status()
                ]);
                
                return back()->with([
                    'type'    => 'error',
                    'message' => trans('TikTok API error: ') . ($response->json('error_description') ?? 'Unknown error'),
                ]);
            }

            $tokenData = $response->object();
            
            Log::info('TikTok access token received:', [
                'open_id' => $tokenData->open_id ?? 'N/A',
                'expires_in' => $tokenData->expires_in ?? 'N/A',
                'scope' => $tokenData->scope ?? 'N/A'
            ]);

        } catch (\Exception $e) {
            Log::error('TikTok OAuth callback exception:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with([
                'type'    => 'error',
                'message' => trans('An error occurred during TikTok authentication.'),
            ]);
        }

        $platformId = cache($this->cacheKey());

        if ($platformId && is_numeric($platformId)) {

            $item = SocialMediaPlatform::query()
                ->where('user_id', Auth::id())
                ->where('platform', PlatformEnum::tiktok)
                ->where('id', $platformId)
                ->first();

            if ($item) {
                $item->update([
                    'credentials' => [
                        'platform_id'            => $tokenData->open_id,
                        'access_token'           => $tokenData->access_token ?? '',
                        'access_token_expire_at' => now()->addSeconds($tokenData->expires_in ?? 0),
                        'scope'                  => $tokenData->scope ?? '',

                        'refresh_token'           => $tokenData->refresh_token ?? '',
                        'refresh_token_expire_at' => now()->addSeconds($tokenData->refresh_expires_in ?? 0),
                    ],
                    'connected_at' => now(),
                    'expires_at'   => now()->addSeconds($tokenData->expires_in ?? 0),
                ]);

                $this->api->setToken($tokenData->access_token);

                $this->setProfileInfo($item);
            }

            cache()->forget($this->cacheKey());
        } else {
            $item = SocialMediaPlatform::query()->create([
                'user_id'     => Auth::id(),
                'platform'    => PlatformEnum::tiktok,
                'credentials' => [
                    'platform_id'            => $tokenData->open_id,
                    'access_token'           => $tokenData->access_token ?? '',
                    'access_token_expire_at' => now()->addSeconds($tokenData->expires_in ?? 0),
                    'scope'                  => $tokenData->scope ?? '',

                    'refresh_token'           => $tokenData->refresh_token ?? '',
                    'refresh_token_expire_at' => now()->addSeconds($tokenData->refresh_expires_in ?? 0),
                ],
                'connected_at' => now(),
                'expires_at'   => now()->addSeconds($tokenData->expires_in ?? 0),
            ]);

            $this->api->setToken($tokenData->access_token);

            $this->setProfileInfo($item);
        }

        return $this->redirectToPlatforms('success', 'TikTok account connected successfully.');
    }

    protected function setProfileInfo(SocialMediaPlatform $item): void
    {
        try {
            Log::info('TikTok setProfileInfo started for item:', ['item_id' => $item->id]);

            $userResponse = $this->api->getAccountInfo([
                'open_id',
                'union_id',
                'avatar_url',
                'avatar_url_100',
                'avatar_url_200',
                'avatar_large_url',
                'display_name'
            ]);

            $userData = $userResponse->throw()->json('data.user');

            Log::info('TikTok getAccountInfo response:', ['user_data' => $userData]);

            // Only try to get creator info if we have the right scopes
            $tokenData = $item->credentials;
            $hasCreatorScope = isset($tokenData['scope']) &&
                (str_contains($tokenData['scope'], 'video.publish') || str_contains($tokenData['scope'], 'video.upload'));

            $creatorInfo = [];
            
            if ($hasCreatorScope) {
                Log::info('TikTok has creator scope, fetching creator info');
                $creatorInfoData = $this->api->getCreatorInfo();
                Log::info('TikTok getCreatorInfo response:', ['creator_data' => $creatorInfoData]);

                if ((!isset($creatorInfoData['error']) && isset($creatorInfoData['data'])) ||
                    (isset($creatorInfoData['error']['code']) && $creatorInfoData['error']['code'] === 'ok')) {
                    $creatorInfo = $creatorInfoData['data'] ?? [];
                    Log::info('TikTok creator info extracted:', ['creator_info' => $creatorInfo]);
                }
            } else {
                Log::info('TikTok does not have creator scope, skipping creator info');
            }

            $updateData = [
                'name'     => $creatorInfo['creator_nickname'] ?? $userData['display_name'] ?? '',
                'username' => $creatorInfo['creator_username'] ?? '',
                'picture'  => $creatorInfo['creator_avatar_url'] ?? $userData['avatar_url'] ?? $userData['avatar_url_100'] ?? '',
                'meta'     => array_merge($userData ?? [], $creatorInfo ?? []),
            ];

            Log::info('TikTok updating item credentials:', ['update_data' => $updateData]);

            $item->update([
                'credentials' => array_merge($item->credentials, $updateData),
            ]);

            Log::info('TikTok profile info updated successfully');

        } catch (\Exception $e) {
            Log::error('TikTok setProfileInfo failed:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw exception, just log it so OAuth can continue
        }
    }

    public function redirectToPlatforms(string $type = 'success', string $message = 'TikTok account connected successfully.'): RedirectResponse
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
