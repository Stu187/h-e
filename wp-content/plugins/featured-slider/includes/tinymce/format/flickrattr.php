<?php 
require_once( dirname ( dirname( dirname(__FILE__) ) ). '/featured-config.php');

$html='
<tr valign="top" >
	<td scope="row">
		<label for="type">'.__('Select type','featured-slider').'</label>
	</td> 
	<td>
		<div class="styled-select">
			<select name="type" class="flickr-type featured-form-input" >
				<option value="user">User</option>
				<option value="album">Album</option>
			</select>
		</div>
	</td>
</tr>

<tr valign="top">
	<td scope="row">
		<label for="id" id="flickr-lb">'.__('User ID','featured-slider').'</label></td> 
	<td>
		<input type="text" name="id" id="fl-user-id" class="featured-form-input" />
	</td>
</tr>
';
print($html);
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery(".flickr-type").change(function() {
		var val = jQuery(this).val();
		if(val == "user") {
			jQuery("#flickr-lb").text("User ID");
		} else if(val == "album") {
			jQuery("#flickr-lb").text("Album ID");
		} 
	});
});
</script>
