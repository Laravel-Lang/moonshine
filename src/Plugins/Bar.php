<?php

declare(strict_types=1);

namespace YourNamespace\Translations\Plugins;

use LaravelLang\Publisher\Plugins\Plugin;

class Bar extends Plugin
{
    protected ?string $vendor = 'laravel/framework';

    protected string $version = '^10.0';

    public function files(): array
    {
        return [
            'packages/bar.json' => 'custom/{locale}.json',
        ];
    }
}
