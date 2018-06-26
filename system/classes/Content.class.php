<?php

	/**
	 * Content Manager
	 */
	abstract class Content {
		/**
		 * @var string Page file
		 */
		private static $file = null;

		/**
		 * @var string Buffered HTML content of current page
		 */
		private static $content;

		/**
		 * Overrides the current page file.
		 * @param string $file Page file
		 */
		public static function set($file) {
			self::$file = $file;
		}

		/**
		 * Returns content.
		 * @return string
		 */
		public static function get() {
			if (!isset(self::$content)) {
				ob_start();
				include self::file();
				self::$content = $content = ob_get_contents();
				ob_end_clean();
			}
			return self::$content;
		}

		/**
		 * @return string|bool Page file path or false on failure
		 */
		public static function file() {
			if (!empty(self::$file)) {
				return self::$file;
			}
			$iteration = 0;
			$request = Request::get();
			$localization = Localization::get();
			$default_localization = Config::get('default_localization', 'en');
			$file = false;
			if (empty(end($request))) {
				$request[key($request)] = 'index';
			}
			else if (end($request) == '_defaults' or $request[0] == '_error') {
				http_response_code(404);
			}
			$request = implode('/', $request);
			do {
				switch ($iteration) {
					case 0:
					case 10:
					case 12:
						if ($http_response = http_response_code() != 200) {
							$file = 'content/pages/'.$localization.'/_error/'.http_response_code().'.php';
						}
						break;
					case 1:
					case 4:
					case 7:
						$file = 'content/pages/'.$localization.'/'.$request.'.php';
						break;
					case 2:
					case 5:
					case 8:
						$file = 'content/pages/'.$localization.'/'.$request.'/index.php';
						break;
					case 3:
						if (Localization::get_region()) {
							$localization = Localization::get_language();
						}
						else {
							$iteration = 5;
						}
						break;
					case 6:
						if ($localization != $default_localization) {
							$localization = $default_localization;
						}
						else {
							$iteration = 8;
						}
						break;
					case 9:
						header('HTTP/1.1 404 Not Found', true, 404);
						break;
					case 11:
						header('HTTP/1.1 500 Internal Server Error', true, 500);
						break;
					default:
						die();
				}
				$iteration++;
			} while (!file_exists($file));
			return $file;
		}
	}

?>