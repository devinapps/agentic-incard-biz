<?php

namespace App\Extensions\SocialMedia\System\Services\Publisher;

use App\Extensions\SocialMedia\System\Helpers\Linkedin;
use App\Extensions\SocialMedia\System\Services\Publisher\Contracts\BasePublisherService;

class LinkedinService extends BasePublisherService
{
    public function handle()
    {
        $media = $this->post->image;

        $message = $this->post->content;

        $linkedin = new Linkedin;
        $linkedin->setToken($this->accessToken);

        return match ((bool) $media) {
            true => $linkedin->publishImage($this->platformId, [
                public_path($media),
            ], $message),

            default => $linkedin->publishText($this->platformId, $message),
        };
    }
}
