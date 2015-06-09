<?php /* RSS feed Template tag and Shortcode */
function featured_carousel_posts_on_slider_rssfeed($max_posts='5', $offset=0, $out_echo = '1', $set='',$feedurl='',$data=array() ) {
   	$r_array=array();
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider = get_option('featured_slider_options');
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	
	global $wpdb, $table_prefix;
	
	$rand = $featured_slider_curr['rand'];
	if(isset($featured_slider_curr['rand']) ){
		$rand = $featured_slider_curr['rand'];
	}
	$src=isset($data['src'])?$data['src']:'';
	if($src=='smugmug'){		
		$feedurl = featuredrssfeed_decode_entities($feedurl);
		if ( !empty($max_posts)) $feedurl = add_query_arg( 'ImageCount', $max_posts, $feedurl );
		$rss = fetch_feed($feedurl);
		if ( is_wp_error($rss) || empty($rss) || count($rss->get_items()) == 0 ) {
			$slides = array();
		}
		else{
			$size=isset($data['size'])?$data['size']:'XL';
			$i=0;
			$rss_items = $rss->get_items();
	
			$offset = intval($offset);
			if ( $offset < 0 )$offset = 0;

			$items = array_slice($rss->get_items(), $offset);
			foreach($items as $item) {
				$slide=array();
				$enclosures = $item->get_enclosures();
				if ( count($enclosures) > 1 ) {
					$thumb_url = $enclosures[1]->get_thumbnail();
					$size = strtolower($size);
					$num_enclosures = count($enclosures);
					
					switch ( $size ) {
						case "Ti":
							$enclosure_index = 0;
							break;
						
						case "Th":
							$enclosure_index = 1;
							break;
						
						case "S":
							$enclosure_index = 2;
							break;
						
						case "M":
							$enclosure_index = 3;
							break;
						
						default:
						case "L":
							$enclosure_index = 4;
							break;
						
						case "X1":
						case "XL":
							$size = "XL";
							$enclosure_index = 5;
							break;
						
						case "X2":
							$enclosure_index = 6;
							break;
						
						case "X3":
							$enclosure_index = 7;
							break;
						
						case "O":
							$enclosure_index = 8;
							break;
					}
					// Use the largest enclosure available
					if ( $num_enclosures > $enclosure_index ) {
						$photo_url = $enclosures[$enclosure_index]->get_link();
					} else {
						$photo_url = $enclosures[$num_enclosures - 1]->get_link();
					}
					
					$slide['post_title'] = htmlspecialchars(strip_tags($item->get_title()), ENT_COMPAT, 'ISO-8859-1', false);	
					$slide['ID'] = 0;
					$slide['post_excerpt'] = (string) $item->get_description();
					$slide['post_content'] = (string) $item->get_description();
					$slide['content_for_image']=(isset($slide['post_content']) && $slide['post_content']!='')? $slide['post_content'] : $slide['post_excerpt'];
					$slide['thumb_image'] = !empty($thumb_url)?$thumb_url:$photo_url;
					$slide['media_image'] = $photo_url;
					$slide['url']=$item->get_link();
					$slide['redirect_url'] = '';
					$slide['nolink'] = '';
					$slide['pubDate'] = (string) $item->get_date();
					$slide['author'] = (string) $item->get_author();
					$slide['category'] = (string) $item->get_category();
					$slide['order']=$data['order'];
					$slide=(object) $slide;
					$slides[]=$slide;
					$i++;
					if($i>=$max_posts and $rand!='1') break;
				}		
			}
		}
	}
	else{
		$xml_source = file_get_contents($feedurl);
		$x = simplexml_load_string($xml_source);
		
		//$x = simplexml_load_file($feedurl);

		$slides = array();
		
		if(count($x) > 0){
			$i=0;
			if($data['feed']=='atom'){
				if($x->entry){
					foreach($x->entry as $item)	{	
						$slide=array();
						$slide['post_title'] = (string) $item->title;
						$slide['ID'] = 0;
						$slide['post_excerpt'] = (string) $item->summary;
						$slide['post_content'] = (string) $item->content;
						if(isset($item->children('content', TRUE)->encoded) and isset($data['content']) ) $slide['content_for_image'] = $item->children('content', TRUE)->encoded;
						else $slide['content_for_image']=(isset($slide['post_content']) && $slide['post_content']!='')? $slide['post_content'] : $slide['post_excerpt'];
						$slide['redirect_url'] = '';
						$slide['nolink'] = '';
						$slide['pubDate'] = (string) $item->published;
						$slide['author'] = (string) $item->author;
						$slide['category'] = (string) $item->category;
						foreach($item->link as $link){
							if( !isset( $slide['url'] ) ) $slide['url'] =  ($item->link->attributes()->rel == 'alternate') ? ($item->link->attributes()->href) : '';
							if( !isset( $slide['media_image'] ) ) $slide['media_image']= ($item->link->attributes()->rel == 'enclosure' and ($item->link->attributes()->type == "image/jpeg" or $item->link->attributes()->type == "image/png" ) ) ? ($item->link->attributes()->href) : '';
						}
						$slide=(object) $slide;
						$slides[]=$slide;
						$i++;
						if($i>=$max_posts and $rand!='1') break;
					}
				}
			}
			else{
				if($x->channel->item){
					foreach($x->channel->item as $item)
					{	
						$slide=array();
						$slide['post_title'] = (string) $item->title;
						$slide['ID'] = 0;
						$slide['post_excerpt'] = (string) $item->description;
						$slide['post_content'] = (string) $item->description;
						if(isset($item->children('content', TRUE)->encoded) and isset($data['content']) ) $slide['content_for_image'] = $item->children('content', TRUE)->encoded;
						else $slide['content_for_image']=$slide['post_content'];
						$slide['redirect_url'] = '';
						$slide['nolink'] = '';
						$slide['url'] = (string) $item->link;
						$slide['pubDate'] = (string) $item->pubDate;
						if( isset($item->author) ) $slide['author'] = (string) $item->author;
						else {
							$namespaces = $item->getNameSpaces(true);
							$dc = $item->children($namespaces['dc']); 
							$slide['author']=$dc->creator;
						}
						$slide['category'] = (string) $item->category;
						if( isset($item->enclosure) )$slide['media_image']= ($item->enclosure->attributes()->type == "image/jpeg" or $item->enclosure->attributes()->type == "image/png") ? ($item->enclosure->attributes()->url) : '' ;
						if(!isset($slide['media_image'])){
							if(isset($item->children('media', TRUE)->content)) {
								foreach($item->children('media', TRUE)->content as $media_content){
									if(!isset($slide['media_image'])) $slide['media_image']= ($media_content->attributes()->medium == "image" ) ? ($media_content->attributes()->url) : '' ;
								}
							}
						}
						$slide['order']=$data['order'];
						$slide=(object) $slide;
						$slides[]=$slide;
						$i++;
						if($i>=$max_posts and $rand!='1') break;
					}
				}
			}
			if($rand=='1') {shuffle($slides);$slides=array_slice($slides, 0, $max_posts);}	
			/* Added for Offset */
			$offset = intval($offset);
			if ( $offset < 0 )$offset = 0;
			$slides = array_slice($slides, $offset);
			/* End - for Offset */
		}
	}
	$r_array=featured_global_data_processor( $slides, $featured_slider_curr, $out_echo, $set, $data );
	return $r_array;
}

