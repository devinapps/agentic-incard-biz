<?php

namespace App\Extensions\ChatbotAgent\System\Http\Controllers;

use App\Extensions\Chatbot\System\Http\Resources\Admin\ChatbotConversationResource;
use App\Extensions\Chatbot\System\Http\Resources\Api\ChatbotHistoryResource;
use App\Extensions\Chatbot\System\Models\ChatbotChannel;
use App\Extensions\Chatbot\System\Models\ChatbotConversation;
use App\Extensions\Chatbot\System\Models\ChatbotHistory;
use App\Extensions\Chatbot\System\Services\ChatbotService;
use App\Extensions\ChatbotAgent\System\Services\ChatbotForFrameEventAbly;
use App\Extensions\ChatbotTelegram\System\Services\Telegram\TelegramService;
use App\Extensions\ChatbotWhatsapp\System\Services\Twillio\TwilioWhatsappService;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Throwable;

class ChatbotAgentController extends Controller
{
    public function __construct(public ChatbotService $service) {}

    public function index(Request $request)
    {
        return view('chatbot-agent::index');
    }

    public function name(Request $request): ChatbotConversationResource|JsonResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'type'    => 'error',
                'message' => 'This feature is disabled in Demo version.',
            ], 403);
        }

        $request->validate([
            'conversation_id'   => 'required|exists:ext_chatbot_conversations,id',
            'conversation_name' => 'required|string',
        ]);

        $conversation = ChatbotConversation::query()->find($request['conversation_id']);

        $conversation->update(['conversation_name' => $request['conversation_name']]);

        return ChatbotConversationResource::make($conversation);
    }

    public function store(Request $request): ChatbotHistoryResource|JsonResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'type'    => 'error',
                'message' => 'This feature is disabled in Demo version.',
            ], 403);
        }

        $request->validate([
            'conversation_id' => 'required|integer|exists:ext_chatbot_conversations,id',
            'message'         => 'required|string',
        ]);

        $chatbotConversation = ChatbotConversation::query()
            ->with('chatbot')
            ->find($request['conversation_id']);

        $history = ChatbotHistory::query()->create([
            'user_id'         => Auth::id(),
            'chatbot_id'      => $chatbotConversation->getAttribute('chatbot_id'),
            'conversation_id' => $chatbotConversation->getAttribute('id'),
            'model'           => $chatbotConversation->chatbot->getAttribute('ai_model'),
            'role'            => 'assistant',
            'message'         => $request['message'],
            'created_at'      => now(),
        ]);

        try {
            if ($chatbotConversation->getAttribute('chatbot_channel_id')) {
                /**
                 * @var ChatbotChannel $chatbotChannel
                 */
                $chatbotChannel = $chatbotConversation->getAttribute('chatbotChannel');

                if ($chatbotChannel) {
                    if ($chatbotChannel?->channel === 'whatsapp' && $chatbotConversation->getAttribute('customer_channel_id')) {
                        app(TwilioWhatsappService::class)
                            ->setChatbotChannel($chatbotChannel)
                            ->sendText(
                                $request['message'],
                                $chatbotConversation->getAttribute('customer_channel_id')
                            );
                    }
                    if ($chatbotChannel?->channel === 'telegram') {
                        app(TelegramService::class)
                            ->setChannel($chatbotChannel)
                            ->sendText(
                                $request['message'],
                                $chatbotConversation->getAttribute('customer_channel_id')
                            );
                    }
                }

            } else {
                ChatbotForFrameEventAbly::dispatch($history, $chatbotConversation->sessionId());
            }
        } catch (Exception $e) {
        }

        return ChatbotHistoryResource::make($history);
    }

    public function conversations(Request $request): AnonymousResourceCollection
    {
        $chatbots = $request->user()->externalChatbots->pluck('id')->toArray();

        $conversations = $this->service->agentConversations($chatbots, 'updated_at');

        return ChatbotConversationResource::collection($conversations);
    }

    public function conversationsWithPaginate(Request $request): AnonymousResourceCollection
    {
        $chatbots = $request->user()->externalChatbots->pluck('id')->toArray();

        $conversations = $this->service->agentConversationsWithPaginate($chatbots);

        return ChatbotConversationResource::collection($conversations);
    }

    public function history(Request $request): AnonymousResourceCollection
    {
        $request->validate(['conversation_id' => 'required|integer|exists:ext_chatbot_conversations,id']);

        ChatbotHistory::query()->where('conversation_id', request('conversation_id'))->update(['read_at' => now()]);

        $conversation = ChatbotConversation::query()->find(request('conversation_id'));

        return ChatbotHistoryResource::collection($conversation->getAttribute('histories'));
    }

    public function searchConversation(Request $request)
    {
        $chatbots = $request->user()->externalChatbots->pluck('id')->toArray();

        $conversations = $this->service->agentConversationsBySearch($chatbots, $request->search ?? '');

        return ChatbotConversationResource::collection($conversations);
    }

    public function destory(Request $request): JsonResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'type'    => 'error',
                'message' => 'This feature is disabled in Demo version.',
            ], 403);
        }

        try {
            $request->validate(['conversation_id' => 'required|integer|exists:ext_chatbot_conversations,id']);

            ChatbotConversation::query()->find(request('conversation_id'))->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Successfully removed conversation',
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'status'       => 'error',
                'message'      => 'Something went wrong',
                'errorMessage' => $th->getMessage(),
            ]);
        }

    }
}
