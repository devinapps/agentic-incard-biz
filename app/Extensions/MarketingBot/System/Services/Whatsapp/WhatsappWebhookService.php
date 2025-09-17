<?php

namespace App\Extensions\MarketingBot\System\Services\Whatsapp;

use App\Extensions\MarketingBot\System\Models\MarketingConversation;
use App\Extensions\MarketingBot\System\Models\MarketingMessageHistory;
use App\Extensions\MarketingBot\System\Models\Whatsapp\WhatsappChannel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class WhatsappWebhookService
{
    public function handle(Request $request, WhatsappChannel $whatsappChannel): void
    {
        $marketingConversation = $this->updateOrCreateMarketingConversation($request, $whatsappChannel);

        if ($request->input('MessageType') === 'text' && ! empty($request->input('Body'))) {
            MarketingMessageHistory::query()->create([
                'conversation_id' => $marketingConversation->getKey(),
                'message_id'      => $request->input('SmsSid'),
                'model'           => null,
                'role'            => 'user',
                'message'         => $request->input('Body'),
                'type'            => 'default',
                'message_type'    => 'text',
                'content_type'    => 'text',
                'created_at'      => now(),
            ]);

            app(WhatsappReplyService::class)
                ->setWhatsappChannel($whatsappChannel)
                ->sendReply($request, $marketingConversation);
        }
    }

    public function updateOrCreateMarketingConversation(Request $request, WhatsappChannel $whatsappChannel): Model|Builder
    {
        return MarketingConversation::query()
            ->firstOrCreate([
                'user_id'             => $whatsappChannel->getAttribute('user_id'),
                'type'                => 'whatsapp',
                'session_id'          => $request->input('WaId'),
                'whatsapp_channel_id' => $whatsappChannel->getKey(),
            ], [
                'conversation_name' => $request->input('WaId', 'Number'),
                'customer_payload'  => [
                    'AccountSid' => $request->input('AccountSid'),
                    'From'       => $request->input('From'),
                ],
            ]);
    }
}
