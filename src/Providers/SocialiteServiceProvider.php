<?php

declare(strict_types=1);

namespace MoonShine\Socialite\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use MoonShine\Laravel\Layouts\LoginLayout;
use MoonShine\Laravel\Pages\ProfilePage;
use MoonShine\Socialite\Components\SocialAuth;

final class SocialiteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/moonshine-socialite.php' => config_path('moonshine-socialite.php'),
        ]);

        $this->loadRoutesFrom(__DIR__ . '/../../routes/socialite.php');

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'moonshine-socialite');

        $this->publishes([
            __DIR__ . '/../../lang' => $this->app->langPath('vendor/moonshine-socialite'),
        ]);

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'moonshine-socialite');

        Blade::componentNamespace('MoonShine\\Socialite\\Components', 'moonshine-socialite');

        LoginLayout::pushComponent(
            static fn () => SocialAuth::make()
        );

        ProfilePage::pushComponent(
            static fn () => SocialAuth::make(profileMode: true)
        );
    }
}
