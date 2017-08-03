<?php
$path =  dirname(__FILE__).'/';
require_once $path.'BannerManager.class.php';

// shortcode setup
function banner_shortcode($atts){

	// get settings
	extract(shortcode_atts(array(
		'name' => ''
	), $atts));

	// return banner content
	$bannerManager = new BannerManager;
	return $bannerManager->getBannerContent(array(
		'name'=>$name
	));
}

/**
  *
  * @desc   Move shortcode past wpautop
  * @author http://www.viper007bond.com/tag/wpautop/
  *
  */
function banner_shortcode_wrapper($content){

	global $shortcode_tags;

	// Backup current registered shortcodes and clear them all out
	$orig_shortcode_tags = $shortcode_tags;
	$shortcode_tags = array();

	add_shortcode('banner', 'banner_shortcode');

	// Do the shortcode (only the one above is registered)
	$content = do_shortcode($content);

	// Put the original shortcodes back
	$shortcode_tags = $orig_shortcode_tags;
	return $content;
}

add_filter('the_content', 'banner_shortcode_wrapper', 15);

?>
