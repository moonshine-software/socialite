<?php

declare(strict_types=1);

namespace MoonShine\Socialite\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MoonShine\Laravel\MoonShineAuth;
use MoonShine\Socialite\Models\MoonshineSocialite;

/**
 * @mixin Model
 */
trait HasMoonShineSocialite
{
    public function moonshineSocialites(): HasMany
    {
        return $this->hasMany(
            MoonshineSocialite::class,
            'moonshine_user_id',
            MoonShineAuth::getModel()?->getKeyName() ?? 'id'
        );
    }
}
