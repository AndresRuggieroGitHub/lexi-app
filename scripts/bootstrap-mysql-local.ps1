param(
    [string]$DbHost = '127.0.0.1',
    [string]$DbPort = '3306',
    [string]$DbName = 'lexi',
    [string]$DbUser = 'lexi',
    [string]$DbPassword = 'lexi',
    [switch]$EmptyPassword,
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

function Find-MySqlCli {
    $candidates = @(
        'C:\Program Files\MySQL\MySQL Server 8.4\bin\mysql.exe',
        'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe',
        'C:\Program Files\MySQL\MySQL Server 5.7\bin\mysql.exe',
        'C:\Program Files\MariaDB 11.4\bin\mysql.exe',
        'C:\Program Files\MariaDB 11.3\bin\mysql.exe',
        'C:\Program Files\MariaDB 10.11\bin\mysql.exe',
        'C:\xampp\mysql\bin\mysql.exe',
        'C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe'
    )

    foreach ($candidate in $candidates) {
        if (Test-Path $candidate) {
            return $candidate
        }
    }

    return $null
}

Set-Location "$PSScriptRoot\.."

$resolvedPassword = if ($EmptyPassword.IsPresent) { '' } else { $DbPassword }
$mysqlCli = Find-MySqlCli

Assert-MySqlServerReachable -TargetHost $DbHost -TargetPort $DbPort -Hint 'Arranca XAMPP/MySQL local o levanta Docker antes de ejecutar este script.'

if ($mysqlCli) {
    $mysqlArgs = @('-h', $DbHost, '-P', $DbPort, '-u', $DbUser)
    $mysqlArgs += @('-e', "CREATE DATABASE IF NOT EXISTS ``$DbName`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;")

    $previousMysqlPwd = $env:MYSQL_PWD

    if (-not $EmptyPassword.IsPresent) {
        $env:MYSQL_PWD = $resolvedPassword
    }

    try {
        & $mysqlCli @mysqlArgs
        Assert-LastExitCode 'mysql.exe create database'
    } finally {
        if ($null -ne $previousMysqlPwd) {
            $env:MYSQL_PWD = $previousMysqlPwd
        } else {
            Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue
        }
    }
}

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

if (-not $SkipSeed.IsPresent) {
    php artisan db:seed --force
    Assert-LastExitCode 'php artisan db:seed'
}

php artisan tinker --execute="dump([
    'users' => DB::table('users')->count(),
    'admin_exists' => DB::table('users')->where('email', 'admin@lexi.app')->exists(),
    'plans' => DB::getSchemaBuilder()->hasTable('plans') ? DB::table('plans')->count() : 0,
    'subscriptions' => DB::getSchemaBuilder()->hasTable('subscriptions') ? DB::table('subscriptions')->count() : 0,
    'ai_generations' => DB::getSchemaBuilder()->hasTable('ai_generations') ? DB::table('ai_generations')->count() : 0,
]);"
Assert-LastExitCode 'php artisan tinker'