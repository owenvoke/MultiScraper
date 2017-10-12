<?php

require_once __DIR__.'/vendor/autoload.php';

use \YeTii\General\Str;

$files = [];
$files[] = (object)[
    'path'=>'Screens/screen0001.png',
    'file_size'=>'318KB'
];
$files[] = (object)[
    'path'=>'Screens/screen0002.png',
    'file_size'=>'124.12KB'
];
$files[] = (object)[
    'path'=>'Screens/Part2/screen0003.png',
    'file_size'=>'178.77KB'
];
$files[] = (object)[
    'path'=>'Screens/Part2/screen0004.png',
    'file_size'=>'325.59KB'
];
$files[] = (object)[
    'path'=>'Torrent Downloaded From WWW.TORRENTING.COM.txt',
    'file_size'=>'84B'
];
$files[] = (object)[
    'path'=>'Vegas.Strip.S02E08.480p.x264-mSD.mkv',
    'file_size'=>'191.04MB'
];
$files[] = (object)[
    'path'=>'nfo/about/the/torrent/Vegas.Strip.S02E08.480p.x264-mSD.nfo',
    'file_size'=>'1.15KB'
];
  
function nest_files($files) {
    $return = [];
    foreach ($files as $file) {
        $tmp =& $return;
        foreach (explode('/', Str::beforeLast($file->path, '/')) as $p) {
            if (!$p) continue;
            if (!isset($tmp[$p]))
                $tmp[$p] = [];
            $tmp =& $tmp[$p];
        }
        $tmp[Str::afterLast($file->path, '/')] = $file;
    }
    return $return;
}

$files = nest_files($files);

print '<pre>'.print_r($files, true);