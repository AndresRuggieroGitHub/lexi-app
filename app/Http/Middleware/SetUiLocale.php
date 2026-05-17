<?php

namespace App\Http\Middleware;

use App\Support\GeneratedUiLocaleCatalog;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetUiLocale
{
    public function __construct(
        private readonly GeneratedUiLocaleCatalog $generatedUiLocaleCatalog,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveUiLocale($request);

        $this->generatedUiLocaleCatalog->loadLocale($locale);

        App::setLocale($locale);
        Carbon::setLocale($this->generatedUiLocaleCatalog->carbonLocale($locale));

        return $next($request);
    }

    private function resolveUiLocale(Request $request): string
    {
        $requestedLocale = $request->user()?->mother_tongue_code;

        return $this->generatedUiLocaleCatalog->supportsUiLocale($requestedLocale)
            ? $requestedLocale
            : config('app.locale', 'es');
    }
}