<?php 
function featured_post_processor_trio($posts, $featured_slider_curr,$out_echo,$set,$data=array()){
	$skin='trio'; 
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider_css = featured_get_inline_css($set);
	
	$html = $page_close = '';
	$featured_sldr_j = $i = $mod = 0;
	$type = isset($data['type'])?$data['type']:'';
	$types = array("woo", "ecom", "eman", "ecal");
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	
	$slider_handle=$right_close='';
	if ( is_array($data) and isset($data['slider_handle']) ) {
		$slider_handle=$data['slider_handle'];
	}
	$curr_visible = 4;
	//Image Cropping
	$cropping='0';
	if($featured_slider_curr['cropping']=='1'){
		$cropping='1';
		require_once(FEATURED_SLIDER_INC_DIR.'BFI_Thumb.php');
	}
	$data['cropping'] = $cropping; 
	foreach($posts as $post) { 
		$id = $post_id = $post->ID;
		$featured_sldr_j++;
		$mod=($featured_sldr_j%$curr_visible);	
		$page_close='';$item_css=$featured_slider_css['featured_slideri'];$item_class='featured_slideri';
		if($mod == 1){ 
			$html .= '<div class="featured_slide" '.$featured_slider_css['featured_slide'].'>
			<!-- featured_slide -->
			<div class="'.$item_class.'" '.$item_css.'><!-- '.$item_class.' -->';
		}
		if($mod == 0){
			$page_close='</div><!-- /featured_slide -->';
		}
		$html = apply_filters('featured_slideri_start_trio',$html,$post_id,$featured_slider_curr,$featured_slider_css,$set,$data);	
		$cleardiv='<div class="sldr_clearlt"></div><div class="sldr_clearrt"></div>';	
		$data['sub_cls']='';
		if( $featured_slider_curr['trio_block']=='4' )
			$data['sub_cls']='sub_full';
		if($featured_slider_curr['block_pos']=='0') {
			if($mod == 0) {
				$html.= featured_trio_construct_html($post,$featured_slider_curr,$featured_slider_css,$set,$data,0,0);
				$right_close ='';
			}
			else if($mod == 1) { 
				$html.= '<div class="featured_slide_right">'; 
				if( $featured_slider_curr['trio_block']=='3')
					$data['sub_cls']='sub_full';
				$html.= featured_trio_construct_html($post,$featured_slider_curr,$featured_slider_css,$set,$data,1,0);
				$right_close ='</div>';
			}
			else if($mod == 3) {
				if( $featured_slider_curr['trio_block']=='2')
					$data['sub_cls']='sub_full';
				$html.= featured_trio_construct_html($post,$featured_slider_curr,$featured_slider_css,$set,$data,1,0).$right_close;
			}
			else { 
				$html.= featured_trio_construct_html($post,$featured_slider_curr,$featured_slider_css,$set,$data,1,0); 
			}
		}
		if($featured_slider_curr['block_pos']=='1') {
			if($mod == 1) {
				$html.= featured_trio_construct_html($post,$featured_slider_curr,$featured_slider_css,$set,$data,0,0);
				$right_close ='';
			}
			else if($mod == 2) { 
				$html.= '<div class="featured_slide_right">'; 
				if( $featured_slider_curr['trio_block']=='3')
					$data['sub_cls']='sub_full';
				$html.= featured_trio_construct_html($post,$featured_slider_curr,$featured_slider_css,$set,$data,1,0);
				$right_close ='</div>';
			}
			else if($mod == 0) { 
				if( $featured_slider_curr['trio_block']=='2')
					$data['sub_cls']='sub_full';
				$html.= featured_trio_construct_html($post,$featured_slider_curr,$featured_slider_css,$set,$data,1,0).$right_close;
			}
			else { 
				$html.= featured_trio_construct_html($post,$featured_slider_curr,$featured_slider_css,$set,$data,1,0); 
			}
		}
		if($mod == 0) { 
			$html .= $cleardiv.'	<!-- /'.$item_class.' -->
			</div>'.$page_close; 
		} 
		
	}
	if($right_close != '' && $mod != 3 && $featured_slider_curr['block_pos']=='0') $html .= $right_close;
	if($right_close != '' && $mod != 0 && $featured_slider_curr['block_pos']=='1') $html .= $right_close;
	if($mod < $curr_visible and $mod > 0) {
		$html .= '</div>'.$cleardiv.'	<!-- /'.$item_class.' -->  
		</div>'.$page_close; 
	}
	if( ($page_close=='' or empty($page_close)) and $posts){ if($mod == 0) $html=$html.'</div><!-- /featured_slide -->';}

	//filter hook
	$html=apply_filters('featured_extract_html_trio',$html,$featured_sldr_j,$posts,$featured_slider_curr,$skin);
	if($out_echo == '1') {
	   echo $html;
	}
	$r_array = array( $featured_sldr_j, $html);
	$r_array=apply_filters('featured_r_array',$r_array,$posts, $featured_slider_curr,$set,$skin);
	return $r_array;	
}
function featured_trio_construct_html($post,$featured_slider_curr,$featured_slider_css,$set,$data,$slide_type,$processor) {
	if (isset ($post->ID)) $id = $post_id = $post->ID;
	$cnt_comeents = $imagevideo = $thumbnail_image = $dolink = $fld_html ='';
	if($processor == 0) {
		$post_title = get_post_meta($id, 'SlideTitle', true);
		if(empty($post_title)) {
			if(function_exists('qtrans_useCurrentLanguageIfNotFoundShowAvailable')) {
				$post_title = qtrans_useCurrentLanguageIfNotFoundShowAvailable( $post->post_title );
				$post_title = stripslashes( $post_title );
			}
			else {
				$post_title = stripslashes($post->post_title);
			}
			$post_title = str_replace('"', '', $post_title);
		}
		if(function_exists('qtrans_useCurrentLanguageIfNotFoundShowAvailable')) {
			$slider_content=qtrans_useCurrentLanguageIfNotFoundShowAvailable( $post->post_content );
		}
		else {
			$slider_content = $post->post_content;			
		}
		// comments Count
		$comments = get_comments_number( $post_id );
		if($comments != 0) {
			$cnt_comeents= '<span class="featured_comments">'.$comments.'</span>';
		} 
		$featured_slide_redirect_url = get_post_meta($post_id, 'featured_slide_redirect_url', true);
		$featured_sslider_nolink = get_post_meta($post_id,'featured_sslider_nolink',true);
		trim($featured_slide_redirect_url);
		if(!empty($featured_slide_redirect_url) and isset($featured_slide_redirect_url)) {
			$permalink = $featured_slide_redirect_url;
		} else {
			$permalink = get_permalink($post_id);
		}
		$dolink = get_post_meta($post_id,'featured_dolink',true);
	}
	if($processor == 1) {
		if(is_array($data)) extract($data,EXTR_PREFIX_ALL,'data');
		$post_title = stripslashes($post->post_title);
		$post_title = str_replace('"', '', $post_title);
		$slider_content = $post->post_content;
		$featured_slide_redirect_url = $post->redirect_url;
		$featured_sslider_nolink = $post->nolink;
		trim($featured_slide_redirect_url);
		if(!empty($featured_slide_redirect_url) and isset($featured_slide_redirect_url)) {
			$permalink = $featured_slide_redirect_url;
		}
		else{
			$permalink = $post->url;
		}
	}
	if($featured_sslider_nolink=='1'){
	  	$permalink='';
	}
	//filter hook
	if (isset($post_id)) $post_title=apply_filters('featured_post_title',$post_title,$post_id,$featured_slider_curr,$featured_slider_css);
	
	/* do not link from settings panel - 3.0 */
	$img_permalink = $permalink;
	if( empty( $dolink ) && $featured_slider_curr['donotlink'] == '1' ) {
		$permalink='';
		if($featured_slider_curr['pphoto'] != "1"){
			$img_permalink ="";
		}
	}
		
	//meta1
	$meta1_parms=$featured_slider_curr['meta1_parms'];
	if(function_exists($featured_slider_curr['meta1_fn'])){
    	$fn_name=$featured_slider_curr['meta1_fn'];
	    $meta1_value=$fn_name($post,$meta1_parms);
	}
	//meta2
	$meta2_parms=$featured_slider_curr['meta2_parms'];
	if(function_exists($featured_slider_curr['meta2_fn'])){
    	$fn_name=$featured_slider_curr['meta2_fn'];
	    $meta2_value=$fn_name($post,$meta2_parms);
	}
	
	//WPML intigration
	$meta1_before = $featured_slider_curr['meta1_before'];
	if( function_exists('icl_plugin_action_links') && function_exists('icl_t') ) {
		$featured_option='[featured_slider_options'.$set.']meta1_before';
		$meta1_before = icl_t('featured-slider-settings', $featured_option, $featured_slider_curr['meta1_before']);
	}
	$meta1_after = $featured_slider_curr['meta1_after'];
	if( function_exists('icl_plugin_action_links') && function_exists('icl_t') ) {
		$featured_option='[featured_slider_options'.$set.']meta1_after';
		$meta1_after = icl_t('featured-slider-settings', $featured_option, $featured_slider_curr['meta1_after']);
	}
	$meta2_before = $featured_slider_curr['meta2_before'];
	if( function_exists('icl_plugin_action_links') && function_exists('icl_t') ) {
		$featured_option='[featured_slider_options'.$set.']meta2_before';
		$meta2_before = icl_t('featured-slider-settings', $featured_option, $featured_slider_curr['meta2_before']);
	}
	$meta2_after = $featured_slider_curr['meta2_after'];
	if( function_exists('icl_plugin_action_links') && function_exists('icl_t') ) {
		$featured_option='[featured_slider_options'.$set.']meta2_after';
		$meta2_after = icl_t('featured-slider-settings', $featured_option, $featured_slider_curr['meta2_after']);
	}
	/* Meta fileds- start */		
	$meta_span='';
	if( $featured_slider_curr['show_meta']=='1' and (!empty($meta1_value) or !empty($meta2_value)) ){
		$meta_span='<span class="featured-meta2">'.$meta2_before.'<span class="featured-meta2-value">'.$meta2_value.'</span>'.$meta2_after.'</span>/<span class="featured-meta1">'.$meta1_before.'<span class="featured-meta1-value">'.$meta1_value.'</span>'.$meta1_after.'</span>';
	}
	$featured_meta='';
	if(!empty($meta_span)) $featured_meta='<span class="featured-meta" '.$featured_slider_css['featured_meta'].'>'.$meta_span.'</span>';
	//filter hook
	$featured_meta=apply_filters('featured_meta_html',$featured_meta,$post_id,$featured_slider_curr,$featured_slider_css);
	/* Meta fields - end */
		
	//Slide link anchor attributes
	$a_attr='';$imglink='';$pphoto_gallery_handle='';$a_attr_img='';
	if(!empty($slider_handle))$pphoto_gallery_handle='['.$slider_handle.']';
	if($processor == 0) 
		$a_attr=get_post_meta($post_id,'featured_link_attr',true);
	if( empty($a_attr) and isset( $featured_slider_curr['a_attr'] ) ) $a_attr=$featured_slider_curr['a_attr'];
	$a_attr_img=$a_attr;
	if( isset($featured_slider_curr['pphoto']) and $featured_slider_curr['pphoto'] == '1' ) {
		if($featured_slider_curr['pphoto'] == '1' and $featured_slider_curr['lbox_type'] == 'pphoto_box') {
			$a_attr_img.='rel="prettyPhoto"';
			$a_attr_lbox='pphoto-box';
		}
		elseif($featured_slider_curr['pphoto'] == '1' and $featured_slider_curr['lbox_type'] == 'swipe_box') {
			$a_attr_lbox='swipe-box';
		}
		elseif($featured_slider_curr['pphoto'] == '1' and $featured_slider_curr['lbox_type'] == 'smooth_box') {
			$a_attr_lbox='smooth-box';
		}
		elseif($featured_slider_curr['pphoto'] == '1' and $featured_slider_curr['lbox_type'] == 'nivo_box') {
			$a_attr_img.='data-lightbox-gallery="featured_gallery"';
			$a_attr_lbox ='';
		}	
		else {
			$a_attr_lbox ='';
		}

		if(!empty($featured_slide_redirect_url) and isset($featured_slide_redirect_url))
			$imglink=$featured_slide_redirect_url;
		else $imglink='1';
	} 
	else {
		$a_attr_lbox ='';
	}
	$fields_html='';$fld='0';
	if($processor == 0) {
		//custom fields
		$featured_fields=$featured_slider_curr['fields'];		
		if($featured_fields and !empty($featured_fields) ){
			$fields=explode( ',', $featured_fields );
			if($fields){
				foreach($fields as $field) {
					$field_val = get_post_meta($post_id, $field, true);
					if( $field_val and !empty($field_val) )
						$fields_html .='<span class="featured_'.$field.' featured_fields">'.$field_val.'</span>';
				}
			}
		}
	}
	// Common Sizes for data processor and post processer
	$lswidth=$featured_slider_curr['lswidth'];
	$sswidth= (100 - $lswidth);
	if($slide_type == 0) {
		$width=($lswidth * $featured_slider_curr['width'])/100;
		$height=$featured_slider_curr['height'];
	}
	else {
		$width=(($sswidth * $featured_slider_curr['width'])/100)/2;
		$height=$featured_slider_curr['height'] / 2;
	}

	if($featured_slider_curr['crop'] == '0'){
		$extract_size = 'full';
	}
	elseif($featured_slider_curr['crop'] == '1'){
		$extract_size = 'large';
	}
	elseif($featured_slider_curr['crop'] == '2'){
		$extract_size = 'medium';
	}
	else{
		$extract_size = 'thumbnail';
	}
	/* Image Transition */
	$tran_class = $itranstyle = $featured_img_transition ='';
	if($processor == 0) {
		$featured_img_transition=get_post_meta($post_id, '_featured_img_transition', true);
	}
	$img_style = $featured_slider_css['featured_slider_thumbnail'];
	if($featured_img_transition != '') {
		/* Per Slide : Image Transition */
		$tran_class = "featured-animated featured-".$featured_img_transition;
		if($featured_slider_curr['stylesheet'] != 'sample') {
			$img_delay=get_post_meta($post_id, '_featured_img_delay', true);
			$img_duration=get_post_meta($post_id, '_featured_img_duration', true);
			if($img_duration != "") $itranstyle .= '-webkit-animation-duration: '.$img_duration.'s;-moz-animation-duration: '.$img_duration.'s;animation-duration: '.$img_duration.'s;';
			if($img_delay != "") $itranstyle .= '-webkit-animation-delay: '.$img_delay.'s;-moz-animation-delay: '.$img_delay.'s;animation-delay: '.$img_delay.'s;';
			if($img_duration != "" || $img_delay != "")
				$img_style = substr_replace($featured_slider_css['featured_slider_thumbnail'], $itranstyle.'"', -1);
		}
	} elseif( isset($featured_slider_curr['img_transition']) && $featured_slider_curr['img_transition'] != '' ) {
		/* Slider : Image Transition */
		$featured_img_transition = $featured_slider_curr['img_transition'];
		$tran_class = "featured-animated featured-".$featured_slider_curr['img_transition'];
		if($featured_slider_curr['stylesheet'] != 'sample') {
			$img_duration = $featured_slider_curr['img_duration'];
			$img_delay = $featured_slider_curr['img_delay'];
			if($img_duration != "") $itranstyle .= '-webkit-animation-duration: '.$img_duration.'s;-moz-animation-duration: '.$img_duration.'s;animation-duration: '.$img_duration.'s;';
			if($img_delay != "") $itranstyle .= '-webkit-animation-delay: '.$img_delay.'s;-moz-animation-delay: '.$img_delay.'s;animation-delay: '.$img_delay.'s;';
			if($img_duration != "" || $img_delay != "")
				$img_style = substr_replace($featured_slider_css['featured_slider_thumbnail'], $itranstyle.'"', -1);
		}
	}
	
	if($processor == 1) {
		//For media images
		$classes[] = $extract_size;
		if($slide_type == 0) $img_class = 'left_img';
		else $img_class = 'right_img';
		$classes[] = "featured_slider_thumbnail $img_class $tran_class";
		$classes[] = isset($data_image_class)?$data_image_class:'';
		$class = join( ' ', array_unique( $classes ) );
		if (isset ($post->media)) $featured_media = $post->media;
		if (isset ($post->media_image)) $featured_media_image = $post->media_image;
			(isset ($post->eshortcode)) ? $featured_eshortcode = $post->eshortcode : $featured_eshortcode = '';
		$data_image_class=(!empty($data_image_class)?$data_image_class:'');
		$data_default_image=(!empty($data_default_image)?$data_default_image:'');
		$tran_data = '';
		if($featured_img_transition != '' ) $tran_data = 'data-anim="'.$featured_img_transition.'"';
		//Image title text
		$image_title_text=(isset($featured_slider_curr['image_title_text']))?($featured_slider_curr['image_title_text']):('0');
		$order_of_image = 0;
		$width = ( ( $width ) ? ' width="' . esc_attr( $width ) . '"' : '' );
		$height = ( ( $height ) ? ' height="' . esc_attr( $height ) . '"' : '' );
		if( ((empty($featured_media) or $featured_media=='' or !($featured_media)) and (empty($featured_media_image) or $featured_media_image=='' or !($featured_media_image)) ) or $data_media!='1' ) {
			preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $post->content_for_image, $matches );
			if(isset($data_default_image))
				$img_url=$data_default_image;
			/* If there is a match for the image, return its URL. */
			$order_of_image='';
			if(isset($data_order)) $order_of_image=$data_order;
		
			if($order_of_image > 0) $order_of_image=$order_of_image; 
			else $order_of_image = 0;

			if ( isset( $matches ) && count($matches[1])<=$order_of_image) $order_of_image=count($matches[1]);
		
			if ( isset( $matches ) && $matches[1][$order_of_image] )
				$img_url = $matches[1][$order_of_image];
			$img_html = '<img src="' . $img_url . '" '.$tran_data.' " class="' . esc_attr( $class ) . '" ' . $width . $height .' '.$img_style.' />';
			//Prettyphoto Integration	
			$ipermalink=$img_permalink;
			if($imglink=='1' and $img_permalink!='') $ipermalink=$img_url;
			elseif($imglink=='') $ipermalink=$img_permalink;
			else {
				if($img_permalink!='')$ipermalink=$imglink;
			}
			if($featured_slider_curr['pphoto'] == "1"){
				$ipermalink=$img_url;
			}
			if($a_attr_lbox=='swipe-box') {
				 $photobox='swipebox featured_thumb_anchor';
			}
			elseif($a_attr_lbox=='smooth-box') {
				 $photobox='sb featured_thumb_anchor';
			}
			else {
				 $photobox='featured_thumb_anchor';			
			}

			$image_title=($image_title_text=='1')?('title="'.$post_title.'"'):'';
		
			if($img_permalink!='') {
			  $img_html = '<a class="'.$photobox.'" href="' . $ipermalink . '" '.$image_title.' '.$a_attr_img.'>' . $img_html . '</a>';
			}
			
			$featured_large_image=$img_html;
		}
		else{
			if(!empty($featured_media)){
				$featured_large_image=$featured_media;
			}
			else{
				if(!empty($featured_media_image)) {
					/* Added for embeding any shortcode on slide - start */
					if(!empty($featured_eshortcode)){
						$shortcode_html=do_shortcode($featured_eshortcode);
						$featured_large_image='<div class="featured_slider_eshortcode" '.$img_style.'>'.$shortcode_html.'</div>';
					} else {
						$featured_large_image='<img src="'.$featured_media_image.'" '.$tran_data.' '.$img_style.' class="' . esc_attr( $class ) . '"' . $width . $height . '/>';
					}
					/* Added for embeding any shortcode on slide - end */	
			
					$img_url=$featured_media_image;
				}
				else {
					/* Added for embeding any shortcode on slide - start */
					if(!empty($featured_eshortcode)){
						$shortcode_html=do_shortcode($featured_eshortcode);
						$featured_large_image='<div class="featured_slider_eshortcode" '.$img_style.'>'.$shortcode_html.'</div>';
					} else {
						$featured_large_image='<img src="'.$data_default_image.'" '.$tran_data.' '.$img_style.' class="' . esc_attr( $class ) . '"' . $width . $height . '/>';
					}					
					$img_url=$data_default_image;
				}
				//Prettyphoto Integration	
				$ipermalink=$img_permalink;
				if($imglink=='1' and $img_permalink!='') $ipermalink=$img_url;
				elseif($imglink=='') $ipermalink=$img_permalink;
				else {
					if($img_permalink!='')$ipermalink=$imglink;
				}
				if($featured_slider_curr['pphoto'] == "1"){
					$ipermalink=$img_url;
				}
				$image_title=($image_title_text=='1')?('title="'.$post_title.'"'):'';
			
				if($img_permalink!='') {
					$featured_large_image = '<a href="' . $ipermalink . '" '.$image_title.' '.$a_attr_img.'>' . $featured_large_image . '</a>';
				}
			}
		}	
	} else if($processor == 0) {	
		//All images
		$featured_media = get_post_meta($post_id,'featured_media',true);	
		if(!isset($featured_slider_curr['img_pick'][0])) $featured_slider_curr['img_pick'][0]='';
		if(!isset($featured_slider_curr['img_pick'][2])) $featured_slider_curr['img_pick'][2]='';
		if(!isset($featured_slider_curr['img_pick'][3])) $featured_slider_curr['img_pick'][3]='';
		if(!isset($featured_slider_curr['img_pick'][5])) $featured_slider_curr['img_pick'][5]='';

		if($featured_slider_curr['img_pick'][0] == '1'){
			$custom_key = array($featured_slider_curr['img_pick'][1]);
		}
		else {
			$custom_key = '';
		}
	
		if($featured_slider_curr['img_pick'][2] == '1'){
			$the_post_thumbnail = true;
		}
		else {
			$the_post_thumbnail = false;
		}
	
		if($featured_slider_curr['img_pick'][3] == '1'){
			$attachment = true;
			$order_of_image = $featured_slider_curr['img_pick'][4];
		}
		else{
			$attachment = false;
			$order_of_image = '1';
		}
	
		if($featured_slider_curr['img_pick'][5] == '1'){
			 $image_scan = true;
		}
		else {
			 $image_scan = false;
		}
		$default_image=(isset($featured_slider_curr['default_image']))?($featured_slider_curr['default_image']):('false');
		$image_title_text=(isset($featured_slider_curr['image_title_text']))?($featured_slider_curr['image_title_text']):('');
		$cropping = isset($data['cropping'])?$data['cropping']:'0'; 
		if($slide_type == 0) $img_class = 'featured_slider_thumbnail left_img '.$tran_class;
		else $img_class = 'featured_slider_thumbnail right_img '.$tran_class;
		
		$img_args = array(
			'custom_key' => $custom_key,
			'post_id' => $post_id,
			'attachment' => $attachment,
			'size' => $extract_size,
			'the_post_thumbnail' => $the_post_thumbnail,
			'default_image' => $default_image,
			'order_of_image' => $order_of_image,
			'link_to_post' => false,
			'image_class' => $img_class,
			'data_tran' => $featured_img_transition,
			'image_scan' => $image_scan,
			'width' => $width,
			'height' => $height,
			'echo' => false,
			'permalink' => $img_permalink,
			'style'=> $img_style,
			'a_attr'=> $a_attr_img,
			'a_attr_lbox'=>$a_attr_lbox,
			'imglink'=>$imglink,
			'cropping'=>$cropping,
			'image_title_text'=>$image_title_text
		);
		
		if( empty($featured_media) or $featured_media=='' or !($featured_media) ) {  
			$featured_large_image=featured_sslider_get_the_image($img_args);
		}
		else{
			$featured_large_image=$featured_media;
		}
		/* Added for embeding any shortcode on slide - start */
		$featured_eshortcode=get_post_meta($post_id, '_featured_embed_shortcode', true);
		if(!empty($featured_eshortcode)){
			$shortcode_html=do_shortcode($featured_eshortcode);
			$imagevideo ='<div class="featured_slider_thumbnail featured_video"><div class="featured_slider_eshortcode" >'.$shortcode_html.'</div></div>';
		}
		$thumbnail_image=get_post_meta($post_id, '_featured_disable_image', true);
	}
	
	//filter hook
	$featured_large_image=apply_filters('featured_large_image',$featured_large_image,$post_id,$featured_slider_curr,$featured_slider_css);
	/* Per Slide : Title Transition */
	$title_tran_class = $ttranstyle = $title_tran_data=$featured_title_transition='';
	$title_style = $featured_slider_css['featured_slider_h2'];
	if($processor == 0) 
		$featured_title_transition=get_post_meta($post_id, '_featured_title_transition', true);
	if($featured_title_transition != '') {
		$title_tran_class = "featured-animated featured-".$featured_title_transition;
		$title_tran_data =  'data-anim="'.$featured_title_transition.'"';
		if($featured_slider_curr['stylesheet'] != 'sample') {
			$title_duration=get_post_meta($post_id, '_featured_title_duration', true);
			$title_delay=get_post_meta($post_id, '_featured_title_delay', true);
			if($title_duration != "") $ttranstyle .= '-webkit-animation-duration: '.$title_duration.'s;-moz-animation-duration: '.$title_duration.'s;animation-duration: '.$title_duration.'s;';
			if($title_delay != "") $ttranstyle .= '-webkit-animation-delay: '.$title_delay.'s;-moz-animation-delay: '.$title_delay.'s;animation-delay: '.$title_delay.'s;';
			if($title_duration != "" || $title_delay != "")
				$title_style = substr_replace($featured_slider_css['featured_slider_h2'], $ttranstyle.'"', -1);
		}
	} elseif( isset($featured_slider_curr['ptitle_transition']) && $featured_slider_curr['ptitle_transition'] != '' ) {
		/* Slider : Title Transition */
		$featured_title_transition = $featured_slider_curr['ptitle_transition'];
		$title_tran_class = "featured-animated featured-".$featured_slider_curr['ptitle_transition'];
		$title_tran_data = 'data-anim="'.$featured_slider_curr['ptitle_transition'].'"';
		if($featured_slider_curr['stylesheet'] != 'sample') {
			$title_duration = $featured_slider_curr['ptitle_duration'];
			$title_delay = $featured_slider_curr['ptitle_delay'];
			if($title_duration != "") $ttranstyle .= '-webkit-animation-duration: '.$title_duration.'s;-moz-animation-duration: '.$title_duration.'s;animation-duration: '.$title_duration.'s;';
			if($title_delay != "") $ttranstyle .= '-webkit-animation-delay: '.$title_delay.'s;-moz-animation-delay: '.$title_delay.'s;animation-delay: '.$title_delay.'s;';
			if($title_duration != "" || $title_delay != "")
				$title_style = substr_replace($featured_slider_css['featured_slider_h2'], $ttranstyle.'"', -1);
		}
	}
	/* END - Title Transition */
	if($featured_slider_curr['mtitle_element']=='1') {
		$starth = '<h1 class="slider_htitle '.$title_tran_class.'" '.$title_tran_data.' '.$title_style.'>';	
		$endh = '</h1>'; 	
	}
	elseif($featured_slider_curr['mtitle_element']=='2') {
		$starth = '<h2 class="slider_htitle '.$title_tran_class.'" '.$title_tran_data.' '.$title_style.'>';	
		$endh = '</h2>';
	}
	elseif($featured_slider_curr['mtitle_element']=='3') {
		$starth = '<h3 class="slider_htitle '.$title_tran_class.'" '.$title_tran_data.' '.$title_style.'>';
		$endh = '</h3>';
	}
	elseif($featured_slider_curr['mtitle_element']=='4') {
		$starth = '<h4 class="slider_htitle '.$title_tran_class.'" '.$title_tran_data.' '.$title_style.'>';
		$endh = '</h4>';
	}
	elseif($featured_slider_curr['mtitle_element']=='5') {
		$starth = '<h5 class="slider_htitle '.$title_tran_class.'" '.$title_tran_data.' '.$title_style.'>';
		$endh = '</h5>';
	}
	elseif($featured_slider_curr['mtitle_element']=='6') {
		$starth = '<h6 class="slider_htitle '.$title_tran_class.'" '.$title_tran_data.' '.$title_style.'>';
		$endh = '</h6>';
	}
	else {
		$starth = '<h2 class="slider_htitle '.$title_tran_class.'" '.$title_tran_data.' '.$title_style.'>';
		$endh = '</h2>';
	}
	/* Per Slide : Sub Title Transition */
	$sub_title_tran_class = $ttranstyle = $sub_title_tran_data =$featured_sub_title_transition='';
	$sub_title_style = $featured_slider_css['featured_slider_sub_h2'];
	if($processor == 0) 
		$featured_sub_title_transition=get_post_meta($post_id, '_featured_title_transition', true);
	if($featured_sub_title_transition != '') {
		$sub_title_tran_class = "featured-animated featured-".$featured_sub_title_transition;
		$sub_title_tran_data =  'data-anim="'.$featured_sub_title_transition.'"';
		if($featured_slider_curr['stylesheet'] != 'sample') {
			$title_duration=get_post_meta($post_id, '_featured_title_duration', true);
			$sub_title_delay=get_post_meta($post_id, '_featured_title_delay', true);
			if($title_duration != "") $ttranstyle .= '-webkit-animation-duration: '.$title_duration.'s;-moz-animation-duration: '.$title_duration.'s;animation-duration: '.$title_duration.'s;';
			if($sub_title_delay != "") $ttranstyle .= '-webkit-animation-delay: '.$sub_title_delay.'s;-moz-animation-delay: '.$sub_title_delay.'s;animation-delay: '.$sub_title_delay.'s;';
			if($title_duration != "" || $sub_title_delay != "")
				$sub_title_style = substr_replace($featured_slider_css['featured_slider_sub_h2'], $ttranstyle.'"', -1);
		}
	} elseif( isset($featured_slider_curr['sub_ptitle_transition']) && $featured_slider_curr['sub_ptitle_transition'] != '' ) {
		/* Slider : Title Transition */
		$featured_sub_title_transition = $featured_slider_curr['sub_ptitle_transition']; 
		$sub_title_tran_class = "featured-animated featured-".$featured_slider_curr['sub_ptitle_transition'];
		$sub_title_tran_data =  'data-anim="'.$featured_slider_curr['sub_ptitle_transition'].'"';
		if($featured_slider_curr['stylesheet'] != 'sample') {
			$title_duration = $featured_slider_curr['sub_ptitle_duration'];
			$sub_title_delay = $featured_slider_curr['sub_ptitle_delay'];
			if($title_duration != "") $ttranstyle .= '-webkit-animation-duration: '.$title_duration.'s;-moz-animation-duration: '.$title_duration.'s;animation-duration: '.$title_duration.'s;';
			if($sub_title_delay != "") $ttranstyle .= '-webkit-animation-delay: '.$sub_title_delay.'s;-moz-animation-delay: '.$sub_title_delay.'s;animation-delay: '.$sub_title_delay.'s;';
			if($title_duration != "" || $sub_title_delay != "")
				$sub_title_style = substr_replace($featured_slider_css['featured_slider_sub_h2'], $ttranstyle.'"', -1);
		}
	}
	/* END - Sub Title Transition */	
	if($featured_slider_curr['stitle_element']=='1') {
		$sub_starth = '<h1 class="slider_htitle '.$sub_title_tran_class.'" '.$sub_title_tran_data.' '.$sub_title_style.'>';	
		$sub_endh = '</h1>'; 	
	}
	elseif($featured_slider_curr['stitle_element']=='2') {
		$sub_starth = '<h2 class="slider_htitle '.$sub_title_tran_class.'" '.$sub_title_tran_data.' '.$sub_title_style.'>';			
		$sub_endh = '</h2>';
	}
	elseif($featured_slider_curr['stitle_element']=='3') {
		$sub_starth = '<h3 class="slider_htitle '.$sub_title_tran_class.'" '.$sub_title_tran_data.' '.$sub_title_style.'>';
		$sub_endh = '</h3>';
	}
	elseif($featured_slider_curr['stitle_element']=='4') {
		$sub_starth = '<h4 class="slider_htitle '.$sub_title_tran_class.'" '.$sub_title_tran_data.' '.$sub_title_style.'>';
		$sub_endh = '</h4>';
	}
	elseif($featured_slider_curr['stitle_element']=='5') {
		$sub_starth = '<h5 class="slider_htitle '.$sub_title_tran_class.'" '.$sub_title_tran_data.' '.$sub_title_style.'>';
		$sub_endh = '</h5>';
	}
	elseif($featured_slider_curr['stitle_element']=='6') {
		$sub_starth = '<h6 class="slider_htitle '.$sub_title_tran_class.'" '.$sub_title_tran_data.' '.$sub_title_style.'>';
		$sub_endh = '</h6>';
	}
	else {
		$sub_starth = '<h2 class="slider_htitle '.$sub_title_tran_class.'" '.$sub_title_tran_data.' '.$sub_title_style.'>';
		$sub_endh = '</h2>';
	}	
	if($permalink!='') { 
		$slide_title = $starth.'<a href="'.$permalink.'" '.$featured_slider_css['featured_slider_h2_a'].' '.$a_attr.'>'.$post_title.'</a>'.$endh;
		$slide_title_right =  $sub_starth.'<a href="'.$permalink.'" '.$featured_slider_css['featured_slider_sub_h2_a'].' '.$a_attr.'>'.$post_title.'</a>'.$sub_endh;
	}
	else {
		$slide_title = $starth.$post_title.$endh;
		$slide_title_right = $sub_starth.$post_title.$sub_endh;
	}
	//filter hook
    	$slide_title=apply_filters('featured_slide_title_html',$slide_title,$post_id,$featured_slider_curr,$featured_slider_css,$post_title,$data);
    	$start_twrap='<div class="featured_slide_content">';
    	$end_twrap='</div>';
    	if($featured_slider_curr['show_title'] == 0) $slide_title ='';
    	if($featured_slider_curr['show_sub_title'] == 0) $slide_title_right ='';
    	if($featured_slider_curr['show_content']=='1') {
		if($processor == 0) {
			$fld='1';
			if ($featured_slider_curr['content_from'] == "slider_content") {
				$slider_content = get_post_meta($post_id, 'slider_content', true);
			}
			if($featured_slider_curr['content_from'] == "excerpt") {
				if(function_exists('qtrans_useCurrentLanguageIfNotFoundShowAvailable')) {
					$slider_content = qtrans_useCurrentLanguageIfNotFoundShowAvailable( $post->post_excerpt );
				}
				else {
					$slider_content = $post->post_excerpt;
				}
			}
		}
		if($processor == 1) {
			if ($featured_slider_curr['content_from'] == "slider_content") {
				$slider_content = $post->post_content;
			}
			if ($featured_slider_curr['content_from'] == "excerpt") {
				$slider_content = $post->post_excerpt;
			}
		}
		$slider_content = strip_shortcodes( $slider_content );

		$slider_content = stripslashes($slider_content);
		$slider_content = str_replace(']]>', ']]&gt;', $slider_content);

		$slider_content = str_replace("\n","<br />",$slider_content);
		$slider_content = strip_tags($slider_content, $featured_slider_curr['allowable_tags']);
		
		$content_limit=$featured_slider_curr['content_limit'];
		$content_chars=$featured_slider_curr['content_chars'];
	
		if( $featured_slider_curr['climit'] == 1 && !empty($content_chars)){ 
			$slider_excerpt = substr($slider_content,0,$content_chars);
		  	$slider_excerpt_right = substr($slider_content,0,$content_chars );
		}
		elseif( $featured_slider_curr['climit'] == 0 && !empty($content_limit)){ 
			$slider_excerpt = featured_slider_word_limiter( $slider_content, $limit = $content_limit, $dots = '...' );
		  	$slider_excerpt_right = featured_slider_word_limiter( $slider_content, $limit = $content_limit );
		}
		
		/* Content Transition */
		$content_tran_class = $ctranstyle = $content_tran_data=$featured_content_transition='';
		$excerpt_style = $featured_slider_css['featured_slider_span'];
		if($processor == 0) 
			$featured_content_transition=get_post_meta($post_id, '_featured_content_transition', true);
		if($featured_content_transition != '') {
			/* Per Slide : Content Transition */
			$content_tran_class = "featured-animated featured-".$featured_content_transition;
			$content_tran_data = 'data-anim="'.$featured_content_transition.'"';
			if($featured_slider_curr['stylesheet'] != 'sample') {
				$content_duration=get_post_meta($post_id, '_featured_content_duration', true);
				$content_delay=get_post_meta($post_id, '_featured_content_delay', true);
				if($content_duration != "") $ctranstyle .= '-webkit-animation-duration: '.$content_duration.'s;-moz-animation-duration: '.$content_duration.'s;animation-duration: '.$content_duration.'s;';
				if($content_delay != "") $ctranstyle .= '-webkit-animation-delay: '.$content_delay.'s;-moz-animation-delay: '.$content_delay.'s;animation-delay: '.$content_delay.'s;';
				if($content_duration != "" || $content_delay != "")
					$excerpt_style = substr_replace($featured_slider_css['featured_slider_span'], $ctranstyle.'"', -1);
			}
		} elseif( isset($featured_slider_curr['content_transition']) && $featured_slider_curr['content_transition'] != '' ) {
			/* Slider : Content Transition */
			$featured_content_transition = $featured_slider_curr['content_transition'];
			$content_tran_class = "featured-animated featured-".$featured_slider_curr['content_transition'];
			$content_tran_data = 'data-anim="'.$featured_slider_curr['content_transition'].'"';
			if($featured_slider_curr['stylesheet'] != 'sample') {
				$content_duration = $featured_slider_curr['content_duration'];
				$content_delay = $featured_slider_curr['content_delay'];
				if($content_duration != "") $ctranstyle .= '-webkit-animation-duration: '.$content_duration.'s;-moz-animation-duration: '.$content_duration.'s;animation-duration: '.$content_duration.'s;';
				if($content_delay != "") $ctranstyle .= '-webkit-animation-delay: '.$content_delay.'s;-moz-animation-delay: '.$content_delay.'s;animation-delay: '.$content_delay.'s;';
				if($content_duration != "" || $content_delay != "")
					$excerpt_style = substr_replace($featured_slider_css['featured_slider_span'], $ctranstyle.'"', -1);
			}
		}
		/* END - Content Transition */	
		
		//filter hook
		$slider_excerpt=apply_filters('featured_slide_excerpt',$slider_excerpt,$post_id,$featured_slider_curr,$featured_slider_css);
		//WPML integration
		$morefield=$featured_slider_curr['more'];
		if( function_exists('icl_plugin_action_links') && function_exists('icl_t') ) {
			$featured_option = '[featured_slider_options'.$set.']more';
			$morefield = icl_t('featured-slider-settings', $featured_option, $featured_slider_curr['more']);
		}
		if($processor == 0) {
			$slider_excerpt='<div class="featured_excerpt">'.$featured_meta.'<span class="featured_slide_content_span '.$content_tran_class.'" '.$content_tran_data.' '.$excerpt_style.'> '.$slider_excerpt.'<span class="more"><a href="'.$permalink.'" '.$featured_slider_css['featured_slider_p_more'].' '.$a_attr.'>'.$morefield.'</a></span>'.$fields_html.'</span></div>'; 
			$slider_excerpt_right= '<div class="featured_slide_sub_excerpt" >'.$featured_meta.'</div>'; 
		}
		if($processor == 1) {
			$slider_excerpt='<div class="featured_excerpt">'.$featured_meta.'<span class="featured_slide_content_span '.$content_tran_class.'" '.$content_tran_data.' '.$excerpt_style.'> '.$slider_excerpt.'<span class="more"><a href="'.$permalink.'" '.$featured_slider_css['featured_slider_p_more'].' '.$a_attr.'>'.$morefield.'</a></span></span></div>';
			$slider_excerpt_right= '<div class="featured_slide_sub_excerpt" >'.$featured_meta.'</div>'; 
		}
	}
	else {
		$slider_excerpt='<div class="featured_excerpt">'.$featured_meta.'</div>';// End of left slide item
		$slider_excerpt_right= '<div class="featured_slide_sub_excerpt" >'.$featured_meta.'</div>';
	}
	//filter hook
	$slider_excerpt=apply_filters('featured_slide_excerpt_html_trio',$slider_excerpt,$post_id,$featured_slider_curr,$featured_slider_css,$data);
	if($fld == '0')$fld_html=$fields_html;
	$html = '';
	// Construct Main Slide
	if($slide_type == 0) {
		$html .= '<div class="featured_slide_left">';
		if ($featured_slider_curr['image_only'] == '0') $html .= $cnt_comeents;
		if($thumbnail_image!='1') 
			$html .= $featured_large_image.$imagevideo;
		else $html .= $imagevideo;
		if ($featured_slider_curr['image_only'] == '0') 
			$html .= $start_twrap.$slide_title.$fld_html.$slider_excerpt.$end_twrap;
		$html .= '</div>';
	}
	// Construct sub Slide
	if($slide_type == 1) {
		if($imagevideo != ""){
			if($thumbnail_image!='1')
				$imgvideo = $featured_large_image.$imagevideo;	
			else $imgvideo = $imagevideo;
		}
		else $imgvideo = $featured_large_image;
		$sub_cls = isset($data['sub_cls'])?$data['sub_cls']:'';
		$html .= '<div class="featured_slide_sub_right '.$sub_cls.'" '.$featured_slider_css['slide_sub_right'].' >';
		if ($featured_slider_curr['image_only'] == '0') $html.= $cnt_comeents;
		$html.= $imgvideo;
		if ($featured_slider_curr['image_only'] == '0')
			$html.= '<div class="featured_slide_sub_content">'.$slide_title_right.$slider_excerpt_right.'</div>';
		$html.= '</div>';
	}
	return $html;
}
/*** ---------------------------Data Processor Function For Add-on----------------------------- ***/
function featured_data_processor_trio($slides, $featured_slider_curr,$out_echo,$set,$data=array()){
	$skin='trio'; 
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider_css = featured_get_inline_css($set);
	$html = '';
	$featured_sldr_j = $i = $mod = 0;
	$right_close ='';	
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	
	$slider_handle='';
	if ( isset($data_slider_handle) ) {
		$slider_handle=$data_slider_handle;
	}
	$curr_visible = 4;
	foreach($slides as $slide) {
		$id = $post_id = '';
		if (isset ($slide->ID)) {$id = $post_id = $slide->ID;}
		$featured_sldr_j++;	
		$mod=($featured_sldr_j%$curr_visible);			
		$page_close='';$item_css=$featured_slider_css['featured_slideri'];$item_class='featured_slideri';
		if($mod == 1) {
		$html .= '<div class="featured_slide" '.$featured_slider_css['featured_slide'].'>
			<!-- featured_slide -->
			<div class="'.$item_class.'" '.$item_css.'><!-- '.$item_class.' -->';
		}

		if($mod == 0){
			$page_close='</div><!-- /featured_slide -->';
		}
		$data['sub_cls']='';
		if( $featured_slider_curr['trio_block']=='4' )
			$data['sub_cls']='sub_full';
		$cleardiv='<div class="sldr_clearlt"></div><div class="sldr_clearrt"></div>';
	 	if($featured_slider_curr['block_pos']=='0') {
			if($mod == 0) {
				$html.= featured_trio_construct_html($slide,$featured_slider_curr,$featured_slider_css,$set,$data,0,1);
				$right_close ='';
			}
			else if($mod == 1) { 
				$html.= '<div class="featured_slide_right">';
				if( $featured_slider_curr['trio_block']=='3')
					$data['sub_cls']='sub_full'; 
				$html.= featured_trio_construct_html($slide,$featured_slider_curr,$featured_slider_css,$set,$data,1,1);
				$right_close ='</div>';
			}
			else if($mod == 3) {
				if( $featured_slider_curr['trio_block']=='2')
					$data['sub_cls']='sub_full';
				$html.= featured_trio_construct_html($slide,$featured_slider_curr,$featured_slider_css,$set,$data,1,1).$right_close;
			}
			else { 
				$html.= featured_trio_construct_html($slide,$featured_slider_curr,$featured_slider_css,$set,$data,1,1); 
			}
		}
		if($featured_slider_curr['block_pos']=='1') {
			if($mod == 1) {
				$html.= featured_trio_construct_html($slide,$featured_slider_curr,$featured_slider_css,$set,$data,0,1);
				$right_close ='';
			}
			else if($mod == 2) { 
				$html.= '<div class="featured_slide_right">'; 
				if( $featured_slider_curr['trio_block']=='3')
					$data['sub_cls']='sub_full';
				$html.= featured_trio_construct_html($slide,$featured_slider_curr,$featured_slider_css,$set,$data,1,1);
				$right_close ='</div>';
			}
			else if($mod == 0) { 
				if( $featured_slider_curr['trio_block']=='2')
					$data['sub_cls']='sub_full';
				$html.= featured_trio_construct_html($slide,$featured_slider_curr,$featured_slider_css,$set,$data,1,1).$right_close;
			}
			else { 
				$html.= featured_trio_construct_html($slide,$featured_slider_curr,$featured_slider_css,$set,$data,1,1); 
			}
		}
		if($mod == 0) { 
			$html .= $cleardiv.'	<!-- /'.$item_class.' -->
			</div>'.$page_close; 
		} 
	}
	if($right_close != '' && $mod != 3 && $featured_slider_curr['block_pos']=='0') $html .= $right_close;
	if($right_close != '' && $mod != 0 && $featured_slider_curr['block_pos']=='1') $html .= $right_close;
	if($mod < $curr_visible and $mod > 0) {
		$html .= '</div>'.$cleardiv.'	<!-- /'.$item_class.' -->
		</div>'.$page_close; 
	}
	if( ($page_close=='' or empty($page_close)) and $slides){ if($mod == 0) $html=$html.'</div><!-- /featured_slide -->';}
	
	//filter hook
	$html=apply_filters('featured_extract_html_trio',$html,$featured_sldr_j,$slides,$featured_slider_curr,$skin);
	if($out_echo == '1') {
	   echo $html;
	}
	$r_array = array( $featured_sldr_j, $html);
	$r_array=apply_filters('featured_r_array',$r_array,$slides, $featured_slider_curr,$set,$skin);
	return $r_array;
}
/***-------------------------------------------------------------------------------------------------------***/
function featured_slider_get_trio($slider_handle,$r_array,$featured_slider_curr,$set,$echo='1',$data=array()){
	$skin='trio';
	$gfeatured_slider = get_option('featured_slider_global_options');
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_sldr_j = $r_array[0];
	$featured_slider_css = featured_get_inline_css($set);
	$html='';
	if(isset($r_array) && $r_array[0] >= 1) : //is slider empty?	
		$featured_slider_curr= populate_featured_current($featured_slider_curr);
		$featured_slider_curr= populate_featured_current($featured_slider_curr);
		
		$slider_id='';
		if (isset ($data['slider_id'])) {
			if( is_array($data)) $slider_id=$data['slider_id'];
		}
		if ( is_array($data) && isset($data['title'])){
			if($data['title']!='' )$sldr_title=$data['title'];
			else {
				if($featured_slider_curr['title_from']=='1' && !empty($slider_id) ) $sldr_title = get_featured_slider_name($slider_id);
				else $sldr_title = $featured_slider_curr['title_text'];
			}
		}
		else{
			if( $featured_slider_curr['title_from']=='1' && !empty($slider_id) ) $sldr_title = get_featured_slider_name($slider_id);
			else $sldr_title = $featured_slider_curr['title_text']; 
		}
		
		
		//filter hook
		$sldr_title=apply_filters('featured_slider_title',$sldr_title,$slider_handle,$featured_slider_curr,$set);
		
		//Scripts
			wp_enqueue_script( 'featured-slider-script', featured_slider_plugin_url( 'js/featured.js' ),array('jquery'), FEATURED_SLIDER_VER, false);
			wp_enqueue_script( 'easing', featured_slider_plugin_url( 'js/jquery.easing.js' ),array('jquery'), FEATURED_SLIDER_VER, false);  
		 
		// WooCommerce Integration
		wp_enqueue_script( 'featured_rateit_js', featured_slider_plugin_url( 'js/jquery.rateit.js' ),array('jquery'), FEATURED_SLIDER_VER, false);
		wp_enqueue_style( 'featured_rateit_css', featured_slider_plugin_url( 'var/css/rateit.css' ),false, FEATURED_SLIDER_VER, 'all');
		
		$slider_pause_handle=str_replace("-", "_", $slider_handle).'_pause';
		if(!isset($featured_slider_curr['fouc']) or $featured_slider_curr['fouc']=='' or $featured_slider_curr['fouc']=='0' ){
			$fouc_ready='jQuery("html").addClass("featured_slider_fouc");jQuery(document).ready(function() {
			   	jQuery(".featured_slider_fouc .featured_slider_set'.$set.'").show();
			});';
			$fouc_style='<style type="text/css">.featured_slider_fouc .featured_slider_set'.$set.'{display:none;}</style>';
		}	
		else{
			$fouc_style=$fouc_ready='';
		}		
		if ($featured_slider_curr['autostep'] == '1'){ $autostep = $featured_slider_curr['time'] * 1000;} else {$autostep = "0";}
		$prevnext='';
		if ($featured_slider_curr['prev_next'] == 1){ 
		  $prevnext='next:   "#'.$slider_handle.'_next", 
			 prev:   "#'.$slider_handle.'_prev",';
		}
				
		if(!empty($sldr_title)) { 
		  $sldr_title = '<div class="sldr_title" '.$featured_slider_css['sldr_title'].'>'. $sldr_title .'</div>';
		}
		
		$nav_top='';$nav_bottom='';
				
		$nav_buttons='';
		if ($featured_slider_curr['prev_next'] == 1){ 
			$nav_buttons='<div id="'.$slider_handle.'_next" class="featured_next" '.$featured_slider_css['featured_next'].'></div>
			<div id="'.$slider_handle.'_prev" class="featured_prev" '.$featured_slider_css['featured_prev'].'></div>';
		} 
		
		// large slide width
		$lswidth=$featured_slider_curr['lswidth'];
		$sswidth= (100 - $lswidth);
		$slider_script='';
		if($featured_slider_curr['fixblocks']!='1') {
		$slider_script='
			jQuery(document).ready(function() {
				jQuery("#'.$slider_handle.'").featured({
					speed: '.($featured_slider_curr['speed'] * 100 ).',
					slides :"div.featured_slide",
					'. $prevnext.'
					pauseOnHover: true,
					fx: "'.$featured_slider_curr['transition'].'",
					swipe: true,
					easing: "'. $featured_slider_curr['easing'].'",
					timeout: '. $autostep.'
				});

			  jQuery("#'.$slider_handle.'").featuredSlider({
					width		:'.$featured_slider_curr['width'].',
					origLw		:'. (($lswidth * $featured_slider_curr['width'])/100) .',
					origLh		:'.$featured_slider_curr['height'].',
					origSw		:'. (($sswidth * $featured_slider_curr['width'])/100) .',
					origSh		:'. ($featured_slider_curr['height'] / 2) .',
					listblock	:'.$featured_slider_curr['trio_block'].',
					subtitlehtm	:'.((isset($featured_slider_curr['stitle_element']))?$featured_slider_curr['stitle_element']:'h2').'
			    });	
		    });';
		    // 3.0 Ecommerce add to cart css
		}
		else {
		$slider_script='
			jQuery(document).ready(function() {
				  jQuery("#'.$slider_handle.'").featuredBlock({
						width		:'.$featured_slider_curr['width'].',
						origLw		:'. (($lswidth * $featured_slider_curr['width'])/100) .',
						origLh		:'.$featured_slider_curr['height'].',
						origSw		:'. (($sswidth * $featured_slider_curr['width'])/100) .',
						origSh		:'. ($featured_slider_curr['height'] / 2) .',
						subtitlehtm	:'.((isset($featured_slider_curr['stitle_element']))?$featured_slider_curr['stitle_element']:'h2').'
				    });	
		    });';
		}
		if(isset($featured_slider_css['featured_ecom_add_to_cart']) && !empty($featured_slider_css['featured_ecom_add_to_cart'])) {
			$slider_script.='jQuery("#'.$slider_handle.' .wpsc_buy_button").css('.$featured_slider_css['featured_ecom_add_to_cart'].');';
		}
		$slider_script=apply_filters('featured_global_script',$slider_script,$slider_handle,$featured_slider_curr,$skin);
		$html=$html.$fouc_style.'<script type="text/javascript"> '.$fouc_ready;
		
		if($featured_slider_curr['pphoto'] == '1') {
		$lightbox_script='';
		if($featured_slider_curr['lbox_type'] == 'pphoto_box') {
			wp_enqueue_script( 'jquery.prettyPhoto', featured_slider_plugin_url( 'js/jquery.prettyPhoto.js' ), array('jquery'), FEATURED_SLIDER_VER, false);
			wp_enqueue_style( 'prettyPhoto_css', featured_slider_plugin_url( 'var/css/prettyPhoto.css' ), false, FEATURED_SLIDER_VER, 'all');
			$lightbox_script='jQuery(document).ready(function(){
				jQuery("a[rel^=\'prettyPhoto\']").prettyPhoto({deeplinking: false,social_tools:false});
			});';	
		}
		if($featured_slider_curr['lbox_type'] == 'swipe_box') {
			wp_enqueue_script( 'jquery.swipebox', featured_slider_plugin_url( 'js/jquery.swipebox.js' ), array('jquery'), FEATURED_SLIDER_VER, false);
			wp_enqueue_style( 'swipebox_css', featured_slider_plugin_url( 'var/css/swipebox.css' ), false, FEATURED_SLIDER_VER, 'all');
			$lightbox_script='jQuery(document).ready(function(){
				jQuery("a[class^=\'swipebox\']").swipebox();
			});';
		}
		if($featured_slider_curr['lbox_type'] == 'nivo_box') {
			wp_enqueue_script( 'jquery.nivobox', featured_slider_plugin_url( 'js/nivo-lightbox.js' ), array('jquery'), FEATURED_SLIDER_VER, false);
			wp_enqueue_style( 'nivobox_css', featured_slider_plugin_url( 'var/css/nivobox.css' ), false, FEATURED_SLIDER_VER, 'all');
			$lightbox_script='jQuery(document).ready(function(){
				jQuery("a[class^=\'featured_thumb_anchor\']").nivoLightbox();
			});';
		}
