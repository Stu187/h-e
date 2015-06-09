<?php 
require_once( dirname ( dirname( dirname(__FILE__) ) ). '/featured-config.php');
$html='
<tr valign="top" >
	<td scope="row">
		<label for="feature">'.__('Select type','featured-slider').'</label>
	</td> 
	<td>
		<div class="styled-select">
			<select name="feature" class="feature featured-form-input" >
				<option value="popular">Popular</option>
				<option value="highest_rated" >Highest Rated</option>
				<option value="upcoming" >Upcoming</option>
				<option value="editors" >Editors</option>
				<option value="fresh_today" >Fresh Today</option>
				<option value="fresh_yesterday" >Fresh Yesterday</option>
				<option value="fresh_week" >Fresh Week</option>
				<option value="user" >User</option>
				<option value="user_favorites">User favorites</option>
			</select>
		</div>
	</td>
</tr>

<tr valign="top" class="pxuser" style="display:none;">
	<td scope="row">
		<label for="username" id="flickr-lb">'.__('User Name','featured-slider').'</label></td> 
	<td>
		<input type="text" name="username" class="featured-form-input" />
	</td>
</tr>
';
print($html);
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery(".feature").change(function() {
		if(jQuery(this).val() == 'user' || jQuery(this).val() == 'user_favorites' ) {
			jQuery(".pxuser").slideDown();
		} else {
			jQuery(".pxuser").slideUp( "slow" );
		}
	});
});
</script>
