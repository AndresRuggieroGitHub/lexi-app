param(
    [switch]$SkipApache,
    [switch]$SkipMySql
)

$ErrorActionPreference = 'Stop'

function Stop-XamppProcesses {
    param(
        [string]$ExecutableName,
        [string]$ExecutablePathPrefix,
        [string]$Label
    )

    $processes = Get-CimInstance Win32_Process -Filter "Name = '$ExecutableName'" |
        Where-Object { $_.ExecutablePath -like "$ExecutablePathPrefix*" }

    if (-not $processes) {
        Write-Host "$Label no estaba en ejecución." -ForegroundColor Yellow
        return
    }

    $processes | ForEach-Object {
        Stop-Process -Id $_.ProcessId -Force
    }

    Write-Host "$Label detenido." -ForegroundColor Green
}

if (-not $SkipApache.IsPresent) {
    Stop-XamppProcesses -ExecutableName 'httpd.exe' -ExecutablePathPrefix 'C:\xampp\apache\bin\' -Label 'Apache/XAMPP'
}

if (-not $SkipMySql.IsPresent) {
    Stop-XamppProcesses -ExecutableName 'mysqld.exe' -ExecutablePathPrefix 'C:\xampp\mysql\bin\' -Label 'MariaDB/XAMPP'
}