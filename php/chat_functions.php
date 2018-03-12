<?php

function convert_emoticon($text){
    $emoticon_path = "/assets/img/mini_";
    $emoticon_ext = ".gif";

    $convert_from = [
        ':skull:',
        ':)',
        ':D',
        ':|',
        '>:(',
        ':(',
        ':s',
        ':o',
        '8)'
    ];

    $convert_to = [
        'skull',
        'smile',
        'happy',
        'neutral',
        'angry',
        'sad',
        'confuse',
        'shocked',
        'cool'
    ];

    foreach($convert_to as &$line){
        $url = $emoticon_path . $line . $emoticon_ext;
        $line = '<img class="emoticon" src="'.$url.'" alt="'.$line.'"/>';
    }

    $text = str_replace($convert_from, $convert_to, $text);
    return $text;
}

?>