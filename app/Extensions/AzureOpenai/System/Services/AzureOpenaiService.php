<?php

declare(strict_types=1);

namespace App\Extensions\AzureOpenai\System\Services;

use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Models\Usage;
use App\Models\UserOpenaiChat;
use OpenAI;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AzureOpenaiService
{
    public static function azureOpenaiStream(string $chat_bot, $history, $main_message, $chat_type, $contain_images): ?StreamedResponse
    {
        ini_set('max_execution_time', 300);
        set_time_limit(300);

        $apiKey = setting('azure_api_key');
        $domain = setting('azure_domain');
        $apiVersion = setting('azure_api_version');
        $deployedModels = setting('deployed_models');
        $driver = Entity::driver(EntityEnum::from($chat_bot));

        return response()->stream(function () use ($driver, $history, $contain_images, $main_message, $apiKey, $domain, $apiVersion, $deployedModels) {
            $total_used_tokens = 0;
            $output = '';
            $responsedText = '';

            echo "event: message\n";
            echo 'data: ' . $main_message->id . "\n\n";

            if (empty($apiKey) || empty($domain) || empty($apiVersion) || empty($deployedModels)) {
                echo PHP_EOL;
                echo "event: data\n";
                echo 'data: ' . __('An error occurred while generating the response.') . "\n\n";
                echo "\n\n";
                flush();
                echo "event: stop\n";
                echo 'data: [DONE]';
                echo "\n\n";
                flush();

                return null;
            }

            if (! $driver->hasCreditBalance()) {
                echo PHP_EOL;
                echo "event: data\n";
                echo 'data: ' . __('You have no credits left. Please buy more credits to continue.');
                echo "\n\n";
                flush();
                echo "event: stop\n";
                echo 'data: [DONE]';
                echo "\n\n";
                flush();

                return null;
            }

            $chat_id = $main_message->user_openai_chat_id;
            $chat = UserOpenaiChat::whereId($chat_id)->first();
            $client = OpenAI::factory()
                ->withBaseUri($domain . '.openai.azure.com/openai/deployments/' . $deployedModels)
                ->withHttpHeader('api-key', $apiKey)
                ->withQueryParam('api-version', $apiVersion)
                ->make();

            $model = $driver->enum()->value;
            $options = [
                'model'             => $model,
                'messages'          => $history,
                'stream'            => true,
            ];

            if (! in_array($model, [EntityEnum::GPT_4_O_MINI_SEARCH_PREVIEW->value, EntityEnum::GPT_4_O_SEARCH_PREVIEW->value], true)) {
                $options['temperature'] = 1.0;
                $options['frequency_penalty'] = 0;
                $options['presence_penalty'] = 0;
            }

            if ($contain_images) {
                $options['max_tokens'] = 2000;
                $options['model'] = EntityEnum::GPT_4_O;
            }

            try {
                $stream = $client->chat()->createStreamed($options);
            } catch (Throwable $e) {
                echo PHP_EOL;
                echo "event: data\n";
                echo 'data: ' . $e->getMessage() . "\n\n";
                echo "\n\n";
                flush();
                echo "event: stop\n";
                echo 'data: [DONE]';
                echo "\n\n";
                flush();

                return null;
            }

            foreach ($stream as $response) {
                if (isset($response->choices[0]->delta->content)) {
                    $text = $response->choices[0]->delta->content;
                    $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $text);
                    $output .= $messageFix;
                    $responsedText .= $text;
                    $total_used_tokens += countWords($text);
                    if (connection_aborted()) {
                        break;
                    }
                    echo PHP_EOL;
                    echo "event: data\n";
                    echo 'data: ' . $messageFix;
                    echo "\n\n";
                    flush();
                }
            }
            echo "event: stop\n";
            echo 'data: [DONE]';
            echo "\n\n";
            flush();

            $main_message->response = $responsedText;
            $main_message->output = $output;
            $main_message->credits = $total_used_tokens;
            $main_message->words = $total_used_tokens;
            $main_message->save();
            $chat->total_credits += $total_used_tokens;
            $chat->save();

            $driver->input($responsedText)->calculateCredit()->decreaseCredit();
            Usage::getSingle()->updateWordCounts($driver->calculate());
        }, 200, [
            'Content-Type'  => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection'    => 'keep-alive',
        ]);
    }
}
