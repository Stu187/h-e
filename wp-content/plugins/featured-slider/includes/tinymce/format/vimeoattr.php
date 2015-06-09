<?php 
require_once( dirname ( dirname( dirname(__FILE__) ) ). '/featured-config.php');

$html='
<tr valign="top" >
	<td scope="row">
		<label for="type">'.__('Select type','featured-slider').'</label>
	</td> 
	<td>
		<div class="styled-select">
			<select name="type" class="vimeo-type featured-form-input" >
				<option value="channel">Channel</option>
				<option value="album">Album</option>
			</select>
		</div>
	</td>
</tr>

<tr valign="top">
	<td scope="row">
		<label for="val" id="vimeo-lb">'.__('Channel Name','featured-slider').'</label></td> 
	<td>
		<input type="text" name="val" class="featured-form-input" />
	</td>
</tr>
';
print($html);
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery(".vimeo-type").change(function() {
		var val = jQuery(this).val();
		if(val == "channel") {
			jQuery("#vimeo-lb").text("Channel Name");
		} else if(val == "album") {
			jQuery("#vimeo-lb").text("Album ID");
		} 
	});
});
</script>
