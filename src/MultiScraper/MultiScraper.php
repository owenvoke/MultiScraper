<?php

namespace YeTii\MultiScraper;

/**
 * Class MultiScraper
 */
class MultiScraper
{
    /**
     * @var bool
     */
    private $debug = true;
    /**
     * @var array
     */
    protected $sites = [];
    /**
     * @var null
     */
    protected $user = null;
    /**
     * @var null
     */
    protected $query = null;
    /**
     * @var int
     */
    protected $page = 1;
    /**
     * @var array
     */
    protected $torrents = [];

    /**
     * MultiScraper constructor.
     * @param array|null $args
     */
    public function __construct(array $args = null)
    {

    }

    /**
     * Get an attribute
     *
     * @param string $name
     * @return null|mixed
     */
    public function __get(string $name)
    {
        return isset($this->{$name}) ? $this->{$name} : null;
    }

    /**
     * Set an attribute
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set(string $name, $value)
    {
        $this->{$name} = $value;
    }

    /**
     * Initialise the sites
     */
    public function initalize()
    {
        $this->sites = require __DIR__ . '/scrapers.php';
    }

    /**
     * Get the latest torrents from a site
     *
     * @param int $page
     * @return array
     */
    public function latest($page = 1)
    {
        if (!$this->initalized) {
            $this->initalize();
        }
        $this->page = $page;

        return $this->scrape();
    }

    /**
     * Get torrents that match a search query
     *
     * @param string $query
     * @param int    $page
     * @return array
     */
    public function search($query, $page = 1)
    {
        if (!$this->initalized) {
            $this->initalize();
        }
        $this->query = $query;
        $this->page = $page;

        return $this->scrape();
    }

    /**
     * Get torrents that match a username
     *
     * @param string $user
     * @param int    $page
     * @return array
     */
    public function user($user, $page = 1)
    {
        if (!$this->initalized) {
            $this->initalize();
        }
        $this->user = $user;
        $this->page = $page;

        return $this->scrape();
    }

    /**
     * Scrape a page for torrent data
     *
     * @return array|bool
     */
    public function scrape()
    {
        $all = [];
        if (!$this->sites) {
            return false;
        }
        foreach ($this->sites as $site) {
            $torrents = null;
            if ($this->user) {
                $torrents = $site->scrapeUser($this->user, $this->page);
            } elseif ($this->query) {
                $torrents = $site->scrapeSearch($this->query, $this->page);
            } else {
                $torrents = $site->scrapeLatest($this->page);
            }
            if ($torrents) {
                $all = array_merge($all, $torrents);
            }
        }

        return $all;
    }
}