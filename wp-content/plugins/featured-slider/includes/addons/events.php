<?php /* Custom Event Template tag and Shortcode */
//For displaying Event specific posts in chronologically reverse order
function featured_carousel_posts_on_slider_event($max_posts='5', $post_type='event', $term='',$tags='', $offset=0, $out_echo = '1', $set='', $scope='all', $data=array() ) {
    	$r_array=array();
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider = get_option('featured_slider_options');
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	global $wpdb, $table_prefix;
	$rand = $featured_slider_curr['rand'];
	if(isset($rand) and $rand=='1'){
		$orderby = 'rand';
	}
	else {
		if($scope == 'all') $orderby = 'event_date_created';
		else $orderby = 'event_start_date';
	}
	if(isset($data['owner']) and $data['owner'] !='' )$author=$data['owner'];
	else $author='0';
	$args=array(
		'limit'     	  => $max_posts,
		'scope'		  => $scope,
		'offset'          => $offset,
		'orderby' 	  => $orderby,
		'post_type'       => $post_type,
		'post_status'     => 'publish'
	);
	if( !empty($term)) $args['category']= $term;
	if( !empty($tags)) $args['tag'] = $tags;
	if($scope == 'all') $args['order'] = 'DESC';
	if($author != 0 ) $args['owner'] = $author;
	//filter hook
	$args=apply_filters('featured_svtaxonomy_args',$args);
	if(class_exists('EM_Events')) {
		$posts = EM_Events::get( $args );
		$data['type']='eman';
		$r_array=featured_global_posts_processor( $posts, $featured_slider_curr, $out_echo,$set,$data );
		return $r_array;
	} else _e("Please activate event manager plugin","featured-slider");
}

function get_featured_slider_event($args='') {
    	$defaults=array('post_type'=>'event', 'term'=>'','tags'=>'', 'set'=>'','offset'=>0,'scope,'=>'all','data'=>array() );
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	// If setting set is 1 then set to blank	
	if($set == '1') $set = '';
	$default_featured_slider_settings=get_featured_slider_default_settings(); 
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider = get_option('featured_slider_options');
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	$handle_string='_evnt';
	if(!empty($term))$handle_string='_t'.str_replace(',','_',$term);
	if(isset($data['owner']) and $data['owner'] != '' )$author=$data['owner'];
	else $author='0';
	$handle_string.=(($author!='0')?('_a'.str_replace(',','_',$author)):'');
	//if(!empty($term))$term=explode(',',$term);
	$slider_handle='featured_slider'.$handle_string;
	$r_array = featured_carousel_posts_on_slider_event($featured_slider_curr['no_posts'], $post_type, $term,$tags, $offset, '0', $set,$scope,$data); 
	get_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,'1',$data);
} 

function return_featured_slider_event($post_type='event', $term='',$tags='', $set='', $offset=0,$scope='',$data=array()) {
	$slider_html='';
	$default_featured_slider_settings=get_featured_slider_default_settings(); 
	// If setting set is 1 then set to blank	
	if($set == '1') $set = '';
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider = get_option('featured_slider_options');
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	$handle_string='_evnt';
	if(!empty($term))$handle_string='_t'.str_replace(',','_',$term);
	if(isset($data['owner']) and $data['owner'] != '' )$author=$data['owner'];
	else $author='0';
	$handle_string.=(($author!='0')?('_a'.str_replace(',','_',$author)):'');
	//if(!empty($term))$term=explode(',',$term);
	$slider_handle='featured_slider'.$handle_string;
	$data['slider_handle']=$slider_handle;
	$r_array = featured_carousel_posts_on_slider_event($featured_slider_curr['no_posts'], $post_type, $term,$tags, $offset, '0', $set,$scope,$data); 
	//get slider 
	$output_function='return_global_featured_slider';
	$slider_html=$output_function($slider_handle,$r_array,$featured_slider_curr,$set,$data);

	return $slider_html;
}

function featured_slider_event_shortcode($atts) {
	extract(shortcode_atts(array(
		'post_type'=>'event', 
		'term' => '',
		'tags' =>'',
		'set' => '',
		'offset'=>'0',
		'scope'=>'all',
		'author'=>'',
	), $atts));
	$data=array();
	$data['owner']=$author;
	return return_featured_slider_event($post_type,$term,$tags,$set,$offset,$scope,$data);
}
add_shortcode('featuredevent', 'featured_slider_event_shortcode');
?>
