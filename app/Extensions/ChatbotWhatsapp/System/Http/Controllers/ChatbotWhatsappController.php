<?php

namespace App\Extensions\ChatbotWhatsapp\System\Http\Controllers;

use App\Extensions\Chatbot\System\Http\Resources\Admin\ChatbotChannelResource;
use App\Extensions\Chatbot\System\Models\ChatbotChannel;
use App\Extensions\ChatbotWhatsapp\System\Http\Requests\WhatsappChannelStoreRequest;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ChatbotWhatsappController extends Controller
{
    public function store(WhatsappChannelStoreRequest $request): ChatbotChannelResource|JsonResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $item = ChatbotChannel::query()->create(
            $request->validated()
        );

        return ChatbotChannelResource::make($item)->additional([
            'status'  => 'success',
            'message' => trans('Chatbot channel successfully created'),
        ]);
    }
}
