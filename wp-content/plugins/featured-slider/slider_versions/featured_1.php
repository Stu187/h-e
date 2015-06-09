<?php 
function featured_global_data_processor( $slides, $featured_slider_curr,$out_echo,$set,$data=array() ){ 
	if( $featured_slider_curr['disable_mobile'] != 1 or !wp_is_mobile() ) {
	//If no Skin specified, consider Default
	$skin='default';
	if(isset($featured_slider_curr['stylesheet'])) $skin=$featured_slider_curr['stylesheet'];
	if(empty($skin))$skin='default';
	
	//Include default skin
	if(!file_exists(dirname( dirname(__FILE__) ) . '/var/skins/'.$skin.'/functions.php'))
		require_once ( dirname( dirname(__FILE__) ) . '/var/skins/default/functions.php');
	//Include Skin function file
	if(file_exists(dirname( dirname(__FILE__) ) . '/var/skins/'.$skin.'/functions.php'))
		require_once ( dirname( dirname(__FILE__) ) . '/var/skins/'.$skin.'/functions.php');
	
	//Skin specific data processor and html generation
	$data_processor_fn='featured_data_processor_'.$skin;
	if(!function_exists($data_processor_fn))$data_processor_fn='featured_data_processor_default';

	$r_array=$data_processor_fn($slides, $featured_slider_curr,$out_echo,$set,$data);
	return $r_array;	
	}
}
function featured_global_posts_processor( $posts, $featured_slider_curr,$out_echo,$set,$data=array() ){
	if( $featured_slider_curr['disable_mobile'] != 1 or !wp_is_mobile() ) {
	//If no Skin specified, consider Default
	$skin='default';
	if(isset($featured_slider_curr['stylesheet'])) $skin=$featured_slider_curr['stylesheet'];
	if(empty($skin))$skin='default';
	
	//Include default skin
	if(!file_exists(dirname( dirname(__FILE__) ) . '/var/skins/'.$skin.'/functions.php'))
		require_once ( dirname( dirname(__FILE__) ) . '/var/skins/default/functions.php');
	//Include Skin function file
	if(file_exists(dirname( dirname(__FILE__) ) . '/var/skins/'.$skin.'/functions.php')) require_once ( dirname( dirname(__FILE__) ) . '/var/skins/'.$skin.'/functions.php');
	
	//Skin specific post processor and html generation
	$post_processor_fn='featured_post_processor_'.$skin;
	if(!function_exists($post_processor_fn))$post_processor_fn='featured_post_processor_default';
	$r_array=$post_processor_fn($posts, $featured_slider_curr,$out_echo,$set,$data);
	return $r_array;
	}	
}

function get_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,$echo='1',$data=array() ){
	if( $featured_slider_curr['disable_mobile'] != 1 or !wp_is_mobile() ) {
	//If no Skin specified, consider Default
	$skin='default';
	if(isset($featured_slider_curr['stylesheet'])) $skin=$featured_slider_curr['stylesheet'];
	if(empty($skin))$skin='default';
	
	//Include CSS
	wp_enqueue_style( 'featured_'.$skin, featured_slider_plugin_url( 'var/skins/'.$skin.'/style.css' ),	false, FEATURED_SLIDER_VER, 'all');
	
	//Include default skin
	if(!file_exists(dirname( dirname(__FILE__) ) . '/var/skins/'.$skin.'/functions.php'))
		require_once ( dirname( dirname(__FILE__) ) . '/var/skins/default/functions.php');
	//Include Skin function file
	if(file_exists(dirname( dirname(__FILE__) ) . '/var/skins/'.$skin.'/functions.php'))
	require_once ( dirname( dirname(__FILE__) ) . '/var/skins/'.$skin.'/functions.php');
	
	//Skin specific post processor and html generation
	$get_processor_fn='featured_slider_get_'.$skin;
	if(!function_exists($get_processor_fn))$get_processor_fn='featured_slider_get_default';
	$r_array=$get_processor_fn($slider_handle,$r_array,$featured_slider_curr,$set,$echo,$data);
	return $r_array;
	}	
}

