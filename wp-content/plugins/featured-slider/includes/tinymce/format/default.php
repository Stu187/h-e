<?php
require_once( dirname ( dirname( dirname(__FILE__) ) ). '/featured-config.php');
$html = '<div class="sv-qt-title">
	'.__('Embed Already Built Slider','featured-slider').'
</div>';

//Fetch All Created Slider
global $wpdb,$table_prefix;
$slider_meta = $table_prefix.FEATURED_SLIDER_META;
$sql = "SELECT * FROM $slider_meta ORDER BY slider_id DESC";
$result = $wpdb->get_results($sql);	
foreach($result as $res) {
	$html .= '<div class="sv-created-slider">';
	$shortcode = "[featuredslider id='$res->slider_id']";
	$html .= '<input type="submit" value="'.$shortcode.'" class="sv-created-slideri" onClick="insertFeaturedShortcode({createdSlider:1,shortCode:this.value});" / >';
	$html .= '<span class="sv-slideri-title">'.$res->slider_name.'</span>';
	$html .= '</div>';
}
// Setting Set Select Option
$scounter=get_option('featured_slider_scounter');
for($i=1;$i<=$scounter;$i++) {
	if ($i==1){
		$setting_html='<option value="'.$i.'" selected >'.__('Default Settings Set','featured-slider').'</option>';
	}
	else {
		if($settings_set=get_option('featured_slider_options'.$i)){
			$setting_html=$setting_html.'<option value="'.$i.'">'.$settings_set['setname'].' (ID '.$i.')</option>';
		}
	}
}
//category slug Select Option
$categories = get_categories();
$scat_html='<option value="" selected >Select the Category</option>';
foreach ($categories as $category) { 
	$scat_html =$scat_html.'<option value="'.$category->slug.'" >'. $category->name .'</option>';
} 

// Slider Name Select Option
$gfeatured_slider = get_option('featured_slider_global_options');

$sliders = featured_ss_get_sliders();
$sname_html='<option value="" selected >Select the Slider</option>';
	
  foreach ($sliders as $slider) {
	if($slider['slider_id'] != 0 ) {
		$sname_html =$sname_html.'<option value="'.$slider['slider_id'].'" >'.$slider['slider_name'].'</option>';
	}
  } 

// YouTube Slider Key Check
$youtube_key = isset($gfeatured_slider['youtube_app_id'])?$gfeatured_slider['youtube_app_id']:'';
// Facebook Slider Key Check
$fbkey = isset($gfeatured_slider['fb_app_key'])?$gfeatured_slider['fb_app_key']:'';
// Instagram Slider Key Check
$igkey = isset($gfeatured_slider['insta_client_id'])?$gfeatured_slider['insta_client_id']:'';
// Flickr Slider Key Check
$flkey = isset($gfeatured_slider['flickr_app_key'])?$gfeatured_slider['flickr_app_key']:'';
// 500px Slider Key Check
$pxkey = isset($gfeatured_slider['px_ckey'])?$gfeatured_slider['px_ckey']:'';

$html .= '<div style="clear:left;"></div>

<div class="sv-qt-title">
	'.__('Build and Embed the Slider','featured-slider').'
</div>
<table class="form-table sv-build-slider" >
<tr valign="top" > <!--  -->
<td scope="row">
	<label for="slider-type">'.__('Slider Type','featured-slider').'</label>
<td>
	<div class="styled-select">
		<select name="slider_type" id="slider-type">
			<option value="featuredrecent" >'.__('Recent featured Slider','featured-slider').'</option>
			<option value="featuredcategory" >'.__('Category featured Slider','featured-slider').'</option>
			<option value="featuredslider" >'.__('Custom Slider with Slider ID','featured-slider').'</option>
			<option value="featuredwoocommerce" >'.__('Woo Commerce Slider','featured-slider').'</option>
			<option value="featuredtaxonomy" >'.__('eCommerce Slider','featured-slider').'</option>
			<option value="featuredevent" >'.__('Event Manager','featured-slider').'</option>
			<option value="featuredcalendar" >'.__('Event Calender','featured-slider').'</option>
			<option value="featuredtaxonomy" >'.__('Taxonomy Slider','featured-slider').'</option>
			<option value="featuredfeed" >'.__('RSS feed Slider','featured-slider').'</option>
			<option value="featuredattachments" >'.__('Post Attachments Slider','featured-slider').'</option>
			<option value="featuredngg" >'.__('NextGenGallery Slider','featured-slider').'</option>
			<option value="featuredfacebook" >'.__('Facebook Album Slider','featured-slider').'</option>
			<option value="featuredflickr" >'.__('Flickr Album Slider','featured-slider').'</option>
			<option value="featuredinstagram" >'.__('Instagram Slider','featured-slider').'</option>
			<option value="featured500px" >'.__('500PX Slider','featured-slider').'</option>
			<option value="featuredyoutube" >'.__('Youtube Slider','featured-slider').'</option>
			<option value="featuredvimeo" >'.__('Vimeo Slider','featured-slider').'</option>
		</select>
	</div>
