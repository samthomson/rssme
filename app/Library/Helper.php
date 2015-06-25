<?php

namespace App\Library;

use DOMDocument;

class Helper
{    

    public static function sRandomUserFeedColour()
    {
        // define a bunch of colours

        $saColours = [
        'turquoise' => '1abc9c',
        'emerald' => '2ecc71',
        'peter river' => '3498db',
        'amethyst' => '9b59b6',
        'wet asphalt' => '34495e',        
        'green sea' => '16a085',
        'nephritus' => '27ae60',
        'belize hole' => '2980b9',
        'wisteria' => '8e44ad',
        'midnight blue' => '2c3e50',        
        'sun flower' => 'f1c40f',
        'carrot' => 'e67e22',
        'alizarin' => 'e74c3c',
        'clouds' => 'ecf0f1',
        'concrete' => '95a5a6',        
        'orange' => 'f39c12',
        'pumpkin' => 'd35400',
        'pomegranite' => 'c0392b',
        'silver' => 'bdc3c7',
        'asbestos' => '7f8c8d'
        ];

        $saKeys = array_keys($saColours);

        return $saColours[$saKeys[mt_rand(0, count($saColours))]];
    }

    public static function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }
    public static function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }
}
