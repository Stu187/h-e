<?php 
function featured_hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);
   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return $rgb; // returns an array with the rgb values
}
function featured_ss_get_sliders(){
	global $wpdb,$table_prefix;
	$slider_meta = $table_prefix.FEATURED_SLIDER_META; 
	$sql = "SELECT * FROM $slider_meta WHERE type=0 || type=17";
 	$sliders = $wpdb->get_results($sql, ARRAY_A);
	return $sliders;
}
function featured_get_slider_posts_in_order($slider_id) {
    global $wpdb, $table_prefix;
	$table_name = $table_prefix.FEATURED_SLIDER_TABLE;
	$slider_posts = $wpdb->get_results("SELECT * FROM $table_name WHERE slider_id = '$slider_id' ORDER BY slide_order ASC, date DESC", OBJECT);
	return $slider_posts;
}
function get_featured_slider_name($slider_id) {
    global $wpdb, $table_prefix;
	$slider_name = '';
	$table_name = $table_prefix.FEATURED_SLIDER_META;
	$slider_obj = $wpdb->get_results("SELECT * FROM $table_name WHERE slider_id = '$slider_id'", OBJECT);
	if (isset ($slider_obj[0]))$slider_name = $slider_obj[0]->slider_name;
	return $slider_name;
}
function featured_ss_get_post_sliders($post_id){
    global $wpdb,$table_prefix;
	$slider_table = $table_prefix.FEATURED_SLIDER_TABLE; 
	$sql = "SELECT * FROM $slider_table 
	        WHERE post_id = '$post_id';";
	$post_sliders = $wpdb->get_results($sql, ARRAY_A);
	return $post_sliders;
}
function featured_ss_post_on_slider($post_id,$slider_id){
    global $wpdb,$table_prefix;
	$slider_postmeta = $table_prefix.FEATURED_SLIDER_POST_META;
    $sql = "SELECT * FROM $slider_postmeta  
	        WHERE post_id = '$post_id' 
			AND slider_id = '$slider_id';";
	$result = $wpdb->query($sql);
	if($result == 1) { return TRUE; }
	else { return FALSE; }
}
function featured_ss_slider_on_this_post($post_id){
    global $wpdb,$table_prefix;
	$slider_postmeta = $table_prefix.FEATURED_SLIDER_POST_META;
    $sql = "SELECT * FROM $slider_postmeta  
	        WHERE post_id = '$post_id';";
	$result = $wpdb->query($sql);
	if($result == 1) { return TRUE; }
	else { return FALSE; }
}
//Checks if the post is already added to slider
function featured_slider($post_id,$slider_id = '1') {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.FEATURED_SLIDER_TABLE;
	$check = "SELECT id FROM $table_name WHERE post_id = '$post_id' AND slider_id = '$slider_id';";
	$result = $wpdb->query($check);
	if($result == 1) { return TRUE; }
	else { return FALSE; }
}
function is_post_on_any_featured_slider($post_id) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.FEATURED_SLIDER_TABLE;
	$check = "SELECT post_id FROM $table_name WHERE post_id = '$post_id' LIMIT 1;";
	$result = $wpdb->query($check);
	if($result == 1) { return TRUE; }
	else { return FALSE; }
}
function is_featured_slider_on_slider_table($slider_id) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.FEATURED_SLIDER_TABLE;
	$check = "SELECT * FROM $table_name WHERE slider_id = '$slider_id' LIMIT 1;";
	$result = $wpdb->query($check);
	if($result == 1) { return TRUE; }
	else { return FALSE; }
}
function is_featured_slider_on_meta_table($slider_id) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.FEATURED_SLIDER_META;
	$check = "SELECT * FROM $table_name WHERE slider_id = '$slider_id' LIMIT 1;";
	$result = $wpdb->query($check);
	if($result == 1) { return TRUE; }
	else { return FALSE; }
}
function is_featured_slider_on_postmeta_table($slider_id) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.FEATURED_SLIDER_POST_META;
	$check = "SELECT * FROM $table_name WHERE slider_id = '$slider_id' LIMIT 1;";
	$result = $wpdb->query($check);
	if($result == 1) { return TRUE; }
	else { return FALSE; }
}
function get_featured_slider_for_the_post($post_id) {
    global $wpdb, $table_prefix;
	$table_name = $table_prefix.FEATURED_SLIDER_POST_META;
	$sql = "SELECT slider_id FROM $table_name WHERE post_id = '$post_id' LIMIT 1;";
	$slider_postmeta = $wpdb->get_row($sql, ARRAY_A);
	$slider_id = $slider_postmeta['slider_id'];
	return $slider_id;
}
function featured_slider_word_limiter( $text, $limit = 50 ) {
    $text = str_replace(']]>', ']]&gt;', $text);
	//Not using strip_tags as to accomodate the 'retain html tags' feature
	//$text = strip_tags($text);
	
    $explode = explode(' ',$text);
    $string  = '';

    $dots = '...';
    if(count($explode) <= $limit){
        $dots = '';
    }
    for($i=0;$i<$limit;$i++){
        if (isset ($explode[$i]))  $string .= $explode[$i]." ";
    }
    if ($dots) {
        $string = substr($string, 0, strlen($string));
    }
    return $string.$dots;
}
function featured_sslider_admin_url( $query = array() ) {
	global $plugin_page;

	if ( ! isset( $query['page'] ) )
		$query['page'] = $plugin_page;

	$path = 'admin.php';

	if ( $query = build_query( $query ) )
		$path .= '?' . $query;

	$url = admin_url( $path );

	return esc_url_raw( $url );
}
function featured_slider_table_exists($table, $db) { 
	$tables = mysql_list_tables ($db); 
	while (list ($temp) = mysql_fetch_array ($tables)) {
		if ($temp == $table) {
			return TRUE;
		}
	}
	return FALSE;
}
function get_featured_nextgen_galleries($id = '1') {
	global $wpdb, $table_prefix;
	$options ='';
	$table_name = $table_prefix."ngg_gallery";
	if($wpdb->get_var("show tables like '$table_name'") == $table_name) {
		$galleries = "SELECT * FROM $table_name";
		$result = $wpdb->get_results($galleries);
		foreach($result as $res) {
			$gid = isset($res->gid)?$res->gid:'1';
			$name = isset($res->name)?$res->name:'';
			$options .= "<option value='".$gid."' ".selected($id,$gid, false).">".$name."</option>";
		}
		return $options;
	} else return '';
}
// Transition 
function get_featured_transitions($name,$transtion) {
	$transition = '<select name="'.$name.'" class="featured_transitions" >
	 <option value="">'.__('Choose Transition','featured-slider').'</option>
	<optgroup label="'.__('Attention Seekers','featured-slider').'">
          <option value="bounce" '. selected( $transtion, "bounce", false ) .'>'.__('bounce','featured-slider').'</option>
          <option value="flash" '. selected( $transtion, "flash", false ) .'>'.__('flash','featured-slider').'</option>
          <option value="pulse" '. selected( $transtion, "pulse", false ) .'>'.__('pulse','featured-slider').'</option>
          <option value="rubberBand" '. selected( $transtion, "rubberBand", false ) .'>'.__('rubberBand','featured-slider').'</option>
          <option value="shake" '. selected( $transtion, "shake", false ) .'>'.__('shake','featured-slider').'</option>
          <option value="swing" '. selected( $transtion, "swing", false ) .'>'.__('swing','featured-slider').'</option>
          <option value="tada" '. selected( $transtion, "tada", false ) .'>'.__('tada','featured-slider').'</option>
          <option value="wobble" '. selected( $transtion, "wobble", false ) .'>'.__('wobble','featured-slider').'</option>
        </optgroup>
	<optgroup label="'.__('Bouncing Entrances','featured-slider').'">
          <option value="bounceIn" '. selected( $transtion, "bounceIn", false ) .'>'.__('bounceIn','featured-slider').'</option>
          <option value="bounceInDown" '. selected( $transtion, "bounceInDown", false ) .'>'.__('bounceInDown','featured-slider').'</option>
          <option value="bounceInLeft" '. selected( $transtion, "bounceInLeft", false ) .'>'.__('bounceInLeft','featured-slider').'</option>
          <option value="bounceInRight" '. selected( $transtion, "bounceInRight", false ) .'>'.__('bounceInRight','featured-slider').'</option>
          <option value="bounceInUp" '. selected( $transtion, "bounceInUp", false ) .'>'.__('bounceInUp','featured-slider').'</option>
        </optgroup>

       <optgroup label="'.__('Fading Entrances','featured-slider').'">
          <option value="fadeIn" '. selected( $transtion, "fadeIn", false ) .'>'.__('fadeIn','featured-slider').'</option>
          <option value="fadeInDown" '. selected( $transtion, "fadeInDown", false ) .'>'.__('fadeInDown','featured-slider').'</option>
          <option value="fadeInDownBig"'. selected( $transtion, "fadeInDownBig", false ) .'>'.__('fadeInDownBig','featured-slider').'</option>
          <option value="fadeInLeft" '. selected( $transtion, "fadeInLeft", false ) .'>'.__('fadeInLeft','featured-slider').'</option>
          <option value="fadeInLeftBig" '. selected( $transtion, "fadeInLeftBig", false ) .'>'.__('fadeInLeftBig','featured-slider').'</option>
          <option value="fadeInRight" '. selected( $transtion, "fadeInRight", false ) .'>'.__('fadeInRight','featured-slider').'</option>
          <option value="fadeInRightBig" '. selected( $transtion, "fadeInRightBig", false ) .'>'.__('fadeInRightBig','featured-slider').'</option>
          <option value="fadeInUp" '. selected( $transtion, "fadeInUp", false ) .'>'.__('fadeInUp','featured-slider').'</option>
          <option value="fadeInUpBig" '. selected( $transtion, "fadeInUpBig", false ) .'>'.__('fadeInUpBig','featured-slider').'</option>
        </optgroup>

       <optgroup label="'.__('Flippers','featured-slider').'">
          <option value="flip" '. selected( $transtion, "flip", false ) .'>'.__('flip','featured-slider').'</option>
          <option value="flipInX" '. selected( $transtion, "flipInX", false ) .'>'.__('flipInX','featured-slider').'</option>
          <option value="flipInY" '. selected( $transtion, "flipInY", false ) .'>'.__('flipInY','featured-slider').'</option>
       </optgroup>

        <optgroup label="'.__('Lightspeed','featured-slider').'">
          <option value="lightSpeedIn" '. selected( $transtion, "lightSpeedIn", false ) .'>'.__('lightSpeedIn','featured-slider').'</option>
        </optgroup>

        <optgroup label="'.__('Rotating Entrances','featured-slider').'">
          <option value="rotateIn" '. selected( $transtion, "rotateIn", false ) .'>'.__('rotateIn','featured-slider').'</option>
          <option value="rotateInDownLeft" '. selected( $transtion, "rotateInDownLeft", false ) .'>'.__('rotateInDownLeft','featured-slider').'</option>
          <option value="rotateInDownRight" '. selected( $transtion, "rotateInDownRight", false ) .'>'.__('rotateInDownRight','featured-slider').'</option>
          <option value="rotateInUpLeft" '. selected( $transtion, "rotateInUpLeft", false ) .'>'.__('rotateInUpLeft','featured-slider').'</option>
          <option value="rotateInUpRight" '. selected( $transtion, "rotateInUpRight", false ) .'>'.__('rotateInUpRight','featured-slider').'</option>
        </optgroup>

        <optgroup label="'.__('Specials','featured-slider').'">
          <option value="hinge" '. selected( $transtion, "hinge", false ) .'>'.__('hinge','featured-slider').'</option>
          <option value="rollIn" '. selected( $transtion, "rollIn", false ) .'>'.__('rollIn','featured-slider').'</option>
        </optgroup>

        <optgroup label="'.__('Zoom Entrances','featured-slider').'">
          <option value="zoomIn" '. selected( $transtion, "zoomIn", false ) .'>'.__('zoomIn','featured-slider').'</option>
          <option value="zoomInDown" '. selected( $transtion, "zoomInDown", false ) .'>'.__('zoomInDown','featured-slider').'</option>
          <option value="zoomInLeft" '. selected( $transtion, "zoomInLeft", false ) .'>'.__('zoomInLeft','featured-slider').'</option>
          <option value="zoomInRight" '. selected( $transtion, "zoomInRight", false ) .'>'.__('zoomInRight','featured-slider').'</option>
          <option value="zoomInUp" '. selected( $transtion, "zoomInUp", false ) .'>'.__('zoomInUp','featured-slider').'</option>
        </optgroup>
	
	 <optgroup label="'.__('Slide Entrances','featured-slider').'">
          <option value="slideInDown" '. selected( $transtion, "slideInDown", false ) .'>'.__('slideInDown','featured-slider').'</option>
          <option value="slideInLeft" '. selected( $transtion, "slideInLeft", false ) .'>'.__('slideInLef','featured-slider').'t</option>
          <option value="slideInRight" '. selected( $transtion, "slideInRight", false ) .'>'.__('slideInRight','featured-slider').'</option>
          <option value="slideInUp" '. selected( $transtion, "slideInUp", false ) .'>'.__('slideInUp','featured-slider').'</option>
         </optgroup>
      
</select>';
	return $transition;
}
function get_featured_slider_default_settings() {
	$default_featured_slider_settings = array(
	'speed'=>'10', 
	'time'=>'3',
	'no_posts'=>'10',
	'bg_color'=>'#000000', 
	'height'=>'400',
	'width'=>'1000',
	'lswidth'=>'50',
	'border'=>'1',
	'brcolor'=>'#000000',
	'prev_next'=>'1',
	
	'title_text'=>'Featured Articles',
	'title_from'=>'0',
	
	't_font' => 'regular',
	'title_font'=>'Trebuchet MS,sans-serif',
	'title_fontg'=>'',
	'title_fsize'=>'18',
	'title_fstyle'=>'normal',
	'title_fcolor'=>'#3F4C6B',	
	'title_fontgw' => '',
	'title_fontgsubset' => array(),	
	'titlefont_custom' => '',
	'show_title'=>'1',
	'pt_font'=>'regular',
	'ptfont_custom'=>'',
	'ptitle_font'=>'sans-serif',
	'ptitle_fontg'=>'Oswald',
	'ptitle_fontgw'=>'',
	'ptitle_fontgsubset'=> array(),
	'ptitle_fsize'=>'40',
	'ptitle_fstyle'=>'normal',
	'ptitle_fcolor'=>'#ffffff',
	'ptitle_transition'=>'',
	'ptitle_duration'=>'',
	'ptitle_delay'=>'',
	'show_sub_title'=>'1',
	'sub_pt_font'=>'regular',
	'sub_ptfont_custom'=>'',
	'sub_ptitle_font'=>'sans-serif',
	'sub_ptitle_fontg'=>'Oswald',
	'sub_ptitle_fontgw'=>'',
	'sub_ptitle_fontgsubset'=> array(),
	'sub_ptitle_fsize'=>'20',
	'sub_ptitle_fstyle'=>'normal',
	'sub_ptitle_fcolor'=>'#ffffff',
	'sub_ptitle_transition'=>'',
	'sub_ptitle_duration'=>'',
	'sub_ptitle_delay'=>'',
	'show_content'=>'0',
	'pc_font'=>'regular',
	'pcfont_custom' => '',
	'content_font'=>'Arial,Helvetica,sans-serif',
	'content_fontg'=>'',
	'content_fontgw'=>'',
	'content_fontgsubset'=> array(),
	'content_fsize'=>'12',
	'content_fstyle'=>'normal',
	'content_fcolor'=>'#ffffff',
	'content_transition'=>'',
	'content_duration'=>'',
	'content_delay'=>'',
	
	'content_from'=>'content',
	'content_chars'=>'',
	'show_meta'=>'0',
	'mt_font' => 'regular',
	'meta_title_font' => 'Verdana,Geneva,sans-serif',
	'meta_title_fontg' => '',
	'meta_title_fontgw' => '',
	'meta_title_fontgsubset' => array(),
	'meta_title_fcolor' => '#a8a8a8',
	'meta_title_fsize' => '12',
	'meta_title_fstyle' => 'normal',
	'mtfont_custom' => '',
	'meta1_fn' => 'featured_get_slide_author',
	'meta1_parms' => 'field=display_name',
	'meta1_before' => 'Posted by ',
	'meta1_after' => ' ',
	'meta2_fn' => 'featured_get_slide_pub_date',
	'meta2_parms' => 'format=M j, Y',
	'meta2_before' => 'on ',
	'meta2_after' => '',
	
	'block_pos'=>'1',
	'trio_block'=>'3',
	'bg'=>'0',
	'image_only'=>'0',
	'allowable_tags'=>'',
	'more'=>'read more',
	'a_attr'=>'',
	'img_size'=>'1',
	'img_transition'=>'',
	'img_duration'=>'',
	'img_delay'=>'',
	'img_pick'=>array('1','featured_slider_thumbnail','1','1','1','1'), 
	'crop'=>'0',
	'transition'=>'scrollHorz',
	'easing'=>'easeInExpo',
	'autostep'=>'1',
	'content_limit'=>'10',
	'stylesheet'=>'default',
	'rand'=>'0',
	'fields'=>'',
	'ver'=>'1',
	'fouc'=>'0',
	'buttons'=>'default',
	'nav_w'=>'64',
	'nav_h'=>'64',
	'preview'=>'2',
	'slider_id'=>'1',
	'catg_slug'=>'',
	'setname'=>'Set',
	'disable_preview'=>'0',
	'ptext_width'=>'',
	'image_title_text'=>'0',
	'active_tab'=>array('active_tabidx'=>'0','closed_sections'=>''),
	'default_image'=>featured_slider_plugin_url( 'images/default_image.png' ),
	'skin_array'=>array('default'),
	'pphoto'=>'0',
	'lbox_type'=>'pphoto_box',
	'nav_margin'=>'0',
	'more_color'=>'#ffffff',
	'cropping'=>'1',
	'climit'=>'0',
	'mtitle_element'=>'2',
	'stitle_element'=>'2',
	//added for events and wooCom
	
	//Add to Cart	
	'enable_wooaddtocart' => '1',
	'woo_adc_text'=>'Add to Cart',
	'woo_adc_color'=>'#3DB432',
	'woo_adc_tcolor'=>'#ffffff',
	'woo_adc_fsize'=>'14',
	'woo_adc_border'=>'1',
	'woo_adc_brcolor'=>'#3db432',
	//sale strip	
	'enable_woosalestrip'=>'1',					
	'woo_sale_color'=>'#3DB432',
	'woo_sale_text'=>'Sale',
	'woo_sale_tcolor'=>'#ffffff',
	//regular slide price
	'enable_wooregprice'=>'1',
	'woo_font' => 'regular',
	'slide_woo_price_font' => 'Arial,Helvetica,sans-serif',
	'slide_woo_price_fontg'=> '',
	'slide_woo_price_fontgw'=> '',
	'slide_woo_price_fontgsubset'=> array(),
	'slide_woo_price_custom'=> '',
	'slide_woo_price_fcolor'=>'#ffffff',
	'slide_woo_price_fsize'=>'16',
	'slide_woo_price_fstyle'=> 'normal',
	//sale slide price
	'enable_woosprice'=>'1',
	'woosale_font'=> 'regular',
	'slide_woo_saleprice_font' => 'Arial,Helvetica,sans-serif',
	'slide_woo_saleprice_fontg'=> '',
	'slide_woo_saleprice_fontgw'=> '',
	'slide_woo_saleprice_fontgsubset'=> array(),
	'slide_woo_saleprice_custom'=> '',
	'slide_woo_saleprice_fcolor'=>'#eeee22',
	'slide_woo_saleprice_fsize'=>'14',
	'slide_woo_saleprice_fstyle'=> 'normal',
	//sale slide category
	'enable_woocat'=>'1',
	'woocat_font' => 'regular',
	'slide_woo_cat_font'=> 'Arial,Helvetica,sans-serif',
	'slide_woo_cat_fontg'=> '',
	'slide_woo_cat_fontgw'=> '',
	'slide_woo_cat_fontgsubset'=> array(),
	'slide_woo_cat_custom' => '',
	'slide_woo_cat_fcolor'=>'#ffffff',
	'slide_woo_cat_fsize'=>'14',
	'slide_woo_cat_fstyle'=> 'normal',
	'nav_woo_star'=> 'yellow',
	'enable_woostar'=>'1',	
	'woo_type'=> '0',
	'product_id'=>'',
	'ecom_type'=>'0',
	'event_type'=> '0',
	'eventcal_type'=> '0', 
	'product_woocatg_slug'=>'',
	'product_ecomcatg_slug'=>'',
	'events_mancatg_slug'=>'',
	'events_mantag_slug'=>'',
	'events_calcatg_slug'=>'',
	'events_caltag_slug'=>'',
	// events manager
	'enable_eventdtnav'=>'1',
	'enable_eventdt'=>'1',
	'enable_eventadd'=>'1',
	'enable_eventcat'=>'1',
	'eventmd_font' => 'regular', 
	'slide_eventm_font'=> 'Arial,Helvetica,sans-serif',
	'slide_eventm_fontg'=> '',
	'slide_eventm_fontgw'=> '',
	'slide_eventm_fontgsubset'=> array(),
	'slide_eventm_custom'=> '',
	'slide_eventm_fcolor'=>'#ffffff',
	'slide_eventm_fsize'=>'14',
	'slide_eventm_fstyle'=> 'normal',
	'event_addr_font'=> 'regular',
	'eventm_addr_font'=> 'Arial,Helvetica,sans-serif',
	'eventm_addr_fontg'=> '',
	'eventm_addr_fontgw'=> '',
	'eventm_addr_fontgsubset'=> array(),
	'eventm_addr_custom'=> '',
	'eventm_addr_fcolor'=>'#ffffff',
	'eventm_addr_fsize'=>'14',
	'eventm_addr_fstyle'=> 'normal',
	'event_cat_font'=> 'regular',
	'eventm_cat_font'=> 'Arial,Helvetica,sans-serif',
	'eventm_cat_fontg'=> '',
	'eventm_cat_fontgw'=> '',
	'eventm_cat_fontgsubset'=> array(),
	'eventm_cat_custom'=> '',
	'eventm_cat_fcolor'=>'#ffffff',
	'eventm_cat_fsize'=>'14',
	'eventm_cat_fstyle'=> 'normal',
	// Taxonomy
	'taxonomy_posttype'=> 'post',
	'taxonomy'=> 'category',
	'taxonomy_term'=> '',
	'taxonomy_show'=> '',
	'taxonomy_operator'=> '',
	'taxonomy_author'=> '',
	// Rss feed
	'rssfeed_id'=> '1',
	'rssfeed_feedurl'=> 'http://mashable.com/feed/',
	'rssfeed_default_image'=>featured_slider_plugin_url( 'images/default_image.png' ),
	'rssfeed_feed'=> 'rss',
	'rssfeed_order'=> '0',
	'rssfeed_content'=> '',
	'rssfeed_media'=> '1',
	'rssfeed_src'=> '',
	'rssfeed_size'=> '',
	'offset' => '0',
	'rssfeed_image_class'=>'',
	// post attachment
	'postattch_id'=> '',
	'donotlink'=> '0',
	// NextGenGallery  
	'nextgen_gallery_id'=> '1', 
	'nextgen_anchor' => '0',
	'active_accordion' =>'basic',
	'fixblocks'=>'0',
	'slide_border'=>'0',
	'slide_brcolor'=>'#222222',
	'disable_mobile'=>'0'
);
	return $default_featured_slider_settings;
}
function get_featured_slider_global_default_settings() {
	$default_featured_slider_global_settings = array(
		'fb_app_key' => '',
		'insta_client_id' => '',
		'flickr_app_key' => '',
		'youtube_app_id' => '',
		'px_ckey' => '',
		/*old settings*/
		'user_level' => 'edit_others_posts',
		'noscript'=> 'This page is having a slideshow that uses Javascript. Your browser either doesn\'t support Javascript or you have it turned off. To see this page as it is meant to appear please use a Javascript enabled browser.',
		'multiple_sliders' => '1',
		'enque_scripts' => '0',
		'custom_post' =>'1', 
		'cpost_slug' => 'slidervilla',
		'remove_metabox'=>array(),
		'css'=>'',
		'support'=>'1'
	);
	return $default_featured_slider_global_settings;
}
function populate_featured_current( $featured_slider_curr ) {
	$default_featured_slider_settings=get_featured_slider_default_settings();
	foreach($default_featured_slider_settings as $key=>$value){
		if(!isset($featured_slider_curr[$key])) $featured_slider_curr[$key]='';
	}
	return $featured_slider_curr;
}

?>
