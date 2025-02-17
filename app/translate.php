<?php

declare(strict_types=1);

use DragonCode\Support\Facades\Filesystem\Directory;
use DragonCode\Support\Facades\Helpers\Arr;
use DragonCode\Support\Helpers\Ables\Arrayable;
use Illuminate\Support\Str;

require_once __DIR__ . '/../vendor/autoload.php';

function dotted(Arrayable $array): array
{
    return collect($array->toArray())->dot()->all();
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

    $flags = JSON_THROW_ON_ERROR ^ JSON_PRETTY_PRINT ^ JSON_UNESCAPED_SLASHES ^ JSON_UNESCAPED_UNICODE;

    file_put_contents(__DIR__ . "/../locales/$locale/$filename", json_encode($values, $flags));
}

foreach (Directory::names(__DIR__ . '/../locales') as $locale) {

    $moonshineAuth       = fromMoonShine('auth.php');
    $moonshineValidation = fromMoonShine('validation.php');

    $translated       = translated($locale, false);
    $translatedInline = translated($locale, true);

    $keys = array_keys(merge($moonshineAuth, $moonshineValidation));

    $lang       = fromLang($locale, false, $keys);
    $langInline = fromLang($locale, true, $keys);

    $merged       = merge($translated, $moonshineAuth, $moonshineValidation, $lang);
    $mergedInline = merge($translatedInline, $moonshineAuth, $moonshineValidation, $langInline);

    $merged = array_filter(
        $merged,
        fn (mixed $value) => filled($value)
    );

    $mergedInline = array_filter(
        $mergedInline,
        fn (mixed $value) => filled($value) && Str::doesntContain($value, ':attribute', true)
    );

    storeTranslations($locale, $merged, false);
    storeTranslations($locale, $mergedInline, true);
}
