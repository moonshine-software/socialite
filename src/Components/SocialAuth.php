<?php

declare(strict_types=1);

namespace MoonShine\Socialite\Components;

use Illuminate\Support\Collection;
use MoonShine\UI\Components\MoonShineComponent;

/**
 * @method static static make(bool $profileMode = false)
 */
final class SocialAuth extends MoonShineComponent
{
    protected string $view = 'moonshine-socialite::components.social-auth';

    public Collection $attached;

    public array $drivers;

    protected array $translates = [
        'linked' => 'moonshine-socialite::socialite.linked_socialite',
        'title' => 'moonshine-socialite::socialite.link_socialite',
    ];

    public function __construct(
        public bool $profileMode = false
    ) {
        parent::__construct();
    }

    protected function prepareBeforeRender(): void
    {
        $this->drivers = collect(
            config('moonshine-socialite.drivers', [])
        )
            ->map(fn (string $src, string $name): array => [
                'name' => $name,
                'src' => moonshineAssets()->getAsset($src),
                'route' => moonshineRouter()->to('socialite.redirect', [
                    'driver' => $name,
                ]),
            ])
            ->toArray();

        $this->attached = auth()->user()?->moonshineSocialites ?? collect();
    }
}
