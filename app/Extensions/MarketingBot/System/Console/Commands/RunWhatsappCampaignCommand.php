<?php

namespace App\Extensions\MarketingBot\System\Console\Commands;

use App\Extensions\MarketingBot\System\Enums\CampaignStatus;
use App\Extensions\MarketingBot\System\Enums\CampaignType;
use App\Extensions\MarketingBot\System\Models\MarketingCampaign;
use App\Extensions\MarketingBot\System\Services\Whatsapp\WhatsappSenderService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunWhatsappCampaignCommand extends Command
{
    protected $signature = 'app:run-whatsapp-campaign';

    protected $description = 'Run a new Whatsapp campaign';

    public function handle()
    {
        Log::info(">>> Running Whatsapp campaign");

        $now = now();

        $whatsappService = app(WhatsappSenderService::class);

        $campaigns = MarketingCampaign::query()
            ->where('type', CampaignType::whatsapp)
            ->where('status', CampaignStatus::scheduled)
            ->where('scheduled_at', '<=', $now)
            ->get();


        Log::info(">>> campaigns: " . json_encode($campaigns));

        $campaigns->map(function (MarketingCampaign $campaign) use ($whatsappService) {
            try {
                Log::info(">>> Processing campaign: " . $campaign->id);
                
                $whatsappService
                    ->setMarketingCampaign($campaign)
                    ->send();
                    
                Log::info(">>> Campaign processed successfully: " . $campaign->id);
            } catch (Exception $e) {
                Log::error('Error running Whatsapp campaign: ' . $e->getMessage(), [
                    'campaign_id' => $campaign->id,
                    'user_id' => $campaign->user_id,
                    'error_code' => $e->getCode(),
                    'trace' => $e->getTraceAsString(),
                ]);
                
                // Update campaign status to failed if needed
                $campaign->update(['status' => CampaignStatus::failed]);
            }
        });
    }
}
