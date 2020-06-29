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
         * Creates a list with all possible content file locations based on request and localization
         * @return array Array with page file paths
         */
        private static function file_list() {
            $files = [];
            foreach (array_unique([
                Localization::get(),
                Localization::get_language(),
                Config::get('default_localization', 'en')
            ]) as $localization) {
                $files[] = __DIR__.'/../content/'.$localization.'/_error/'.http_response_code().'.php';
                $request_string = Request::get_string();
                $request_string_trimmed = rtrim($request_string, '/');
                $tmp = [
                    __DIR__.'/../content/'.$localization.'/'.$request_string_trimmed.'.php',
                    __DIR__.'/../content/'.$localization.'/'.$request_string_trimmed.'/index.php'
                ];
                if (strlen($request_string) and $request_string{-1} == '/') {
                    $tmp = array_reverse($tmp); //Check directory index first if last character of request is "/"
                }
                $files = array_merge($files, $tmp);
            }
            return $files;
        }

        /**
         * Searches for a matching content file based on request and localization
         * @return string|bool Page file path or false on failure
         */
        public static function file() {
            if (!empty(self::$file)) {
                return self::$file;
            }
            foreach (Request::get() as $part) {
                if (strlen($part) and $part{0} == '_') {
                    http_response_code(404);
                    break;
                }
            }
            $files = self::file_list();
            while(!file_exists($file = current($files))) {
                if (!next($files)) {
                    if (http_response_code() == 404) {
                        break;
                    }
                    http_response_code(404);
                    reset($files);
                }
            }
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