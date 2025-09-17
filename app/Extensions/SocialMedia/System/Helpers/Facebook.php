<?php

namespace App\Extensions\SocialMedia\System\Helpers;

use App\Extensions\SocialMedia\System\Helpers\Contracts\BaseMetaHelper;
use Illuminate\Http\Client\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;

class Facebook extends BaseMetaHelper
{
    protected array $config = [];

    public function __construct(?array $config = null, protected ?string $accessToken = null)
    {
        $this->config = $config ?? config('social-media.facebook');

        $this->config = array_merge($this->config, [
            'app_id'       => setting('FACEBOOK_APP_ID'),
            'app_secret'   => setting('FACEBOOK_APP_SECRET'),
            'redirect_uri' => secure_url(config('social-media.facebook.redirect_uri')),
        ]);
    }

    public static function authRedirect(array $scopes = []): RedirectResponse
    {
        $fb = new self;

        if ($scopes) {
            $fb->config['scopes'] = $scopes;
        }

        $scopes = collect($fb->config['scopes'])->join(',');

        $authUri = $fb->apiUrl('dialog/oauth', [
            'response_type' => 'code',
            'client_id'     => $fb->config['app_id'],
            'redirect_uri'  => $fb->config['redirect_uri'],
            'scope'         => $scopes,
        ], true);

        return redirect($authUri);
    }

    public function refreshAccessToken(): Response
    {
        $apiUrl = $this->apiUrl('/oauth/access_token', [
            'client_id'         => $this->config['app_id'],
            'client_secret'     => $this->config['app_secret'],
            'grant_type'        => 'fb_exchange_token',
            'fb_exchange_token' => $this->accessToken,
        ]);

        return Http::post($apiUrl);
    }

    public function getAccountInfo(array $fields = []): Response
    {
        $apiUrl = $this->apiUrl('/me', [
            'access_token' => $this->accessToken,
            'fields'       => collect($fields)->join(','),
        ]);

        return Http::get($apiUrl);
    }

    public function getPagesInfo(array $fields = []): Response
    {
        $apiUrl = $this->apiUrl('/me/accounts', [
            'access_token' => $this->accessToken,
            'fields'       => collect($fields)->join(','),
        ]);

        return Http::get($apiUrl);
    }

    public function publishTextOnPage(int $pageId, string $text): Response
    {
        return Http::withToken($this->accessToken)
            ->acceptJson()
            ->post(
                $this->apiUrl($pageId . '/feed'),
                [
                    'message' => $text,
                ]
            );
    }

    public function publishPhotoOnPage(int $pageId, string $text, array $photos): Response
    {
        $attached_media = [];
        foreach ($photos as $url) {
            $res = Http::retry(3, 3000)
                ->withToken($this->accessToken)
                ->post($this->apiUrl($pageId . '/photos'), [
                    'url'       => url($url),
                    'published' => false,
                ]);
            $attached_media[] = ['media_fbid' => $res->json('id')];
        }

        return Http::retry(3, 3000)
            ->withToken($this->accessToken)
            ->post($this->apiUrl($pageId . '/feed'), [
                'message'        => $text,
                'attached_media' => $attached_media,
            ]);
    }

    public function publishVideoOnPage(string $pageId, string $fileUrl): Response
    {
        $postData = [
            'file_url'     => $fileUrl,
            'description'  => 'example caption',
            'access_token' => $this->accessToken,
        ];

        return Http::post($this->apiUrl("$pageId/videos"), $postData);
    }

    public function getPostAnalytics(string $postId, array $fields = []): Response
    {
        return Http::withToken($this->accessToken)
            ->get($this->apiUrl($postId, [
                'fields' => collect($fields)->join(','),
            ]));
    }
}
