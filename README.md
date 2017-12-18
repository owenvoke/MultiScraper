# MultiScraper

A few torrent scraper libraries for scraping torrents from all over.

## Install

Via Composer

```bash
$ composer require undeadyetii/multiscraper
```

## Usage

```php
$scraper = new \YeTii\MultiScraper\MultiScraper();

$scraper->latest(); // Scrape the latest pages over all sites
$scraper->latest(2); // Scrape latest page#2 over all sites
$scraper->search('Rick and Morty'); // Search for a query over all sites
$scraper->search('Rick and Morty', 2); // Search for a query over all sites
$scraper->category(YeTii\MultiScraper\Category::MOVIES); // Scrape torrents from a specified category

$scraper->require_fields('title', 'hash'); // Require title AND hash, otherwise trash the torrent
$scraper->require_fields(['title', 'date_created', 'trackers']); // Require an array of fields
$scraper->require_all(); // Require ALL fields, otherwise trash the torrent

$scraper->readable_bytes(); // File sizes set as `4KB` instead of `4096` [bytes]
$scraper->nested_files(); // Return a file-tree array instead of `dir/dir/dir/file.ext`
```

#### Features coming soon

```php
$scraper->user('ettv'); // Cross-site User scraping (possibly not)

$scraper->extract_images(); // Enable extraction of images from the torrent desc+page
$scraper->convert_images('jpg'); // Convert any scraped images
```

#### Logging

This package supports logging using [Monolog].  
To enable this, you will need to create a new instance of `Monolog\Logger`.  
Next attach some handlers to it using `$log->pushHandler()`.

```php
$log = new Monolog\Logger;
// Add handlers
...

$scraper = new YeTii\MultiScraper\MultiScraper($args, $log);
...
```

## Testing

```bash
$ composer test
```

## Credits

- [undeadyetii][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[monolog]: https://packagist.org/packages/monolog/monolog

[link-packagist]: https://packagist.org/packages/undeadyetii/colesscraper
[link-author]: https://github.com/undeadyetii
[link-contributors]: ../../contributors
