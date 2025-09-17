<?php

namespace App\Extensions\SocialMedia\System\Services\Publisher;

use App\Extensions\SocialMedia\System\Helpers\Tiktok;
use App\Extensions\SocialMedia\System\Services\Publisher\Contracts\BasePublisherService;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TiktokService extends BasePublisherService
{
    public function handle(): Response
    {
        try {
            Log::info('TikTok Publisher: Starting video publish', [
                'post_id' => $this->post->id,
                'platform_id' => $this->platformId
            ]);

            $media = $this->post->video;
            
            if (!$media) {
                Log::error('TikTok Publisher: No video file found', ['post_id' => $this->post->id]);
                // Create a mock failed HTTP response
                return new \Illuminate\Http\Client\Response(
                    new \GuzzleHttp\Psr7\Response(400, [], json_encode(['error' => 'No video file found for TikTok post']))
                );
            }

            $message = $this->post->content;

            $tiktok = new Tiktok;
            $tiktok->setToken($this->accessToken);

            // Get options from post metadata or use defaults
            $options = $this->post->options ?? [];

            $postData = [
                'post_info' => [
                    'title'                    => str($message)->limit(150)->toString(),
                    'privacy_level'            => $options['privacy_level'] ?? config('social-media.tiktok.options.privacy_level'),
                    'disable_duet'             => $options['disable_duet'] ?? config('social-media.tiktok.options.disable_duet'),
                    'disable_comment'          => $options['disable_comment'] ?? config('social-media.tiktok.options.disable_comment'),
                    'disable_stitch'           => $options['disable_stitch'] ?? config('social-media.tiktok.options.disable_stitch'),
                    'video_cover_timestamp_ms' => $options['video_cover_timestamp_ms'] ?? config('social-media.tiktok.options.video_cover_timestamp_ms'),
                ],
                'source_info' => [
                    'source'    => 'PULL_FROM_URL',
                    'video_url' => url($media),
                ],
            ];

            Log::info('TikTok Publisher: Sending post data', [
                'post_id' => $this->post->id,
                'video_url' => url($media),
                'title' => $postData['post_info']['title']
            ]);

            $response = $tiktok->postVideo($postData);
            
            Log::info('TikTok Publisher: Response received', [
                'post_id' => $this->post->id,
                'status' => $response->status(),
                'response_body' => $response->json()
            ]);

            return $response;
            
        } catch (\Exception $e) {
            Log::error('TikTok Publisher: Error occurred', [
                'post_id' => $this->post->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return a mock failed HTTP response
            return new \Illuminate\Http\Client\Response(
                new \GuzzleHttp\Psr7\Response(500, [], json_encode(['error' => $e->getMessage()]))
            );
        }
    }
}
