<?php

namespace App\Extensions\MarketingBot\System\Http\Controllers\Campaign;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class GenerateController extends Controller
{
    public function generateContent(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:5000',
        ]);

        $prompt = $request->input('prompt');

        try {
            Helper::setOpenAiKey();

            $content = OpenAI::chat()->create([
                'model'    => Helper::setting('openai_default_model'),
                'messages' => [[
                    'role'    => 'user',
                    'content' => "You are an experienced copywriter and digital marketing expert. The following message from the client (prompt) requests a short and impactful campaign message for promoting a product or service. Based on the platform specified by the client (WhatsApp or Telegram), generate an engaging and conversion-focused message.Client Prompt:  $prompt",
                ]],
            ]);

            if (isset($content['choices'][0]['message']['content'])) {
                $content = $content['choices'][0]['message']['content'];

                return response()->json([
                    'status'  => 'success',
                    'type'    => 'success',
                    'message' => 'Content generated successfully.',
                    'content' => $content,
                ]);
            }

            return response()->json([
                'status'  => 'error',
                'type'    => 'error',
                'message' => 'No content generated.',
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'type'    => 'error',
                'message' => 'An error occurred while generating content: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function image(Request $request): JsonResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'type'    => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ], 400);
        }

        $request->validate(['upload_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:8192']);

        $path = $request->file('upload_image')->store('social-media', 'public');

        return response()->json([
            'image_path' => '/uploads/' . $path,
            'url'        => url('uploads/' . $path),
        ]);
    }
}
