<?php

/**
 * Router
 */
abstract class Router {

	/**
	 * @var string|null Base path
	 */
	private static $base_path = null;

	/**
	 * Returns an array with all custom routes and their corresponding callables.
	 * @return array
	 */
	public static function routes() {
		return Config::get('custom_routes', []);
	}

	/**
	 * Adds custom routes.
	 * @param string|array $url URL or array with routes
	 * @param mixed (unneeded if array provided in first parameter) $callable Function or method to call
	 */
	public static function add($url, $callable = null) {
		$routes = Config::get('custom_routes', []);
		if (is_array($url)) {
			$routes = array_merge($routes, $url);
			Config::set('custom_routes', $routes);
		}
		else {
			Config::set('custom_routes', $routes[$url] = $callable);
		}
	}

	/**
	 * Checks if the current request matches a custom route. If a match is found, the corresponding callable will be executed.
	 * @return bool True if request matches custom route
	 */
	public static function custom_route() {
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
				elseif (strlen(trim($route[$i], '{}')) == strlen($route[$i]) - 2) {
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
	 * Redirects the client.
	 * @param string $target Target URL
	 */
	public static function redirect($target) {
		header('Location: '.$target, true, 302);
		die();
	}

	/**
	 * Refreshes the current page.
	 */
	public static function refresh() {
		header("Refresh:0");
		die();
	}

	/**
	 * Returns the path where the system is installed relative to the document root.
	 * @return string
	 */
	public static function base_path() {
		if (self::$base_path === null) {
			self::$base_path = rtrim(str_replace('\\', '/', pathinfo($_SERVER['PHP_SELF'], PATHINFO_DIRNAME)), '/');
		}
		return self::$base_path;
	}

	/**
	 * Creates a (fully qualified) URL from a local page path.
	 * @param string $path Path (without leading slash)
	 * @param bool $fully_qualified (optional) Prepend protocol and host name
	 * @return string
	 */
	public static function url($path, $fully_qualified = false) {
		$path = ltrim($path, '/');
		$url = self::base_path().'/'.$path;
		if ($fully_qualified) {
			$url = 'http'.(isset($_SERVER['HTTPS']) ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$url;
		}
		return $url;
	}
}