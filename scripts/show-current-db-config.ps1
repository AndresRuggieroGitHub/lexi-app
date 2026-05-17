param()

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

$connection = Illuminate\Support\Facades\DB::connection();
$driver = $connection->getDriverName();

echo json_encode([
    'app_env' => config('app.env'),
    'app_url' => config('app.url'),
    'default_connection' => config('database.default'),
    'driver' => $driver,
    'database_name' => $connection->getDatabaseName(),
    'is_mysql_like' => in_array($driver, ['mysql', 'mariadb'], true),
], JSON_UNESCAPED_SLASHES);
'@

Set-Location "$PSScriptRoot\.."

php artisan config:clear | Out-Null
Assert-LastExitCode 'php artisan config:clear'

$raw = php -r $connectionCheck
Assert-LastExitCode 'php -r current db config check'

$status = $raw | ConvertFrom-Json

Write-Host "Entorno: $($status.app_env)"
Write-Host "URL: $($status.app_url)"
Write-Host "Conexion Laravel: $($status.default_connection)"
Write-Host "Driver: $($status.driver)"
Write-Host "Base de datos: $($status.database_name)"

if ($status.is_mysql_like) {
    Write-Host 'Estado: MySQL/MariaDB activo' -ForegroundColor Green
} else {
    Write-Host 'Estado: SQLite local activa' -ForegroundColor Yellow
}