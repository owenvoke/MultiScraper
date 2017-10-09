<?php

namespace YeTii\MultiScraper;

class MultiScraper
{
    private $debug = true;
    protected $sites = [];
    protected $user = null;
    protected $query = null;
    protected $page = 1;
    protected $torrents = [];

    public function __construct(array $args = null)
    {

    }

    public function __get(string $name)
    {
        return isset($this->{$name}) ? $this->{$name} : null;
    }

    public function __set(string $name, $value)
    {
        $this->{$name} = $value;
    }

    public function initalize()
    {
        $this->sites = require __DIR__ . '/scrapers.php';
    }

    public function latest($page = 1)
    {
        if (!$this->initalized) {
            $this->initalize();
        }
        $this->page = $page;

        return $this->scrape();
    }

    public function search($query, $page = 1)
    {
        if (!$this->initalized) {
            $this->initalize();
        }
        $this->query = $query;
        $this->page = $page;

        return $this->scrape();
    }

    public function user($user, $page = 1)
    {
        if (!$this->initalized) {
            $this->initalize();
        }
        $this->user = $user;
        $this->page = $page;

        return $this->scrape();
    }

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