<?php

declare(strict_types=1);

namespace App\Extensions\MarketingBot\System;

use App\Domains\Marketplace\Contracts\UninstallExtensionServiceProviderInterface;
use App\Extensions\MarketingBot\System\Console\Commands\RunTelegramCampaignCommand;
use App\Extensions\MarketingBot\System\Console\Commands\RunWhatsappCampaignCommand;
use App\Extensions\MarketingBot\System\Http\Controllers\Campaign\GenerateController;
use App\Extensions\MarketingBot\System\Http\Controllers\Campaign\TelegramCampaignController;
use App\Extensions\MarketingBot\System\Http\Controllers\Campaign\WhatsappCampaignController;
use App\Extensions\MarketingBot\System\Http\Controllers\InboxController;
use App\Extensions\MarketingBot\System\Http\Controllers\MarketingBotTrainController;
use App\Extensions\MarketingBot\System\Http\Controllers\MarketingDashboardController;
use App\Extensions\MarketingBot\System\Http\Controllers\Setting\TelegramSettingController;
use App\Extensions\MarketingBot\System\Http\Controllers\Setting\ViewSettingController;
use App\Extensions\MarketingBot\System\Http\Controllers\Setting\WhatsappSettingController;
use App\Extensions\MarketingBot\System\Http\Controllers\Telegram\TelegramGroupController;
use App\Extensions\MarketingBot\System\Http\Controllers\Telegram\TelegramSubscriberController;
use App\Extensions\MarketingBot\System\Http\Controllers\Webhook\TelegramWebhookController;
use App\Extensions\MarketingBot\System\Http\Controllers\Webhook\WhatsappWebhookController;
use App\Extensions\MarketingBot\System\Http\Controllers\Whatsapp\ContactController;
use App\Extensions\MarketingBot\System\Http\Controllers\Whatsapp\ContactListController;
use App\Extensions\MarketingBot\System\Http\Controllers\Whatsapp\SegmentController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MarketingBotServiceProvider extends ServiceProvider implements UninstallExtensionServiceProviderInterface
{
    public function register(): void
    {
        $this->registerConfig();
    }

    public function boot(Kernel $kernel): void
    {
        $this->registerTranslations()
            ->registerViews()
            ->registerRoutes()
            ->registerMigrations()
            ->publishAssets()
            ->registerCommand()
            ->registerComponents();
    }

    public function registerCommand(): static
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RunWhatsappCampaignCommand::class,
                RunTelegramCampaignCommand::class,
            ]);

            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                $schedule->command('app:run-whatsapp-campaign')->everyTwoMinutes();
                $schedule->command('app:run-telegram-campaign')->everyTwoMinutes();
            });
        }

        return $this;
    }

    public function registerComponents(): static
    {
        //        $this->loadViewComponentsAs('example', []);

        return $this;
    }

    public function publishAssets(): static
    {
        $this->publishes([
            __DIR__ . '/../resources/assets/images' => public_path('vendor/marketing-bot/images'),
        ], 'extension');

        return $this;
    }

    public function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/marketing-bot.php', 'marketing-bot');

        return $this;
    }

    protected function registerTranslations(): static
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'marketing-bot');

        return $this;
    }

    public function registerViews(): static
    {
        $this->loadViewsFrom([__DIR__ . '/../resources/views'], 'marketing-bot');

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
                'middleware' => 'api',
                'prefix'     => 'api/marketing-bot',
                'as'         => 'api.marketing-bot.',
            ], function (Router $router) {
                $router->any('whatsapp/{whatsappChannel}/webhook', WhatsappWebhookController::class)->name('whatsapp.webhook');
                $router->any('telegram/webhook/{token}', TelegramWebhookController::class)->name('telegram.webhook');
            });
        $this->router()
            ->group([
                'controller' => InboxController::class,
                'prefix'     => 'dashboard/user/marketing-bot/inbox',
                'as'         => 'dashboard.user.marketing-bot.inbox.',
                'middleware' => ['web', 'auth'],
            ], function (Router $router) {
                $router->get('', 'index')->name('index');
                $router->post('conversations/name', 'name')->name('conversations.name.update');
                $router->get('conversations', 'conversations')->name('conversations');
                $router->post('conversations/search', 'searchConversation')->name('conversations.search');
                $router->get('conversations-with-paginate', 'conversationsWithPaginate')->name('conversations.with.paginate');
                $router->get('history', 'history')->name('history');
                $router->post('history', 'store');
                $router->delete('destroy', 'destroy')->name('destroy');
                $router->get('notification/count', 'notification')->name('notification.count');
            });
        $this->router()
            ->group([
                'middleware' => [
                    'web', 'auth',
                ],
                'prefix'     => 'dashboard/user/marketing-bot',
                'as'         => 'dashboard.user.marketing-bot.',
            ], function (Router $router) {
                $router
                    ->controller(MarketingBotTrainController::class)
                    ->prefix('train')
                    ->name('train.')
                    ->group(function (Router $route) {
                        $route->get('data', 'trainData')->name('data');
                        $route->post('delete-embedding', 'deleteEmbedding')->name('delete');
                        $route->post('generate-embedding', 'generateEmbedding')->name('generate.embedding');
                        $route->get('{marketingCampaign}', 'train')->name('index');
                        $route->post('url', 'trainUrl')->name('url');
                        $route->post('file', 'trainFile')->name('file');
                        $route->post('text', 'trainText')->name('text');
                        $route->post('qa', 'trainQa')->name('qa');
                    });

                $router->get('', MarketingDashboardController::class)->name('dashboard');

                $router->post('image/upload', [GenerateController::class, 'image'])->name('image.upload');
                $router->post('generate/content', [GenerateController::class, 'generateContent'])->name('generate.content');

                $router->resource('telegram-campaign', TelegramCampaignController::class);
                $router->resource('whatsapp-campaign', WhatsappCampaignController::class);

                $router->resource('contact', ContactController::class)->except('show', 'create');
                $router->resource('segment', SegmentController::class)->except('show', 'create');
                $router->resource('contact-list', ContactListController::class)->except('show');

                $router->resource('telegram-group', TelegramGroupController::class)
                    ->only(['index', 'destroy']);

                $router->resource('telegram-subscriber', TelegramSubscriberController::class)
                    ->only(['index', 'destroy']);
            });
        $this->router()
            ->group([
                'middleware' => ['web', 'auth'],
                'prefix'     => 'dashboard/user/marketing-bot/settings',
                'as'         => 'dashboard.user.marketing-bot.settings.',
            ], function (Router $router) {
                $router->get('', ViewSettingController::class)->name('index');
                $router->post('telegram', TelegramSettingController::class)->name('telegram');
                $router->post('whatsapp', WhatsappSettingController::class)->name('whatsapp');
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
