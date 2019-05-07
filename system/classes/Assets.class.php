<?php

	/**
	 * Assets Manager
	 */
	abstract class Assets {
		/**
		 * @var mixed[] Assets
		 */
		private static $assets = array();

		/**
		 * Adds a new asset (CSS, JavaScript, etc.)
		 * @param string $html HTML code to embed the asset
		 * @param string (optional) $location Location identifier
		 */
		public static function add($html, $location = 'head') {
			self::$assets[$location][] = $html;
		}

		/**
		 * Gets asset information for a specific location.
		 * @param string $location Location identifier
		 * @return string HTML code to embed assets
		 */
		public static function get($location) {
			if (array_key_exists($location, self::$assets)) {
				return implode("\n", self::$assets[$location]);
			}
			return '';
		}
	}