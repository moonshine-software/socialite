<?php

declare(strict_types=1);

namespace MoonShine\Socialite\Http\Controllers;

use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\Facades\Socialite;

use Illuminate\Http\RedirectResponse;
use MoonShine\Socialite\Exceptions\AuthException;
use MoonShine\Laravel\Http\Controllers\MoonShineController;
use MoonShine\Laravel\Pages\ProfilePage;
use MoonShine\Support\Enums\ToastType;

use MoonShine\Socialite\Models\MoonshineSocialite;

class SocialiteController extends MoonShineController
{
    /**
     * @throws AuthException
     */
    public function redirect(string $driver)
    {
        $this->ensureSocialiteIsInstalled();

        if (! $this->hasDriver($driver)) {
            throw AuthException::driverNotFound();
        }

        return Socialite::driver($driver)
            ->redirect();
    }

    protected function ensureSocialiteIsInstalled(): void
    {
        if (class_exists(Socialite::class)) {
            return;
        }

        throw new \RuntimeException(
            'Please install the Socialite: laravel/socialite'
        );
    }

    protected function hasDriver(string $driver): bool
    {
        return isset($this->drivers()[$driver]);
    }

    protected function drivers(): array
    {
        return config('moonshine-socialite.drivers', []);
    }

    /**
     * @throws AuthException
     */
    public function callback(string $driver): RedirectResponse
    {
        $this->ensureSocialiteIsInstalled();

        if (! $this->hasDriver($driver)) {
            throw AuthException::driverNotFound();
        }

        $socialiteUser = Socialite::driver($driver)->user();

        $account = MoonshineSocialite::query()
            ->where('driver', $driver)
            ->where('identity', $socialiteUser->getId())
            ->first();

        if ($this->auth()->check()) {
            return $this->bindAccount($socialiteUser, $driver, $account);
        }

        if (! $account) {
            $this->toast(
                __('moonshine::auth.failed'),
                ToastType::ERROR
            );

            return redirect(moonshineRouter()->to('login'))->withErrors([
                'username' => __('moonshine::auth.failed'),
            ]);
        }

        $this->auth()->loginUsingId($account->moonshine_user_id);

        return redirect(
            moonshineRouter()->getEndpoints()->home()
        );
    }

    protected function bindAccount(
        User $socialiteUser,
        string $driver,
        ?MoonshineSocialite $account
    ): RedirectResponse {
        if ($account instanceof MoonshineSocialite) {
            $this->toast(
                __('moonshine-socialite::socialite.link_exists')
            );
        } else {
            MoonshineSocialite::query()->create([
                'moonshine_user_id' => $this->auth()->id(),
                'driver' => $driver,
                'identity' => $socialiteUser->getId(),
            ]);

            $this->toast(
                __('moonshine-socialite::socialite.link_success'),
                ToastType::SUCCESS
            );
        }

        return toPage(
            moonshineConfig()->getPage('profile', ProfilePage::class),
            redirect: true
        );
    }
}
