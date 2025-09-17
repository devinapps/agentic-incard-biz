<?php

declare(strict_types=1);

namespace App\Extensions\AIChatPro\System\Services;

use App\Http\Controllers\AIController;
use Illuminate\Support\Facades\Log;
use JsonException;

class AiChatProService
{
    private static function availableTools(): array
    {
        return [
            [
                'type'        => 'function',
                'name'        => 'generate_image',
                'description' => 'Generate an image based on the given prompt.',
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'prompt' => [
                            'type'        => 'string',
                            'description' => 'The text prompt to generate the image.',
                        ],
                    ],
                    'required' => ['prompt'],
                ],
            ],
            // add more functions here
        ];
    }

    public static function tools(): array
    {
        return self::availableTools();
    }

    public static function callFunction(?string $functionName, ?string $argsString): ?string
    {
        return match ($functionName) {
            'generate_image' => self::generateImage($argsString),
            default          => null,
        };
    }

    public static function generateImage(?string $argsString): string
    {
        try {
            $args = json_decode($argsString, true, 512, JSON_THROW_ON_ERROR);
            if (! isset($args['prompt'])) {
                throw new JsonException('Invalid arguments: prompt is required');
            }

            $prompt = $args['prompt'];

            return app(AIController::class)->generateImageWithOpenAI($prompt);
        } catch (JsonException $e) {
            Log::error($e->getMessage());

            return '';
        }
    }
}
