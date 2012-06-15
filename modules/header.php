<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
	<title>WretchedLocket &raquo; <?php app::echo_page_title(); ?></title>
	<meta name="keywords" content="<?php app::echo_meta_keywords(); ?>" />
	<meta name="description" content="<?php app::echo_meta_description(); ?>" />
	<link rel="stylesheet" href="<?= url::assets(); ?>/css/style.css" id="main-style" type="text/css" />
	<link rel="stylesheet" href="<?= url::assets(); ?>/css/forms.css" id="forms-style" type="text/css" />
	<?php app::css(); ?>
</head>
<body>
<div class="content">
	<?php if ( !app::is_home() ) { ?>
	<a href="<?php echo url::root(); ?>">Home</a>
	<?php } ?>
	<a href="<?php echo url::root(); ?>/test">Test</a>
	<a href="<?php echo url::root(); ?>/test-form">A Form</a>
	<a href="<?php echo url::root(); ?>/account">Account (Session Protected)</a>