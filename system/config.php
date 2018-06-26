<?php
	Config::set(
		[
			'base_path' => trim(str_replace('\\', '/', pathinfo($_SERVER['PHP_SELF'], PATHINFO_DIRNAME)), '/'), //Replace backslashes for Windows support
			'default_localization' => 'en',
			'default_localization_redirect' => false
		]
	);
?>