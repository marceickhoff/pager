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
		 * Builds the template.
		 */
		public static function build() {
			self::include_defaults(Content::file());
			if (self::is_set()) {
				include Template::file(); // Include template if set
			}
			else {
				echo Content::get(); // Show plain content without template
			}
		}

		/**
		 * Includes relevant defaults files for a given content file
		 */
		private static function include_defaults($content_file) {
			$content_file = explode('/', pathinfo($content_file, PATHINFO_DIRNAME));
			$defaults_files = array();
			do {
				$file = implode('/', $content_file).'/_defaults.php';
				if (file_exists($file)) {
					$defaults_files[] = $file;
				}
			} while (array_pop($content_file));
			foreach ($defaults_files as $defaults_file) {
				include $defaults_file;
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
			return self::is_set() ? 'theme/'.self::$template.'.php' : null;
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
			include 'theme/'.$part.'.php';
		}
	}

?>