// Photo box
		if($featured_slider_curr['lbox_type'] == 'photo_box') {
			wp_enqueue_script( 'jquery.photobox', featured_slider_plugin_url( 'js/jquery.photobox.js' ), array('jquery'), FEATURED_SLIDER_VER, false);
			wp_enqueue_style( 'photobox_css', featured_slider_plugin_url( 'var/css/photobox.css' ), false, FEATURED_SLIDER_VER, 'all');
			$lightbox_script='jQuery(document).ready(function(){
				jQuery(".featured_slide").photobox(\'a.featured_thumb_anchor\');
			});';
		}
// smooth box
		if($featured_slider_curr['lbox_type'] == 'smooth_box') {
			wp_enqueue_script( 'jquery.smoothbox', featured_slider_plugin_url( 'js/smoothbox.js' ), array('jquery'), FEATURED_SLIDER_VER, false);
			wp_enqueue_style( 'smoothbox_css', featured_slider_plugin_url( 'var/css/smoothbox.css' ), false, FEATURED_SLIDER_VER, 'all');
		}		
			//filter hook
		   	$lightbox_script=apply_filters('featured_lightbox_inline',$lightbox_script);
			$html.=$lightbox_script;
			}	
			else $imglink='1';
		
		//action hook
		$html.='</script><noscript><p><strong>'. $gfeatured_slider['noscript'] .'</strong></p></noscript>
		
	<div class="featured_slider featured_slider_set'. $set .'" '.$featured_slider_css['featured_slider'].'>
		'.$sldr_title.'
		'.$nav_top.'
		<div id="'.$slider_handle.'" class="featured_slider_instance" '.$featured_slider_css['featured_slider_instance'].'>
			'. $r_array[1] .'
		</div>
		'.$nav_buttons.'
		'.$nav_bottom.'
	</div>';
		$html.='<script type="text/javascript">'.$slider_script.'</script>';
		$html=apply_filters('featured_slider_html',$html,$r_array,$featured_slider_curr,$set,$skin);		
		if($echo == '1')  {echo $html; }
		else { return $html; }
	endif; //is slider empty?
}

