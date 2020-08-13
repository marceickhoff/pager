<!doctype html>
<html lang="<?php echo Localization::get(); ?>">
<head>
	<meta charset="UTF-8">
	<title><?php echo Meta::get('title'); ?> &bull; <?php echo Meta::get('website_title'); ?></title>
	<meta name="robots" content="<?php echo Meta::get('robots'); ?>">
	<meta name="description" content="<?php echo Meta::get('description'); ?>">
	<link rel="stylesheet" href="<?= Router::url('style.css') ?>?>"
	<?php Assets::get('head'); ?>
</head>
<body>