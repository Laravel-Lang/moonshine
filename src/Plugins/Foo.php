<?php

declare(strict_types=1);

namespace YourNamespace\Translations\Plugins;

use LaravelLang\Publisher\Plugins\Plugin;

class Foo extends Plugin
{
    protected ?string $vendor = 'laravel/framework';

    protected string $version = '^9.0';

    public function files(): array
    {
        return [
            'packages/foo.json' => '{locale}.json',
        ];
    }
}
