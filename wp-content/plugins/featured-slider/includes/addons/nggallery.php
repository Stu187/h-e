<?php /* Fetch images from NextGen Gallery in Featured Slider - Template tag and Shortcode */
function featured_carousel_posts_on_slider_nggallery($max_posts='10', $gid='1', $anchor='0', $offset=0, $out_echo = '1', $set='', $data=array() ) {
    $r_array=array();
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider = get_option('featured_slider_options');
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	global $wpdb, $table_prefix;
	if(!class_exists('C_Gallery')){
		$r_array[0]=0;
		$r_array[1]="NextGen Gallery Plugin not installed";
	}
	else {
		$args = array('gid'=> $gid);
		$gallery=new C_Gallery($args);
		$mapper = $gallery->get_registry()->get_utility('I_Gallery_Mapper');
		$gallery_key=$mapper->get_primary_key_column();
		$mapper->select();
		$mapper->where(array("{$gallery_key} IN %s", '1'));
		$gallery_props=$mapper->run_query();
		
		$displayed_gallery=new C_Displayed_Gallery(array('source'=>'gallery','container_ids'=>$gid,'order_by'=>''));
		$source_obj = $displayed_gallery->object->get_source();
		$images =  $displayed_gallery->object->_get_image_entities($source_obj, $max_posts, $offset, FALSE, 'included');
		
		$data['title']=$gallery_props[0]->title;
		$author=get_user_by( 'id', $gallery_props[0]->author );
		
		$rand = $featured_slider_curr['rand'];
		if(isset($rand) and $rand=='1'){
		  shuffle($images);
		}
		
		$slides = array();
		
		foreach($images as $image){
			$iwrap=new C_Image_Wrapper($image);
			$r2=$iwrap->__get('imageURL');
			$slide=array();
			$slide['post_title'] = (string) $iwrap->__get('alttext');
			$slide['ID'] = $iwrap->__get('pid');
			$slide['post_excerpt'] = (string) $iwrap->__get('description');
			$slide['post_content'] = (string) $iwrap->__get('description');
			$slide['content_for_image'] ='<img src="'. $iwrap->__get('imageURL').'" />';
			
			$slide['redirect_url'] = '';
			
			if($anchor!='1') $slide['nolink'] = '1';
			else $slide['nolink'] = '';
			
			$slide['pubDate'] = $image->imagedate;
			$slide['author'] = $author->display_name;
			$slide['category'] = '';
			
			$slide['url'] =  $iwrap->__get('imageURL'); 
			
			$slide['media_image'] = '';
			//$slide['media_image']= $iwrap->__get('imageURL'); 
			
			$slide=(object) $slide;
			$slides[]=$slide;
		}
		
		$r_array=featured_global_data_processor( $slides, $featured_slider_curr, $out_echo, $set, $data );
	}
	return $r_array;
}

function get_featured_slider_ngg($args='') {
    	$defaults=array('gallery_id'=>'1', 'anchor'=>'0','set'=>'', 'offset'=>0, 'data'=>array() );
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
			
	$slider_handle='featured_slider_ngg_'.$gallery_id;
	$data['slider_handle']=$slider_handle;
	$data['preload']='true';
	
	$r_array = featured_carousel_posts_on_slider_nggallery($featured_slider_curr['no_posts'], $gallery_id,$anchor, $offset, '0', $set, $data); 
	get_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,'1',$data);
} 

function return_featured_slider_nggallery($gid='1', $anchor='0', $set='', $offset=0, $data=array()) {
	$slider_html='';
	$default_featured_slider_settings=get_featured_slider_default_settings();
	// If setting set is 1 then set to blank	
	if($set == '1') $set = '';
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider = get_option('featured_slider_options');
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	$slider_handle='featured_slider_ngg_'.$gid;
	$data['slider_handle']=$slider_handle;
	$r_array = featured_carousel_posts_on_slider_nggallery($featured_slider_curr['no_posts'], $gid,$anchor, $offset, '0', $set, $data); 
	//get slider 
	$slider_html=return_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,$data);

	return $slider_html;
}

function featured_slider_nggallery_shortcode($atts) {
	extract(shortcode_atts(array(
		'gallery_id'=>'1', 
		'anchor'=>'0',
		'set' => '',
		'offset'=>'0',
	), $atts));
	$data=array();
	return return_featured_slider_nggallery($gallery_id,$anchor,$set,$offset,$data);
}
add_shortcode('featuredngg', 'featured_slider_nggallery_shortcode');
?>
