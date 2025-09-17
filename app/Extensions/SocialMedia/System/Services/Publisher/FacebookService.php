<?php

namespace App\Extensions\SocialMedia\System\Services\Publisher;

use App\Extensions\SocialMedia\System\Helpers\Facebook;
use App\Extensions\SocialMedia\System\Services\Publisher\Contracts\BasePublisherService;

class FacebookService extends BasePublisherService
{
    public function handle()
    {
        $media = $this->post->image;

        $message = $this->post->content;

        $facebook = new Facebook;

        $facebook->setToken($this->accessToken);

        return match ((bool) $media) {
            // post an image if the brand post has images
            true => $facebook->publishPhotoOnPage($this->platformId, $message, [
                $media,
            ]),
            // post a video if the brand post has videos
            default => $facebook->publishTextOnPage($this->platformId, $message),
        };
    }
}
