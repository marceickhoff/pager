<?php

	/**
	 * Localization Manager
	 */
	abstract class Localization {
		/**
		 * @var string Language code
		 */
		private static $language;

		/**
		 * @var string Region code
		 */
		private static $region;

		/**
		 * @var string[] All supported languages
		 */
		private static $supported = array();

		/**
		 * @return bool Checks if the current localization is the default localization
		 */
		public static function is_default() {
			return self::get() == Config::get('default_localization');
		}

		/**
		 * Returns the current localization
		 * @return string Localization in RFC 5646 format
		 */
		public static function get() {
			if (!empty($language = self::get_language()) and !empty($region = self::get_region())) {
				return implode('-', [$language, $region]);
			}
			else if (!empty($language)) {
				return $language;
			}
			else {
				return self::negotiate();
			}
		}

		/**
		 * @return string Current language
		 */
		public static function get_language() {
			if (!isset(self::$language)) {
				self::negotiate();
			}
			return self::$language;
		}

		/**
		 * Negotiates the most fitting localization.
		 * @return string Negotiated localization in RFC 5646 format
		 */
		private static function negotiate() {
			$supported_languages = self::get_supported();
			foreach ($supported_languages as $code) {
				$pos = strpos(' '.$_SERVER['HTTP_ACCEPT_LANGUAGE'], $code);
				if (intval($pos) != 0) {
					$position[$code] = intval($pos);
				}
			}
			$negotiated_localization = $supported_languages[0];
			if (!empty($position)) {
				foreach ($supported_languages as $code) {
					if (isset($position[$code]) &&
						$position[$code] == min($position)) {
						$negotiated_localization = $code;
					}
				}
			}
			self::override($negotiated_localization);
			return $negotiated_localization;
		}

		/**
		 * Returns all supported localizations
		 * @return string[] Supported localizations
		 */
		public static function get_supported() {
			if (!count(self::$supported)) {
				$directories = glob('content/pages/*');
				foreach ($directories as $directory) {
					if (is_dir($directory)) {
						self::$supported[] = pathinfo($directory, PATHINFO_BASENAME);
					}
				}
			}
			return self::$supported;
		}

		/**
		 * Overrides the current localization
		 * @param string $localization New localization in RFC5646 format
		 */
		public static function override($localization) {
			$localization = explode('-', $localization, 2);
			self::$language = $localization[0];
			self::$region = empty($localization[1]) ? false : $localization[1];
			setlocale(LC_ALL, $localization);
		}

		/**
		 * @return string Current region
		 */
		public static function get_region() {
			if (!isset(self::$region)) {
				self::negotiate();
			}
			return self::$region;
		}
	}

?>