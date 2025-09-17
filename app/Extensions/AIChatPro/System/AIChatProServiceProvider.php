<?php

declare(strict_types=1);

namespace App\Extensions\AIChatPro\System;

use App\Domains\Marketplace\Contracts\UninstallExtensionServiceProviderInterface;
use App\Extensions\AIChatPro\System\Http\Controllers\AIChatProController;
use App\Extensions\AIChatPro\System\Http\Controllers\AIChatProSettingsController;
use App\Http\Controllers\AIChatController;
use App\Http\Controllers\OpenAi\GeneratorController;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/**
 * Author: MagicAI Team <info@liquid-themes.com>
 *
 * @note When you create a new service provider, make sure to add it to the "MarketplaceServiceProvider". Otherwise, your Laravel application won’t recognize this provider, and the related functions won’t work properly.
 * @note If you want to perform a specific action when an extension is uninstalled, you can use the UninstallExtensionServiceProviderInterface. By implementing this interface, you can define custom operations that will be triggered during the uninstallation of the extension.
 */
class AIChatProServiceProvider extends ServiceProvider implements UninstallExtensionServiceProviderInterface
{
    public function register(): void {}

    public function boot(Kernel $kernel): void
    {
        $this->registerTranslations()
            ->registerViews()
            ->registerRoutes()
            ->registerMigrations()
            ->publishAssets()
            ->registerComponents();

    }

    public function registerComponents(): static
    {
        //        $this->loadViewComponentsAs('example', []);

        return $this;
    }

    public function publishAssets(): static
    {
        $this->publishes([
            //            __DIR__ . '/../resources/assets/js' => public_path('vendor/example/js'),
            //            __DIR__ . '/../resources/assets/images' => public_path('vendor/example/images'),
        ], 'extension');

        return $this;
    }

    protected function registerTranslations(): static
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'ai-chat-pro');

        return $this;
    }

    public function registerViews(): static
    {
        $this->loadViewsFrom([__DIR__ . '/../resources/views'], 'ai-chat-pro');

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
                'prefix'     => LaravelLocalization::setLocale(),
                'middleware' => ['web', 'auth', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
            ], function (Router $router) {
                $router
                    ->controller(AIChatProSettingsController::class)
                    ->prefix('dashboard/admin/openai/chat/pro')
                    ->name('dashboard.admin.openai.chat.pro.')
                    ->middleware('admin')
                    ->group(function (Router $router) {
                        $router->get('settings', 'index')->name('settings');
                        $router->post('settings', 'update')->name('update');
                    });

                $router
                    ->controller(AIChatProController::class)
                    ->prefix('dashboard/user/openai/chat/pro')
                    ->name('dashboard.user.openai.chat.pro.')
                    ->group(function (Router $router) {
                        $router->get('chat/{slug?}', 'index')->name('index');
                    });
            });

        $this->router()
            ->group([
                'prefix'     => LaravelLocalization::setLocale(),
                'middleware' => ['web', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
            ], function (Router $router) {
                $router
                    ->controller(AIChatProController::class)
                    ->group(function (Router $router) {
                        $router->get('/chat/{slug?}', 'index')->name('chat.pro');
                    });
                $router
                    ->controller(AIChatController::class)
                    ->group(function (Router $router) {
                        $router->post('/dashboard/user/openai/chat/start-new-chat', 'startNewChat');
                    });
                $router
                    ->controller(AIChatController::class)
                    ->group(function (Router $router) {
                        $router->post('dashboard/user/generator/generate-stream', [GeneratorController::class, 'buildStreamedOutput'])->name('stream.generate')->middleware('surveyMiddleware');
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
