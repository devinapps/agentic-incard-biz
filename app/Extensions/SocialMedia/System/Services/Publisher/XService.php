<?php

namespace App\Extensions\SocialMedia\System\Services\Publisher;

use App\Extensions\SocialMedia\System\Helpers\X;
use App\Extensions\SocialMedia\System\Services\Publisher\Contracts\BasePublisherService;

class XService extends BasePublisherService
{
    public function handle()
    {
        $media = $this->post->image;

        $message = $this->post->content;

        $x = new X;
        $x->setToken($this->accessToken);

        $response = match ((bool) $media) {
            true    => $x->publishMediaPost([$media], $message),
            default => $x->publishTweet($message),
        };

        if (! ($response instanceof \Illuminate\Http\Client\Response)) {
            $response = json_decode(json_encode($response), true);
        }

        return $response;
    }
}
