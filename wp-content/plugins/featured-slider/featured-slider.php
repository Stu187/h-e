<?php
/****************************************************************************************************************
Plugin Name: Featured Slider
Plugin URI: http://slidervilla.com/featured-slider/
Description: Featured Slider adds a very attractive slider of five featured blocks to any location of your blog.
Version: 3.0
Author: SliderVilla
Author URI: http://slidervilla.com/
Wordpress version supported: 3.5 and above
*-----------------------------------------*
* Copyright 2010-2015  SliderVilla  (email : support@slidervilla.com)
* Developers: Sukhada, Tejaswini (@WebFanzine Media)
* Testing by: Sagar, Sanjeev (@WebFanzine Media)
************************************************************************************************************************/
// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a slider plugin, not much I can do when called directly.';
	exit;
}
global $slidervillaSliders;
define('FEATURED_SLIDER_TABLE','featured_slider'); //Slider TABLE NAME
define('FEATURED_SLIDER_META','featured_slider_meta'); //Meta TABLE NAME
define('FEATURED_SLIDER_POST_META','featured_slider_postmeta'); //Meta TABLE NAME
define("FEATURED_SLIDER_VER",'3.0',false);//Current Version of Featured Slider
if ( ! defined( 'FEATURED_SLIDER_PLUGIN_BASENAME' ) )
	define( 'FEATURED_SLIDER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
if ( ! defined( 'FEATURED_SLIDER_CSS_DIR' ) )
	define( 'FEATURED_SLIDER_CSS_DIR', WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/var/skins/' );
if ( ! defined( 'FEATURED_SLIDER_CSS_OUTER' ) )
	define( 'FEATURED_SLIDER_CSS_OUTER', WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/var/' );
if ( ! defined( 'FEATURED_SLIDER_INC_DIR' ) )
	define( 'FEATURED_SLIDER_INC_DIR', WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/includes/' );
$slidervillaSliders['featured-slider']= FEATURED_SLIDER_VER;
// Create Text Domain For Translations
load_plugin_textdomain('featured-slider', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
require_once (dirname (__FILE__) . '/includes/featured-slider-functions.php');

function install_featured_slider() {
	global $wpdb, $table_prefix;
	$installed_ver = get_option( "featured_db_version" );
	if( $installed_ver != FEATURED_SLIDER_VER ) {
		$table_name = $table_prefix.FEATURED_SLIDER_TABLE;
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			$sql = "CREATE TABLE $table_name (
				id int(5) NOT NULL AUTO_INCREMENT,
				post_id int(11) NOT NULL,
				date datetime NOT NULL,
				slider_id int(5) NOT NULL DEFAULT '1',
				slide_order int(5) NOT NULL DEFAULT '0',
				UNIQUE KEY id(id)
			);";
			$rs = $wpdb->query($sql);
		}

	   	$meta_table_name = $table_prefix.FEATURED_SLIDER_META;
		if($wpdb->get_var("show tables like '$meta_table_name'") != $meta_table_name) {
			$sql = "CREATE TABLE $meta_table_name (
				slider_id int(5) NOT NULL AUTO_INCREMENT,
				slider_name varchar(100) NOT NULL default '',
				UNIQUE KEY slider_id(slider_id)
			);";
			$rs2 = $wpdb->query($sql);

			$sql = "INSERT INTO $meta_table_name (slider_id,slider_name) VALUES('1','Featured Slider');";
			$rs3 = $wpdb->query($sql);
		}
	
		if($wpdb->get_var("SHOW COLUMNS FROM $meta_table_name LIKE 'type'") != 'type') {
			// Add Columns 
			$sql = "ALTER TABLE $meta_table_name
			ADD COLUMN type INT(4) NOT NULL,
			ADD COLUMN setid INT(4) NOT NULL default '1',
			ADD COLUMN param varchar(500)";
			$rs5 = $wpdb->query($sql);
		}
	
		$slider_postmeta = $table_prefix.FEATURED_SLIDER_POST_META;
		if($wpdb->get_var("show tables like '$slider_postmeta'") != $slider_postmeta) {
			$sql = "CREATE TABLE $slider_postmeta (
						post_id int(11) NOT NULL,
						slider_id int(5) NOT NULL default '1',
						UNIQUE KEY post_id(post_id)
					);";
			$rs4 = $wpdb->query($sql);
		}
	   // Featured Slider Settings and Options
	   $default_slider = array();
	   $default_featured_slider_global_settings = get_featured_slider_global_default_settings();
	   $default_featured_slider_settings=get_featured_slider_default_settings();
	   $default_slider = $default_featured_slider_settings;
	   $glb_slider = $default_featured_slider_global_settings;
	   	   $default_scounter='1';
		   $scounter=get_option('featured_slider_scounter');
		   if(!isset($scounter) or $scounter=='' or empty($scounter)){
		   	update_option('featured_slider_scounter',$default_scounter);
			$scounter=$default_scounter;
		   }
		   $fontgarr = array('title_fontg','ptitle_fontg','sub_ptitle_fontg','content_fontg','meta_title_fontg');
		   for($i=1;$i<=$scounter;$i++){
		       if ($i==1){
			    $featured_slider_options='featured_slider_options';
			   }
			   else{
			    $featured_slider_options='featured_slider_options'.$i;
			   }
			   $featured_slider_curr=get_option($featured_slider_options);
		   				 
			   if(!$featured_slider_curr and $i==1) {
				 $featured_slider_curr = array();
			   }
		
			   if($featured_slider_curr or $i==1) {
				   foreach($default_slider as $key=>$value) {
					  if(!isset($featured_slider_curr[$key])) {
						 $featured_slider_curr[$key] = $value;
					  }
					  if(in_array($key,$fontgarr) && !empty($featured_slider_curr["$key"]) ){
						$fname = str_replace( ' ', '+', $featured_slider_curr["$key"] );
						$featured_slider_curr[$key]=$fname;
						if (strpos($featured_slider_curr["$key"],':') !== false) {
							$gfontarr = explode( ':', $featured_slider_curr["$key"] );
							$featured_slider_curr["$key"] = str_replace( ' ', '+', $gfontarr[0]);
							$featured_slider_curr["$key"."w"] = $gfontarr[1];
						}
					  }
					  if( $key=='t_font' && !empty($featured_slider_curr['title_fontg']) ) {
						$featured_slider_curr[$key]='google';
					  }
					  if( $key=='pt_font' && !empty($featured_slider_curr['ptitle_fontg']) ) {
						$featured_slider_curr[$key]='google';
					  }
					  if( $key=='sub_pt_font' && !empty($featured_slider_curr['sub_ptitle_font']) ) {
						$featured_slider_curr[$key]='google';
					  }
					  if( $key=='pc_font' && !empty($featured_slider_curr['content_fontg']) ) {
						$featured_slider_curr[$key]='google';
					  }
					  if( $key=='mt_font' && !empty($featured_slider_curr['meta_title_fontg']) ) {
						$featured_slider_curr[$key]='google';
					  }
				   }
				   update_option($featured_slider_options,$featured_slider_curr);
				   update_option( "featured_db_version", FEATURED_SLIDER_VER);
			   }
		   } //end for loop
			/* Global settings - start */
			$featured_slider_curr=get_option('featured_slider_options');
			$garr = array();
			if($featured_slider_curr) {
				foreach($featured_slider_curr as $key=>$value) {
					if($key=='user_level' || $key=='noscript' || $key=='multiple_sliders' || $key=='enque_scripts' || $key=='custom_post' || $key=='remove_metabox' || $key=='css' || $key=='support' || $key == 'cpost_slug') {
						$garr[$key] = $value;
					}
			
				}
			}
			$featured_slider_gcurr=get_option('featured_slider_global_options');
		   	if(!$featured_slider_gcurr) {
				$featured_slider_gcurr = array();
			}
			foreach($glb_slider as $key=>$value) {
				if(!isset($featured_slider_gcurr[$key])) {
					$featured_slider_gcurr[$key] = $value;
				}
			}
			if( count($garr) > 0 ) {
				$msliders=0;
				foreach($garr as $key=>$value) {
					$featured_slider_gcurr[$key] = $value;
					if($key=='multiple_sliders') {
						$featured_slider_gcurr[$key]=1;
					}
					if($key=='custom_post') {
						$featured_slider_gcurr[$key]=1;
					}
				}
			}
			update_option('featured_slider_global_options',$featured_slider_gcurr);
			/* Global settings - end */
	}//end of if db version chnage
}
register_activation_hook( __FILE__, 'install_featured_slider' );
/* Added for auto update - start */
function featured_update_db_check() {
	if (get_option('featured_db_version') != FEATURED_SLIDER_VER) {
		install_featured_slider();
	}
    	/* Check whether Featured Slider Options are created (if not) add options */
	if(get_option('featured_slider_options') == false) {
		$default_featured_slider_settings=get_featured_slider_default_settings();
		add_option('featured_slider_options',$default_featured_slider_settings);
	}
	if(get_option('featured_slider_global_options') == false) {
		$default_featured_slider_global_settings = get_featured_slider_global_default_settings();
		add_option('featured_slider_global_options',$default_featured_slider_global_settings);
	}
}
add_action('plugins_loaded', 'featured_update_db_check');
/* Added for auto update - end */
require_once (dirname (__FILE__) . '/includes/featured-slider-meta-functions.php');
require_once (dirname (__FILE__) . '/includes/sslider-get-the-image-functions.php');
require_once (FEATURED_SLIDER_INC_DIR.'help.php');

//This adds the post to the slider
function featured_add_to_slider($post_id) {
$gfeatured_slider = get_option('featured_slider_global_options');
$featured_slider = get_option('featured_slider_options');
 if(isset($_POST['featured-sldr-verify']) and current_user_can( $gfeatured_slider['user_level'] ) ) {
	global $wpdb, $table_prefix, $post;
	$table_name = $table_prefix.FEATURED_SLIDER_TABLE;
	
	if( !isset($_POST['featured-slider']) and  is_post_on_any_featured_slider($post_id) ){
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		 $wpdb->query($sql);
	}
	
	if(isset($_POST['featured-slider']) and !isset($_POST['featured_slider_name'])) {
	  $slider_id = '1';
	  if(is_post_on_any_featured_slider($post_id)){
	     $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		 $wpdb->query($sql);
	  }
	  
	  if(isset($_POST['featured-slider']) and $_POST['featured-slider'] == "featured-slider" and !featured_slider($post_id,$slider_id)) {
		$dt = date('Y-m-d H:i:s');
		$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$post_id', '$dt', '$slider_id')";
		$wpdb->query($sql);
	  }
	}
	if(isset($_POST['featured-slider']) and $_POST['featured-slider'] == "featured-slider" and isset($_POST['featured_slider_name'])){
	  $slider_id_arr = $_POST['featured_slider_name'];
	  $post_sliders_data = featured_ss_get_post_sliders($post_id);
	  
	  foreach($post_sliders_data as $post_slider_data){
		if(!in_array($post_slider_data['slider_id'],$slider_id_arr)) {
		  $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		  $wpdb->query($sql);
		}
	  }

		foreach($slider_id_arr as $slider_id) {
			if(!featured_slider($post_id,$slider_id)) {
				$dt = date('Y-m-d H:i:s');
				$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$post_id', '$dt', '$slider_id')";
				$wpdb->query($sql);
			}
		}
	}
	
	$table_name = $table_prefix.FEATURED_SLIDER_POST_META;
	if(isset($_POST['featured_display_slider']) and !isset($_POST['featured_display_slider_name'])) {
	  	$slider_id = '1';
	}
	if(isset($_POST['featured_display_slider']) and isset($_POST['featured_display_slider_name'])){
	  	$slider_id = $_POST['featured_display_slider_name'];
	}
  	if(isset($_POST['featured_display_slider'])){	
		  if(!featured_ss_post_on_slider($post_id,$slider_id)) {
		    	$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		    	$wpdb->query($sql);
			$sql = "INSERT INTO $table_name (post_id, slider_id) VALUES ('$post_id', '$slider_id')";
			$wpdb->query($sql);
		  }
	}
	$thumbnail_key = $featured_slider['img_pick'][1];
	$featured_sslider_thumbnail = get_post_meta($post_id,$thumbnail_key,true);
	$post_slider_thumbnail=isset($_POST['featured_sslider_thumbnail'])?$_POST['featured_sslider_thumbnail']:'';
	if($featured_sslider_thumbnail != $post_slider_thumbnail) {
	  	update_post_meta($post_id, $thumbnail_key, $_POST['featured_sslider_thumbnail']);	
	}
	
	$featured_link_attr = get_post_meta($post_id,'featured_link_attr',true);
	$link_attr = isset($_POST['featured_link_attr'])?$_POST['featured_link_attr']:'';
	if($link_attr != '') $link_attr=html_entity_decode($link_attr,ENT_QUOTES);
	if($featured_link_attr != $link_attr) {
	  	update_post_meta($post_id, 'featured_link_attr', $link_attr);	
	}
	
	$featured_sslider_link = get_post_meta($post_id,'featured_slide_redirect_url',true);
	$link=isset($_POST['featured_sslider_link'])?$_POST['featured_sslider_link']:'';
	if($featured_sslider_link != $link) {
	  	update_post_meta($post_id, 'featured_slide_redirect_url', $link);	
	}
	
	$featured_sslider_nolink = get_post_meta($post_id,'featured_sslider_nolink',true);
	//3.0 : Debug error
	$post_featured_sslider_nolink = isset( $_POST['featured_sslider_nolink'] ) ? $_POST['featured_sslider_nolink'] : '';
	if($featured_sslider_nolink != $post_featured_sslider_nolink) {
	  	update_post_meta($post_id, 'featured_sslider_nolink', $post_featured_sslider_nolink);	
	}

	/* Added For Image Transitions  */
	$featured_img_transition = get_post_meta($post_id,'_featured_img_transition',true);
	$img_transition= isset($_POST['featured_img_transition'])?$_POST['featured_img_transition']:'';
	if($featured_img_transition != $img_transition) {
		update_post_meta($post_id, '_featured_img_transition', $img_transition);	
	}
	$featured_img_duration = get_post_meta($post_id,'_featured_img_duration',true);
	$img_duration= isset($_POST['featured_img_duration'])?$_POST['featured_img_duration']:'';
	if($featured_img_duration != $img_duration) {
		update_post_meta($post_id, '_featured_img_duration', $img_duration);	
	}
	$featured_img_delay = get_post_meta($post_id,'_featured_img_delay',true);
	$img_delay= isset($_POST['featured_img_delay'])?$_POST['featured_img_delay']:'';
	if($featured_img_delay != $img_delay) {
		update_post_meta($post_id, '_featured_img_delay', $img_delay);	
	}
	/* Added For Title Transitions  */
	$featured_title_transition = get_post_meta($post_id,'_featured_title_transition',true);
	$title_transition= isset($_POST['featured_title_transition'])?$_POST['featured_title_transition']:'';
	if($featured_title_transition != $title_transition) {
		update_post_meta($post_id, '_featured_title_transition', $title_transition);	
	}
	$featured_title_duration = get_post_meta($post_id,'_featured_title_duration',true);
	$title_duration= isset($_POST['featured_title_duration'])?$_POST['featured_title_duration']:'';
	if($featured_title_duration != $title_duration) {
		update_post_meta($post_id, '_featured_title_duration', $title_duration);	
	}
	$featured_title_delay = get_post_meta($post_id,'_featured_title_delay',true);
	$title_delay= isset($_POST['featured_title_delay'])?$_POST['featured_title_delay']:'';
	if($featured_title_delay != $title_delay) {
		update_post_meta($post_id, '_featured_title_delay', $title_delay);	
	}
	/* Added For Content Transitions  */
	$featured_content_transition = get_post_meta($post_id,'_featured_content_transition',true);
	$content_transition= isset($_POST['featured_content_transition'])?$_POST['featured_content_transition']:'';
	if($featured_content_transition != $content_transition) {
		update_post_meta($post_id, '_featured_content_transition', $content_transition);	
	}
	$featured_content_duration = get_post_meta($post_id,'_featured_content_duration',true);
	$content_duration= isset($_POST['featured_content_duration'])?$_POST['featured_content_duration']:'';
	if($featured_content_duration != $content_duration) {
		update_post_meta($post_id, '_featured_content_duration', $content_duration);	
	}
	$featured_content_delay = get_post_meta($post_id,'_featured_content_delay',true);
	$content_delay= isset($_POST['featured_content_delay'])?$_POST['featured_content_delay']:'';
	if($featured_content_delay != $content_delay) {
		update_post_meta($post_id, '_featured_content_delay', $content_delay);	
	}
	/* Added for embed shortcode - start */
	
	$featured_disable_image = get_post_meta($post_id,'_featured_disable_image',true);
	//3.0 : Debug error
	$post_featured_disable_image = isset( $_POST['featured_disable_image'] ) ? $_POST['featured_disable_image'] : '';
	if($featured_disable_image != $post_featured_disable_image) {
	  	update_post_meta($post_id, '_featured_disable_image', $post_featured_disable_image);	
	}
	$featured_sslider_eshortcode = get_post_meta($post_id,'_featured_embed_shortcode',true);
	$post_featured_sslider_eshortcode = isset($_POST['featured_sslider_eshortcode'])?$_POST['featured_sslider_eshortcode']:'';
	if($featured_sslider_eshortcode != $post_featured_sslider_eshortcode) {
	  	update_post_meta($post_id, '_featured_embed_shortcode', $post_featured_sslider_eshortcode);	
	}
	/* Added for embed shortcode -end */
	$featured_select_set = get_post_meta($post_id,'_featured_select_set',true);
	$post_featured_select_set = isset($_POST['featured_select_set'])?$_POST['featured_select_set']:'';
	if($featured_select_set != $post_featured_select_set and $post_featured_select_set!='0') {
	  	update_post_meta($post_id, '_featured_select_set', $post_featured_select_set);	
	}
  } //featured-sldr-verify
}

//Removes the post from the slider, if you uncheck the checkbox from the edit post screen
function featured_remove_from_slider($post_id) {
if(isset($_POST['featured-sldr-verify'])) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.FEATURED_SLIDER_TABLE;
	
	// authorization
	if (!current_user_can('edit_post', $post_id))
		return $post_id;
	// origination and intention
	if (!wp_verify_nonce($_POST['featured-sldr-verify'], 'FeaturedSlider'))
		return $post_id;
	
   		if(empty($_POST['featured-slider']) and is_post_on_any_featured_slider($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
		}

		$display_slider = isset( $_POST['featured_display_slider'] ) ? $_POST['featured_display_slider'] : '';
		$table_name = $table_prefix.FEATURED_SLIDER_POST_META;
		if(empty($display_slider) and featured_ss_slider_on_this_post($post_id)){
		  $sql = "DELETE FROM $table_name where post_id = '$post_id'";
			    $wpdb->query($sql);
		}
	}  
}
  
function featured_delete_from_slider_table($post_id){
    global $wpdb, $table_prefix;
	$table_name = $table_prefix.FEATURED_SLIDER_TABLE;
    if(is_post_on_any_featured_slider($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
	}
	$table_name = $table_prefix.FEATURED_SLIDER_POST_META;
    if(featured_ss_slider_on_this_post($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
	}
}

// Slider checkbox on the admin page

function featured_slider_edit_custom_box(){
   featured_add_to_slider_checkbox();
}

function featured_slider_add_custom_box() {
$gfeatured_slider = get_option('featured_slider_global_options');
 if (current_user_can( $gfeatured_slider['user_level'] )) {
	if( function_exists( 'add_meta_box' ) ) {
	    $post_types=get_post_types(); 
	    	if (isset ($gfeatured_slider['remove_metabox']))
		$remove_post_type_arr=( isset($gfeatured_slider['remove_metabox']) ? $gfeatured_slider['remove_metabox'] : '' );
		if(!isset($remove_post_type_arr) or !is_array($remove_post_type_arr) ) $remove_post_type_arr=array();
		foreach($post_types as $post_type) {
			if(!in_array($post_type,$remove_post_type_arr)){
				add_meta_box( 'featured_slider_box', __( 'Featured Slider' , 'featured-slider'), 'featured_slider_edit_custom_box', $post_type, 'advanced' );
			}
		}
		//add_meta_box( $id,   $title,     $callback,   $page, $context, $priority ); 
	} 
 }
}
/* Use the admin_menu action to define the custom boxes */
add_action('admin_menu', 'featured_slider_add_custom_box');

function featured_add_to_slider_checkbox() {
	global $post;
	$featured_slider = get_option('featured_slider_options');
	$gfeatured_slider = get_option('featured_slider_global_options');
	//for WPML start
	if( function_exists('icl_plugin_action_links') ) {
		if( isset($_GET['source_lang']) && isset($_GET['trid']) ) {
			global $wpdb, $table_prefix;
			$id = $wpdb->get_var( "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid=".$_GET['trid']." AND language_code='".$_GET['source_lang']."'" );			
			$table_name = $table_prefix.FEATURED_SLIDER_TABLE;
			$q = "select * from $table_name where post_id=".$id;
			$res = $wpdb->get_results($q);
			if( count($res) > 0 ) {
				$sarr=array();
				foreach($res as $re) {
					$sarr[] = $re->slider_id;
				}
				echo "<script type='text/javascript'>";
				echo "jQuery(document).ready(function($) {";
					echo "jQuery('.featured-psldr-post').prop('checked','true');";
					$sliders = featured_ss_get_sliders();
					foreach ($sliders as $slider) { 
						if(in_array($slider['slider_id'],$sarr)) {
							echo "jQuery('#featured_slider_name".$slider['slider_id']."').prop('checked','true');";
						}
					}
				echo "});";
				echo "</script>";
			}
		}
	}
	//for WPML end
	if (current_user_can( $gfeatured_slider['user_level'] )) {
		$extra = "";
		
		$post_id = $post->ID;
		
		if(isset($post->ID)) {
			$post_id = $post->ID;
			if(is_post_on_any_featured_slider($post_id)) { $extra = 'checked="checked"'; }
		} 
		
		$post_slider_arr = array();
		
		$post_sliders = featured_ss_get_post_sliders($post_id);
		if($post_sliders) {
			foreach($post_sliders as $post_slider){
			   $post_slider_arr[] = $post_slider['slider_id'];
			}
		}
		
		$sliders = featured_ss_get_sliders();
		$featured_sslider_link= get_post_meta($post_id, 'featured_slide_redirect_url', true);  
		$featured_sslider_nolink=get_post_meta($post_id, 'featured_sslider_nolink', true);
		$thumbnail_key = (isset($featured_slider['img_pick'][1]) ? $featured_slider['img_pick'][1] : 'featured_slider_thumbnail');
        	$featured_sslider_thumbnail= get_post_meta($post_id, $thumbnail_key, true); 
		$featured_link_attr=get_post_meta($post_id, 'featured_link_attr', true);

		/* Transitions */
		$featured_img_transition=get_post_meta($post_id, '_featured_img_transition', true);
		$featured_img_duration=get_post_meta($post_id, '_featured_img_duration', true);
		$featured_img_delay=get_post_meta($post_id, '_featured_img_delay', true);
		$featured_title_transition=get_post_meta($post_id, '_featured_title_transition', true);
		$featured_title_duration=get_post_meta($post_id, '_featured_title_duration', true);
		$featured_title_delay=get_post_meta($post_id, '_featured_title_delay', true);
		$featured_content_transition=get_post_meta($post_id, '_featured_content_transition', true);
		$featured_content_duration=get_post_meta($post_id, '_featured_content_duration', true);
		$featured_content_delay=get_post_meta($post_id, '_featured_content_delay', true);
		/* END Transitions */
		$featured_disable_image=get_post_meta($post_id, '_featured_disable_image', true);
		$featured_embed_shortcode=get_post_meta($post_id, '_featured_embed_shortcode', true);
		$featured_select_set=get_post_meta($post_id, '_featured_select_set', true);
		/* Post Meta Box Style */
		wp_enqueue_style( 'featured-meta-box', featured_slider_plugin_url( 'var/css/featured-meta-box.css' ), false, FEATURED_SLIDER_VER, 'all');
?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			jQuery("#featured_basic").css({"height":"auto","color":"#444","background":"#FFFFFF"});
			jQuery(".featured-tab").click(function() {
				var idex = jQuery(this).index();
				jQuery(".featured-tab-content").fadeOut("fast");
				jQuery(".featured-tab").css({"height":"17px","color":"#0074a2","background": "#F5F5F5"});
				jQuery(".featured-tab-content:eq("+idex+")").fadeIn("fast");
				jQuery(this).css({"height":"auto","color":"#444","background":"#FFFFFF"});
			});
			 jQuery(".show-all").click(function() {
				jQuery(this).fadeOut("fast");
				jQuery(".slider-name").fadeIn("slow");
				return false;
			});
			jQuery('a.featured-help').click(function(e) {
				e.preventDefault(); jQuery('#contextual-help-link').click();
				jQuery('#tab-link-featured-edtposthelp a').click();
			});
			jQuery(".featured-add-to-slider").click(function() {
				var added = 0;
				jQuery(".featured-add-to-slider").each(function() {
					if(jQuery(this).prop("checked") == true) { added = added + 1; }
				});
				if( added >= 1 ) {
					jQuery(".featured-psldr-post").prop("checked", true );
				} else { 
					jQuery(".featured-psldr-post").prop("checked", false );
				}
			});
		});
		</script>
		
		<div class="featured-tabs">
			<div id="featured_basic" class="featured-tab"><?php _e('Basic','featured-slider'); ?></div>
			<div id="featured_transitions" class="featured-tab"><?php _e('Transitions','featured-slider'); ?></div>
			<div id="featured_advanced" class="featured-tab last"><?php _e('Advanced','featured-slider'); ?></div>
		</div>
		<div id="featured_basic_tab" class="featured-tab-content" style="padding: 0 10px;background: #ffffff;">
		<div class="slider_checkbox">
		<table class="form-table" style="margin: 0;">
				<tr valign="top">
				<a class="featured-help" herf="#"><?php _e('Help ?','featured-slider'); ?></a>
				</tr>
				<tr valign="top">
				<td scope="row">
					<input id="featured-slider" name="featured-slider" class="featured-psldr-post" type="checkbox" value="featured-slider" <?php echo $extra;?> >
					<label><?php _e('Add this post/page to','featured-slider'); ?></label>
				</td>
				<td>
                		<?php $i = 0;
				foreach ($sliders as $slider) { 
					if($i < 3) $display="display:block;"; else $display="display:none;"; ?>
					<div style="margin-bottom: 16px;float: left;width: 100%;<?php echo $display;?>" class="slider-name">
					<span style="float: left;margin-right: 20px;min-width: 100px;"><?php echo $slider['slider_name'];?></span>
					<input id="featured_slider_name<?php echo $slider['slider_id'];?>" name="featured_slider_name[]" class="featured-meta-toggle featured-meta-toggle-round featured-add-to-slider" type="checkbox" value="<?php echo $slider['slider_id'];?>" <?php if(in_array($slider['slider_id'],$post_slider_arr)){echo 'checked';} ?> >
					<label for="featured_slider_name<?php echo $slider['slider_id'];?>"></label>
					</div>
                		<?php $i++;
				} if($i > 3) { ?>
					<a href="" class='show-all'>Show All</a>
				<?php } ?>
               				<input type="hidden" name="featured-sldr-verify" id="featured-sldr-verify" value="<?php echo wp_create_nonce('FeaturedSlider');?>" />
				</td>
				</tr>
				<tr valign="top">
                <td scope="row"><label for="featured_sslider_link"><?php _e('Slide Link URL ','featured-slider'); ?></label></td>
                <td><input type="text" name="featured_sslider_link" class="featured_sslider_link" value="<?php echo $featured_sslider_link;?>" size="50" /><br /><small><?php _e('If left empty, it will be by default linked to the permalink.','featured-slider'); ?></small> </td>
				</tr>

				<tr valign="top">
				<td scope="row"><label for="featured_sslider_nolink"><?php _e('Disable Slide Link','featured-slider'); ?> </label></td>
                		<td><input id="featured_sslider_nolink" name="featured_sslider_nolink" class="featured-meta-toggle featured-meta-toggle-round" type="checkbox" value="1" <?php if($featured_sslider_nolink=='1'){echo "checked";}?> >
				<label for="featured_sslider_nolink"></label> </td>
				</tr>
				</table>
				</div>
		</div>
	<div id="featured_transition_tab" class="featured-tab-content" style="display:none;background: #ffffff;">
		<table class="form-table">
			<tr valign="top">
                		<th scope="row" colspan="2" class="tab-title">
					<label for="featured-slider"><?php _e('Slide Image','featured-slider'); ?> </label>
				</th>
			</tr>
			<tr valign="top">
                		<td scope="row"><label for="featured_img_transition"><?php _e('Transition','featured-slider'); ?></label></td>
               			<td> <?php echo get_featured_transitions('featured_img_transition',$featured_img_transition); ?> </td>
			</tr>
			<tr valign="top">
				<td scope="row"><label for="featured_img_duration"><?php _e('Duration (seconds)','featured-slider'); ?> </label></td>
               			<td><input type="number" name="featured_img_duration" value="<?php echo $featured_img_duration;?>" style="width:60px" /></td>
			</tr>
			<tr valign="top">
				<td scope="row"><label for="featured_img_delay"><?php _e('Delay (seconds)','featured-slider'); ?> </label></td>
               			<td><input type="number" name="featured_img_delay" value="<?php echo $featured_img_delay;?>" style="width:60px" /></td>
			</tr>
		</table>
		<div style="background: #F5F5F5;margin: 0 10px;">
		<table class="form-table">
			<tr valign="top">
                		<th scope="row" colspan="2" class="tab-title" >
					<label for="featured-slider"><?php _e('Slide Title','featured-slider'); ?> </label>
				</th>
			</tr>
			<tr valign="top">
                		<td scope="row"><label for="featured_title_transition"><?php _e('Transition','featured-slider'); ?></label></td>
               			<td> <?php echo get_featured_transitions('featured_title_transition',$featured_title_transition); ?> </td>
			</tr>
			<tr valign="top">
				<td scope="row"><label for="featured_title_duration"><?php _e('Duration (seconds)','featured-slider'); ?> </label></td>
               			<td><input type="number" name="featured_title_duration" value="<?php echo $featured_title_duration;?>" style="width:60px"  /></td>
			</tr>
			<tr valign="top">
				<td scope="row"><label for="featured_title_delay"><?php _e('Delay (seconds)','featured-slider'); ?> </label></td>
               			<td><input type="number" name="featured_title_delay" value="<?php echo $featured_title_delay;?>" style="width:60px" /></td>
			</tr>
		</table>
		</div>
		<table class="form-table">
			<tr valign="top">
                		<th scope="row" colspan="2" class="tab-title" >
					<label for="featured-slider"><?php _e('Slide Content','featured-slider'); ?> </label>
				</th>
			</tr>
			<tr valign="top">
                		<td scope="row"><label for="featured_content_transition"><?php _e('Transition','featured-slider'); ?></label></td>
               			<td> <?php echo get_featured_transitions('featured_content_transition',$featured_content_transition); ?> </td>
			</tr>
			<tr valign="top">
				<td scope="row"><label for="featured_content_duration"><?php _e('Duration (seconds)','featured-slider'); ?> </label></td>
               			<td><input type="number" name="featured_content_duration" value="<?php echo $featured_content_duration;?>" style="width:60px" /></td>
			</tr>
			<tr valign="top">
				<td scope="row"><label for="featured_content_delay"><?php _e('Delay (seconds)','featured-slider'); ?> </label></td>
               			<td><input type="number" name="featured_content_delay" value="<?php echo $featured_content_delay;?>" style="width:60px" /></td>
			</tr>
		</table>	
	</div>
        <div id="featured_advanced_tab" class="featured-tab-content" style="display:none;background: #ffffff;padding: 0 10px;">
	  <?php
		$scounter=get_option('featured_slider_scounter');
		$settingset_html='<option value="0" selected >Select the Settings</option>';
		for($i=1;$i<=$scounter;$i++) { 
			 if($i==$featured_select_set){$selected = 'selected';} else{$selected='';}
			   if($i==1){
			     $settings='Default Settings';
				 $settingset_html =$settingset_html.'<option value="1" '.$selected.'>'.$settings.'</option>';
			   }
			   else{
				  if($settings_set=get_option('featured_slider_options'.$i))
					$settingset_html =$settingset_html.'<option value="'.$i.'" '.$selected.'>'.$settings_set['setname'].' (ID '.$i.')</option>';
			   }
		} ?>
	<div class="slider_checkbox">
	<table class="form-table" style="margin:0;">
		<tr valign="top">
			<td scope="row"><label for="featured_sslider_thumbnail"><?php _e('Custom Image url','featured-slider'); ?></label></td>
        		<td><input type="text" name="featured_sslider_thumbnail" class="featured_sslider_thumbnail" value="<?php echo $featured_sslider_thumbnail;?>" size="50" /></td>
		</tr>
		<!-- Added for disable thumbnail image - Start -->
		<tr valign="top">
			<td scope="row">
				<span style="float: left;margin-right: 20px;min-width: 100px;"><?php _e('Hide Image','featured-slider'); ?> </span>
			</td>
			<td>
				<input id="featured_disable_image" name="featured_disable_image" class="featured-meta-toggle featured-meta-toggle-round" type="checkbox" value="1" <?php if($featured_disable_image=='1'){echo "checked";}?> >
				<label for="featured_disable_image"></label>
			</td>
		</tr>
		<!-- Added for disable thumbnail image - end -->
		<tr valign="top">
                	<td scope="row">
				<label for="featured_link_attr"><?php _e('Slide Link (anchor) attributes ','featured-slider'); ?></label>
			</td>
                	<td>
				<input type="text" name="featured_link_attr" class="featured_link_attr" value="<?php echo htmlentities($featured_link_attr,ENT_QUOTES);?>" size="50" /><br /><small><?php _e('e.g. target="_blank" rel="external nofollow"','featured-slider'); ?></small>
			</td>
		</tr>
	</table>
	<!-- Added for embed shortcode - Start -->
	<div>
		<label for="embed_shortcode" style="font-size: 14px;"><?php _e('Shortcode to Replace Image','featured-slider'); ?> </label>
		<div style="font-size: 10px;color: #222222;margin-left: 20px;">e.g YouTube video shortcode to replace slide image.</div>
	
		<textarea rows="4" cols="50" name="featured_sslider_eshortcode" style="margin-left: 270px;"><?php echo htmlentities( $featured_embed_shortcode, ENT_QUOTES);?></textarea>
	</div>
	<!-- Added for video for embed shortcode - end -->
	<table class="form-table" style="width:520px;">
		<tr valign="top">
			<td scope="row">	
				<input id="featured_display_slider" name="featured_display_slider" class="featured-meta-toggle featured-meta-toggle-round" type="checkbox"  value="1" <?php if(featured_ss_slider_on_this_post($post_id)){echo "checked";}?> >
				<label for="featured_display_slider"></label>		
				<span><?php _e('Force Display Slider','featured-slider'); ?></span>
			</td>
			<td>
				<select name="featured_display_slider_name">
					<?php foreach ($sliders as $slider) { ?>
					  <option value="<?php echo $slider['slider_id'];?>" <?php if(featured_ss_post_on_slider($post_id,$slider['slider_id'])){echo 'selected';} ?>><?php echo $slider['slider_name'];?></option>
					<?php } ?>
				</select> 
			</td>
		</tr>
         	<tr valign="top">
		       	<td scope="row">
				<label for="featured_setting_set"><?php _e('Force Apply Setting','featured-slider'); ?></label>
			</td>
			<td>
				<select id="featured_select_set" name="featured_select_set"><?php echo $settingset_html;?></select>
			</td>
		</tr>
        </table>
	</div>		
        </div>
<?php }
}

add_action('publish_post', 'featured_add_to_slider');
add_action('publish_page', 'featured_add_to_slider');
add_action('edit_post', 'featured_add_to_slider');
add_action('publish_post', 'featured_remove_from_slider');
add_action('edit_post', 'featured_remove_from_slider');
add_action('deleted_post','featured_delete_from_slider_table');

add_action('edit_attachment', 'featured_add_to_slider');
add_action('delete_attachment','featured_delete_from_slider_table');

function featured_slider_plugin_url( $path = '' ) {
	return plugins_url( $path, __FILE__ );
}
add_filter( 'plugin_action_links', 'featured_sslider_plugin_action_links', 10, 2 );

function featured_sslider_plugin_action_links( $links, $file ) {
	if ( $file != FEATURED_SLIDER_PLUGIN_BASENAME )
		return $links;

	$url = featured_sslider_admin_url( array( 'page' => 'manage-featured-slider' ) );

	$settings_link = '<a href="' . esc_attr( $url ) . '">'
		. esc_html( __( 'Manage') ) . '</a>';

	array_unshift( $links, $settings_link );

	return $links;
}

//New Custom Post Type
$gfeatured_slider = get_option('featured_slider_global_options');
if( $gfeatured_slider['custom_post'] == '1' and !post_type_exists('slidervilla') ){
	add_action( 'init', 'featured_post_type', 11 );
	function featured_post_type() {
			$gfeatured_slider = get_option('featured_slider_global_options');
			$labels = array(
			'name' => _x('SliderVilla Slides', 'post type general name'),
			'singular_name' => _x('SliderVilla Slide', 'post type singular name'),
			'add_new' => _x('Add New', 'featured'),
			'add_new_item' => __('Add New SliderVilla Slide'),
			'edit_item' => __('Edit SliderVilla Slide'),
			'new_item' => __('New SliderVilla Slide'),
			'all_items' => __('All SliderVilla Slides'),
			'view_item' => __('View SliderVilla Slide'),
			'search_items' => __('Search SliderVilla Slides'),
			'not_found' =>  __('No SliderVilla slides found'),
			'not_found_in_trash' => __('No SliderVilla slides found in Trash'), 
			'parent_item_colon' => '',
			'menu_name' => 'SliderVilla Slides'
			);
			$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => array('slug' => $gfeatured_slider['cpost_slug'],'with_front' => false),
			'capability_type' => 'post',
			'has_archive' => true, 
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title','editor','thumbnail','excerpt','custom-fields')
			); 
			register_post_type('slidervilla',$args);
	}

	//add filter to ensure the text Featured, or featured, is displayed when user updates a featured 
	add_filter('post_updated_messages', 'featured_updated_messages');
	function featured_updated_messages( $messages ) {
	  global $post, $post_ID;

	  $messages['featured'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('SliderVilla Slide updated. <a href="%s">View SliderVilla slide</a>'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('SliderVilla Slide updated.'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('SliderVilla Slide restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('SliderVilla Slide published. <a href="%s">View SliderVilla slide</a>'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Featured saved.'),
		8 => sprintf( __('SliderVilla Slide submitted. <a target="_blank" href="%s">Preview SliderVilla slide</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('SliderVilla Slides scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview SliderVilla slide</a>'),
		  // translators: Publish box date format, see http://php.net/date
		  date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('SliderVilla Slide draft updated. <a target="_blank" href="%s">Preview SliderVilla slide</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	  );

	  return $messages;
	}
} //if custom_post is true

require_once (dirname (__FILE__) . '/slider_versions/featured_1.php');
require_once (dirname (__FILE__) . '/settings/settings.php');
require_once (dirname (__FILE__) . '/settings/easy_builder.php');
require_once (dirname (__FILE__) . '/settings/admin-ajax.php');

//SliderVilla Quicktag
function featured_quicktag_select(){
	echo '<option value="featured-slider" >Featured Slider</option>';
}
function featured_quicktag_js(){
	echo '<script language="javascript" type="text/javascript" src="'.featured_slider_plugin_url('includes/tinymce/tinymce.js').'"></script>';
}
function featured_plugins_loaded(){
	if( !class_exists( 'add_slidervilla_button' ) ) {
		include_once (dirname (__FILE__) . '/includes/tinymce/tinymce.php');
		add_action( 'svquicktag_select', 'featured_quicktag_select' );
		add_action( 'svquicktag_js', 'featured_quicktag_js' );
	}
	else{
		add_action( 'svquicktag_select', 'featured_quicktag_select' );
		add_action( 'svquicktag_js', 'featured_quicktag_js' );
	}
}
add_action( 'plugins_loaded', 'featured_plugins_loaded' );
// Load the update-notification class
add_action('init', 'featured_update_notification');
function featured_update_notification()
{
    require_once (dirname (__FILE__) . '/includes/upgrade.php');
    $featured_upgrade_remote_path = 'http://support.slidervilla.com/sv-updates/featured.php';
    new featured_update_class ( FEATURED_SLIDER_VER, $featured_upgrade_remote_path, FEATURED_SLIDER_PLUGIN_BASENAME );
}
?>
