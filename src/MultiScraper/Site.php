<?php

namespace YeTii\MultiScraper;

use JJG\Request;
use Monolog\Logger;
use YeTii\MultiScraper\Attributes\DateCreated;
use YeTii\MultiScraper\Attributes\FileCount;
use YeTii\MultiScraper\Attributes\Files;
use YeTii\MultiScraper\Attributes\FileSize;
use YeTii\MultiScraper\Attributes\Hash;
use YeTii\MultiScraper\Attributes\MagnetLink;
use YeTii\MultiScraper\Attributes\Tracker;
use YeTii\MultiScraper\Attributes\Trackers;

/**
 * Class Site
 */
class Site
{
    /**
     * @var array
     */
    protected $urls = [];
    /**
     * @var array
     */
    protected $attributes = [];
    /**
     * @var array
     */
    protected $available_attributes = [
        'title',
        'hash',
        'date_created',
        'magnet_link',
        'file_size',
        'description',
        'is_verified',
        'files',
        'file_count',
        'trackers',
        'images',
        'category',
        'user',
        'id'
    ];
    /**
     * @var array
     */
    protected $methods = [];
    /**
     * @var array
     */
    protected $categories = [];
    /**
     * @var string
     */
    protected $name = '';
    /**
     * @var bool
     */
    protected $debug = true;
    /**
     * @var Logger|null
     */
    public $logger = null;


    /* ===================================================
     + ==========            Init              ===========
     + =================================================== */

    /**
     * Site constructor.
     *
     * @param null $args
     */
    public function __construct($args = null)
    {
        if (is_string($args)) {
            $this->name = $args;
        } elseif (is_array($args) || is_object($args)) {
            foreach ($args as $key => $value) {
                $this->attributes[$key] = $value;
            }
        }
    }

    /**
     * Get an attribute
     *
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if (isset($this->{$name})) {
            return $this->{$name};
        } else {
            return null;
        }
    }

    /**
     * Set an attribute
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }

    /**
     * Set the $name property
     *
     * @param $name
     * @return $this
     */
    public function add($name)
    {
        $this->name = $name;

        return $this;
    }


    /* ===================================================
     + ==========         Categories           ===========
     + =================================================== */

    /**
     * Add a set of categories to the $categories property
     *
     * @param array $data
     * @return $this
     */
    public function addCategory($data)
    {
        foreach ($data as $key => $value) {
            $this->categories[$key] = $value;
        }

        return $this;
    }


    /* ===================================================
     + ==========            URLs              ===========
     + =================================================== */

    /**
     * Add a set of URLs to the $urls property
     *
     * @param array $data
     * @return $this
     */
    public function addUrl($data)
    {
        foreach ($data as $key => $value) {
            $this->urls[$key] = $value;
        }

        return $this;
    }

    /**
     * Get a URL from the $urls property
     *
     * @param string     $name
     * @param array|null $args
     * @return bool|string
     */
    public function getUrl($name, array $args = null)
    {
        if (!isset($this->urls['base'])) {
            // log
            return false;
        }
        if (!isset($this->urls[$name])) {
            // log
            return false;
        }
        $base = $this->urls['base'];
        $endpoint = $this->urls[$name];

        $endpoint = is_callable($endpoint) ? call_user_func($endpoint, $args) : $endpoint;

        if (strpos($endpoint, '$page')) {
            $endpoint = preg_replace('/\$page\-1/', $args['page'] - 1, $endpoint);
            $endpoint = preg_replace('/\$page/', $args['page'], $endpoint);
        }
        if (strpos($endpoint, '$query')) {
            $endpoint = preg_replace('/\$query/', $args['query'], $endpoint);
        }
        if (strpos($endpoint, '$user')) {
            $endpoint = preg_replace('/\$user/', $args['user'], $endpoint);
        }
        if (strpos($endpoint, '$category')) {
            $category = $this->categories[$args['category']] ?? null;
            $endpoint = preg_replace('/\$category/', $category, $endpoint);
        }
        if (strpos($endpoint, '$id')) {
            $endpoint = preg_replace('/\$id/', $args['id'], $endpoint);
        }
        if (strpos($endpoint, '$hash')) {
            $endpoint = preg_replace('/\$hash/', $args['hash'], $endpoint);
        }
        if (strpos($endpoint, '$torrent_id')) {
            $endpoint = preg_replace('/\$torrent_id/', $args['torrent_id'], $endpoint);
        }

        return $base . $endpoint;
    }


