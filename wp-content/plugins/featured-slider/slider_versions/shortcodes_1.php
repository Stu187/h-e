<?php
function return_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,$echo='0',$data=array()){
	$slider_html='';
	$slider_html=get_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,$echo,$data);
	return $slider_html;
}

function return_featured_slider($slider_id='',$set='',$offset=0,$format='',$data=array()) {
	$gfeatured_slider = get_option('featured_slider_global_options');
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider = get_option('featured_slider_options');
	//Select Settings Set from Meta Box
	if(is_singular() and empty($set)) {
		global $post;
		$sel_set = get_post_meta($post->ID,'_featured_select_set',true);
		if(!empty($sel_set) and $sel_set!='1') $set=$sel_set;
	}
	// If setting set is 1 then set to blank	
	if($set == '1') $set = '';

 	$featured_slider_options='featured_slider_options'.$set;
    	$featured_slider_curr=get_option($featured_slider_options);
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	if(is_singular()){
		global $post;
		$post_id = $post->ID;
		if(featured_ss_slider_on_this_post($post_id))
			$slider_id = get_featured_slider_for_the_post($post_id);
	}
	if(empty($slider_id) or !isset($slider_id)){
	  $slider_id = '1';
	}
	$slider_html='';

	if(!empty($slider_id)){
		$data['slider_id']=$slider_id;
		$slider_handle='featured_slider_'.$slider_id;
		$data['slider_handle']=$slider_handle;
		$r_array = featured_carousel_posts_on_slider($featured_slider_curr['no_posts'], $offset, $slider_id, $echo = '0', $set,$data); 
		$slider_html=return_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,$echo='0',$data);
	} //end of not empty slider_id condition
	
	return $slider_html;
}

