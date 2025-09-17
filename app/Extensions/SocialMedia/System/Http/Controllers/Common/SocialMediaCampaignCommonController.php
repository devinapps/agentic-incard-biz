<?php

namespace App\Extensions\SocialMedia\System\Http\Controllers\Common;

use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Extensions\SocialMedia\System\Models\SocialMediaCampaign;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenAI\Laravel\Facades\OpenAI;

class SocialMediaCampaignCommonController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'data' => SocialMediaCampaign::query()
                ->select('id', 'name', 'target_audience')
                ->where('user_id', Auth::id())->get(),
        ]);
    }

    public function generate(Request $request)
    {
        Helper::setOpenAiKey();

        $driver = Entity::driver(EntityEnum::tryFrom(Setting::getCache()->openai_default_model));
        $driver->redirectIfNoCreditBalance();

        try {
            $campaign_name = $request->campaign_name ?? 'any';
            // $productIds = $repeat->productIds?? 0; later we can add products to improve the output..
            $completion = OpenAI::chat()->create([
                'model'    => Setting::getCache()->openai_default_model,
                'messages' => [[
                    'role'    => 'user',
                    'content' => "Generate only a list of target audience attributes, including demographics, interests, and pain points, for the purpose of $campaign_name campaign. Must result as array json data only. Result format is [attribute1, attribute2, ..., attributen]. Ensure that the result does not contain backticks (\`) or the string \"```json\".",
                ]],
            ]);
            $response = $completion['choices'][0]['message']['content'];
            $driver->input($response)->calculateCredit()->decreaseCredit();

            return response()->json(['result' => $completion['choices'][0]['message']['content']]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
