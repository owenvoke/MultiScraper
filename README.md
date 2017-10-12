# MultiScraper
A multi-site torrent scraper


## Installation

Require through composer:  
`composer require undeadyetii/multiscraper`

## Unit Testing

This package uses [PHPUnit](https://phpunit.de) for it's unit tests. To run the test suite, use the following:  
`php vendor/bin/phpunit` or `phpunit` (if installed globally)

#### How to use

```php
$scraper = new \YeTii\MultiScraper\MultiScraper();
$scraper->latest(); // scrape the latest pages over all sites
$scraper->latest(2); // scrape latest page#2 over all sites
$scraper->search('Rick and Morty'); // search for a query over all sites
$scraper->require_fields('title', 'hash'); // require title AND hash, otherwise trash the torrent
$scraper->require_fields(['title', 'date_created', 'trackers']); // require an array of fields
$scraper->require_all(); // require ALL fields, otherwise trash the torrent
```

#### Features coming soon

```php
$scraper->category('Movies'); // Category scraping
$scraper->user('ettv'); // Cross-site User scraping (possibly not)

$scraper->require_all(); // Only return torrents with all fields present and populated with data.
$scraper->extract_images(); // Enable extraction of images from the torrent desc+page
$scraper->convert_images('jpg'); // Convert any scraped images
$scraper->readable_bytes(); // `4KB` instead of `4096`
$scraper->nested_files(); // return a file-tree array instead of `dir/dir/dir/file.ext`
```

#### Logging

This package supports logging using [Monolog](https://packagist.org/packages/monolog/monolog).  
To enable this, you will need to create a new instance of `Monolog\Logger`. Then attach some handlers to it using `$log->pushHandler()`.

```php
$log = new Monolog\Logger;
// Add handlers
...

$scraper = new YeTii\MultiScraper\MultiScraper($args, $log);
...
```