/**
 * ---------------------------------------------------------------------------------------------
 * Filter to add Add-ons sale tag field at slideri start
 *
 * @return HTML including wooCommerce field
 * ---------------------------------------------------------------------------------------------
 **/
function featured_slideri_start_filter_trio($html,$id,$featured_slider_curr,$featured_slider_css,$set,$data) {
	$type = isset($data['type'])?$data['type']:'';
	/**
	 * ---------------------------------------------------------------------------------------------
	 * Filter to add wooCommerce sale tag field at slideri start
	 *
	 * @return HTML including wooCommerce field
	 * ---------------------------------------------------------------------------------------------
	 **/
	if( ( $type == 'woo' && class_exists('WooCommerce') ) || ( $type == 'ecom'  && class_exists('WP_eCommerce') ) ) {
		//WPML intigration
		$woo_sale_text = $featured_slider_curr['woo_sale_text'];
		if( function_exists('icl_plugin_action_links') && function_exists('icl_t') ) {
			$featured_option='[featured_slider_options'.$set.']woo_sale_text';
			$woo_sale_text = icl_t('featured-slider-settings', $featured_option, $featured_slider_curr['woo_sale_text']);
		}
		$sale_tag='';
		if($type == 'woo') {
			$onsell_id = wc_get_product_ids_on_sale();
			if (in_array( $id, $onsell_id )) {
				if( $featured_slider_curr['enable_woosalestrip'] == '1' ) {
					$sale_tag = '<div class="woo_sale" '.$featured_slider_css['featured_woo_sale_strip'].'><span>'.$woo_sale_text.'</span></div>';
				}
			}
		}
		if($type == 'ecom') {
			$sale_price = get_product_meta( $id, 'special_price' , true);
			if($sale_price != 0 ) {
				if( $featured_slider_curr['enable_woosalestrip'] == '1' ) {
					$sale_tag = '<div class="woo_sale" '.$featured_slider_css['featured_woo_sale_strip'].'><span>'.$woo_sale_text.'</span></div>';	
				}
			}
		}
		$html .= $sale_tag;
	}
	return $html;
}
add_filter('featured_slideri_start_trio', 'featured_slideri_start_filter_trio',10,6);

