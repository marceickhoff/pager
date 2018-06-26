<?php

	/**
	 * Class Config
	 */
	abstract class Config {
		/**
		 * @var array Configurations
		 */
		private static $config = array();

		/**
		 * @param string $key [optional] Config key (returns all if empty)
		 * @param mixed $default Output this if key is not set
		 * @return mixed Configuration or default value
		 */
		public static function get($key = null, $default = false) {
			if (empty($key)) {
				return self::$config;
			}
			if (array_key_exists($key, self::$config)) {
				return self::$config[$key];
			}
			return $default;
		}

		/**
		 * @param string|array $key Config key or config array
		 * @param mixed $value [optional] Config value
		 */
		public static function set($key, $value = null) {
			if (is_array($key)) {
				self::$config = $key;
			}
			else {
				self::$config[$key] = $value;
			}
		}
	}

?>