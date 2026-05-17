param(
    [string]$DbHost = '127.0.0.1',
    [string]$DbPort = '3306',
    [string]$DbName = 'lexi',
    [string]$DbUser = 'lexi',
    [string]$DbPassword = 'lexi',
    [switch]$EmptyPassword
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

$env:APP_ENV = 'local'
$env:DB_CONNECTION = 'mysql'
$env:DB_HOST = $DbHost
$env:DB_PORT = $DbPort
$env:DB_DATABASE = $DbName
$env:DB_USERNAME = $DbUser
$env:DB_PASSWORD = $resolvedPassword

php artisan config:clear
Assert-LastExitCode 'php artisan config:clear'
php artisan migrate --force
Assert-LastExitCode 'php artisan migrate'
php artisan db:seed --force
Assert-LastExitCode 'php artisan db:seed'
php artisan tinker --execute="dump([
    'users' => DB::table('users')->count(),
    'admin_exists' => DB::table('users')->where('email', 'admin@lexi.app')->exists(),
    'words' => DB::table('words')->count(),
    'translations' => DB::table('translations')->count(),
    'categories' => DB::table('categories')->count(),
    'plans' => DB::getSchemaBuilder()->hasTable('plans') ? DB::table('plans')->count() : 0,
    'subscriptions' => DB::getSchemaBuilder()->hasTable('subscriptions') ? DB::table('subscriptions')->count() : 0,
    'ai_generations' => DB::getSchemaBuilder()->hasTable('ai_generations') ? DB::table('ai_generations')->count() : 0,
]);"
Assert-LastExitCode 'php artisan tinker'