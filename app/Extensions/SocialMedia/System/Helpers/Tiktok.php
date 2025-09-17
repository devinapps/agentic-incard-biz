<?php

namespace App\Extensions\SocialMedia\System\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Tiktok
{
    protected array $config = [];
    protected ?string $accessToken = null;

    public function __construct(?array $config = null, ?string $accessToken = null)
    {
        $this->accessToken = $accessToken;
        $this->config = $config ?? config('social-media.tiktok');

        $this->config = array_merge($this->config, [
            'app_id'       => setting('TIKTOK_APP_ID'),
            'app_key'      => setting('TIKTOK_APP_KEY'),
            'app_secret'   => setting('TIKTOK_APP_SECRET'),
        ]);

        $this->config['redirect_uri'] = secure_url($this->config['redirect_uri']);
    }

    private function apiUrl(string $endpoint, array $params = [], bool $isBaseUrl = false): string
    {
        $apiUrl = $isBaseUrl ? $this->config['base_url'] : $this->config['api_url'];

        if (str_starts_with($endpoint, '/')) {
            $endpoint = substr($endpoint, 1);
        }

        $v = $this->config['api_version'];
        $versionedUrlWithEndpoint = $apiUrl . '/' . ($v ? ($v . '/') : '') . $endpoint;

        if (count($params)) {
            $versionedUrlWithEndpoint .= '?' . http_build_query($params);
        }

        return $versionedUrlWithEndpoint;
    }

    public function setToken(string $bearerToken): self
    {
        $this->accessToken = $bearerToken;

        return $this;
    }

    public static function authRedirect()
    {
        $tiktok = new self;
        $client_key = $tiktok->config['app_key'];
        $scope = collect($tiktok->config['scope'])->join(',');
        $response_type = 'code';
        $state = '';
        $redirect_uri = $tiktok->config['redirect_uri'];

        // Generate PKCE code verifier and challenge
        $codeVerifier = $tiktok->generateCodeVerifier();
        $codeChallenge = $tiktok->generateCodeChallenge($codeVerifier);
        
        // Store code verifier in session for later use
        session(['tiktok_code_verifier' => $codeVerifier]);

        $apiUri = "{$tiktok->config['base_url']}/{$tiktok->config['api_version']}/auth/authorize?client_key=$client_key&response_type=$response_type&scope=$scope&redirect_uri=$redirect_uri&state=$state&code_challenge=$codeChallenge&code_challenge_method=S256";

        return redirect($apiUri);
    }

    public function getAccessToken($code)
    {
        $apiUri = $this->apiUrl('oauth/token/');
        
        // Get code verifier from session
        $codeVerifier = session('tiktok_code_verifier');

        $postData = [
            'client_key'    => $this->config['app_key'],
            'client_secret' => $this->config['app_secret'],
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $this->config['redirect_uri'],
        ];

        // Add code verifier if exists
        if ($codeVerifier) {
            $postData['code_verifier'] = $codeVerifier;
        }

        Log::info('TikTok getAccessToken request:', [
            'url' => $apiUri,
            'data' => array_merge($postData, ['client_secret' => '***HIDDEN***'])
        ]);

        $response = Http::asForm()->post($apiUri, $postData);

        Log::info('TikTok getAccessToken response:', [
            'status' => $response->status(),
            'body' => $response->json(),
            'headers' => $response->headers()
        ]);

        return $response;
    }

    public function refreshAccessToken()
    {
        $apiUri = $this->apiUrl('oauth/token/');

        return Http::asForm()->post(
            $apiUri,
            [
                'client_key'    => $this->config['app_key'],
                'client_secret' => $this->config['app_secret'],
                'grant_type'    => 'refresh_token',
                'refresh_token' => $this->accessToken,
            ]
        );
    }

    public function getAccountInfo(?array $fields = null)
    {
        $apiUri = $this->apiUrl('user/info/', [
            'fields' => collect($fields)->join(','),
        ]);

        return Http::withToken($this->accessToken)->get($apiUri);
    }

    public function getCreatorInfo(): array
    {
        $headers = [
            'Content-Type: application/json',
            "Authorization: Bearer $this->accessToken",
        ];

        $url = $this->apiUrl('post/publish/creator_info/query/');
        
        Log::info('TikTok getCreatorInfo request:', [
            'url' => $url,
            'access_token' => $this->accessToken ? 'SET' : 'NOT_SET'
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        Log::info('TikTok getCreatorInfo response:', [
            'http_code' => $httpCode,
            'curl_error' => $curlError,
            'response' => $result
        ]);

        if ($result === false || $httpCode !== 200) {
            Log::error('TikTok getCreatorInfo failed:', [
                'http_code' => $httpCode,
                'curl_error' => $curlError
            ]);
            
            return [
                'error' => [
                    'code' => 'api_error',
                    'message' => 'Failed to fetch creator info',
                    'http_code' => $httpCode,
                    'curl_error' => $curlError
                ]
            ];
        }

        $decoded = json_decode($result, true);
        
        if ($decoded === null) {
            Log::error('TikTok getCreatorInfo JSON decode failed:', [
                'response' => $result,
                'json_last_error' => json_last_error_msg()
            ]);
        }
        
        return $decoded ?? [
            'error' => [
                'code' => 'json_decode_error',
                'message' => 'Invalid JSON response',
                'raw_response' => $result
            ]
        ];
    }

    public function postVideo(array $postData): \Illuminate\Http\Client\Response
    {
        return Http::withToken($this->accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json; charset=UTF-8',
            ])
            ->post($this->apiUrl('post/publish/video/init/'), $postData);
    }

    public function postPhoto(array $postData)
    {
        return Http::withToken($this->accessToken)
            ->acceptJson()
            ->post($this->apiUrl('post/publish/content/init/'), $postData);
    }

    public function getPostAnalytics(array $videoIds, array $fields = [])
    {
        $apiUri = $this->apiUrl('video/query/', ['fields' => collect($fields)->join(',')]);

        return Http::withToken($this->accessToken)
            ->post($apiUri, [
                'filters' => [
                    'video_ids' => $videoIds,
                ],
            ]);
    }

    /**
     * Generate PKCE code verifier
     */
    private function generateCodeVerifier(): string
    {
        $codeVerifier = '';
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~';
        $charactersLength = strlen($characters);
        
        // Code verifier must be 43-128 characters long
        for ($i = 0; $i < 128; $i++) {
            $codeVerifier .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $codeVerifier;
    }

    /**
     * Generate PKCE code challenge from code verifier
     */
    private function generateCodeChallenge(string $codeVerifier): string
    {
        return rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
    }
}
