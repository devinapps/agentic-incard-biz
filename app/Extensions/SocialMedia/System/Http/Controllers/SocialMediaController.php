<?php

namespace App\Extensions\SocialMedia\System\Http\Controllers;

use App\Extensions\SocialMedia\System\Enums\PlatformEnum;
use App\Extensions\SocialMedia\System\Models\SocialMediaPost;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class SocialMediaController extends Controller
{
    public function __invoke()
    {
        $platforms = PlatformEnum::all();

        $posts = SocialMediaPost::query()
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        $posts_stats = [
            'today'        => $this->getPostStats(Carbon::today(), Carbon::now()),
            'last_7_days'  => $this->getPostStats(Carbon::now()->subDays(7), Carbon::now()),
            'last_30_days' => $this->getPostStats(Carbon::now()->subDays(30), Carbon::now()),
        ];

        $platforms_published_posts = $this->getPublishedPostsByMonth();

        return view('social-media::index', compact('posts', 'platforms', 'posts_stats', 'platforms_published_posts'));
    }

    public function getPublishedPostsByMonth()
    {
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Platformları tanımla
        $platforms = [
            PlatformEnum::facebook->name,
            PlatformEnum::instagram->name,
            PlatformEnum::x->name,
            PlatformEnum::linkedin->name,
        ];

        // Veritabanından platform bazlı aylık yayınlanan postları getir
        $query = SocialMediaPost::selectRaw("
            social_media_platform as platform,
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as count
        ")
            ->where('status', 'published')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('platform', 'month')
            ->orderBy('month')
            ->get();

        // Son 12 ayın listesini oluştur
        $months = collect(range(0, 11))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('Y-m');
        })->reverse()->values();

        // Platform bazlı istatistikleri oluştur
        $result = collect($platforms)->map(function ($platform) use ($query, $months) {
            return [
                'name' => $platform,
                'data' => $months->map(function ($month) use ($query, $platform) {
                    return $query->where('platform', $platform)->where('month', $month)->sum('count') ?? 0;
                })->toArray(),
            ];
        })->toArray();

        return $result;
    }

    public function getPostStats($startDate, $endDate)
    {
        $query = SocialMediaPost::query()
            ->selectRaw("
            COUNT(*) as all_posts,
            SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published_posts,
            SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END) as scheduled_posts,
            SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_posts
        ")
            ->whereBetween('scheduled_at', [$startDate, $endDate])
            ->first();

        return [
            'all_posts'       => $query->all_posts ?? 0,
            'published_posts' => $query->published_posts ?? 0,
            'scheduled_posts' => $query->scheduled_posts ?? 0,
            'failed_posts'    => $query->failed_posts ?? 0,
        ];
    }
}
