<?php

declare(strict_types=1);

namespace LaravelLang\MoonShine\Plugins;

use LaravelLang\Publisher\Plugins\Plugin;

class V3 extends Plugin
{
    protected ?string $vendor = 'moonshine/moonshine';

    protected string $version = '^3.0';

    public function files(): array
    {
        return [
            'moonshine/3.x/auth.php'       => 'vendor/moonshine/{locale}/auth.php',
            'moonshine/3.x/pagination.php' => 'vendor/moonshine/{locale}/pagination.php',
            'moonshine/3.x/ui.php'         => 'vendor/moonshine/{locale}/ui.php',
            'moonshine/3.x/validation.php' => 'vendor/moonshine/{locale}/validation.php',
        ];
    }
}
