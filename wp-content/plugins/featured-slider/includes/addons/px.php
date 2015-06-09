<?php
function featured_carousel_data_on_slider_500px($max='3', $offset=0, $out_echo = '1', $set='',$feature='',$username,$key, $data=array() ) {
    	$r_array=array();
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider = get_option('featured_slider_options');
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	$slides = array();
	if($feature == "user" || $feature == "user_favorites") {
		$pxurl = "https://api.500px.com/v1/photos?feature=".$feature."&username=".$username."&consumer_key=$key&image_size=4";
	} else {
		$pxurl = "https://api.500px.com/v1/photos?feature=".$feature."&consumer_key=$key&image_size=4";
	}
	$pxjson = @file_get_contents($pxurl);
	if($pxjson == true) {
		$px = json_decode($pxjson);
		$count = count($px->photos);
		if($count > $max) $count = $max;
		for($i = $offset; $i < $count; $i++) {
			$limg = isset($px->photos[$i]->image_url)?$px->photos[$i]->image_url:'';
			$title = isset($px->photos[$i]->name)?$px->photos[$i]->name:'';
			$description = isset($px->photos[$i]->description)?$px->photos[$i]->description:'';
			$pubDate = isset($px->photos[$i]->created_at)?$px->photos[$i]->created_at:'';
			$author = isset($px->photos[$i]->user->firstname)?$px->photos[$i]->user->firstname:'';
			$link = isset($px->photos[$i]->url)?$px->photos[$i]->url:'';
			$url = "https://500px.com$link";
			$slide=array();
			$slide['post_title'] = $title;
			$slide['ID'] = 0;
			$slide['post_excerpt'] = '';
			$slide['post_content'] = $description;
			$slide['content_for_image'] = '';
			$slide['redirect_url'] = $url;
			$slide['nolink'] = '';
			$slide['pubDate'] = $pubDate;
			$slide['author'] = $author;
			$slide['category'] = '';
			$slide['media_image']= $limg;
			$slide=(object) $slide;
			$slides[]=$slide;
		}
		$r_array=featured_global_data_processor( $slides, $featured_slider_curr, $out_echo, $set, $data );
		return $r_array;
	} else {
		_e('Please enter the correct information','featured-slider');
	}
}
function get_featured_500px_slider( $args = '' ) {
	$defaults=array('feature'=>'','username'=>'', 'set'=>'', 'offset'=>'0', 'max'=>'10');
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	$gfeatured_slider = get_option('featured_slider_global_options');
	$key = isset($gfeatured_slider['px_ckey'])?$gfeatured_slider['px_ckey']:'';
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
		$featurehandle = str_replace(array("-","@"),'_',$feature);
		$slider_handle='featured_slider_'.$featurehandle;
		$data['slider_handle']=$slider_handle;
		$data['media']=1;
		$r_array=featured_carousel_data_on_slider_500px($featured_slider_curr['no_posts'], $offset, '0', $set, $feature,$username,$key,$data); 
		get_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,$echo='1',$data);
	} else {
		_e("Please Eneter the 500px Consumer Key on Global Settings!","featured-slider");
	}
}
function return_featured_slider_500px($feature='',$username='',$set='',$offset='0') {
	$gfeatured_slider = get_option('featured_slider_global_options');
	$key = isset($gfeatured_slider['px_ckey'])?$gfeatured_slider['px_ckey']:'';
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
		$featurehandle = str_replace(array("-","@"),'_',$feature);
		$slider_handle='featured_slider_'.$featurehandle;
		$data['slider_handle']=$slider_handle;
		$data['media']=1;
		$r_array=featured_carousel_data_on_slider_500px($featured_slider_curr['no_posts'], $offset, '0', $set,$feature,$username,$key,$data); 
		//get slider 
		$output_function='return_global_featured_slider';
		$slider_html=$output_function($slider_handle,$r_array,$featured_slider_curr,$set,$echo='0',$data);
		return $slider_html;
	} else {
		_e("Please Eneter the 500px Consumer Key on Global Settings!","featured-slider");
	}
}

function featured_slider_500px_shortcode($atts) {
	extract(shortcode_atts(array(
		'feature'=>'',
		'username'=>'',
		'set'=>'',
		'offset'=>'0',
	), $atts));
	return return_featured_slider_500px($feature,$username,$set,$offset);
}
add_shortcode('featured500px', 'featured_slider_500px_shortcode');
?>
