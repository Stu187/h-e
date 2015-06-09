<?php /* Post Attachments Template tag and Shortcode */
//For displaying all the attachments of a particular post in Featured Slider
function featured_carousel_posts_on_slider_attachments($max_posts='5', $offset=0, $out_echo = '1', $set='',$id,$data=array() ) {
	$r_array=array();
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider = get_option('featured_slider_options');
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr = populate_featured_current($featured_slider_curr);
		
	global $wpdb, $table_prefix;
	
	$rand = $featured_slider_curr['rand'];
	if(isset($rand) and $rand=='1'){
	  $orderby = '&orderby=rand';
	}
	else {
	  $orderby = 'menu_order ID';
	}
	$args=array(
		'post_type'	=> 'attachment',
		'numberposts'	=> $max_posts,
		'offset'    	=> $offset,
		'orderby'	=> $orderby,
		'post_status'	=> null,
		'post_parent'	=> $id
		) ;
	//filter hook
	$args=apply_filters('featured_svattachments_args',$args);
	$attachments = get_posts( $args	);
	foreach($attachments as $attachment){
		$attachment->slide_url=wp_get_attachment_url( $attachment->ID );
		//filter hook
		$attachment->slide_url=apply_filters('featured_svattachments_slide_url',$attachment->slide_url);
	}
	$r_array=featured_global_posts_processor( $attachments, $featured_slider_curr, $out_echo,$set,$data );
	return $r_array;
}

function get_featured_slider_attachments($args='') {
    $defaults=array('set'=>'', 'offset'=>0, 'id'=>'','data'=>array() );
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	$default_featured_slider_settings=get_featured_slider_default_settings();
	// If setting set is 1 then set to blank	
	if($set == '1') $set = '';
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider = get_option('featured_slider_options');
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	global $post;
	$id=(int) $id;
	if( empty($id) or $id==0 or !$id) $id=$post->ID;
	$slider_handle='featured_slider_'.$id;
	$data['slider_handle']=$slider_handle;
	$r_array = featured_carousel_posts_on_slider_attachments($featured_slider_curr['no_posts'], $offset, '0', $set, $id,$data); 
	get_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,$echo='1',$data);

} 

function return_featured_slider_attachments($set='', $offset=0, $id,$data=array()) {
	$slider_html='';
	$default_featured_slider_settings=get_featured_slider_default_settings();
	// If setting set is 1 then set to blank	
	if($set == '1') $set = ''; 
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider = get_option('featured_slider_options');
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	global $post;
	$id=(int) $id;
	if( empty($id) or $id==0 or !$id) $id=$post->ID;
	$slider_handle='featured_slider_'.$id;
	$data['slider_handle']=$slider_handle;
	$r_array = featured_carousel_posts_on_slider_attachments($featured_slider_curr['no_posts'], $offset, '0', $set,$id,$data); 
	//get slider 
	$slider_html=return_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,$echo='0',$data);
	return $slider_html;
}

function featured_slider_attachments_shortcode($atts) {
	extract(shortcode_atts(array(
		'set' => '',
		'offset'=>'0',
		'id'=>'',
	), $atts));

	return return_featured_slider_attachments($set,$offset,$id);
}
add_shortcode('featuredattachments', 'featured_slider_attachments_shortcode');
?>
