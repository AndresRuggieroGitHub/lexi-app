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

    /** @var array<string, bool> */
    private array $nativeMissingMemo = [];

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
        if (! $this->supportsUiLocale($locale)) {
            return;
        }

        $lines = $this->loadCachedCatalog($locale);
        $hasCompleteCatalog = is_array($lines) && $this->catalogCoversRequiredKeys($locale, $lines);

        if (app()->runningInConsole() && config('lexi.ui_runtime_warm', false) && $this->shouldWarmAtRuntime($locale) && ! $hasCompleteCatalog) {
            try {
                $this->warmLocale($locale, $lines !== null);
                $lines = $this->loadCachedCatalog($locale);
            } catch (Throwable $exception) {
                Log::warning('Lexi UI locale runtime warmup failed.', [
                    'locale' => $locale,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $effectiveLines = is_array($lines) ? $lines : [];

        if ($this->hasNativeCatalog($locale)) {
            $effectiveLines = array_replace_recursive($this->nativeCatalog($locale), $effectiveLines);
        }

        $effectiveFlat = Arr::dot($effectiveLines);
        $missingFlat = array_diff_key($this->requiredGeneratedFlat($locale), $effectiveFlat);

        if ($missingFlat !== []) {
            $effectiveFlat += $missingFlat;
        }

        if ($effectiveFlat !== []) {
            $this->translator->addLines(
                collect($effectiveFlat)
                    ->mapWithKeys(fn (mixed $value, string $key): array => ['lexi.' . $key => (string) $value])
                    ->all(),
                $locale
            );
        }
    }

    public function warmLocale(string $locale, bool $force = false): bool
    {
        if (! $this->supportsUiLocale($locale)) {
            return false;
        }

        $cachedCatalog = $this->loadCachedCatalog($locale);

        if (! $force && is_array($cachedCatalog) && $this->catalogCoversRequiredKeys($locale, $cachedCatalog)) {
            return false;
        }

        $catalog = $this->generateCatalog($locale);

        if ($catalog === []) {
            return false;
        }

        $this->storeCachedCatalog($locale, $catalog);

        return true;
    }

    public function carbonLocale(string $locale): string
    {
        return config('lexi.ui_locale_carbon_map.' . $locale, $locale);
    }

    public function generatedCatalogPath(string $locale): string
    {
        $prefix = $this->isNativeLocale($locale)
            ? 'app/generated-ui-locales/supplements/'
            : 'app/generated-ui-locales/';

        return storage_path($prefix . $locale . '.php');
    }

    private function isNativeLocale(string $locale): bool
    {
        return $this->hasNativeCatalog($locale);
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
        $sourceFlat = Arr::dot($this->sourceCatalog());

        if ($this->hasNativeCatalog($locale)) {
            $nativeFlat = Arr::dot($this->nativeCatalog($locale));
            /** @var array<string, mixed> $sourceFlat */
            $sourceFlat = array_diff_key($sourceFlat, $nativeFlat);
        }

        if ($sourceFlat === []) {
            return [];
        }

        $targetLocale = $this->translateLocale($locale);
        $flatSource = collect($sourceFlat)
            ->mapWithKeys(fn (mixed $value, string $key): array => [$key => (string) $value]);
        $translated = [];

        $flatSource
            ->chunk(self::CHUNK_SIZE)
            ->each(function (Collection $chunk, int $index) use (&$translated, $targetLocale): void {
                $translated += $this->translateChunk($chunk->all(), $targetLocale, $index);
            });

        return Arr::undot($translated);
    }

    private function shouldWarmAtRuntime(string $locale): bool
    {
        if (! $this->supportsUiLocale($locale)) {
            return false;
        }

        if (! $this->hasNativeCatalog($locale)) {
            return true;
        }

        if (array_key_exists($locale, $this->nativeMissingMemo)) {
            return $this->nativeMissingMemo[$locale];
        }

        $sourceFlat = Arr::dot($this->sourceCatalog());
        $nativeFlat = Arr::dot($this->nativeCatalog($locale));
        $hasMissingKeys = array_diff_key($sourceFlat, $nativeFlat) !== [];
        $this->nativeMissingMemo[$locale] = $hasMissingKeys;

        return $hasMissingKeys;
    }

    /**
     * @return array<string, mixed>
     */
    private function requiredGeneratedFlat(string $locale): array
    {
        $sourceFlat = Arr::dot($this->sourceCatalog());

        if (! $this->hasNativeCatalog($locale)) {
            return $sourceFlat;
        }

        $nativeFlat = Arr::dot($this->nativeCatalog($locale));

        /** @var array<string, mixed> $sourceFlat */
        return array_diff_key($sourceFlat, $nativeFlat);
    }

    private function catalogCoversRequiredKeys(string $locale, array $catalog): bool
    {
        $requiredFlat = $this->requiredGeneratedFlat($locale);

        if ($requiredFlat === []) {
            return true;
        }

        $catalogFlat = Arr::dot($catalog);

        return array_diff_key($requiredFlat, $catalogFlat) === [];
    }

    /**
     * @return array<string, mixed>
     */
    private function sourceCatalog(): array
    {
        /** @var array<string, mixed> $source */
        $source = require lang_path('en/' . self::GROUP . '.php');

        return $source;
    }

    /**
     * @return array<string, mixed>
     */
    private function nativeCatalog(string $locale): array
    {
        $path = lang_path($locale . '/' . self::GROUP . '.php');

        if (! File::exists($path)) {
            return [];
        }

        $catalog = require $path;

        return is_array($catalog) ? $catalog : [];
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

        $tokenMaps = [];
        $maskedValues = [];

        foreach ($chunk as $key => $value) {
            [$maskedValue, $tokens] = $this->maskHtmlTokens($value, $chunkIndex . '_' . count($maskedValues));
            $maskedValues[] = $maskedValue;
            $tokenMaps[$key] = $tokens;
        }

        $translatedValues = [];

        foreach ($maskedValues as $maskedValue) {
            try {
                $translatedValues[] = $translator->translate($maskedValue);
            } catch (Throwable $exception) {
                Log::warning('Lexi UI locale string translation failed. Falling back to source string.', [
                    'target_locale' => $targetLocale,
                    'chunk_index' => $chunkIndex,
                    'message' => $exception->getMessage(),
                ]);

                $translatedValues[] = $maskedValue;
            }
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