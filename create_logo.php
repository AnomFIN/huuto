<?php
// Create a professional auction house logo
$width = 200;
$height = 60;

// Create image
$image = imagecreate($width, $height);

// Colors
$white = imagecolorallocate($image, 255, 255, 255);
$blue = imagecolorallocate($image, 37, 99, 235);  // Tailwind blue-600
$gold = imagecolorallocate($image, 245, 158, 11); // Tailwind amber-500
$darkBlue = imagecolorallocate($image, 30, 58, 138); // Tailwind blue-800

// Fill background
imagefill($image, 0, 0, $white);

// Draw auction hammer/gavel
$hammerPoints = [
    15, 25,  // Handle start
    35, 25,  // Handle end
    35, 15,  // Head start
    55, 15,  // Head end
    55, 35,  // Head bottom
    35, 35   // Back to handle
];
imagefilledpolygon($image, $hammerPoints, 6, $gold);

// Draw auction block
imagerectangle($image, 50, 30, 70, 45, $darkBlue);
imagefilledellipse($image, 60, 47, 8, 4, $darkBlue);

// Add text "HUUTO" in modern font style
$fontsize = 16;
$text = "HUUTO";
$x = 85;
$y = 35;

// Use built-in font for compatibility
imagestring($image, 5, $x, $y-15, $text, $blue);

// Add tagline in smaller text
$tagline = "Auction House";
imagestring($image, 2, $x, $y+5, $tagline, $darkBlue);

// Output as PNG
imagepng($image, 'logo.png', 9);
imagedestroy($image);

echo "Logo created successfully: logo.png";
?>