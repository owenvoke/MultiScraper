<?php

use YeTii\MultiScraper\Site;

return [
    (new Site('ThePirateBay'))
        ->addUrl([
            'base'     => 'https://thepiratebay.org',
            'latest'   => '/recent/$page-1/',
            'torrent'  => '/torrent/$torrent_id/',
            'search'   => '/search/$query/$page-1/7//',
            'category' => '/browse/$category/$page-1/3',
            'user'     => '/user/$username/$page-1/3',
            'files'    => '/ajax_details_filelist.php?id=$torrent_id&turing=iamhuman'
        ])
        ->addAttribute([
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
        ])
        ->addMethod([
            'extract_rows' => [
                'match_all' => '/<tr[^>]*>.+?<a href="\/torrent\/(\d+)\/.+?<\/tr>/is',
            ]
        ]),
    (new Site('LimeTorrents'))
        ->addUrl([
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
        ])
        ->addAttribute([
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
        ])
        ->addMethod([
            'extract_rows' => [
                'match_all' => '/<tr[^>]*><td[^>]*>.+?<a href="\/[^">]+-torrent-(\d+)/ism',
            ]
        ]),
    (new Site('Demonoid'))
        ->addUrl([
            'base'     => 'https://www.demonoid.pw',
            'latest'   => '/files/?seeded=2&page=$page',
            'torrent'  => '/files/details/$torrent_id/?show_files=1',
            'search'   => '/files/?seeded=2&query=$query&page=$page',
            'category' => '/files/?seeded=2&category=$category&page=$page',
        ])
        ->addAttribute([
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
        ])
        ->addMethod([
            'extract_rows'  => [
                'match_all' => '/<tr[^>]*>.+?<a href="\/files\/details\/(\d+)\/">.+?<\/tr>/ism',
            ],
            'parse_torrent' => [
                'match' => '/<a href="(https:\/\/www\.hypercache\.pw[^"]+)">/ism'
            ]
        ]),


    /*

    (new Site('RARBG'))
        ->addUrl([
            'base'=>'http://rarbg.to',
            'latest'=>'/torrents.php?search=&order=data&by=DESC&page=$page',
            'torrent'=>'/torrent/$torrent_id',
            'search'=>'/torrents.php?search=$urlencoded_query&page=$page',
            'category'=>'/torrents.php?category[]=$category&page=$page',
        ])
        ->addAttribute([
            'title'=>[
                'match'=>'/<h1[^>]+itemprop="name">(.+?)<\/h1>/ism',
                // 'accuracy'=>64 // length of title
            ],
            'magnet_link'=>[
                'match'=>'/<a href="(magnet:\?xt=urn:btih:[a-f0-9]{40}[^"]*)">/ism',
            ],
            'hash'=>[
                'match'=>'/<a href="magnet:\?xt=urn:btih:([a-f0-9]{40})[^"]*">/ism',
            ],
            'file_count'=>[
                'match'=>'/<td[^>]*class="lista"[^>]*><div[^>]*id="files">.+?<div[^>]*id="msgfile">(\d+) files<\/div>/ism',
            ],
            'date_created'=>[
                'match'=>'/<td class="lista"><span itemprop="releaseDate">([^<]+)</ism'
            ],
            'description'=>[
                'match'=>'/<td[^>]*id="description"[^>]*>(.+?)<\/td>/ism',
                'callback'=>function($matches, $torrent) {
                    return preg_replace('/<b><a id="a_show_hide_mediainfo".+?<\/b>/ism', '', $matches[1]);
                }
            ],
            'category'=>[
                'match'=>'/<td[^>]*class="lista"[^>]*><a href="\/torrents\?category=(\d+)">/ism',
                'site_specific'=>true // only tpb category
            ],
            'user'=>[
                'value'=>'RARBG',
                'site_specific'=>true
            ],
            'is_verified'=>[
                'value'=>true
            ],
            'trackers'=>[
                'match'=>'<a href="(magnet:\?xt=urn:btih:[a-f0-9]{40}[^"]*)">/',
                'callback'=>function($matches, $torrent) {
                    $trackers = [];
                    preg_match_all('/&tr=([^&"]+)/i', $matches[1], $m);
                    foreach ($m[1] as $tracker) {
                        if (!in_array($tracker, $trackers))
                            $trackers[] = $tracker;
                    }
                    return $trackers;
                }
            ],
            'files'=>[
                'match_all'=>'<div[^>]*id="files"[^>]*><table class="lista">.+?(?=<\/table>)/i',
                'callback'=>function($matches, $torrent) {
                    $files = [];
                    preg_match_all('/<td[^>]*class="lista"[^>]*><img[^>]+>(?:\s|&nbsp;)+(.+?)<\/td><td[^>]*>(.+?)<\/td>/', $matches[1], $m);
                    for ($i=0;$i<count($m[1]);$i++) {
                        $file = new \stdClass;
                        if (isset($m[1][$i]) && isset($m[2][$i])) {
                            $file->path = $m[1][$i];
                            $file->file_size = $m[2][$i];
                            $files[] = $file;
                        }
                    }
                    return $files;
                }
            ]
        ])
        ->addMethod([
            'extract_rows'=>[
                'match_all'=>'/<tr class="lista2"><td[^>]+?>.+?<a.+?href="\/torrent\/([a-z0-9]+)"/ism',
            ]
        ]),
    (new Site('Monova'))
        ->addUrl([
            'base'=>'https://monova.to',
            'latest'=>'/latest?page=$page&verified=1',
            'torrent'=>'/$torrent_id/',
            'search'=>'/search?term=$query&page=$page&verified=1',
            'category'=>'/browse/$category/$page/3',
            'user'=>'/user/$user?page=$page'
        ])
        ->addAttribute([
            'title'=>[
                'match'=>'/<h1>(.+?)<\/h1>/ism',
                'callback'=>function($matches, $torrent) {
                    return isset($matches[1]) ? trim(htmlspecialchars_decode($matches[1])) : null;
                },
                // 'accuracy'=>64 // length of title
            ],
            'magnet_link'=>[
                'match'=>'/var torrent_magnet = "([^"]+)";/ism',
                'callback'=>function($matches, $torrent) {
                    return isset($matches[1]) ? urldecode($matches[1]) : null;
                }
            ],
            'hash'=>[
                'match'=>'/<td>Hash:<\/td>[^<]+<td>([a-f0-9]{40})<\/td>/ism',
            ],
            'file_size'=>[
                'match'=>'/<td>Total Size:<\/td>.+<td>(\d+.+?)<\/td>/ism',
                'accuracy'=>80 // 80% accuracy
            ],
            'date_created'=>[
                'match'=>'/<td>Added:<\/td>[^<]+<td>([^<]+)<\/td>/ism'
            ],
            'category'=>[
                'match'=>'/<a[^>]+class="breadcrumb">Home<\/a>[^<]*<a class="breadcrumb" href="[^"]+\/(.+?)">/ism',
                'callback'=>function($matches, $torrent) {
                    switch (strtolower($matches[1])) {
                        case 'photos':
                            return 8;
                        case 'other':
                            return 7;
                        case 'adult':
                            return 6;
                        case 'software':
                            return 5;
                        case 'games':
                            return 4;
                        case 'books':
                            return 3;
                        case 'audio':
                            return 3;
                        case 'video':
                            return 1;
                        default:
                            return null;
                    }
                },
                'site_specific'=>true // only monova category
            ],
            'user'=>[
                'match'=>'/<td>Added By:<\/td>\s*<td>\s*<a href="[^.]+?[^\/]+\/user\/[^"]+">([^<]+)<\/a>/ism',
                'site_specific'=>true // only monova user
            ],
            // 'is_verified'=>[
            // 	'match'=>'/<img[^>]+alt="(Trusted|VIP)"/ism',
            // ],
            'trackers'=>[
                'match'=>'/var torrent_magnet = "([^"]+)";/ism',
                'callback'=>function($matches, $torrent) {
                    $trackers = [];
                    if (isset($matches[1])) {
                        $matches[1] = urldecode($matches[1]);
                        preg_match_all('/&tr=([^&"]+)/i', $matches[1], $m);
                        foreach ($m[1] as $tracker) {
                            if (!in_array($tracker, $trackers))
                                $trackers[] = $tracker;
                        }
                    }
                    return $trackers;
                }
            ],
            'files'=>[
                'match'=>'/<div class="files-box">.+?<ul>(.+?)<\/ul>\s*<\/div>/ism',
                'callback'=>function($matches, $torrent) {
                    $files = []; $size = 0;
                    if (isset($matches[1])) {
                        if (strpos($matches[1], 'Paste Crack Here'))
                            printDie($matches[1]);
                        preg_match_all('/<li class="root"*>(.+)\s+\[([^\]]+)\]<\/li>/im', $matches[1], $m);
                        for ($i=0;$i<count($m[1]);$i++) {
                            $files[$i] = new \stdClass;
                            $files[$i]->path = $m[1][$i];
                            $files[$i]->file_size = $m[2][$i];
                            $tmp = new \YeTii\MultiScraper\Attributes\FileSize($m[2][$i]);
                            $size += $tmp->get();
                        }
                    }
                    if (count($files))
                        $torrent->file_count = new \YeTii\MultiScraper\Attributes\FileCount(count($files));
                    if ($size>0)
                        $torrent->file_size = new \YeTii\MultiScraper\Attributes\FileSize($size);
                    return $files;
                }
            ]
        ])
        ->addMethod([
            'extract_rows'=>[
                'match_all'=>'/<tr[^>]*>.+?<a href="[^"]+([a-f0-9]{40}).+?<\/tr>/is',
            ],
            'remove_if_deleted'=>[
                'match'=>'/(class="removed-message">)/i'
            ]
        ]),*/

];
