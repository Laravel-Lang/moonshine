<?php

declare(strict_types=1);

use DragonCode\Support\Facades\Filesystem\Directory;
use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Helpers\Ables\Arrayable;
use Illuminate\Support\Str;

require_once __DIR__ . '/../vendor/autoload.php';

function dotted(array|Arrayable $array, int|string $prepend = ''): array
{
    $results = [];

    if ($array instanceof Arrayable) {
        $array = $array->toArray();
    }

    foreach ($array as $key => $value) {
        if (is_array($value) && ! empty($value)) {
            $results = Arr::merge($results, dotted($value, $prepend . $key . '.'));
        } else {
            $results[$prepend . $key] = $value;
        }
    }

    return $results;
}

function fromLang(string $locale, bool $isInline, array $onlyKeys): array
{
    $filename = $isInline ? 'php-inline.json' : 'php.json';

    $values = Arr::ofFile(__DIR__ . "/../vendor/laravel-lang/lang/locales/$locale/$filename");

    return Arr::only(dotted($values), $onlyKeys);
}

function fromMoonShine(string $filename): array
{
    $values = Arr::ofFile(__DIR__ . '/../source/moonshine/3.x/' . $filename);

    return dotted($values);
}

function translated(string $locale, bool $isInline): array
{
    $filename = $isInline ? 'php-inline.json' : 'php.json';

    $path = __DIR__ . "/../locales/$locale/$filename";

    if (! file_exists($path)) {
        return [];
    }

    $values = Arr::ofFile(__DIR__ . "/../locales/$locale/$filename");

    return dotted($values);
}

function merge(array ...$arrays): array
{
    $first = $arrays[0];

    for ($i = 1; $i < count($arrays); $i++) {
        foreach ($arrays[$i] as $key => $value) {
            $first[$key] = $value;
        }
    }

    return $first;
}

function storeTranslations(string $locale, array $values, bool $isInline): void
{
    $filename = $isInline ? 'php-inline.json' : 'php.json';

    $flags = JSON_PRETTY_PRINT ^ JSON_UNESCAPED_SLASHES ^ JSON_UNESCAPED_UNICODE;

    unset($values['custom.attribute-name.rule-name']);

    $values = Arr::ksort($values);

    file_put_contents(__DIR__ . "/../locales/$locale/$filename", json_encode($values, $flags));
}

foreach (Directory::names(__DIR__ . '/../locales') as $locale) {
    $moonshineAuth       = fromMoonShine('auth.php');
    $moonshinePagination = fromMoonShine('pagination.php');
    $moonshineUi         = fromMoonShine('ui.php');
    $moonshineValidation = fromMoonShine('validation.php');

    $translated       = translated($locale, false);
    $translatedInline = translated($locale, true);

    $keys = array_keys(merge($moonshineAuth, $moonshineValidation));

    $lang       = fromLang($locale, false, $keys);
    $langInline = fromLang($locale, true, $keys);

    $merged = merge($moonshineAuth, $moonshinePagination, $moonshineUi, $moonshineValidation, $translated, $lang);
    $mergedInline
        = merge($moonshineAuth, $moonshinePagination, $moonshineUi, $moonshineValidation, $translatedInline, $langInline);

    $merged = array_filter(
        $merged,
        fn (mixed $value) => filled($value)
    );

    $mergedInline = array_filter($mergedInline, function (mixed $value, int|string $key) use ($merged) {
        if (blank($value)) {
            return false;
        }

        if (Str::contains($value, ':attribute', true)) {
            return false;
        }

        return Str::contains($merged[$key], ':attribute', true);
    }, ARRAY_FILTER_USE_BOTH);

    storeTranslations($locale, $merged, false);
    storeTranslations($locale, $mergedInline, true);
}
