<?php

namespace App\Extensions\MarketingBot\System\Http\Controllers\Setting;

use App\Extensions\MarketingBot\System\Models\Whatsapp\WhatsappChannel;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WhatsappSettingController extends Controller
{
    public function __invoke(Request $request)
    {
        if (Helper::appIsDemo()) {
            return back()->with([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $data = $request->validate([
            'whatsapp_sid'           => 'required|string',
            'whatsapp_token'         => 'required|string',
            'whatsapp_phone'         => 'required|string',
            'whatsapp_sandbox_phone' => 'nullable|string',
            'whatsapp_environment'   => 'required',
        ]);

        WhatsappChannel::query()->updateOrCreate([
            'user_id' => auth()->id(),
        ], $data ?? []);

        return back()->with([
            'message' => __('WhatsApp settings updated successfully.'),
            'type'    => 'success',
        ]);
    }
}
