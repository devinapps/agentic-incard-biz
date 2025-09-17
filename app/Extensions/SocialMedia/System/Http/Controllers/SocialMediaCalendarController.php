<?php

namespace App\Extensions\SocialMedia\System\Http\Controllers;

use App\Extensions\SocialMedia\System\Models\SocialMediaPost;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SocialMediaCalendarController extends Controller
{
    public function __invoke()
    {
        $items = SocialMediaPost::query()
            ->where('user_id', Auth::id())
            ->whereDate('scheduled_at', '>=', now()->startOfMonth()->format('Y-m-d'))
            ->whereDate('scheduled_at', '<=', now()->endOfMonth()->format('Y-m-d'))
            ->get();

        return view('social-media::calendar', [
            'items' => $items,
        ]);
    }
}