function get_featured_slider_feed($args='') {
    	$defaults=array('set'=>'', 'offset'=>0, 'feedurl'=>'', 'default_image'=>'', 'image_class'=>'', 'id'=>'1', 'feed'=>'', 'order'=>'0', 'content'=>'', 'media'=>'1', 'src'=>'', 'size'=>'');
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
	
	$slider_handle='featured_slider_f'.$id;
	$data['slider_handle']=$slider_handle;
	$data['image_class']=$image_class;
	$data['default_image']=$default_image;
	$data['feed']=$feed;
	$data['order']=$order;
	if(!empty($content)) $data['content']=$content;
	$data['media']=$media;
	$data['title']=$title;
	$data['preload']='true';
	$data['src']=$src;
	$data['size']=$size;
	$r_array = featured_carousel_posts_on_slider_rssfeed($featured_slider_curr['no_posts'], $offset, '0', $set, $feedurl, $data); 
	get_global_featured_slider($slider_handle,$r_array,$featured_slider_curr,$set,$echo='1',$data);
} 

function return_featured_slider_rssfeed($set='', $offset=0, $feedurl='', $default_image='', $image_class='', $id='1',$feed='', $order='0', $content='', $media='1', $title='', $src='', $size='') {
	$slider_html='';
	$default_featured_slider_settings=get_featured_slider_default_settings();
	// If setting set is 1 then set to blank	
	if($set == '1') $set = '';
	$featured_slider_options='featured_slider_options'.$set;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider = get_option('featured_slider_options');
	if(!isset($featured_slider_curr) or !is_array($featured_slider_curr) or empty($featured_slider_curr)){$featured_slider_curr=$featured_slider;$set='';}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	
	$slider_handle='featured_slider_f'.$id;
	$data['slider_handle']=$slider_handle;
	$data['image_class']=$image_class;
	$data['default_image']=$default_image;
	$data['feed']=$feed;
	$data['order']=$order;
	if(!empty($content)) $data['content']=$content;
	$data['media']=$media;
	$data['title']=$title;
	$data['preload']='true';
	$data['src']=$src;
	$data['size']=$size;
	
	$r_array = featured_carousel_posts_on_slider_rssfeed($featured_slider_curr['no_posts'], $offset, '0', $set, $feedurl, $data); 

	//get slider 
	$output_function='return_global_featured_slider';
	$slider_html=$output_function($slider_handle,$r_array,$featured_slider_curr,$set,$echo='0',$data);

	return $slider_html;
}

