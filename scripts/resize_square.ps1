param(
    [Parameter(Mandatory = $true)]
    [string]$InputImage,

    [Parameter(Mandatory = $true)]
    [string]$OutputPrefix
)

Add-Type -AssemblyName System.Drawing

if (-not (Test-Path $InputImage)) {
    throw "Input image not found: $InputImage"
}

$directory = Split-Path -Parent $OutputPrefix
if ($directory -and -not (Test-Path $directory)) {
    New-Item -ItemType Directory -Path $directory -Force | Out-Null
}

$img = [System.Drawing.Image]::FromFile($InputImage)
try {
    $side = [Math]::Min($img.Width, $img.Height)
    $srcX = [int](($img.Width - $side) / 2)
    $srcY = [int](($img.Height - $side) / 2)
    $srcRect = New-Object System.Drawing.Rectangle($srcX, $srcY, $side, $side)

    foreach ($size in @(300, 600, 1200)) {
        $bmp = New-Object System.Drawing.Bitmap($size, $size)
        try {
            $g = [System.Drawing.Graphics]::FromImage($bmp)
            try {
                $g.CompositingQuality = [System.Drawing.Drawing2D.CompositingQuality]::HighQuality
                $g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
                $g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
                $g.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality

                $destRect = New-Object System.Drawing.Rectangle(0, 0, $size, $size)
                $g.DrawImage($img, $destRect, $srcRect, [System.Drawing.GraphicsUnit]::Pixel)
            }
            finally {
                $g.Dispose()
            }

            $outputPath = "$OutputPrefix`_${size}.png"
            $bmp.Save($outputPath, [System.Drawing.Imaging.ImageFormat]::Png)
            Write-Output "Generated: $outputPath"
        }
        finally {
            $bmp.Dispose()
        }
    }
}
finally {
    $img.Dispose()
}
