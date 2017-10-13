<?php

namespace YeTii\MultiScraper\Sites;

use YeTii\MultiScraper\Site;

/**
 * Class LimeTorrents
 */
class LimeTorrents extends Site
{
    /**
     * LimeTorrents constructor.
     *
     * @param mixed|null $args
     */
    public function __construct($args = null)
    {
        parent::__construct($args);

        $this->addUrl([
            'base'     => 'https://www.limetorrents.cc',
            'latest'   => '/latest100',
            'torrent'  => '/Torrent-torrent-$torrent_id.html',
            'search'   => function ($args) {
                if (!isset($args['query'])) {
                    return '/latest100';
                }

                return '/search/all/' . trim(preg_replace('/[^a-z0-9]+/i', '-', $args['query']), '-') . '/';
            },
            'category' => '/search/$name_category/$search_urlify/',
        ]);

        $this->addAttribute([
            'title'        => [
                'match' => '/<h1>(.+)<\/h1>/ism',
                // 'accuracy'=>64 // length of title
            ],
            'magnet_link'  => [
                'match' => '/<a [^>]*href="(magnet:\?xt=urn:btih:[a-f0-9]{40}[^"]*)"[^>]*>Magnet Link<\/a>/ism',
            ],
            'hash'         => [
                'match' => '/<a [^>]*href="magnet:\?xt=urn:btih:([a-f0-9]{40})[^"]*"[^>]*>Magnet Link<\/a>/ism',
            ],
            'file_count'   => [
                'match' => '/<h2>Torrent File Content \((\d+) files??\)<\/h2>/ism',
            ],
            'date_created' => [
                'match' => '/<tr><td[^>]*><b>Added<\/b> :<\/td><td>(.+? ago).+? in <a href="\/browse-torrents\/[^"]+">/ism'
            ],
            'category'     => [
                'match'         => '/<tr><td[^>]*><b>Added<\/b> :<\/td><td>.+? in <a href="\/browse-torrents\/([^"]+)">/ism',
                'callback'      => function ($matches, $torrent) {
                    switch (strtolower($matches[1])) {
                        case 'movies':
                            return 16;
                        case 'tv shows':
                        case 'tv-shows':
                        case 'tv':
                            return 20;
                        case 'music':
                            return 17;
                        case 'games':
                            return 8;
                        case 'applications':
                            return 2;
                        case 'anime':
                            return 1;
                        case 'other':
                            return 21;
                        default:
                            return null;
                    }
                },
                'site_specific' => true // only tpb category
            ],
            'user'         => [
                'match'         => '/by <a href="\/profile\/[^\/"]+\/">(.+?)<\/a>/ism',
                'site_specific' => true
            ],
            'trackers'     => [
                'match'    => '/<a [^>]*href="(magnet:\?xt=urn:btih:[a-f0-9]{40}[^"]*)"[^>]*>Magnet Link<\/a>/ism',
                'callback' => function ($matches, $torrent) {
                    $trackers = [];
                    preg_match_all('/&tr=([^&"]+)/i', $matches[1], $m);
                    foreach ($m[1] as $tracker) {
                        if (!in_array($tracker, $trackers)) {
                            $trackers[] = $tracker;
                        }
                    }

                    return $trackers;
                }
            ]
        ]);

        $this->addMethod([
            'extract_rows' => [
                'match_all' => '/<tr[^>]*><td[^>]*>.+?<a href="\/[^">]+-torrent-(\d+)/ism',
            ]
        ]);
    }
}