<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);

require_once __DIR__.'/vendor/autoload.php';

$scraper = new YeTii\MultiScraper\MultiScraper();

$scraper->latest();
printDie('End of file');
// $str = json_encode($t);
// header("Content-Type: application/json");
// print $str;