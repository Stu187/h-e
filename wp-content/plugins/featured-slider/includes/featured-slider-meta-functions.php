<?php
function featured_get_slide_author($post_obj,$args){
 	$defaults = array(
		'field' => 'display_name',
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

    if(isset($post_obj->post_author)) {
		$author_id=$post_obj->post_author;
		return get_the_author_meta( $field , $author_id );
	}
	if(isset($post_obj->author)){
		return $post_obj->author;
	}
	return 'admin';
}
function featured_get_slide_category_name($post_obj,$args){
 	$defaults = array(
		'show' => 'first',
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	if( isset($post_obj->ID) and ( $post_obj->ID )!='0' ) {
		$post_id=$post_obj->ID;
		$categories= get_the_category( $post_id );
		$cat_arr=array();
		foreach($categories as $category){
		  $cat_arr[]=$category->cat_name;
		}
		$category_string = '';
		if($show=='all'){
		   $category_string = implode(', ',$cat_arr);
		}
		else{
		   if(isset($cat_arr[0]))$category_string = $cat_arr[0];
		}
		return $category_string;
	}
	if( isset($post_obj->category) ){
		return $post_obj->category;
	}
	return  _e('Uncategorized','featured-slider');
}
function featured_get_slide_pub_date($post_obj,$args){
	$defaults = array(
		'format' => 'M j,Y',
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );
	
	$pubdate=date('Y-m-d');
	if( isset($post_obj->post_date) ) {
		$pubdate=$post_obj->post_date ;
	}
	if( isset($post_obj->pubDate) ){
		$pubdate=$post_obj->pubDate ;
	}
	
	$pubdate=strtotime($pubdate);
	return date( $format , $pubdate );
}
function featured_get_slide_comments_number($post_obj,$args){
 	$defaults = array(
		'zero' => false,
		'one' => false, 
		'more' => false,
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

    $output = '';
	if( isset($post_obj->ID) ){
		$post_id=$post_obj->ID;

		$number = get_comments_number( $post_id );
		
		if ( $number > 1 )
				$output = str_replace('%', number_format_i18n($number), ( false === $more ) ? __('% Comments') : $more);
		elseif ( $number == 0 )
				$output = ( false === $zero ) ? __('No Comments') : $zero;
		else // must be one
				$output = ( false === $one ) ? __('1 Comment') : $one;
	}
	
	return $output;
}
?>
