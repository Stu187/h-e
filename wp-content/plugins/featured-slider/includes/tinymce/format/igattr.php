<?php 
require_once( dirname ( dirname( dirname(__FILE__) ) ). '/featured-config.php');
$html='
<tr valign="top" >
	<td scope="row">
		<label for="username">'.__('User Name','featured-slider').'</label>
	</td> 
	<td>
		<input type="text" name="username" id="user-name" class="featured-form-input" />
	</td>
</tr>
';
print($html);
?>