function featured_carousel_posts_on_slider($max_posts, $offset=0, $slider_id = '1',$out_echo = '1',$set='', $data=array() ) {
	$featured_slider = get_option('featured_slider_options');
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider_options='featured_slider_options'.$set;
    	$featured_slider_curr=get_option($featured_slider_options);
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
		
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.FEATURED_SLIDER_TABLE;
	$post_table = $table_prefix."posts";
	$rand = $featured_slider_curr['rand'];
	if(isset($rand) and $rand=='1'){
	  	$orderby = 'RAND()';
	}
	else {
	  	$orderby = 'a.slide_order ASC, a.date DESC';
	}
	
	//WPML
	if( function_exists('icl_plugin_action_links') ) {
		$tr_table = $table_prefix."icl_translations";
		$posts = $wpdb->get_results("SELECT b.* FROM 
                     $table_name a 
		 LEFT OUTER JOIN $post_table b 
			ON a.post_id = b.ID 
		 LEFT OUTER JOIN $tr_table t 
			ON a.post_id = t.element_id 
		  WHERE ((b.post_status = 'publish' AND t.language_code = '".ICL_LANGUAGE_CODE."') OR (b.post_type='attachment' AND b.post_status = 'inherit')) 
		 AND a.slider_id = '$slider_id' 
		 ORDER BY ".$orderby." LIMIT $offset, $max_posts", OBJECT);
	}
	else {
		$posts = $wpdb->get_results("SELECT b.* FROM 
             	 $table_name a 
		 LEFT OUTER JOIN $post_table b 
			ON a.post_id = b.ID 
		 WHERE (b.post_status = 'publish' OR (b.post_type='attachment' AND b.post_status = 'inherit')) 
		 AND a.slider_id = '$slider_id' 
		 ORDER BY ".$orderby." LIMIT $offset, $max_posts", OBJECT);
	}

	$r_array=featured_global_posts_processor( $posts, $featured_slider_curr, $out_echo, $set, $data );
	return $r_array;
}

function get_featured_slider($slider_id='',$set='',$offset=0, $title='', $data=array() ) {
	if(empty($slider_id) or !isset($slider_id)){
		$slider_id = '1';
	}
	global $wpdb,$table_prefix;
	$gfeatured_slider = get_option('featured_slider_global_options');
	$slider_meta = $table_prefix.FEATURED_SLIDER_META;
	$sql = "SELECT * FROM $slider_meta WHERE slider_id = $slider_id"; // WHERE type=0
	$result = $wpdb->get_row($sql);
	$type = isset($result->type)?$result->type:'';
	$scounter = isset($result->setid)?$result->setid :'';
	if($scounter == 1 ) $scounter = '';
	if(count($result) > 0) $param_array = unserialize($result->param);
	$data=array();
	$data['title']=$title;
	$data['slider_id']=$slider_id;
	//Select Settings Set from Meta Box
	if(is_singular()) {
		global $post;
		$sel_set = get_post_meta($post->ID,'_featured_select_set',true);
		if(!empty($sel_set) and $sel_set!='1') {
			$set=$sel_set;
			$scounter = $sel_set;
		}
	}
	if( $type == 0 || $type == 17 ) { 
		$featured_slider = get_option('featured_slider_options');
		$default_featured_slider_settings=get_featured_slider_default_settings();
		// If template Tag is not having set or offset then pick it from DataBase
		if($set == '' ) $set = $scounter; 
		if($offset == 0) $offset = isset($param_array['offset'])?$param_array['offset']:'0';
		//Select Settings Set from Meta Box
		if(is_singular() and empty($set)) {
			global $post;
			$sel_set = get_post_meta($post->ID,'_featured_select_set',true);
			if(!empty($sel_set) and $sel_set!='1') $set=$sel_set;
		}
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
		if(!empty($slider_id)){
			$data['slider_id']=$slider_id;
			$slider_handle='featured_slider_'.$slider_id;
			$data['slider_handle']=$slider_handle;
			$r_array = featured_carousel_posts_on_slider($featured_slider_curr['no_posts'], $offset, $slider_id, '0', $set, $data); 
			$sliderhtml = get_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,$echo='1',$data);
		} //end of not empty slider_id condition
	} elseif( $type == 1 ) {
		$offset = isset($param_array['offset'])?$param_array['offset']:'0';
		$catg_slug=isset($param_array['catg_slug'])?$param_array['catg_slug']:'';
		get_featured_slider_category($catg_slug=$catg_slug,$set=$scounter,$title,$offset=$offset,$data);
		
	} elseif( $type == 2 ) {
		$offset = isset($param_array['offset'])?$param_array['offset']:'0';
		get_featured_slider_recent($set=$scounter,$title,$offset=$offset,$data);
	} elseif( $type == 3 ) {
		$args['offset'] = isset($param_array['offset'])?$param_array['offset']:'0';
		if(isset($param_array['woo-catg'])) {
			$args['term'] = isset($param_array['woo-catg'])?$param_array['woo-catg']:'';
			$args['taxonomy'] = 'product_cat';
		}
		$args['type'] = isset($param_array['woo_slider_type'])?$param_array['woo_slider_type']:'';
		$args['post_type']='product';
		$args['product_id']= isset($param_array['product_id'])?$param_array['product_id']:''; ;
		$args['set']=$scounter;
		$args['data']=$data;
		get_featured_slider_woocommerce($args);
	} elseif( $type == 4 ) {
		$args['offset'] = isset($param_array['offset'])?$param_array['offset']:'0';
		if(isset($param_array['ecom_slider_type']) && $param_array['ecom_slider_type'] == '1' && isset($param_array['ecom-catg']) ) {
				$args['term'] = isset($param_array['ecom-catg'])?$param_array['ecom-catg']:'';
				$args['taxonomy'] = 'wpsc_product_category';
		}
		$args['post_type']='wpsc-product';
		$args['set']=$scounter;
		$args['data']['type'] = 'ecom';
		get_featured_slider_taxonomy($args);
	} elseif( $type == 5 ) {
		$args['offset'] = isset($param_array['offset'])?$param_array['offset']:'0';
		if(isset($param_array['eman-catg']) && $param_array['eman-catg'] != '' )
			$args['term'] = isset($param_array['eman-catg'])?$param_array['eman-catg']:'';
		if(isset($param_array['eman-tags']) && $param_array['eman-tags'] != '' ) 
			$args['tags'] = isset($param_array['eman-tags'])?$param_array['eman-tags']:'';
		$args['scope'] = isset($param_array['eventm_slider_scope'])?$param_array['eventm_slider_scope']:'';
		$args['post_type']='event';
		$args['set']=$scounter;
		$args['data']=$data;
		get_featured_slider_event($args);
	} elseif( $type == 6 ) {
		$args['offset'] = isset($param_array['offset'])?$param_array['offset']:'0';
		if(isset($param_array['ecal-catg']) && $param_array['ecal-catg'] != '' ) {
			$args['term'] = isset($param_array['ecal-catg'])?$param_array['ecal-catg']:'';
			$args['taxonomy'] = 'tribe_events_cat';
		}
		if(isset($param_array['ecal-tags']) && $param_array['ecal-tags'] != '' ) 
			$args['tags'] = isset($param_array['ecal-tags'])?$param_array['ecal-tags']:'';
		$args['type'] = isset($param_array['eventcal_slider_type'])?$param_array['eventcal_slider_type']:'';
		$args['post_type']='tribe_events';
		$args['set']=$scounter;
		$args['data']=$data;
get_featured_slider_event_calender($args);
	} elseif( $type == 7 ) {
		$data['author']=isset($param_array['taxonomy_author'])?$param_array['taxonomy_author']:'';
		$args=array(		
			'post_type'=>isset($param_array['post_type'])?$param_array['post_type']:'',
			'taxonomy'=>isset($param_array['taxonomy_name'])?$param_array['taxonomy_name']:'',
			'term'=>isset($param_array['taxonomy_term'])?$param_array['taxonomy_term']:'',
			'set'=>$scounter,
			'offset'=>isset($param_array['offset'])?$param_array['offset']:'0',
			'show'=>isset($param_array['taxonomy_show'])?$param_array['taxonomy_show']:'',
			'operator'=>isset($param_array['taxonomy_operator'])?$param_array['taxonomy_operator']:'',
			'data'=>$data
		);
		get_featured_slider_taxonomy($args);
	} elseif( $type == 8 ) {
		$args=array(
			'set'=>$scounter,
			'offset'=>isset($param_array['offset'])?$param_array['offset']:'0',
			'feedurl'=>isset($param_array['feed_url'])?$param_array['feed_url']:'', 
			'default_image'=>isset($param_array['feed_img'])?$param_array['feed_img']:'',
			'title'=>'',
			'id'=>isset($param_array['feed_id'])?$param_array['feed_id']:'',
			'feed'=>isset($param_array['feed'])?$param_array['feed']:'',
			'order'=>isset($param_array['feed_order'])?$param_array['feed_order']:'',
			'content'=>isset($param_array['feed_content'])?$param_array['feed_content']:'', 
			'media'=>isset($param_array['feed_media'])?$param_array['feed_media']:'',
			'src'=>isset($param_array['feed_src'])?$param_array['feed_src']:'',
			'size'=>isset($param_array['feed_size'])?$param_array['feed_size']:'',
			'image_class'=>isset($param_array['feed_imgclass'])?$param_array['feed_imgclass']:'',
			'data'=>$data
		);
		get_featured_slider_feed($args);
	} elseif( $type == 9 ) {
		$args=array(
			'set'=>$scounter,
			'offset'=>isset($param_array['offset'])?$param_array['offset']:'0',
			'id'=>isset($param_array['postattch-id'])?$param_array['postattch-id']:'',
			'data'=>$data
		);
		get_featured_slider_attachments($args);
	} elseif( $type == 10 ) {
		$args=array(
			'gallery_id'=>$param_array['nextgen-id'],
			'set'=>$scounter,
			'offset'=>isset($param_array['offset'])?$param_array['offset']:'0',
			'anchor'=>isset($param_array['nextgen-anchor'])?$param_array['nextgen-anchor']:'',
			'data'=>$data
		);
		get_featured_slider_ngg($args);
	} elseif( $type == 11 ) {
		$args=array(
			'type'=>'playlist',
			'val'=>isset($param_array['yt-playlist-id'])?$param_array['yt-playlist-id']:'',
			'set'=>$scounter,
			'offset'=>isset($param_array['offset'])?$param_array['offset']:'0',
			'data'=>$data
		);
		get_featured_youtube_slider($args);
	} elseif( $type == 12 ) {
		$args = array(
			'type'=>'search',
			'val'=>isset($param_array['yt-search-term'])?$param_array['yt-search-term']:'',
			'set'=>$scounter,
			'offset'=>isset($param_array['offset'])?$param_array['offset']:'0',
			'data'=>$data
		);
		get_featured_youtube_slider($args);
	} elseif( $type == 13 ) {
		$vimeotype = isset($param_array['vimeo-type'])?$param_array['vimeo-type']:'';
		$args = array(
			'type'=>$vimeotype,
			'val'=>$param_array['vimeo-val'],
			'set'=>$scounter,
			'offset'=>isset($param_array['offset'])?$param_array['offset']:'0',
			'data'=>$data
		);
		get_featured_vimeo_slider($args);
	} elseif( $type == 14 ) {
		$pageurl = isset($param_array['fb-pg-url'])?$param_array['fb-pg-url']:'';
		$fbalbum = isset($param_array['fb-album'])?$param_array['fb-album']:'';
		$args = array(
			'page'=>$pageurl,
			'album'=>$fbalbum,
			'set'=>$scounter,
			'offset'=>isset($param_array['offset'])?$param_array['offset']:'0',
			'data'=>$data
		);
		get_featured_facebook_slider($args);
	} elseif( $type == 15 ) {
		$args = array(
			'username'=>isset($param_array['user-name'])?$param_array['user-name']:'',
			'set'=>$scounter,
			'offset'=>isset($param_array['offset'])?$param_array['offset']:'0',
			'data'=>$data
		);
		get_featured_instagram_slider($args);
	} elseif( $type == 16 ) {
		$flickrtype = isset($param_array['flickr-type'])?$param_array['flickr-type']:'';
		$args = array(
			'type'=>$flickrtype,
			'id'=>isset($param_array['fl-id'])?$param_array['fl-id']:'',
			'set'=>$scounter,
			'offset'=>isset($param_array['offset'])?$param_array['offset']:'0',
			'data'=>$data
		);
		get_featured_flickr_slider($args);
	} elseif( $type == 18 ) {
		$feature = isset($param_array['feature'])?$param_array['feature']:'';
		$args = array(
			'feature'=>$feature,
			'username'=>isset($param_array['pxuser'])?$param_array['pxuser']:'',
			'set'=>$scounter,
			'offset'=>isset($param_array['offset'])?$param_array['offset']:'0',
			'data'=>$data
		);
		get_featured_500px_slider($args);
	}

	/* Add Edit Slider Link on front end */
	$editurlhtml='';	
	if ( !is_admin() && is_user_logged_in() && (count($result) > 0) ) {
		if ( current_user_can('manage_options') ) {
			$editurl = featured_sslider_admin_url( array( 'page' => 'featured-slider-easy-builder' ) ).'&id='.$slider_id;
			$editurlhtml='<a class="featured-edit" href="'.$editurl.'">'.__('Edit Slider','featured-slider').'</a>';
		}
	}
	echo $editurlhtml;
}

//For displaying category specific posts in chronologically reverse order
function featured_carousel_posts_on_slider_category($max_posts='5', $catg_slug='', $offset=0, $out_echo = '1', $set='', $data=array() ) {
   	$featured_slider = get_option('featured_slider_options');
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider_options='featured_slider_options'.$set;
   	$featured_slider_curr=get_option($featured_slider_options);
   	
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	
	$featured_slider_curr= populate_featured_current($featured_slider_curr);

	global $wpdb, $table_prefix;
	
	if (!empty($catg_slug)) {
		$category = get_category_by_slug($catg_slug); 
		$slider_cat = $category->term_id;
	}
	else {
		$category = get_the_category();
		$slider_cat = isset($category[0]->cat_ID)?$category[0]->cat_ID:1;
	}
	//WPML
	if( function_exists('icl_plugin_action_links') ) {
		$tr_table = $table_prefix."icl_translations";
		$slider_cat = $wpdb->get_var("
			SELECT element_id 
			FROM $tr_table 
			WHERE element_type = 'tax_category' 
			AND language_code = '".ICL_LANGUAGE_CODE."' 
			AND trid = ( 	SELECT trid 
				FROM $tr_table 
				WHERE element_type = 'tax_category' 
				AND element_id = $slider_cat
			)
		");
	}	
	//WPML END
	$rand = $featured_slider_curr['rand'];
	if(isset($rand) and $rand=='1') $orderby = '&orderby=rand';
	else $orderby = '';
	
	//extract the posts
	$posts = get_posts('numberposts='.$max_posts.'&offset='.$offset.'&category='.$slider_cat.$orderby);
	
	$r_array=featured_global_posts_processor( $posts, $featured_slider_curr, $out_echo,$set,$data );
	return $r_array;
}

function get_featured_slider_category($catg_slug='', $set='', $title='', $offset=0, $data=array()) {
   	$featured_slider = get_option('featured_slider_options');
	$default_featured_slider_settings=get_featured_slider_default_settings();
	//Select Settings Set from Meta Box
	if(is_singular() and empty($set)) {
		global $post;
		$sel_set = get_post_meta($post->ID,'_featured_select_set',true);
		if(!empty($sel_set) and $sel_set!='1') $set=$sel_set;
	}	 
 	$featured_slider_options='featured_slider_options'.$set;
   	$featured_slider_curr=get_option($featured_slider_options);
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	
	$data['title']=$title;
	if( !$offset or empty($offset) or !is_numeric($offset)  ) $offset=0;
   	$slider_handle='featured_slider_'.$catg_slug;
    $data['slider_handle']=$slider_handle;
	$r_array = featured_carousel_posts_on_slider_category($featured_slider_curr['no_posts'], $catg_slug, $offset, '0', $set, $data); 
	get_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,$echo='1',$data);
} 

//For displaying recent posts in chronologically reverse order
function featured_carousel_posts_on_slider_recent($max_posts='5', $offset=0, $out_echo = '1', $set='', $data=array()) {
   	$featured_slider = get_option('featured_slider_options');
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider_options='featured_slider_options'.$set;
    	$featured_slider_curr=get_option($featured_slider_options);
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
    	
 	//WPML
	if( function_exists('icl_plugin_action_links') ) {
		global $wpdb, $table_prefix;
		$post_table = $table_prefix."posts";
		$tr_table = $table_prefix."icl_translations";
		$posts=$wpdb->get_results("SELECT *
			FROM $post_table AS p
			LEFT OUTER JOIN $tr_table AS t 
			ON p.ID = t.element_id 
			WHERE t.element_type = 'post_post' 
			AND t.language_code = '".ICL_LANGUAGE_CODE."' 
			AND p.post_status = 'publish' 
			ORDER BY p.post_date DESC 
			LIMIT $offset, $max_posts
		");
	}
	else {
		$posts = get_posts('numberposts='.$max_posts.'&offset='.$offset);
	}
	
	//randomize the slides
	$rand = $featured_slider_curr['rand'];
	if(isset($rand) and $rand=='1'){
	  	shuffle($posts);
	}
	$r_array=featured_global_posts_processor( $posts, $featured_slider_curr, $out_echo,$set,$data );
	return $r_array;
}

function get_featured_slider_recent($set='',$title='',$offset=0,$data=array()) {
	$featured_slider = get_option('featured_slider_options');
	$default_featured_slider_settings=get_featured_slider_default_settings();
	//Select Settings Set from Meta Box
	if(is_singular() and empty($set)) {
		global $post;
		$sel_set = get_post_meta($post->ID,'_featured_select_set',true);
		if(!empty($sel_set) and $sel_set!='1') $set=$sel_set;
	}
 	$featured_slider_options='featured_slider_options'.$set;
    	$featured_slider_curr=get_option($featured_slider_options);
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	
	$data['title']=$title;
	if( !$offset or empty($offset) or !is_numeric($offset)  ) $offset=0;
	$slider_handle='featured_slider_recent';
	$data['slider_handle']=$slider_handle;
	$r_array = featured_carousel_posts_on_slider_recent($featured_slider_curr['no_posts'], $offset, '0', $set, $data);
	get_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,$echo='1',$data);
}

require_once (dirname (__FILE__) . '/shortcodes_1.php');
require_once (dirname (__FILE__) . '/widgets_1.php');

// Add-on Inclusions
include_once (dirname( dirname (__FILE__) ) . '/includes/addons/rssfeed.php');
include_once (dirname( dirname (__FILE__) ) . '/includes/addons/attachments.php');
include_once (dirname( dirname (__FILE__) ) . '/includes/addons/taxonomy.php');
include_once (dirname( dirname (__FILE__) ) . '/includes/addons/woocom.php');
include_once (dirname( dirname (__FILE__) ) . '/includes/addons/events.php');
include_once (dirname( dirname (__FILE__) ) . '/includes/addons/event_cal.php');
include_once (dirname( dirname (__FILE__) ) . '/includes/addons/nggallery.php');
// For social and videos
include_once (dirname( dirname (__FILE__) ) . '/includes/addons/px.php');
include_once (dirname( dirname (__FILE__) ) . '/includes/addons/flickr.php');
include_once (dirname( dirname (__FILE__) ) . '/includes/addons/instagram.php');
include_once (dirname( dirname (__FILE__) ) . '/includes/addons/youtube.php');
include_once (dirname( dirname (__FILE__) ) . '/includes/addons/vimeo.php');
include_once (dirname( dirname (__FILE__) ) . '/includes/addons/fb.php');


function featured_slider_enqueue_scripts() {
	$gfeatured_slider = get_option('featured_slider_global_options'); 
	wp_enqueue_script( 'jquery');
	if( isset($gfeatured_slider['enque_scripts']) and $gfeatured_slider['enque_scripts']=='1' ) { 
		if((is_admin() && isset($_GET['page']) && ( ('featured-slider-settings' == $_GET['page'] or 'featured-slider-easy-builder' == $_GET['page']) ) ) || !is_admin() ) {
			wp_enqueue_script( 'featured-slider-script', featured_slider_plugin_url( 'js/featured.js' ),array('jquery'), FEATURED_SLIDER_VER, false);

 		}
	}
}
add_action( 'init', 'featured_slider_enqueue_scripts' );

//admin settings
function featured_slider_admin_scripts() {
$gfeatured_slider = get_option('featured_slider_global_options'); 
  if ( is_admin() ){ // admin actions
  // Settings page only
	if ( isset($_GET['page']) && ('featured-slider-admin' == $_GET['page'] or 'featured-slider-settings' == $_GET['page'] or 'featured-slider-easy-builder' == $_GET['page'] or 'manage-featured-slider' == $_GET['page'] || 'featured-slider-global-settings' == $_GET['page'])  ) {
		wp_register_script('jquery', false, false, false, false);
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		if ( ! did_action( 'wp_enqueue_media' ) ) wp_enqueue_media();
		wp_enqueue_script('jquery-ui-autocomplete');
		wp_enqueue_script( 'featured_slider_admin_js', featured_slider_plugin_url( 'js/admin.js' ),array('jquery'), FEATURED_SLIDER_VER, false);
		wp_enqueue_style( 'featured_fontawesome_css', featured_slider_plugin_url( 'includes/font-awesome/css/font-awesome.min.css' ),false, FEATURED_SLIDER_VER, 'all');
		wp_enqueue_script( 'easing', featured_slider_plugin_url( 'js/jquery.easing.js' ),false, FEATURED_SLIDER_VER, false);
	}
	// JS/CSS for Easy Builder Page
	if( isset($_GET['page']) && 'featured-slider-easy-builder' == $_GET['page'] ) {
		wp_enqueue_script( 'accordition', featured_slider_plugin_url( 'js/jquery.accordion.js' ),
			array('jquery'), FEATURED_SLIDER_VER, false);
		/* All Litebox JS and CSS*/
		wp_enqueue_script( 'jquery.prettyPhoto', featured_slider_plugin_url( 'js/jquery.prettyPhoto.js' ), array('jquery'), FEATURED_SLIDER_VER, true);
		wp_enqueue_style( 'prettyPhoto_css', featured_slider_plugin_url( 'var/css/prettyPhoto.css' ), false, FEATURED_SLIDER_VER, 'all');
		wp_enqueue_script( 'jquery.swipebox', featured_slider_plugin_url( 'js/jquery.swipebox.js' ), array('jquery'), FEATURED_SLIDER_VER, true);
		wp_enqueue_style( 'swipebox_css', featured_slider_plugin_url( 'var/css/swipebox.css' ), false, FEATURED_SLIDER_VER, 'all');
		wp_enqueue_script( 'jquery.nivobox', featured_slider_plugin_url( 'js/nivo-lightbox.js' ), array('jquery'), FEATURED_SLIDER_VER, true);
		wp_enqueue_style( 'nivobox_css', featured_slider_plugin_url( 'var/css/nivobox.css' ), false, FEATURED_SLIDER_VER, 'all');
		wp_enqueue_script( 'jquery.photobox', featured_slider_plugin_url( 'js/jquery.photobox.js' ), array('jquery'), FEATURED_SLIDER_VER, true);
		wp_enqueue_style( 'photobox_css', featured_slider_plugin_url( 'var/css/photobox.css' ), false, FEATURED_SLIDER_VER, 'all');
		wp_enqueue_script( 'jquery.smoothbox', featured_slider_plugin_url( 'js/smoothbox.js' ), array('jquery'), FEATURED_SLIDER_VER, false);
		wp_enqueue_style( 'smoothbox_css', featured_slider_plugin_url( 'var/css/smoothbox.css' ), false, FEATURED_SLIDER_VER, 'all');	
		wp_enqueue_script( 'featured-slider-script', featured_slider_plugin_url( 'js/featured.js' ),array('jquery'), FEATURED_SLIDER_VER, false);
	}
	// JS/CSS for Manage Sliders Page
	if( isset($_GET['page']) && 'manage-featured-slider' == $_GET['page'] ) {
		/* Data Tables JS/CSS */
		wp_enqueue_script( 'featured_datatable_admin_js', featured_slider_plugin_url( 'js/jquery.dataTables.min.js' ), array('jquery'), FEATURED_SLIDER_VER, false);
		wp_enqueue_style( 'featured_datatable_admin_css', featured_slider_plugin_url( 'var/css/jquery.dataTables.min.css' ), false, FEATURED_SLIDER_VER, 'all');
		/* END Data Tables JS/CSS  */
	}
  }
}

add_action( 'admin_init', 'featured_slider_admin_scripts' );

function featured_slider_admin_head() {
if ( is_admin() ){ // admin actions
	if ( isset($_GET['page']) && ('featured-slider-admin' == $_GET['page'] or 'featured-slider-settings' == $_GET['page'] or 'featured-slider-easy-builder' == $_GET['page'] or 'manage-featured-slider' == $_GET['page'] || 'featured-slider-global-settings' == $_GET['page'])  ) {
		wp_enqueue_style( 'featured_slider_admin_css', featured_slider_plugin_url( 'var/css/admin.css' ),false, FEATURED_SLIDER_VER, 'all');
	}
	// Sliders & Settings page only
    if ( isset($_GET['page']) && ('featured-slider-admin' == $_GET['page'] or 'featured-slider-settings' == $_GET['page'] or 'featured-slider-easy-builder' == $_GET['page']  or 'manage-featured-slider' == $_GET['page'] ) ) {
		$flag = 0;
		$slider = isset($_GET['id'])?$_GET['id']:'0';	
	  	$sliders = featured_ss_get_sliders(); 
		foreach($sliders as $key=>$val) {
			if(is_array($val)) {
				if($val['slider_id'] == $slider)
					$flag = 1;
			}
		}
	?>
	<script type="text/javascript">
        // <![CDATA[
        jQuery(document).ready(function() {
		<?php
		if ( 'featured-slider-easy-builder' == $_GET['page'] && $flag == 1 ) { ?>
			jQuery("#sslider_sortable_<?php echo $slider ?>").sortable({ items: ".featured-reorder" });
			jQuery("#sslider_sortable_<?php echo $slider ?>").disableSelection();
        	<?php } ?>
	});
		
        function confirmRemove()
        {
            var agree=confirm("This will remove selected Posts/Pages from Slider.");
            if (agree)
            return true ;
            else
            return false ;
        }
        function confirmRemoveAll()
        {
            var agree=confirm("Remove all Posts/Pages from Featured Slider??");
            if (agree)
            return true ;
            else
            return false ;
        }
        function confirmSliderDelete()
        {
            var agree=confirm("Delete this Slider??");
            if (agree)
            return true ;
            else
            return false ;
        }
        function slider_checkform ( form )
        {
          if (form.new_slider_name.value == "") {
            alert( "Please enter the New Slider name." );
            form.new_slider_name.focus();
            return false ;
          }
          return true ;
        }
        </script>
<?php
   } //Sliders page only
  // Settings page only
   if ( isset($_GET['page']) && ( 'featured-slider-settings' == $_GET['page'] || 'featured-slider-easy-builder' == $_GET['page'] ) ) {
		wp_enqueue_style( 'wp-color-picker' );
   		wp_enqueue_script( 'wp-color-picker' );
		
?>
<script type="text/javascript">
function confirmSettingsCreate()
        {
            var agree=confirm("Create New Settings Set??");
            if (agree)
            return true ;
            else
            return false ;
}
function confirmSettingsDelete()
        {
            var agree=confirm("Delete this Settings Set??");
            if (agree)
            return true ;
            else
            return false ;
}
</script>
	<style type="text/css">.color-picker-wrap {position: absolute;	display: none; background: #fff;border: 3px solid #ccc;	padding: 3px;z-index: 1000;}</style>
	<?php
   } //for featured slider option page  
 }//only for admin
//Below css will add the menu icon for Featured Slider admin menu
?>
<style type="text/css">#adminmenu #toplevel_page_featured-slider-admin div.wp-menu-image:before { content: "\f233"; }</style>
<?php
}
add_action('admin_head', 'featured_slider_admin_head');

//get inline css with style attribute attached
function featured_get_inline_css($set='',$echo='0'){ 

	$featured_slider = get_option('featured_slider_options');
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider_options='featured_slider_options'.$set;
    	$featured_slider_curr=get_option($featured_slider_options);

	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);	
	//If no Skin specified, consider Default
	$skin='default';
	if(isset($featured_slider_curr['stylesheet'])) $skin=$featured_slider_curr['stylesheet'];
	if(empty($skin))$skin='default';
	
	//Include default skin
	if(!file_exists(dirname( dirname(__FILE__) ) . '/var/skins/'.$skin.'/functions.php'))
		require_once ( dirname( dirname(__FILE__) ) . '/var/skins/default/functions.php');
	//Include Skin function file
	if(file_exists(dirname( dirname(__FILE__) ) . '/var/skins/'.$skin.'/functions.php'))
		require_once ( dirname( dirname(__FILE__) ) . '/var/skins/'.$skin.'/functions.php');
		
	//Skin specific data processor and html generation
	$data_processor_fn='featured_data_processor_'.$skin;
	if(function_exists($data_processor_fn))$default=true;
	else $default=false;
	$featured_slider_css=array('featured_slider'=>'',
				'title_fstyle'=>'',
				'sldr_title'=>'',
				'featured_slider_instance'=>'',
				'featured_slide'=>'',
				'featured_slideri_br'=>'',
				'featured_slideri'=>'',
				'featured_slider_h2'=>'',
				'featured_slider_h2_a'=>'',
				'featured_slider_sub_h2'=>'',
				'featured_slider_sub_h2_a'=>'',
				'featured_slider_span'=>'',
				'featured_slider_thumbnail'=>'',
				'featured_slider_eshortcode'=>'',
				'featured_slider_p_more'=>'',
				'featured_meta'=>'',
				'featured_next'=>'',
				'featured_prev'=>'',
				'featured_nav'=>'',
				'featured_nav_a'=>'',
				
				// woo
				'featured_woo_add_to_cart'=>'',
				'featured_ecom_add_to_cart'=>'',
				'featured_woo_sale_strip'=>'',
				'featured_rateit'=>'',
				'featured_slide_wooprice'=>'', 
				'featured_slide_woosaleprice'=>'',
				'featured_slide_cat'=>'',
				// events manager
				'slide_eventm_datetime'=>'',
				'eventm_addr'=>'',
				'eventm_cat'=>'',
				'slide_sub_right'=>''
				 );
	if($default){
		$style_start= ($echo=='0') ? 'style="':'';
		$style_end= ($echo=='0') ? '"':'';
			
		if ($featured_slider_curr['title_fstyle'] == "bold" or $featured_slider_curr['title_fstyle'] == "bold italic" ){$slider_title_fontw = "bold";} else { $slider_title_fontw = "normal"; }
		if ($featured_slider_curr['title_fstyle'] == "italic" or $featured_slider_curr['title_fstyle'] == "bold italic" ){$slider_title_style = "italic";} else {$slider_title_style = "normal";}
		$sldr_title = $featured_slider_curr['title_text']; if(!empty($sldr_title)) { $slider_title_margin = "5px 0 10px 0"; } else {$slider_title_margin = "0";} 
	//sldr_title
	
		/* New fonts application - start -*/
		$t_font = $t_fontw =$t_fontst ="";
		if( $featured_slider_curr['t_font'] == 'regular' ) {
			$t_font = $featured_slider_curr['title_font'].', Arial, Helvetica, sans-serif';
			$t_fontw = $slider_title_fontw;
			$t_fontst = $slider_title_style;
		} else if( $featured_slider_curr['t_font'] == 'google' ) {
			$ttle_fontg=isset($featured_slider_curr['title_fontg'])?trim($featured_slider_curr['title_fontg']):'';
			$pgfont= get_featured_google_font($featured_slider_curr['title_fontg']);
			(isset($pgfont['category']))?$tfamily = $pgfont['category']:'';
			(isset($featured_slider_curr['title_fontgw']))?$tfontw = $featured_slider_curr['title_fontgw']:''; 
			if (strpos($tfontw,'italic') !== false) {
				$t_fontst = 'italic';
			} else {
				$t_fontst = 'normal';
			}
			if( strpos($tfontw,'italic') > 0 ) { 
				$len = strpos($tfontw,'italic');
				$tfontw = substr( $tfontw, 0, $len );
			}
			if( strpos($tfontw,'regular') !== false ) { 
				$tfontw = 'normal';
			}
			if(isset($featured_slider_curr['title_fontgw']) && !empty($featured_slider_curr['title_fontgw']) ){
				$currfontw=$featured_slider_curr['title_fontgw'];
				$gfonturl = $pgfont['urls'][$currfontw];
			
			}  else {
				$gfonturl = 'http://fonts.googleapis.com/css?family='.$featured_slider_curr['title_fontg'];
			}
			if(isset($featured_slider_curr['title_fontgsubset']) && !empty($featured_slider_curr['title_fontgsubset']) ){
				$strsubset = implode(",",$featured_slider_curr['title_fontgsubset']);
				$gfonturl = $gfonturl.'&subset='.$strsubset;
			} 
			if(!empty($ttle_fontg)) 	{
				wp_enqueue_style( 'featured_title'.$set, $gfonturl,array(),FEATURED_SLIDER_VER);
				$ttle_fontg=$pgfont['name'];
				$t_font = $ttle_fontg.','.$tfamily;
				$t_fontw = $tfontw;	
			}
			else { //if not set google font fall back to default font
				
				$t_font = 'Arial, Helvetica, sans-serif';
				$t_fontw = 'normal';
				$t_fontst = 'normal';
			}
		} else if( $featured_slider_curr['t_font'] == 'custom' ) {
			$t_font = $featured_slider_curr['titlefont_custom'];
			$t_fontw = $slider_title_fontw;
			$t_fontst = $slider_title_style;
		}
		/* New fonts application - end -*/	
	
		$featured_slider_css['sldr_title']=$style_start.'font-family:'.$t_font.' '.$featured_slider_curr['title_font'].';font-size:'.$featured_slider_curr['title_fsize'].'px;font-weight:'.$t_fontw.';font-style:'.$t_fontst.';color:'.$featured_slider_curr['title_fcolor'].';margin:'.$slider_title_margin.$style_end;
 
		if ($featured_slider_curr['bg'] == '1') { $featured_slideri_bg = "transparent";} else { $featured_slideri_bg = $featured_slider_curr['bg_color']; }
	//featured slider
	$featured_slider_css['featured_slider']=$style_start.'max-width:'.$featured_slider_curr['width'].'px;'.$style_end;
	//featured_slider_instance
		$featured_slider_css['featured_slider_instance']=$style_start.'background-color:'.$featured_slideri_bg.';border:'.$featured_slider_curr['border'].'px solid '.$featured_slider_curr['brcolor'].';height:'. $featured_slider_curr['height'].'px;overflow:hidden;'.$style_end;
	
	
	$featured_slider_css['featured_slider_thumbnail']=$style_start.$style_end;
	//featured_slide
		$featured_slider_css['featured_slide']=$style_start.'height:100%;'.$style_end;
		
	//featured_slider_h2
		if ($featured_slider_curr['ptitle_fstyle'] == "bold" or $featured_slider_curr['ptitle_fstyle'] == "bold italic" ){$ptitle_fweight = "bold";} else {$ptitle_fweight = "normal";}
		if ($featured_slider_curr['ptitle_fstyle'] == "italic" or $featured_slider_curr['ptitle_fstyle'] == "bold italic"){$ptitle_fstyle = "italic";} else {$ptitle_fstyle = "normal";}
		/* New fonts application - start -*/
		$pt_font = $pt_fontw =$pt_fontst ="";
		if( $featured_slider_curr['pt_font'] == 'regular' ) {
			$pt_font = $featured_slider_curr['ptitle_font'].', Arial, Helvetica, sans-serif';
			$pt_fontw = $ptitle_fweight;
			$pt_fontst = $ptitle_fstyle;
		} else if( $featured_slider_curr['pt_font'] == 'google' ) {
			$ptitle_fontg=isset($featured_slider_curr['ptitle_fontg'])?trim($featured_slider_curr['ptitle_fontg']):'';
			$pgfont=get_featured_google_font($featured_slider_curr['ptitle_fontg']);
			(isset($pgfont['category']))?$ptfamily = $pgfont['category']:'';
			(isset($featured_slider_curr['ptitle_fontgw']))?$ptfontw = $featured_slider_curr['ptitle_fontgw']:''; 
			if (strpos($ptfontw,'italic') !== false) {
				$pt_fontst = 'italic';
			} else {
				$pt_fontst = 'normal';
			}
			if( strpos($ptfontw,'italic') > 0 ) { 
				$len = strpos($ptfontw,'italic');
				$ptfontw = substr( $ptfontw, 0, $len );
			}
			if( strpos($ptfontw,'regular') !== false ) { 
				$ptfontw = 'normal';
			}
			if(isset($featured_slider_curr['ptitle_fontgw']) && !empty($featured_slider_curr['ptitle_fontgw']) ){
				$currfontw=$featured_slider_curr['ptitle_fontgw'];
				$gfonturl = $pgfont['urls'][$currfontw];
			
			}  else {
				$gfonturl = 'http://fonts.googleapis.com/css?family='.$featured_slider_curr['ptitle_fontg'];
			}
			if(isset($featured_slider_curr['ptitle_fontgsubset']) && !empty($featured_slider_curr['ptitle_fontgsubset']) ){
				$strsubset = implode(",",$featured_slider_curr['ptitle_fontgsubset']);
				$gfonturl = $gfonturl.'&subset='.$strsubset;
			} 
			if(!empty($ptitle_fontg)) 	{
				wp_enqueue_style( 'featured_ptitle'.$set, $gfonturl,array(),FEATURED_SLIDER_VER);
				$ptitle_fontg=$pgfont['name'];
				$pt_font = $ptitle_fontg.','.$ptfamily;
				$pt_fontw = $ptfontw;	
			}
			else { //if not set google font fall back to default font
				
				$pt_font = 'Arial, Helvetica, sans-serif';
				$pt_fontw = 'normal';
				$pt_fontst = 'normal';
			}
		} else if( $featured_slider_curr['pt_font'] == 'custom' ) {
			$pt_font = $featured_slider_curr['ptfont_custom'];
			$pt_fontw = $ptitle_fweight;
			$pt_fontst = $ptitle_fstyle;
		}
		/* New fonts application - end -*/
		
		$featured_slider_css['featured_slider_h2']=$style_start.'clear:none;line-height:'. ($featured_slider_curr['ptitle_fsize'] + 3) .'px;font-family:'. $pt_font . ' '. $featured_slider_curr['ptitle_font'].';font-size:'.$featured_slider_curr['ptitle_fsize'].'px;font-weight:'.$pt_fontw.';font-style:'.$pt_fontst.';color:'.$featured_slider_curr['ptitle_fcolor'].';'.$style_end;

	//Feratured Slider Sub H2
 
		//featured_slider_h2
		if ($featured_slider_curr['sub_ptitle_fstyle'] == "bold" or $featured_slider_curr['sub_ptitle_fstyle'] == "bold italic" ){$sub_ptitle_fweight = "bold";} else {$sub_ptitle_fweight = "normal";}
		if ($featured_slider_curr['sub_ptitle_fstyle'] == "italic" or $featured_slider_curr['sub_ptitle_fstyle'] == "bold italic"){$sub_ptitle_fstyle = "italic";} else {$sub_ptitle_fstyle = "normal";}
	
		/* New fonts application - start -*/
		$sub_pt_font = $sub_pt_fontw =$sub_pt_fontst ="";
		if( $featured_slider_curr['sub_pt_font'] == 'regular' ) {
			$sub_pt_font = $featured_slider_curr['sub_ptitle_font'].', Arial, Helvetica, sans-serif';
			$sub_pt_fontw = $sub_ptitle_fweight;
			$sub_pt_fontst = $sub_ptitle_fstyle;
		} else if( $featured_slider_curr['sub_pt_font'] == 'google' ) {
			$sub_ptitle_fontg=isset($featured_slider_curr['sub_ptitle_fontg'])?trim($featured_slider_curr['sub_ptitle_fontg']):'';
			$sub_pgfont=get_featured_google_font($featured_slider_curr['sub_ptitle_fontg']);
			(isset($sub_pgfont['category']))?$sub_ptfamily = $sub_pgfont['category']:'';
			(isset($featured_slider_curr['sub_ptitle_fontgw']))?$sub_ptfontw = $featured_slider_curr['sub_ptitle_fontgw']:''; 
			if (strpos($sub_ptfontw,'italic') !== false) {
				$sub_pt_fontst = 'italic';
			} else {
				$sub_pt_fontst = 'normal';
			}
			if( strpos($sub_ptfontw,'italic') > 0 ) { 
				$len = strpos($sub_ptfontw,'italic');
				$sub_ptfontw = substr( $sub_ptfontw, 0, $len );
			}
			if( strpos($sub_ptfontw,'regular') !== false ) { 
				$sub_ptfontw = 'normal';
			}
			if(isset($featured_slider_curr['sub_ptitle_fontgw']) && !empty($featured_slider_curr['sub_ptitle_fontgw']) ){
				$sub_currfontw=$featured_slider_curr['sub_ptitle_fontgw'];
				$sub_gfonturl = $sub_pgfont['urls'][$sub_currfontw];
			
			} else {
				$sub_gfonturl = 'http://fonts.googleapis.com/css?family='.$featured_slider_curr['sub_ptitle_fontg'];
			}
			if(isset($featured_slider_curr['sub_ptitle_fontgsubset']) && !empty($featured_slider_curr['sub_ptitle_fontgsubset']) ){
				$strsubset = implode(",",$featured_slider_curr['sub_ptitle_fontgsubset']);
				$sub_gfonturl = $sub_gfonturl.'&subset='.$strsubset;
			} 
			if(!empty($sub_ptitle_fontg)) 	{
				wp_enqueue_style( 'featured_sub_ptitle'.$set, $sub_gfonturl,array(),FEATURED_SLIDER_VER);
				$sub_ptitle_fontg=$sub_pgfont['name'];
				$sub_pt_font = $sub_ptitle_fontg.','.$sub_ptfamily;
				$sub_pt_fontw = $sub_ptfontw;	
			}
			else { //if not set google font fall back to default font
				$sub_pt_font = 'Arial, Helvetica, sans-serif';
				$sub_pt_fontw = 'normal';
				$sub_pt_fontst = 'normal';
			}
		} else if( $featured_slider_curr['sub_pt_font'] == 'custom' ) {
			$sub_pt_font = $featured_slider_curr['sub_ptfont_custom'];
			$sub_pt_fontw = $sub_ptitle_fweight;
			$sub_pt_fontst = $sub_ptitle_fstyle;
		}
		/* New fonts application - end -*/
 
	$featured_slider_css['featured_slider_sub_h2']=$style_start.'clear:none;line-height:'. ($featured_slider_curr['sub_ptitle_fsize'] + 3) .'px;font-family:'. $sub_pt_font.';font-size:'.$featured_slider_curr['sub_ptitle_fsize'].'px;font-weight:'.$sub_ptitle_fweight.';font-style:'.$sub_pt_fontst.';color:'.$featured_slider_curr['sub_ptitle_fcolor'].';margin:0 0 5px 0;'.$style_end;

	//featured_slider_h2 a
		$featured_slider_css['featured_slider_h2_a']=$style_start.'color:'.$featured_slider_curr['ptitle_fcolor'].';'.$style_end;
	
		if ($featured_slider_curr['content_fstyle'] == "bold" or $featured_slider_curr['content_fstyle'] == "bold italic" ){$content_fweight= "bold";} else {$content_fweight= "normal";}
		if ($featured_slider_curr['content_fstyle']=="italic" or $featured_slider_curr['content_fstyle'] == "bold italic"){$content_fstyle= "italic";} else {$content_fstyle= "normal";}
	//featured_slider_sub_h2 a
		$featured_slider_css['featured_slider_sub_h2_a']=$style_start.'color:'.$featured_slider_curr['sub_ptitle_fcolor'].';'.$style_end;
	//featured_slider_span
		 
		$pc_font = $pc_fontw =$pc_fontst ="";
		if( $featured_slider_curr['pc_font'] == 'regular' ) {
			$pc_font = $featured_slider_curr['content_font'].', Arial, Helvetica, sans-serif';
			$pc_fontw = $content_fweight;
			$pc_fontst = $content_fstyle;
		} else if( $featured_slider_curr['pc_font'] == 'google' ) {
			$content_fontg=isset($featured_slider_curr['content_fontg'])?trim($featured_slider_curr['content_fontg']):'';
			$pgfont=get_featured_google_font($featured_slider_curr['content_fontg']);
			(isset($pgfont['category']))?$pcfamily = $pgfont['category']:'';
			(isset($featured_slider_curr['content_fontgw']))?$pcfontw = $featured_slider_curr['content_fontgw']:''; 
			if (strpos($pcfontw,'italic') !== false) {
				$pc_fontst = 'italic';
			} else {
				$pc_fontst = 'normal';
			}
			if( strpos($pcfontw,'italic') > 0 ) { 
				$len = strpos($pcfontw,'italic');
				$pcfontw = substr( $pcfontw, 0, $len );
			}
			if( strpos($pcfontw,'regular') !== false ) { 
				$pcfontw = 'normal';
			}
			if(isset($featured_slider_curr['content_fontgw']) && !empty($featured_slider_curr['content_fontgw']) ){
				$currfontw=$featured_slider_curr['content_fontgw'];
				$gfonturl = $pgfont['urls'][$currfontw];
			} else {
				$gfonturl = 'http://fonts.googleapis.com/css?family='.$featured_slider_curr['content_fontg'];
			}
			if(isset($featured_slider_curr['content_fontgsubset']) && !empty($featured_slider_curr['content_fontgsubset']) )			{
				$strsubset = implode(",",$featured_slider_curr['content_fontgsubset']);
				$gfonturl = $gfonturl.'&subset='.$strsubset;
			}
			if(!empty($content_fontg)) 	{
				wp_enqueue_style( 'featured_content'.$set, $gfonturl,array(),FEATURED_SLIDER_VER);
				$content_fontg=$pgfont['name'];
				$pc_font = $content_fontg.','.$pcfamily;
				$pc_fontw = $pcfontw;	
			}
			else { //if not set google font fall back to default font
				
				$pc_font = 'Arial, Helvetica, sans-serif';
				$pc_fontw = 'normal';
				$pc_fontst = 'normal';
			}
		} else if( $featured_slider_curr['pc_font'] == 'custom' ) {
			$pc_font = $featured_slider_curr['pcfont_custom'];
			$pc_fontw = $content_fweight;
			$pc_fontst = $content_fstyle;
		}
		
		$featured_slider_css['featured_slider_span']=$style_start.'font-family:'.$pc_font.' '.$featured_slider_curr['content_font'].';font-size:'.$featured_slider_curr['content_fsize'].'px;font-weight:'.$pc_fontw.';font-style:'.$pc_fontst.';color:'. $featured_slider_curr['content_fcolor'].';'.$style_end;
	
		//featured_slider_p_more
		$featured_slider_css['featured_slider_p_more']=$style_start.'color:'.$featured_slider_curr['more_color'].';font-family:'.$pc_font.' '.$featured_slider_curr['content_font'].';font-size:'.$featured_slider_curr['content_fsize'].'px;text-decoration: none;font-style: italic;'.$style_end;
		//Sub Slide spacing 

		if($featured_slider_curr['block_pos']=='1') {	
			$side_border = 'border-left:'.$featured_slider_curr['slide_border'].'px solid '.$featured_slider_curr['slide_brcolor'];
		}
		else {
			$side_border = 'border-right:'.$featured_slider_curr['slide_border'].'px solid '.$featured_slider_curr['slide_brcolor'];
			
		}
			$featured_slider_css['slide_sub_right']=$style_start.'border-bottom:'.$featured_slider_curr['slide_border'].'px solid '.$featured_slider_curr['slide_brcolor'].';'.$side_border.$style_end;
	 
		if($featured_slider_curr['stylesheet']=='trio') {
			$featured_slider_css['slide_sub_right_trio']=$style_start.'border-bottom:'.$featured_slider_curr['slide_border'].'px solid '.$featured_slider_curr['slide_brcolor'].';width:100%;'.$side_border.$style_end;
		}

	
// Added for wooCom and Events
// woo add to cart
			$featured_slider_css['featured_woo_add_to_cart'] = $style_start.'background:'.$featured_slider_curr['woo_adc_color'].'; font-size:'.$featured_slider_curr['woo_adc_fsize'].'px;color:'.$featured_slider_curr['woo_adc_tcolor'].'; border:'.$featured_slider_curr['woo_adc_border'].'px solid '.$featured_slider_curr['woo_adc_brcolor'].';'.$style_end;
			// e com
			$featured_slider_css['featured_ecom_add_to_cart'] = '{ "background":"'.$featured_slider_curr['woo_adc_color'].'","font-size":"'.$featured_slider_curr['woo_adc_fsize'].'px","color":"'.$featured_slider_curr['woo_adc_tcolor'].'","border":"'.$featured_slider_curr['woo_adc_border'].'px solid '.$featured_slider_curr['woo_adc_brcolor'].'","border-radius":"0","padding":"1px 6px"}';
			// woo sale strip
			$featured_slider_css['featured_woo_sale_strip'] = $style_start.'background-color:'.$featured_slider_curr['woo_sale_color'].';color:'.$featured_slider_curr['woo_sale_tcolor'].';'.$style_end;
			// woo slide price
			if ($featured_slider_curr['slide_woo_price_fstyle'] == "bold" or $featured_slider_curr['slide_woo_price_fstyle'] == "bold italic" ){$wprice_fweight = "bold";} else {$wprice_fweight = "normal";}
			if ($featured_slider_curr['slide_woo_price_fstyle'] == "italic" or $featured_slider_curr['slide_woo_price_fstyle'] == "bold italic"){$wprice_fstyle = "italic";} else {$wprice_fstyle = "normal";}
			/* New fonts application - start -*/
			$woo_font = $woo_fontw = $woo_fontst = '';
			if( $featured_slider_curr['woo_font'] == 'regular' ) {
				$woo_font = $featured_slider_curr['slide_woo_price_font'].', Arial, Helvetica, sans-serif';
				$woo_fontw = $wprice_fweight;
				$woo_fontst = $wprice_fstyle;
			} else if( $featured_slider_curr['woo_font'] == 'google' ) {
				$slide_woo_price_fontg=isset($featured_slider_curr['slide_woo_price_fontg'])?trim($featured_slider_curr['slide_woo_price_fontg']):'';
				$pgfont=get_featured_google_font($featured_slider_curr['slide_woo_price_fontg']);
				(isset($pgfont['category']))?$woofamily = $pgfont['category']:'';
				$woofontw = $featured_slider_curr['slide_woo_price_fontgw']; 
				if (strpos($woofontw,'italic') !== false) {
					$woo_fontst = 'italic';
				} else {
					$woo_fontst = 'normal';
				}
				if( strpos($woofontw,'italic') > 0 ) { 
					$len = strpos($woofontw,'italic');
					$woofontw = substr( $woofontw, 0, $len );
				}
				if( strpos($woofontw,'regular') !== false ) { 
					$woofontw = 'normal';
				}
				if(isset($featured_slider_curr['slide_woo_price_fontgw']) && !empty($featured_slider_curr['slide_woo_price_fontgw']) ){
					$currfontw=$featured_slider_curr['slide_woo_price_fontgw'];
					$gfonturl = $pgfont['urls'][$currfontw];
			
				}  else {
					$gfonturl = 'http://fonts.googleapis.com/css?family='.$featured_slider_curr['slide_woo_price_fontg'];
				}
				if(isset($featured_slider_curr['slide_woo_price_fontgsubset']) && !empty($featured_slider_curr['slide_woo_price_fontgsubset']) ){
					$strsubset = implode(",",$featured_slider_curr['slide_woo_price_fontgsubset']);
					$gfonturl = $gfonturl.'&subset='.$strsubset;
				} 
				if(!empty($slide_woo_price_fontg)) {
					wp_enqueue_style( 'featured_slide_woo_price'.$set, $gfonturl,array(),FEATURED_SLIDER_VER);
					$slide_woo_price_fontg=$pgfont['name'];
					$woo_font = $slide_woo_price_fontg.','.$woofamily;
					$woo_fontw = $woofontw;	
				}
				else { //if not set google font fall back to default font
				
					$woo_font = 'Arial, Helvetica, sans-serif';
					$woo_fontw = 'normal';
					$woo_fontst = 'normal';
				}
			} else if( $featured_slider_curr['woo_font'] == 'custom' ) {
				$woo_font = $featured_slider_curr['slide_woo_price_custom'];
				$woo_fontw =  $wprice_fweight;
				$woo_fontst = $wprice_fstyle;
			}
			/* New fonts application - end -*/		
			$featured_slider_css['featured_slide_wooprice'] = $style_start.'font-family:'.$woo_font.';color:'.$featured_slider_curr['slide_woo_price_fcolor'].';font-weight:'.$woo_fontw.';font-style:'.$woo_fontst.';font-size:'.$featured_slider_curr['slide_woo_price_fsize'].'px; padding-right: 10px;'.$style_end;
			// woo sale slide price
			if ($featured_slider_curr['slide_woo_saleprice_fstyle'] == "bold" or $featured_slider_curr['slide_woo_saleprice_fstyle'] == "bold italic" ){$saleprice_fweight = "bold";} else {$saleprice_fweight = "normal";}
			if ($featured_slider_curr['slide_woo_saleprice_fstyle'] == "italic" or $featured_slider_curr['slide_woo_saleprice_fstyle'] == "bold italic"){$saleprice_fstyle = "italic";} else {$saleprice_fstyle = "normal";}
			/* New fonts application - start -*/
			$woosale_font = $woosale_fontw = $woosale_fontst = '';
			if( $featured_slider_curr['woosale_font'] == 'regular' ) {
				$woosale_font = $featured_slider_curr['slide_woo_saleprice_font'].', Arial, Helvetica, sans-serif';
				$woosale_fontw = $saleprice_fweight;
				$woosale_fontst = $saleprice_fstyle;
			} else if( $featured_slider_curr['woosale_font'] == 'google' ) {
				$slide_woo_saleprice_fontg=isset($featured_slider_curr['slide_woo_saleprice_fontg'])?trim($featured_slider_curr['slide_woo_saleprice_fontg']):'';
				$pgfont=get_featured_google_font($featured_slider_curr['slide_woo_saleprice_fontg']);
				(isset($pgfont['category']))?$woosalefamily = $pgfont['category']:'';
				(isset($featured_slider_curr['slide_woo_saleprice_fontgw']))?$woosalefontw = $featured_slider_curr['slide_woo_saleprice_fontgw']:''; 
				if (strpos($woosalefontw,'italic') !== false) {
					$woosale_fontst = 'italic';
				} else {
					$woosale_fontst = 'normal';
				}
				if( strpos($woosalefontw,'italic') > 0 ) { 
					$len = strpos($woosalefontw,'italic');
					$woosalefontw = substr( $woosalefontw, 0, $len );
				}
				if( strpos($woosalefontw,'regular') !== false ) { 
					$woosalefontw = 'normal';
				}
				if(isset($featured_slider_curr['slide_woo_saleprice_fontgw']) && !empty($featured_slider_curr['slide_woo_saleprice_fontgw']) ){
					$currfontw=$featured_slider_curr['slide_woo_saleprice_fontgw'];
					$gfonturl = $pgfont['urls'][$currfontw];
			
				}  else {
					$gfonturl = 'http://fonts.googleapis.com/css?family='.$featured_slider_curr['slide_woo_saleprice_fontg'];
				}
				if(isset($featured_slider_curr['slide_woo_saleprice_fontgsubset']) && !empty($featured_slider_curr['slide_woo_saleprice_fontgsubset']) ){
					$strsubset = implode(",",$featured_slider_curr['slide_woo_saleprice_fontgsubset']);
					$gfonturl = $gfonturl.'&subset='.$strsubset;
				} 
				if(!empty($slide_woo_saleprice_fontg)) 	{
					wp_enqueue_style( 'featured_slide_woo_saleprice'.$set, $gfonturl,array(),FEATURED_SLIDER_VER);
					$slide_woo_saleprice_fontg=$pgfont['name'];
					$woosale_font = $slide_woo_saleprice_fontg.','.$woosalefamily;
					$woosale_fontw = $woosalefontw;	
				}
				else { //if not set google font fall back to default font
				
					$woosale_font = 'Arial, Helvetica, sans-serif';
					$woosale_fontw = 'normal';
					$woosale_fontst = 'normal';
				}
			} else if( $featured_slider_curr['woosale_font'] == 'custom' ) {
				$woosale_font = $featured_slider_curr['slide_woo_saleprice_custom'];
				$woosale_fontw =  $saleprice_fweight;
				$woosale_fontst = $saleprice_fstyle;
			}
			/* New fonts application - end -*/		
			$featured_slider_css['featured_slide_woosaleprice'] = $style_start.'font-family:'.$woosale_font.';color:'.$featured_slider_curr['slide_woo_saleprice_fcolor'].';font-weight:'.$woosale_fontw.';font-style:'.$woosale_fontst.';font-size:'.$featured_slider_curr['slide_woo_saleprice_fsize'].'px; padding-right: 8px;'.$style_end;
			// woo sale slide category
			if ($featured_slider_curr['slide_woo_cat_fstyle'] == "bold" or $featured_slider_curr['slide_woo_cat_fstyle'] == "bold italic" ){$wcat_fweight = "bold";} else {$wcat_fweight = "normal";}
			if ($featured_slider_curr['slide_woo_cat_fstyle'] == "italic" or $featured_slider_curr['slide_woo_cat_fstyle'] == "bold italic"){$wcat_fstyle = "italic";} else {$wcat_fstyle = "normal";}
			/* New fonts application - start -*/
			$woocat_font = $woocat_fontw = $woocat_fontst = '';
			if( $featured_slider_curr['woocat_font'] == 'regular' ) {
				$woocat_font = $featured_slider_curr['slide_woo_cat_font'].', Arial, Helvetica, sans-serif';
				$woocat_fontw = $wcat_fweight;
				$woocat_fontst = $wcat_fstyle;
			} else if( $featured_slider_curr['woocat_font'] == 'google' ) {
				$slide_woo_cat_fontg=isset($featured_slider_curr['slide_woo_cat_fontg'])?trim($featured_slider_curr['slide_woo_cat_fontg']):'';
				$pgfont=get_featured_google_font($featured_slider_curr['slide_woo_cat_fontg']);
				(isset($pgfont['category']))?$woocatfamily = $pgfont['category']:'';
				(isset($featured_slider_curr['slide_woo_cat_fontgw']))?$woocatfontw = $featured_slider_curr['slide_woo_cat_fontgw']:''; 
				if (strpos($woocatfontw,'italic') !== false) {
					$woocat_fontst = 'italic';
				} else {
					$woocat_fontst = 'normal';
				}
				if( strpos($woocatfontw,'italic') > 0 ) { 
					$len = strpos($woocatfontw,'italic');
					$woocatfontw = substr( $woocatfontw, 0, $len );
				}
				if( strpos($woocatfontw,'regular') !== false ) { 
					$woocatfontw = 'normal';
				}
				if(isset($featured_slider_curr['slide_woo_cat_fontgw']) && !empty($featured_slider_curr['slide_woo_cat_fontgw']) ){
					$currfontw=$featured_slider_curr['slide_woo_cat_fontgw'];
					$gfonturl = $pgfont['urls'][$currfontw];
			
				}  else {
					$gfonturl = 'http://fonts.googleapis.com/css?family='.$featured_slider_curr['slide_woo_cat_fontg'];
				}
				if(isset($featured_slider_curr['slide_woo_cat_fontgsubset']) && !empty($featured_slider_curr['slide_woo_cat_fontgsubset']) ){
					$strsubset = implode(",",$featured_slider_curr['slide_woo_cat_fontgsubset']);
					$gfonturl = $gfonturl.'&subset='.$strsubset;
				} 
				if(!empty($slide_woo_cat_fontg)) 	{
					wp_enqueue_style( 'featured_slide_woo_cat'.$set, $gfonturl,array(),FEATURED_SLIDER_VER);
					$slide_woo_cat_fontg=$pgfont['name'];
					$woocat_font = $slide_woo_cat_fontg.','.$woocatfamily;
					$woocat_fontw = $woocatfontw;	
				}
				else { //if not set google font fall back to default font
				
					$woocat_font = 'Arial, Helvetica, sans-serif';
					$woocat_fontw = 'normal';
					$woocat_fontst = 'normal';
				}
			} else if( $featured_slider_curr['woocat_font'] == 'custom' ) {
				$woocat_font = $featured_slider_curr['slide_woo_cat_custom'];
				$woocat_fontw = $wcat_fweight;
				$woocat_fontst = $wcat_fstyle;
			}
			/* New fonts application - end -*/		
			$featured_slider_css['featured_slide_cat'] = $style_start.'font-family:'.$woocat_font.'; color:'.$featured_slider_curr['slide_woo_cat_fcolor'].' !important;font-weight:'.$woocat_fontw.';font-style:'.$woocat_fontst.';font-size:'.$featured_slider_curr['slide_woo_cat_fsize'].'px;'.$style_end;
			// events manager slide date-time
			if ($featured_slider_curr['slide_eventm_fstyle'] == "bold" or $featured_slider_curr['slide_eventm_fstyle'] == "bold italic" ){$sevent_fweight = "bold";} else {$sevent_fweight = "normal";}
			if ($featured_slider_curr['slide_eventm_fstyle'] == "italic" or $featured_slider_curr['slide_eventm_fstyle'] == "bold italic"){$sevent_fstyle = "italic";} else {$sevent_fstyle = "normal";}
			/* New fonts application - start -*/
			$eventmd_font = $eventmd_fontw = $eventmd_fontst = '';
			if( $featured_slider_curr['eventmd_font'] == 'regular' ) {
				$eventmd_font = $featured_slider_curr['slide_eventm_font'].', Arial, Helvetica, sans-serif';
				$eventmd_fontw = $sevent_fweight;
				$eventmd_fontst = $sevent_fstyle;
			} else if( $featured_slider_curr['eventmd_font'] == 'google' ) {
				$slide_eventm_fontg=isset($featured_slider_curr['slide_eventm_fontg'])?trim($featured_slider_curr['slide_eventm_fontg']):'';
				$pgfont=get_featured_google_font($featured_slider_curr['slide_eventm_fontg']);
				(isset($pgfont['category']))?$eventmdfamily = $pgfont['category']:'';
				(isset($featured_slider_curr['slide_eventm_fontgw']))?$eventmdfontw = $featured_slider_curr['slide_eventm_fontgw']:''; 
				if (strpos($eventmdfontw,'italic') !== false) {
					$eventmd_fontst = 'italic';
				} else {
					$eventmd_fontst = 'normal';
				}
				if( strpos($eventmdfontw,'italic') > 0 ) { 
					$len = strpos($eventmdfontw,'italic');
					$eventmdfontw = substr( $eventmdfontw, 0, $len );
				}
				if( strpos($eventmdfontw,'regular') !== false ) { 
					$eventmdfontw = 'normal';
				}
				if(isset($featured_slider_curr['slide_eventm_fontgw']) && !empty($featured_slider_curr['slide_eventm_fontgw']) ){
					$currfontw=$featured_slider_curr['slide_eventm_fontgw'];
					$gfonturl = $pgfont['urls'][$currfontw];
			
				}  else {
					$gfonturl = 'http://fonts.googleapis.com/css?family='.$featured_slider_curr['slide_eventm_fontg'];
				}
				if(isset($featured_slider_curr['slide_eventm_fontgsubset']) && !empty($featured_slider_curr['slide_eventm_fontgsubset']) ){
					$strsubset = implode(",",$featured_slider_curr['slide_eventm_fontgsubset']);
					$gfonturl = $gfonturl.'&subset='.$strsubset;
				} 
				if(!empty($slide_eventm_fontg)) 	{
					wp_enqueue_style( 'featured_slide_eventm_fontg'.$set, $gfonturl,array(),FEATURED_SLIDER_VER);
					$slide_eventm_fontg=$pgfont['name'];
					$eventmd_font = $slide_eventm_fontg.','.$eventmdfamily;
					$eventmd_fontw = $eventmdfontw;	
				}
				else { //if not set google font fall back to default font
				
					$eventmd_font = 'Arial, Helvetica, sans-serif';
					$eventmd_fontw = 'normal';
					$eventmd_fontst = 'normal';
				}
			} else if( $featured_slider_curr['eventmd_font'] == 'custom' ) {
				$eventmd_font = $featured_slider_curr['slide_eventm_custom'];
				$eventmd_fontw = $sevent_fweight;
				$eventmd_fontst = $sevent_fstyle;
			}
			/* New fonts application - end -*/		
			$featured_slider_css['slide_eventm_datetime'] = $style_start.'font-family:'.$eventmd_font.'; color:'.$featured_slider_curr['slide_eventm_fcolor'].';font-weight:'.$eventmd_fontw.';font-style:'.$eventmd_fontst.';font-size:'.$featured_slider_curr['slide_eventm_fsize'].'px;'.$style_end;
			
			// event address
			if ($featured_slider_curr['eventm_addr_fstyle'] == "bold" or $featured_slider_curr['eventm_addr_fstyle'] == "bold italic" ){$eventaddr_fweight = "bold";} else {$eventaddr_fweight = "normal";}
			if ($featured_slider_curr['eventm_addr_fstyle'] == "italic" or $featured_slider_curr['eventm_addr_fstyle'] == "bold italic"){$eventaddr_fstyle = "italic";} else {$eventaddr_fstyle = "normal";}
			/* New fonts application - start -*/
			$event_addr_font = $event_addr_fontw = $event_addr_fontst = '';
			if( $featured_slider_curr['event_addr_font'] == 'regular' ) {
				$event_addr_font = $featured_slider_curr['eventm_addr_font'].', Arial, Helvetica, sans-serif';
				$event_addr_fontw = $eventaddr_fweight;
				$event_addr_fontst = $eventaddr_fstyle;
			} else if( $featured_slider_curr['event_addr_font'] == 'google' ) {
				$eventm_addr_fontg=isset($featured_slider_curr['eventm_addr_fontg'])?trim($featured_slider_curr['eventm_addr_fontg']):'';
				$pgfont=get_featured_google_font($featured_slider_curr['eventm_addr_fontg']);
				(isset($pgfont['category']))?$event_addr_family = $pgfont['category']:'';
				(isset($featured_slider_curr['eventm_addr_fontgw']))?$event_addrs_fontw = $featured_slider_curr['eventm_addr_fontgw']:''; 
				if (strpos($event_addrs_fontw,'italic') !== false) {
					$event_addr_fontst = 'italic';
				} else {
					$event_addr_fontst = 'normal';
				}
				if( strpos($event_addrs_fontw,'italic') > 0 ) { 
					$len = strpos($event_addrs_fontw,'italic');
					$event_addrs_fontw = substr( $event_addrs_fontw, 0, $len );
				}
				if( strpos($event_addrs_fontw,'regular') !== false ) { 
					$event_addrs_fontw = 'normal';
				}
				if(isset($featured_slider_curr['eventm_addr_fontgw']) && !empty($featured_slider_curr['eventm_addr_fontgw']) ){
					$currfontw=$featured_slider_curr['eventm_addr_fontgw'];
					$gfonturl = $pgfont['urls'][$currfontw];
			
				}  else {
					$gfonturl = 'http://fonts.googleapis.com/css?family='.$featured_slider_curr['eventm_addr_fontg'];
				}
				if(isset($featured_slider_curr['eventm_addr_fontgsubset']) && !empty($featured_slider_curr['eventm_addr_fontgsubset']) ){
					$strsubset = implode(",",$featured_slider_curr['eventm_addr_fontgsubset']);
					$gfonturl = $gfonturl.'&subset='.$strsubset;
				} 
				if(!empty($eventm_addr_fontg)) 	{
					wp_enqueue_style( 'featured_eventm_address_fontg'.$set, $gfonturl,array(),FEATURED_SLIDER_VER);
					$eventm_addr_fontg=$pgfont['name'];
					$event_addr_font = $eventm_addr_fontg.','.$event_addr_family;
					$event_addr_fontw = $event_addrs_fontw;	
				}
				else { //if not set google font fall back to default font
				
					$event_addr_font = 'Arial, Helvetica, sans-serif';
					$event_addr_fontw = 'normal';
					$event_addr_fontst = 'normal';
				}
			} else if( $featured_slider_curr['event_addr_font'] == 'custom' ) {
				$event_addr_font = $featured_slider_curr['nav_eventm_custom'];
				$event_addr_fontw = $eventaddr_fweight;
				$event_addr_fontst = $eventaddr_fstyle;
			}
			/* New fonts application - end -*/		

			$featured_slider_css['eventm_addr'] = $style_start.'font-family:'.$event_addr_font.'; color:'.$featured_slider_curr['eventm_addr_fcolor'].';font-weight:'.$event_addr_fontw.';font-style:'.$event_addr_fontst.';font-size:'.$featured_slider_curr['eventm_addr_fsize'].'px;'.$style_end;
			// event category
			if ($featured_slider_curr['eventm_cat_fstyle'] == "bold" or $featured_slider_curr['eventm_cat_fstyle'] == "bold italic" ){$eventcat_fweight = "bold";} else {$eventcat_fweight = "normal";}
			if ($featured_slider_curr['eventm_cat_fstyle'] == "italic" or $featured_slider_curr['eventm_cat_fstyle'] == "bold italic"){$eventcat_fstyle = "italic";} else {$eventcat_fstyle = "normal";}
			/* New fonts application - start -*/
			$event_cat_font = $event_cat_fontw = $event_cat_fontst = '';
			if( $featured_slider_curr['event_cat_font'] == 'regular' ) {
				$event_cat_font = $featured_slider_curr['eventm_cat_font'].', Arial, Helvetica, sans-serif';
				$event_cat_fontw = $eventcat_fweight;
				$event_cat_fontst = $eventcat_fstyle;
			} else if( $featured_slider_curr['event_cat_font'] == 'google' ) {
				$eventm_cat_fontg=isset($featured_slider_curr['eventm_cat_fontg'])?trim($featured_slider_curr['eventm_cat_fontg']):'';
				$pgfont=get_featured_google_font($featured_slider_curr['eventm_cat_fontg']);
				(isset($pgfont['category']))?$event_cat_family = $pgfont['category']:'';
				(isset($featured_slider_curr['eventm_cat_fontgw']))?$event_cat_fontw = $featured_slider_curr['eventm_cat_fontgw']:''; 
				if (strpos($event_cat_fontw,'italic') !== false) {
					$event_cat_fontst = 'italic';
				} else {
					$event_cat_fontst = 'normal';
				}
				if( strpos($event_cat_fontw,'italic') > 0 ) { 
					$len = strpos($event_cat_fontw,'italic');
					$event_cat_fontw = substr( $event_cat_fontw, 0, $len );
				}
				if( strpos($event_cat_fontw,'regular') !== false ) { 
					$event_cat_fontw = 'normal';
				}
				if(isset($featured_slider_curr['eventm_cat_fontgw']) && !empty($featured_slider_curr['eventm_cat_fontgw']) ){
					$currfontw=$featured_slider_curr['eventm_cat_fontgw'];
					$gfonturl = $pgfont['urls'][$currfontw];
			
				}  else {
					$gfonturl = 'http://fonts.googleapis.com/css?family='.$featured_slider_curr['eventm_cat_fontg'];
				}
				if(isset($featured_slider_curr['eventm_cat_fontsubset']) && !empty($featured_slider_curr['eventm_cat_fontsubset']) ){
					$strsubset = implode(",",$featured_slider_curr['eventm_cat_fontsubset']);
					$gfonturl = $gfonturl.'&subset='.$strsubset;
				} 
				if(!empty($eventm_cat_fontg)) 	{
					wp_enqueue_style( 'featured_eventm_cat_fontg'.$set, $gfonturl,array(),FEATURED_SLIDER_VER);
					$eventm_cat_fontg=$pgfont['name'];
					$event_cat_font = $eventm_cat_fontg.','.$event_cat_family;
					$event_cat_fontw = $event_cat_fontw;	
				}
				else { //if not set google font fall back to default font
				
					$event_cat_font = 'Arial, Helvetica, sans-serif';
					$event_cat_fontw = 'normal';
					$event_cat_fontst = 'normal';
				}
			} else if( $featured_slider_curr['event_cat_font'] == 'custom' ) {
				$event_cat_font = $featured_slider_curr['nav_eventm_custom'];
				$event_cat_fontw = $eventcat_fweight;
				$event_cat_fontst = $eventcat_fstyle;
			}
			/* New fonts application - end -*/		
			$featured_slider_css['eventm_cat'] = $style_start.'font-family:'.$event_cat_font.'; color:'.$featured_slider_curr['eventm_cat_fcolor'].';font-weight:'.$event_cat_fontw.';font-style:'.$event_cat_fontst.';font-size:'.$featured_slider_curr['eventm_cat_fsize'].'px;'.$style_end;
	
// wooCom and Events end	
	//featured_meta
		$meta_border_color_arr=featured_hex2rgb($featured_slider_curr['content_fcolor']);
	    	if ($featured_slider_curr['meta_title_fstyle'] == "bold" or $featured_slider_curr['meta_title_fstyle'] == "bold italic" ){$meta_title_font_weight = "bold";} else { $meta_title_font_weight = "normal"; }
		if ($featured_slider_curr['meta_title_fstyle'] == "italic" or $featured_slider_curr['meta_title_fstyle'] == "bold italic" ){$meta_title_font_style = "italic";} else {$meta_title_font_style = "normal";}
		
		$mt_font = $mt_fontw = $mt_fontst = '';
		if( $featured_slider_curr['mt_font'] == 'regular' ) {
			$mt_font = $featured_slider_curr['meta_title_font'].', Arial, Helvetica, sans-serif';
			$mt_fontw = $meta_title_font_weight;
			$mt_fontst = $meta_title_font_style;
		} else if( $featured_slider_curr['mt_font'] == 'google' ) {
			
			$meta_title_fontg=isset($featured_slider_curr['meta_title_fontg'])?trim($featured_slider_curr['meta_title_fontg']):'';
			$pgfont=get_featured_google_font($featured_slider_curr['meta_title_fontg']);
			(isset($pgfont['category']))?$mtfamily = $pgfont['category']:'';
			(isset($featured_slider_curr['meta_title_fontgw']))?$mtfontw = $featured_slider_curr['meta_title_fontgw']:''; 
			if (strpos($mtfontw,'italic') !== false) {
				$mt_fontst = 'italic';
			} else {
				$mt_fontst = 'normal';
			}
			if( strpos($mtfontw,'italic') > 0 ) { 
				$len = strpos($mtfontw,'italic');
				$mtfontw = substr( $mtfontw, 0, $len );
			}
			if( strpos($mtfontw,'regular') !== false ) { 
				$mtfontw = 'normal';
			}
			if(isset($featured_slider_curr['meta_title_fontgw']) && !empty($featured_slider_curr['meta_title_fontgw']) ){
				$currfontw=$featured_slider_curr['meta_title_fontgw'];
				$gfonturl = $pgfont['urls'][$currfontw];
			
			}  else {
				$gfonturl = 'http://fonts.googleapis.com/css?family='.$featured_slider_curr['meta_title_fontg'];
			}
			if(isset($featured_slider_curr['meta_title_fontgsubset']) && !empty($featured_slider_curr['meta_title_fontgsubset']) ){
				$strsubset = implode(",",$featured_slider_curr['meta_title_fontgsubset']);
				$gfonturl = $gfonturl.'&subset='.$strsubset;
			} 
			if(!empty($meta_title_fontg)) 	{
				wp_enqueue_style( 'featured_meta_title'.$set, $gfonturl,array(),FEATURED_SLIDER_VER);
				$meta_title_fontg=$pgfont['name'];
				$mt_font = $meta_title_fontg.','.$mtfamily;
				$mt_fontw = $mtfontw;	
			}
			else { //if not set google font fall back to default font
				
				$mt_font = 'Arial, Helvetica, sans-serif';
				$mt_fontw = 'normal';
				$mt_fontst = 'normal';
			}
		} else if( $featured_slider_curr['mt_font'] == 'custom' ) {
			$mt_font = $featured_slider_curr['ptfont_custom'];
			$mt_fontw = $meta_title_font_weight;
			$mt_fontst = $meta_title_font_style;
		}
		
		$featured_slider_css['featured_meta'] = $style_start.'font-family:'. $mt_font . ' '.$featured_slider_curr['meta_title_font'].', Arial, Helvetica, sans-serif; font-weight:'.$mt_fontw.';font-style:'.$mt_fontst.'; font-size: '.$featured_slider_curr['meta_title_fsize'].'px; color: '.$featured_slider_curr['meta_title_fcolor'].';'.$style_end;

	//featured_next
	    $nexturl='var/buttons/'.$featured_slider_curr['buttons'].'/next.png';
		$featured_slider_css['featured_next']=$style_start.'background: transparent url('.featured_slider_plugin_url( $nexturl ) .') no-repeat 0 0;max-width:'.$featured_slider_curr['nav_w'].'px;margin-top:-'.($featured_slider_curr['nav_h']/2).'px;max-height:'.$featured_slider_curr['nav_h'].'px;right:'.$featured_slider_curr['nav_margin'].'px;background-size: 100%;'.$style_end;
	//featured_prev
	    $prevurl='var/buttons/'.$featured_slider_curr['buttons'].'/prev.png';
		$featured_slider_css['featured_prev']=$style_start.'background: transparent url('.featured_slider_plugin_url( $prevurl ) .') no-repeat 0 0;max-width:'.$featured_slider_curr['nav_w'].'px;margin-top:-'.($featured_slider_curr['nav_h']/2).'px;max-height:'.$featured_slider_curr['nav_h'].'px;left:'.$featured_slider_curr['nav_margin'].'px;background-size: 100%;'.$style_end;
		
	
	}
	return $featured_slider_css;
}
//Image Cropping
if(!defined('BFITHUMB_UPLOAD_DIR'))define( 'BFITHUMB_UPLOAD_DIR', 'sliderImages' );

function featured_slider_css() {
$gfeatured_slider = get_option('featured_slider_global_options');
$css=$gfeatured_slider['css'];
if($css and !empty($css)){?>
 <style type="text/css"><?php echo $css;?></style>
<?php }
}
add_action('wp_head', 'featured_slider_css');
add_action('admin_head', 'featured_slider_css');
?>
