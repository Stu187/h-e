<?php 
require_once( dirname ( dirname( dirname(__FILE__) ) ). '/featured-config.php');
	$wooterms = get_terms('product_cat');
	$woocat_html='<option value="" selected >All Category</option>';
	foreach( $wooterms as $woocategory) {
		$woocat_html =$woocat_html.'<option value="'.$woocategory->slug.'" >'. $woocategory->name .'</option>';
	}

$html='
<tr valign="top" >
	<td scope="row">
		<label for="type">'.__('Slider type','featured-slider').'</label>
	</td> 
	<td>
		<div class="styled-select">
			<select name="type" >
				<option value="recent" >'. __('Recent Product Slider','featured-slider').'</option>
				<option value="upsells" >'. __('Upsells Product Slider','featured-slider').'</option>
				<option value="crosssells" >'. __('Crosssells Product Slider','featured-slider').'</option>
				<option value="external" >'. __('External Product Slider','featured-slider').'</option>
				<option value="grouped" >'. __('Grouped Product Slider','featured-slider').'</option>
			</select>
		</div>
	</td>
</tr>
<tr valign="top" class="woo-product" style="display:none;">
	<td scope="row">
		<label for="product_id">'.__('Product ID','featured-slider').'</label></td> 
	<td>
		<input id="product_id" name="product_id" value="" type="text" >
	</td>
</tr>
<tr valign="top" >
	<td scope="row">
		<label for="term">'.__('category','featured-slider').'</label></td> 
	<td>
		<select id="featured_slider_woocatslug" class="featured-multiselect" multiple >'.$woocat_html.'</select>
		<input type="hidden" name="term" value="" />
	</td>
</tr>
';
print($html);
?>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("select[name='type']").change(function() {
		var sliderType = jQuery(this).val();
		if( sliderType != "recent" && sliderType != "external" ) {
				jQuery(".woo-product").css("display","table-row");
		} else {
			jQuery(".woo-product").css("display","none");
		}
	});
});
</script>
