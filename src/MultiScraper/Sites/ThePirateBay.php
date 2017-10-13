<?php

namespace YeTii\MultiScraper\Sites;

use YeTii\MultiScraper\Category;
use YeTii\MultiScraper\Site;

/**
 * Class ThePirateBay
 */
class ThePirateBay extends Site
{
    /**
     * ThePirateBay constructor.
     *
     * @param mixed|null $args
     */
    public function __construct($args = null)
    {
        parent::__construct($args);

        $this->addUrl([
            'base'     => 'https://thepiratebay.org',
            'latest'   => '/recent/$page-1/',
            'torrent'  => '/torrent/$torrent_id/',
            'search'   => '/search/$query/$page-1/7//',
            'category' => '/browse/$category/$page-1/3',
            'user'     => '/user/$user/$page-1/3',
            'files'    => '/ajax_details_filelist.php?id=$torrent_id&turing=iamhuman'
        ]);

        $this->addAttribute([
            'title'        => [
                'match' => '/<div id="title">(.+?)<\/div>/ism',
                // 'accuracy'=>64 // length of title
            ],
            'magnet_link'  => [
                'match' => '/class="download">.+?href="(magnet:\?xt[^"]+)"/ism',
            ],
            'hash'         => [
                'match' => '/class="download">.+?href="[^"]+?([a-f0-9]{40})/ism',
            ],
            'file_count'   => [
                'match' => '/<dt>Files:<\/dt>.+?<a[^>]+>(\d+)/ism',
            ],
            'file_size'    => [
                'match' => '/<dt>Size:<\/dt>.+?<dd>.+\((\d+)(?:\s|&nbsp;)Bytes\)/ism',
                // 'accuracy'=>100 // 100% accuracy
            ],
            'date_created' => [
                'match' => '/<dt>Uploaded:<\/dt>.+?<dd>(.+?)<\/dd>/ism'
            ],
            'description'  => [
                'match' => '/<div class="nfo">(.+?)<\/div>.+?<div class="download">/ism'
            ],
            'category'     => [
                'match'         => '/<a href="\/browse\/(\d+)/ism',
                'site_specific' => true // only tpb category
            ],
            'user'         => [
                'match'         => '/<dt>By:<\/dt>.+?<a[^>]+?>(.+?)<\/a>/ism',
                'site_specific' => true // only tpb user
            ],
            'is_verified'  => [
                'match' => '/<img[^>]+alt="(Trusted|VIP)"/ism',
            ],
            'trackers'     => [
                'match'    => '/<a[^>]+href="(magnet:?[^"]+)"/',
                'callback' => function ($matches, $torrent) {
                    $trackers = [];
                    if (isset($matches[1])) {
                        preg_match_all('/&tr=([^&"]+)/i', $matches[1], $m);
                        foreach ($m[1] as $tracker) {
                            if (!in_array($tracker, $trackers)) {
                                $trackers[] = $tracker;
                            }
                        }
                    }

                    return $trackers;
                }
            ],
            'files'        => [
                'from'      => 'files',
                'match_all' => '/<tr><td[^>]+>([^<]+)<\/td><td[^>]+>([^<]+)/i',
                'callback'  => function ($matches, $torrent) {
                    $files = [];
                    for ($i = 0; $i < count($matches[1]); $i++) {
                        $file = new \stdClass;
                        if (isset($matches[1][$i]) && isset($matches[2][$i])) {
                            $file->path = $matches[1][$i];
                            $file->file_size = $matches[2][$i];
                            $files[] = $file;
                        }
                    }

                    return $files;
                }
            ]
        ]);

        $this->addMethod([
            'extract_rows' => [
                'match_all' => '/<tr[^>]*>.+?<a href="\/torrent\/(\d+)\/.+?<\/tr>/is',
            ]
        ]);

        $this->addCategory([
            Category::MOVIES       => 201,
            Category::TV           => 205,
            Category::GAMES        => 400,
            Category::MUSIC        => 101,
            Category::APPLICATIONS => 300,
            Category::BOOKS        => 601,
            Category::XXX          => 500,
            Category::OTHER        => 600,
        ]);
    }
}