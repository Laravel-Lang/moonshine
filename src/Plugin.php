<?php

declare(strict_types=1);

namespace LaravelLang\MoonShine;

use LaravelLang\MoonShine\Plugins\V3;
use LaravelLang\Publisher\Plugins\Provider;

class Plugin extends Provider
{
    protected ?string $package_name = 'moonshine/moonshine';

    protected string $base_path = __DIR__ . '/../';

    protected array $plugins = [
        V3::class,
    ];
}
