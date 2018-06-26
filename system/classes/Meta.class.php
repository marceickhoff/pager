<?php

	/**
	 * Class Meta
	 */
	class Meta {
		private static $meta = array();

		/**
		 * Sets meta information.
		 * @param mixed[]|string $data Meta data (array) or single meta identifier (string)
		 * @param string $content Meta content
		 */
		public static function set($data, $content = '') {
			if (is_array($data)) {
				self::$meta = array_merge(self::$meta, $data);
			}
			else {
				self::$meta[$data] = $content;
			}
		}

		/**
		 * Gets meta information.
		 * @param string $key Meta identifier
		 * @return mixed Meta information
		 */
		public static function get($key = null) {
			if (array_key_exists($key, self::$meta)) {
				return self::$meta[$key];
			}
			return '';
		}
	}

?>