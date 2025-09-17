<?php

namespace App\Extensions\MarketingBot\System\Http\Controllers\Telegram;

use App\Extensions\MarketingBot\System\Models\Telegram\TelegramGroup;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TelegramGroupController extends Controller
{
    public function index()
    {
        return view('marketing-bot::telegram-group.index', [
            'items' => TelegramGroup::query()->where('user_id', Auth::id())->get(),
        ]);
    }

    public function destroy(TelegramGroup $telegramGroup): JsonResponse
    {
        $telegramGroup->delete();

        return response()->json([
            'status'  => 'success',
            'message' => __('Contact deleted successfully'),
        ]);
    }
}