    /* ===================================================
     + ==========         Attributes           ===========
     + =================================================== */

    /**
     * Add a new attribute
     *
     * @param array|object $data
     * @return $this
     */
    public function addAttribute($data)
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $value = ['match' => $value];
            }
            $value = (object)$value;

            $attr = new \stdClass;
            $attr->match = isset($value->match) && is_string($value->match) ? $value->match : null;
            $attr->match_all = isset($value->match_all) && is_string($value->match_all) ? $value->match_all : null;
            $attr->from = isset($value->from) && is_string($value->from) ? $value->from : null;
            $attr->callback = isset($value->callback) ? $value->callback : null;
            $attr->class_name = $this->getAttributeClass($key);
            $attr->match_group = isset($value->match_group) ? $value->match_group : 1;
            $attr->site_specific = isset($value->site_specific) ? boolval($value->site_specific) : false;
            $attr->value = isset($value->value) ? $value->value : false;
            if ($attr->match || $attr->match_all) {
                $this->attributes[$key] = $attr;
            } else {
                $this->log('Failed to add attribute.', Logger::ERROR);
            }
        }

        return $this;
    }

    /**
     * Get the class name of an attribute
     *
     * @param string $key
     * @return string
     */
    public function getAttributeClass($key)
    {
        $new = 'YeTii\\MultiScraper\\Attributes\\';
        foreach (preg_split('/[\.\-\_]/', $key) as $k) {
            $new .= ucfirst($k);
        }

        return $new;
    }

    /**
     * Get an attribute that has been set
     *
     * @param string $name
     * @return bool|mixed
     */
    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : false;
    }


    /* ===================================================
     + ==========          Methods             ===========
     + =================================================== */


    /**
     * Add a new method/callback for the site
     *
     * @param array|object $data
     * @return $this
     */
    public function addMethod($data)
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $value = ['match' => $value];
            }
            $value = (object)$value;

            $method = new \stdClass;
            $method->match = isset($value->match) ? $value->match : null;
            $method->match_all = isset($value->match_all) ? $value->match_all : null;
            $method->callback = isset($value->callback) ? $value->callback : null;
            $method->match_group = isset($value->match_group) ? (int)$value->match_group : 1;
            if ($method->match || $method->match_all) {
                $this->methods[$key] = $method;
            } else {
                $this->log('Failed to add method.', Logger::ERROR);
            }
        }

        return $this;
    }

    /**
     * Return a requested method
     *
     * @param string $name
     * @return bool|mixed
     */
    public function getMethod($name)
    {
        return isset($this->methods[$name]) ? $this->methods[$name] : false;
    }

    /**
     * Execute a specified method
     *
     * @param string $name
     * @param mixed  $args
     * @return mixed|null
     */
    private function runMethod($name, $args)
    {
        if (!$method = $this->getMethod($name)) {
            // log
            return null;
        }
        $m = null;
        if ($method->match) {
            preg_match($method->match, $args['html'], $m);
        }
        if ($method->match_all) {
            preg_match_all($method->match_all, $args['html'], $m);
        }
        if ($method->callback) {
            return call_user_func($method->callback, $m);
        }

        return isset($m[$method->match_group]) ? $m[$method->match_group] : null;
    }


    /* ===================================================
     + ==========          Scraping            ===========
     + =================================================== */

    /**
     * Scrape the latest torrents for a site
     *
     * @param int $page
     * @return array|object
     * @throws \Exception
     */
    public function scrapeLatest(int $page = 1)
    {
        $torrents = [];
        if ($url = $this->getUrl('latest', ['page' => $page])) {
            $html = $this->getHtml($url);
            if ($html) {
                $torrent_ids = $this->runMethod('extract_rows', ['html' => $html]);
                if ($torrent_ids) {
                    foreach ($torrent_ids as $torrent_id) {
                        $t = $this->scrapeTorrent(['torrent_id' => $torrent_id]);
                        if (isset($t->title)) {
                            $torrents[] = $t;
                        }
                    }
                }
            } else {
                $this->log('Failed to scrape the latest torrents.', Logger::WARNING);
            }
        }

        return crawl_attribute($torrents);
    }

    /**
     * Scrape search results for a site
     *
     * @param string $query
     * @param int    $page
     * @return array|object
     * @throws \Exception
     */
    public function scrapeSearch(string $query, int $page = 1)
    {
        $torrents = [];
        if ($url = $this->getUrl('search', ['query' => $query, 'page' => $page])) {
            $html = $this->getHtml($url);
            if ($html) {
                $torrent_ids = $this->runMethod('extract_rows', ['html' => $html]);
                if ($torrent_ids) {
                    foreach ($torrent_ids as $torrent_id) {
                        $t = $this->scrapeTorrent(['torrent_id' => $torrent_id]);
                        if (isset($t->title)) {
                            $torrents[] = $t;
                        }
                    }
                }
            } else {
                $this->log('Failed to scrape the queried torrents.', Logger::WARNING);
            }
        }

        return crawl_attribute($torrents);
    }

    /**
     * Scrape a user's torrents for a site
     *
     * @param string $user
     * @param int    $page
     * @return array|object
     * @throws \Exception
     */
    public function scrapeUser(string $user, int $page = 1)
    {
        $torrents = [];
        if ($url = $this->getUrl('user', ['user' => $user, 'page' => $page])) {
            $html = $this->getHtml($url);
            if ($html) {
                $torrent_ids = $this->runMethod('extract_rows', ['html' => $html]);
                if ($torrent_ids) {
                    foreach ($torrent_ids as $torrent_id) {
                        $t = $this->scrapeTorrent(['torrent_id' => $torrent_id]);
                        if (isset($t->title)) {
                            $torrents[] = $t;
                        }
                    }
                }
            } else {
                $this->log('Failed to scrape the user\'s torrents.', Logger::WARNING);
            }
        }

        return crawl_attribute($torrents);
    }

    /**
     * Scrape a category's torrents for a site
     *
     * @param string $category
     * @param int    $page
     * @return array|object
     * @throws \Exception
     */
    public function scrapeCategory(string $category, int $page = 1)
    {
        $torrents = [];
        if ($url = $this->getUrl('category', ['category' => $category, 'page' => $page])) {
            $html = $this->getHtml($url);
            if ($html) {
                $torrent_ids = $this->runMethod('extract_rows', ['html' => $html]);
                if ($torrent_ids) {
                    foreach ($torrent_ids as $torrent_id) {
                        $t = $this->scrapeTorrent(['torrent_id' => $torrent_id]);
                        if (isset($t->title)) {
                            $torrents[] = $t;
                        }
                    }
                }
            } else {
                $this->log('Failed to scrape the category\'s torrents.', Logger::WARNING);
            }
        }

        return crawl_attribute($torrents);
    }

    /**
     * Scrape a specific torrent for a site
     *
     * @param $args
     * @return \stdClass
     * @throws \Exception
     */
    public function scrapeTorrent($args)
    {
        $torrent = new \stdClass;
        if (isset($args['torrent_id']) && $torrent_id = $args['torrent_id']) {
            $url = $this->getUrl('torrent', ['torrent_id' => $torrent_id]);
            $html = $this->getHtml($url);

            if ($this->getMethod('remove_if_deleted') && $this->runMethod('remove_if_deleted', ['html' => $html])) {
                return $torrent;
            }

            if (false && $this->getMethod('parse_torrent') && $url2 = $this->runMethod('parse_torrent', ['html' => $html])) {
                if ($info = torrent_info($url2)) {
                    $torrent->date_created = new DateCreated($info['creation date']);
                    $torrent->hash = new Hash($info['info_hash']);
                    $file_size = 0;
                    $files = [];
                    if (isset($info['info']['files'])) {
                        foreach ($info['info']['files'] as $f) {
                            $file_size += $f['length'];
                            $file = new \stdClass;
                            $file->path = implode('/', $f['path']);
                            $file->file_size = $f['length'];
                            $files[] = $file;
                        }
                    } elseif (isset($info['info']['length'])) {
                        $file_size = $info['info']['length'];
                        $file = new \stdClass;
                        $file->path = $info['info']['name'];
                        $file->file_size = $info['info']['length'];
                        $files[] = $file;
                    }
                    $torrent->file_size = new FileSize($file_size);
                    $torrent->files = new Files($files);
                    $torrent->file_count = new FileCount(count($files));
                    $trackers = [];
                    foreach ($info['announce-list'] as $tr) {
                        if (!in_array($tr[0], $trackers)) {
                            $trackers[] = $tr[0];
                        }
                    }
                    $torrent->trackers = new Trackers($trackers);
                }
            }

            foreach ($this->attributes as $key => $attr) {
                if (isset($torrent->{$key})) {
                    continue;
                }
                $m = null;
                $from = $attr->from ? $this->getHtml($this->getUrl($attr->from, ['torrent_id' => $torrent_id])) : $html;

                if ($attr->match) {
                    preg_match($attr->match, $from, $m);
                }
                if ($attr->match_all) {
                    preg_match_all($attr->match_all, $from, $m);
                }

                $class_name = $attr->class_name;

                if ($m && $attr->callback) {
                    $val = call_user_func($attr->callback, $m, $torrent);
                } else {
                    $val = isset($m[$attr->match_group]) ? $m[$attr->match_group] : null;
                }

                if ($attr->value) {
                    $val = $attr->value;
                }
                $val = new $class_name($val);

                if ($attr->site_specific) {
                    $val->site_specific = $this->name;
                }

                $torrent->{$key} = $val;
            }

            if (isset($torrent->trackers) && $torrent->trackers->get() && isset($torrent->title) && isset($torrent->hash)) {
                $trackers = '';
                /** @var Tracker $tracker */
                foreach ($torrent->trackers->get() as $tracker) {
                    $trackers .= "&tr=" . urlencode($tracker->get());
                }

                $torrent->magnet_link = new MagnetLink('magnet:?xt=urn:btih:'
                                                       . $torrent->hash->get()
                                                       . '&dn='
                                                       . preg_replace('/\&/', '%26', $torrent->title->get())
                                                       . $trackers);
            }
        }

        return $torrent;
    }


    /* ===================================================
     + ==========          General             ===========
     + =================================================== */

    /**
     * Get the HTML for a site using the caching system
     *
     * @param string $url
     * @return bool|null|string
     */
    private function getHtml(string $url)
    {
        if (!file_exists('cache')) {
            mkdir('cache');
        }
        $hash = sha1($url);
        if ($this->debug && file_exists("cache/{$hash}.cache")) {
            return file_get_contents("cache/{$hash}.cache");
        }
        for ($i = 0; $i < 3; $i++) {
            $request = new Request($url);
            $request->enableCookies(realpath(__DIR__ . '/cookie.monster'));
            $request->execute();
            if ($request->getHttpCode() == 200) {
                $html = $request->getResponse();
                $i = 3;
                if ($this->debug) {
                    file_put_contents("cache/{$hash}.cache", $html);
                }

                return $html;
            }
        }

        return null;
    }

    /**
     * Output to a specified Logger
     *
     * @param string $message
     * @param int    $level
     */
    private function log(string $message, $level = Logger::DEBUG)
    {
        if ($this->logger) {
            $this->logger->log($level, $message);
        }
    }
}