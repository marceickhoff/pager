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
		 * @deprecated Use custom routes instead.
		 */
		public static function set($file) {
			self::$file = $file;
		}

		/**
		 * Returns content either from file or custom route.
		 * @return string
		 */
		public static function get() {
			if (!isset(self::$content)) {
				ob_start();
				if (!self::custom_route()) {
					include self::file();
				}
				self::$content = $content = ob_get_contents();
				ob_end_clean();
			}
			return self::$content;
		}

		/**
		 * Checks if the current request matches a custom route. If a match is found, the corresponding callable will be executed.
		 * @return bool True if request matches custom route
		 */
		private static function custom_route() {
			foreach (Router::routes() as $route => $method) {
				$route = explode('/', trim($route, '/'));
				$parameters = [];
				$match = true;
				$request = Request::get();
				if ($request[count($request) - 1] === '') {
					array_pop($request);
				}
				if (count($request) != count($route)) {
					continue;
				}
				foreach ($request as $i => $request_part) {
					if ($route[$i] == $request_part) {
						continue;
					}
					else if (strlen(trim($route[$i], '{}')) == strlen($route[$i]) - 2) {
						$parameters[] = urldecode(filter_var($request_part, FILTER_SANITIZE_URL));
					}
					else {
						$match = false;
						break;
					}
				}
				if ($match) {
					call_user_func_array($method, $parameters);
					return true;
				}
			}
			return false;
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
			foreach ($request as $part) {
				if (substr($part, 0, 1) == '_') {
					http_response_code(404);
					break;
				}
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
			}
			while (!file_exists($file));
			return $file;
		}

		/**
		 * Replaces content with error page file and terminates.
		 * @param int $code HTTP status code
		 */
		public static function error($code) {
			ob_clean();
			http_response_code($code);
			include self::file();
			die();
		}
	}