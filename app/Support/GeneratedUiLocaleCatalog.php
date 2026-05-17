<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Translation\Translator;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Throwable;

class GeneratedUiLocaleCatalog
{
    private const GROUP = 'lexi';
    private const CHUNK_SIZE = 40;

    public function __construct(
        private readonly Translator $translator,
    ) {
    }

    public function supportsUiLocale(?string $locale): bool
    {
        return is_string($locale)
            && in_array($locale, config('lexi.ui_locales', []), true);
    }

    public function loadLocale(string $locale): void
    {
        if (! $this->supportsUiLocale($locale) || $this->hasNativeCatalog($locale)) {
            return;
        }

        $lines = $this->loadCachedCatalog($locale);

        if ($lines === null) {
            Log::info('Lexi UI locale cache missing; skipping runtime generation.', [
                'locale' => $locale,
            ]);

            return;
        }

        $this->translator->addLines($this->flattenCatalog($lines), $locale);
    }

    public function warmLocale(string $locale, bool $force = false): bool
    {
        if (! $this->supportsUiLocale($locale) || $this->hasNativeCatalog($locale)) {
            return false;
        }

        if (! $force && $this->loadCachedCatalog($locale) !== null) {
            return false;
        }

        $this->storeCachedCatalog($locale, $this->generateCatalog($locale));

        return true;
    }

    public function carbonLocale(string $locale): string
    {
        return config('lexi.ui_locale_carbon_map.' . $locale, $locale);
    }

    public function generatedCatalogPath(string $locale): string
    {
        return storage_path('app/generated-ui-locales/' . $locale . '.php');
    }

    private function hasNativeCatalog(string $locale): bool
    {
        return File::exists(lang_path($locale . '/' . self::GROUP . '.php'));
    }

    private function loadCachedCatalog(string $locale): ?array
    {
        $path = $this->generatedCatalogPath($locale);

        if (! File::exists($path)) {
            return null;
        }

        $catalog = require $path;

        return is_array($catalog) ? $catalog : null;
    }

    private function storeCachedCatalog(string $locale, array $catalog): void
    {
        $path = $this->generatedCatalogPath($locale);

        File::ensureDirectoryExists(dirname($path));
        File::put($path, "<?php\n\nreturn " . var_export($catalog, true) . ";\n");
    }

    private function generateCatalog(string $locale): array
    {
        /** @var array<string, mixed> $source */
        $source = require lang_path('en/' . self::GROUP . '.php');

        $targetLocale = $this->translateLocale($locale);
        $flatSource = collect(Arr::dot($source));
        $translated = [];

        $flatSource
            ->chunk(self::CHUNK_SIZE)
            ->each(function (Collection $chunk, int $index) use (&$translated, $targetLocale): void {
                $translated += $this->translateChunk($chunk->all(), $targetLocale, $index);
            });

        return Arr::undot($translated);
    }

    /**
     * @param  mixed  $value
     * @return mixed
     */
    private function translateValue(mixed $value, string $targetLocale): mixed
    {
        if (is_array($value)) {
            $translated = [];

            foreach ($value as $key => $nestedValue) {
                $translated[$key] = $this->translateValue($nestedValue, $targetLocale);
            }

            return $translated;
        }

        if (! is_string($value) || trim($value) === '') {
            return $value;
        }

        return $this->translateString($value, $targetLocale);
    }

    private function translateString(string $value, string $targetLocale): string
    {
        return $this->translateChunk(['single' => $value], $targetLocale, 0)['single'];
    }

    /**
     * @param  array<string, mixed>  $catalog
     * @return array<string, string>
     */
    private function flattenCatalog(array $catalog): array
    {
        return collect(Arr::dot($catalog))
            ->mapWithKeys(fn (mixed $value, string $key): array => ['lexi.' . $key => (string) $value])
            ->all();
    }

    private function translateLocale(string $locale): string
    {
        return config('lexi.ui_locale_translation_map.' . $locale, $locale);
    }

    /**
     * @param  array<string, string>  $chunk
     * @return array<string, string>
     */
    private function translateChunk(array $chunk, string $targetLocale, int $chunkIndex): array
    {
        $translator = new GoogleTranslate();
        $translator
            ->setSource('en')
            ->setTarget($targetLocale)
            ->setOptions([
                'timeout' => 20,
                'connect_timeout' => 5,
            ])
            ->preserveParameters();

        $separator = ':lexiBoundary' . $chunkIndex;
        $tokenMaps = [];
        $maskedValues = [];

        foreach ($chunk as $key => $value) {
            [$maskedValue, $tokens] = $this->maskHtmlTokens($value, $chunkIndex . '_' . count($maskedValues));
            $maskedValues[] = $maskedValue;
            $tokenMaps[$key] = $tokens;
        }

        $translatedJoined = $translator->translate(implode("\n{$separator}\n", $maskedValues));
        $translatedValues = explode("\n{$separator}\n", $translatedJoined);

        if (count($translatedValues) !== count($maskedValues)) {
            throw new \RuntimeException('Translated UI locale chunk could not be split safely.');
        }

        $restored = [];

        foreach (array_values(array_keys($chunk)) as $index => $key) {
            $restored[$key] = strtr($translatedValues[$index], $tokenMaps[$key]);
        }

        return $restored;
    }

    /**
     * @return array{0: string, 1: array<string, string>}
     */
    private function maskHtmlTokens(string $value, string $suffix): array
    {
        $tokens = [];

        $maskedValue = preg_replace_callback('/<[^>]+>/', function (array $matches) use (&$tokens, $suffix): string {
            $token = ':lexiHtml' . $suffix . count($tokens);
            $tokens[$token] = $matches[0];

            return $token;
        }, $value) ?? $value;

        return [$maskedValue, $tokens];
    }
}