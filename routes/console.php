<?php

use App\Support\GeneratedUiLocaleCatalog;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('lexi:warm-ui-locales {locale?*} {--force}', function () {
    /** @var GeneratedUiLocaleCatalog $catalog */
    $catalog = app(GeneratedUiLocaleCatalog::class);

    $requestedLocales = (array) $this->argument('locale');
    $locales = $requestedLocales !== []
        ? $requestedLocales
        : config('lexi.ui_locales', []);

    $generated = 0;
    $skipped = 0;

    foreach ($locales as $locale) {
        if (! is_string($locale) || $locale === '') {
            continue;
        }

        if ($catalog->warmLocale($locale, (bool) $this->option('force'))) {
            $generated++;
            $this->info('Generated UI catalog for [' . $locale . '].');

            continue;
        }

        $skipped++;
        $this->line('Skipped [' . $locale . '].');
    }

    $this->newLine();
    $this->comment('Generated: ' . $generated);
    $this->comment('Skipped: ' . $skipped);
})->purpose('Pre-generate cached UI translation catalogs for Lexi locales');
