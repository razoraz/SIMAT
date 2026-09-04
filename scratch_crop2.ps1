Add-Type -AssemblyName System.Drawing
$imgPath = 'C:\Users\Windows\.gemini\antigravity-ide\brain\923caf7e-b35b-4921-9909-5a9b9ad02cd2\.user_uploaded\media_1788445839038.png'
$outDir = 'C:\Users\Windows\.gemini\antigravity-ide\brain\923caf7e-b35b-4921-9909-5a9b9ad02cd2\scratch'
[System.IO.Directory]::CreateDirectory($outDir) | Out-Null

$img = [System.Drawing.Image]::FromFile($imgPath)
Write-Host "Original: $($img.Width) x $($img.Height)"

# Split into 6 equal parts
$numParts = 6
$partW = [int]($img.Width / $numParts)

for ($i = 0; $i -lt $numParts; $i++) {
    $left = $i * $partW
    $right = [Math]::Min($img.Width, ($i + 1) * $partW + 60)
    if ($i -eq 0) { $right = $right + 40 }
    $width = $right - $left

    $cropRect = New-Object System.Drawing.Rectangle($left, 0, $width, $img.Height)
    $scale = 5
    $newW = $width * $scale
    $newH = $img.Height * $scale
    $bmp = New-Object System.Drawing.Bitmap($newW, $newH)
    $g = [System.Drawing.Graphics]::FromImage($bmp)
    $srcRect = New-Object System.Drawing.RectangleF([float]$left, 0.0, [float]$width, [float]$img.Height)
    $dstRect = New-Object System.Drawing.RectangleF(0.0, 0.0, [float]$newW, [float]$newH)
    $g.DrawImage($img, $dstRect, $srcRect, [System.Drawing.GraphicsUnit]::Pixel)
    $g.Dispose()

    $outPath = Join-Path $outDir ("kib_b_16_46_p" + ($i + 1) + ".png")
    $bmp.Save($outPath, [System.Drawing.Imaging.ImageFormat]::Png)
    $bmp.Dispose()
    Write-Host "Saved $outPath"
}
$img.Dispose()
