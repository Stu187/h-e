<?php
// look up for the path
require_once( dirname( dirname(__FILE__) ) . '/featured-config.php');
// check for rights
if ( !current_user_can('edit_pages') && !current_user_can('edit_posts') ) 
	wp_die(__("You are not allowed to be here"));
global $wpdb,$wpts;
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php _e('Embed Slider','featured-slider'); ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<?php wp_print_scripts('jquery');?>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<?php do_action( 'svquicktag_js' ); ?>
	<base target="_self" />
	<link rel="stylesheet" type="text/css" href="<?php echo featured_slider_plugin_url('includes/tinymce/sv-style.css');?>" />
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery("#slider").change(function() {
				var sliderval=jQuery("#slider").val();
				var sliderName = sliderval.replace(/\s+/g, '-').toLowerCase();
				if( sliderval ){
					var atts_file='../../../'+sliderName+'/includes/tinymce/format/default.php';
					jQuery("#content").load(atts_file);		
				}
			});
		});
	</script>
</head>
<body id="link" onLoad="tinyMCEPopup.executeOnLoad('featuredSliderInit();');document.body.style.display='';document.getElementById('slider').focus();" style="display: none;margin:0;">
<?php global $slidervillaSliders;
if( count($slidervillaSliders) > 1) {
	$showSelect = 'style="display:block;"';
	//$showContent = 'style="display:none;"';
}
else {
	$showSelect = 'style="display:none;"';
	//$showContent = 'style="display:block;"';
?>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			var atts_file='format/default.php';
			jQuery("#content").load(atts_file);		
		});
	</script>
<?php
} 
?>
	<div class="sv-qt-body">
		<div class="sv-qt-content">
			<form name="svslider" id="svslider" action="#" class="sv-select-slider">
			<table class="form-table" id="sliders" <?php echo $showSelect;?>>
				<tr valign="top">
				<td scope="row"><label for="slider"><?php _e('Select Slider','featured-slider'); ?></label></td> 
				<td>
				<div class="styled-select">
					<select name="slider" id="slider" class="select_slider">
						<option value=""><?php _e('Select Slider','featured-slider'); ?></option>
						<?php 
							do_action( 'svquicktag_select' );
						?>
					</select>
				</div>
				</td>
				</tr>
			</table>
			<div id="content" <?php //echo $showContent;?>> </div>
			</form>
		</div>
	</div>
</body>
</html>
