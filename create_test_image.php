<?php
// Create a simple test image
$image = imagecreate(100, 100);
$backgroundColor = imagecolorallocate($image, 255, 255, 255);
$textColor = imagecolorallocate($image, 0, 0, 0);
imagestring($image, 5, 20, 40, 'TEST', $textColor);
imagejpeg($image, 'test_image.jpg', 90);
imagedestroy($image);
echo "Test image created: test_image.jpg\n";
?>