</td>
</tr>
<tr valign="top">
	<td scope="row">
		<label for="set">'.__('Setting Set','featured-slider').'</label>
	</td> 
	<td>
		<div class="styled-select">
			<select name="set" class="featured_set">'.$setting_html.'</select>
		</div>
	</td>
</tr>
<tr valign="top">
	<td scope="row">
		<label for="offset">'.__('Offset','featured-slider').'</label></td> 
	<td>
		<input type="number" name="offset" style="max-width:60px;" />
	</td>
</tr>
<tr valign="top" id="slider_name" style="display:none;">
	<td scope="row">
		<label for="id">'.__('Slider','featured-slider').'</label>
	</td> 
	<td>
		<div class="styled-select">
			<select id="featured_slider_id" name="id" class="featured_sid">'.$sname_html.'</select>
		</div>
	</td>
</tr>
<tr valign="top" id="cat_slug" style="display:none;">
	<td scope="row">
		<label for="catg_slug">'.__('Category','featured-slider').'</label></td> 
	<td>
		<div class="styled-select">
			<select name="catg_slug" id="featured_slider_catslug" class="featured-catslug" ><'.$scat_html .'</select>
		</div>
	</td>
</tr>
</table>
<div class="sv-slider-error"> </div>
<table class="form-table sv-build-slider" id="slider-atts">
</table>
<div class="mceActionPanel">
	<div>
		<input type="submit" class="button-primary" id="insert" name="insert" value="'.__('Insert','featured-slider').'" onClick="insertFeaturedShortcode();" />
	</div>
