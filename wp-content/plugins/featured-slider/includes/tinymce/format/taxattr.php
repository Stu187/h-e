<?php
require_once( dirname ( dirname( dirname(__FILE__) ) ). '/featured-config.php');
$post_types = get_post_types('','objects'); 
$taxonomy_names = get_object_taxonomies( 'post' );
$html='
<!-- taxonomy -->
<tr valign="top" class="taxo">
	<td scope="row">
		<label for="post_type">'.__('Post Type','featured-slider').'</label></td> 
	<td>
		<div class="styled-select">
			<select name="post_type" id="featured_taxonomy_posttype" class="taxo-quicktag" >';
			foreach ( $post_types as $cpost_type ) { 
				if(!in_array(($cpost_type->name),array('attachment','revision','nav_menu_item'))) {
					$html .=' <option value="'.$cpost_type->name.'" >' . $cpost_type->labels->name . '</option>';
				}
			} 
			$html.='</select>
		</div>
	</td>
</tr>

<tr valign="top" class="sh-taxo">
	<td scope="row">
		<label for="taxonomy">'.__('Taxonomy','featured-slider').'</label></td> 
	<td>
		<div class="styled-select">
			<select name="taxonomy" id="featured_taxonomy" >
			<option value="" >Select Taxonomy </option>';
			foreach ( $taxonomy_names as $taxonomy_name ) { 
				$html.= '<option value="'.$taxonomy_name.'" >' . $taxonomy_name . '</option>';
			}
			$html.= '</select>
		</div>
	</td>
</tr>

<tr valign="top" class="sh-term" style="display:none;">
	
</tr>
';
print($html);
?>
