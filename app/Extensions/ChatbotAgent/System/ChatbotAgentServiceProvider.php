<?php

declare(strict_types=1);

namespace App\Extensions\ChatbotAgent\System;

use App\Domains\Marketplace\Contracts\UninstallExtensionServiceProviderInterface;
use App\Extensions\ChatbotAgent\System\Http\Controllers\AblyController;
use App\Extensions\ChatbotAgent\System\Http\Controllers\ChatbotAgentController;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ChatbotAgentServiceProvider extends ServiceProvider implements UninstallExtensionServiceProviderInterface
{
    public function boot(Kernel $kernel): void
    {
        $this->registerTranslations()
            ->registerViews()
            ->registerRoutes()
            ->registerMigrations()
            ->publishAssets();

    }

    public function publishAssets(): static
    {
        $this->publishes([
            //            __DIR__ . '/../resources/assets/js' => public_path('vendor/chatbot-agent/js'),
            //            __DIR__ . '/../resources/assets/images' => public_path('vendor/chatbot-agent/images'),
        ], 'extension');

        return $this;
    }

    protected function registerTranslations(): static
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'chatbot-agent');

        return $this;
    }

    public function registerViews(): static
    {
        $this->loadViewsFrom([__DIR__ . '/../resources/views'], 'chatbot-agent');

        return $this;
    }

    public function registerMigrations(): static
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        return $this;
    }

    private function registerRoutes(): static
    {

        $this->router()
            ->group([
                'middleware' => ['web', 'auth'],
            ], function (Router $router) {
                $router
                    ->controller(ChatbotAgentController::class)
                    ->prefix('dashboard/chatbot-agent')
                    ->name('dashboard.chatbot-agent.')
                    ->group(function (Router $router) {
                        $router->get('', 'index')->name('index');
                        $router->post('conversations/name', 'name')->name('conversations.name.update');
                        $router->get('conversations', 'conversations')->name('conversations');
                        $router->post('conversations/search', 'searchConversation')->name('conversations.search');
                        $router->get('conversations-with-paginate', 'conversationsWithPaginate')->name('conversations.with.paginate');
                        $router->get('history', 'history')->name('history');
                        $router->post('history', 'store');
                        $router->delete('destory', 'destory')->name('destroy');
                    });

                $router
                    ->controller(AblyController::class)
                    ->prefix('dashboard/admin/settings')
                    ->name('dashboard.admin.settings.')
                    ->group(function (Router $router) {
                        $router->get('ably', 'index')->name('ably');
                        $router->post('ably', 'update')->name('ably.update');
                    });
            });

        return $this;
    }

    private function router(): Router|Route
    {
        return $this->app['router'];
    }

    public static function uninstall(): void
    {
        // TODO: Implement uninstall() method.
    }
}