</div>';
print($html);
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("#slider-type").change(function() { 
		jQuery("#slider-atts").empty();
		jQuery(".sv-slider-error").text('');
		jQuery("input[name='insert']").show();
		var sliderindex = jQuery("#slider-type option:selected").index();
		if( sliderindex == "1" ) { 
			jQuery("#cat_slug").css({"display":"table-row"});
			jQuery("#slider_name").css({"display":"none"});
		}
		else if( sliderindex == "2" ) {
			jQuery("#slider_name").css({"display":"table-row"});
			jQuery("#cat_slug").css({"display":"none"});
		}
		else if( sliderindex == "3" ) {
			var shop_attr='<?php echo featured_slider_plugin_url("includes/tinymce/format/wooattr.php"); ?>';
			<?php if(is_plugin_active('woocommerce/woocommerce.php')) { ?>
				var cntx = jQuery(".sv-build-slider"); 
				jQuery("#slider-atts").load(shop_attr, function() { bindMultiBehaviors(cntx); });
				jQuery("#slider_name").css({"display":"none"});
				jQuery("#cat_slug").css({"display":"none"});
			<?php } else { ?>
				jQuery(".sv-slider-error").text("Install and activate the WooCommerce Plugin to use this shortcode").fadeIn( "slow" );
				jQuery("input[name='insert']").hide();
			<?php } ?>
		}
		else if( sliderindex == "4" ) {
			var ecom_attr='<?php echo featured_slider_plugin_url("includes/tinymce/format/ecomattr.php"); ?>';
			<?php if(is_plugin_active('wp-e-commerce/wp-shopping-cart.php')) { ?>
				jQuery("#slider-atts").load(ecom_attr);	
				jQuery("#slider_name").css({"display":"none"});
				jQuery("#cat_slug").css({"display":"none"});
			<?php } else { ?>
				jQuery(".sv-slider-error").text("Install and activate the WP e-Commerce Plugin to use this shortcode").fadeIn( "slow" );
				jQuery("input[name='insert']").hide();
			<?php } ?>
		}
		else if( sliderindex == "5" ) {
			var eman_attr='<?php echo featured_slider_plugin_url("includes/tinymce/format/emanattr.php"); ?>';
			<?php if(is_plugin_active('events-manager/events-manager.php')) { ?>
				var cntx = jQuery(".sv-build-slider"); 
				jQuery("#slider-atts").load(eman_attr, function() { bindMultiBehaviors(cntx); });	
				jQuery("#slider_name").css({"display":"none"});
				jQuery("#cat_slug").css({"display":"none"});
			<?php } else { ?>
				jQuery(".sv-slider-error").text("Install and activate the Event Manager Plugin to use this shortcode").fadeIn( "slow" );
				jQuery("input[name='insert']").hide();
			<?php } ?>
		}
		else if( sliderindex == "6" ) {
			var ecal_attr='<?php echo featured_slider_plugin_url("includes/tinymce/format/ecalattr.php"); ?>';
			<?php if(is_plugin_active('the-events-calendar/the-events-calendar.php')) { ?>
				var cntx = jQuery(".sv-build-slider"); 
				jQuery("#slider-atts").load(ecal_attr, function() { bindMultiBehaviors(cntx); });	
				jQuery("#slider_name").css({"display":"none"});
				jQuery("#cat_slug").css({"display":"none"});
			<?php } else { ?>
				jQuery(".sv-slider-error").text("Install and activate the Event Calendar Plugin to use this shortcode").fadeIn( "slow" );
				jQuery("input[name='insert']").hide();
			<?php } ?>
		}
		else if( sliderindex == "7" ) {
			var cntx = jQuery(".sv-build-slider"); 
			var tax_attr='<?php echo featured_slider_plugin_url("includes/tinymce/format/taxattr.php"); ?>';
			jQuery("#slider-atts").load(tax_attr, function() { bindMultiBehaviors(cntx); });
			jQuery("#slider_name").css({"display":"none"});
			jQuery("#cat_slug").css({"display":"none"});
		}
		else if( sliderindex == "8" ) {
			var rss_attr='<?php echo featured_slider_plugin_url("includes/tinymce/format/rssattr.php"); ?>';
			jQuery("#slider-atts").load(rss_attr);
			jQuery("#slider_name").css({"display":"none"});
			jQuery("#cat_slug").css({"display":"none"});
		}
		else if( sliderindex == "9" ) {
			var post_attr='<?php echo featured_slider_plugin_url("includes/tinymce/format/postattr.php"); ?>';
			jQuery("#slider-atts").load(post_attr);
			jQuery("#slider_name").css({"display":"none"});
			jQuery("#cat_slug").css({"display":"none"});
		}
		else if( sliderindex == "10" ) {
			var ngg_attr='<?php echo featured_slider_plugin_url("includes/tinymce/format/nggattr.php"); ?>';
			<?php if(is_plugin_active('nextgen-gallery/nggallery.php')) { ?>
				jQuery("#slider-atts").load(ngg_attr);
				jQuery("#slider_name").css({"display":"none"});
				jQuery("#cat_slug").css({"display":"none"});
			<?php } else { ?>
				jQuery(".sv-slider-error").text("Install and activate the NextGen Gallery Plugin to use this shortcode!").fadeIn( "slow" );
				jQuery("input[name='insert']").hide();
			<?php } ?>
		}
		else if( sliderindex == "11" ) {
			var fb_attr='<?php echo featured_slider_plugin_url("includes/tinymce/format/fbattr.php"); ?>';
			<?php if($fbkey != '') { ?>
				jQuery("#slider-atts").load(fb_attr);
				jQuery("#slider_name").css({"display":"none"});
				jQuery("#cat_slug").css({"display":"none"});
			<?php } else { ?>
				var url = "<?php echo featured_sslider_admin_url( array( 'page' => 'featured-slider-global-settings' ) );?>";
				jQuery(".sv-slider-error").html("<p>Add FaceBook App Key on <a href='"+url+"api-keys' class='sv-redirect' target='_blank'>featured Slider Global Settings!</a></p>").fadeIn( "slow" );
				jQuery("input[name='insert']").hide();
			<?php } ?>
		}
		else if( sliderindex == "12" ) {
			var flickr_attr='<?php echo featured_slider_plugin_url("includes/tinymce/format/flickrattr.php"); ?>';
			<?php if($flkey != '') { ?>
				jQuery("#slider-atts").load(flickr_attr);
				jQuery("#slider_name").css({"display":"none"});
				jQuery("#cat_slug").css({"display":"none"});
			<?php } else { ?>
				var url = "<?php echo featured_sslider_admin_url( array( 'page' => 'featured-slider-global-settings' ) );?>";
				jQuery(".sv-slider-error").html("<p>Add Flickr API Key on <a href='"+url+"api-keys' class='sv-redirect' target='_blank'>featured Slider Global Settings!</a></p>").fadeIn( "slow" );
				jQuery("input[name='insert']").hide();
			<?php } ?>
		}
		else if( sliderindex == "13" ) {
			var ig_attr='<?php echo featured_slider_plugin_url("includes/tinymce/format/igattr.php"); ?>';
			<?php if($igkey != '') { ?>
				jQuery("#slider-atts").load(ig_attr);
				jQuery("#slider_name").css({"display":"none"});
				jQuery("#cat_slug").css({"display":"none"});
			<?php } else { ?>
				var url = "<?php echo featured_sslider_admin_url( array( 'page' => 'featured-slider-global-settings' ) );?>";
				jQuery(".sv-slider-error").html("<p>Add Instagram Client Id on <a href='"+url+"api-keys' class='sv-redirect' target='_blank'>featured Slider Global Settings!</a></p>").fadeIn( "slow" );
				jQuery("input[name='insert']").hide();
			<?php } ?>
		}
		else if( sliderindex == "14" ) {
			var px_attr='<?php echo featured_slider_plugin_url("includes/tinymce/format/500pxattr.php"); ?>';
			<?php if($pxkey != '') { ?>
				jQuery("#slider-atts").load(px_attr);
				jQuery("#slider_name").css({"display":"none"});
				jQuery("#cat_slug").css({"display":"none"});
			<?php } else { ?>
				var url = "<?php echo featured_sslider_admin_url( array( 'page' => 'featured-slider-global-settings' ) );?>";
				jQuery(".sv-slider-error").html("<p>Add 500PX Consumer Key on <a href='"+url+"#api-keys' class='sv-redirect' target='_blank'>featured Slider Global Settings!</a></p>").fadeIn( "slow" );
				jQuery("input[name='insert']").hide();
			<?php } ?>
		}
		else if( sliderindex == "15" ) {
			var yt_attr='<?php echo featured_slider_plugin_url("includes/tinymce/format/ytattr.php"); ?>';
			
			<?php if($youtube_key != '') { ?>
				jQuery("#slider-atts").load(yt_attr);
				jQuery("#slider_name").css({"display":"none"});
				jQuery("#cat_slug").css({"display":"none"});
			<?php } else { ?>
				var url = "<?php echo featured_sslider_admin_url( array( 'page' => 'featured-slider-global-settings' ) );?>";
				jQuery(".sv-slider-error").html("<p>Add YouTube API Key on <a href='"+url+"#api-keys' class='sv-redirect' target='_blank'>featured Slider Global Settings!</a></p>").fadeIn( "slow" );
				jQuery("input[name='insert']").hide();
			<?php } ?>
		}
		else if( sliderindex == "16" ) {
			var vimeo_attr='<?php echo featured_slider_plugin_url("includes/tinymce/format/vimeoattr.php"); ?>';
			jQuery("#slider-atts").load(vimeo_attr);
			jQuery("#slider_name").css({"display":"none"});
			jQuery("#cat_slug").css({"display":"none"});
		}
		else {
			jQuery(".nextgen").css({"display":"none"});	
			jQuery("#cat_slug").css({"display":"none"});
			jQuery("#slider_name").css({"display":"none"});
		}
		jQuery(".sv-redirect").click(function(event) {
				setTimeout(function() {	
					tinyMCEPopup.close();
				}, 200);
		});
	});
	var bindMultiBehaviors = function(scope) {
		jQuery(".featured-multiselect", scope).focusout(function() { 
			var sel = jQuery(this)[0]; 
			var terms = [],opt;
			// loop through options in select list
			for (var i=0, len=sel.options.length; i<len; i++) {
				opt = sel.options[i];
				// check if selected
				if ( opt.selected ) {
					terms.push(opt.value);
				}
			}
			terms = terms.join();
			jQuery(this).next("input[type='hidden']").val(terms);
		});
		jQuery("#featured_taxonomy_posttype", scope).change(function() {
			var data = {};
			data['quicktag'] = 'true';
			data['post_type'] = jQuery(this).val();
			data['action'] = 'featured_show_taxonomy';
			data['featured_slider_pg'] = '<?php echo wp_create_nonce( "featured-slider-nonce" ); ?>';
			var ajaxurl = '<?php echo admin_url("admin-ajax.php");?>';
			jQuery.post(ajaxurl, data, function(response) { 
				jQuery(".sh-taxo").html(response);
			}).always(function() {
				var cnxt=jQuery(".sh-taxo");
	   			bindMultiBehaviors(cnxt);
			});
			return false;
		});
		jQuery("#featured_taxonomy", scope).change(function() {
			var data = {};
			data['quicktag'] = 'true';
			data['taxo'] = jQuery(this).val();
			data['action'] = 'featured_show_term';
			data['featured_slider_pg'] = '<?php echo wp_create_nonce( "featured-slider-nonce" ); ?>';
			var ajaxurl = '<?php echo admin_url("admin-ajax.php");?>';
			jQuery.post(ajaxurl, data, function(response) { 
				jQuery(".sh-term").fadeIn("slow");
				jQuery(".sh-term").html(response);
			}).always(function() {
			   	var cnxt=jQuery(".sh-term");
	   			bindMultiBehaviors(cnxt);
			});
			return false;
		});
	};
});
</script>
