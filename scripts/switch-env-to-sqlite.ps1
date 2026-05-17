param(
    [string]$SqlitePath = 'database/database.sqlite'
)

$ErrorActionPreference = 'Stop'

function Assert-LastExitCode {
    param([string]$CommandName)

    if ($LASTEXITCODE -ne 0) {
        throw "$CommandName failed with exit code $LASTEXITCODE."
    }
}

$connectionCheck = @'
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
Illuminate\Support\Facades\DB::connection()->getPdo();
echo json_encode([
    'default_connection' => config('database.default'),
    'database_name' => Illuminate\Support\Facades\DB::connection()->getDatabaseName(),
], JSON_UNESCAPED_SLASHES);
'@

Set-Location "$PSScriptRoot\.."

$envPath = Join-Path (Get-Location) '.env'
$backupPath = Join-Path (Get-Location) '.env.sqlite.backup'

if (-not (Test-Path $envPath)) {
    throw 'No existe .env en la raíz del proyecto.'
}

if (Test-Path $backupPath) {
    Copy-Item $backupPath $envPath -Force
} else {
    $content = Get-Content $envPath -Raw
    $patterns = @{
        'DB_CONNECTION' = 'DB_CONNECTION=sqlite'
        'DB_DATABASE' = "DB_DATABASE=$SqlitePath"
        'DB_HOST' = '# DB_HOST=127.0.0.1'
        'DB_PORT' = '# DB_PORT=3306'
        'DB_USERNAME' = '# DB_USERNAME=root'
        'DB_PASSWORD' = '# DB_PASSWORD='
    }

    foreach ($entry in $patterns.GetEnumerator()) {
        $regex = '(?m)^#?\s*' + [regex]::Escape($entry.Key) + '=.*$'
        if ($content -match $regex) {
            $content = [regex]::Replace($content, $regex, [System.Text.RegularExpressions.MatchEvaluator]{ param($m) $entry.Value }, 1)
        } else {
            if (-not $content.EndsWith("`r`n") -and -not $content.EndsWith("`n")) {
                $content += "`r`n"
            }
            $content += $entry.Value + "`r`n"
        }
    }

    [System.IO.File]::WriteAllText($envPath, $content, [System.Text.UTF8Encoding]::new($false))
}

php artisan config:clear
Assert-LastExitCode 'php artisan config:clear'
php -r $connectionCheck
Assert-LastExitCode 'php -r sqlite connection check'