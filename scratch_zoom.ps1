Add-Type -AssemblyName System.Drawing
$outDir = 'C:\Users\Windows\.gemini\antigravity-ide\brain\923caf7e-b35b-4921-9909-5a9b9ad02cd2\scratch'

for ($i = 1; $i -le 5; $i++) {
    $srcPath = Join-Path $outDir ("kib_b_part_" + $i + ".png")
    if (Test-Path $srcPath) {
        $img = [System.Drawing.Image]::FromFile($srcPath)
        $scale = 4
        $bmp = New-Object System.Drawing.Bitmap($img.Width * $scale, $img.Height * $scale)
        $g = [System.Drawing.Graphics]::FromImage($bmp)
        $g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::NearestNeighbor
        $g.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::Half
        $g.DrawImage($img, 0, 0, $bmp.Width, $bmp.Height)
        $g.Dispose()
        $img.Dispose()

        $dstPath = Join-Path $outDir ("kib_b_part_" + $i + "_zoom.png")
        $bmp.Save($dstPath, [System.Drawing.Imaging.ImageFormat]::Png)
        $bmp.Dispose()
        Write-Host "Upscaled part $i -> $dstPath"
    }
}
