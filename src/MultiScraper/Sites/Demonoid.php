<?php

namespace YeTii\MultiScraper\Sites;

use YeTii\MultiScraper\Site;

/**
 * Class Demonoid
 */
class Demonoid extends Site
{
    /**
     * Demonoid constructor.
     *
     * @param mixed|null $args
     */
    public function __construct($args = null)
    {
        parent::__construct($args);

        $this->addUrl([
            'base'     => 'https://www.demonoid.pw',
            'latest'   => '/files/?seeded=2&page=$page',
            'torrent'  => '/files/details/$torrent_id/?show_files=1',
            'search'   => '/files/?seeded=2&query=$query&page=$page',
            'category' => '/files/?seeded=2&category=$category&page=$page',
        ]);

        $this->addAttribute([
            'title'       => [
                'match' => '/<td [^>]*class="ctable_header"[^>]*>Details for (.+?)<\/td>/ism',
                // 'accuracy'=>64 // length of title
            ],
            'category'    => [
                'match'         => '/<td colspan="2" class="tone_3_bl"><b>(.+?)<\/b>/ism',
                'callback'      => function ($matches, $torrent) {
                    switch (strtolower(preg_replace('/[^a-z]/i', '', $matches[1]))) {
                        case 'movies':
                            return 1;
                        case 'music':
                            return 2;
                        case 'tv':
                            return 3;
                        case 'games':
                            return 4;
                        case 'applications':
                            return 5;
                        case 'miscellaneous':
                            return 6;
                        case 'pictures':
                            return 8;
                        case 'japaneseanime':
                            return 9;
                        case 'comics':
                            return 10;
                        case 'musicvideos':
                            return 13;
                        case 'books':
                            return 11;
                        case 'audiobooks':
                            return 17;
                        default:
                            return null;
                    }
                },
                'site_specific' => true // only tpb category
            ],
            'description' => [
                'match' => '/class="ctable_content_no_pad">\s*<table[^>]*>.+?<\/div><font[^>]*>(.+?(?=<\/font>))<\/font>/ism'
            ],
            'user'        => [
                'match'         => '/<td [^>]*class="tone_1_bl"><b>Created by<\/b>.+?<a href="\/users\/([^"]+?)"/ism',
                'site_specific' => true
            ],
            'trackers'    => [
                'match'    => '/<td [^>]*class="tone_3_pad"[^>]*>Tracker:<\/td>\s*<td class="tone_3_pad" width="50%">(.+?)<\/td>\s*<\/tr>/ism',
                'callback' => function ($matches, $torrent) {
                    return [$matches[1]];
                }
            ]
        ]);

        $this->addMethod([
            'extract_rows'  => [
                'match_all' => '/<tr[^>]*>.+?<a href="\/files\/details\/(\d+)\/">.+?<\/tr>/ism',
            ],
            'parse_torrent' => [
                'match' => '/<a href="(https:\/\/www\.hypercache\.pw[^"]+)">/ism'
            ]
        ]);
    }
}