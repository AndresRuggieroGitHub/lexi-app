param(
    [switch]$SkipApache,
    [switch]$SkipMySql
)

$ErrorActionPreference = 'Stop'

function Get-ListeningEntry {
    param([int]$Port)

    return Get-NetTCPConnection -State Listen -LocalPort $Port -ErrorAction SilentlyContinue |
        Select-Object -First 1
}

function Wait-ForPort {
    param(
        [int]$Port,
        [string]$ServiceName
    )

    for ($attempt = 0; $attempt -lt 20; $attempt++) {
        $listener = Get-ListeningEntry -Port $Port

        if ($listener) {
            Write-Host "$ServiceName escuchando en el puerto $Port (PID $($listener.OwningProcess))." -ForegroundColor Green
            return
        }

        Start-Sleep -Milliseconds 500
    }

    throw "$ServiceName no ha quedado escuchando en el puerto $Port."
}

function Start-IfNeeded {
    param(
        [string]$Name,
        [string]$ExecutablePath,
        [string[]]$Arguments,
        [int]$Port
    )

    $listener = Get-ListeningEntry -Port $Port
    if ($listener) {
        Write-Host "$Name ya estaba escuchando en el puerto $Port (PID $($listener.OwningProcess))." -ForegroundColor Yellow
        return
    }

    if (-not (Test-Path $ExecutablePath)) {
        throw "No existe el ejecutable de $Name en $ExecutablePath."
    }

    Start-Process -FilePath $ExecutablePath -ArgumentList $Arguments -WindowStyle Hidden | Out-Null
    Wait-ForPort -Port $Port -ServiceName $Name
}

$apachePath = 'C:\xampp\apache\bin\httpd.exe'
$mySqlPath = 'C:\xampp\mysql\bin\mysqld.exe'
$mySqlIniPath = 'C:\xampp\mysql\bin\my.ini'

if (-not $SkipMySql.IsPresent) {
    Start-IfNeeded -Name 'MariaDB/XAMPP' -ExecutablePath $mySqlPath -Arguments @("--defaults-file=$mySqlIniPath") -Port 3306
}

if (-not $SkipApache.IsPresent) {
    Start-IfNeeded -Name 'Apache/XAMPP' -ExecutablePath $apachePath -Arguments @() -Port 80
}

if (-not $SkipApache.IsPresent) {
    try {
        $response = Invoke-WebRequest -Uri 'http://127.0.0.1/phpmyadmin/' -UseBasicParsing -MaximumRedirection 5 -TimeoutSec 10
        Write-Host "phpMyAdmin responde en http://127.0.0.1/phpmyadmin/ (HTTP $([int]$response.StatusCode))." -ForegroundColor Green
    } catch {
        Write-Host 'Apache está arriba, pero phpMyAdmin no respondió correctamente todavía.' -ForegroundColor Yellow
    }
}