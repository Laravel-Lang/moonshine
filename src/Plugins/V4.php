<?php

declare(strict_types=1);

namespace LaravelLang\MoonShine\Plugins;

use LaravelLang\Publisher\Plugins\Plugin;

class V4 extends Plugin
{
    protected ?string $vendor = 'moonshine/moonshine';

    protected string $version = '^4.0';

    public function files(): array
    {
        return [
            'moonshine/4.x/auth.php'       => 'vendor/moonshine/{locale}/auth.php',
            'moonshine/4.x/pagination.php' => 'vendor/moonshine/{locale}/pagination.php',
            'moonshine/4.x/ui.php'         => 'vendor/moonshine/{locale}/ui.php',
            'moonshine/4.x/validation.php' => 'vendor/moonshine/{locale}/validation.php',
        ];
    }
}
