<!doctype html>
<html lang="<?php echo Localization::get(); ?>">
<head>
	<meta charset="UTF-8">
	<title><?php echo Meta::get('website_title'); ?> &bull; <?php echo Meta::get('title'); ?></title>
	<meta name="robots" content="<?php echo Meta::get('robots'); ?>">
	<meta name="description" content="<?php echo Meta::get('description'); ?>">
	<meta name="viewport" content="width=device-width; initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<?php Assets::get('head'); ?>
</head>
<body>