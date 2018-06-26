<?php

	/**
	 * Sitemap Manager
	 */
	class Sitemap {
		const file = 'sitemap.xml';

		/**
		 * Updates or adds an entry to the sitemap.
		 * @param string[] $args
		 * @param string $url (optional) URL to add (current page URL by default)
		 * @return bool
		 */
		public static function update(Array $args = array(), $url = null) {
			if (empty($url)) {
				$url = $_SERVER['REQUEST_URI'];
			}
			$loc = null;
			$lastmod = null;
			$changefreq = null;
			$priority = null;
			if (array_key_exists('loc', $args)) {
				$loc = $args['loc'];
			}
			if (array_key_exists('lastmod', $args)) {
				$lastmod = $args['lastmod'];
			}
			if (array_key_exists('changefreq', $args)) {
				$changefreq = $args['changefreq'];
			}
			if (array_key_exists('priority', $args)) {
				$priority = $args['priority'];
			}
			if (!file_exists(self::file)) {
				$file = fopen(self::file, 'w');
				fwrite($file, '<?xml version="1.0"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');
				fclose($file);
			}
			$sitemap = simplexml_load_file(self::file);
			$sitemap_array = json_decode(json_encode($sitemap), true);
			$xml = new \SimpleXMLElement('<urlset/>');
			$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			$changed = false;
			if (array_key_exists('url', $sitemap_array) and array_key_exists('loc', $sitemap_array['url'])) {
				$sitemap_array = array('url' => $sitemap_array['url']);
				foreach ($sitemap_array['url'] as $sitemap_url) {
					//if (empty($url['loc'])) continue;
					$url_xml = $xml->addChild('url');
					if ($sitemap_url['loc'] == $url) {
						if (!empty($loc)) {
							$sitemap_url['loc'] = $loc;
						}
						if (!empty($lastmod)) {
							$sitemap_url['lastmod'] = $lastmod;
						}
						if (!empty($changefreq)) {
							$sitemap_url['changefreq'] = $changefreq;
						}
						if (!empty($priority)) {
							$sitemap_url['priority'] = $priority;
						}
						$changed = true;
					}
					$url_xml->addChild('loc', $sitemap_url['loc']);
					if (!empty($sitemap_url['lastmod'])) {
						$url_xml->addChild('lastmod', $sitemap_url['lastmod']);
					}
					if (!empty($sitemap_url['changefreq'])) {
						$url_xml->addChild('changefreq', $sitemap_url['changefreq']);
					}
					if (!empty($sitemap_url['priority'])) {
						$url_xml->addChild('priority', $sitemap_url['priority']);
					}
				}
			}
			if (!$changed) {
				$url_xml = $xml->addChild('url');
				if (empty($loc)) {
					$loc = $url;
				}
				$url_xml->addChild('loc', $loc);
				if (!empty($lastmod)) {
					$url_xml->addChild('lastmod', $lastmod);
				}
				if (!empty($changefreq)) {
					$url_xml->addChild('changefreq', $changefreq);
				}
				if (!empty($priority)) {
					$url_xml->addChild('priority', $priority);
				}
			}
			$new_sitemap = $xml->asXML();
			file_put_contents('sitemap.xml', $new_sitemap);
			return true;
		}

		/**
		 * Removes an entry from the sitemap.
		 * @param $loc
		 * @return bool
		 */
		public function remove($loc) {
			$xml = new \SimpleXMLElement('<urlset/>');
			$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			$sitemap = simplexml_load_file(self::file);
			$sitemap_array = json_decode(json_encode($sitemap), true);
			if (array_key_exists('loc', $sitemap_array['url'])) {
				$sitemap_array['url'] = array($sitemap_array['url']);
			}
			foreach ($sitemap_array['url'] as $url) {
				if ($url['loc'] != $loc) {
					$url_xml = $xml->addChild('url');
					$url_xml->addChild('loc', $url['loc']);
					if (!empty($url['lastmod'])) {
						$url_xml->addChild('lastmod', $url['lastmod']);
					}
					if (!empty($url['changefreq'])) {
						$url_xml->addChild('changefreq', $url['changefreq']);
					}
					if (!empty($url['priority'])) {
						$url_xml->addChild('priority', $url['priority']);
					}
				}
			}
			$new_sitemap = $xml->asXML();
			file_put_contents('sitemap.xml', $new_sitemap);
			return true;
		}
	}

?>