function featured_slide_excerpt_html_filter_trio($slider_excerpt,$id,$featured_slider_curr,$featured_slider_css,$data) {
	$type = isset($data['type'])?$data['type']:'';
	/**
	 * ---------------------------------------------------------------------------------------------
	 * Filter to add Events Manager html fields(Category, Location) in slide excerpt html
	 *
	 * @return HTML including Events Manager fields
	 * ---------------------------------------------------------------------------------------------
	 **/
	if($type == 'woo') {
		$product = get_product( $id );
		$onsell_id = wc_get_product_ids_on_sale();
		$sym = get_woocommerce_currency_symbol();
	
		$product_cat='';
		$categories = get_the_terms($id, 'product_cat');
		if( $categories && $featured_slider_curr['enable_woocat'] == '1' ) {
			$c=0;
			$product_cat='<span class="featured_woocat_wrap"><span class="featured_woocat">';
			foreach($categories as $category) {
				$pro_cat=$category->name;
				$cat_link=get_term_link($category,'product_cat');
				$comma=', ';				
				if($c=='0')$comma='';
				$product_cat.=$comma.'<a href='.$cat_link.' '.$featured_slider_css['featured_slide_cat'].'>'.$pro_cat.'</a>';
				$c++;
			}		
			$product_cat.='</span></span>';
		}			
		$sale_price=$product->get_sale_price();
		$product_price=$sym.$product->regular_price;
		$saleprice='';
		$reg_amount = '';
		if (in_array( $id, $onsell_id )) {
			$reg_amount = '';
			if( $featured_slider_curr['enable_wooregprice'] == '1' ) {
				$reg_amount='<strike>'.$product_price.'</strike>';
			}
			if($featured_slider_curr['enable_woosprice'] == '1') {
				$saleprice=$sym.$sale_price;
			}
		}
		else {
			if( $featured_slider_curr['enable_wooregprice'] == '1' ) {
				$reg_amount=$product_price;
			}
		}
		$addtocart_link=$product->add_to_cart_url( );
		//WPML intigration
		$woo_adc_text = $featured_slider_curr['woo_adc_text'];
		if( function_exists('icl_plugin_action_links') && function_exists('icl_t') ) {
			$featured_option='[featured_slider_options'.$set.']woo_adc_text';
			$woo_adc_text = icl_t('featured-slider-settings', $featured_option, $featured_slider_curr['woo_adc_text']);
		}	
		$button_adc='';
		if( $featured_slider_curr['enable_wooaddtocart'] == '1' ) {
			$button_adc='<a href="'.$addtocart_link.'" class="add_to_cart_button product_type_simple" data-product_id="'.$id.'" ><button '.$featured_slider_css['featured_woo_add_to_cart'].'>'.$woo_adc_text.'</button></a>';	
		}
		$review_tempcount=$product->get_rating_count();
		if($review_tempcount>1) {
			$review_count='('.$review_tempcount." Reviews)";
		}
		else {
			$review_count='';
		}		
		$rating_count = $product->get_average_rating();
		$average_rating = '';
		if( $featured_slider_curr['enable_woostar'] == '1' ) {
			$average_rating ='<div class="slidewoorate" ><div class="rateit '.$featured_slider_curr['nav_woo_star'].'" data-rateit-value="'.$rating_count.'" data-rateit-max="'.$rating_count.'" data-rateit-ispreset="true" data-rateit-starwidth="16" data-rateit-starheight="16" data-rateit-readonly="true"></div><span class="wooreview">'.$review_count.'</span></div>';
		}
		$wooslideprice='<div class="ecomwrap" ><span class="regular" '.$featured_slider_css['featured_slide_wooprice'].'>'.$reg_amount.'</span><span class="sale-price" '.$featured_slider_css['featured_slide_woosaleprice'].'>'.$saleprice.'</span>'.$button_adc.'<br />'.$product_cat.$average_rating.'</div>';
		$slider_excerpt .= $wooslideprice;
	}
	if($type == 'ecom') {
		$product_cat='';
		$categories = get_the_terms($id, 'wpsc_product_category');
		if($categories && $featured_slider_curr['enable_woocat'] == '1') {
			$c=0;
			$product_cat='<span class="featured_woocat_wrap"><span class="featured_woocat">';
			foreach($categories as $category) {
				$pro_cat=$category->name;
				$cat_link=get_term_link($category,'wpsc_product_category');
				$comma=', ';				
				if($c=='0')$comma='';
				$product_cat.=$comma.'<a href='.$cat_link.' '.$featured_slider_css['featured_slide_cat'].'>'.$pro_cat.'</a>';
				$c++;
			}		
			$product_cat.='</span></span>';
		}

		// eCom checkout link
		$checkout_link='<a class="checkout_link" href='.get_option( 'shopping_cart_url' ).'>'.__( 'Go to Checkout', 'wpsc' ).'</a>';	
		// eCommerce
		$product = get_post( $id );
		$regular_price = get_product_meta( $id, 'price', true);
		$sale_price = get_product_meta( $id, 'special_price' , true);	
		// for average rating and review count	
		global $wpdb;
		$get_average = $wpdb->get_results( $wpdb->prepare( "SELECT AVG(`rated`) AS `average`, COUNT(*) AS `count` FROM `" . WPSC_TABLE_PRODUCT_RATING . "` WHERE `productid`= %d ", $id ), ARRAY_A );
		
		$average = floor( $get_average[0]['average'] );
		$count = $get_average[0]['count'];

		if($count>1) {
			$review_count='('.$count." Reviews)";
		}
		else {
			$review_count='';
		}
		$adcbutton = '';
		if( $featured_slider_curr['enable_wooaddtocart'] == '1' ) {	
			$adcbutton = wpsc_add_to_cart_button($id,true);
		}
		$slide_star_html ='<div class="slidewoorate" >';
		if( $featured_slider_curr['enable_woostar'] == '1' ) {
			$slide_star_html .= '<div class="rateit '.$featured_slider_curr['nav_woo_star'].'" data-rateit-max="'.$average.'" data-rateit-value="'.$average.'" data-rateit-ispreset="true" data-rateit-starwidth="16" data-rateit-starheight="16" data-rateit-readonly="true"></div>';
		}
		$slide_star_html .= '<span class="wooreview">'.$review_count.'</span></div>';
		$regular_price_currency = '';
		if( $featured_slider_curr['enable_wooregprice'] == '1' ) {
			$regular_price_currency =wpsc_currency_display($regular_price);
		}
		$sale_price_currency = '';
		if($featured_slider_curr['enable_woosprice'] == '1') {
			$sale_price_currency =wpsc_currency_display($sale_price);
		}
		if($sale_price==0) {
			$slideprice='<p><span class="slideprice regular" '.$featured_slider_css['featured_slide_wooprice'].'>'.$regular_price_currency.'</span></p>'.$adcbutton.$checkout_link.'<br/>'.$product_cat;
		} 
		else {
			$sale_regular = '';
			if( $featured_slider_curr['enable_wooregprice'] == '1' ) {
				$sale_regular='<strike>'.$regular_price_currency.'</strike>';
			}
			$slideprice='<p class="slideprice" ><span class="regular" '.$featured_slider_css['featured_slide_wooprice'].'>'.$sale_regular.'</span><span class="sale-price" '.$featured_slider_css['featured_slide_woosaleprice'].'>'.$sale_price_currency.' </span></p>'.$adcbutton.$checkout_link.'<br/>'.$product_cat;
		}
		$slider_excerpt.= '<div class="ecomwrap" >'.$slideprice.$slide_star_html.'</div>';
	}
	
	if($type == 'eman'  && class_exists('EM_Object') ) {
		$event = em_get_event($id, 'post_id');
		$event_time = date('h:i A', strtotime(substr( $event->event_start_time, 0, 5 )))." - ".date('h:i A', strtotime(substr ( $event->event_end_time, 0, 5 ))); 
		$event_date = $event->event_start_date;
		if(!empty($event_date)) $event_date = date( 'jS M, Y' , strtotime($event_date) );
		$slidedatetime = '';
		$loc_id = $event->location_id;	
		$location = em_get_location($loc_id, $search_by = 'location_id');		
		$location_link = $location->guid;
		$event_addr='';
		if($featured_slider_curr['enable_eventadd']=='1') {
			$event_taddress=$location->location_name.' '.$location->location_address.' '.$location->location_state.' '.$location->location_country;
			$trimmed_addr=trim($event_taddress);
			if($trimmed_addr!='') {
				$event_addr.= '<span class="event_address_wrap"><a class="featured_eventaddr" href="'.$location_link.'"></a><span class="event_addr" '.$featured_slider_css['eventm_addr'].'>'.$event_taddress.'</span></span>';
			}
		}
		$event_cat='';
		$categories = get_the_terms($id,'event-categories');	
		if($categories) {
			$c=0;
			if($featured_slider_curr['enable_eventcat']=='1') {
				$event_cat='<span class="featured_eventcat_wrap"><span class="featured_eventcat">';
				foreach($categories as $category) {
					$cat_name=$category->name;
					$cat_link=get_term_link($category,'event-categories');
					$comma=', ';				
					if($c=='0')$comma='';
					$event_cat.=$comma.'<a href='.$cat_link.' '.$featured_slider_css['eventm_cat'].'>'.$cat_name.'</a>';
					$c++;
				}		
				$event_cat.='</span></span>';
			}
		}
		if( $featured_slider_curr['enable_eventdt'] == '1' ) {
			$slidedatetime = '<div class ="slidedatetime" '.$featured_slider_css['slide_eventm_datetime'].'><span class="eventdateico">'.$event_date.'</span><span class="eventtimeico">'.$event_time.'</span></div>';
		}
		$slider_excerpt.='<div class="eventwrap" >'.$slidedatetime.$event_addr.$event_cat.'</div>';
	}
	/**
	 * ---------------------------------------------------------------------------------------------
	 * Filter to add The Events Calendar html fields(Category, Location) in slide excerpt html
	 *
	 * @return HTML including The Events Calendar fields
	 * ---------------------------------------------------------------------------------------------
	 **/
	if($type == 'ecal'  && class_exists('TribeEvents') ) {
		
		$startdate =tribe_get_start_date($id, true, 'jS M, Y' );  /* 'D . F j Y g:i A' Mon . August 6 2014 10:00 AM Sun */
		if(!empty($startdate)) $starttime = tribe_get_start_date($id, true, 'h:i A' );
		$endtime =tribe_get_end_date($id, true, 'g:i a' );
		$slidedatetime = '';

		$eventaddress = tribe_get_full_address($id);
		$gmaplink = tribe_get_map_link($id);	
		
		//Added in title and not in excerpt
		$event_addr = '';
		if($featured_slider_curr['enable_eventadd']=='1') {
			$event_addr= '<span class="event_address_wrap"><a class="featured_eventaddr" href="'.$gmaplink.'"></a><span class="event_addr" '.$featured_slider_css['eventm_addr'].'>'.$eventaddress.'</span></span>'; 
		}

		$event_cat='';
		$categories = get_the_terms($id,'tribe_events_cat');
		if($categories) {
			$c=0;
			if($featured_slider_curr['enable_eventcat']=='1') {
				$event_cat='<span class="featured_eventcat_wrap"><span class="featured_eventcat">';
				foreach($categories as $category) {
					$cat_name=$category->name;
					$cat_link=get_term_link($category,'event-categories');
					$comma=', ';				
					if($c=='0')$comma='';
					$event_cat.=$comma.'<a href='.$cat_link.' '.$featured_slider_css['eventm_cat'].'>'.$cat_name.'</a>';
					$c++;
				}		
				$event_cat.='</span></span>';
			}
		}
		if( $featured_slider_curr['enable_eventdt'] == '1' ) {
			$slidedatetime = '<div class ="slidedatetime" '.$featured_slider_css['slide_eventm_datetime'].'><span class="eventdateico">'.$startdate.'</span><span class="eventtimeico">'.$starttime.' - '.$endtime.'</span></div>';
		}
		$slider_excerpt.= '<div class="eventwrap" >'.$slidedatetime.$event_addr.$event_cat.'</div>';
	}
	return $slider_excerpt;
}
add_filter('featured_slide_excerpt_html_trio', 'featured_slide_excerpt_html_filter_trio',10,6);
?>
