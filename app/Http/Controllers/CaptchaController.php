<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CaptchaController extends Controller
{
    public function generate()
    {
        $first = rand(1, 10);
        $second = rand(1, 10);
        
        // Store the result in session with a generic key 'captcha_result'
        session(['captcha_result' => $first + $second]);
        
        $text = $first . ' + ' . $second . ' = ';

        if (ob_get_level()) ob_end_clean();

        // Check if GD library is available
        if (function_exists('imagecreatetruecolor')) {
            $width = 120;
            $height = 40;
            $image = \imagecreatetruecolor($width, $height);
            
            // Colors
            $background = \imagecolorallocate($image, 255, 255, 255); // White
            $textColor = \imagecolorallocate($image, 51, 51, 51); // Dark Gray
            $lineColor = \imagecolorallocate($image, 200, 200, 200); // Light Gray for noise
            
            // Fill background
            \imagefilledrectangle($image, 0, 0, $width, $height, $background);
            
            // Add some noise (lines)
            for ($i = 0; $i < 5; $i++) {
                \imageline($image, 0, rand(0, $height), $width, rand(0, $height), $lineColor);
            }
            
            // Add text
            // Using built-in font (1-5), 5 is the largest
            \imagestring($image, 5, 20, 12, $text, $textColor);
            
            ob_start();
            \imagepng($image);
            $png = ob_get_clean();
            \imagedestroy($image);
    
            return response($png, 200)->header('Content-Type', 'image/png');
        }

        // SVG Fallback
        $svg = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg width="120" height="40" xmlns="http://www.w3.org/2000/svg">
    <rect width="100%" height="100%" fill="white"/>
    <line x1="0" y1="'.rand(0,40).'" x2="120" y2="'.rand(0,40).'" stroke="#c8c8c8" stroke-width="1"/>
    <line x1="0" y1="'.rand(0,40).'" x2="120" y2="'.rand(0,40).'" stroke="#c8c8c8" stroke-width="1"/>
    <line x1="0" y1="'.rand(0,40).'" x2="120" y2="'.rand(0,40).'" stroke="#c8c8c8" stroke-width="1"/>
    <text x="20" y="28" font-family="monospace" font-size="20" fill="#333333" font-weight="bold">'.$text.'</text>
</svg>';
        return response($svg, 200)->header('Content-Type', 'image/svg+xml');
    }
}