function featured_slider_rssfeed_shortcode($atts) {
	extract(shortcode_atts(array(
		'set' => '',
		'offset'=>'0',
		'id'=>'',
		'feedurl'=>'', 
		'default_image'=>'', 
		'image_class'=>'',
		'feed'=>'',
		'order'=>'0',
		'content'=>'',
		'media'=>'1',
		'title'=>'',
		'src'=>'',
		'size'=>'',
	), $atts));

	return return_featured_slider_rssfeed($set,$offset,$feedurl,$default_image,$image_class,$id,$feed,$order,$content,$media,$title,$src,$size);
}
add_shortcode('featuredfeed', 'featured_slider_rssfeed_shortcode');
//Convert the encoded html to normal HTML characters in feed url
function featuredrssfeed_decode_entities($text, $quote_style = ENT_COMPAT){
	if ( function_exists('html_entity_decode') ) {
		$text = html_entity_decode($text, $quote_style, 'ISO-8859-1');
	} else { 
		$trans_tbl = get_html_translation_table(HTML_ENTITIES, $quote_style);
		$trans_tbl = array_flip($trans_tbl);
		$text = strtr($text, $trans_tbl);
	}
	$text = preg_replace_callback('~&#x([0-9a-f]+);~i', 
       		create_function ('$matches', 'return chr(hexdec($matches[1]));'), 
	$text);
	/*
		$text = preg_replace_callback('/(0-9)([a-f])/', 
       		create_function ('$matches', 'return chr(hexdec($matches[1]));'), 
	$text);
	*/

	//preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $text); 
	$text = preg_replace_callback('~&#([0-9]+);~', 
       		create_function ('$matches', 'return chr($matches[1]);'), 
	$text);
	// preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $text);
	return $text;
}
?>
