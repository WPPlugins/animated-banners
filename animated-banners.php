<?php
/*
Plugin Name: Animated Banners
Plugin URI: http://www.acumendevelopment.net
Description: Animated Banners for WP3.0
Author: Leo Brown
Version: 0.1
Author URI: http://www.acumendevelopment.net
*/

// load dependencies
$path = dirname(__FILE__).'/';
require_once $path.'BannerManager.class.php';	// Core banner features such as displaying banners
require_once $path.'BannerShortcode.php';	// Shortcode support
require_once $path.'BannerAdmin.class.php';	// Backend custom post type support
require_once $path.'BannerWidget.class.php';	// Widget admin options

?>
