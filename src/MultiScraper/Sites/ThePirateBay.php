<?php
namespace YeTii\MultiScraper\Sites;

class ThePirateBay {

	protected $instance;
	protected $url = 'https://thepiratebay.org';
	protected $scrapable = [
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
		// 'images',
		'category',
		'user'
	];

	function __get(string $name) {
		return isset($this->{$name}) ? $this->{$name} : null;
	}

	function __set(string $name, $value) {
		$this->{$name} = $value;
	}

	function __construct($instance) {
		$this->instance = $instance;
	}

	public function latest($page = 1) {
		print 'latest<br>';
		$this->do('/recent/'.($page-1));
	}

	public function do($endpoint) {
		$html = $this->instance->getHtml($this->url.$endpoint);
		$ids = $this->getTorrentsFromHtml($html);
		foreach ($ids as $id) {
			$t = $this->scrapeTorrent($id);
		}
	}

	private function getTorrentsFromHtml(string $html) {
		$torrents = [];
		preg_match_all('/<tr[\s\S]*?<\/tr>/', $html, $m); // match all rows
		foreach ($m[0] as $row) { // each table row
			if (preg_match('/\/torrent\/\d+/', $row)) { // is row
				if (preg_match('/<a href="\/torrent\/(\d+)\//i', $row, $m) && $id = $m[1]) { // get id
					$torrents[] = $id;
				}
			}
			if (count($torrents)>5)
				return $torrents;
		}
		return $torrents;
	}

	public function scrapeTorrent($id) {
		$torrent = new \YeTii\MultiScraper\Torrent($this->instance, ['tpb_id'=>$id, 'source'=>'ThePirateBay']);
		$torrent->fill();
		printDie($torrent);
	}

	public function torrent($torrent) {
		if (!isset($torrent->tpb_id) || !$torrent->tpb_id) return $torrent;
		$html = $this->instance->getHtml($this->url."/torrent/{$torrent->tpb_id}/");
		$torrent->html = $html;

		foreach ($this->scrapable as $key) {
			$getter = $this->instance->getGetter($key);
			if (!isset($torrent->{$key}) && method_exists($this, $getter))
				$torrent->{$key} = $this->{$getter}($torrent);
		}
		$torrent->tpb_id = new \YeTii\MultiScraper\Attributes\TpbId($torrent->tpb_id);
		if (is_string($torrent->hash))
			$torrent->hash = new \YeTii\MultiScraper\Attributes\Hash($torrent->hash);

		unset($torrent->html);
		return $torrent;
	}



	/* ============================== GETTERS ============================== */
	private function getTitle($torrent) {
		preg_match('/<div id="title">(.+?)<\/div>/ism', $torrent->html, $m);
		return isset($m[1]) ? new \YeTii\MultiScraper\Attributes\Title(trim($m[1])) : null;
	}

	private function getMagnetLink($torrent) {
		preg_match('/class="download">.+?href="(magnet:\?xt[^"]+)"/ism', $torrent->html, $m);
		return isset($m[1]) ? new \YeTii\MultiScraper\Attributes\MagnetLink($m[1]) : null;
	}

	private function getHash($torrent) {
		preg_match('/class="download">.+?href="[^"]+?([a-f0-9]{40})/ism', $torrent->html, $m);
		return isset($m[1]) ? new \YeTii\MultiScraper\Attributes\Hash($m[1]) : null;
	}

	private function getFileCount($torrent) {
		preg_match('/<dt>Files:<\/dt>.+?<a[^>]+>(\d+)/ism', $torrent->html, $m);
		return isset($m[1]) ? new \YeTii\MultiScraper\Attributes\FileCount($m[1]) : null;
	}

	private function getFileSize($torrent) {
		preg_match('/<dt>Size:<\/dt>.+?<dd>.+\((\d+)(?:\s|&nbsp;)Bytes\)/ism', $torrent->html, $m);
		return isset($m[1]) ? new \YeTii\MultiScraper\Attributes\FileSize($m[1]) : null;
	}

	private function getDateCreated($torrent) {
		preg_match('/<dt>Uploaded:<\/dt>.+?<dd>(.+?)<\/dd>/ism', $torrent->html, $m);
		return isset($m[1]) ? new \YeTii\MultiScraper\Attributes\DateCreated($m[1]) : null;
	}

	private function getDescription($torrent) {
		preg_match('/<div class="nfo">(.+?)<\/div>.+?<div class="download">/ism', $torrent->html, $m);
		// .'<br>Screens: http://www.moviedeskback.com/wp-content/uploads/2016/01/Gods_of_Egypt_HD_Screencaps-7.png'
		return isset($m[1]) ? new \YeTii\MultiScraper\Attributes\Description($m[1]) : null;
	}

	private function getCategory($torrent) {
		preg_match('/<a href="\/browse\/(\d+)/ism', $torrent->html, $m);
		return isset($m[1]) ? new \YeTii\MultiScraper\Attributes\TpbCategory($m[1]) : null;
	}

	private function getUser($torrent) {
		preg_match('/<dt>By:<\/dt>.+?<a[^>]+?>(.+?)<\/a>/ism', $torrent->html, $m);
		return isset($m[1]) ? new \YeTii\MultiScraper\Attributes\TpbUser($m[1]) : null;
	}

	private function getIsVerified($torrent) {
		preg_match('/<img[^>]+alt="(Trusted|VIP)"/ism', $torrent->html, $m);
		return isset($m[1]) ? new \YeTii\MultiScraper\Attributes\IsVerified($m[1]) : null;
	}

	// private function getImages($torrent) {
	// 	if (!$this->instance->rules['image_extract']) return null;
	// 	$description = $this->getDescription($torrent);
	// 	return new \YeTii\MultiScraper\Attributes\Images($this->instance->extractImages($description));
	// }

	private function getTrackers($torrent) {
		$trackers = [];
		if (preg_match('/<a[^>]+href="(magnet:?[^"]+)"/', $torrent->html, $magnet)) {
			if (preg_match_all('/[\?\&]tr=([^\s\&]+)/i', $magnet[1], $m)) {
				foreach ($m[1] as $t) {
					if (!in_array($t, $trackers))
						$trackers[] = $t;
				}
			}
		}
		return new \YeTii\MultiScraper\Attributes\Trackers($trackers);
	}

	private function getFiles($torrent) {
		$files = [];
		$html = $this->instance->getHtml($this->url.'/ajax_details_filelist.php?id='.$torrent->tpb_id.'&turing=iamhuman');
		if ($html) {
			if (preg_match_all('/<tr><td[^>]+>([^<]+)<\/td><td[^>]+>([^<]+)/i', $html, $m)) {
				for ($i=0;$i<count($m[1]);$i++) { 
					$file = new \stdClass;
					$file->path = $m[1][$i];
					$file->file_size = $m[2][$i];
					$files[] =  $file;
				}
			}
		}
		return new \YeTii\MultiScraper\Attributes\Files($files);
	}



}