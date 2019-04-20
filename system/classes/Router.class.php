<?php

	/**
	 * Router
	 */
	abstract class Router {

		/**
		 * Returns an array with all custom routes and their corresponding callables.
		 * You can define custom routes here.
		 * @return array
		 */
		public static function routes() {
			return [
				//Custom routes
			];
		}

		/**
		 * Redirects the client.
		 * @param string $target Relative target URL
		 * @param int $http_response_code (optional, default: 302) HTTP response code
		 */
		public static function redirect($target, $http_response_code = 302) {
			header('Location: '.BASE_PATH.'/'.$target, true, $http_response_code);
			die();
		}

		/**
		 * Refreshes the current page.
		 */
		public static function refresh() {
			header("Refresh:0");
			die();
		}
	}

?>