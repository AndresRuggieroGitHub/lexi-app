param(
    [int]$Port = 8001,
    [string]$DbHost = '127.0.0.1',
    [string]$DbPort = '3306',
    [string]$DbName = 'lexi',
    [string]$DbUser = 'lexi',
    [string]$DbPassword = 'lexi',
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

Set-Location "$PSScriptRoot\.."

$resolvedPassword = if ($EmptyPassword.IsPresent) { '' } else { $DbPassword }

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

$env:APP_ENV = 'local'
$env:APP_URL = "http://127.0.0.1:$Port"
$env:DB_CONNECTION = 'mysql'
$env:DB_HOST = $DbHost
$env:DB_PORT = $DbPort
$env:DB_DATABASE = $DbName
$env:DB_USERNAME = $DbUser
$env:DB_PASSWORD = $resolvedPassword

php artisan serve --host=127.0.0.1 --port=$Port
Assert-LastExitCode 'php artisan serve'