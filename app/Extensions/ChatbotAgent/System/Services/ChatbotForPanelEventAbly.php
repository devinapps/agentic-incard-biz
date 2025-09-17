<?php

namespace App\Extensions\ChatbotAgent\System\Services;

use App\Extensions\Chatbot\System\Http\Resources\Admin\ChatbotConversationForAblyResource;
use App\Extensions\Chatbot\System\Http\Resources\Api\ChatbotHistoryResource;
use App\Extensions\Chatbot\System\Models\Chatbot;
use App\Extensions\Chatbot\System\Models\ChatbotConversation;
use App\Extensions\Chatbot\System\Models\ChatbotHistory;
use App\Extensions\ChatbotAgent\System\Services\Contracts\AblyService;

class ChatbotForPanelEventAbly extends AblyService
{
    public static string $chanel = 'panel-conversation-';

    public static function dispatch(
        Chatbot $chatbot,
        ChatbotConversation $chatbotConversation,
        ?ChatbotHistory $history = null,
    ): void {
        $ably = self::ablyRest();

        $channel = $ably->channels->get(
            self::$chanel . $chatbot->getAttribute('user_id')
        );

        $channel->publish('conversation', [
            'userId'              => $chatbot->getAttribute('user_id'),
            'conversationId'      => $chatbotConversation->getKey(),
            'history'             => $history ? ChatbotHistoryResource::make($history)->jsonSerialize() : null,
            'chatbotConversation' => ChatbotConversationForAblyResource::make($chatbotConversation)->jsonSerialize(),
        ]);
    }
}