function featured_slider_simple_shortcode($atts) {
	extract(shortcode_atts(array(
		'id' => '1',
		'set' => '',
		'offset'=>'0',
		'format'=>'',
	), $atts));
	global $wpdb,$table_prefix;
	$slider_meta = $table_prefix.FEATURED_SLIDER_META;
	$sql = "SELECT * FROM $slider_meta WHERE slider_id = $id"; // WHERE type=0
	$result = $wpdb->get_row($sql);
	$type = isset($result->type)?$result->type:'';
	$scounter = isset($result->setid)?$result->setid :'';
	if($scounter == 1 ) $scounter = '';
	if(count($result) > 0) $param_array = unserialize($result->param);
	$offset = isset($param_array['offset'])?$param_array['offset']:$offset;
	$taxonomy = $term = '';
	//Select Settings Set from Meta Box
	if(is_singular()) {
		global $post;
		$sel_set = get_post_meta($post->ID,'_featured_select_set',true);
		if(!empty($sel_set) and $sel_set!='1') {
			$set=$sel_set;
			$scounter = $sel_set;
		}
	}
	if( $type == 0 || $type == 17  ) {
		// If shortcode is not having set or offset then pick it from DataBase
		if($set == '' ) $set = $scounter;
		if($offset == 0) $offset = isset($param_array['offset'])?$param_array['offset']:$offset;
		$slider_html=return_featured_slider($id,$set,$offset,$format);
	} elseif( $type == 1 ) {
		$catg_slug=$param_array['catg_slug'];
		$slider_html=return_featured_slider_category($catg_slug,$scounter,$offset);
	} elseif( $type == 2 ) {
		$slider_html=return_featured_slider_recent($scounter,$offset);
	} elseif( $type == 3 ) {
		$post_type='product';
		if(isset($param_array['woo-catg'])) {
			$taxonomy='product_cat';
			$term = isset($param_array['woo-catg'])?$param_array['woo-catg']:'';
		} 
		$type = isset($param_array['woo_slider_type'])?$param_array['woo_slider_type']:'';
		$product_id = isset($param_array['product_id'])?$param_array['product_id']:''; ;
		$slider_html=return_featured_slider_woocommerce($post_type,$taxonomy,$term,$scounter,$offset,$product_id,$type);
	} elseif( $type == 4 ) {
		$post_type='wpsc-product';
		if(isset($param_array['ecom_slider_type']) && $param_array['ecom_slider_type'] == '1' && isset($param_array['ecom-catg']) ) {
			$taxonomy='wpsc_product_category';
			$term = isset($param_array['ecom-catg'])?$param_array['ecom-catg']:'';
		}
		$data['type'] = 'ecom';
		$slider_html=return_featured_slider_taxonomy($post_type,$taxonomy,$term,$scounter,$offset,'','',$data);
	} elseif( $type == 5 ) {
		$post_type='event';
		$term = isset($param_array['eman-catg'])?$param_array['eman-catg']:'';
		$tags = isset($param_array['eman-tags'])?$param_array['eman-tags']:'';
		$scope = isset($param_array['eventm_slider_scope'])?$param_array['eventm_slider_scope']:'';
		$slider_html=return_featured_slider_event($post_type, $term,$tags, $scounter, $offset,$scope);
	} elseif( $type == 6 ) {
		$post_type='tribe_events';
		$tags = '';
		if(isset($param_array['ecal-catg']) && $param_array['ecal-catg'] != '' ) {
			$taxonomy='tribe_events_cat';
			$term = isset($param_array['ecal-catg'])?$param_array['ecal-catg']:'';
		}
		$tags = isset($param_array['ecal-tags'])?$param_array['ecal-tags']:'';
		$evtype = isset($param_array['eventcal_slider_type'])?$param_array['eventcal_slider_type']:'';
		$slider_html=return_featured_slider_event_calender($post_type,$taxonomy,$term,$scounter,$offset,$tags,$evtype);
	} elseif( $type == 7 ) {
		$post_type=isset($param_array['post_type'])?$param_array['post_type']:'';
		$taxonomy=isset($param_array['taxonomy_name'])?$param_array['taxonomy_name']:'';
		$term=isset($param_array['taxonomy_term'])?$param_array['taxonomy_term']:'';
		$show=isset($param_array['taxonomy_show'])?$param_array['taxonomy_show']:'';
		$operator=isset($param_array['taxonomy_operator'])?$param_array['taxonomy_operator']:'';
		$author=isset($param_array['taxonomy_author'])?$param_array['taxonomy_author']:'';
		$data=array();
		$data['author']=$author;
		$slider_html=return_featured_slider_taxonomy($post_type,$taxonomy,$term,$scounter,$offset,$show,$operator,$data);
	} elseif( $type == 8 ) {
		$feedurl=isset($param_array['feed_url'])?$param_array['feed_url']:'';
		$default_image=isset($param_array['feed_img'])?$param_array['feed_img']:'';
		$title='';
		$rid=isset($param_array['feed_id'])?$param_array['feed_id']:'';
		$feed=isset($param_array['feed'])?$param_array['feed']:'';
		$order=isset($param_array['feed_order'])?$param_array['feed_order']:'';
		$content=isset($param_array['feed_content'])?$param_array['feed_content']:'';
		$media=isset($param_array['feed_media'])?$param_array['feed_media']:'';
		$src=isset($param_array['feed_src'])?$param_array['feed_src']:'';
		$size=isset($param_array['feed_size'])?$param_array['feed_size']:'';
		$image_class=isset($param_array['feed_imgclass'])?$param_array['feed_imgclass']:'';
		$slider_html=return_featured_slider_rssfeed($scounter,$offset,$feedurl,$default_image,$image_class,$rid,$feed,$order,$content,$media,$title,$src,$size);
	} elseif( $type == 9 ) {
		$paid=isset($param_array['postattch-id'])?$param_array['postattch-id']:'';
		$slider_html=return_featured_slider_attachments($scounter,$offset,$paid);
	} elseif( $type == 10 ) {
		$gallery_id=isset($param_array['nextgen-id'])?$param_array['nextgen-id']:'';
		$anchor=isset($param_array['nextgen-anchor'])?$param_array['nextgen-anchor']:'';
		$slider_html=return_featured_slider_nggallery($gallery_id,$anchor,$scounter,$offset);
			
	} elseif( $type == 11 ) {
		$type='playlist';
		$val = isset($param_array['yt-playlist-id'])?$param_array['yt-playlist-id']:'';
		$slider_html=return_featured_slider_youtube($type,$val,$scounter,$offset);
			
	} elseif( $type == 12 ) {
		$type='search';
		$val = isset($param_array['yt-search-term'])?$param_array['yt-search-term']:'';
		$slider_html=return_featured_slider_youtube($type,$val,$scounter,$offset);
			
	} elseif( $type == 13 ) {
		$type=isset($param_array['vimeo-type'])?$param_array['vimeo-type']:'';
		$val = isset($param_array['vimeo-val'])?$param_array['vimeo-val']:'';
		$slider_html=return_featured_slider_vimeo($type,$val,$scounter,$offset);
			
	} elseif( $type == 14 ) { 
		$type='page';
		$album = isset($param_array['fb-album'])?$param_array['fb-album']:'';
		$slider_html=return_featured_slider_facebook($type,$album,$scounter,$offset);
			
	} elseif( $type == 15 ) {
		$type='';
		$username = isset($param_array['user-name'])?$param_array['user-name']:'';
		$slider_html=return_featured_slider_instagram($type,$username,$scounter,$offset);
			
	} elseif( $type == 16 ) {
		$type=isset($param_array['flickr-type'])?$param_array['flickr-type']:'';
		$fid = isset($param_array['fl-id'])?$param_array['fl-id']:'';
		$slider_html=return_featured_slider_flickr($type,$fid,$scounter,$offset);
			
	} elseif( $type == 18 ) {
		$feature=isset($param_array['feature'])?$param_array['feature']:'';
		$username = isset($param_array['pxuser'])?$param_array['pxuser']:'';
		$slider_html=return_featured_slider_500px($feature,$username,$scounter,$offset);
	}

	/* Add Edit Slider Link on front end */
	$editurlhtml='';	
	if ( !is_admin() && is_user_logged_in() && (count($result) > 0) ) {
		if ( current_user_can('manage_options') ) {
			$editurl = featured_sslider_admin_url( array( 'page' => 'featured-slider-easy-builder' ) ).'&id='.$id;
			$editurlhtml='<a class="featured-edit" href="'.$editurl.'">'.__('Edit Slider','featured-slider').'</a>';
		}
	}
	return $slider_html.$editurlhtml;
}
add_shortcode('featuredslider', 'featured_slider_simple_shortcode');

