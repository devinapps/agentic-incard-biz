<?php

namespace App\Extensions\MarketingBot\System\Http\Controllers\Webhook;

use App\Extensions\MarketingBot\System\Models\Whatsapp\WhatsappChannel;
use App\Extensions\MarketingBot\System\Services\Whatsapp\WhatsappWebhookService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsappWebhookController extends Controller
{
    public function __construct(
        public WhatsappWebhookService $whatsappWebhookService,

    ) {}

    public function __invoke(Request $request, WhatsappChannel $whatsappChannel)
    {
        $this->whatsappWebhookService->handle($request, $whatsappChannel);

        Log::info('test', [
            'request'         => $request->all(),

            'whatsappChannel' => $whatsappChannel->toArray(),
        ]);
    }
}
