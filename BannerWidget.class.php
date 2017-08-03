<?php
/*
 * @desc   Widget for Animated Banners
 * @author Leo Brown
 */
Class Banner_Widget extends WP_Widget{

	var $bannerManager;

	function Banner_Widget(){
		$this->bannerManager = new BannerManager();
		$widget_ops = array( 'classname' => 'banner', 'description' => 'Grabs abitrary HTML from other pages/sites');
		$control_ops = array( 'width' => 200, 'height' => 350, 'id_base' => 'banner-widget' );
		$this->WP_Widget( 'banner-widget', 'Banner Widget', $widget_ops, $control_ops );
	}

	function widget($args, $instance){

		extract($args);
		$title = apply_filters('widget_title', $instance['title'] );

		echo $before_widget;
		if($title) echo $before_title . $title . $after_title;

		// widget display
		print $this->bannerManager->getBannerContent(array(
			'name'=>$instance['name'],
			'selector'=>$instance['selector'],
			'show_on'=>$instance['show_on']
		));

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['name'] = strip_tags( $new_instance['name'] );
		$instance['selector'] = strip_tags( $new_instance['selector'] );
		$instance['show_on'] = $new_instance['show_on'];

		if(!$instance['selector']) $instance['selector']='IMG';
		return $instance;
	}

	function form($instance){?>
		<p><table width="100%">
			<tr>
			<td><label for="<?php echo $this->get_field_id('name'); ?>">Banner</label></td>
			<td align="right"><select id="<?php echo $this->get_field_id('name'); ?>"
				name="<?php echo $this->get_field_name('name'); ?>">
				<?php
					$banners = query_posts(array('post_type'=>'animated-banner'));
					if($banners) foreach($banners as $banner){
						$selected = ($banner->post_title == $instance['name'])?'SELECTED':'';
						echo "<option {$selected} value=\"{$banner->post_title}\">{$banner->post_title}</option>\n";
					}
				?>
			</select></td>
			</tr>

			<tr>
			<td><label for="<?php echo $this->get_field_id('show_on'); ?>">Show on</label></td>
			<td align="right"><select id="<?php echo $this->get_field_id('show_on'); ?>"
				name="<?php echo $this->get_field_name('show_on'); ?>">
				<option>All Pages</option>
				<option value="front_only" <?php if($instance['show_on']=='front_only') echo 'SELECTED';?>>Front Page Only</option>
			</select></td>
			</tr>

			<tr>
			<td><label for="<?php echo $this->get_field_id('selector'); ?>">Selector</label></td>
			<td align="right"><input id="<?php echo $this->get_field_id('selector'); ?>"
				name="<?php echo $this->get_field_name('selector'); ?>"
				value="<?php echo $instance['selector']; ?>"
				style="width:100px;"
			/></td>
			</tr>

			</table>
		</p>	
	<?php }
}

// get WP to load our widget
function init_banner(){
	register_widget('Banner_Widget');
}
add_action("widgets_init", "init_banner");
?>
