# MultiScraper
A multi-site torrent scraper


## Installation

Require through composer:  
`composer require undeadyetii/multiscraper`

## Unit Testing

This package uses [PHPUnit](https://phpunit.de) for it's unit tests. To run the test suite, use the following:  
`php vendor/bin/phpunit` or `phpunit` (if installed globally)

#### How to use:
```php
$scraper = new \YeTii\MultiScraper\MultiScraper();

$scraper->latest(); // scrape the latest pages over all sites
$scraper->latest(2); // scrape latest page#2 over all sites
$scraper->search('Rick and Morty'); // search for a query over all sites
$scraper->search('Rick and Morty', 2); // search for a query over all sites

$scraper->require_fields('title', 'hash'); // require title AND hash, otherwise trash the torrent
$scraper->require_fields(['title', 'date_created', 'trackers']); // require an array of fields
$scraper->require_all(); // require ALL fields, otherwise trash the torrent

$scraper->readable_bytes(); // File sizes set as `4KB` instead of `4096` [bytes]
$scraper->nested_files(); // return a file-tree array instead of `dir/dir/dir/file.ext`
```

#### Features coming soon:

```php
$scraper->category('Movies'); // Category scraping
$scraper->user('ettv'); // Cross-site User scraping (possibly not)

$scraper->extract_images(); // Enable extraction of images from the torrent desc+page
$scraper->convert_images('jpg'); // Convert any scraped images
```
