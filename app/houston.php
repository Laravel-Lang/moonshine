<?php

declare(strict_types=1);

use DragonCode\Support\Facades\Filesystem\Directory;
use DragonCode\Support\Facades\Helpers\Arr;

require_once __DIR__ . '/../vendor/autoload.php';

foreach (Directory::names(__DIR__ . '/../locales') as $locale) {
    $path = __DIR__ . "/../locales/$locale/php.json";

    $values = Arr::ofFile($path);

    if (! $houston = $values->get(0)) {
        continue;
    }

    $values = $values->set('404', $houston)->toArray();

    unset($values[0], $values[1]);

    file_put_contents(
        $path,
        json_encode($values, JSON_PRETTY_PRINT ^ JSON_UNESCAPED_SLASHES ^ JSON_UNESCAPED_UNICODE)
    );
    
    dump($locale);
}
