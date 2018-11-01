<?php

	/**
	 * Request Manager
	 */
	abstract class Request {
		/**
		 * @var array Request
		 */
		private static $request = array();

		/**
		 * Returns the current request as a string.
		 * @return string Current request
		 */
		public static function get_string() {
			return implode('/', self::get());
		}

		/**
		 * Returns the current request as an array.
		 * @return array Current request
		 */
		public static function get() {
			if (count(self::$request)) {
				$request = self::$request;
			}
			else {
				$request = substr($_SERVER['REQUEST_URI'], strlen(Config::get('base_path', '')));
				$request = ltrim($request, '/');
				$request = explode('/', $request);
				if (in_array(reset($request), Localization::get_supported())) {
					Localization::override(current($request));
					array_shift($request);
				}
				else if (!Localization::is_default() or Config::get('default_localization_redirect', true)) {
					$request = implode('/', $request);
					header('Location: '.(isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].Config::get('base_path', '').'/'.Localization::get().'/'.$request, true, 303);
					die();
				}
			}
			return $request;
		}

		/**
		 * Checks if the current request starts with a specific string.
		 * @param string $path String to check
		 * @return bool
		 */
		public static function starts_with($path) {
			$request = self::get_string();
			return substr($request, 0, strlen($path)) == $path;
		}
	}

?>