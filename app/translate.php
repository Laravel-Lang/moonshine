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

function fromLang(string $locale, bool $isInline): array
{
    $filename = $isInline ? 'php-inline.json' : 'php.json';

    $values = Arr::ofFile(__DIR__ . "/../vendor/laravel-lang/lang/locales/$locale/$filename");

    return dotted($values);
}

function fromMoonShine(string $filename): array
{
    $values = Arr::ofFile(__DIR__ . '/../source/moonshine/3.x/' . $filename);

    return dotted($values);
}

function translated(string $locale, bool $isInline): array
{
    $filename = $isInline ? 'php-inline.json' : 'php.json';

    $values = Arr::ofFile(__DIR__ . "/../locales/$locale/$filename");

    return dotted($values);
}

function storeTranslations(string $locale, array $values, bool $isInline): void
{
    $filename = $isInline ? 'php-inline.json' : 'php.json';

    file_put_contents(__DIR__ . "/../locales/$locale/$filename", json_encode($values, JSON_THROW_ON_ERROR));
}

foreach (Directory::names(__DIR__ . '/../locales') as $locale) {
    $langTranslated       = fromLang($locale, false);
    $langInlineTranslated = fromLang($locale, true);

    $moonshineAuth       = fromMoonShine('auth.php');
    $moonshineValidation = fromMoonShine('validation.php');

    $translated       = translated($locale, false);
    $translatedInline = translated($locale, true);

    $keysAuth       = array_keys($moonshineAuth);
    $keysValidation = array_keys($moonshineValidation);

    $keys = Arr::merge($keysAuth, $keysValidation);

    $merged = Arr::of($translated)->merge($langTranslated)->only($keys);

    $mergedInline = Arr::of($translatedInline)->merge($langInlineTranslated)->only($keys)->filter(
        fn (mixed $value) => Str::doesntContain($value, ':attribute', true)
    );

    storeTranslations($locale, $merged->toArray(), false);
    storeTranslations($locale, $mergedInline->toArray(), true);
}
