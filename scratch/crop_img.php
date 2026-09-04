<?php
$path = 'C:/Users/Windows/.gemini/antigravity-ide/brain/923caf7e-b35b-4921-9909-5a9b9ad02cd2/.user_uploaded/media_1788447232193.png';
$im = imagecreatefrompng($path);
$w = imagesx($im);
$h = imagesy($im);
echo "Width: $w, Height: $h\n";

if (!is_dir('scratch')) {
    mkdir('scratch', 0777, true);
}

$parts = 5;
$pw = intval($w / $parts);
for ($i = 0; $i < $parts; $i++) {
    $crop = imagecrop($im, ['x' => $i * $pw, 'y' => 0, 'width' => $pw, 'height' => $h]);
    imagepng($crop, "scratch/kib_b_crop_" . ($i + 1) . ".png");
}
echo "Cropped 5 parts successfully.\n";
