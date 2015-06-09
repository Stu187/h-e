<?php /* Custom Taxonomy Template tag and Shortcode */
//For displaying taxonomy specific posts in chronologically reverse order
function featured_carousel_posts_on_slider_taxonomy($max_posts='5', $post_type='post', $taxonomy='category', $term='', $offset=0, $out_echo = '1', $set='', $show='', $operator='',$data=array() ) {
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
	
	//$show introduced in version 1.0.1
	if( $show == 'per_tax' and $term ){
		$posts=array();$j=0;
		foreach($term as $term_slug){
			$args=array(
					'numberposts'		=> '1',
					'offset'		=> $offset,
					'orderby'		=> $orderby,
					'post_type'		=> $post_type,
					'post_status'		=> 'publish',
					'author'		=> $author,
					'tax_query'		=> array( array('taxonomy' => $taxonomy,'field' => 'slug','terms' => $term_slug	) )
				);
			//filter hook
			$args=apply_filters('featured_svtaxonomy_args',$args);
			$posts_arr=get_posts( $args );
			$posts[]=$posts_arr[0];
			$j++;
			if( $j == $max_posts ){
				break;
			}
		}
	}
	else{
		if( (!is_array($term) and !empty($term)) or (is_array($term) and count($term) > 0) ) {
			$tax_query_array=array('taxonomy' => $taxonomy,'field' => 'slug','terms' => $term	);
			if( !empty($operator) ){
				$tax_query_array['operator']=$operator;
			}
			//extract the posts
			$args=array(
					'numberposts'     => $max_posts,
					'offset'          => $offset,
					'orderby'	  => $orderby,
					'post_type'       => $post_type,
					'post_status'     => 'publish',
					'author'	  => $author,
					'tax_query'	  => array( $tax_query_array )
				);
			//filter hook
			$args=apply_filters('featured_svtaxonomy_args',$args);
			$posts = get_posts( $args );
		}
		else{
			//extract the posts
			$args=array(
					'numberposts'     => $max_posts,
					'offset'          => $offset,
					'orderby' 	  => $orderby,
					'post_type'       => $post_type,
					'post_status'     => 'publish',
					'author'	  => $author
				);
			//filter hook
			$args=apply_filters('featured_svtaxonomy_args',$args);
			$posts = get_posts( $args );
		}
	} //$show ends

	$r_array=featured_global_posts_processor( $posts, $featured_slider_curr, $out_echo,$set,$data );
	return $r_array;
}

function get_featured_slider_taxonomy($args='') {
    	$defaults=array('post_type'=>'post', 'taxonomy'=>'category','term'=>'', 'set'=>'', 'offset'=>0, 'show'=>'', 'operator'=>'','data'=>array() );
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
	$handle_string='';
	if(!empty($term))$handle_string='_t'.str_replace(',','_',$term);
	if(isset($data['author']) and $data['author'] != '' )$author=$data['author'];
	else $author='0';
	// pass type in case of ecom slider
	if($taxonomy=='wpsc_product_category') $data['type'] = 'ecom';

	$handle_string.=(($author!='0')?('_a'.str_replace(',','_',$author)):'');
	if(!empty($term))$term=explode(',',$term);
	$slider_handle='featured_slider'.$handle_string;
	$r_array = featured_carousel_posts_on_slider_taxonomy($featured_slider_curr['no_posts'], $post_type, $taxonomy, $term, $offset, '0', $set, $show, $operator,$data); 
	get_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,'1',$data);
} 

function return_featured_slider_taxonomy($post_type='post', $taxonomy='category', $term='', $set='', $offset=0,$show='', $operator='',$data=array()) {
	$slider_html='';
	$default_featured_slider_settings=get_featured_slider_default_settings();
	// If setting set is 1 then set to blank	
	if($set == '1') $set = '';

	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider = get_option('featured_slider_options');
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	$handle_string='';
	if(!empty($term))$handle_string='_t'.str_replace(',','_',$term);
	if(isset($data['author']) and $data['author'] != '' )$author=$data['author'];
	else $author='0';
	$handle_string.=(($author!='0')?('_a'.str_replace(',','_',$author)):'');
	if(!empty($term))$term=explode(',',$term);
	$slider_handle='featured_slider'.$handle_string;
	$data['slider_handle']=$slider_handle;
	// pass type in case of ecom slider
	if($taxonomy=='wpsc_product_category') $data['type'] = 'ecom';

	$r_array = featured_carousel_posts_on_slider_taxonomy($featured_slider_curr['no_posts'], $post_type, $taxonomy, $term, $offset, '0', $set, $show, $operator,$data); 
	//get slider 
	$output_function='return_global_featured_slider';
	$slider_html=$output_function($slider_handle,$r_array,$featured_slider_curr,$set,$data);

	return $slider_html;
}

function featured_slider_taxonomy_shortcode($atts) {
	extract(shortcode_atts(array(
		'post_type'=>'post', 
		'taxonomy'=>'category',
		'term' => '',
		'set' => '',
		'offset'=>'0',
		'show'=>'',
		'operator'=>'',
		'author'=>'',
	), $atts));
	$data=array();
	$data['author']=$author;
	return return_featured_slider_taxonomy($post_type,$taxonomy,$term,$set,$offset,$show,$operator,$data);
}
add_shortcode('featuredtaxonomy', 'featured_slider_taxonomy_shortcode');
?>
