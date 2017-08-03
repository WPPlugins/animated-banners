<?php
/**
  * @desc   Banner Display Manager
  * @author Leo Brown <leo@acumendevelopment.net>
  * @date   May 12th 2010
  *
  */
Class BannerManager{

	var $path;

	function BannerManager(){
		$this->path = dirname(__FILE__).'/';
	}

	function getBannersByName($name){

		$banners = array();
		$bannerResults = query_posts(array(
			'post_type'=>'animated-banner',
			'name'=>$name
		));

		foreach($bannerResults as $banner){

			// get captions
			$captions=array();
			$captionResults = wp_get_object_terms($banner->ID,'caption');
			foreach($captionResults as $caption){
				$captions[]=
					"<div class=\"banner_caption_name\"><strong>{$caption->name}</strong></div>".
					"<div class=\"banner_caption_description\">{$caption->description}</div>";
			}

			// specific options
			$meta = get_post_custom($banner->ID);
			$options = unserialize($meta['banner-options'][0]);

			$banners[]=array(
				'content'=>$banner->post_content,
				'captions'=>$captions,
				'options'=>$options
			);
		}
		return $banners;
	}

	function getBannerContent($options){

		// defaults
		if(!$options['selector']) $options['selector']='IMG';

		if(@$options['show_on']=='front_only'){
			if(function_exists('is_front_page')){
				if(!is_front_page()) return;
			}
		}

		// supporting scripts
		wp_enqueue_script('jquery');
		global $scriptLoaded;
		if(!$scriptLoaded){
			$content = '<script src="'.WP_PLUGIN_URL.'/animated-banners/displayBanner.js"></script>';
			$scriptLoaded=true;
		}

		// get our banners and captions
		$banners = $this->getBannersByName($options['name']);

		// write script and tags for these banners
		foreach($banners as $banner){

			// unique ID for js, could use the slug possibly
			$bannerID=md5(serialize($banner));

			$delay=1000;

			// Javascript Array format for Captions
			$theseCaptions = implode($banner['captions'], "','" );
			$theseCaptions = ereg_replace("\r\n", " ",$theseCaptions);;
			$theseCaptions = "['{$theseCaptions}']";

			$content.='<div class="skyscraper" id="banner_'.$bannerID.'">';
			$content.=$banner['content'];
			$content.='</div>';
			$content.="<script>
				jQuery(document).ready(function(){
			        displayBannerAfter(
					'#banner_{$bannerID} {$options['selector']}',
					{$theseCaptions},
					".json_encode($banner['options']).",
					$delay
				);
			});
			</script>";
		}

		return $content;
	}
}
?>
