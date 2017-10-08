# MultiScraper
A multi-site torrent scraper


## Installation

Require through composer:
`composer require undeadyetii/multiscraper`

#### How to use:
```php
$scraper = new \YeTii\MultiScraper\MultiScraper();
$scraper->latest(); // scrape the latest pages over all sites
$scraper->latest(2); // scrape latest page#2 over all sites
$scraper->search('Rick and Morty'); // search for a query over all sites
```

#### Features coming soon:

```php
$scraper->category('Movies'); // Category scraping
$scraper->user('ettv'); // Cross-site User scraping (possibly not)

$scraper->require_all(); // Only return torrents with all fields present and populated with data.
$scraper->extract_images(); // Enable extraction of images from the torrent desc+page
$scraper->convert_images('jpg'); // Convert any scraped images
$scraper->readable_bytes(); // `4KB` instead of `4096`
$scraper->nested_files(); // return a file-tree array instead of `dir/dir/dir/file.ext`
```
