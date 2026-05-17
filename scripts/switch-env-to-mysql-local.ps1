param(
    [string]$DbHost = '127.0.0.1',
    [string]$DbPort = '3306',
    [string]$DbName = 'lexi',
    [string]$DbUser = 'root',
    [string]$DbPassword = '',
    [switch]$EmptyPassword,
    [switch]$Bootstrap,
    [switch]$SkipSeed
)

$ErrorActionPreference = 'Stop'

function Assert-LastExitCode {
    param([string]$CommandName)

    if ($LASTEXITCODE -ne 0) {
        throw "$CommandName failed with exit code $LASTEXITCODE."
    }
}

function Assert-MySqlServerReachable {
    param(
        [string]$TargetHost,
        [string]$TargetPort,
        [string]$Hint
    )

    $reachable = Test-NetConnection -ComputerName $TargetHost -Port ([int]$TargetPort) -InformationLevel Quiet -WarningAction SilentlyContinue

    if (-not $reachable) {
        throw "No hay ningun servidor MySQL/MariaDB escuchando en ${TargetHost}:${TargetPort}. $Hint"
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
    'connection_ok' => true,
], JSON_UNESCAPED_SLASHES);
'@

Set-Location "$PSScriptRoot\.."

$envPath = Join-Path (Get-Location) '.env'
$backupPath = Join-Path (Get-Location) '.env.sqlite.backup'

if (-not (Test-Path $envPath)) {
    throw 'No existe .env en la raíz del proyecto.'
}

if (-not (Test-Path $backupPath)) {
    Copy-Item $envPath $backupPath
}

$originalContent = Get-Content $envPath -Raw
$resolvedPassword = if ($EmptyPassword.IsPresent) { '' } else { $DbPassword }
$content = $originalContent

Assert-MySqlServerReachable -TargetHost $DbHost -TargetPort $DbPort -Hint 'Arranca XAMPP/MySQL local o Docker antes de conmutar el .env a MySQL.'

$patterns = @{
    'DB_CONNECTION' = 'DB_CONNECTION=mysql'
    'DB_HOST' = "DB_HOST=$DbHost"
    'DB_PORT' = "DB_PORT=$DbPort"
    'DB_DATABASE' = "DB_DATABASE=$DbName"
    'DB_USERNAME' = "DB_USERNAME=$DbUser"
    'DB_PASSWORD' = "DB_PASSWORD=$resolvedPassword"
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

try {
    php artisan config:clear
    Assert-LastExitCode 'php artisan config:clear'

    if ($Bootstrap.IsPresent) {
        $bootstrapArgs = @(
            '-ExecutionPolicy', 'Bypass',
            '-File', (Join-Path $PSScriptRoot 'bootstrap-mysql-local.ps1'),
            '-DbHost', $DbHost,
            '-DbPort', $DbPort,
            '-DbName', $DbName,
            '-DbUser', $DbUser
        )

        if (-not $EmptyPassword.IsPresent) {
            $bootstrapArgs += @('-DbPassword', $DbPassword)
        }

        if ($EmptyPassword.IsPresent) {
            $bootstrapArgs += '-EmptyPassword'
        }

        if ($SkipSeed.IsPresent) {
            $bootstrapArgs += '-SkipSeed'
        }

        powershell @bootstrapArgs
        Assert-LastExitCode 'bootstrap-mysql-local.ps1'
    }

    php -r $connectionCheck
    Assert-LastExitCode 'php -r mysql connection check'
} catch {
    [System.IO.File]::WriteAllText($envPath, $originalContent, [System.Text.UTF8Encoding]::new($false))
    php artisan config:clear
    Assert-LastExitCode 'php artisan config:clear rollback'
    throw
}