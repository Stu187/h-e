<?php /* Custom Taxonomy Template tag and Shortcode */
//For displaying taxonomy specific posts in chronologically reverse order
function featured_carousel_posts_on_slider_woocommerce($max_posts='5', $post_type='product', $taxonomy='product_cat', $term='', $offset=0, $out_echo = '1', $set='', $product_id='',$type='', $operator='',$data=array() ) {
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
		$orderby = 'date';
	}

	if(isset($data['author']) and $data['author'] !='' )$author=$data['author'];
	else $author='0';
	
	$args = array(
		'numberposts'     => $max_posts,
		'offset'          => $offset,
		'orderby'	  => $orderby,
		'post_type'       => $post_type,
		'post_status'     => 'publish',
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => 1,
	);
	if( $type == 'external' && !empty($term) && count($term) > 0 ) {
		$tax_query_external=array('taxonomy' => 'product_type','field' => 'slug','terms' => 'external');
		$tax_query_term=array('taxonomy' => $taxonomy,'field' => 'slug','terms' => $term);
		$args['tax_query']= array( 'relation' => 'AND',$tax_query_external,$tax_query_term );
	}
	elseif($type == 'external') {
		$args['tax_query'] = array( array('taxonomy' => 'product_type','field' => 'slug','terms' => 'external') );
	}
	elseif( (!is_array($term) and !empty($term)) or (is_array($term) and count($term) > 0) ) {
		$tax_query_array=array('taxonomy' => $taxonomy,'field' => 'slug','terms' => $term);
		if( !empty($operator) ){
			$tax_query_array['operator']=$operator;
		}
		$args['tax_query'] = array( $tax_query_array );
	}
	if($author != '0' ) $args['author'] = $author;	
	if($product_id != '') {
		$postsin = '';
		if( ($type=='upsells' || $type=='crosssells') && function_exists('get_product') ) {
			$product = get_product( $product_id );
			if($type=='upsells') { $postsin = $product->get_upsells(); }
			if($type=='crosssells') $postsin = $product->get_cross_sells();	
			$args['post__in'] = $postsin;
			$args['post__not_in'] = array( $product->id );
		}
		elseif($type=='grouped') $args['post_parent'] = $product_id;
	}
	//filter hook
	$args=apply_filters('featured_svwoocom_args',$args);
	//extract the posts
	$posts = get_posts( $args );
	$data['type']='woo';
	$r_array=featured_global_posts_processor( $posts, $featured_slider_curr, $out_echo,$set,$data );
	return $r_array;
}

function get_featured_slider_woocommerce($args='') {
    $defaults=array('post_type'=>'product', 'taxonomy'=>'product_cat','term'=>'', 'set'=>'', 'offset'=>0, 'product_id'=>'','type'=>'', 'operator'=>'','data'=>array() );
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	$featured_slider = get_option('featured_slider_options');
	// If setting set is 1 then set to blank	
	if($set == '1') $set = '';
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	$handle_string='_woo';
	if(!empty($term))$handle_string='_t'.str_replace(',','_',$term);
	if(isset($data['author']) and $data['author'] != '' )$author=$data['author'];
	else $author='0';
	$handle_string.=(($author!='0')?('_a'.str_replace(',','_',$author)):'');
	if(!empty($term))$term=explode(',',$term);
	$slider_handle='featured_slider'.$handle_string;
	$r_array = featured_carousel_posts_on_slider_woocommerce($featured_slider_curr['no_posts'], $post_type, $taxonomy, $term, $offset, '0', $set, $product_id, $type, $operator,$data); 
	get_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,'1',$data);
} 

function return_featured_slider_woocommerce($post_type='product', $taxonomy='product_cat', $term='', $set='', $offset=0,$product_id='', $type='', $operator='',$data=array()) {
	$slider_html='';
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider = get_option('featured_slider_options');
	// If setting set is 1 then set to blank	
	if($set == '1') $set = '';
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	$handle_string='_woo';
	if(!empty($term))$handle_string='_t'.str_replace(',','_',$term);
	if(isset($data['author']) and $data['author'] != '' )$author=$data['author'];
	else $author='0';
	$handle_string.=(($author!='0')?('_a'.str_replace(',','_',$author)):'');
	if(!empty($term))$term=explode(',',$term);
	$slider_handle='featured_slider'.$handle_string;
	$data['slider_handle']=$slider_handle;
	$r_array = featured_carousel_posts_on_slider_woocommerce($featured_slider_curr['no_posts'], $post_type, $taxonomy, $term, $offset, '0', $set, $product_id,$type,$operator,$data); 
	//get slider 
	$output_function='return_global_featured_slider';
	$slider_html=$output_function($slider_handle,$r_array,$featured_slider_curr,$set,$data);

	return $slider_html;
}

function featured_slider_woocommerce_shortcode($atts) {
	extract(shortcode_atts(array(
		'post_type'=>'product', 
		'taxonomy'=>'product_cat',
		'term' => '',
		'set' => '',
		'offset'=>'0',
		'product_id'=>'',
		'type'=>'',
		'operator'=>'',
		'author'=>'',
	), $atts));
	$data=array();
	$data['author']=$author;
	return return_featured_slider_woocommerce($post_type,$taxonomy,$term,$set,$offset,$product_id,$type,$operator,$data);
}
add_shortcode('featuredwoocommerce', 'featured_slider_woocommerce_shortcode');
?>
