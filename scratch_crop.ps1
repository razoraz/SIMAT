Add-Type -AssemblyName System.Drawing
$imgPath = 'C:\Users\Windows\.gemini\antigravity-ide\brain\923caf7e-b35b-4921-9909-5a9b9ad02cd2\.user_uploaded\media_1788444666708.png'
$outDir = 'C:\Users\Windows\.gemini\antigravity-ide\brain\923caf7e-b35b-4921-9909-5a9b9ad02cd2\scratch'
[System.IO.Directory]::CreateDirectory($outDir) | Out-Null

$img = [System.Drawing.Image]::FromFile($imgPath)
Write-Host "Original Size: $($img.Width) x $($img.Height)"

$numParts = 5
$partW = [int]($img.Width / $numParts)
$overlap = 80

for ($i = 0; $i -lt $numParts; $i++) {
    $oLeft = 0
    if ($i -gt 0) { $oLeft = $overlap }
    $oRight = 0
    if ($i -lt $numParts - 1) { $oRight = $overlap }

    $left = [Math]::Max(0, $i * $partW - $oLeft)
    $right = [Math]::Min($img.Width, ($i + 1) * $partW + $oRight)
    $width = $right - $left

    $cropRect = New-Object System.Drawing.Rectangle($left, 0, $width, $img.Height)
    $bmp = New-Object System.Drawing.Bitmap($width, $img.Height)
    $g = [System.Drawing.Graphics]::FromImage($bmp)
    $g.DrawImage($img, 0, 0, $cropRect, [System.Drawing.GraphicsUnit]::Pixel)
    $g.Dispose()

    $outPath = Join-Path $outDir ("kib_b_part_" + ($i + 1) + ".png")
    $bmp.Save($outPath, [System.Drawing.Imaging.ImageFormat]::Png)
    $bmp.Dispose()
    Write-Host "Saved $outPath ($width x $($img.Height))"
}
$img.Dispose()
