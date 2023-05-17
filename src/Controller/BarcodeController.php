<?php

namespace App\Controller;

class BarcodeController
{
    public function bar128($code, $filename, $height, $with_text) 
    {
            // Code 128 character set
            $char128asc=' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~'; 
            $chars = array(
                ' ', '!', '"', '#', '$', '%', '&', "'", '(', ')', '*', '+', ',', '-', '.', '/',
                '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':', ';', '<', '=', '>', '?',
                '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
                'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '[', '\\', ']', '^', '_',
                '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o',
                'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '{', '|', '}', '~', 'DEL'
            );
            
            // Code 128 start character, stop character, and checksum calculation
            $start = 104;
            $stop = 106;
            $sum = $start;
            for ($i = 0; $i < strlen($code); $i++) {
                $char = strpos($char128asc, $code[$i]);
                $sum += ($char * ($i + 1));
            }
            $checksum = $sum % 103;
            
            // Code 128 barcode pattern and width calculation
            $pattern = array($start);
            for ($i = 0; $i < strlen($code); $i++) {
                $char = strpos($char128asc, $code[$i]);
                $pattern[] = $char;
            }
            $pattern[] = $checksum;
            $pattern[] = $stop;
            
            $width = 1;
            foreach ($pattern as $bit) {
                $width += $bit;
            }
            
            // Code 128 barcode image creation
            $image = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($image, 255, 255, 255);
            $black = imagecolorallocate($image, 0, 0, 0);
            imagefill($image, 0, 0, $white);
            $x = 0;
            foreach ($pattern as $bit) {
                $color = ($bit ? $black : $white);
                imagefilledrectangle($image, $x, 0, $x+$bit-1, $height-1, $color);
                $x += $bit;
            }
        
        // Code 128 barcode image text label
    }
}