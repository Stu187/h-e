<?php
if ( is_admin() ){ // admin actions
	add_action( 'admin_init', 'register_featured_global_settings' ); 
}
function featured_slider_gbl_settings(){
	$default_featured_slider_global_settings = get_featured_slider_global_default_settings();
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$group='featured-slider-global-group';
	$featured_slider_global_options = 'featured_slider_global_options';
	$featured_slider_global=get_option('featured_slider_global_options');
	
	foreach($default_featured_slider_global_settings as $key=>$value){
		if(!isset($featured_slider_global[$key])) $featured_slider_global[$key]='';
	}
		
?>
	<div id="global_settings">
		<h2> <?php _e('Global Settings','featured-slider'); ?> </h2>
		<form method="post" action="options.php" id="featured_slider_global_form" >
		<?php settings_fields($group); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Minimum User Level to add Post to the Slider','featured-slider'); ?></th>
				<td><select name="<?php echo $featured_slider_global_options;?>[user_level]" id="featured_slider_user_level" >
				<option value="manage_options" <?php if ($featured_slider_global['user_level'] == "manage_options"){ echo "selected";}?> ><?php _e('Administrator','featured-slider'); ?></option>
				<option value="edit_others_posts" <?php if ($featured_slider_global['user_level'] == "edit_others_posts"){ echo "selected";}?> ><?php _e('Editor and Admininstrator','featured-slider'); ?></option>
				<option value="publish_posts" <?php if ($featured_slider_global['user_level'] == "publish_posts"){ echo "selected";}?> ><?php _e('Author, Editor and Admininstrator','featured-slider'); ?></option>
				<option value="edit_posts" <?php if ($featured_slider_global['user_level'] == "edit_posts"){ echo "selected";}?> ><?php _e('Contributor, Author, Editor and Admininstrator','featured-slider'); ?></option>
				</select>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Text to display in the JavaScript disabled browser','featured-slider'); ?></th>
				<td><input type="text" name="<?php echo $featured_slider_global_options;?>[noscript]" id="featured_slider_noscript" class="regular-text code" value="<?php echo $featured_slider_global['noscript']; ?>" /></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('Embed JS files in head','featured-slider'); ?></th>
				<td>
					<div class="eb-switch eb-switchnone">
						<input type="hidden" name="<?php echo $featured_slider_global_options;?>[enque_scripts]" class="hidden_check" id="featured_slider_enque_scripts" value="<?php echo $featured_slider_global['enque_scripts'];?>">
						<input id="featured_enuescripts" class="cmn-toggle eb-toggle-round" type="checkbox" <?php checked("1", $featured_slider_global['enque_scripts']); ?> >
						<label for="featured_enuescripts"></label>
					</div>
				</td>
			</tr>

				<?php if($featured_slider_global['custom_post'] == 1) $showchk = "showSelected";
					else $showchk = "";
				      if($featured_slider_global['custom_post'] == 0) $hidechk = "hideSelected";
					else $hidechk = "";
				?>
			
			<tr valign="top"> 
			<th scope="row"><?php _e('Create "SliderVilla Slides" Custom Post Type','featured-slider'); ?></th> 
				<td><select name="<?php echo $featured_slider_global_options;?>[custom_post]" id="featured_slider_custom_post">
		<option value="1" <?php if ($featured_slider_global['custom_post'] == "1"){ echo "selected";}?> ><?php _e('Yes','featured-slider'); ?></option>
		<option value="0" <?php if ($featured_slider_global['custom_post'] == "0"){ echo "selected";}?> ><?php _e('No','featured-slider'); ?></option>
				</select>
				</td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><?php _e('Custom Post Type Slug','featured-slider'); ?></th>
				<td><input type="text" name="<?php echo $featured_slider_global_options;?>[cpost_slug]" id="featured_slider_cpost_slug" value="<?php echo $featured_slider_global['cpost_slug']; ?>" />
				</td>
			</tr>

			
			<tr valign="top">
				<th scope="row"><?php _e('Remove Featured Slider Metabox on','featured-slider'); ?></th>
				<td>
					<select name="<?php echo $featured_slider_global_options;?>[remove_metabox][]" multiple="multiple" size="3" style="min-height:6em;">
				<?php 
				$args=array(
				  'public'   => true
				); 
				$output = 'objects'; // names or objects, note names is the default
				$post_types=get_post_types($args,$output); $remove_post_type_arr=$featured_slider_global['remove_metabox'];
				if(!isset($remove_post_type_arr) or !is_array($remove_post_type_arr) ) $remove_post_type_arr=array();
						foreach($post_types as $post_type) { ?>
						  <option value="<?php echo $post_type->name;?>" <?php if(in_array($post_type->name,$remove_post_type_arr)){echo 'selected';} ?>><?php echo $post_type->labels->name;?></option>
						<?php } ?>
				</select>
				<span class="moreInfo">
					&nbsp; <span class="trigger"> ? </span>
					<div class="tooltip">
					<?php _e('You can select single/multiple post types using Ctrl+Mouse Click. To deselect a single post type, use Ctrl+Mouse Click','featured-slider'); ?>
					</div>
				</span>
				</td>
			</tr>
				
			<tr valign="top">
				<th scope="row"><?php _e('Custom Styles','featured-slider'); ?></th>
				<td><textarea name="<?php echo $featured_slider_global_options;?>[css]"  id="featured_slider_css" rows="5" class="regular-text code"><?php echo $featured_slider_global['css']; ?></textarea>
				<span class="moreInfo">
					&nbsp; <span class="trigger"> ? </span>
					<div class="tooltip">
					<?php _e('custom css styles that you would want to be applied to the slider elements','featured-slider'); ?>
					</div>
				</span>
			</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Show Slider Details on Featured Slider Admin Pages','featured-slider'); ?></th>
				<td><select name="<?php echo $featured_slider_global_options;?>[support]" id="featured_slider_support">
				<option value="1" <?php if ($featured_slider_global['support'] == "1"){ echo "selected";}?> ><?php _e('Yes','featured-slider'); ?></option>
				<option value="0" <?php if ($featured_slider_global['support'] == "0"){ echo "selected";}?> ><?php _e('No','featured-slider'); ?></option>
				</select>
				</td>
			</tr> 
		</table>
		<p class="submit">
			<input type="submit" name="Save" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>	
	
		<h2 id="api-keys"><?php _e('API Keys','featured-slider'); ?></h2>
		<div id="fbkey">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<span class="fa fa-facebook-square"></span><?php _e('Facebook App key','featured-slider'); ?>
					</th>
					<td><input type="text" name="<?php echo $featured_slider_global_options;?>[fb_app_key]" id="featured_slider_fb_appid" value="<?php echo $featured_slider_global['fb_app_key']; ?>" />
					<input type="submit" name="Save Global" class="button-primary" value="<?php _e('Save') ?>" />
					</td>
				</tr>
			</table>
		</div>

		<div id="instakey">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<span class="fa fa-instagram"></span><?php _e('Instagram Client Id','featured-slider'); ?>
					</th>
					<td><input type="text" name="<?php echo $featured_slider_global_options;?>[insta_client_id]" id="featured_slider_insta_client" value="<?php echo $featured_slider_global['insta_client_id']; ?>" />
					<input type="submit" name="Save Global" class="button-primary" value="<?php _e('Save') ?>" />
					</td>
				</tr>
			</table>
		</div>

		<div id="flickrkey">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<span class="fa fa-flickr"></span><?php _e('Flickr API Key','featured-slider'); ?></th>
					<td><input type="text" name="<?php echo $featured_slider_global_options;?>[flickr_app_key]" id="featured_slider_flickr_key" value="<?php echo $featured_slider_global['flickr_app_key']; ?>" />
					<input type="submit" name="Save Global" class="button-primary" value="<?php _e('Save') ?>" />
					</td>
				</tr>
			</table>
		</div>	

		<div id="youtubekey">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<span class="fa fa-youtube"></span><?php _e('Youtube App ID','featured-slider'); ?></th>
					<td><input type="text" name="<?php echo $featured_slider_global_options;?>[youtube_app_id]" id="featured_slider_youtube_appid" value="<?php echo $featured_slider_global['youtube_app_id']; ?>" />
					<input type="submit" name="Save Global" class="button-primary" value="<?php _e('Save') ?>" />
					</td>
				</tr>
			</table>
		</div>
		<div id="pxkey">
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<img src="<?php echo featured_slider_plugin_url( 'images/500px.png' ); ?>" width="13" height="14" />&nbsp; <?php _e('500PX Consumer Key','featured-slider'); ?>
					</th>
					<td><input type="text" name="<?php echo $featured_slider_global_options;?>[px_ckey]" id="featured_slider_px_ckey" value="<?php echo $featured_slider_global['px_ckey']; ?>" />
					<input type="submit" name="Save Global" class="button-primary" value="<?php _e('Save') ?>" />
					</td>
				</tr>
			</table>
		</div>
	</form>	
</div>
<?php 
}
function register_featured_global_settings() { // whitelist options
  	  register_setting( 'featured-slider-global-group', 'featured_slider_global_options' );
}
?>
