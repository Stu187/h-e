<?php
require_once( dirname ( dirname( dirname(__FILE__) ) ). '/featured-config.php');
$html='
<!-- Facebook Album Slider -->
<tr valign="top">
	<td scope="row">
		<label for="fb-url">'.__('Page Url','featured-slider').'</label></td> 
	<td>
		<input type="text" name="fb-url" id="fb-pg-url" />
		<input type="submit" name="fb_connect" value="Connect" class="btn_save fb_connect" />
	</td>
</tr>

<tr valign="top" class="fb-albums">
	
</tr>
';
print($html);
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery(".fb_connect").click(function() {
		var limg = "<?php echo admin_url('images/loading.gif');?>"; 
		jQuery(".fb-albums").append('<div class="featured-loader" style="background: url('+limg+') 50% 50% ;background-repeat: no-repeat;width: 100px;height: 20px;margin: 15px auto;"></div>');
		var data = {};
		data['page_url'] = jQuery("#fb-pg-url").val();
		data['action'] = 'featured_shfb_album';
		data['page'] = 'quicktag';
		data['featured_slider_pg'] = '<?php echo wp_create_nonce( "featured-slider-nonce" );?>';
		var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
		jQuery.post(ajaxurl, data, function(response) { 
			jQuery(".fb-albums").find(".featured-loader").remove();
			jQuery(".fb-albums").html(response);
		});
		return false;
	});
});
</script>
