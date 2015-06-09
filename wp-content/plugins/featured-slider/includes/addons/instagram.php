<?php
function featured_carousel_data_on_slider_instagram($max='3', $offset=0, $out_echo = '1', $set='',$type='',$username,$cid, $data=array() ) {
    	$r_array=array();
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider = get_option('featured_slider_options');
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	$slides = array();
	$insta_id_url = "https://api.instagram.com/v1/users/search?q=".$username."&client_id=$cid";
	$json_source_id = @file_get_contents($insta_id_url);
	$insta_id_data = json_decode($json_source_id);
	if(isset($insta_id_data->data[0])) {
		$insta_id = $insta_id_data->data[0]->id;
		$insta_media_url="https://api.instagram.com/v1/users/".$insta_id."/media/recent/?client_id=$cid";
		$json_source = @file_get_contents($insta_media_url);
		$insta_media_data = json_decode($json_source);
		if(isset($insta_media_data->data)) {
			$count = count($insta_media_data->data);
			if($count > $max) $count = $max;
			for($i=$offset;$i<$count;$i++) {
				$imgurl = isset($insta_media_data->data[$i]->images->standard_resolution->url)?$insta_media_data->data[$i]->images->standard_resolution->url:'';
				$url = isset($insta_media_data->data[$i]->link)?$insta_media_data->data[$i]->link:'';
				$title = isset($insta_media_data->data[$i]->caption->text)?$insta_media_data->data[$i]->caption->text:'';
				$time = isset($insta_media_data->data[$i]->caption->created_time)?$insta_media_data->data[$i]->caption->created_time:'';
				if($time!='') $pubDate = date("Y-m-d H:i:s",$time);else $pubDate = '';
				$author = isset($insta_media_data->data[$i]->caption->from->username)?$insta_media_data->data[$i]->caption->from->username:'';
				$thumb_src = isset($insta_media_data->data[$i]->images->thumbnail->url)?$insta_media_data->data[$i]->images->thumbnail->url:'';
				$slide=array();
					$slide['post_title'] = $title;
					$slide['ID'] = 0;
					$slide['post_excerpt'] = '';
					$slide['post_content'] = '';
					$slide['content_for_image'] = '';
					$slide['redirect_url'] = $url;
					$slide['nolink'] = '';
					$slide['pubDate'] = $pubDate;
					$slide['author'] = $author;
					$slide['category'] = '';
					$slide['media_image']= $imgurl;
					$slide=(object) $slide;
					$slides[]=$slide;
			}
			$r_array=featured_global_data_processor( $slides, $featured_slider_curr, $out_echo, $set, $data );
			return $r_array;
		} else {
			_e('Please enter the correct information','featured-slider');
		}
	} else {
		_e('Please enter the correct information','featured-slider');
	}
}
function get_featured_instagram_slider( $args = '' ) {
	$defaults=array('type'=>'','username'=>'', 'set'=>'', 'offset'=>'0', 'max'=>'10');
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	$gfeatured_slider = get_option('featured_slider_global_options');
	$cid = isset($gfeatured_slider['insta_client_id'])?$gfeatured_slider['insta_client_id']:'';
	if($cid != "") {
		$r_array=array();
		$default_featured_slider_settings=get_featured_slider_default_settings();
		$featured_slider_css = featured_get_inline_css($set);
		$featured_slider_options='featured_slider_options'.$set;
		$featured_slider_curr=get_option($featured_slider_options);
		$featured_slider = get_option('featured_slider_options');
		if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
		$featured_slider_curr= populate_featured_current($featured_slider_curr);
		$userhandle = str_replace(array("-","@"),'_',$username);
		$slider_handle='featured_slider_'.$userhandle;
		$data['slider_handle']=$slider_handle;
		$data['media']=1;
		$r_array=featured_carousel_data_on_slider_instagram($featured_slider_curr['no_posts'], $offset, '0', $set, $type,$username,$cid,$data); 
		get_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,$echo='1',$data);
	} else {
		_e("Please Eneter the Instagram Customer Key on Global Settings!","featured-slider");
	}
}
function return_featured_slider_instagram($type='',$username='',$set='',$offset='0') {
	$gfeatured_slider = get_option('featured_slider_global_options');
	$cid = isset($gfeatured_slider['insta_client_id'])?$gfeatured_slider['insta_client_id']:'';
	if($cid != "") {
		$slider_html='';
		$default_featured_slider_settings=get_featured_slider_default_settings();
		$featured_slider_options='featured_slider_options'.$set;
		$featured_slider_curr=get_option($featured_slider_options);
		$featured_slider = get_option('featured_slider_options');
		if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
		$featured_slider_curr= populate_featured_current($featured_slider_curr);
		$userhandle = str_replace(array("-","@"),'_',$username);
		$slider_handle='featured_slider_'.$userhandle;
		$data['slider_handle']=$slider_handle;
		$data['media']=1;
		$r_array=featured_carousel_data_on_slider_instagram($featured_slider_curr['no_posts'], $offset, '0', $set,$type,$username,$cid,$data); 
		//get slider 
		$output_function='return_global_featured_slider';
		$slider_html=$output_function($slider_handle,$r_array,$featured_slider_curr,$set,$echo='0',$data);
		return $slider_html;
	} else {
		_e("Please Eneter the Instagram Customer Key on Global Settings!","featured-slider");
	}
}

function featured_slider_instagram_shortcode($atts) {
	extract(shortcode_atts(array(
		'type'=>'',
		'username'=>'',
		'set'=>'',
		'offset'=>'0'
	), $atts));
	return return_featured_slider_instagram($type,$username,$set,$offset);
}
add_shortcode('featuredinstagram', 'featured_slider_instagram_shortcode');
?>
