<?php 
require_once( dirname ( dirname( dirname(__FILE__) ) ). '/featured-config.php');
	$eventterms = get_terms('event-categories');
	$eventcat_html='<option value="" selected >Select the Category</option>';
	foreach( $eventterms as $eventcategory) {
		$eventcat_html =$eventcat_html.'<option value="'.$eventcategory->slug.'" >'.$eventcategory->name.'</option>';
	} 
	$evtags = get_terms("event-tags");
	$evtag_html='<option value="" selected >All Tags</option>';
	foreach( $evtags as $tags) {
		$evtag_html = $evtag_html.'<option value="'.$tags->slug.'">'.$tags->name.'</option>';
	} 

$html='
<tr valign="top" id="eman-slider-type" >
<td scope="row">
<label for="scope">'.__('Slider Scope','featured-slider').'</label>
</td> 
<td>
	<div class="styled-select">
		<select name="scope" >
			<option value="future" >'. __('Future Events','featured-slider').'</option>
			<option value="past" >'. __('Past Events','featured-slider').'</option>
			<option value="all" >'. __('Recent Events','featured-slider').'</option>
		</select>
	</div>
</td>
</tr>

<tr valign="top">
	<td scope="row">
		<label for="term">'.__('Category','featured-slider').'</label></td> 
	<td>
		<select class="featured-multiselect" multiple >'.$eventcat_html.'</select>
		<input type="hidden" name="term" value="" />
	</td>
</tr>

<tr valign="top">
	<td scope="row">
		<label for="tags">'.__('Tags','featured-slider').'</label></td> 
	<td>
		<select class="featured-multiselect" multiple >'.$evtag_html.'</select>
		<input type="hidden" name="tags" value="" />
	</td>
</tr>
<tr valign="top" >
	<td scope="row">
		<label for="slider">'.__('Taxonomy(Read only)','featured-slider').'</label></td> 
	<td>
		<input type="text" name="taxonomy" value="event-categories" readonly/>
	</td>
</tr>
';
print($html);
?>
