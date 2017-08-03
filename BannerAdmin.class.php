<?php

// Initiate the plugin
add_action("init", "AnimatedBannersInit");
function AnimatedBannersInit() { global $banner; $banner = new AnimatedBanners(); }

/**
  * @desc   Banner Custom Post Type
  * @author Leo Brown <leo@acumendevelopment.net>
  * @date   May 12th 2010
  *
  */
class AnimatedBanners {

	var $meta_fields = array("banner-options");
	var $path;

	var $defaults=array(
	        'padding'        =>'60px 0px 60px 0px',
        	'textAlign'      =>'center',
	        'fontSize'       =>'15px',
	        'foreColour'     =>'#eee',
        	'maskColour'     =>'#000',
	        'maskOpacity'    =>0.4,
	        'slideDuration'  =>800,
        	'fadeInDuration' =>200,
	        'holdDuration'   =>2000,
        	'fadeOutDuration'=>200
	);

	/**
	  * @desc Constructor; initialise post type, register taxonomy, and setup filters
	  *	
	  */
	function AnimatedBanners()
	{
		$this->path = dirname(__FILE__).'/';

		// Register custom post types
		register_post_type('animated-banner', array(
			'label' => __('Banners'),
			'singular_label' => __('Banner'),
			'public' => true,
			'show_ui' => true, // UI in admin panel
			'_builtin' => false, // It's a custom post type, not built in
			'_edit_link' => 'post.php?post=%d',
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => array("slug" => "animated-banner"), // Permalinks
			'query_var' => "animated-banner", // This goes to the WP_Query schema
			'supports' => array('title','editor')
		));
		
		add_filter("manage_edit-animated-banner_columns", array(&$this, "edit_columns"));
		add_action("manage_posts_custom_column", array(&$this, "custom_columns"));
		
		// Register custom taxonomy
		register_taxonomy("caption", array("animated-banner"), array(
			"hierarchical" => true,
			"label" => "Captions",
			"singular_label" =>
			"Caption",
			"rewrite" => true
		));

		// Admin interface init
		add_action("admin_init", array(&$this, "admin_init"));
		add_action("template_redirect", array(&$this, 'template_redirect'));
		
		// Insert post hook
		add_action("wp_insert_post", array(&$this, "wp_insert_post"), 10, 2);
	}

	/**
	  * @desc  Provide columns on the Edit Banners admin panel
	  */
	function edit_columns(){

		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => "Animated Banner Title",
			"banner_description" => "Description",
			"banner_options" => "Options",
			"banner_captions" => "Captions",
		);		
		return $columns;
	}
	
	/**
	  * @desc  Provide data for a given column of the Edit Banners Admin Panel
	  * @param @column string Column to provide data for
	  */
	function custom_columns($column)
	{
		global $post;
		switch ($column)
		{
			case "banner_description":
				the_excerpt();
				break;
//			case "banner_options":
//				$custom = get_post_custom();
//				echo $custom["options"][0];
//				break;
			case "banner_captions":
				$captions = get_the_terms(0, "caption");
				$captions_html = array();
				if($captions) foreach ($captions as $caption)
					array_push($captions_html, '<a href="' . get_term_link($caption->slug, "caption") . '">' . $caption->name . '</a>');
				
				echo implode($captions_html, ", ");
				break;
		}
	}
	
	/**
	  * @desc  Display this banner directly - not supported
	  */
	function template_redirect(){

		global $wp;
		if ($wp->query_vars["post_type"] == "animated-banner"){
			print "This plugin does not support previewing banners yet.";
		}
	}
	
	/**
	  * @desc  Insert post action
	  * @param $post_id int Post ID that's being saved
	  * @param $post object Post Object that's being saved
	  */
	function wp_insert_post($post_id, $post = null){

		if ($post->post_type == "animated-banner"){

			// Loop through the POST data
			foreach ($this->meta_fields as $key){
				$value = @$_POST[$key];
				if (empty($value)){
					delete_post_meta($post_id, $key);
					continue;
				}

				if (!update_post_meta($post_id, $key, $value)){
					add_post_meta($post_id, $key, $value);
				}
			}
		}
	}
	
	/**
	  * @desc  Initalise meta area on the admin loader
	  */
	function admin_init(){

		// Custom meta boxes for the edit animated-banner screen
		add_meta_box(
			"banner-meta",
			"Information",
			array(&$this, "meta_options"),
			"animated-banner",
			"advanced", //side
			"high" //"low"
		);
	}
	
	/**
	  * @desc  Prepare contents of the meta area in the Banner Admin (loads HTML template)
	  */
	function meta_options(){
		global $post;
		$custom = get_post_custom($post->ID);
		$options = unserialize($custom['banner-options'][0]);

		foreach($this->defaults as $name=>$value){
			if(!$thisVal = $options[$name]) $thisVal = $value;
			print "<span style='display:inline-block;width:130px'>{$name}</span>";
			print "<input type=\"text\" name=\"banner-options[{$name}]\" value=\"{$thisVal}\" /> <br />";
		}
	}
}
?>
