<?php

	/**
	 * Template Manager
	 */
	abstract class Template {
		/**
		 * @var string Current template name
		 */
		private static $template;

		/**
		 * Initializes the template.
		 */
		public static function init() {
			self::include_directory_defaults(Content::file());
			Content::get(); // Parse content before template
			if (self::is_set()) {
				include Template::file(); // Include template if set
			}
			else {
				echo Content::get(); // Show plain content without template
			}
		}

		/**
		 * Includes relevant config files for a given content file
		 */
		private static function include_directory_defaults($content_file) {
			$parts = explode('/', pathinfo($content_file, PATHINFO_DIRNAME));
			$files = array();
			do {
				$file = implode('/', $parts).'/_directory.php';
				if (file_exists($file)) {
                    $files[] = $file;
				}
			}
			while (array_pop($parts));
			foreach (array_reverse($files) as $file) {
				include $file;
			}
		}

		/**
		 * Checks if a template is set.
		 * @return bool
		 */
		public static function is_set() {
			return !empty(self::$template);
		}

		/**
		 * Returns the current template.
		 * @return string|null Current template file or null if no template is set
		 */
		public static function file() {
			return self::is_set() ? __DIR__.'/../templates/'.self::$template.'.php' : null;
		}

		/**
		 * Sets the template.
		 * @param string $name New template name
		 */
		public static function set($name) {
			self::$template = $name;
		}

		/**
		 * Gets the current template.
		 * @return string Template name
		 */
		public static function get() {
			return self::$template;
		}

		/**
		 * Includes a template part.
		 * @param string $part Template part name
		 */
		public static function part($part) {
			include __DIR__.'/../templates/'.$part.'.php';
		}
	}

?>