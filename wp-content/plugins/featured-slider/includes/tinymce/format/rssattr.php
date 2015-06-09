<?php
require_once( dirname ( dirname( dirname(__FILE__) ) ). '/featured-config.php');
$html='
<!-- rssfeed -->
<tr valign="top" class="rssfeed">
	<td scope="row">
		<label for="feedurl">'.__('Feedurl','featured-slider').'</label></td> 
	<td>
		<input type="text" name="feedurl" />
	</td>
</tr>
';
print($html);
?>
