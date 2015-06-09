<?php
function featured_carousel_videos_on_slider_vimeo($max='3', $offset=0, $out_echo = '1', $set='',$type='',$val, $data=array() ) {
    	$r_array=array();
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider = get_option('featured_slider_options');
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	$slides = array();
	if($type == "channel") {
		$channel_url = "http://vimeo.com/api/v2/channel/".$val."/videos.json";
		$channeljson_source = @file_get_contents($channel_url);
		$channelfx = json_decode($channeljson_source);
		$count = count($channelfx);
		if($count > $max) $count = $max;
		for($i=0;$i<$count;$i++) {
			$id = isset($channelfx[$i]->id)?$channelfx[$i]->id:'';
			$title = isset($channelfx[$i]->title)?$channelfx[$i]->title:'';
			$description = isset($channelfx[$i]->description)?$channelfx[$i]->description:'';
			$url = isset($channelfx[$i]->url)?$channelfx[$i]->url:'';
			$upload_date = isset($channelfx[$i]->upload_date)?$channelfx[$i]->upload_date:'';
			$user_name = isset($channelfx[$i]->user_name)?$channelfx[$i]->user_name:'';
			$imgurl = isset($channelfx[$i]->thumbnail_small)?$channelfx[$i]->thumbnail_small:'';
			$featured_eshortcode = '<iframe src="http://player.vimeo.com/video/'.$id.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
			$slide=array();
			$slide['post_title'] = $title;
			$slide['ID'] = 0;
			$slide['post_excerpt'] = '';
			$slide['post_content'] = $description;
			$slide['content_for_image'] = '';
			$slide['redirect_url'] = $url;
			$slide['nolink'] = '';
			$slide['pubDate'] = $upload_date;
			$slide['author'] = $user_name;
			$slide['category'] = '';
			$slide['media_image']= $imgurl;
			$slide['eshortcode'] = $featured_eshortcode;
			$slide=(object) $slide;
			$slides[]=$slide;
	
		}
	} elseif($type == "album") {
		//videos by Album
		$album_url = "http://vimeo.com/api/v2/album/".$val."/videos.json";
		$albumjson_source = @file_get_contents($album_url);
		$albumfx = json_decode($albumjson_source);
		$count = count($albumfx);
		if($count > $max) $count = $max;
		for($i=$offset;$i<$count;$i++) {
			$id = isset($albumfx[$i]->id)?$albumfx[$i]->id:'';
			$title = isset($albumfx[$i]->title)?$albumfx[$i]->title:'';
			$description = isset($albumfx[$i]->description)?$albumfx[$i]->description:'';
			$url = isset($albumfx[$i]->url)?$albumfx[$i]->url:'';
			$upload_date = isset($albumfx[$i]->upload_date)?$albumfx[$i]->upload_date:'';
			$user_name = isset($albumfx[$i]->user_name)?$albumfx[$i]->user_name:'';
			$imgurl = isset($albumfx[$i]->thumbnail_small)?$albumfx[$i]->thumbnail_small:'';
			$featured_eshortcode = '<iframe src="http://player.vimeo.com/video/'.$id.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
			$slide=array();
			$slide['post_title'] = $title;
			$slide['ID'] = 0;
			$slide['post_excerpt'] = '';
			$slide['post_content'] = $description;
			$slide['content_for_image'] = '';
			$slide['redirect_url'] = $url;
			$slide['nolink'] = '';
			$slide['pubDate'] = $upload_date;
			$slide['author'] = $user_name;
			$slide['category'] = '';
			$slide['media_image']= $imgurl;
			$slide['eshortcode'] = $featured_eshortcode;
			$slide=(object) $slide;
			$slides[]=$slide;
		}
	}
	$r_array=featured_global_data_processor( $slides, $featured_slider_curr, $out_echo, $set, $data );
	return $r_array;
}
function get_featured_vimeo_slider( $args = '' ) {
	$defaults=array('type'=>'','val'=>'', 'set'=>'', 'offset'=>'0');
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	$default_featured_slider_settings=get_featured_slider_default_settings();
	// If setting set is 1 then set to blank	
	if($set == '1') $set = '';
	$featured_slider_css = featured_get_inline_css($set);
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider = get_option('featured_slider_options');
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	$handleval = str_replace(array("-","@"),'_',$val);
	$slider_handle='featured_slider_'.$handleval;
	$data['slider_handle']=$slider_handle;
	$data['media']=1;
	$r_array = featured_carousel_videos_on_slider_vimeo($featured_slider_curr['no_posts'], $offset, '0', $set, $type,$val,$data); 
	get_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,$echo='1',$data);
}
function return_featured_slider_vimeo($type='',$val='',$set='',$offset='0') {
	$slider_html='';
	$default_featured_slider_settings=get_featured_slider_default_settings();
	// If setting set is 1 then set to blank	
	if($set == '1') $set = '';
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider = get_option('featured_slider_options');
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	$handleval = str_replace(array("-","@"),'_',$val);
	$slider_handle='featured_slider_'.$handleval;
	$data['slider_handle']=$slider_handle;
	$data['media']=1;
	$r_array = featured_carousel_videos_on_slider_vimeo($featured_slider_curr['no_posts'], $offset, '0', $set, $type,$val,$data); 
	//get slider 
	$output_function='return_global_featured_slider';
	$slider_html=$output_function($slider_handle,$r_array,$featured_slider_curr,$set,$echo='0',$data);
	return $slider_html;
}

function featured_slider_vimeo_shortcode($atts) {
	extract(shortcode_atts(array(
		'type'=>'',
		'val'=>'',
		'set'=>'',
		'offset'=>'0'
	), $atts));

	return return_featured_slider_vimeo($type,$val,$set,$offset);
}
add_shortcode('featuredvimeo', 'featured_slider_vimeo_shortcode');
?>
