<?php
function featured_carousel_data_on_slider_facebook($max='3', $offset=0, $out_echo = '1', $set='',$type='',$album='',$key, $data=array() ) {
    	$r_array=array();
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider = get_option('featured_slider_options');
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	$slides = array();
	if($album != '') {
		$fb_url = "https://graph.facebook.com/v2.2/?id=".$album."&fields=id,name,photos&access_token=$key";
		$json_source = @file_get_contents($fb_url);
		$fb = json_decode($json_source);
		$count = count($fb->photos->data);
		if($count > $max) $count = $max;
		for($i=$offset;$i<$count;$i++) {
			$imgurl = isset($fb->photos->data[$i]->images[1]->source)?$fb->photos->data[$i]->images[1]->source:'';
			$pubDate = isset($fb->photos->data[$i]->created_time)?$fb->photos->data[$i]->created_time:'';
			$catg = isset($fb->photos->data[$i]->from->category)?$fb->photos->data[$i]->from->category:'';
			$auth = isset($fb->photos->data[$i]->from->name)?$fb->photos->data[$i]->from->name:'';
			$title = isset($fb->photos->data[$i]->name)?$fb->photos->data[$i]->name:'';
			$url = isset($fb->photos->data[$i]->link)?$fb->photos->data[$i]->link:'';
			$nav_img_src = isset($fb->photos->data[$i]->picture)?$fb->photos->data[$i]->picture:'';
			$slide=array();
			$slide['post_title'] = $title;
			$slide['ID'] = 0;
			$slide['post_excerpt'] = '';
			$slide['post_content'] = '';
			$slide['content_for_image'] = '';
			$slide['redirect_url'] = $url;
			$slide['nolink'] = '';
			$slide['pubDate'] = $pubDate;
			$slide['author'] = $auth;
			$slide['category'] = $catg;
			$slide['media_image']= $imgurl;
			$slide=(object) $slide;
			$slides[]=$slide;
		}
		$r_array=featured_global_data_processor( $slides, $featured_slider_curr, $out_echo, $set, $data );
		return $r_array;
	}
}
function get_featured_facebook_slider( $args = '' ) {
	$defaults=array('page'=>'','album'=>'', 'set'=>'', 'type'=>'', 'offset'=>'0');
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	$gfeatured_slider = get_option('featured_slider_global_options');
	$key = isset($gfeatured_slider['fb_app_key'])?$gfeatured_slider['fb_app_key']:'';
	if($key != "") {
		$r_array=array();
		$default_featured_slider_settings=get_featured_slider_default_settings();
		// If setting set is 1 then set to blank	
		if($set == '1') $set = '';
		$featured_slider_css = featured_get_inline_css($set);
		$featured_slider_options='featured_slider_options'.$set;
		$featured_slider_curr=get_option($featured_slider_options);
		$featured_slider = get_option('featured_slider_options');
		if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
		$featured_slider_curr= populate_featured_current($featured_slider_curr);
		$slider_handle='featured_slider_'.$album;
		$data['slider_handle']=$slider_handle;
		$data['media']=1;
		$r_array=featured_carousel_data_on_slider_facebook($featured_slider_curr['no_posts'], $offset, '0', $set, $type,$album,$key,$data); 
		get_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,$echo='1',$data);
	}  else {
		_e("Please Eneter the Facebook API Key on Global Settings!","featured-slider");
	}
}
function return_featured_slider_facebook($type='',$album='',$set='',$offset='0') {
	$gfeatured_slider = get_option('featured_slider_global_options');
	$key = isset($gfeatured_slider['fb_app_key'])?$gfeatured_slider['fb_app_key']:'';
	if($key != "") {
		$slider_html='';
		$default_featured_slider_settings=get_featured_slider_default_settings();
		// If setting set is 1 then set to blank	
		if($set == '1') $set = ''; 
		$featured_slider_options='featured_slider_options'.$set;
		$featured_slider_curr=get_option($featured_slider_options);
		$featured_slider = get_option('featured_slider_options');
		if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
		$featured_slider_curr= populate_featured_current($featured_slider_curr);
		$slider_handle='featured_slider_'.$album;
		$data['slider_handle']=$slider_handle;
		$data['media']=1;
		$r_array=featured_carousel_data_on_slider_facebook($featured_slider_curr['no_posts'], $offset, '0', $set,$type,$album,$key,$data); 
		//get slider 
		$output_function='return_global_featured_slider';
		$slider_html=$output_function($slider_handle,$r_array,$featured_slider_curr,$set,$echo='0',$data);
		return $slider_html;
	}  else {
		_e("Please Eneter the Facebook API Key on Global Settings!","featured-slider");
	}
}

function featured_slider_facebook_shortcode($atts) {
	extract(shortcode_atts(array(
		'type'=>'page',
		'album'=>'',
		'set'=>'',
		'offset'=>'0'
	), $atts));
	return return_featured_slider_facebook($type,$album,$set,$offset);
}
add_shortcode('featuredfacebook', 'featured_slider_facebook_shortcode');
?>
