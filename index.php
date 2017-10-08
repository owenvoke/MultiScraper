<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);

require_once __DIR__.'/vendor/autoload.php';

$scraper = new YeTii\MultiScraper\MultiScraper();
$torrents = $scraper->latest();

// $b = torrent_info('https://www.hypercache.pw/metadata/MlRQeU1MR2tteGV0UitxTnF0WUxFdz09/?inuid=0');
// print '<pre>';
// print_r($b);
printDie($torrents);
printDie('End of file');
