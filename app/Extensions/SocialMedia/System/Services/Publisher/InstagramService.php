<?php

namespace App\Extensions\SocialMedia\System\Services\Publisher;

use App\Extensions\SocialMedia\System\Enums\LogStatusEnum;
use App\Extensions\SocialMedia\System\Enums\StatusEnum;
use App\Extensions\SocialMedia\System\Helpers\Instagram;
use App\Extensions\SocialMedia\System\Models\SocialMediaSharedLog;
use App\Extensions\SocialMedia\System\Services\Publisher\Contracts\BasePublisherService;

class InstagramService extends BasePublisherService
{
    public function handle()
    {
        $media = $this->post->image;

        $message = $this->post->content;

        // initialize the Instagram API class
        $instagram = new Instagram;
        // set the access token for the Instagram API class
        $instagram->setToken($this->accessToken);

        // check if the brand post has no media or more than 10 media
        if (! $media) {

            $this->post->update([
                'status' => StatusEnum::failed,
            ]);

            SocialMediaSharedLog::query()->create([
                'social_media_post_id' => $this->post->id,
                'response'             => [
                    'message' => 'Media not found.',
                ],
                'status'     => LogStatusEnum::failed,
                'created_at' => now(),
            ]);

            return false;
        }

        $postData = [
            'image_url' => url($media),
            'caption'   => $message,
        ];

        return $instagram->publishSingleMediaPost($this->platformId, $postData);
    }
}
