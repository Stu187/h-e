<?php
function featured_slider_license(){
$featured_license_key=get_option('featured_license_key');
?>
<div class="wrap" style="clear:both;">
<h2><?php _e('License','featured-slider'); if(isset($curr)) echo $curr; ?> </h2>
<form method="post" action="options.php" id="featured_slider_form"> <?php settings_fields('featured-slider-license-info'); ?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e('License Key','featured-slider'); ?></th>
			<td><input type="text" name="featured_license_key" id="featured_license_key" class="regular-text code" value="<?php echo $featured_license_key; ?>" />
				<div>
					<?php _e('Enter the License Key which you would have received on ','featured-slider');
					echo '<a href="http://support.slidervilla.com/my-downloads/" target="_blank">';_e('My Downloads Area','featured-slider');echo '</a>';?>
				</div>
			</td>
		</tr>
	</table>
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
</form>	
</div>
<?php
}
function featured_license_notice() {  
$featured_license_key=get_option('featured_license_key');
	if ( isset($_GET['page']) && ('featured-slider-admin' == $_GET['page'] or 'featured-slider-settings' == $_GET['page']) && empty($featured_license_key) ){
	?>
		<div class="error">
			<p><?php _e( 'Enter the License Key for Featured Slider on ', 'featured-slider' ); echo '<a href="'.featured_sslider_admin_url( array( 'page' => 'featured-slider-license-key' ) ).'">';_e('this page','featured-slider');echo '</a>';?></p>
		</div>
	<?php
	}
}
add_action( 'admin_notices', 'featured_license_notice' );
if ( is_admin() ){ 
	add_action( 'admin_init', 'register_featured_license_settings' ); 
}
function register_featured_license_settings() { 
	register_setting( 'featured-slider-license-info', 'featured_license_key' );
}
?>