function return_featured_slider_category($catg_slug='', $set='', $offset=0, $data=array()) {
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider = get_option('featured_slider_options');
	//Select Settings Set from Meta Box
	if(is_singular() and empty($set)) {
		global $post;
		$sel_set = get_post_meta($post->ID,'_featured_select_set',true);
		if(!empty($sel_set) and $sel_set!='1') $set=$sel_set;
	}
	// If setting set is 1 then set to blank	
	if($set == '1') $set = '';

 	$featured_slider_options='featured_slider_options'.$set;
    	$featured_slider_curr=get_option($featured_slider_options);
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	
	$slider_handle='featured_slider_'.$catg_slug;
	$data['slider_handle']=$slider_handle;
    	$r_array = featured_carousel_posts_on_slider_category($featured_slider_curr['no_posts'], $catg_slug, $offset, '0', $set, $data); 
	//get slider 
	$slider_html=return_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,$echo='0',$data);
	
	return $slider_html;
}

function featured_slider_category_shortcode($atts) {
	extract(shortcode_atts(array(
		'catg_slug' => '',
		'set' => '',
		'offset'=>'0',
	), $atts));

	return return_featured_slider_category($catg_slug,$set,$offset);
}
add_shortcode('featuredcategory', 'featured_slider_category_shortcode');

function return_featured_slider_recent($set='',$offset=0, $data=array()) {
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider = get_option('featured_slider_options');
	//Select Settings Set from Meta Box
	if(is_singular() and empty($set)) {
		global $post;
		$sel_set = get_post_meta($post->ID,'_featured_select_set',true);
		if(!empty($sel_set) and $sel_set!='1') $set=$sel_set;
	}
	// If setting set is 1 then set to blank	
	if($set == '1') $set = '';
 	$featured_slider_options='featured_slider_options'.$set;
    	$featured_slider_curr=get_option($featured_slider_options);
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	
	$slider_handle='featured_slider_recent';
	$data['slider_handle']=$slider_handle;
	$r_array = featured_carousel_posts_on_slider_recent($featured_slider_curr['no_posts'], $offset, '0', $set,$data);  
	//get slider 
	$slider_html=return_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,$echo='0',$data);
	
	return $slider_html;
}

function featured_slider_recent_shortcode($atts) {
	extract(shortcode_atts(array(
		'set' => '',
		'offset'=>'0',
	), $atts));
	return return_featured_slider_recent($set,$offset);
}
add_shortcode('featuredrecent', 'featured_slider_recent_shortcode');
?>
