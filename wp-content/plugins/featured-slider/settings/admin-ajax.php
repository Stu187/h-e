<?php
if ( is_admin() ) {
	/*
	* ----------------------------------------------------------------------
	*	Easy Builder Functions for Custom Slider and update slider type
	* ----------------------------------------------------------------------
	*/
	add_action( 'wp_ajax_featured_add_form', 'featured_add_form' );
	add_action( 'wp_ajax_featured_show_posts', 'featured_show_posts' );
	add_action( 'wp_ajax_featured_add_video','featured_add_video' );
	add_action( 'wp_ajax_featured_show_px','featured_show_px' );
	add_action( 'wp_ajax_featured_show_fb','featured_show_fb' );
	add_action( 'wp_ajax_featured_show_flickr','featured_show_flickr' );
	add_action( 'wp_ajax_featured_show_it','featured_show_it' );
	add_action( 'wp_ajax_featured_fip_insert','featured_fip_insert' );
	add_action( 'wp_ajax_featured_insert_video','featured_insert_video' );
	add_action( 'wp_ajax_featured_show_post_type','featured_show_post_type');
	add_action( 'wp_ajax_featured_insert_posts', 'featured_insert_posts' );
	add_action( 'wp_ajax_featured_insert_slide', 'featured_insert_slide' );
	add_action( 'wp_ajax_featured_shfb_album','featured_shfb_album' );
	add_action( 'wp_ajax_featured_slider_preview','featured_slider_preview' );
	add_action( 'wp_ajax_featured_insert_media','featured_insert_media' );
	add_action( 'wp_ajax_featured_delete_slide','featured_delete_slide' );
	// update slider type
	add_action( 'wp_ajax_featured_update_slider_type', 'featured_updt_sldr_type');
	add_action( 'wp_ajax_featured_change_type', 'featured_change_type');
	add_action( 'wp_ajax_featured_show_params', 'featured_show_params');
	//Easy Bulder Settings 
	add_action( 'wp_ajax_featured_eb_settings', 'featured_eb_settings');
	/*
	* -----------------------------------------------------------------
	*	Create new slider Functions 
	* -----------------------------------------------------------------
	*/
	add_action( 'wp_ajax_featured_show_taxonomy','featured_show_taxonomy' );
	add_action( 'wp_ajax_featured_show_term','featured_show_term' );
	add_action( 'wp_ajax_featured_woo_product','featured_woo_product');
	/*
	* -----------------------------------------------------------------
	*	Google fonts Functions 
	* -----------------------------------------------------------------
	*/
	add_action( 'wp_ajax_featured_disp_gfweight','featured_google_font_weight');
	add_action( 'wp_ajax_featured_load_fontsdiv','featured_load_fontsdiv_callback');
	/*
	* -----------------------------------------------------------------
	*	Settings Set preview Params
	* -----------------------------------------------------------------
	*/
	add_action( 'wp_ajax_featured_preview_params','featured_preview_params' );
	add_action( 'wp_ajax_featured_tab_contents','featured_tab_contents' );
	
}
/*
* -----------------------------------------------------------------
*	Easy Builder Functions for Custom Slider
* -----------------------------------------------------------------
*/
function featured_insert_media() {
	check_ajax_referer( 'featured-add-slides-nonce', 'add_slides' );
	$images=(isset($_POST['imgID']))?$_POST['imgID']:array();
	$slider_id=$_POST['current_slider_id'];
	$ids=array_reverse($images);
	global $wpdb,$table_prefix;
	foreach($ids as $id){
		$title=(isset($_POST['title'][$id]))?$_POST['title'][$id]:'';
		$desc=(isset($_POST['desc'][$id]))?$_POST['desc'][$id]:'';
		$link=(isset($_POST['link'][$id]))?$_POST['link'][$id]:'';
		$nolink=(isset($_POST['nolink'][$id]))?$_POST['nolink'][$id]:'';
		$attachment = array(
			'ID'           => $id,
			'post_title'   => $title,
			'post_content' => $desc
		);
		wp_update_post( $attachment );
		update_post_meta($id, 'featured_slide_redirect_url', $link);
		update_post_meta($id, 'featured_sslider_nolink', $nolink);
		update_post_meta($id, '_featured_slide_type', '1');
		if(!featured_slider($id,$slider_id)) {
				$dt = date('Y-m-d H:i:s');
				$sql = "INSERT INTO ".$table_prefix.FEATURED_SLIDER_TABLE." (post_id, date, slider_id) VALUES ('$id', '$dt', '$slider_id')";
				$wpdb->query($sql);
		}
	}
	die();
}
function featured_slider_preview() {
	check_ajax_referer( 'featured-preview-nonce', 'preview_html' );
	global $wpdb,$table_prefix;
	$slider_meta = $table_prefix.FEATURED_SLIDER_META;
	if(isset($_POST['slider_id']) ) { 
		$slider_id = $_POST['slider_id'];
		$sql = "SELECT * FROM $slider_meta WHERE slider_id=$slider_id";
		$result = $wpdb->get_row($sql);
		$param_array = unserialize($result->param);
		$sliderset = $result->setid;
	}
	$html = '<div class="eb-preview-title">'.__('Live Preview','featured-slider').'</div>';
	$offset = isset($param_array['offset'])?$param_array['offset']:'0';
	$scounter = isset($result->setid)?$result->setid :'';
	if($scounter == 1 ) $scounter = '';
	if( isset($_POST['slider_id']) && $_POST['slider_id'] != "" && $_POST['slider_id']!='0') {
		$html .= return_featured_slider($_POST['slider_id'],$scounter,$offset);
	}
	$html .='featuredSplit';
	/* Thumbs Code */
	$slider_id = isset($_POST['slider_id'])?$_POST['slider_id']:'';
	$slider_posts=featured_get_slider_posts_in_order($slider_id);
	$count = 0;
	if(isset($sliderset) && $sliderset != '1' ) $cntr = $sliderset; else $cntr = '';
	$featured_slider_options='featured_slider_options'.$cntr;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	foreach($slider_posts as $slider_post) {
		$icon = '';
		$slider_arr[] = $slider_post->post_id;
		$post = get_post($slider_post->post_id);	  
		if(isset($post) and isset($slider_arr)){
			if ( in_array($post->ID, $slider_arr) ) {
				$count++;
				/*---------- Image Fetch Start ---------*/
				$post_id = $post->ID;
				$featured_media = get_post_meta($post_id,'featured_media',true);
				if(!isset($featured_slider_curr['img_pick'][0])) $featured_slider_curr['img_pick'][0]='';
				if(!isset($featured_slider_curr['img_pick'][2])) $featured_slider_curr['img_pick'][2]='';
				if(!isset($featured_slider_curr['img_pick'][3])) $featured_slider_curr['img_pick'][3]='';
				if(!isset($featured_slider_curr['img_pick'][5])) $featured_slider_curr['img_pick'][5]='';
				   	
				$custom_key = '';
				if ((isset ($featured_slider_curr['img_pick'][0])) && (isset ($featured_slider_curr['img_pick'][1]))) {
					if($featured_slider_curr['img_pick'][0] == '1'){
					 $custom_key = array($featured_slider_curr['img_pick'][1]);
					}
				}

				$the_post_thumbnail = false;
				if(isset ($featured_slider_curr['img_pick'][2])){
					if($featured_slider_curr['img_pick'][2] == '1') $the_post_thumbnail = true;
				}
		
				$attachment = false;
				$order_of_image = '1';
				if (isset ($featured_slider_curr['img_pick'][3])) {
					if($featured_slider_curr['img_pick'][3] == '1'){
					 $attachment = true;
					 $order_of_image = $featured_slider_curr['img_pick'][4];
					}
				}
		
				$image_scan = false;
				if (isset ($featured_slider_curr['img_pick'][5])) {
					if($featured_slider_curr['img_pick'][5] == '1'){
						 $image_scan = true;
					}
				}

				$extract_size = 'thumbnail';
				
				$default_image=(isset($featured_slider_curr['default_image']))?($featured_slider_curr['default_image']):('false');
				$img_args = array(
					'custom_key' => $custom_key,
					'post_id' => $post_id,
					'attachment' => $attachment,
					'size' => $extract_size,
					'the_post_thumbnail' => $the_post_thumbnail,
					'default_image' => $default_image,
					'order_of_image' => $order_of_image,
					'link_to_post' => false,
					'image_class' => "featured_slider_thumbnail",
					'image_scan' => $image_scan,
					'width' => '70',
					'height' => false,
					'echo' => false,
					'style'=> ''
				);
				$navigation_image=featured_sslider_get_the_image($img_args);
				/*------------ Image Fetch End -----------*/
				$sslider_author = get_userdata($post->post_author);
				$sslider_author_dname = $sslider_author->display_name;
				$desc = $post->post_content;
				//$delurl = admin_url("admin.php?page=featured-slider-easy-builder&id=$slider_id&del=$post->ID");
				$featured_sslider_link = get_post_meta($post_id,'featured_slide_redirect_url',true);
				$stype = get_post_meta($post_id,'_featured_slide_type',true);
				if($stype == 1 ) { $icon = '<div class="cs-icon"><i class="fa fa-file-word-o"></i></div>'; }
				if($stype == 5 ) { $icon = '<div class="cs-icon"><i class="fa fa-facebook"></i></div>'; }
				if($stype == 6 ) { $icon = '<div class="cs-icon"><i class="fa fa-flickr"></i></div>'; }
				if($stype == 7 ) { $icon = '<div class="cs-icon"><i class="fa fa-instagram"></i></div>'; }
				if($stype == 8 ) { $icon = '<div class="cs-icon"><i class="fa fa-youtube"></i></div>'; }
				if($stype == 9 ) { $icon = '<div class="cs-icon"><i class="fa fa-vimeo-square"></i></div>'; }
				if($stype == 10 ) { $icon = '<div class="cs-icon"><img src="'.featured_slider_plugin_url( 'images/500px.png' ).'" width="13" height="14" style="vertical-align: middle;" /></div>'; }
			 	$html .= '<div id="'.$post->ID.'" class="featured-reorder"><input type="hidden" name="order[]" value="'.$post->ID.'" />'.$icon.'<div>'.$navigation_image;
				if($post->post_type == "slidervilla")
					$html .= '<span class="editSlide"></span>';
				else 
					$html .= '<a href="'. get_edit_post_link( $post->ID ).'" target="_blank"><span class="editcore"></span></a>';
					$html .= '<a href="" onclick="return confirmDelete()" >
					<span class="delSlide" id="'.$post_id.'" ></span></a>';
				if($post->post_type == "slidervilla") {
					$edtlink = get_edit_post_link($post->ID);
					$html .= '<div class="featured_slideDetails" style="display: none;">
						<div class="fL">
							<span class="imgTitle">
								<input placeholder="Title" title="Enter Image Title" type="text" name="title'.$post_id.'" value="'.$post->post_title.'" />
							</span>
							<span class="imgDesc">
								<textarea placeholder="Description" title="Enter Image Description" rows=3 name="desc'.$post_id.'">'.$desc.'</textarea>
							</span>
						</div>
						<div class="fR">
							<span class="imgLink">
								<input type="text" placeholder="Link to" value="'.$featured_sslider_link.'" name="link'.$post_id.'" />
							</span>
							<a href="'.esc_attr($edtlink).'" target="_blank">'.__('Open Edit Window','akkord-slider').'</a>
						</div> 
					</div>';
				}
				$html .= '</div>
					<strong class="cs-post-title"> ' . $post->post_title . '</strong>
				</div>'; 
				
			}
		}
	}
	    
	if ($count == 0) {
	    $html .= '<div>'.__( 'No posts/pages have been added to the Slider - You can add respective post/page to slider on the Edit screen for that Post/Page', 'featured-slider' ).'</div>';
	}
	$html .= '<input type="hidden" name="slider_posts" />';
	if ($count) { $html .= '<input type="submit" name="update_slides" class="btn_save nt-img" value="Save" style="clear: left;" />';}
	echo $html;
	die();
}
function featured_shfb_album(){
	check_ajax_referer( 'featured-slider-nonce', 'featured_slider_pg' );
	$page_url = isset($_POST['page_url']) ? $_POST['page_url'] : '';
	$page = isset($_POST['page']) ? $_POST['page'] : '';
	$html = '';
	if($page_url != '') { 
		$gfeatured_slider = get_option('featured_slider_global_options');
		// Facebook Slider Key
		$fbkey = isset($gfeatured_slider['fb_app_key'])?$gfeatured_slider['fb_app_key']:'';
		$page_url_data = "https://graph.facebook.com/v2.2/?id=".$page_url."&field=id&access_token=$fbkey";
		$json_source = @file_get_contents($page_url_data);
		$fb_page = json_decode($json_source);
		if(isset($fb_page->id)) {
			$fb_page_id = $fb_page->id;
			//fetch list of albums
			if(preg_match("/https/",$fb_page_id) == 0 || preg_match("/http/",$fb_page_id) == 0) {
				$fb_album_data = "https://graph.facebook.com/v2.2/?id=".$fb_page_id."&fields=albums.limit(8)&access_token=$fbkey";
				$json_source_album = @file_get_contents($fb_album_data);
				$fb_page_album = json_decode($json_source_album);
				if(isset($fb_page_album->albums->data[0])) {
					// fetches album id's & names
					if($page == "create_new") $html .= '<label class="featured-form-label">'.__('Albums','featured-slider').'</label>';
					elseif($page == "quicktag") $html .= '<td scope="row"><label for="album">'.__('Albums','svslider').'</label></td>';
					else $html .= '<th scope="row">'.__('Albums','featured-slider').'</th>';
					$fb_album_id = $fb_page_album->albums->data[0]->id;
					if($page != "create_new") $html .= '<td>';
					if($page == "quicktag") $html .= '<div class="styled-select"><select name="album" >';
					else $html .= '<select name="fb-album" class="featured-form-input" >';
					$count = count($fb_page_album->albums->data);
					if($count > 8 ) $count = 8;
			 		for($j=0;$j<$count;$j++) {
						$selected = '';
						$fbalbum_id = $fb_page_album->albums->data[$j]->id;
						if($fb_album_id==$fbalbum_id) $selected='selected';
						$fb_album_name = $fb_page_album->albums->data[$j]->name;
						$html .= '<option value="'.$fbalbum_id.'" '.$selected.' >'.$fb_album_name.'</option>';
					}
					$html .= '</select>';
				} else {
					$html .= __('Please enter correct url','featured-slider');
				}
			} else {
				$html .= __('Please Enter correct url','featured-slider');
			}
			if($page == "quicktag") $html .= '</div>';
			if($page != "create_new") $html .= '</td>';
		}
	}
	echo $html;
	die();
}
function featured_add_form() {
	check_ajax_referer( 'featured-add-slides-nonce', 'add_slides' );
	global $wpdb; // this is how you get access to the database
	$slider_id = intval( $_POST['slider_id'] );
	$html = '<form method="post" name="add_new_slide" id="add_new_slide" class="add-new-slide" >';
	$html .= '	<div class="featured-slide">';
	$html .= '	<div class="featured-slide-content">';
	$html .= '	<div class="featured-form-row">';
	$html .= '		<label class="featured-form-label">'.__('Title','featured-slider').'</label>';
	$html .= '		<input type="text" name="slide_title" class="featured-form-input slide_title" />';
	$html .= '	</div>';
	$html .= '	<div class="featured-form-row">';
	$html .= '		<label class="featured-form-label">'.__('Image','featured-slider').'</label>';
	$html .= '		<input type="text" name="slide_image" class="featured-form-input slide_image" value="" size="50" /> &nbsp; <input  class="featured_upload_button" type="button" value="'.__('Upload','featured-slider').'" />';
	$html .= '	</div>';
	$html .= '	<div class="featured-form-row">';
	$html .= '		<label class="featured-form-label">'.__('Slide Url','featured-slider').'</label>';
	$html .= '		<input type="text" name="slide_url" class="featured-form-input slide_url" />';
	$html .= '	</div>';
	$html .= '	<div class="featured-form-lrow">';
	$html .= '		<label class="featured-form-label">'.__('Description','featured-slider').'</label>';
	$html .= '		<textarea name="slide_desc" class="featured-form-input txtarea slide_desc" /> </textarea>';
	$html .= '	</div>';
	$html .= '	</div>';
	$html .= '	</div>';
	$html .= '	<div class="featured-form-row">';
	$gfeatured_slider = get_option('featured_slider_global_options');
	$custom_post = isset($gfeatured_slider['custom_post'])?$gfeatured_slider['custom_post']:'0';
	if($custom_post == 0 ) $custom_post = post_type_exists('slidervilla');
	$html .= "<input type='hidden' name='custom_post' value='".$custom_post."' />";
	
	$html .= '		<input type="hidden" name="sliderid" value="'. $slider_id.'">';
	$html .= '		<input type="button" name="add_more" class="add_more" value="'.__('Add More','featured-slider').'" /> ';
	$html .= '		<input type="submit" class="btn_save btn-insert" name="insert" value="'.__('Insert','featured-slider').'" />';
	$html .= '<div id="featured-error-msg" class="featured-error-msg"></div>';
	$html .= '	</div>';
	$html .= '</form>';
	echo $html;
	die(); // this is required to terminate immediately and return a proper response
}
function featured_show_posts() {
	check_ajax_referer( 'featured-add-slides-nonce', 'add_slides' );
	global $paged,$wpdb,$post; 
	$pages = '';
	$paged = isset($_POST['paged'])?$_POST['paged']:'';
	$post_type = isset($_POST['post_type'])?$_POST['post_type']:'';
	$sliderid = isset($_POST['sliderid'])?$_POST['sliderid']:'';
	$custom = isset($_POST['custom']) ? $_POST['custom'] : '';
	$range = 10;
	$html = '';
	if($custom == true) {
		$args = array(
			'_builtin' => false
		);
		$post_types = get_post_types( $args, 'names' ); 
		$html .= '	<div class="featured-form-row">';
		$html .= '		<label class="featured-form-label">'.__('Post Type','featured-slider').'</label>';
		$html .= '<select name="post_type" class="featured-form-input sel_post_type" >';
		foreach ( $post_types as $cpost_type ) {
			if($cpost_type == $post_type) $selected = 'selected'; else $selected = '';
		   $html .= '<option value="'.$cpost_type.'" '.$selected.'>' . $cpost_type . '</option>';
		}
		$html .= '</select>';
		$html .= '</div>';
	}
	$showitems = ($range * 2)+1; 
	if(empty($paged)) $paged = 1;
	if($post_type == 'attachment' ) {
		$args = array(
			//'post_parent' => $post->ID, // Get data from the current post
			'post_type' => 'attachment', // Only bring back attachments
			'post_status' => 'inherit',
			'posts_per_page'=>8,	
		        'post_mime_type' => 'image',
			'paged'=>$paged   
		);
	} else {
		$args = array(
			'post_type' => $post_type,
			'posts_per_page'=>10,	
			'post_status'   => 'publish',
			'paged'=>$paged
		);
	}
	$the_query = new WP_Query( $args );
	$i=0;
	// The Loop
	if ( $the_query->have_posts() ) {
		$html .= '<div style="margin-left: 20px;" >';
		$html .= '<form name="eb-wp-posts" id="eb-wp-posts" method="post" >';
		$html .= '<table class="wp-list-table widefat sliders" >';
		if($post_type == 'attachment' ) {
			$html .= '<col width="20%">
			<col width="40%">
			<col width="40%">
			<thead>
			<tr>
				<th class="sliderid-column">ID</th>
				<th class="slidername-column">Name</th>
				<th class="slidername-column">Attachment</th>
			</tr>
			</thead>';
		} else {
			$html .= '<col width="20%">
				<col width="80%">
				<thead>
				<tr>
					<th class="sliderid-column">'.__('ID','featured-slider').'</th>
					<th class="slidername-column">'.__('Name','featured-slider').'</th>
				</tr>
				</thead>';
		}
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$i++;
			$html .= '<tr>';
			$html .= '<td><input type="checkbox" name="post_id[]" value="'.get_the_ID().'"></td>';
			$html .= '<td>' . get_the_title() . '</td>';
			if($post_type == 'attachment' ) {
				$html .= '<td> <img src="'. wp_get_attachment_url( ).'" width="50" height="30" /> </td>';
			}
			$html .= '</tr>';
		}
		$html .= '</table>';
		if($pages == '') {
			$pages = $the_query->max_num_pages;
			if(!$pages) {
				$pages = 1;
			}
		}  

		if(1 != $pages)
		{
			if($paged > 1 ) $prev = ($paged - 1); else $prev = 1;
			$html .= "<div class=\"eb-cs-pagination\"><span>".__('Page','featured-slider')." ".$paged." ".__('of','featured-slider')." ".$pages."</span>";
			$html .= "<a id='1' class='pageclk' >&laquo; ".__('First','featured-slider')."</a>";
			$html .= "<a id='".$prev."' class='pageclk' >&lsaquo; ".__('Previous','featured-slider')."</a>";

			for ($i=1; $i <= $pages; $i++) {
				if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {
					$html .= ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a id=\"$i\" class=\"inactive pageclk\">".$i."</a>";
				}
			}
			$html .= "<a id=\"".($paged + 1) ."\" class='pageclk' >".__('Next','featured-slider')." &rsaquo;</a>"; 
			$html .= "<a id='".$pages."' class='pageclk' >".__('Last','featured-slider')." &raquo;</a>";
			$html .= "</div>\n";
		}
		$html .= "<input type='submit' name='add_posts' value='".__('Insert','featured-slider')."' class='btn_save add_posts' />\n";
		$html .= '<input type="hidden" name="sliderid" value="'. $sliderid.'">';
		$html .= '<input type="hidden" name="post_type" class="post_type" value="'. $post_type.'">';
		$html .= '</form>';
		$html .= "</div>\n";
		echo $html;
		/* Restore original Post Data */
		wp_reset_postdata();
	} else {
		echo "no posts found";
	}
	die();
}
function featured_show_post_type() {
	check_ajax_referer( 'featured-add-slides-nonce', 'add_slides' );
	$html = '';
	$args = array(
		'_builtin' => false
	);
	$post_types = get_post_types( $args, 'names' ); 
	$html .= '	<div class="featured-form-row">';
	$html .= '		<label class="featured-form-label">'.__('Post Type','featured-slider').'</label>';
	$html .= '<select name="post_type" class="featured-form-input sel_post_type" >';
	foreach ( $post_types as $post_type ) {

	   $html .= '<option value="'.$post_type.'">' . $post_type . '</option>';
	}
	$html .= '</select>';
	$html .= '</div>';
	echo $html;
	die();
}
function featured_add_video() {
	check_ajax_referer( 'featured-add-slides-nonce', 'add_slides' );
	$sliderid = $_POST['sliderid'];	
	$type = $_POST['type'];
	$html = '';
	$html .= "<form name='' method='post' id='featured_insert_video'>";
	$html .= '<div class="featured-video-wrap">';
	$html .= '<div class="featured-video-slide">';
	$html .= '<div class="featured-form-row">';
	$html .= '	<label class="featured-form-label">'.__('Title','featured-slider').'</label>';
	$html .= '	<input type="text" name="video_title" id="video_title" class="featured-form-input" value="" />';
	$html .= '</div>';
	$html .= '<div class="featured-form-row">';
	$html .= '	<label class="featured-form-label">'.__('Video Url','featured-slider').'</label>';
	$html .= '	<input type="text" name="video_url" id="video_url" class="featured-form-input" value="" />';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<div class="featured-form-row">';
	$gfeatured_slider = get_option('featured_slider_global_options');
	$custom_post = isset($gfeatured_slider['custom_post'])?$gfeatured_slider['custom_post']:'0';
	if($custom_post == 0 ) $custom_post = post_type_exists('slidervilla');
	$html .= "<input type='hidden' name='custom_post' value='".$custom_post."' />";
	$html .= "<input type='button' name='add_video' value='".__('Add More','featured-slider')."' class='add_video'  />\n";
	$html .= "<input type='submit' name='add_posts' value='".__('Insert','featured-slider')."' class='btn_save featured_insert_video' />\n";
	$html .= '<div id="featured-error-msg" class="featured-error-msg"></div>';
	$html .= '<input type="hidden" name="sliderid" value="'. $sliderid.'">';
	$html .= '<input type="hidden" name="type" value="'. $type.'">';
	$html .= '</div>';
	$html .= "</form>";
	echo $html;
	die();
}
function featured_show_px() {
	check_ajax_referer( 'featured-add-slides-nonce', 'add_slides' );
	$gfeatured_slider = get_option('featured_slider_global_options');
	$pxkey = isset($gfeatured_slider['px_ckey'])?$gfeatured_slider['px_ckey']:'';
	$sliderid = $_POST['sliderid'];
	$feature = isset($_POST['feature']) ? $_POST['feature'] : 'popular';
	$pxuser = isset($_POST['pxuser']) ? $_POST['pxuser'] : '';
	if($feature == "popular") $psle = "selected"; else $psle = "";
	if($feature == "highest_rated") $hsle = "selected"; else $hsle = "";
	if($feature == "upcoming") $usle = "selected"; else $usle = "";
	if($feature == "editors") $esle = "selected"; else $esle = "";
	if($feature == "fresh_today") $ftsle = "selected"; else $ftsle = "";
	if($feature == "fresh_yesterday") $fysle = "selected"; else $fysle = "";
	if($feature == "fresh_week") $fwsle = "selected"; else $fwsle = "";	
	if($feature == "user") $usersle = "selected"; else $usersle = "";	
	if($feature == "user_favorites") $userfvsle = "selected"; else $userfvsle = "";	
	$html = '';
	$html .= "<form name='' method='post' id='px_connect'>";
	$html .= '<div class="featured-form-row">';
	$html .= '	<label class="featured-form-label">'.__('Type','featured-slider').'</label>';
	$html .= '	<select name="feature" class="feature">';
	$html .= '		<option value="popular" '.$psle.'>'.__('Popular','featured-slider').'</option>';
	$html .= '		<option value="highest_rated" '.$hsle.'>'.__('Highest Rated','featured-slider').'</option>';
	$html .= '		<option value="upcoming" '.$usle.'>'.__('Upcoming','featured-slider').'</option>';
	$html .= '		<option value="editors" '.$esle.'>'.__('Editors','featured-slider').'</option>';
	$html .= '		<option value="fresh_today" '.$ftsle.'>'.__('Fresh Today','featured-slider').'</option>';
	$html .= '		<option value="fresh_yesterday" '.$fysle.'>'.__('Fresh Yesterday','featured-slider').'</option>';
	$html .= '		<option value="fresh_week" '.$fwsle.'>'.__('Fresh Week','featured-slider').'</option>';
	$html .= '		<option value="user" '.$usersle.'>'.__('User','featured-slider').'</option>';
	$html .= '		<option value="user_favorites" '.$userfvsle.'>'.__('User favorites','featured-slider').'</option>';
	$html .= '	</select>';
	$html .= "<input type='submit' name='px_connect' value='".__('Connect','featured-slider')."' class='btn_save px_connect' />\n";
	$html .= '</div>';
	if($feature == "user" || $feature == "user_favorites") $style = "display:block;"; else $style = "display:none;";
	$html .= '<div class="featured-form-row pxuser" style="'.$style.'">';
	$html .= '	<label class="featured-form-label">'.__('Name','featured-slider').'</label>';
	$html .= "	<input type='text' name='pxuser' value='".$pxuser."'  />\n";
	$html .= '</div>';

	if($feature != '') { 
		if($feature == "user" || $feature == "user_favorites") {
			$pxurl = "https://api.500px.com/v1/photos?feature=".$feature."&username=".$pxuser."&consumer_key=$pxkey&image_size=4";
		} else {
			$pxurl = "https://api.500px.com/v1/photos?feature=".$feature."&consumer_key=$pxkey&image_size=4";
		}
		$pxjson = @file_get_contents($pxurl);
		if($pxjson == true) {
			$px = json_decode($pxjson);
			if(isset($px->photos)) {
				for($i = 0; $i < count($px->photos); $i++) {
					$limg = isset($px->photos[$i]->image_url)?$px->photos[$i]->image_url:'';
					$html .= '<div class="featured-img-box"><img src="'.$limg.'" /></div>';
				}
			}
		} else {
			$html .= __('Please enter the correct information','featured-slider');
		}
	}
	$html .= '<div style="clear:left;"></div>';
	$html .= '<div class="featured-insert-btn">';
	if($feature != '') { 
		$custom_post = isset($gfeatured_slider['custom_post'])?$gfeatured_slider['custom_post']:'0';
		if($custom_post == 0 ) $custom_post = post_type_exists('slidervilla');
		$html .= "<input type='hidden' name='custom_post' value='".$custom_post."' /><input type='submit' name='px_insert' value='".__('Insert','featured-slider')."' class='btn_save featured_fip_insert' />\n";
		$html .= '<div id="featured-error-msg" class="featured-error-msg"></div>';
	} 
	$html .= '<input type="hidden" name="sliderid" value="'. $sliderid.'">';
	$html .= '</div>';
	$html .= "</form>";
	echo $html;
	die();
}
function featured_show_fb() {
	check_ajax_referer( 'featured-add-slides-nonce', 'add_slides' );
	$sliderid = $_POST['sliderid'];	
	$page_url = isset($_POST['page_url']) ? $_POST['page_url'] : '';
	$albumid = isset($_POST['fb_album']) ? $_POST['fb_album'] : '';
	$html = '';
	$html .= "<form name='' method='post' id='fb_connect'>";
	$html .= '<div class="featured-form-row">';
	$html .= '	<label class="featured-form-label">'.__('Page Url','featured-slider').'</label>';
	$html .= '	<input type="text" name="page_url" id="page_url" class="featured-form-input regular-text" value="'.$page_url.'" />';
	$html .= "<input type='submit' name='fb_connect' value='".__('Connect','featured-slider')."' class='btn_save fb_connect' />\n";
	$html .= '</div>';
	if($page_url != '') { 
		$gfeatured_slider = get_option('featured_slider_global_options');
		// Facebook Slider Key
		$fbkey = isset($gfeatured_slider['fb_app_key'])?$gfeatured_slider['fb_app_key']:'';
		$page_url_data = "https://graph.facebook.com/v2.2/?id=".$page_url."&field=id&access_token=$fbkey";
		$json_source = @file_get_contents($page_url_data);
		$fb_page = json_decode($json_source);
		if(isset($fb_page->id)) {
			$fb_page_id = $fb_page->id;
			//fetch list of albums
			$fb_album_data = "https://graph.facebook.com/v2.2/?id=".$fb_page_id."&fields=albums.limit(8)&access_token=$fbkey";
			$json_source_album = @file_get_contents($fb_album_data);
			$fb_page_album = json_decode($json_source_album);
			if(isset($fb_page_album->albums->data[0])) {
				// fetches album id's & names	
				$html .= '<div class="featured-form-row">';
				$html .= '	<label class="featured-form-label">'.__('Albums','featured-slider').'</label>';
				if($albumid != '' ) $fb_album_id = $albumid; else $fb_album_id = $fb_page_album->albums->data[0]->id;
				$html .= '<select name="fb_album" class="fb_albums" >';
				$count = count($fb_page_album->albums->data);
				if($count > 8 ) $count = 8;
		 		for($j=0;$j<$count;$j++) {
					$selected = '';
					$fbalbum_id = $fb_page_album->albums->data[$j]->id;
					if($fb_album_id==$fbalbum_id) $selected='selected';
					$fb_album_name = $fb_page_album->albums->data[$j]->name;
					$html .= '<option value="'.$fbalbum_id.'" '.$selected.' >'.$fb_album_name.'</option>';
				}
				$html .= '</select>';
				$html .= '</div>';
				$fb_url = "https://graph.facebook.com/v2.2/?id=".$fb_album_id."&fields=id,name,photos&access_token=$fbkey";
				$json_source = @file_get_contents($fb_url);
				$fb = json_decode($json_source);
				$countr = count($fb->photos->data);
				$html .= '<div class="fb-img-wrap">';
				for($i=0;$i<$countr;$i++) {
					$fb_img_src = $fb->photos->data[$i]->images[1]->source;
					$html .= '<div class="featured-img-box"><img src="'.$fb_img_src.'" /></div>';
				}
				$html .= '</div>';
			} else {
				$html .= __('Please enter correct url','featured-slider');
			}
		} else {
			$html .= __('Please enter correct url','featured-slider');
		}
	}
	$html .= '<div style="clear:left;"></div>';
	$html .= '<div class="featured-insert-btn">';
	if($page_url != '') { 
		$custom_post = isset($gfeatured_slider['custom_post'])?$gfeatured_slider['custom_post']:'0';
		if($custom_post == 0 ) $custom_post = post_type_exists('slidervilla');
		$html .= "<input type='hidden' name='custom_post' value='".$custom_post."' />";
		$html .= "<input type='submit' name='featured_fip_insert' value='".__('Insert','featured-slider')."' class='btn_save featured_fip_insert' />\n";
		$html .= '<div id="featured-error-msg" class="featured-error-msg"></div>';
	} 
	$html .= '<input type="hidden" name="sliderid" value="'. $sliderid.'">';
	$html .= '</div>';
	$html .= "</form>";
	echo $html;
	die();
}
function featured_show_flickr() {
	check_ajax_referer( 'featured-add-slides-nonce', 'add_slides' );
	$sliderid = $_POST['sliderid'];
	$method = isset($_POST['method']) ? $_POST['method'] : 'public_photo';
	$id = isset($_POST['id']) ? $_POST['id'] : '';
	$asle = $psle = '';
	if($method == "public_photo") $psle = "selected";
	if($method == "album") $asle = "selected";
	$html = '';
	$html .= "<form name='' method='post' id='flickr_connect'>";
	$html .= '<div class="featured-form-row">';
	$html .= '	<label class="featured-form-label">'.__('Type','featured-slider').'</label>';
	$html .= '	<select name="method" class="method">';
	$html .= '		<option value="public_photo" '.$psle.'>'.__('User','featured-slider').'</option>';
	$html .= '		<option value="album" '.$asle.'>'.__('Album','featured-slider').'</option>';
	$html .= '	</select>';
	$html .= "<input type='submit' name='flickr_connect' value='".__('Connect','featured-slider')."' class='btn_save flickr_connect' />\n";
	$html .= '</div>';
	$html .= '<div class="featured-form-row id" >';
	$html .= '	<label class="featured-form-label">'.__('ID','featured-slider').'</label>';
	$html .= "	<input type='text' name='id' value='".$id."'  />\n";
	$html .= '</div>';
	if($id != "") {
		$gfeatured_slider = get_option('featured_slider_global_options');
		// Flickr Slider Key
		$flkey = isset($gfeatured_slider['flickr_app_key'])?$gfeatured_slider['flickr_app_key']:'';
		if($method == 'public_photo') { 
			$flicker_url = "https://api.flickr.com/services/rest/?&method=flickr.people.getPublicPhotos&api_key=$flkey&user_id=".$id."&format=json&nojsoncallback=1";
			$json_source = @file_get_contents($flicker_url);
			$fx = json_decode($json_source);
			if(isset($fx->photos)) {
				$count = count($fx->photos->photo);
				for($i=0;$i<$count;$i++) {
					$id = $fx->photos->photo[$i]->id;
					$owner = $fx->photos->photo[$i]->owner;
					$secret = $fx->photos->photo[$i]->secret;
					$server = $fx->photos->photo[$i]->server;
					$farm = $fx->photos->photo[$i]->farm;
					$html .= '<div class="featured-img-box"><img src="https://farm'.$farm.'.staticflickr.com/'.$server.'/'.$id.'_'.$secret.'_z.jpg" /></div>';
				}
			} else {
				_e('Please enter the correct user','featured-slider');
			}
		} elseif($method == 'album') { 
			$flicker_seturl = "https://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key=$flkey&photoset_id=".$id."&format=json&nojsoncallback=1";
			$json_setsource = @file_get_contents($flicker_seturl);
			$setfx = json_decode($json_setsource);
			if(isset($setfx->photoset)) {
				$count = count($setfx->photoset->photo);
				for($i=0;$i<$count;$i++) {
					$id = $setfx->photoset->photo[$i]->id;
					$owner = isset($setfx->photoset->photo[$i]->owner)?$setfx->photoset->photo[$i]->owner:'';
					//$owner = $setfx->photoset->photo[$i]->owner;
					$secret = $setfx->photoset->photo[$i]->secret;
					$server = $setfx->photoset->photo[$i]->server;
					$farm = $setfx->photoset->photo[$i]->farm;
					$html .= '<div class="featured-img-box"><img src="https://farm'.$farm.'.staticflickr.com/'.$server.'/'.$id.'_'.$secret.'_z.jpg" /></div>';
				}
			} else {
				_e('Please enter the correct album','featured-slider');
			}
		}
	}
	$html .= '<div style="clear:left;"></div>';
	$html .= '<div class="featured-insert-btn">';
	if($id != '') { 
		$custom_post = isset($gfeatured_slider['custom_post'])?$gfeatured_slider['custom_post']:'0';
		if($custom_post == 0 ) $custom_post = post_type_exists('slidervilla');
		$html .= "<input type='hidden' name='custom_post' value='".$custom_post."' />";
		$html .= "<input type='submit' name='px_insert' value='".__('Insert','featured-slider')."' class='btn_save featured_fip_insert' />\n";
		$html .= '<div id="featured-error-msg" class="featured-error-msg"></div>';
	} 
	$html .= '<input type="hidden" name="sliderid" value="'. $sliderid.'">';
	$html .= '</div>';
	$html .= "</form>";
	echo $html;
	die();
}
function featured_show_it() {
	check_ajax_referer( 'featured-add-slides-nonce', 'add_slides' );
	$sliderid = $_POST['sliderid'];
	$uname = isset($_POST['uname']) ? $_POST['uname'] : '';
	$html = '';
	$html .= "<form name='' method='post' id='it_connect'>";
	$html .= '<div class="featured-form-row" >';
	$html .= '	<label class="featured-form-label">'.__('User Name','featured-slider').'</label>';
	$html .= "	<input type='text' name='uname' value='".$uname."'  />\n";
	$html .= "<input type='submit' name='it_connect' value='".__('Connect','featured-slider')."' class='btn_save it_connect' />\n";
	$html .= '</div>';
	
	if($uname != "") {
		$gfeatured_slider = get_option('featured_slider_global_options');
		// Instagram Slider Key
		$igkey = isset($gfeatured_slider['insta_client_id'])?$gfeatured_slider['insta_client_id']:'';
		$insta_id_url = "https://api.instagram.com/v1/users/search?q=".$uname."&client_id=$igkey";
		$json_source_id = @file_get_contents($insta_id_url);
		$insta_id_data = json_decode($json_source_id);
		if(isset($insta_id_data->data[0])) {
			$insta_id = $insta_id_data->data[0]->id;
			$insta_media_url="https://api.instagram.com/v1/users/".$insta_id."/media/recent/?client_id=$igkey";
			$json_source = @file_get_contents($insta_media_url);
			$insta_media_data = json_decode($json_source);
			$count = count($insta_media_data->data);
			for($i=0;$i<$count;$i++) {
				$img_src = $insta_media_data->data[$i]->images->standard_resolution->url;
				$html .= '<div class="featured-img-box"><img src="'.$img_src.'" /></div>';	
			}
		} else {
			$html .= __('Please enter the correct information','featured-slider');
		}
	}
	$html .= '<div style="clear:left;"></div>';
	$html .= '<div class="featured-insert-btn">';
	if($uname != '') { 
		$custom_post = isset($gfeatured_slider['custom_post'])?$gfeatured_slider['custom_post']:'0';
		if($custom_post == 0 ) $custom_post = post_type_exists('slidervilla');
		$html .= "<input type='hidden' name='custom_post' value='".$custom_post."' />";
		$html .= "<input type='submit' name='px_insert' value='".__('Insert','featured-slider')."' class='btn_save featured_fip_insert' />\n";
		$html .= '<div id="featured-error-msg" class="featured-error-msg"></div>';
	} 
	$html .= '<input type="hidden" name="sliderid" value="'. $sliderid.'">';
	$html .= '</div>';
	$html .= "</form>";
	echo $html;
	die();
}
function featured_fip_insert() {
	check_ajax_referer( 'featured-add-slides-nonce', 'add_slides' );
	$featured_slider = get_option('featured_slider_options');
	global $wpdb, $table_prefix, $post;
	$table_name = $table_prefix.FEATURED_SLIDER_TABLE;
	$slider_id = $_POST['sliderid'];
	$img_url = $_POST['img_url'];
	$type = $_POST['type'];
	if($type == "fb_connect") { $ftitle = 'Facebook Image'; $stype ='5'; }
	elseif($type == "px_connect") { $ftitle = '500px Image'; $stype ='10'; }
	elseif($type == "flickr_connect") { $ftitle = 'Flickr Image'; $stype ='6'; }
	elseif($type == "it_connect") { $ftitle = 'Instagram Image'; $stype ='7'; }
	else $ftitle = 'Default Image';
	for($i = 0; $i < count($img_url); $i++) {
		$slide_desc = '';
		$title = $ftitle.$i;
		$url = $img_url[$i];
		$cptpost_args = array(
			'post_title'    => $title,
			'post_content'  => $slide_desc,
			'post_status'   => 'publish',
			'post_type' => 'slidervilla'
		);
		// insert the post into the database
		$cpt_id = @wp_insert_post($cptpost_args);
		$dt = date('Y-m-d H:i:s');
		$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$cpt_id', '$dt', '$slider_id')";
		$wpdb->query($sql);
		if($url != '') {
			$thumbnail_key = $featured_slider['img_pick'][1];
			update_post_meta($cpt_id,$thumbnail_key,$url);
			update_post_meta($cpt_id,'_featured_slide_type',$stype);
		}
	}
	die();
}
function featured_insert_video() {
	check_ajax_referer( 'featured-add-slides-nonce', 'add_slides' );
	global $wpdb, $table_prefix, $post;
	$table_name = $table_prefix.FEATURED_SLIDER_TABLE;
	$type = $_POST['type'];
	$slider_id = $_POST['sliderid'];
	$video_url = $_POST['video_url'];
	$video_title = (isset($_POST['video_title']) && $_POST['video_title'] != '') ? $_POST['video_title'] : '';
	for($i = 0; $i < count($video_url); $i++) {
		$slide_desc = '';
		$title = $video_title[$i];
		$url = $video_url[$i];
		$cptpost_args = array(
			'post_title'    => $title,
			'post_content'  => $slide_desc,
			'post_status'   => 'publish',
			'post_type' => 'slidervilla'
		);
		// insert the post into the database
		$cpt_id = @wp_insert_post($cptpost_args);
		$dt = date('Y-m-d H:i:s');
		$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$cpt_id', '$dt', '$slider_id')";
		$wpdb->query($sql);
		if($url != '') {
			if($type == 'vimeo') {
				$size = 'thumbnail_small';
				$pieces = explode("/", $url);
				$video_id = end($pieces);
				if(get_transient('vimeo_' . $size . '_' . $video_id)) {
					$thumbnail = get_transient('vimeo_' . $size . '_' . $video_id);
				} else {
					$json = json_decode(@file_get_contents( "http://vimeo.com/api/v2/video/" . $video_id . ".json" ));
					$thumbnail = $json[0]->$size;
					set_transient('vimeo_' . $size . '_' . $id, $thumbnail, 2629743);
				}
				$video_shortcode = '<iframe src="http://player.vimeo.com/video/'.$video_id.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
				$stype = '9';
			} else {
				$video_id = substr($url, strpos($url, "v=") + 2);
				$thumbnail = "http://img.youtube.com/vi/".$video_id."/default.jpg";
				$video_shortcode = "[video src='".$url."' pauseOtherPlayers='true']";
				$stype = '8';
			}
			$featured_slider = get_option('featured_slider_options');
			$thumbnail_key = $featured_slider['img_pick'][1];
			update_post_meta($cpt_id, $thumbnail_key,$thumbnail);	
			update_post_meta($cpt_id,'_featured_embed_shortcode',$video_shortcode);
			update_post_meta($cpt_id, '_featured_disable_image', 1 );
			update_post_meta($cpt_id, '_featured_slide_type', $stype);
		}
	}
}
function featured_insert_slide() { 
	check_ajax_referer( 'featured-add-slides-nonce', 'add_slides' );
	$featured_slider = get_option('featured_slider_options');
	global $wpdb, $table_prefix, $post;
	$table_name = $table_prefix.FEATURED_SLIDER_TABLE;
	$slider_id = $_POST['sliderid'];
	$aimage_url = $_POST['slide_image'];
	$aslide_url = $_POST['slide_url'];
	$aslide_title = (isset($_POST['slide_title']) && $_POST['slide_title'] != '') ? $_POST['slide_title'] : '';
	$aslide_desc = (isset($_POST['slide_desc']) && $_POST['slide_desc'] != '') ? $_POST['slide_desc'] : '';
	for($i =0; $i < count($aslide_title); $i++) {
		$slide_title = $aslide_title[$i];
		$slide_desc = $aslide_desc[$i];
		$slide_url = $aslide_url[$i];
		$image_url = $aimage_url[$i];
		$cptpost_args = array(
		'post_title'    => $slide_title,
		'post_content'  => $slide_desc,
		'post_status'   => 'publish',
		'post_type' => 'slidervilla'
		);
		$stype = '1';
		// insert the post into the database
		$cpt_id = @wp_insert_post($cptpost_args);
		update_post_meta($cpt_id, 'featured_slide_redirect_url', $slide_url);
		update_post_meta($cpt_id, '_featured_slide_type', $stype);
		
		$thumbnail_key = $featured_slider['img_pick'][1];
		update_post_meta($cpt_id, $thumbnail_key, $image_url);
		
		$dt = date('Y-m-d H:i:s');
		$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$cpt_id', '$dt', '$slider_id')";
		$wpdb->query($sql);	
	}
	die();
}
function featured_insert_posts() { 
	check_ajax_referer( 'featured-add-slides-nonce', 'add_slides' );
	global $wpdb, $table_prefix, $post;
	$table_name = $table_prefix.FEATURED_SLIDER_TABLE;
	$slider_id = $_POST['sliderid'];
	$dt = date('Y-m-d H:i:s');
	$count = count($_POST['post_id']);
	$values = '';
	for($i = 0; $i < $count; $i++ ) {
		$id = $_POST['post_id'][$i];
		if(!featured_slider($id,$slider_id)) {
			if($i == $count-1)
				$values .= "('$id', '$dt', '$slider_id')";
			else
				$values .= "('$id', '$dt', '$slider_id'),";
		}
		update_post_meta($id, '_featured_slide_type', '1');
	}
	$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES $values";
	$wpdb->query($sql);
	die();
}

function featured_change_type() {
	check_ajax_referer('featured-eb-settings-nonce','eb_settings_nonce');
	$id = isset($_POST['slider_id']) ? $_POST['slider_id'] : '';
	$sname = isset($_POST['sname']) ? $_POST['sname'] : '';
	$gfeatured_slider = get_option('featured_slider_global_options');
	// YouTube Slider Key Check
	$youtube_key = isset($gfeatured_slider['youtube_app_id'])?$gfeatured_slider['youtube_app_id']:'';
	// Facebook Slider Key Check
	$fbkey = isset($gfeatured_slider['fb_app_key'])?$gfeatured_slider['fb_app_key']:'';
	// Instagram Slider Key Check
	$igkey = isset($gfeatured_slider['insta_client_id'])?$gfeatured_slider['insta_client_id']:'';
	// Flickr Slider Key Check
	$flkey = isset($gfeatured_slider['flickr_app_key'])?$gfeatured_slider['flickr_app_key']:'';
	// 500px Slider Key Check
	$pxkey = isset($gfeatured_slider['px_ckey'])?$gfeatured_slider['px_ckey']:'';
	$html = ''; 
	$html .= '<form method="post" name="featured-update-type" id="featured-update-type" >
			<div class="featured-head">
				<span class="featured-step-title">Select Slider Type</span>
			</div>';
	$html .= '	<div class="featured-col featured-vert-line">
				<div class="featured-col-head">'.__('WordPress Core','featured-slider').'</div>
				<div class="featured-col-row">
					<input type="radio" name="slider_type" class="updt-sldr-type"  value="2" ><i class="fa fa-align-justify"></i><span class="featured-icon-title">'.__('Recent Posts Slider','featured-slider').'</span>
			</div>
			<div class="featured-col-row">
				<input type="radio" name="slider_type" class="updt-sldr-type"  value="1" ><i class="fa fa-align-justify"></i><span class="featured-icon-title">'.__('Category Slider','featured-slider').'</span>
			</div>
			<div class="featured-col-row">
				<input type="radio" name="slider_type" class="updt-sldr-type"  value="0" ><i class="fa fa-align-justify"></i><span class="featured-icon-title">'.__('Custom Slider','featured-slider').'</span>
			</div>
			<div class="featured-col-row">
				<input type="radio" name="slider_type" class="updt-sldr-type"  value="7" ><i class="fa fa-align-justify"></i><span class="featured-icon-title">'.__('Taxonomy Slider','featured-slider').'</span>
			</div>
			<div class="featured-col-row">
				<input type="radio" name="slider_type" class="updt-sldr-type"  value="17" ><i class="fa fa-align-justify"></i><span class="featured-icon-title">'.__('Image Slider','featured-slider').'</span>
			</div>
			<div class="featured-col-head second">'.__('Social Network','featured-slider').'</div>
			<div class="featured-col-row">';
				if($fbkey == '') { $fbclass="no_key"; } else { $fbclass=""; } 
	$html .= '			<input type="radio" name="slider_type" class="updt-sldr-type '.$fbclass.'"  value="14" ><i class="fa fa-facebook-square"></i><span class="featured-icon-title">'.__('Facebook Album Slider','featured-slider').'</span>';
				if($fbkey == '') { 
	$html .= '			<i class="fa fa-lock" title="'.__('Add Facebook App Key on Global Settings','featured-slider').'"></i>'; 
				}
	$html .= '		</div>
			<div class="featured-col-row">';
				if($igkey == '') { $igclass="no_key"; } else { $igclass=""; } 
	$html .= '			<input type="radio" name="slider_type" class="updt-sldr-type '.$igclass.'"  value="15" ><i class="fa fa-instagram"></i><span class="featured-icon-title">'.__('Instagram Slider','featured-slider').'</span>';
				if($igkey == '') { 
	$html .= '			<i class="fa fa-lock" title="'.__('Add Instagram Client Id on Global Settings','featured-slider').'"></i>';
				}
	$html .= '		</div>
			<div class="featured-col-row">';
				if($flkey == '') { $flclass="no_key"; } else { $flclass=""; }
	$html .= '			<input type="radio" name="slider_type" class="updt-sldr-type '.$flclass.'"  value="16" ><i class="fa fa-flickr"></i><span class="featured-icon-title">'.__('Flickr Slider','featured-slider').'</span>';
				if($flkey == '') { 
	$html .= '			<i class="fa fa-lock" title="'.__('Add Flickr API Key on Global Settings','featured-slider').'"></i>';
				}
	$html .= '		</div>
			<div class="featured-col-row">';
				if($pxkey == '') { $pxclass="no_key"; } else { $pxclass=""; } 
	$html .= '			<input type="radio" name="slider_type" class="updt-sldr-type '.$pxclass.'"  value="18" ><img src="'. featured_slider_plugin_url( 'images/500px.png' ).'" width="13" height="14" /><span class="featured-icon-title">'.__('500px Slider','featured-slider').'</span>';
				if($pxkey == '') { 
	$html .= '			<i class="fa fa-lock" title="'.__('Add 500px Consumer Key on Global Settings','featured-slider').'"></i>';
				} 
	$html .= '		</div>
			<div class="featured-col-head second">'.__('Videos','featured-slider').'</div>
			<div class="featured-col-row">';
				if($youtube_key == '') { $youtubeclass="no_key"; } else { $youtubeclass=""; } 
	$html .= '			<input type="radio" name="slider_type" class="updt-sldr-type '.$youtubeclass.'"  value="11" ><i class="fa fa-youtube"></i><span class="featured-icon-title">'.__('YouTube Playlist Slider','featured-slider').' </span>';
				if($youtube_key == '') { 
	$html .= '			<i class="fa fa-lock" title="'.__('Add Youtube API Key on Global Settings','featured-slider').'"></i>';
				} 
	$html .= '		</div>
			<div class="featured-col-row">';
				if($youtube_key == '') { $youtubeclass="no_key"; } else { $youtubeclass=""; } 
	$html .= '			<input type="radio" name="slider_type" class="updt-sldr-type '.$youtubeclass.'"  value="12" ><i class="fa fa-youtube"></i><span class="featured-icon-title">'.__('YouTube Search Slider','featured-slider').'</span>';
				if($youtube_key == '') { 
	$html .= '			<i class="fa fa-lock" title="'.__('Add Youtube API Key on Global Settings','featured-slider').'"></i>';
				} 
	$html .= '	</div>
			<div class="featured-col-row">
				<input type="radio" name="slider_type" class="updt-sldr-type"  value="13" ><i class="fa fa-vimeo-square"></i></span><span class="featured-icon-title">'.__('Vimeo Slider','featured-slider').'</span>
			</div>

		</div>
		<div class="featured-col">';
			if(!is_plugin_active('nextgen-gallery/nggallery.php')) { 
				$nggclass="no_key"; 
			} else { $nggclass = ""; } 
	$html .= '	<div class="featured-col-head">'.__('Gallary Integration','featured-slider').'</div>
			<div class="featured-col-row">
				<input type="radio" name="slider_type" class="updt-sldr-type '.$nggclass.'"  value="10" ><i class="fa fa-picture-o"></i><span class="featured-icon-title">'.__('NextGen Gallery Slider','featured-slider').'</span>';
				if($nggclass != '') { 
	$html .= '			<i class="fa fa-lock" title="'.__('Install NextGen Gallery Plugin to use it','featured-slider').'"></i>';
				}
	$html .= '		</div>

			<div class="featured-col-head second">'.__('Ecommerce','featured-slider').'</div>';
			if(!is_plugin_active('woocommerce/woocommerce.php') ) { 
				$wooclass = "no_key" ;
				} else {
					$wooclass = "";
				} 
	$html .= '		<div class="featured-col-row">
					<input type="radio" name="slider_type" class="updt-sldr-type '.$wooclass.'"  value="3" ><i class="fa fa-shopping-cart"></i><span class="featured-icon-title">'.__('WooCommerce Slider','featured-slider').'</span>';
				if($wooclass != '') { 
	$html .= '			 <i class="fa fa-lock" title="'.__('Install WooCommerce Plugin to use it','featured-slider').'"></i>'; 
				}
	$html .= '			</div>';
			 if(!is_plugin_active('wp-e-commerce/wp-shopping-cart.php') ) { 
					$ecomclass = "no_key" ;
				} else {
					$ecomclass = "";
				} 
	$html .= '			<div class="featured-col-row">
					<input type="radio" name="slider_type" class="updt-sldr-type '.$ecomclass.'"  value="4" ><i class="fa fa-shopping-cart"></i><span class="featured-icon-title">'.__('WP Ecommerce Slider','featured-slider').'</span>';
				if($ecomclass != '') { 
	$html .= '			<i class="fa fa-lock" title="'.__('Install WP Ecommerce Plugin to use it','featured-slider').'"></i>'; 
				}
	$html .= '			</div>
				<div class="featured-col-head second">'.__('Events','featured-slider').'</div>';
				if(!is_plugin_active('events-manager/events-manager.php') ) { 
					$emanclass = "no_key" ;
				} else {
					$emanclass = "";
				}
	$html .= '			<div class="featured-col-row">
					<input type="radio" name="slider_type" class="updt-sldr-type '.$emanclass.'"  value="5" ><i class="fa fa-align-justify"></i><span class="featured-icon-title">'.__('Event Manager','featured-slider').'</span>';
				if($emanclass != '') { 
	$html .= '			<i class="fa fa-lock" title="'.__('Install Event Manager Plugin to use it','featured-slider').'"></i>';
				}
	$html .= '			</div>';
				if(!is_plugin_active('the-events-calendar/the-events-calendar.php') ) { 
					$ecalclass = "no_key" ;
				} else {
					$ecalclass = "";
				}
	$html .= '		<div class="featured-col-row">
					<input type="radio" name="slider_type" class="updt-sldr-type '.$emanclass.'"  value="6" ><i class="fa fa-align-justify"></i><span class="featured-icon-title">'.__('Event Calender','featured-slider').'</span>';
				if($ecalclass != '') { 
	$html .= '			<i class="fa fa-lock" title="'.__('Install Event Calender Plugin to use it','featured-slider').'"></i>'; 
				} 
	$html .= '			</div>
				<div class="featured-col-head second">'.__('Miscellaneous','featured-slider').'</div>
				<div class="featured-col-row">
				<input type="radio" name="slider_type" class="updt-sldr-type"  value="9" ><i class="fa fa-align-justify"></i><span class="featured-icon-title">'.__('Post Attachments Slider','featured-slider').'</span>
			</div>
			<div class="featured-col-row">
				<input type="radio" name="slider_type" class="updt-sldr-type"  value="8" ><i class="fa fa-rss-square"></i><span class="featured-icon-title">'.__('RSS feed Slider','featured-slider').'</span>
			</div>
		</div>
		<div class="featured-col-row">
			<input type="hidden" name="id" value="'.$id.'"  /> <input type="hidden" name="sname" value="'.$sname.'"  />
		</div>
	</form>';
	echo $html;
	die();
}

function featured_show_params () {
	check_ajax_referer('featured-eb-settings-nonce','eb_settings_nonce');
	if (isset ($_POST['slider_type'])) {
		$slider_type = isset($_POST['slider_type'])?$_POST['slider_type']:'2';
		$id = isset($_POST['id'])?$_POST['id']:'0';
		$html = '';
		$html .= '<div class="featured-step2">
			<form method="post" name="featured-create-new-step2" id="featured-update-step2" class="featured-step2-form featured-validate" >';
			if($slider_type == 2) { 
		$html .= '	<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="2" checked ><i class="fa fa-align-justify"></i><span class="featured-icon-title">'.__('Recent Posts Slider','featured-slider').'</span>
				</div>';
			} elseif($slider_type == 1) { 
		$html .= '	<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="1" checked ><i class="fa fa-align-justify"></i><span class="featured-icon-title">'.__('Category Slider','featured-slider').'</span>
				</div>';
			} elseif($slider_type == 0) { 
		$html .= '	<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="0" checked ><i class="fa fa-align-justify"></i><span class="featured-icon-title">'.__('Custom Slider','featured-slider').'</span>
				</div>';
			} elseif($slider_type == 3) { 
		$html .= '	<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="3" checked ><i class="fa fa-shopping-cart"></i></span><span class="featured-icon-title">'.__('WooCommerce Slider','featured-slider').'</span>
				</div>';
			} elseif($slider_type == 4) { 
		$html .= '	<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="4" checked ><i class="fa fa-shopping-cart"></i></span><span class="featured-icon-title">'.__('ECommerce Slider','featured-slider').'</span>
				</div>';
			} elseif($slider_type == 5) { 
		$html .= '	<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="5" checked ><i class="fa fa-align-justify"></i><span class="featured-icon-title">'.__('Event Manager Slider','featured-slider').'</span>
				</div>';
			} elseif($slider_type == 6) {
		$html .= '	<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="6" checked ><i class="fa fa-align-justify"></i><span class="featured-icon-title">'.__('Event Calender Slider','featured-slider').'</span>
				</div>';
			} elseif($slider_type == 7) { 
		$html .= '	<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="7" checked ><i class="fa fa-align-justify"></i><span class="featured-icon-title">'.__('Taxonomy Slider','featured-slider').'</span>
				</div>';
			} elseif($slider_type == 17) { 
		$html .= '	<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="17" checked ><i class="fa fa-align-justify"></i><span class="featured-icon-title">'.__('Image Slider','featured-slider').'</span>
				</div>';
			} elseif($slider_type == 8) { 
		$html .= '		<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="8" checked ><i class="fa fa-rss-square"></i><span class="featured-icon-title">'.__('RSS Feed Slider','featured-slider').'</span>
				</div>';
			}  elseif($slider_type == 9) { 
		$html .= '	<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="9" checked ><i class="fa fa-align-justify"></i><span class="featured-icon-title">'.__('Post Attachment Slider','featured-slider').'</span>
				</div>';
			} elseif($slider_type == 10) { 
		$html .= '	<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="10" checked ><i class="fa fa-picture-o"></i><span class="featured-icon-title">'.__('NextGen Gallery Slider','featured-slider').'</span>
				</div>';
			} elseif($slider_type == 11) { 
		$html .= '	<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="11" checked ><i class="fa fa-youtube"></i><span class="featured-icon-title">'.__('Youtube Playlist Slider','featured-slider').'</span>
				</div>';
			} elseif($slider_type == 12) {
		$html .= '	<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="12" checked ><i class="fa fa-youtube"></i><span class="featured-icon-title">'.__('YouTube Search Slider','featured-slider').'</span>
				</div>';
		} elseif($slider_type == 13) { 
		$html .= '		<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="13" checked ><i class="fa fa-youtube"></i><span class="featured-icon-title">'.__('Vimeo Slider','featured-slider').'</span>
				</div>';
		} elseif($slider_type == 14) { 
		$html .= '	<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="14" checked ><i class="fa fa-youtube"></i><span class="featured-icon-title">'.__('Facebook Album Slider','featured-slider').'</span>
				</div>';
		} elseif($slider_type == 15) { 
		$html .= '	<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="15" checked ><i class="fa fa-instagram"></i><span class="featured-icon-title">'.__('Instagram Slider','featured-slider').'</span>
				</div>';
		} elseif($slider_type == 16) { 
		$html .= '	<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="16" checked ><i class="fa fa-flickr"></i><span class="featured-icon-title">'.__('Flickr Slider','featured-slider').'</span>
				</div>';
		} elseif($slider_type == 18) {
		$html .= '	<div class="featured-col-row">
					<input type="radio" name="update-slider-type" value="18" checked ><img src="'. featured_slider_plugin_url( 'images/500px.png' ).'" width="13" height="14" /><span class="featured-icon-title">'.__('500px Slider','featured-slider').'</span>
				</div>';
		}
		$sname = isset($_POST['sname'])?$_POST['sname']:'';
		$html .= '<div class="featured-form-row"> 	
				<label>'.__('Slider Name','featured-slider').'</label>			
				 <input type="text" name="new_slider_name" id="update_slider_name" value="'.$sname.'" class="featured-form-input" /> 
			</div>
			<div class="featured-form-row">
				<label>'.__('Offset','featured-slider').'</label>
				<input type="number" name="offset" value="0" class="featured-form-input small" />
			</div>';
			if($slider_type == 1) { 
				//category Slider Param
				$categories = get_categories();
				$scat_html='<option value="" selected >Select the Category</option>';
				foreach ($categories as $category) { 
					 if( isset($param_array['catg_slug']) && $category->slug==$param_array['catg_slug']){$selected = 'selected';} else{$selected='';}
					 $scat_html =$scat_html.'<option value="'.$category->slug.'" '.$selected.'>'.$category->name.'</option>';
				}
		$html .= '	<div class="featured-form-row">
					<label>'.__('Category','featured-slider').'</label>
					<select name="catg_slug" id="featured_slider_catslug" class="featured-form-input" >'.$scat_html.'</select>
				</div>';
			} elseif($slider_type == 3 ) { 
				if( is_plugin_active('woocommerce/woocommerce.php') ) {
					$wooterms = get_terms('product_cat');
					$woocat_html='<option value="" selected >Select the Category</option>';
					foreach( $wooterms as $woocategory) {
						if( isset($param_array['woo-catg']) && $woocategory->slug==$param_array['woo-catg'] ){$selected = 'selected';} else{$selected='';}
						$woocat_html =$woocat_html.'<option value="'.$woocategory->slug.'" '.$selected.'>'. $woocategory->name .'</option>';
					}
				} 
		$html .= '	<div class="featured-form-row">
				<label>'.__('Select Slider Type','featured-slider').'</label>
				<select name="woo_slider_type" id="woo-slider-type" class="featured-form-input featured_woo_type" >
					<option value="recent" >'.__('Recent Product Slider','featured-slider').'</option>
					<option value="upsells" >'.__('Upsells Product Slider','featured-slider').'</option>
					<option value="crosssells" >'.__('Crosssells Product Slider','featured-slider').'</option>
					<option value="external" >'.__('External Product Slider','featured-slider').'</option>
					<option value="grouped" >'.__('Grouped Product Slider','featured-slider').'</option>
				</select>
			</div>
			<div class="featured-form-row woo-product" style="display:none;">
				<label>'.__('Enter Product','featured-slider').'</label>
				<input id="products" class="featured-form-input" >
				<input id="product_id" name="product_id" value="" type="hidden" >
			</div>
			<div class="featured-form-row">
				<label>'.__('Select Category','featured-slider').'</label>
				<select id="featured_slider_woo_catslug" multiple class="featured-multiselect featured-form-input" >'. $woocat_html.'</select>
				<input type="hidden" name="woo-catg"  />
			</div>';
			} elseif($slider_type == 4 ) { 
				if( is_plugin_active('wp-e-commerce/wp-shopping-cart.php') ) {
					$ecomterms = get_terms('wpsc_product_category');
					$ecomcat_html='<option value="" selected >Select the Category</option>';
					foreach( $ecomterms as $ecomcategory) {
						if( isset($param_array['ecom-catg']) && $ecomcategory->slug==$param_array['ecom-catg']){$selected = 'selected';} else{$selected='';}
						$ecomcat_html =$ecomcat_html.'<option value="'.$ecomcategory->slug.'" '.$selected.'>'.$ecomcategory->name.'</option>';
					}
				}
			
			$html .= '<div class="featured-form-row">
					<label>'.__('Select Slider Type','featured-slider').'</label>
					<select name="ecom_slider_type" id="ecom_slider_preview" onchange="catgtype(this.value);"  class="featured-form-input" >
						<option value="0" >'.__('eCom Recent Product Slider','featured-slider').'</option>
						<option value="1" >'.__('eCom Product Category Slider','featured-slider').'</option>
					</select>
				</div>
				<div class="featured-form-row featured_catg" style="display:none;">
					<label>'.__('Select Category','featured-slider').'</label>
					<select id="featured_slider_ecom_catslug" name="ecom-catg" class="featured-form-input" >'. $ecomcat_html.'</select>
				</div>';
			} elseif($slider_type == 5 ) { 
				if( is_plugin_active('events-manager/events-manager.php') ) {
					$eventterms = get_terms('event-categories');
					$eventcat_html='<option value="" selected >All Category</option>';
					foreach( $eventterms as $eventcategory) {
						$eventcat_html =$eventcat_html.'<option value="'.$eventcategory->slug.'" >'.$eventcategory->name.'</option>';
					} 
					$evtags = get_terms("event-tags");
					$evtag_html='<option value="" selected >All Tags</option>';
					foreach( $evtags as $tags) {
						$evtag_html = $evtag_html.'<option value="'.$tags->slug.'">'.$tags->name.'</option>';
					} 
				}
			$html .= '<div class="featured-form-row">
					<label>'.__('Select Slider Scope','featured-slider').'</label>
					<select name="eventm_slider_scope" id="eventm_slider_preview" class="featured-form-input" >
						<option value="future" >'.__('Future Events','featured-slider').'</option>
						<option value="past" >'.__('Past Events','featured-slider').'</option>
						<option value="all" >'.__('Recent Events','featured-slider').'</option>
					</select>
				</div>
				<div class="featured-form-row">
					<label>'.__('Select Category','featured-slider').'</label>
					<select id="featured_slider_event_catslug" multiple class="featured-multiselect featured-form-input" >'.$eventcat_html.'</select>
					<input type="hidden" name="eman-catg"  />
				</div>
				<div class="featured-form-row">
					<label>'.__('Select Tags','featured-slider').'</label>
					<select id="featured_slider_event_tags" multiple class="featured-multiselect featured-form-input" >'.$evtag_html.'</select>
					<input type="hidden" name="eman-tags"  />
				</div>';
			}  elseif($slider_type == 6 ) { 
				if( is_plugin_active('the-events-calendar/the-events-calendar.php') ) { 
					$eventcalterms = get_terms('tribe_events_cat');
					$eventcal_html='<option value="" selected >All Category</option>';
					foreach( $eventcalterms as $eventcalcat) {
						$eventcal_html =$eventcal_html.'<option value="'.$eventcalcat->slug.'" '.$selected.'>'.$eventcalcat->name.'</option>';
					}
					$evcaltags = get_terms("post_tag");
					$evcaltag_html='<option value="" selected >All Tags</option>';
					foreach( $evcaltags as $tags) {
						$evcaltag_html = $evcaltag_html.'<option value="'.$tags->slug.'">'.$tags->name.'</option>';
					} 
				}
			$html .= '<div class="featured-form-row">
					<label>'.__('Select Slider Type','featured-slider').'</label>
					<select name="eventcal_slider_type" id="eventcal_slider_preview" class="featured-form-input" >
						<option value="list" >'.__('Future Events','featured-slider').'</option>
						<option value="past" >'.__('Past Events','featured-slider').'</option>
						<option value="all" >'.__('Recent Events','featured-slider').'</option>
					</select>
				</div>
				<div class="featured-form-row">
					<label>'.__('Select Category','featured-slider').'</label>
					<select id="featured_slider_eventcal_catslug" multiple class="featured-multiselect featured-form-input" >'.$eventcal_html.'</select>
					<input type="hidden" name="ecal-catg"  />
				</div>
				<div class="featured-form-row">
					<label>'.__('Select Tags','featured-slider').'</label>
					<select id="featured_slider_eventcal_tags" multiple class="featured-multiselect featured-form-input" >'.$evcaltag_html.'</select>
					<input type="hidden" name="ecal-tags"  />
				</div>';
			} elseif($slider_type == 7) { 
				$post_types = get_post_types(); 
				$taxonomy_names = get_object_taxonomies( 'post' );
				// Taxonomy Slider Params  
			$html .= '
				<div class="featured-form-row">
					<label>'.__('Post Type','featured-slider').'</label>
					<select name="taxo_posttype" id="featured_taxonomy_posttype" class="featured-form-input" >';
					foreach ( $post_types as $cpost_type ) { 
						$html .= '<option value="'.$cpost_type.'" >' . $cpost_type . '</option>';
					} 
			$html .= '	</select>
				</div>
				<div class="featured-form-row sh-taxo">
					<label>'.__('Taxonomy','featured-slider').'</label>
					<select name="taxonomy_name" id="featured_taxonomy" class="featured-form-input" >
						<option value="" >Select Taxonomy </option>';
					foreach ( $taxonomy_names as $taxonomy_name ) { 
			$html .= '		<option value="'.$taxonomy_name.'" >' . $taxonomy_name . '</option>';
					}
			$html .= '	</select>
				</div>
				<div class="featured-form-row sh-term" style="display:none;">
					
				</div>
				<div class="featured-form-row">
					<label>'.__('Show','featured-slider').'</label>
					<select name="taxonomy_show" id="featured_taxonomy_show" class="featured-form-input" >
						<option value="" >'.__('Default','featured-slider').'</option>
						<option value="per_tax" >'.__('One Per Tax','featured-slider').'</option>
					</select>
				</div>
				<div class="featured-form-row">
					<label>'.__('Operator','featured-slider').'</label>
					<select name="taxonomy_operator" id="featured_taxonomy_operator" class="featured-form-input" >
						<option value="IN" >'.__('IN','featured-slider').'</option>
						<option value="NOT IN" >'.__('NOT IN','featured-slider').'</option>
						<option value="AND" >'.__('AND','featured-slider').'</option>
					</select>
				</div>
				<div class="featured-form-row">
					<label>'.__('Author','featured-slider').'</label>
					<select id="featured_taxonomy_author" class="featured-multiselect featured-form-input" multiple >';
						$blogusers = get_users();						
						// Array of WP_User objects.
						foreach ( $blogusers as $user ) {
				$html .= '		<option value="'.$user->ID.'" >' . $user->user_nicename . '</option>';
						}
				$html .= '</select>
						<input type="hidden" name="taxonomy_author"  />
				</div>';
			} elseif($slider_type == 8) {
				$html .= '
				<div class="featured-form-row">
					<label>'.__('Feedurl','featured-slider').'</label>
					<input type="text" name="rssfeedurl" id="featured_rssfeed_feedurl" class="featured-form-input large" placeholder="http://mashable.com/feed/" /> 
				</div>

				<div class="featured-form-row">
					<label>'.__('RSS Slider Id','featured-slider').'</label>
					<input type="text" name="rssfeedid" id="featured_rssfeed_id" class="featured-form-input" /> 
				</div>

				<div class="featured-form-row">
					<label>'.__('Default image','featured-slider').'</label>
					<input type="text" name="rssfeedimg" id="featured_rssfeed_defimage" class="featured-form-input large" placeholder="'.featured_slider_plugin_url('/images/default_image.png').'" /> 
				</div>
				
				<div class="featured-form-row">
					<label>'.__('Image Class','featured-slider').'</label>
					<input type="text" name="rss-image-class" id="featured_rssfeed_image_class" class="featured-form-input" /> 
				</div>

				<div class="featured-form-row">
					<label>'.__('Source','featured-slider').'</label>
					<select name="rssfeed-src" id="featured_rssfeed_src" class="featured-form-input rss-source">
						<option value="">'.__('Other','featured-slider').'</option>
						<option value="smugmug">'.__('Smugmug','featured-slider').'</option>
					</select>
				</div>
		
				<div class="featured-form-row rss-feed">
					<label>'.__('Feed','featured-slider').'</label>
					<select name="feed" name="feed" id="featured_rssfeed_feed" class="featured-form-input">
						<option value="">'.__('Other','featured-slider').'</option>
						<option value="atom">'.__('Atom','featured-slider').'</option>
					</select>
				</div>			
	
				<div class="featured-form-row rss-size" style="display:none;">
					<label>'.__('Size','featured-slider').'</label>
					<select name="rss-size" name="rss-size" id="featured_rssfeed_size" class="featured-form-input">
						<option value="Ti">'.__('Tiny thumbnails','featured-slider').'</option>
						<option value="Th">'.__('Large thumbnails','featured-slider').'</option>
						<option value="S">'.__('Small','featured-slider').'</option>
						<option value="M">'.__('Medium','featured-slider').'</option>
						<option value="L">'.__('Other','featured-slider').'</option>
						<option value="XL">'.__('Large','featured-slider').'</option>
						<option value="X2">'.__('X2Large','featured-slider').'</option>
						<option value="X3">'.__('X3Large','featured-slider').'</option>
						<option value="O">'.__('Original','featured-slider').'</option>
					</select> 
				</div>

				<div class="featured-form-row">
					<label>'.__('Scan child node content for images','featured-slider').'</label>
					<input type="checkbox" name="rss-content" id="featured_rssfeed_content" class="featured-form-input" />
				</div>';
			} elseif($slider_type == 9) { 
			$html .= '<div class="featured-form-row">
					<label>'.__('Post Id','featured-slider').'</label>
					<input type="text" name="postattch-id" id="featured_postattch_id" class="featured-form-input" /> 
				</div>';
			} elseif($slider_type == 10) { 
				$galleriesOptions = get_featured_nextgen_galleries();
				$html .= '<div class="featured-form-row">
					<label>'.__('Select Gallery','featured-slider').'</label>
					<select name="nextgen-id" id="featured_nextgen_galleryid" class="featured-form-input">
						'.$galleriesOptions.'
					</select>
				</div>
				<div class="featured-form-row">
					<label>'.__('Link','featured-slider').'</label>
					<input type="checkbox" name="nextgen-anchor" id="featured_nextgen_anchor" value="1" class="featured-form-input" />
				</div>';
			} elseif($slider_type == 11) { 
			$html .= '<div class="featured-form-row">
					<label>'.__('Playlist id','featured-slider').'</label>
					<input type="text" name="yt-playlist-id" id="yt-playlist-id" class="featured-form-input" />
				</div>';
			} elseif($slider_type == 12) { 
			$html .= '<div class="featured-form-row">
					<label>'.__('Term','featured-slider').'</label>
					<input type="text" name="yt-search-term" id="yt-search-term" class="featured-form-input" />
				</div>';
			} elseif($slider_type == 13) { 
			$html .= '<div class="featured-form-row">
					<label>'.__('Select type','featured-slider').'</label>
					<select name="vimeo-type" class="vimeo-type featured-form-input" >
						<option value="channel">'.__('Channel','featured-slider').'</option>
						<option value="album">'.__('Album','featured-slider').'</option>
					</select>
				</div>
				<div class="featured-form-row">
					<label id="vimeo-lb">'.__('Channel Name','featured-slider').'</label>
					<input type="text" name="vimeo-val" id="vimeo-val" class="featured-form-input" />
				</div>';
			} elseif($slider_type == 14) {
			$html .= '<div class="featured-form-row">
					<label>'.__('Page Url','featured-slider').'</label>
					<input type="text" name="fb-pg-url" id="fb-pg-url" class="featured-form-input regular-text" />
					<input type="submit" name="cfb_connect" value="'.__('Connect','featured-slider').'" class="btn_save cfb_connect" />
				</div>
				<div class="featured-form-row fb-albums">
			
				</div>';
			} elseif($slider_type == 15) { 
			$html .= '<div class="featured-form-row">
					<label>'.__('User Name','featured-slider').'</label>
					<input type="text" name="user-name" id="user-name" class="featured-form-input" />
				</div>';
			} elseif($slider_type == 16) {
			$html .= '<div class="featured-form-row">
					<label>'.__('Select type','featured-slider').'</label>
					<select name="flickr-type" class="flickr-type featured-form-input" >
						<option value="user">'.__('User','featured-slider').'</option>
						<option value="album">'.__('Album','featured-slider').'</option>
					</select>
				</div>
				<div class="featured-form-row">
					<label id="flickr-lb">'.__('User ID','featured-slider').'</label>
					<input type="text" name="fl-id" id="fl-user-id" class="featured-form-input" />
				</div>';
			} elseif($slider_type == 18) {
			$html .= '<div class="featured-form-row">
					<label>'.__('Select type','featured-slider').'</label>
					<select name="feature" class="feature featured-form-input" >
						<option value="popular">'.__('Popular','featured-slider').'</option>
						<option value="highest_rated" >'.__('Highest Rated','featured-slider').'</option>
						<option value="upcoming" >'.__('Upcoming','featured-slider').'</option>
						<option value="editors" >'.__('Editors','featured-slider').'</option>
						<option value="fresh_today" >'.__('Fresh Today','featured-slider').'</option>
						<option value="fresh_yesterday" >'.__('Fresh Yesterday','featured-slider').'</option>
						<option value="fresh_week" >'.__('Fresh Week','featured-slider').'</option>
						<option value="user" >'.__('User','featured-slider').'</option>
						<option value="user_favorites">'.__('User favorites','featured-slider').'</option>
					</select>
				</div>
				<div class="featured-form-row pxuser" style="display:none;">
					<label class="featured-form-label">'.__('User Name','featured-slider').'</label>
					<input type="text" name="pxuser" value="" class="featured-form-input" />
				</div>';
			}
			$html .= '<input type="hidden" name="id" value="'.$id.'" / > <input type="hidden" name="sname" value="'.$sname.'"  />
			<input type="submit" name="update-type" class="btn_save update-type" value="'.__('Update','featured-slider').'" />
			<input type="button" name="step2-prev" value="'.__('Back','featured-slider').'" class="featured-updt-btn-back" >
			<div style="clear:left;"></div>
			</form>
		</div>';
		echo $html;
	}
	die();
}
function featured_updt_sldr_type() {
	check_ajax_referer('featured-eb-settings-nonce','eb_settings_nonce');
	global $wpdb,$table_prefix;
	$slider_id = $_POST['id'];
	$slider_type = $_POST['update-slider-type'];
	$parm = $param = '';
	$param_array = array();	
	if($_POST['offset'] != '0' && $_POST['offset'] != '') {
			$param_array['offset']=$_POST['offset'];
	}
	if( $slider_type == '1') {
		if($_POST['catg_slug'] != '0' && $_POST['catg_slug'] != '') {
			$param_array['catg_slug']=$_POST['catg_slug'];
		}
	}
	if($slider_type == '3') {
		if(isset($_POST['woo_slider_type']) && $_POST['woo_slider_type'] != '') {
			$param_array['woo_slider_type'] = $_POST['woo_slider_type'];
		}
		if(isset($_POST['product_id']) && $_POST['product_id'] != '') {
			$param_array['product_id']=$_POST['product_id'];
		}
		if(isset($_POST['woo-catg']) && $_POST['woo-catg'] != '') {
			$param_array['woo-catg']=$_POST['woo-catg'];
		}
	}
	if($slider_type == '4') {
		if(isset($_POST['ecom-catg']) && $_POST['ecom-catg'] != '') {
			$param_array['ecom-catg']=$_POST['ecom-catg'];
		}
		if(isset($_POST['ecom_slider_type']) && $_POST['ecom_slider_type'] != '') {
			$param_array['ecom_slider_type']=$_POST['ecom_slider_type'];
		}
	}
	if($slider_type == '5') {
		if(isset($_POST['eventm_slider_scope']) && $_POST['eventm_slider_scope'] != '') {
			$param_array['eventm_slider_scope'] = $_POST['eventm_slider_scope'];
		}
		if(isset($_POST['eman-catg']) && $_POST['eman-catg'] != '0' && $_POST['eman-catg'] != '') {
			$param_array['eman-catg']=$_POST['eman-catg'];
		}
		if(isset($_POST['eman-tags']) && $_POST['eman-tags'] != '') {
			$param_array['eman-tags']=$_POST['eman-tags'];
		}
	}
	if($slider_type == '6') {
		if(isset($_POST['eventcal_slider_type']) && $_POST['eventcal_slider_type'] != '') {
			$param_array['eventcal_slider_type'] = $_POST['eventcal_slider_type'];
		}
		if(isset($_POST['ecal-catg']) && $_POST['ecal-catg'] != '0' && $_POST['ecal-catg'] != '') {
			$param_array['ecal-catg']=$_POST['ecal-catg'];
		}
		if(isset($_POST['ecal-tags']) && $_POST['ecal-tags'] != '') {
			$param_array['ecal-tags']=$_POST['ecal-tags'];
		}
	}	
	if($slider_type == '7') {
		if( isset($_POST['taxo_posttype']) && $_POST['taxo_posttype'] != '') {
			$param_array['post_type']=$_POST['taxo_posttype'];
		}
		if( isset($_POST['taxonomy_name']) && $_POST['taxonomy_name'] != '') {
			$param_array['taxonomy_name'] = $_POST['taxonomy_name'];
		}
		if( isset($_POST['taxonomy_term']) && $_POST['taxonomy_term'] != '') {
			$param_array['taxonomy_term']=$_POST['taxonomy_term'];
		}
		if( isset($_POST['taxonomy_show']) && $_POST['taxonomy_show'] != '') {
			$param_array['taxonomy_show']=$_POST['taxonomy_show'];
		}
		if( isset($_POST['taxonomy_operator']) && $_POST['taxonomy_operator'] != '') {
			$param_array['taxonomy_operator']=$_POST['taxonomy_operator']; 
		}
		if( isset($_POST['taxonomy_author']) && $_POST['taxonomy_author'] != '') {
			$param_array['taxonomy_author']=$_POST['taxonomy_author'];
		}
	}
	if($slider_type == '8') {
		if( isset($_POST['rssfeedid']) && $_POST['rssfeedid'] != '') {
			$param_array['feed_id']=$_POST['rssfeedid'];
		}
		if( isset($_POST['rssfeedurl']) && $_POST['rssfeedurl'] != '') {
			$param_array['feed_url']=$_POST['rssfeedurl'];
		}
		if( isset($_POST['rssfeedimg']) && $_POST['rssfeedimg'] != '') {
			$param_array['feed_img']=$_POST['rssfeedimg'];
		}	
		if( isset($_POST['feed']) && $_POST['feed'] != '') {
			$param_array['feed']=$_POST['feed'];
		}
		if( isset($_POST['rssfeed-order']) && $_POST['rssfeed-order'] != '') {
			$param_array['feed_order']=$_POST['rssfeed-order'];
		}
		if( isset($_POST['rss-content']) && $_POST['rss-content'] != '') {
			$param_array['feed_content']=$_POST['rss-content'];
		}
		if( isset($_POST['rssfeed-media']) && $_POST['rssfeed-media'] != '') {
			$param_array['feed_media']=$_POST['rssfeed-media'];
		}
		if( isset($_POST['rssfeed-src']) && $_POST['rssfeed-src'] != '') {
			$param_array['feed_src']=$_POST['rssfeed-src'];
		}	
		if( isset($_POST['rss-size']) && $_POST['rss-size'] != '') {
			$param_array['feed_size']=$_POST['rss-size'];
		}
		if( isset($_POST['rss-img-class']) && $_POST['rss-img-class'] != '') {
			$param_array['feed_imgclass']=$_POST['rss-img-class'];
		}	
	}
	if($slider_type == '9') {
		if(isset($_POST['postattch-id']) && $_POST['postattch-id'] != '') {
			$param_array['postattch-id']=$_POST['postattch-id'];
		}
	}	
	if($slider_type == '10') {
		if(isset($_POST['nextgen-id']) && $_POST['nextgen-id'] != '') {
			$param_array['nextgen-id']=$_POST['nextgen-id'];
		}
		if(isset($_POST['nextgen-anchor']) && $_POST['nextgen-anchor'] != '') {
			$param_array['nextgen-anchor']=$_POST['nextgen-anchor'];
		}		
	}
	if($slider_type == '11') {
		if(isset($_POST['yt-playlist-id']) && $_POST['yt-playlist-id'] != '') {
			$param_array['yt-playlist-id']=$_POST['yt-playlist-id'];
		}
	}
	if($slider_type == '12') {
		if(isset($_POST['yt-search-term']) && $_POST['yt-search-term'] != '') {
			$param_array['yt-search-term']=$_POST['yt-search-term'];
		}
	}
	if($slider_type == '13') {
		if(isset($_POST['vimeo-type']) && $_POST['vimeo-type'] != '') {
			$param_array['vimeo-type']=$_POST['vimeo-type'];
		}
		if(isset($_POST['vimeo-val']) && $_POST['vimeo-val'] != '') {
			$param_array['vimeo-val']=$_POST['vimeo-val'];
		}
	}
	if($slider_type == '14') {
		if(isset($_POST['fb-pg-url']) && $_POST['fb-pg-url'] != '') {
			$param_array['fb-pg-url']=$_POST['fb-pg-url'];
		}
		if(isset($_POST['fb-album']) && $_POST['fb-album'] != '') {
			$param_array['fb-album']=$_POST['fb-album'];
		}
	}
	if($slider_type == '15') {
		if(isset($_POST['user-name']) && $_POST['user-name'] != '') {
			$param_array['user-name']=$_POST['user-name'];
		}
	}
	if($slider_type == '16') {
		if(isset($_POST['flickr-type']) && $_POST['flickr-type'] != '') {
			$param_array['flickr-type']=$_POST['flickr-type'];
		}
		if(isset($_POST['fl-id']) && $_POST['fl-id'] != '') {
			$param_array['fl-id']=$_POST['fl-id'];
		}
	}
	if($slider_type == '18') {
		if(isset($_POST['feature']) && $_POST['feature'] != '') {
			$param_array['feature']=$_POST['feature'];
		}
		if(isset($_POST['pxuser']) && $_POST['pxuser'] != '') {
			$param_array['pxuser']=$_POST['pxuser'];
		}
	}
	$sparam = serialize($param_array);
	$param = $sparam;
	$parm = ",param = '$param'";
	$slider_name = isset($_POST["new_slider_name"])?$_POST["new_slider_name"]:'';
	$slider_meta = $table_prefix.FEATURED_SLIDER_META;
	$sql = "UPDATE ".$slider_meta." SET type='".$slider_type."', slider_name='".$slider_name."' $parm WHERE slider_id=$slider_id";
	$wpdb->query($sql);	
	$current_url = admin_url('admin.php?page=featured-slider-easy-builder');
	$urlarg = array();
	$urlarg['id'] = $slider_id;
	$query_arg = add_query_arg( $urlarg ,$current_url);
	echo $current_url = $query_arg;
	die();
}

/*
	Delete Slide from custom Slider
*/

function featured_eb_settings() {
	check_ajax_referer('featured-eb-settings-nonce','eb_settings_nonce');
	$tab = isset($_POST['tab'])?$_POST['tab']:'';	
	$cntr = isset($_POST['cntr'])?$_POST['cntr']:'';
	$featured_slider_options='featured_slider_options'.$cntr;
	$featured_slider_curr=get_option($featured_slider_options);
	if(!isset($cntr) or empty($cntr)){$curr = 'Default';}
	else{$curr = $cntr;}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	$html = '';
	if(get_transient( 'featured_eb_undo_set' ) != false) { 
		$undo_style="display:inline-block;";	
	} else $undo_style="display:none;";
	if($tab == 'basic') {
		$curr_tran = $featured_slider_curr['transition'];
		$curr_ease = $featured_slider_curr['easing'];
			$html.='<div class="ebs-row">
				<label>'.__('Skin','featured-slider').'</label>
				<select class="eb-selnone" name="'.$featured_slider_options.'[stylesheet]" id="featured_slider_stylesheet" >';
					$directory = FEATURED_SLIDER_CSS_DIR;
					if ($handle = opendir($directory)) {
					    while (false !== ($file = readdir($handle))) { 
					     if($file != '.' and $file != '..') { 
					 	
						$html .='<option value="'.$file.'" '.selected($file,$featured_slider_curr['stylesheet'],false).'>'.$file.'</option>';
					} }
					    closedir($handle);
					}
					$html .='</select>
			</div>
			
			<div class="ebs-row">
			<label>'.__('Auto-slide','featured-slider').'</label>
				<div class="eb-switch eb-switchnone">
					<input type="hidden" id="featured_slider_autostep" name="'.$featured_slider_options.'[autostep]" class="hidden_check" value="'. $featured_slider_curr['autostep'].'">
					<input id="featured_autostepsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['autostep'],false).' >
					<label for="featured_autostepsett"></label>
				</div>
			</div>	
			
			<div class="ebs-row">
				<label>'.__('Transition','featured-slider').'</label>	
					<select class="eb-selnone" name="'.$featured_slider_options.'[transition]" id="featured_slider_transition" >
						<option value="scrollHorz" '.selected("scrollHorz",$curr_tran,false).' >'.__('Scroll Horizontally','featured-slider').'</option>
						<option value="fade" '.selected("fade",$curr_tran,false).' >'.__('Fade','featured-slider').'</option>
					</select>
			</div>
			
			<div class="ebs-row">
				<label>'.__('Easing','featured-slider').'</label>	
		 		<select class="eb-selnone" name="'.$featured_slider_options.'[easing]" >
				
				<option value="swing" '.selected("swing",$curr_ease,false).'>'.__('swing','featured-slider').'</option>
				<option value="easeInQuad" '.selected("easeInQuad",$curr_ease,false).'>'.__('easeInQuad','featured-slider').'</option>
				<option value="easeOutQuad" '.selected("easeOutQuad",$curr_ease,false).'>'.__('easeOutQuad','featured-slider').'</option>
				<option value="easeInOutQuad" '.selected("easeInOutQuad",$curr_ease,false).'>'.__('easeInOutQuad','featured-slider').'</option>
				<option value="easeInCubic" '.selected("easeInCubic",$curr_ease,false).'>'.__('easeInCubic','featured-slider').'</option>
				<option value="easeOutCubic" '.selected("easeOutCubic",$curr_ease,false).'>'.__('easeOutCubic','featured-slider').'</option>
				<option value="easeInOutCubic" '.selected("easeInOutCubic",$curr_ease,false).'>'.__('easeInOutCubic','featured-slider').'</option>
				<option value="easeInQuart" '.selected("easeInQuart",$curr_ease,false).'>'.__('easeInQuart','featured-slider').'</option>
				<option value="easeOutQuart" '.selected("easeOutQuart",$curr_ease,false).'>'.__('easeOutQuart','featured-slider').'</option>				
				<option value="easeInOutQuart" '.selected("easeInOutQuart",$curr_ease,false).'>'.__('easeInOutQuart','featured-slider').'</option>		
				<option value="easeInQuint" '.selected("easeInQuint",$curr_ease,false).'>'.__('easeInQuint','featured-slider').'</option>		
				<option value="easeOutQuint" '.selected("easeOutQuint",$curr_ease,false).'>'.__('easeOutQuint','featured-slider').'</option>		
				<option value="easeInOutQuint" '.selected("easeInOutQuint",$curr_ease,false).'>'.__('easeInOutQuint','featured-slider').'</option>		
				<option value="easeInSine" '.selected("easeInSine",$curr_ease,false).'>'.__('easeInSine','featured-slider').'</option>		
				<option value="easeOutSine" '.selected("easeOutSine",$curr_ease,false).'>'.__('easeOutSine','featured-slider').'</option>		
				<option value="easeInOutSine" '.selected("easeInOutSine",$curr_ease,false).'>'.__('easeInOutSine','featured-slider').'</option>		
				<option value="easeInExpo" '.selected("easeInExpo",$curr_ease,false).'>'.__('easeInExpo','featured-slider').'</option>		
				<option value="easeOutExpo" '.selected("easeOutExpo",$curr_ease,false).'>'.__('easeOutExpo','featured-slider').'</option>					
				<option value="easeInOutExpo" '.selected("easeInOutExpo",$curr_ease,false).'>'.__('easeInOutExpo','featured-slider').'</option>					
				<option value="easeInCirc" '.selected("easeInCirc",$curr_ease,false).'>'.__('easeInCirc','featured-slider').'</option>		
				<option value="easeOutCirc" '.selected("easeOutCirc",$curr_ease,false).'>'.__('easeOutCirc','featured-slider').'</option>		
				<option value="easeInOutCirc" '.selected("easeInOutCirc",$curr_ease,false).'>'.__('easeInOutCirc','featured-slider').'</option>		
				<option value="easeInElastic" '.selected("easeInElastic",$curr_ease,false).'>'.__('easeInElastic','featured-slider').'</option>		
				<option value="easeOutElastic" '.selected("easeOutElastic",$curr_ease,false).'>'.__('easeOutElastic','featured-slider').'</option>		
				<option value="easeInOutElastic" '.selected("easeInOutElastic",$curr_ease,false).'>'.__('easeInOutElastic','featured-slider').'</option>	
				<option value="easeInBack" '.selected("easeInBack",$curr_ease,false).'>'.__('easeInBack','featured-slider').'</option>	
				<option value="easeOutBack" '.selected("easeOutBack",$curr_ease,false).'>'.__('easeOutBack','featured-slider').'</option>	
				<option value="easeInOutBack" '.selected("easeInOutBack",$curr_ease,false).'>'.__('easeInOutBack','featured-slider').'</option>	
				<option value="easeInBounce" '.selected("easeInBounce",$curr_ease,false).'>'.__('easeInBounce','featured-slider').'</option>	
				<option value="easeOutBounce" '.selected("easeOutBounce",$curr_ease,false).'>'.__('easeOutBounce','featured-slider').'</option>	
				<option value="easeInOutBounce" '.selected("easeInOutBounce",$curr_ease,false).'>'.__('easeInOutBounce','featured-slider').'</option>	
				</select>
			</div>

			<div class="ebs-row">
				<label>'.__('Speed','featured-slider').'</label>
				<input type="number" name="'.$featured_slider_options.'[speed]" id="featured_slider_speed" class="small-text" value="'.$featured_slider_curr['speed'].'" min="1"/>ms
			</div>
			
			<div class="ebs-row">
				<label>'.__('Transitions Time','featured-slider').'</label>					
				<input type="number" name="'.$featured_slider_options.'[time]" id="featured_slider_time" class="small-text" value="'.$featured_slider_curr['time'].'" min="1" />ms
			</div>
			
			<div class="ebs-row">
				<label>'.__('No. of Slides','featured-slider').'</label>
				<input type="number" name="'.$featured_slider_options.'[no_posts]" id="featured_slider_no_posts" class="small-text" value="'.$featured_slider_curr['no_posts'].'" min="1" />
			</div>
			
			<div class="ebs-row">
				<label>'.__('Max. Slider Width','featured-slider').'</label>
				<input type="number" name="'.$featured_slider_options.'[width]" id="featured_slider_width" class="small-text" value="'.$featured_slider_curr['width'].'" min="1" />
			</div>
 
			<div class="ebs-row">
				<label>'.__('Large Slider Width','featured-slider').'</label>
				<input type="number" name="'.$featured_slider_options.'[lswidth]" id="featured_slider_lswidth" class="small-text" value="'.$featured_slider_curr['lswidth'].'" min="1" />
			</div>
			
			<div class="ebs-row">
				<label>'.__('Max. Slider Height','featured-slider').'</label>
				<input type="number" name="'.$featured_slider_options.'[height]" id="featured_slider_height" class="small-text" value="'.$featured_slider_curr['height'].'" />&nbsp;'.__('px','featured-slider').' 
			</div>

			<div class="ebs-row">
				<label>'.__('Sub Slide Border','featured-slider').'</label>
				<div class="eb-lrightdiv">
					<div>
						<label class="eb-mlabel">'.__('Thickness','featured-slider').'</label>
						<input type="number" name="'.$featured_slider_options.'[slide_border]" id="featured_slider_slide_border" class="small-text" value="'.$featured_slider_curr['slide_border'].'" min="0" />
					</div>
					<div style="margin-top: 5px;">
						<label class="eb-mlabel">'.__('Color','featured-slider').'</label>
						<input type="text" name="'.$featured_slider_options.'[slide_brcolor]" id="featured_slider_slide_brcolor" value="'.$featured_slider_curr['slide_brcolor'].'" class="wp-color-picker-field" data-default-color="#D8E7EE" />
					</div>
				</div>
			</div>

			<div class="ebs-row">
				<label class="eb-slabel">'.__('Border','featured-slider').'</label>
				<div class="eb-lrightdiv">
					<div>
						<label class="eb-mlabel">'.__('Thickness','featured-slider').'</label>
						<input type="number" name="'.$featured_slider_options.'[border]" id="featured_slider_border" class="small-text" value="'.$featured_slider_curr['border'].'" min="0" />
					</div>
					<div style="margin-top: 5px;">
						<label class="eb-mlabel">'.__('Color','featured-slider').'</label>
						<input type="text" name="'.$featured_slider_options.'[brcolor]" id="featured_slider_brcolor" value="'.$featured_slider_curr['brcolor'].'" class="wp-color-picker-field" data-default-color="#D8E7EE" />
					</div>
				</div>
			</div>
			
			<div class="ebs-row">
				<label style="width:30%">'.__('Background','featured-slider').'</label>
				<input type="text" name="'.$featured_slider_options.'[bg_color]" id="featured_slider_bg_color" value="'.$featured_slider_curr['bg_color'].'" class="wp-color-picker-field" data-default-color="#000000" />
			</div>
			<div class="ebs-row">
				<div class="eb-switch eb-switchnone">
					<input type="hidden" name="'.$featured_slider_options.'[bg]" id="featured_slider_bg" class="hidden_check" value="'.$featured_slider_curr['bg'].'">
					<input id="featured_bgsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['bg'],false).'>
					<label for="featured_bgsett"></label>
				</div>
				'.__('Enable Transparent Background','featured-slider').'
			</div>
				
			<div class="ebs-row">
				<label>'.__('Fixed Blocks','featured-slider').'</label>
				<div class="eb-switch">
					<input type="hidden" name="'.$featured_slider_options.'[fixblocks]" id="featured_slider_fixblocks" class="hidden_check" value="'.$featured_slider_curr['fixblocks'].'">
					<input id="featured_fixblocks" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['fixblocks'],false).'>
					<label for="featured_fixblocks"></label>
				</div>
		 	</div>
		 	
		 	<div class="ebs-row">
				<label>'.__('Block Location','featured-slider').'</label>
				<select name="'.$featured_slider_options.'[block_pos]" id="featured_slider_block_pos" >
					<option value="1" '.selected("1",$featured_slider_curr['block_pos'],false).' >'.__('Right','featured-slider').'</option>
					<option value="0" '.selected("0",$featured_slider_curr['block_pos'],false).' >'.__('Left','featured-slider').'</option>
				</select>
			</div>';
			if( $featured_slider_curr['stylesheet']== 'trio' ) {	
			$html.= '<div class="ebs-row">
					<label>'.__('Trio blocks','featured-slider').'</label>
					<select name="'.$featured_slider_options.'[trio_block]" id="featured_slider_trio_block" >
						<option value="2" '.selected("2",$featured_slider_curr['trio_block'],false).' >'.__('Upper block','featured-slider').'</option>
						<option value="3" '.selected("3",$featured_slider_curr['trio_block'],false).' >'.__('Lower block','featured-slider').'</option>
						<option value="4" '.selected("4",$featured_slider_curr['trio_block'],false).' >'.__('List block','featured-slider').'</option>
					</select>
				</div>';
			}
				
			$html.= '<p class="submit">
			<input type="submit" class="button-primary" name="save_eb_settings" value="'.__('Save Changes','featured-slider').'" />
			<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
		</p>';
			
	} elseif($tab == 'miscellaneous') {
	
		$html .= '<div class="ebs-row">
			<label class="eb-llabel">'.__('Lightbox Effect','featured-slider').'</label>
			<div class="eb-switch">
				<input type="hidden" name="'.$featured_slider_options.'[pphoto]" class="featured_slider_pphoto hidden_check" value="'.$featured_slider_curr['pphoto'].'">
				<input id="featured_pphoto" class="cmn-toggle eb-toggle-round featured_pphoto" type="checkbox" '.checked('1', $featured_slider_curr['pphoto'],false).'>
				<label for="featured_pphoto"></label>
			</div>
		</div>';
			if($featured_slider_curr['pphoto'] == 1 ) $lbox_style = 'display:block';
			else $lbox_style = 'display:none';

		$html .= '<div class="ebs-row featured_slider_lbox_type"  style="'.$lbox_style.'" >
			<label class="eb-llabel">'.__('Select LightBox','featured-slider').'</label>
			<select name="'.$featured_slider_options.'[lbox_type]" >
					<option value="pphoto_box" '.selected("pphoto_box",$featured_slider_curr['lbox_type'],false).' >'.__('PrettyPhoto','featured-slider').'</option>
					<option value="nivo_box" '.selected("nivo_box",$featured_slider_curr['lbox_type'],false).' >'.__('Nivo box','featured-slider').'</option>
					<option value="photo_box" '.selected("photo_box",$featured_slider_curr['lbox_type'],false).' >'.__('Photo box','featured-slider').'</option>
					<option value="smooth_box" '.selected("smooth_box",$featured_slider_curr['lbox_type'],false).' >'.__('Smooth box','featured-slider').'</option>
					<option value="swipe_box" '.selected("swipe_box",$featured_slider_curr['lbox_type'],false).' >'.__('Swipe box','featured-slider').'</option>
			</select>
		</div>

		<div class="ebs-row">
				<label class="eb-llabel">'.__('Continue Reading Text','featured-slider').'</label>
				<input type="text" name="'.$featured_slider_options.'[more]" id="featured_slider_more" class="eb-smalltext" value="'.$featured_slider_curr['more'].'" />
		</div>

		<div class="ebs-row">
				<label class="eb-llabel">'.__('Color','featured-slider').'</label>
				<input type="text" name="'.$featured_slider_options.'[more_color]" id="featured_slider_more_color" value="'.$featured_slider_curr['more_color'].'" class="wp-color-picker-field" data-default-color="#ffffff" />
		</div>

		<div class="ebs-row">
				<label class="eb-llabel">'.__('Retain tags','featured-slider').'</label>
				<input type="text" name="'.$featured_slider_options.'[allowable_tags]" id="featured_slider_allowable_tags" class="eb-smalltext" value="'.$featured_slider_curr['allowable_tags'].'" />
		</div>

	
		<div class="ebs-row">
				<label class="eb-llabel">'.__('Link attributes  ','featured-slider').'</label>
				<input type="text" name="'.$featured_slider_options.'[a_attr]" id="featured_slider_a_attr" class="eb-smalltext" value="'.htmlentities( $featured_slider_curr['a_attr'] , ENT_QUOTES).'" />
		</div>
	
		<div class="ebs-row">
				<label class="eb-llabel">'.__('Enable FOUC','featured-slider').'</label>
				<div class="eb-switch havemoreinfo">
					<input type="hidden" name="'.$featured_slider_options.'[fouc]" id="featured_slider_fouc" class="hidden_check" value="'.$featured_slider_curr['fouc'].'">
					<input id="featured_foucsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['fouc'],false).'>
					<label for="featured_foucsett"></label>
				</div>
		</div>

		<div class="ebs-row">
				<label class="eb-llabel">'.__('Custom fields','featured-slider').'</label>
				<textarea name="'.$featured_slider_options.'[fields]"  id="featured_slider_fields" rows="2" class="regular-text code">'.$featured_slider_curr['fields'].'</textarea>
		</div>
		
		<div class="ebs-row">
			<label class="eb-llabel">'.__('Randomize slides','featured-slider').'</label>
			<div class="eb-switch">
				<input type="hidden" name="'.$featured_slider_options.'[rand]" id="featured_slider_rand" class="hidden_check" value="'.$featured_slider_curr['rand'].'">
				<input id="featured_rand" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['rand'],false).' >
				<label for="featured_rand"></label>
			</div>
		</div>
		
		<div class="ebs-row">
			<label class="eb-llabel">'.__('Do not link slide to any url','featured-slider').'</label>
			<div class="eb-switch">
				<input type="hidden" name="'.$featured_slider_options.'[donotlink]" id="featured_slider_donotlink" class="hidden_check" value="'.$featured_slider_curr['donotlink'].'">
				<input id="featured_donotlink" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['donotlink'],false).' >
				<label for="featured_donotlink"></label>
			</div>
		</div>
		
		<div class="ebs-row">
			<label style="width: 60%;">'.__('Disable Slider on Mobiles and Tablets','featured-slider').'</label>
			<div class="eb-switch">
				<input type="hidden" name="'.$featured_slider_options.'[disable_mobile]" id="featured_slider_disable_mobile" class="hidden_check" value="'.$featured_slider_curr['disable_mobile'].'">
				<input id="featured_disable_mobile" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['disable_mobile'],false).'>
				<label for="featured_disable_mobile"></label>
			</div>
	 	</div>
		
		<p class="submit">
			<input type="submit" class="button-primary" name="save_eb_settings" value="'.__('Save Changes','featured-slider').'" />
			<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
		</p>';

	} elseif($tab == 'image') {
		$default_featured_slider_settings=get_featured_slider_default_settings();
		$featured_slider_curr['img_pick'][0]=(isset($featured_slider_curr['img_pick'][0]))?$featured_slider_curr['img_pick'][0]:'';
		$featured_slider_curr['img_pick'][1]=(isset($featured_slider_curr['img_pick'][1]))?$featured_slider_curr['img_pick'][1]:'';
		$featured_slider_curr['img_pick'][2]=(isset($featured_slider_curr['img_pick'][2]))?$featured_slider_curr['img_pick'][2]:'';
		$featured_slider_curr['img_pick'][3]=(isset($featured_slider_curr['img_pick'][3]))?$featured_slider_curr['img_pick'][3]:'';
		$featured_slider_curr['img_pick'][5]=(isset($featured_slider_curr['img_pick'][5]))?$featured_slider_curr['img_pick'][5]:'';
		$imgpick_style = '';
	 	if(isset($cntr) and $cntr>0) $imgpick_style = 'style="display:none;"';
  	$html.='<div class="ebs-row">
	<div style="margin-bottom: 10px;">'.__('Image Source','featured-slider').'</div>
		<div class="eb-switch eb-switchnone">
			<input type="hidden" name="'.$featured_slider_options.'[img_pick][0]" class="hidden_check" value="'.$featured_slider_curr['img_pick'][0].'">
			<input id="featured_customfldchk" class="cmn-toggle eb-toggle-round" type="checkbox" '. checked('1', $featured_slider_curr['img_pick'][0],false).'>
			<label for="featured_customfldchk"></label>
		</div>';		
		if(!isset($cntr) or empty($cntr)){ 
				$html .= __('Custom field','featured-slider'); 
		} else { 
			$html .= __('(Set custom field name on Default Settings)','featured-slider'); 
		}
		$html.='<input type="text" name="'.$featured_slider_options.'[img_pick][1]" class="text eb-small-text" value="'.$featured_slider_curr['img_pick'][1].'" '.$imgpick_style.' />
	<div class="ebs-row">
		<div class="eb-switch eb-switchnone">
			<input type="hidden" name="'.$featured_slider_options.'[img_pick][2]" class="hidden_check" value="'.$featured_slider_curr['img_pick'][2].'">
			<input id="featured_featuredimgchk" class="cmn-toggle eb-toggle-round" type="checkbox" '. checked('1', $featured_slider_curr['img_pick'][2],false).'>
			<label for="featured_featuredimgchk"></label>
		</div>
		<label>'.__('Featured Image','featured-slider').'</label>	
	</div>

	<div class="ebs-row">
		<div class="eb-switch eb-switchnone">
			<input type="hidden" name="'.$featured_slider_options.'[img_pick][3]" class="hidden_check" value="'.$featured_slider_curr['img_pick'][3].'">
			<input id="featured_attachedimgchk" class="cmn-toggle eb-toggle-round" type="checkbox" '. checked('1', $featured_slider_curr['img_pick'][3],false).'>
			<label for="featured_attachedimgchk"></label>
		</div>
		<label>'. __('Attached image,order','featured-slider').'</label>
		<input type="text" name="'.$featured_slider_options.'[img_pick][4]" class="small-text" value="'.$featured_slider_curr['img_pick'][4].'" />
	</div>

	<div class="ebs-row">
		<div class="eb-switch eb-switchnone">
			<input type="hidden" name="'.$featured_slider_options.'[img_pick][5]" class="hidden_check" value="'.$featured_slider_curr['img_pick'][5].'">
			<input id="featured_scanimgchk" class="cmn-toggle eb-toggle-round" type="checkbox" '. checked('1', $featured_slider_curr['img_pick'][5],false).'>
			<label for="featured_scanimgchk"></label>
		</div>
		<label>'.__('Scan content','featured-slider').'</label>	
	</div>
		
	<div class="ebs-row">
		<label>'.__('Fetched Image size','featured-slider').'</label>
		<select name="'.$featured_slider_options.'[crop]" id="featured_slider_crop" >
			<option value="0" '.selected("0",$featured_slider_curr['crop'],false).'>'.__('Full','featured-slider').'</option>
			<option value="1" '.selected("1",$featured_slider_curr['crop'],false).' >'.__('Large','featured-slider').'</option>
			<option value="2" '.selected("2",$featured_slider_curr['crop'],false).' >'.__('Medium','featured-slider').'</option>
			<option value="3" '.selected("3",$featured_slider_curr['crop'],false).' >'.__('Thumbnail','featured-slider').'</option>
		</select>
	</div>
	
	<div class="ebs-row">
		<label>'.__('Pure image slider','featured-slider').'</label>
		<div class="eb-switch eb-rswitch">
			<input type="hidden" name="'.$featured_slider_options.'[image_only]" id="featured_slider_image_only" class="hidden_check" value="'.$featured_slider_curr['image_only'].'">
			<input id="featured_imageonly" class="cmn-toggle eb-toggle-round" type="checkbox" '. checked('1', $featured_slider_curr['image_only'],false).'>
			<label for="featured_imageonly"></label>
		</div>
	</div>
	
	<div class="ebs-row">
		<label>'.__('Image title on hover','featured-slider').'</label>
		<div class="eb-switch eb-rswitch">
			<input type="hidden" name="'.$featured_slider_options.'[image_title_text]" class="hidden_check" value="'.$featured_slider_curr['image_title_text'].'">
			<input id="featured_thover" class="cmn-toggle eb-toggle-round" type="checkbox" '. checked('1', $featured_slider_curr['image_title_text'],false).'>
			<label for="featured_thover"></label>
		</div>
	</div>
	
	<div class="ebs-row">
		<label class="eb-llabel">'.__('Enable image cropping','featured-slider').'</label>
		<div class="eb-switch eb-rswitch">
			<input type="hidden" name="'.$featured_slider_options.'[cropping]" class="hidden_check" value="'.$featured_slider_curr['cropping'].'">
			<input id="featured_disableimgcrop" class="cmn-toggle eb-toggle-round" type="checkbox" '. checked('1', $featured_slider_curr['cropping'],false).'>
			<label for="featured_disableimgcrop"></label>
		</div>
	</div>
	
	<div class="ebs-row">
		<label style="width: 38%;">'.__('Transition','featured-slider').'</label>';
			$tital_tran_name = $featured_slider_options.'[img_transition]';
			$html .= get_featured_transitions($tital_tran_name,$featured_slider_curr['img_transition']).'
	</div>

	<div class="ebs-row">
		<label class="eb-llabel">'.__('Duration (seconds)','featured-slider').'</label> 
		<input type="text" name="'.$featured_slider_options.'[img_duration]" id="featured_slider_img_duration" class="eb-gsmalltext" value="'.$featured_slider_curr['img_duration'].'" />
	</div>

	<div class="ebs-row">
		<label class="eb-llabel">'.__('Delay time (seconds)','featured-slider').'</label> 
		<input type="text" name="'.$featured_slider_options.'[img_delay]" id="featured_slider_img_delay" class="eb-gsmalltext" value="'.$featured_slider_curr['img_delay'].'" />
	</div>	

	<div class="ebs-row">	
		<label>'.__('Default Image','featured-slider').'</label>
		<img id="default-img" src="'.$featured_slider_curr['default_image'].'" width="80" height="70" />
		<input type="submit" name="default-image-upload" class="featured-upload-default" value="Upload" style="float:left;margin-left: 50%;background: #dddddd;cursor:pointer;" >
		<input type="submit" name="default-image-reset" class="featured-reset-default" value="Reset" style="background: #dddddd;cursor:pointer;" >
		<input type="hidden" id="default-image-url" value="'.$default_featured_slider_settings['default_image'].'">
		<input type="hidden" name="'.$featured_slider_options.'[default_image]" id="featured_slider_default_image" class="regular-text code" value="'.$featured_slider_curr['default_image'].'" />
	</div>
	
	<p class="submit">
		<input type="submit" class="button-primary" name="save_eb_settings" value="'.__('Save Changes','featured-slider').'" />
		<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
	</p>';
	} elseif($tab == 'text') {
		$title_from = $featured_slider_curr['title_from'];
		$html.='<div class="settings-tbl">
		<label>'.__('Slider title','featured-slider').'</label>
		
		<div class="ebs-row">	
			<label>'.__('Default Text','featured-slider').'</label>
			<input type="text" name="'.$featured_slider_options.'[title_text]" id="featured_slider_title_text" value="'.htmlentities($featured_slider_curr['title_text'], ENT_QUOTES).'" class="eb-title-text" />
		</div>
		
		<div class="ebs-row">	
			<label>'.__('Select title from','featured-slider').'</label>
			<select name="'.$featured_slider_options.'[title_from]" >
				<option value="0" '.selected('0',$title_from,false).'>'.__('Default Text','featured-slider').'</option>
				<option value="1" '.selected('1',$title_from,false).'>'.__('Slider Name','featured-slider').'</option>
			</select>
		</div>
	
		<div class="ebs-row">
				<label class="eb-slabel">'.__('Font','featured-slider').'</label>			
						
					<input type="hidden" value="title_font" class="ftype_rname">
					<input type="hidden" value="title_fontg" class="ftype_gname">
					<input type="hidden" value="titlefont_custom" class="ftype_cname">
					<select name="'.$featured_slider_options.'[t_font]" id="featured_slider_st_font" class="main-font">
						<option value="regular" '.selected( $featured_slider_curr['t_font'], "regular", false ).' > Regular Fonts </option>
						<option value="google" '.selected( $featured_slider_curr['t_font'], "google", false ).' > Google Fonts </option>
						<option value="custom" '.selected( $featured_slider_curr['t_font'], "custom", false ).' > Custom Fonts </option>
					</select>	
				<div class="load-fontdiv">	</div>
		</div>

		<div class="ebs-row">
			<label>'.__('Color','featured-slider').'</label>
			<input type="text" name="'.$featured_slider_options.'[title_fcolor]" id="featured_slider_title_fcolor" value="'.$featured_slider_curr['title_fcolor'].'" class="wp-color-picker-field" data-default-color="#ffffff" />
		</div>

		<div class="ebs-row">
			<label>'.__('Size','featured-slider').'</label>
			<input type="number" name="'.$featured_slider_options.'[title_fsize]" id="featured_slider_title_fsize" class="small-text" value="'.$featured_slider_curr['title_fsize'].'" min="1" />
		</div>

		<div class="ebs-row font-style">	
		<label>'.__('Style','featured-slider').'</label>
			<select name="'.$featured_slider_options.'[title_fstyle]" id="featured_slider_title_fstyle" >
			<option value="bold" '.selected("bold",$featured_slider_curr['title_fstyle'],false).' >'.__('Bold','featured-slider').'</option>
					<option value="bold italic" '.selected("bold italic",$featured_slider_curr['title_fstyle'],false).' >'.__('Bold Italic','featured-slider').'</option>
					<option value="italic" '.selected("italic",$featured_slider_curr['title_fstyle'],false).' >'.__('Italic','featured-slider').'</option>
					<option value="normal" '.selected("normal",$featured_slider_curr['title_fstyle'],false).' >'.__('Normal','featured-slider').'</option>
			</select>
		</div>
		
		</div>
		
		<div class="eb-altcolor">
		<div class="eb-altcolorinner settings-tbl">
		
		<div class="ebs-row">
			<label>'.__('Slide title','featured-slider').'</label>
			<div class="eb-switch">
				<input type="hidden" name="'.$featured_slider_options.'[show_title]" id="featured_slider_show_title" class="hidden_check" value="'.$featured_slider_curr['show_title'].'">
				<input id="featured_showtitle" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['show_title'],false).'>
				<label for="featured_showtitle"></label>
			</div>
		</div>
		
		<div class="ebs-row">
			<label class="eb-slabel">'.__('Font','featured-slider').'</label>			
				<!-- code for new fonts - 3.0 -->
				<input type="hidden" value="ptitle_font" class="ftype_rname">
				<input type="hidden" value="ptitle_fontg" class="ftype_gname">
				<input type="hidden" value="ptfont_custom" class="ftype_cname">
			<select name="'.$featured_slider_options.'[pt_font]" id="featured_slider_pt_font" class="main-font">
					<option value="regular" '.selected( $featured_slider_curr['pt_font'], "regular", false ).' > Regular Fonts </option>
					<option value="google" '.selected( $featured_slider_curr['pt_font'], "google", false ).' > Google Fonts </option>
					<option value="custom" '.selected( $featured_slider_curr['pt_font'], "custom", false ).' > Custom Fonts </option>
			</select>	
			<div class="load-fontdiv" colspan="2"></div>
		</div>

	<div class="ebs-row">
	<label>'.__('Color','featured-slider').'</label>
		<input type="text" name="'.$featured_slider_options.'[ptitle_fcolor]" id="featured_slider_ptitle_fcolor" value="'.$featured_slider_curr['ptitle_fcolor'].'" class="wp-color-picker-field" data-default-color="#ffffff" />
	</div>

	<div class="ebs-row">
	<label>'.__('Size','featured-slider').'</label>
	<input type="number" name="'.$featured_slider_options.'[ptitle_fsize]" id="featured_slider_ptitle_fsize" class="small-text" value="'.$featured_slider_curr['ptitle_fsize'].'" min="1" />
	</div>


	<div class="ebs-row font-style">
	<label>'.__('Style','featured-slider').'</label>
		<select name="'.$featured_slider_options.'[ptitle_fstyle]" id="featured_slider_ptitle_fstyle" >
				<option value="bold" '.selected("bold",$featured_slider_curr['ptitle_fstyle'],false).' >'.__('Bold','featured-slider').'</option>
				<option value="bold italic" '.selected("bold italic",$featured_slider_curr['ptitle_fstyle'],false).' >'.__('Bold Italic','featured-slider').'</option>
				<option value="italic" '.selected("italic",$featured_slider_curr['ptitle_fstyle'],false).' >'.__('Italic','featured-slider').'</option>
				<option value="normal" '.selected("normal",$featured_slider_curr['ptitle_fstyle'],false).' >'.__('Normal','featured-slider').'</option>
		</select>
	</div>
	
		<div class="ebs-row">
	<label>'.__('HTML element','featured-slider').'</label>
		<select name="'.$featured_slider_options.'[mtitle_element]" >
				<option value="1" '.selected("1",$featured_slider_curr['mtitle_element'], false).' >h1</option>
				<option value="2" '.selected("2",$featured_slider_curr['mtitle_element'], false).' >h2</option>
				<option value="3" '.selected("3",$featured_slider_curr['mtitle_element'], false).' >h3</option>
				<option value="4" '.selected("4",$featured_slider_curr['mtitle_element'], false).' >h4</option>
				<option value="5" '.selected("5",$featured_slider_curr['mtitle_element'], false).' >h5</option>
				<option value="6" '.selected("6",$featured_slider_curr['mtitle_element'], false).' >h6</option>
		</select>	
	</div>

	<div class="ebs-row">
			<label style="width: 38%;">'.__('Transition','featured-slider').'</label>';
			$tital_tran_name = $featured_slider_options.'[ptitle_transition]';
			$html .= get_featured_transitions($tital_tran_name,$featured_slider_curr['ptitle_transition']).' 
	</div>
	
	<div class="ebs-row">
		<label class="eb-llabel">'.__('Duration (seconds)','featured-slider').'</label> 
		<input type="text" name="'.$featured_slider_options.'[ptitle_duration]" id="featured_slider_ptitle_duration" class="eb-gsmalltext" value="'.$featured_slider_curr['ptitle_duration'].'" />
	</div>

	<div class="ebs-row">
		<label class="eb-llabel">'.__('Delay time (seconds)','featured-slider').'</label> 
		<input type="text" name="'.$featured_slider_options.'[ptitle_delay]" id="featured_slider_ptitle_delay" class="eb-gsmalltext" value="'.$featured_slider_curr['ptitle_delay'].'" />
	</div>
	
	</div><!--eb-altcolorinner-->
	</div><!--eb-altcolor-->

	<div class="eb-altcolor">
		<div class="eb-altcolorinner settings-tbl">
		<div class="ebs-row">
			<label class="eb-llabel">'.__('Sub Slide title','featured-slider').'</label> 
			<div class="eb-switch">
				<input type="hidden" name="'.$featured_slider_options.'[show_sub_title]" id="featured_slider_show_sub_title" class="hidden_check" value="'.$featured_slider_curr['show_sub_title'].'">
				<input id="featured_showsubtitle" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['show_sub_title'],false).'>
				<label for="featured_showsubtitle"></label>
			</div>
		</div>
		
		<div class="ebs-row">
			<label class="eb-slabel">'.__('Font','featured-slider').'</label>			
			<!-- code for new fonts - 3.0 -->
			<input type="hidden" value="sub_ptitle_font" class="ftype_rname">
			<input type="hidden" value="sub_ptitle_fontg" class="ftype_gname">
			<input type="hidden" value="sub_ptfont_custom" class="ftype_cname">
			<select name="'.$featured_slider_options.'[sub_pt_font]" id="featured_slider_sub_pt_font" class="main-font">
				<option value="regular" '.selected( $featured_slider_curr['sub_pt_font'], "regular", false ).' > Regular Fonts </option>
				<option value="google" '.selected( $featured_slider_curr['sub_pt_font'], "google", false ).' > Google Fonts </option>
				<option value="custom" '.selected( $featured_slider_curr['sub_pt_font'], "custom", false ).' > Custom Fonts </option>
			</select>	
			<div class="load-fontdiv" colspan="2"></div>
		</div>

	<div class="ebs-row">
		<label>'.__('Color','featured-slider').'</label>
		<input type="text" name="'.$featured_slider_options.'[sub_ptitle_fcolor]" id="featured_slider_sub_ptitle_fcolor" value="'.$featured_slider_curr['sub_ptitle_fcolor'].'" class="wp-color-picker-field" data-default-color="#ffffff" />
	</div>

	<div class="ebs-row">
		<label>'.__('Size','featured-slider').'</label>
		<input type="number" name="'.$featured_slider_options.'[sub_ptitle_fsize]" id="featured_slider_sub_ptitle_fsize" class="small-text" value="'.$featured_slider_curr['sub_ptitle_fsize'].'" min="1" />
	</div>
 
	<div class="ebs-row font-style">	
		<label>'.__('Style','featured-slider').'</label>
		<select name="'.$featured_slider_options.'[sub_ptitle_fstyle]" id="featured_slider_sub_ptitle_fstyle" >
			<option value="bold" '.selected("bold",$featured_slider_curr['sub_ptitle_fstyle'],false).' >'.__('Bold','featured-slider').'</option>
			<option value="bold italic" '.selected("bold italic",$featured_slider_curr['sub_ptitle_fstyle'],false).' >'.__('Bold Italic','featured-slider').'</option>
			<option value="italic" '.selected("italic",$featured_slider_curr['sub_ptitle_fstyle'],false).' >'.__('Italic','featured-slider').'</option>
			<option value="normal" '.selected("normal",$featured_slider_curr['sub_ptitle_fstyle'],false).' >'.__('Normal','featured-slider').'</option>
		</select>
	</div>
	
	<div class="ebs-row">
	<label>'.__('HTML element','featured-slider').'</label>
		<select name="'.$featured_slider_options.'[stitle_element]" >
				<option value="1" '.selected("1",$featured_slider_curr['stitle_element'], false).' >h1</option>
				<option value="2" '.selected("2",$featured_slider_curr['stitle_element'], false).' >h2</option>
				<option value="3" '.selected("3",$featured_slider_curr['stitle_element'], false).' >h3</option>
				<option value="4" '.selected("4",$featured_slider_curr['stitle_element'], false).' >h4</option>
				<option value="5" '.selected("5",$featured_slider_curr['stitle_element'], false).' >h5</option>
				<option value="6" '.selected("6",$featured_slider_curr['stitle_element'], false).' >h6</option>
		</select>	
	</div>

	<div class="ebs-row">
			<label style="width: 38%;">'.__('Transition','featured-slider').'</label>';
			$tital_tran_name = $featured_slider_options.'[sub_ptitle_transition]';
			$html .= get_featured_transitions($tital_tran_name,$featured_slider_curr['sub_ptitle_transition']).' 
	</div>

	<div class="ebs-row">
		<label class="eb-llabel">'.__('Duration (seconds)','featured-slider').'</label> 
		<input type="text" name="'.$featured_slider_options.'[sub_ptitle_duration]" id="featured_slider_sub_ptitle_duration" class="eb-gsmalltext" value="'.$featured_slider_curr['sub_ptitle_duration'].'" />
	</div>

	<div class="ebs-row">
		<label class="eb-llabel">'.__('Delay time (seconds)','featured-slider').'</label> 
		<input type="text" name="'.$featured_slider_options.'[sub_ptitle_delay]" id="featured_slider_sub_ptitle_delay" class="eb-gsmalltext" value="'.$featured_slider_curr['sub_ptitle_delay'].'" />
	</div>
	
	</div><!--eb-altcolorinner-->
	</div><!--eb-altcolor-->


	<div class="settings-tbl">
	<div class="ebs-row">
		<label>'.__('Content','featured-slider').'</label>
		<div class="eb-switch">
			<input type="hidden" name="'.$featured_slider_options.'[show_content]" id="featured_slider_show_content" class="hidden_check" value="'.$featured_slider_curr['show_content'].'">
			<input id="featured_showcontent" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['show_content'],false).'>
			<label for="featured_showcontent"></label>
		</div>
 	</div>

	<div class="ebs-row">
		<label class="eb-slabel">'.__('Font','featured-slider').'</label>			
		<!-- code for new fonts - 3.0 -->
		<input type="hidden" value="content_font" class="ftype_rname">
		<input type="hidden" value="content_fontg" class="ftype_gname">
		<input type="hidden" value="pcfont_custom" class="ftype_cname">
		<select name="'.$featured_slider_options.'[pc_font]" id="featured_slider_pc_font" class="main-font">
			<option value="regular" '.selected( $featured_slider_curr['pc_font'], "regular",false ).' > Regular Fonts </option>
			<option value="google" '.selected( $featured_slider_curr['pc_font'], "google",false ).' > Google Fonts </option>
			<option value="custom" '.selected( $featured_slider_curr['pc_font'], "custom",false ).' > Custom Fonts </option>
		</select>
		<div class="load-fontdiv"></div>
	</div>

	<div class="ebs-row">
		<label>'.__('Color','featured-slider').'</label>
		<input type="text" name="'.$featured_slider_options.'[content_fcolor]" id="featured_slider_content_fcolor" value="'.$featured_slider_curr['content_fcolor'].'" class="wp-color-picker-field" data-default-color="#ffffff" />
	</div>

	<div class="ebs-row">
		<label>'.__('Size','featured-slider').'</label>
		<input type="number" name="'.$featured_slider_options.'[content_fsize]" id="featured_slider_content_fsize" class="small-text" value="'.$featured_slider_curr['content_fsize'].'" min="1" />
	</div>

	<div class="ebs-row font-style">	
		<label>'.__('Style','featured-slider').'</label>
		<select name="'.$featured_slider_options.'[content_fstyle]" id="featured_slider_content_fstyle" >
			<option value="bold" '.selected("bold",$featured_slider_curr['content_fstyle'],false).' >'.__('Bold','featured-slider').'</option>
			<option value="bold italic" '.selected("bold italic",$featured_slider_curr['content_fstyle'],false).' >'.__('Bold Italic','featured-slider').'</option>
			<option value="italic" '.selected("italic",$featured_slider_curr['content_fstyle'],false).' >'.__('Italic','featured-slider').'</option>
			<option value="normal" '.selected("normal",$featured_slider_curr['content_fstyle'],false).' >'.__('Normal','featured-slider').'</option>
		</select>
	</div>

	<div class="ebs-row">
		<label>'.__('source','featured-slider').'</label>
		<select name="'.$featured_slider_options.'[content_from]" id="featured_slider_content_from" >
			<option value="slider_content" '.selected("slider_content",$featured_slider_curr['content_from'],false).' >'.__('Slider Content Custom field','featured-slider').'</option>
			<option value="excerpt" '.selected("excerpt",$featured_slider_curr['content_from'],false).' >'.__('Post Excerpt','featured-slider').'</option>
			<option value="content" '.selected("content",$featured_slider_curr['content_from'],false).' >'.__('From Content','featured-slider').'</option>
		</select>
	</div>

	<div class="ebs-row">
		<label class="eb-slabel">'.__('Length','featured-slider').'</label>		
		<div class="eb-lrightdiv">
			<div>
				<input id="featured_slider_climit" name="'.$featured_slider_options.'[climit]" type="radio" value="0" '.checked('0', $featured_slider_curr['climit'],false).'  />
				<label class="eb-mlabel">'.__(' words','featured-slider').'</label>
				<input type="number" name="'.$featured_slider_options.'[content_limit]" id="featured_slider_content_limit" class="small-text" value="'.$featured_slider_curr['content_limit'].'" min="1" />
			</div>
			<div class="eb-margindiv">
				<input id="featured_slider_climit" name="'.$featured_slider_options.'[climit]" type="radio" value="1" '.checked('1', $featured_slider_curr['climit'],false).'  />
				<label class="eb-mlabel">'.__(' Characters','featured-slider').'</label>
				<input type="number" name="'.$featured_slider_options.'[content_chars]" id="featured_slider_content_chars" class="small-text" value="'.$featured_slider_curr['content_chars'].'" min="1"/>
			</div>
		</div>	
	</div>

	<div class="ebs-row">
		<label style="width: 38%;">'.__('Transition','featured-slider').'</label>'; 
		$content_tran_name = $featured_slider_options.'[content_transition]';
		$html.=get_featured_transitions($content_tran_name,$featured_slider_curr['content_transition']).' 
	</div>

	<div class="ebs-row">
		<label class="eb-llabel">'.__('Duration (seconds)','featured-slider').'</label> 
		<input type="text" name="'.$featured_slider_options.'[content_duration]" id="featured_slider_content_duration" class="eb-gsmalltext" value="'.$featured_slider_curr['content_duration'].'" />
	</div>

	<div class="ebs-row">
		<label class="eb-llabel">'.__('Delay time (seconds)','featured-slider').'</label> 
		<input type="text" name="'.$featured_slider_options.'[content_delay]" id="featured_slider_content_delay" class="eb-gsmalltext" value="'.$featured_slider_curr['content_delay'].'" />
	</div>
	
	</div> 
	
	<div class="eb-altcolor">
	<div class="eb-altcolorinner settings-tbl">
	
 	<div class="ebs-row">
		<label>'.__('Meta','featured-slider').'</label>
		<div class="eb-switch">
			<input type="hidden" name="'.$featured_slider_options.'[show_meta]" id="featured_slider_show_meta" class="hidden_check" value="'.$featured_slider_curr['show_meta'].'">
			<input id="featured_showmeta" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['show_meta'],false).'>
			<label for="featured_showmeta"></label>
		</div>
 	</div>

	<div class="ebs-row">
		<label>'.__('Meta Field 1','featured-slider').'</label>	
	</div>

	<div class="ebs-row">
		<label for="'.$featured_slider_options.'[meta1_fn]" class="eb-mlabel">Function</label>
		<input type="text" name="'.$featured_slider_options.'[meta1_fn]" class="eb-ltext" value="'.$featured_slider_curr['meta1_fn'].'" /> 
	</div>

	<div class="ebs-row">
		<label for="'.$featured_slider_options.'[meta1_parms]" class="eb-mlabel">Parameters</label>
		<input type="text" name="'.$featured_slider_options.'[meta1_parms]" class="eb-ltext" value="'.$featured_slider_curr['meta1_parms'].'" /> 
	</div>

	<div class="ebs-row">
		<label for="'.$featured_slider_options.'[meta1_before]" class="eb-mlabel">Before Text</label>
		<input type="text" name="'.$featured_slider_options.'[meta1_before]" class="eb-ltext" value="'.htmlentities( $featured_slider_curr['meta1_before'] , ENT_QUOTES).'" /> 
	</div>
	
	<div class="ebs-row">
		<label for="'.$featured_slider_options.'[meta1_after]" class="eb-mlabel">After Text</label>
		<input type="text" name="'.$featured_slider_options.'[meta1_after]" class="eb-ltext" value="'.$featured_slider_curr['meta1_after'].'" /> 
	</div>

	<div class="ebs-row">
		<label class="eb-mlabel">'.__('Meta Field 2','featured-slider').'</label>	
	</div>
	<div class="ebs-row">
		<label for="'.$featured_slider_options.'[meta2_fn]" class="eb-mlabel">Function</label>
		<input type="text" name="'.$featured_slider_options.'[meta2_fn]" class="eb-ltext" value="'.$featured_slider_curr['meta2_fn'].'" /> 
	</div>
	<div class="ebs-row">
		<label for="'.$featured_slider_options.'[meta2_parms]" class="eb-mlabel">Parameters</label>
		<input type="text" name="'.$featured_slider_options.'[meta2_parms]" class="eb-ltext" value="'.$featured_slider_curr['meta2_parms'].'" />
	</div>
	<div class="ebs-row">
		<label for="'.$featured_slider_options.'[meta2_before]" class="eb-mlabel">Before Text</label>
		<input type="text" name="'.$featured_slider_options.'[meta2_before]" class="eb-ltext" value="'.htmlentities( $featured_slider_curr['meta2_before'] , ENT_QUOTES).'" />
	</div>
	<div class="ebs-row">
		<label for="'.$featured_slider_options.'[meta2_after]" class="eb-mlabel">After Text</label>
		<input type="text" name="'.$featured_slider_options.'[meta2_after]" class="eb-ltext" value="'.$featured_slider_curr['meta2_after'].'" />
	</div>
		<div class="ebs-row">
			<label class="eb-slabel">'.__('Font','featured-slider').'</label>			
			<!-- code for new fonts - 3.0 -->
			<input type="hidden" value="meta_title_font" class="ftype_rname">
			<input type="hidden" value="meta_title_fontg" class="ftype_gname">
			<input type="hidden" value="mtfont_custom" class="ftype_cname">
			<select name="'.$featured_slider_options.'[mt_font]" id="featured_slider_mt_font" class="main-font">
				<option value="regular" '.selected( $featured_slider_curr['mt_font'], "regular", false ).' > Regular Fonts </option>
				<option value="google" '.selected( $featured_slider_curr['mt_font'], "google", false ).' > Google Fonts </option>
				<option value="custom" '.selected( $featured_slider_curr['mt_font'], "custom", false ).' > Custom Fonts </option>
			</select>	
			<div class="load-fontdiv"></div>
		</div>


		<div class="ebs-row">
			<label>'.__('Font Size','featured-slider').'</label>	
				<input type="number" name="'.$featured_slider_options.'[meta_title_fsize]" id="featured_slider_meta_title_fsize" class="small-text eb-numright" value="'.$featured_slider_curr['meta_title_fsize'].'" min="1" />
		</div>

		<div class="ebs-row">
			<label>'.__('Color','featured-slider').'</label>
				<input type="text" name="'.$featured_slider_options.'[meta_title_fcolor]" id="featured_slider_meta_title_fcolor" value="'.$featured_slider_curr['meta_title_fcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" />
		</div>
	
		<div class="ebs-row font-style">
			<label>'.__('Style','featured-slider').'</label>
				<select name="'.$featured_slider_options.'[meta_title_fstyle]" id="featured_slider_meta_title_fstyle" >
					<option value="bold" '.selected("bold",$featured_slider_curr['meta_title_fstyle'],false).' >'.__('Bold','featured-slider').'</option>
					<option value="bold italic" '.selected("bold italic",$featured_slider_curr['meta_title_fstyle'],false).' >'.__('Bold Italic','featured-slider').'</option>
					<option value="italic" '.selected("italic",$featured_slider_curr['meta_title_fstyle'],false).' >'.__('Italic','featured-slider').'</option>
					<option value="normal" '.selected("normal",$featured_slider_curr['meta_title_fstyle'],false).' >'.__('Normal','featured-slider').'</option>
				</select>
		</div>

	</div><!--eb-altcolorinner-->
	</div><!--eb-altcolor-->

		<p class="submit">
			<input type="submit" class="button-primary" name="save_eb_settings" value="'.__('Save Changes','featured-slider').'" />
			<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />		
		</p>';

	} elseif($tab == 'nav') {
	
		$html.='<div class="ebs-row">
		<label>'.__('Arrows','featured-slider').'</label>
			<div class="eb-switch">
				<input type="hidden" name="'.$featured_slider_options.'[prev_next]" id="featured_slider_prev_next" class="hidden_check" value="'.$featured_slider_curr['prev_next'].'">
				<input id="featured_disablearrow" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1',$featured_slider_curr['prev_next'], false).' />
				<label for="featured_disablearrow"></label>
			</div>
		</div>
		<div class="ebs-row">
			<label>'.__('Choose Arrow','featured-slider').'</label>
			<div style="background: #ccc;">';
			 
			$directory = FEATURED_SLIDER_CSS_OUTER.'/buttons/';
			if ($handle = opendir($directory)) {
			    while (false !== ($file = readdir($handle))) { 
			     if($file != '.' and $file != '..') { 
			     $nexturl='var/buttons/'.$file.'/next.png';
			$html .='<div class="arrows"><img src="'.featured_slider_plugin_url($nexturl).'" width="64" height="64"/>
			<input name="'.$featured_slider_options.'[buttons]" type="radio" id="featured_slider_buttons" class="arrows_input" value="'.$file.'" '.checked($file,$featured_slider_curr['buttons'],false).' /></div>';
			} }
			    closedir($handle);
			}
			$html.='</div>
		</div>
		
		<div class="ebs-row">
			<label class="eb-slabel">'.__('Size','featured-slider').'</label> 
			<span class="eb-span">W</span>
			<input type="number" name="'.$featured_slider_options.'[nav_w]" id="featured_slider_nav_w" class="small-text" value="'.$featured_slider_curr['nav_w'].'" min="1" />
			<span class="eb-span">H</span>
			<input type="number" name="'.$featured_slider_options.'[nav_h]" id="featured_slider_nav_h" class="small-text" value="'.$featured_slider_curr['nav_h'].'" min="1" />
		</div>
		
		<div class="ebs-row">
			<label>'.__('Distance from left and right','featured-slider').'</label> 
			<input type="number" name="'.$featured_slider_options.'[nav_margin]" id="featured_slider_nav_margin" class="small-text" value="'.$featured_slider_curr['nav_margin'].'" min="0" />&nbsp;px
		</div>
		<p class="submit">
			<input type="submit" class="button-primary" name="save_eb_settings" value="'.__('Save Changes','featured-slider').'" />
			<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />		
		</p>';
	} elseif($tab == 'events') {
		$html .= '<div class="settings-tbl">
	<div class="ebs-row">
		<label>'.__('Event date-time','featured-slider').'</label>
		<div class="eb-switch">
			<input type="hidden" name="'.$featured_slider_options.'[enable_eventdt]" id="featured_slider_enable_eventdt" class="hidden_check" value="'.$featured_slider_curr['enable_eventdt'].'">
			<input id="featured_enableeventdt" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['enable_eventdt'],false).'>
			<label for="featured_enableeventdt"></label>
		</div> 
	</div>
	<div class="ebs-row">
	
		<label class="eb-slabel">'.__('Font','featured-slider').'</label>			
						<!-- code for new fonts - 3.0 -->
						<input type="hidden" value="slide_eventm_font" class="ftype_rname">
						<input type="hidden" value="slide_eventm_fontg" class="ftype_gname">
						<input type="hidden" value="slide_eventm_custom" class="ftype_cname">
						<select name="'.$featured_slider_options.'[eventmd_font]" id="featured_slider_eventmd_font" class="main-font">
						<option value="regular" '.selected( $featured_slider_curr['eventmd_font'], "regular",false ).' > Regular Fonts </option>
						<option value="google" '.selected( $featured_slider_curr['eventmd_font'], "google",false ).' > Google Fonts </option>
						<option value="custom" '.selected( $featured_slider_curr['eventmd_font'], "custom",false ).' > Custom Fonts </option>
						</select>	
				<div class="load-fontdiv"></div>
	</div>
	
	<div class="ebs-row">
	<label>'.__('Font Color','featured-slider').'</label>
		<input type="text" name="'.$featured_slider_options.'[slide_eventm_fcolor]" id="featured_slide_woocat_fcolor" value="'.$featured_slider_curr['slide_eventm_fcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" />
	</div>

		<div class="ebs-row">
			<label>'.__('Font Size','featured-slider').'</label>
			<input type="number" name="'.$featured_slider_options.'[slide_eventm_fsize]" id="featured_slide_woocat_fsize" class="small-text" value="'.$featured_slider_curr['slide_eventm_fsize'].'" min="1" />
		</div>

		<div class="ebs-row font-style">
		<label>'.__('Font Style','featured-slider').'</label>
		<select name="'.$featured_slider_options.'[slide_eventm_fstyle]" id="featured_slide_woocat_fstyle" >
			<option value="bold" '.selected("bold",$featured_slider_curr['slide_eventm_fstyle'], false).' >'.__('Bold','featured-slider').'</option>
			<option value="bold italic" '.selected("bold italic",$featured_slider_curr['slide_eventm_fstyle'], false).' >'.__('Bold Italic','featured-slider').'</option>
			<option value="italic" '.selected("italic",$featured_slider_curr['slide_eventm_fstyle'], false).' >'.__('Italic','featured-slider').'</option>
			<option value="normal" '.selected("normal",$featured_slider_curr['slide_eventm_fstyle'], false).' >'.__('Normal','featured-slider').'</option>
		</select>
	</div>
	</div>

	<div class="eb-altcolor">
		<div class="eb-altcolorinner settings-tbl">
			<div class="ebs-row">
			<label>'.__('Event category','featured-slider').'</label>
			<div class="eb-switch">
				<input type="hidden" name="'.$featured_slider_options.'[enable_eventcat]" id="featured_slider_enable_eventcat" class="hidden_check" value="'.$featured_slider_curr['enable_eventcat'].'">
				<input id="featured_enableeventcat" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['enable_eventcat'],false).'>
				<label for="featured_enableeventcat"></label>
			</div> 
		</div>

		<div class="ebs-row">	
		<label class="eb-slabel">'.__('Font','featured-slider').'</label>			
							<!-- code for new fonts - 3.0 -->
							<input type="hidden" value="eventm_cat_font" class="ftype_rname">
							<input type="hidden" value="eventm_cat_fontg" class="ftype_gname">
							<input type="hidden" value="eventm_cat_custom" class="ftype_cname">
							<select name="'.$featured_slider_options.'[event_cat_font]" id="featured_slider_event_cat_font" class="main-font">
								<option value="regular" '.selected( $featured_slider_curr['event_cat_font'], "regular", false ).' > Regular Fonts </option>
								<option value="google" '.selected( $featured_slider_curr['event_cat_font'], "google", false ).' > Google Fonts </option>
								<option value="custom" '.selected( $featured_slider_curr['event_cat_font'], "custom", false ).' > Custom Fonts </option>
							</select>	
					<div class="load-fontdiv"></div>
		</div>
	<div class="ebs-row">
	<label>'.__('Font Color','featured-slider').'</label>
		<input type="text" name="'.$featured_slider_options.'[eventm_cat_fcolor]" id="featured_slide_woocat_fcolor" value="'.$featured_slider_curr['eventm_cat_fcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" />
	</div>

	<div class="ebs-row">
		<label>'.__('Font Size','featured-slider').'</label>
		<input type="number" name="'.$featured_slider_options.'[eventm_cat_fsize]" id="featured_slide_woocat_fsize" class="small-text" value="'.$featured_slider_curr['eventm_cat_fsize'].'" min="1" />
	</div>

	<div class="ebs-row font-style">
	<label>'.__('Font Style','featured-slider').'</label>
	<select name="'.$featured_slider_options.'[eventm_cat_fstyle]" id="featured_slide_woocat_fstyle" >
	<option value="bold" '.selected("bold",$featured_slider_curr['slide_woo_cat_fstyle'],false).' >'.__('Bold','featured-slider').'</option>
		<option value="bold italic" '.selected("bold italic",$featured_slider_curr['slide_woo_cat_fstyle'],false).' >'.__('Bold Italic','featured-slider').'</option>
		<option value="italic" '.selected("italic",$featured_slider_curr['slide_woo_cat_fstyle'],false).' >'.__('Italic','featured-slider').'</option>
		<option value="normal" '.selected("normal",$featured_slider_curr['slide_woo_cat_fstyle'],false).' >'.__('Normal','featured-slider').'</option>
	</select>
	</div>
	</div><!--eb-altcolorinner-->
	</div><!--eb-altcolor-->
	<div class="settings-tbl">
		<div class="ebs-row">
		<label>'.__('Event address','featured-slider').'</label>
		<div class="eb-switch">
			<input type="hidden" name="'.$featured_slider_options.'[enable_eventadd]" id="featured_slider_enable_eventadd" class="hidden_check" value="'.$featured_slider_curr['enable_eventadd'].'">
			<input id="featured_enableeventadd" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['enable_eventadd'],false).'>
			<label for="featured_enableeventadd"></label>
		</div> 
		</div>
		<div class="ebs-row">
					<label class="eb-slabel">'.__('Font','featured-slider').'</label>			
							<!-- code for new fonts - 3.0 -->
							<input type="hidden" value="eventm_addr_font" class="ftype_rname">
							<input type="hidden" value="eventm_addr_fontg" class="ftype_gname">
							<input type="hidden" value="eventm_addr_custom" class="ftype_cname">
							<select name="'.$featured_slider_options.'[event_addr_font]" id="featured_slider_event_addr_font" class="main-font">
								<option value="regular" '.selected( $featured_slider_curr['event_addr_font'], "regular", false ).' > Regular Fonts </option>
								<option value="google" '.selected( $featured_slider_curr['event_addr_font'], "google", false ).' > Google Fonts </option>
								<option value="custom" '.selected( $featured_slider_curr['event_addr_font'], "custom", false ).' > Custom Fonts </option>
							</select>	
					<div class="load-fontdiv"></div>
		</div>

	<div class="ebs-row">
		<label>'.__('Font Color','featured-slider').'</label>
			<input type="text" name="'.$featured_slider_options.'[eventm_addr_fcolor]" id="featured_slide_woocat_fcolor" value="'.$featured_slider_curr['eventm_addr_fcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" />
	</div>

	<div class="ebs-row">
		<label>'.__('Font Size','featured-slider').'</label>
			<input type="number" name="'.$featured_slider_options.'[eventm_addr_fsize]" id="featured_slide_woocat_fsize" class="small-text" value="'.$featured_slider_curr['eventm_addr_fsize'].'" min="1" /></div>

	<div class="ebs-row font-style">
	<label>'.__('Font Style','featured-slider').'</label>
	<select name="'.$featured_slider_options.'[eventm_addr_fstyle]" id="featured_slide_woocat_fstyle" >
		<option value="bold" '.selected("bold",$featured_slider_curr['eventm_addr_fstyle'],false).' >'.__('Bold','featured-slider').'</option>
		<option value="bold italic" '.selected("bold italic",$featured_slider_curr['eventm_addr_fstyle'],false).' >'.__('Bold Italic','featured-slider').'</option>
		<option value="italic" '.selected("italic",$featured_slider_curr['eventm_addr_fstyle'],false).' >'.__('Italic','featured-slider').'</option>
		<option value="normal" '.selected("normal",$featured_slider_curr['eventm_addr_fstyle'],false).' >'.__('Normal','featured-slider').'</option>
	</select>
	</div>
	</div>

	<p class="submit">
		<input type="submit" class="button-primary" name="save_eb_settings" value="'.__('Save Changes','featured-slider').'" />
		<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
	</p>';
	
	} elseif($tab == 'eshop') {
	$html .= '<div class="ebs-row">
	<label>'.__('Slide Add to cart ','featured-slider').'</label>
	<div class="eb-switch eb-switchnone">
		<input type="hidden" name="'.$featured_slider_options.'[enable_wooaddtocart]" id="featured_slider_enable_wooaddtocart" class="hidden_check" value="'.$featured_slider_curr['enable_wooaddtocart'].'">
		<input id="featured_enablewooaddtocartsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['enable_wooaddtocart'],false).'>
		<label for="featured_enablewooaddtocartsett"></label>
	</div>
</div>
<div class="ebs-row">
	<label>'.__('Button Text','featured-slider').'</label>
	<input type="text" name="'.$featured_slider_options.'[woo_adc_text]" id="featured_woo_adc_text" class="eb-smalltext" value="'.$featured_slider_curr['woo_adc_text'].'" />
</div>

<div class="ebs-row">
<label>'.__('Button Color','featured-slider').'</label>
	<input type="text" name="'.$featured_slider_options.'[woo_adc_color]" id="featured_woo_adc_color" value="'.$featured_slider_curr['woo_adc_color'].'" class="wp-color-picker-field" data-default-color="#3DB432" />
</div>

<div class="ebs-row">
<label>'.__('Button text Color','featured-slider').'</label>
	<input type="text" name="'.$featured_slider_options.'[woo_adc_tcolor]" id="featured_woo_adc_tcolor" value="'.$featured_slider_curr['woo_adc_tcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" /></div>

<div class="ebs-row">
<label>'.__('Button Size','featured-slider').'</label>
	<input type="number" name="'.$featured_slider_options.'[woo_adc_fsize]" id="featured_woo_adc_fsize" class="small-text" value="'.$featured_slider_curr['woo_adc_fsize'].'" min="1" />
</div>


<div class="ebs-row">
<label>'.__('Border Thickness','featured-slider').'</label>
	<input type="number" name="'.$featured_slider_options.'[woo_adc_border]" id="featured_woo_adc_border" class="small-text" value="'.$featured_slider_curr['woo_adc_border'].'" min="0" />
</div>

<div class="ebs-row">
<label>'.__('Border Color','featured-slider').'</label>
	<input type="text" name="'.$featured_slider_options.'[woo_adc_brcolor]" id="featured_woo_adc_brcolor" value="'.$featured_slider_curr['woo_adc_brcolor'].'" class="wp-color-picker-field" data-default-color="#D8E7EE" /></div>

<div class="eb-altcolor">
<div class="eb-altcolorinner settings-tbl">
<div class="ebs-row">
	<label>'.__('Slide Regular price ','featured-slider').'</label>
	<div class="eb-switch eb-switchnone">
		<input type="hidden" name="'.$featured_slider_options.'[enable_wooregprice]" id="featured_slider_enable_wooregprice" class="hidden_check" value="'.$featured_slider_curr['enable_wooregprice'].'">
		<input id="featured_enablewooregpricesett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['enable_wooregprice'],false).'>
		<label for="featured_enablewooregpricesett"></label>
	</div> 
</div>
<div class="ebs-row">
		<label class="eb-slabel">'.__('Font','featured-slider').'</label>			
						<!-- code for new fonts - 3.0 -->
						<input type="hidden" value="slide_woo_price_font" class="ftype_rname">
						<input type="hidden" value="slide_woo_price_fontg" class="ftype_gname">
						<input type="hidden" value="slide_woo_price_custom" class="ftype_cname">
						<select name="'.$featured_slider_options.'[woo_font]" id="featured_slider_woo_font" class="main-font">
							<option value="regular" '.selected( $featured_slider_curr['woo_font'], "regular", false ).' > Regular Fonts </option>
							<option value="google" '.selected( $featured_slider_curr['woo_font'], "google", false ).' > Google Fonts </option>
							<option value="custom" '.selected( $featured_slider_curr['woo_font'], "custom", false ).' > Custom Fonts </option>
						</select>
				<div class="load-fontdiv"></div>
</div>

<div class="ebs-row">
<label>'.__('Font Color','featured-slider').'</label>
	<input type="text" name="'.$featured_slider_options.'[slide_woo_price_fcolor]" id="featured_slide_wooprice_fcolor" value="'.$featured_slider_curr['slide_woo_price_fcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" /></div>

<div class="ebs-row">
<label>'.__('Font Size','featured-slider').'</label>
	<input type="number" name="'.$featured_slider_options.'[slide_woo_price_fsize]" id="featured_slide_wooprice_fsize" class="small-text" value="'.$featured_slider_curr['slide_woo_price_fsize'].'" min="1" />
</div>

<div class="ebs-row font-style">
<label>'.__('Font Style','featured-slider').'</label>
<select name="'.$featured_slider_options.'[slide_woo_price_fstyle]" id="featured_slide_wooprice_fstyle" >
			<option value="bold" '.selected("bold",$featured_slider_curr['slide_woo_price_fstyle'],false).' >'.__('Bold','featured-slider').'</option>
			<option value="bold italic" '.selected("bold italic",$featured_slider_curr['slide_woo_price_fstyle'],false).' >'.__('Bold Italic','featured-slider').'</option>
			<option value="italic" '.selected("italic",$featured_slider_curr['slide_woo_price_fstyle'],false).' >'.__('Italic','featured-slider').'</option>
			<option value="normal" '.selected("normal",$featured_slider_curr['slide_woo_price_fstyle'],false).' >'.__('Normal','featured-slider').'</option>
</select>
</div>

</div><!--eb-altcolorinner-->
</div><!--eb-altcolor-->
<div class="settings-tbl">
<div class="ebs-row">
	<label>'.__('Slide Sale price','featured-slider').'</label>
	<div class="eb-switch eb-switchnone">
		<input type="hidden" name="'.$featured_slider_options.'[enable_woosprice]" id="featured_slider_enable_woosprice" class="hidden_check" value="'.$featured_slider_curr['enable_woosprice'].'">
		<input id="featured_enablewoospricesett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['enable_woosprice'],false).'>
		<label for="featured_enablewoospricesett"></label>
	</div> 
</div>
	<div class="ebs-row"> 
	<label class="eb-slabel">'.__('Font','featured-slider').'</label>			
						<!-- code for new fonts - 3.0 -->
						<input type="hidden" value="slide_woo_saleprice_font" class="ftype_rname">
						<input type="hidden" value="slide_woo_saleprice_fontg" class="ftype_gname">
						<input type="hidden" value="slide_woo_saleprice_custom" class="ftype_cname">
						<select name="'.$featured_slider_options.'[woosale_font]" id="featured_slider_woosale_font" class="main-font">
							<option value="regular" '.selected( $featured_slider_curr['woosale_font'], "regular", false ).' > Regular Fonts </option>
							<option value="google" '.selected( $featured_slider_curr['woosale_font'], "google", false ).' > Google Fonts </option>
							<option value="custom" '.selected( $featured_slider_curr['woosale_font'], "custom", false ).' > Custom Fonts </option>
						</select>	
				<div class="load-fontdiv"></div>
	</div>

<div class="ebs-row">
<label>'.__('Font Color','featured-slider').'</label>
	<input type="text" name="'.$featured_slider_options.'[slide_woo_saleprice_fcolor]" id="featured_slide_woosaleprice_fcolor" value="'.$featured_slider_curr['slide_woo_saleprice_fcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" /></div>

<div class="ebs-row">
<label>'.__('Font Size','featured-slider').'</label>
	<input type="number" name="'.$featured_slider_options.'[slide_woo_saleprice_fsize]" id="featured_slide_woosaleprice_fsize" class="small-text" value="'.$featured_slider_curr['slide_woo_saleprice_fsize'].'" min="1" />
</div>

<div class="ebs-row font-style">
<label>'.__('Font Style','featured-slider').'</label>
<select name="'.$featured_slider_options.'[slide_woo_saleprice_fstyle]" id="featured_slide_woosaleprice_fstyle" >
		<option value="bold" '.selected("bold",$featured_slider_curr['slide_woo_saleprice_fstyle'],false).' >'.__('Bold','featured-slider').'</option>
		<option value="bold italic" '.selected("bold italic",$featured_slider_curr['slide_woo_saleprice_fstyle'],false).' >'.__('Bold Italic','featured-slider').'</option>
		<option value="italic" '.selected("italic",$featured_slider_curr['slide_woo_saleprice_fstyle'],false).' >'.__('Italic','featured-slider').'</option>
		<option value="normal" '.selected("normal",$featured_slider_curr['slide_woo_saleprice_fstyle'],false).' >'.__('Normal','featured-slider').'</option>
</select>
</div>
</div>

<div class="eb-altcolor">
<div class="eb-altcolorinner settings-tbl">
<div class="ebs-row">
	<label>'.__('Slide Category','featured-slider').'</label>
	<div class="eb-switch eb-switchnone">
		<input type="hidden" name="'.$featured_slider_options.'[enable_woocat]" id="featured_slider_enable_woocat" class="hidden_check" value="'.$featured_slider_curr['enable_woocat'].'">
		<input id="featured_enablewoocatsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['enable_woocat'],false).'>
		<label for="featured_enablewoocatsett"></label>
	</div> 
</div>
<div class="ebs-row"> 
	<label class="eb-slabel">'.__('Font','featured-slider').'</label>			
						<!-- code for new fonts - 3.0 -->
						<input type="hidden" value="slide_woo_cat_font" class="ftype_rname">
						<input type="hidden" value="slide_woo_cat_fontg" class="ftype_gname">
						<input type="hidden" value="slide_woo_cat_custom" class="ftype_cname">
						<select name="'.$featured_slider_options.'[woocat_font]" id="featured_slider_woocat_font" class="main-font">
							<option value="regular" '.selected( $featured_slider_curr['woocat_font'], "regular",false ).' > Regular Fonts </option>
							<option value="google" '.selected( $featured_slider_curr['woocat_font'], "google",false ).' > Google Fonts </option>
							<option value="custom" '.selected( $featured_slider_curr['woocat_font'], "custom",false ).' > Custom Fonts </option>
						</select>	
				<div class="load-fontdiv"></div>
</div>

<div class="ebs-row">
<label>'.__('Font Color','featured-slider').'</label>
	<input type="text" name="'.$featured_slider_options.'[slide_woo_cat_fcolor]" id="featured_slide_woocat_fcolor" value="'.$featured_slider_curr['slide_woo_cat_fcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" /></div>

<div class="ebs-row">
<label>'.__('Font Size','featured-slider').'</label>
	<input type="number" name="'.$featured_slider_options.'[slide_woo_cat_fsize]" id="featured_slide_woocat_fsize" class="small-text" value="'.$featured_slider_curr['slide_woo_cat_fsize'].'" min="1" />
</div>

<div class="ebs-row font-style">
	<label>'.__('Font Style','featured-slider').'</label>
	<select name="'.$featured_slider_options.'[slide_woo_cat_fstyle]" id="featured_slide_woocat_fstyle" >
			<option value="bold" '.selected("bold",$featured_slider_curr['slide_woo_cat_fstyle'],false).' >'.__('Bold','featured-slider').'</option>
			<option value="bold italic" '.selected("bold italic",$featured_slider_curr['slide_woo_cat_fstyle'],false).' >'.__('Bold Italic','featured-slider').'</option>
			<option value="italic" '.selected("italic",$featured_slider_curr['slide_woo_cat_fstyle'],false).' >'.__('Italic','featured-slider').'</option>
			<option value="normal" '.selected("normal",$featured_slider_curr['slide_woo_cat_fstyle'],false).' >'.__('Normal','featured-slider').'</option>
	</select>
</div>
</div><!--eb-altcolorinner-->
</div><!--eb-altcolor-->
<div class="ebs-row">
	<label>'.__('Stars','featured-slider').'</label>
	<div class="eb-switch eb-switchnone">
		<input type="hidden" name="'.$featured_slider_options.'[enable_woostar]" id="featured_slider_enable_woostar" class="hidden_check" value="'.$featured_slider_curr['enable_woostar'].'">
		<input id="featured_enablewoostarsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['enable_woostar'],false).'>
		<label for="featured_enablewoostarsett"></label>
	</div> 
</div>
<div class="ebs-row">
	<div style="display: flex;">';
	$url='images/star/gold.png';
	$url1='images/star/black.png';
	$url2='images/star/red.png'; 
	$url3='images/star/green.png';
	$url4='images/star/grogreen.png';
	$url5='images/star/yellow.png';
	$url6='images/star/grored.png';
	$url7='images/star/groyellow.png';

	$html .= '</div>
	<div class="arrows"><img src="'.featured_slider_plugin_url($url).'" width="16" height="16"/>
			<input name="'.$featured_slider_options.'[nav_woo_star]" type="radio" id="featured_nav_woo_star1" class="woo_star" value="gold" '.checked('gold',$featured_slider_curr['nav_woo_star'],false).' /> </div>

			<div class="arrows"><img src="'.featured_slider_plugin_url($url1).'" width="16" height="16"/>
			<input name="'.$featured_slider_options.'[nav_woo_star]" type="radio" id="featured_nav_woo_star2" class="woo_star" value="black" '.checked('black',$featured_slider_curr['nav_woo_star'],false).' /> </div>

			<div class="arrows"><img src="'.featured_slider_plugin_url($url2).'" width="16" height="16"/>
			<input name="'.$featured_slider_options.'[nav_woo_star]" type="radio" id="featured_nav_woo_star3" class="woo_star" value="red" '.checked('red',$featured_slider_curr['nav_woo_star'],false).' /> </div>

			<div class="arrows"><img src="'.featured_slider_plugin_url($url3).'" width="16" height="16"/>
			<input name="'.$featured_slider_options.'[nav_woo_star]" type="radio" id="featured_nav_woo_star4" class="woo_star" value="green" '.checked('green',$featured_slider_curr['nav_woo_star'],false).' /> </div>

			<div class="arrows"><img src="'.featured_slider_plugin_url($url4).'" width="16" height="16"/>
			<input name="'.$featured_slider_options.'[nav_woo_star]" type="radio" id="featured_nav_woo_star5" class="woo_star" value="grogreen" '.checked('grogreen',$featured_slider_curr['nav_woo_star'],false).' /> </div>

			<div class="arrows"><img src="'.featured_slider_plugin_url($url5).'" width="16" height="16"/>
			<input name="'.$featured_slider_options.'[nav_woo_star]" type="radio" id="featured_nav_woo_star6" class="woo_star" value="yellow" '.checked('yellow',$featured_slider_curr['nav_woo_star'],false).' /> </div>

			<div class="arrows"><img src="'.featured_slider_plugin_url($url6).'" width="16" height="16"/>
			<input name="'.$featured_slider_options.'[nav_woo_star]" type="radio" id="featured_nav_woo_star7" class="woo_star" value="grored" '.checked('grored',$featured_slider_curr['nav_woo_star'],false).' /> </div>

			<div class="arrows"><img src="'.featured_slider_plugin_url($url7).'" width="16" height="16"/>
			<input name="'.$featured_slider_options.'[nav_woo_star]" type="radio" id="featured_nav_woo_star8" class="woo_star" value="groyellow" '.checked('groyellow',$featured_slider_curr['nav_woo_star'],false).' /> </div>	
			<div style="clear: left;"></div>
	</div>
	<div class="eb-altcolor">
		<div class="eb-altcolorinner">
			<div class="ebs-row">
				<label>'.__('Slide Sale strip ','featured-slider').'</label>
				<div class="eb-switch eb-switchnone">
					<input type="hidden" name="'.$featured_slider_options.'[enable_woosalestrip]" id="featured_slider_enable_woosalestrip" class="hidden_check" value="'.$featured_slider_curr['enable_woosalestrip'].'">
					<input id="featured_enablewoosalestripsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['enable_woosalestrip'],false).'>
					<label for="featured_enablewoosalestripsett"></label>
				</div>
			</div>
			<div class="ebs-row">
				<label>'.__('Strip Text','featured-slider').'</label>
				<input type="text" name="'.$featured_slider_options.'[woo_sale_text]" id="featured_woo_saletext" class="eb-smalltext" value="'.$featured_slider_curr['woo_sale_text'].'" />
			</div>
			<div class="ebs-row">
				<label>'.__('Strip Color','featured-slider').'</label>
				<input type="text" name="'.$featured_slider_options.'[woo_sale_color]" id="featured_woo_salecolor" value="'.$featured_slider_curr['woo_sale_color'].'" class="wp-color-picker-field" data-default-color="#D8E7EE" />
			</div>
			<div class="ebs-row">
				<label>'.__('Text Color','featured-slider').'</label>
				<input type="text" name="'.$featured_slider_options.'[woo_sale_tcolor]" id="featured_woo_saletcolor" value="'.$featured_slider_curr['woo_sale_tcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" />
			</div>
		</div><!--eb-altcolorinner-->
	</div><!--eb-altcolor-->
	<p class="submit">
		<input type="submit" class="button-primary" name="save_eb_settings" value="'.__('Save Changes','featured-slider').'" />
		<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
	</p>
	</div>';
	}
	echo $html;
	die();
}

function featured_delete_slide() {
	check_ajax_referer( 'featured-preview-nonce', 'preview_html' );
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.FEATURED_SLIDER_TABLE;
	$current_slider = isset($_POST['slider_id'])?$_POST['slider_id']:'0'; 
	$post_id = isset($_POST['post_id'])?$_POST['post_id']:'0'; 
	$sql = "DELETE FROM $table_name WHERE post_id = '$post_id' AND slider_id = '$current_slider' LIMIT 1";
	$wpdb->query($sql);
	$current_url = admin_url('admin.php?page=featured-slider-easy-builder');
	$urlarg = array();
	$urlarg['id'] = $current_slider;
	$query_arg = add_query_arg( $urlarg ,$current_url);
	echo $current_url = $query_arg;
	//echo "Deleated Successfully!";
	die();
}
/*
* -----------------------------------------------------------------
*	Create new slider and QuickTag Functions
* -----------------------------------------------------------------
*/
function featured_woo_product() {
	check_ajax_referer( 'featured-slider-nonce', 'featured_slider_pg' );
	$type = $_POST['type'];
	$param   = $_POST['term'];
	$options = array();
	global $wpdb,$table_prefix;
	$tbl = $table_prefix.'posts';
	if($type == 'grouped') {
		$args = array(
			'post_type'	=> 'product',
			'tax_query'	=> array( array('taxonomy' => 'product_type','field' => 'slug','terms' => 'grouped') )
		);
		$grouped = get_posts($args);
		foreach($grouped as $group) {
			if (preg_match('#^'.$param.'#i', $group->post_title) === 1) {
				$options['product'][] = array(
					'ID' => $group->ID,
					'title' => $group->post_title
				);
			}
		}
	} else {
		$sql = "SELECT * FROM $tbl WHERE post_type = 'product' and post_title LIKE '".$param."%'";
		$results = $wpdb->get_results($sql);
		foreach($results as $row ) {
		   // more structure in data allows an easier processing
		   $options['product'][] = array(
			'ID' => $row->ID,
			'title' => $row->post_title
		   );
		}
	}
	echo json_encode($options);
	die();
}                                  
function featured_show_taxonomy() {
	check_ajax_referer( 'featured-slider-nonce', 'featured_slider_pg' );
	$html = '';
	$post_type = isset($_POST['post_type'])?$_POST['post_type']:'';
	$update = isset($_POST['update'])?$_POST['update']:'';
	$quicktag = isset($_POST['quicktag'])?$_POST['quicktag']:'';
	$option = isset($_POST['option'])?$_POST['option']:'';
	$taxonomy_names = get_object_taxonomies( $post_type );
	if($update != '') $html .= '<th  scope="row">'.__('Taxonomy','featured-slider').'</th>';
	elseif($quicktag != '') $html .= '<td  scope="row">'.__('Taxonomy','svslider').'</td>';
	else $html .= '<label>'.__('Taxonomy','featured-slider').'</label>';
	if($update != '') $html .= '<td><select name="taxonomy_name" id="featured_taxonomy" class="taxo-update" >';
	elseif($quicktag != '') $html .= '<td><div class="styled-select"><select name="taxonomy" id="featured_taxonomy"  >';
	elseif($option != '') $html .= '<select name="'.$option.'[taxonomy]" id="featured_taxonomy" >';
	else $html .= '<select name="taxonomy_name" id="featured_taxonomy" class="featured-form-input" >';
	$html .= '<option value="" >Select Taxonomy </option>';
	foreach ( $taxonomy_names as $taxonomy_name ) { 
		$html .= '<option value="'.$taxonomy_name.'" >' . $taxonomy_name . '</option>';
	}
	$html .= '</select>';
	if($update != '') $html .= '</td>';
	elseif($quicktag != '') $html .= '</div></td>';
	echo $html;
	die();
}
function featured_show_term() {
	check_ajax_referer( 'featured-slider-nonce', 'featured_slider_pg' );
	$html = '';
	$taxo = isset($_POST['taxo'])?$_POST['taxo']:'';
	$update = isset($_POST['update'])?$_POST['update']:'';
	$quicktag = isset($_POST['quicktag'])?$_POST['quicktag']:'';
	$preview = isset($_POST['preview'])?$_POST['preview']:'';
	$option = isset($_POST['option'])?$_POST['option']:'';
	$terms = get_terms( $taxo );
	if($update != '') $html .= '<th  scope="row">'.__('Term','featured-slider').'</th>';
	elseif($quicktag != '') $html .= '<td  scope="row">'.__('Term','svslider').'</td>';
	else $html .= '<label>'.__('Term','featured-slider').'</label>';
	if($update != '') $html .= '<td><select name="taxonomy_term[]" id="featured_taxonomy_term" class="featured-form-input" multiple >';
	elseif($quicktag != '') $html .= '<td><select class="featured-multiselect" multiple >';
	elseif($preview != '') $html .= '<select class="featured-multiselect" id="featured_taxonomy_term" multiple >';
	else $html .= '<select name="taxonomy_term[]" id="featured_taxonomy_term" class="featured-form-input" multiple >';
	foreach ( $terms as $term ) { 
		$html .= '<option value="'.$term->slug.'" >' . $term->name . '</option>';
	} 
	$html .= '</select>';
	if($update != '') $html .= '</td>';
	elseif($quicktag != '') $html .= '<input type="hidden" name="term" value="" /></td>'; 
	elseif($preview != '') { 
		if($option != 'undefined' && $option != '') $html .= '<input type="hidden" name="'.$option.'[taxonomy_term]" value="" />';
		else $html .= '<input type="hidden" name="taxonomy_term" value="" />';
	}
	echo $html;
	die();
}
/*
* -----------------------------------------------------------------
*	Google fonts Functions 
* -----------------------------------------------------------------
*/
function featured_google_font_weight() {
	check_ajax_referer( 'featured-google-nonce', 'google_fonts' );
	$arrg=array();
	$html = get_featured_google_font_weight( $currfont = $_POST['font'], $name = $_POST['fname'], $id = $_POST['fid'], $current_value ='' );
	$html_fsubset = get_featured_google_font_subset_html( $currfont = $_POST['font'], $subsetname = $_POST['fsubsetnm'], $subsetid = $_POST['fsubsetid'], $current_value ='' );
	array_push($arrg, $html, $html_fsubset);
	echo json_encode($arrg);
	die();
}
function featured_load_fontsdiv_callback() {
	check_ajax_referer( 'featured-google-nonce', 'google_fonts' );
	$html ='';
	$featured_slider_options = 'featured_slider_options'.$_POST['currcounter'];
	$featured_slider_curr=get_option($featured_slider_options);
	//$featured_slider_curr= populate_rt_current($featured_slider_curr);
	$currpage = $_POST['currpage'];
	$nm = isset($_POST['nm'])?$_POST['nm']:'';
	$type = isset($_POST['font_type'])?$_POST['font_type']:'';
	if( $currpage == 'featured-slider-settings' ) {
		if( $type == 'regular' ) {
			$html.='<table><tr valign="top">
			<th scope="row">'.__('Font','featured-slider') .'</th>
			<td>';
			$dfonts=get_featured_default_fonts($name= $featured_slider_options."[$nm]", $id = "featured_slider_$nm", $class = "havemoreinfo", $current_value=$featured_slider_curr["$nm"] );
			$html.=$dfonts;
			$html.='<span class="moreInfo">
				<div class="tooltip1">'.__('This value will be fallback font if Google web font value is specified below','featured-slider').'
				</div>
			</span>
			</td>
			</tr></table>';
		} else if( $type == 'google' ) {
			$html='';
			$nmgw = $nm.'w';
			$nmgsubset = $nm.'subset';
			$html.='<table><tr valign="top">
			<th scope="row">'.__('Google Web Font','featured-slider').'</th>
			<td>';
				$google_fonts  = get_featured_google_fonts_html( $name = $featured_slider_options."[$nm]", $gid = "featured_slider_$nm", $current_value = $featured_slider_curr["$nm"]);
				$html.=$google_fonts; 
			$html.='</td>	
			</tr>

			<tr valign="top">
			<th scope="row">'.__('Google Font Weight','featured-slider').'</th>
			<td class="google-fontsweight">';
				$google_fw=get_featured_google_font_weight( $currfont = $featured_slider_curr[$nm], $name = $featured_slider_options."[$nmgw]", $id = "featured_slider_$nmgw", $current_value = $featured_slider_curr["$nmgw"] );
				$html.=$google_fw;
			$html.='</td>
			</tr>

			<tr valign="top">
			<th scope="row">'.__('Google Font Subset','featured-slider').'</th>
			<td class="google-fontsubset">';
				$google_fsubset=get_featured_google_font_subset_html($currfont = $featured_slider_curr[$nm], $name = $featured_slider_options."[$nmgsubset][]", $id = "featured_slider_$nmgsubset", $current_value = $featured_slider_curr["$nmgsubset"]);
				$html.=$google_fsubset;
			$html.='</td>
			</tr></table>';
		} else if( $type == 'custom' ) {
			$html.='<table><tr valign="top">
			<th scope="row">'.__('Custom Font','featured-slider').'</th>
			<td>';
				$custom_font=get_featured_custom_font_html($name = $featured_slider_options."[$nm]", $id = "featured_slider_$nm", $current_value = $featured_slider_curr["$nm"]);
				$html.=$custom_font;
			$html.='</td>
			</tr></table>';
		}
	} else { //Easy builder page fonts
			if( $type == 'regular' ) {
			$html.='<div class="ebs-row">
			<lable>'.__('Font','featured-slider') .'</lable>';
			$dfonts=get_featured_default_fonts($name= $featured_slider_options."[$nm]", $id = "featured_slider_$nm", $class = "havemoreinfo", $current_value=$featured_slider_curr["$nm"] );
			$html.=$dfonts;
			$html.='<span class="moreInfo">
				<div class="tooltip1">'.__('This value will be fallback font if Google web font value is specified below','featured-slider').'
				</div>
			</span>
			</div>';
		} else if( $type == 'google' ) {
			$html='';
			$nmgw = $nm.'w';
			$nmgsubset = $nm.'subset';
			$html.='<div class="ebs-row">
			<lable>'.__('Google Web Font','featured-slider').'</lable>';
				$google_fonts  = get_featured_google_fonts_html( $name = $featured_slider_options."[$nm]", $gid = "featured_slider_$nm", $current_value = $featured_slider_curr["$nm"]);
				$html.=$google_fonts; 
			$html.='</div>	

			<div class="ebs-row">	
			<lable>'.__('Google Font Weight','featured-slider').'</lable>
			<div class="google-fontsweight">';
				$google_fw=get_featured_google_font_weight( $currfont = $featured_slider_curr[$nm], $name = $featured_slider_options."[$nmgw]", $id = "featured_slider_$nmgw", $current_value = $featured_slider_curr["$nmgw"] );
				$html.=$google_fw;
			$html.='</div><div>

			<div class="ebs-row">
			<lable>'.__('Google Font Subset','featured-slider').'</lable>
			<div class="google-fontsubset">';
				$google_fsubset=get_featured_google_font_subset_html($currfont = $featured_slider_curr[$nm], $name = $featured_slider_options."[$nmgsubset][]", $id = "featured_slider_$nmgsubset", $current_value = $featured_slider_curr["$nmgsubset"]);
				$html.=$google_fsubset;
			$html.='</div></div>';
		} else if( $type == 'custom' ) {
			$html.='<div class="ebs-row">
			<lable>'.__('Custom Font','featured-slider').'</th>';
				$custom_font=get_featured_custom_font_html($name = $featured_slider_options."[$nm]", $id = "featured_slider_$nm", $current_value = $featured_slider_curr["$nm"]);
				$html.=$custom_font;
			$html.='</div>';
		}
	}
	echo $html;
	die();	
}
/*
* -----------------------------------------------------------------
*	Settings Set Preview Param Function
* -----------------------------------------------------------------
*/
function featured_preview_params() {
	check_ajax_referer( 'featured-settings-nonce', 'settings_nonce' );
	$slider_type = isset($_POST['slider_type'])?$_POST['slider_type']:'';
	$cntr = isset($_POST['cntr'])?$_POST['cntr']:'';
	$featured_slider_options='featured_slider_options'.$cntr;
	$featured_slider_curr=get_option($featured_slider_options);
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	$html = '<th scope="row">'.__('Choose Sub Type','featured-slider').'</th> 
		<td>
			<fieldset>
				<legend class="screen-reader-text"><span>'.__('Preview Slider Params','featured-slider').'</span></legend>';
	$html .= '<label for="'.$featured_slider_options.'[offset]" >'.__('Offset','featured-slider').'</label><input type="number" name="'.$featured_slider_options.'[offset]" value="'.$featured_slider_curr['offset'].'" id="featured_slider_offset" class="small-text code" >';
	if($slider_type == 0 ) {
		//slider names for Custom Slider
		$slider_id = $featured_slider_curr['slider_id'];	
		$sliders = featured_ss_get_sliders();
		$sname_html='<option value="0" selected >Select the Slider</option>';
 		
	  	foreach ($sliders as $slider) { 
		 if($slider['slider_id']==$slider_id ){$selected = 'selected';} else{$selected='';}
			 if($slider['slider_id']!='0') {
			 	$sname_html =$sname_html.'<option value="'.$slider['slider_id'].'" '.$selected.'>'.$slider['slider_name'].'</option>';		 
			}
	  	} 
		
		$html .= '<label for="'.$featured_slider_options.'[slider_id]" class="featured_sid">'.__('Select Slider Name','featured-slider').'</label><select id="featured_slider_id" name="'.$featured_slider_options.'[slider_id]" class="featured_sid">'.$sname_html.'</select>';
	} elseif($slider_type == 1 ) {
		// Categories For Category Slider
		$categories = get_categories();
		$scat_html='<option value="" selected >Select the Category</option>';
		foreach ($categories as $category) { 
			 if($category->slug==$featured_slider_curr['catg_slug']){$selected = 'selected';} else{$selected='';}
			 $scat_html =$scat_html.'<option value="'.$category->slug.'" '.$selected.'>'. $category->name .'</option>';
		} 
		$html .= '<label for="'.$featured_slider_options.'[catg_slug]" class="featured_catslug">'.__('Select Category','featured-slider').'</label><select id="featured_slider_catslug" name="'.$featured_slider_options.'[catg_slug]" class="featured_catslug">'.$scat_html.'</select>';
	} elseif( $slider_type == 3 ) {
		// WooCommerce Slider	
		$pstyle = "display:none;";
		if($featured_slider_curr['woo_type'] == 'recent') { $woor = 'selected'; } else $woor = '';
		if($featured_slider_curr['woo_type'] == 'upsells') { $woou = 'selected'; $pstyle = "display:block;";} else $woou = '';
		if($featured_slider_curr['woo_type'] == 'crosssells') { $wooc = 'selected'; $pstyle = "display:block;"; } else $wooc = '';
		if($featured_slider_curr['woo_type'] == 'external') { $wooe = 'selected'; $pstyle = "display:block;"; } else $wooe = '';
		if($featured_slider_curr['woo_type'] == 'grouped') { $woog = 'selected'; $pstyle = "display:block;"; } else $woog = '';
		// WooCommerce Categories
		if( is_plugin_active('woocommerce/woocommerce.php') ) {
			$wooterms = get_terms('product_cat');
			$catgs = isset($featured_slider_curr['product_woocatg_slug'])?$featured_slider_curr['product_woocatg_slug']:'';
			$catgs_a = explode(",",$catgs);
			if($catgs == '' ) $selc = 'selected'; else $selc = '';
			$woocat_html='<option value="" '.$selc.' >All Category</option>';
			foreach( $wooterms as $woocategory) {
				if($catgs != '' && in_array($woocategory->slug, $catgs_a)){
					$selected = 'selected';
				} else{ $selected=''; }
				$woocat_html =$woocat_html.'<option value="'.$woocategory->slug.'" '.$selected.'>'. $woocategory->name .'</option>';
			}
		}
		$product_id = isset($featured_slider_curr['product_id'])?$featured_slider_curr['product_id']:''; 
		if($product_id != '') $product = get_the_title($product_id);
		$html .= '<label for="'.$featured_slider_options.'[woo_type]" class="featured_woo_type">'.__('Select Slider Type','featured-slider').'</label>
<select name="'.$featured_slider_options.'[woo_type]" class="featured_woo_type" id="woo_slider_preview" >
	<option value="recent" '.$woor.'>'.__('Recent Product Slider','featured-slider').'</option>
	<option value="upsells"'.$woou.'>'.__('Upsells Product Slider','featured-slider').'</option>
	<option value="crosssells"'.$wooc.'>'.__('Crosssells Product Slider','featured-slider').'</option>
	<option value="external"'.$wooe.'>'.__('External Product Slider','featured-slider').'</option>
	<option value="grouped"'.$woog.'>'.__('Grouped Product Slider','featured-slider').'</option>
</select>';
		$html .= '<label for="'.$featured_slider_options.'[product_id]" class="woo-product"  style="'.$pstyle.'" >'.__('Product','featured-slider').'</label><input id="products" value="'.$product.'" class="woo-product" style="'.$pstyle.'" >
		<input id="product_id" name="'.$featured_slider_options.'[product_id]" value="'.$product_id.'" type="hidden" >';
		$html .= '<label for="'.$featured_slider_options.'[product_woocatg_slug]" class="featured_woo_catg">'.__('Select Category','featured-slider').'</label><select id="featured_slider_woo_catslug" class="featured-multiselect" multiple > '.$woocat_html.'</select><input type="hidden" name="'.$featured_slider_options.'[product_woocatg_slug]" value="'.$featured_slider_curr['product_woocatg_slug'].'" />';
	} elseif( $slider_type == 4 ) {
		$ecstyle="display:none;";
		if ($featured_slider_curr['ecom_type'] == "0"){ $rsel = "selected";} else { $rsel = ''; }
		if ($featured_slider_curr['ecom_type'] == "1"){ $csel = "selected"; $ecstyle="display:table-row;"; } else { $csel =''; }
		$ecomcat_html='<option value="" selected >Select the Category</option>';
		if( is_plugin_active('wp-e-commerce/wp-shopping-cart.php') ) {
			$ecomterms = get_terms('wpsc_product_category');
			$ecomcat_html='<option value="" selected >Select the Category</option>';
			foreach( $ecomterms as $ecomcategory) {
				if($ecomcategory->slug==$featured_slider_curr['product_ecomcatg_slug']){$selected = 'selected';} else{$selected='';}
				$ecomcat_html =$ecomcat_html.'<option value="'.$ecomcategory->slug.'" '.$selected.'>'.$ecomcategory->name.'</option>';
			}
		}
		$html .= '<label for="'.$featured_slider_options.'[ecom_type]" class="featured_ecom_type">'.__('Select Slider Type','featured-slider').'</label>
<select name="'.$featured_slider_options.'[ecom_type]" class="featured_ecom_type" id="ecom_slider_preview" onchange="ecomtype(this.value);" >
<option value="0" '.$rsel.' >'.__('eCom Recent Product Slider','featured-slider').'</option>
<option value="1" '.$csel.' >'.__('eCom Product Category Slider','featured-slider').'</option>
</select><label for="'.$featured_slider_options.'[product_ecomcatg_slug]" class="featured_ecom_catg" style="'.$ecstyle.'">'.__('Select Category','featured-slider').'</label>
<select id="featured_slider_ecom_catslug" name="'.$featured_slider_options.'[product_ecomcatg_slug]" class="featured_ecom_catg"  style="'.$ecstyle.'">'.$ecomcat_html.'</select>';
	} elseif( $slider_type == 5 ) {
		// Event Manager Slider
		if($featured_slider_curr['event_type'] == 'future') { $eventmf = 'selected'; } else $eventmf = '';
		if($featured_slider_curr['event_type'] == 'past') { $eventmp = 'selected'; } else $eventmp = '';
		if($featured_slider_curr['event_type'] == 'all') { $eventmr = 'selected'; } else $eventmr = '';	
		// Event Categories
		$eventcat_html='<option value="" selected >Select the Category</option>';
		if( is_plugin_active('events-manager/events-manager.php') ) { 
			$eventterms = get_terms('event-categories');
			$catgs = isset($featured_slider_curr['events_mancatg_slug'])?$featured_slider_curr['events_mancatg_slug']:'';
			$catgs_a = explode(",",$catgs);
			if($catgs == '' ) $selc = 'selected'; else '';
			$eventcat_html='<option value="" '.$selc.' >All Categories</option>';
			foreach( $eventterms as $eventcategory) {
				if($catgs != '' && in_array($eventcategory->slug, $catgs_a)){$selected = 'selected';} else{$selected='';}
				$eventcat_html =$eventcat_html.'<option value="'.$eventcategory->slug.'" '.$selected.'>'.$eventcategory->name.'</option>';
			} 
			// Event Tags
			$evtags = get_terms("event-tags");
			$tags = isset($featured_slider_curr['events_mantag_slug'])?$featured_slider_curr['events_mantag_slug']:'';
			$tags_a = explode(",",$tags);
			if($tags == '') $sel = 'selected'; else $sel = '';
			$evtag_html='<option value="" '.$sel.' >All Tags</option>';
			foreach( $evtags as $tags) {
				if($tags != '' && in_array($tags->slug, $tags_a)){$selected = 'selected';} else {$selected='';}
				$evtag_html = $evtag_html.'<option value="'.$tags->slug.'" '.$selected.'>'.$tags->name.'</option>';
			}
		}
		$html .= '<label for="'.$featured_slider_options.'[event_type]" class="featured_eventman_type">'.__('Select Slider Scope','featured-slider').'</label>
	<select name="'.$featured_slider_options.'[event_type]" class="featured_eventman_type" id="eventm_slider_preview" >
		<option value="future" '.$eventmf.'>'.__('Future Events','featured-slider').'</option>
		<option value="past" '.$eventmp.'>'.__('Past Events','featured-slider').'</option>
		<option value="all" '.$eventmr.'>'.__('Recent Events','featured-slider').'</option>
	</select>';
		$html .= '<label for="'.$featured_slider_options.'[events_mancatg_slug]" class="featured_eventman_catg">'.__('Select Category','featured-slider').'</label>
<select id="featured_slider_event_catslug" class="featured-multiselect" multiple>'.$eventcat_html.'</select><input type="hidden" name="'.$featured_slider_options.'[events_mancatg_slug]" value="'.$featured_slider_curr['events_mancatg_slug'].'" />';
		$html .= '<label for="'.$featured_slider_options.'[events_mantag_slug]" class="featured_eventman_catg">'.__('Select Tag','featured-slider').'</label>
<select class="featured-multiselect" multiple >'.$evtag_html.'</select><input type="hidden" name="'.$featured_slider_options.'[events_mantag_slug]" value="'.$featured_slider_curr['events_mantag_slug'].'" />';
	} elseif( $slider_type == 6 ) {
		// Event Calender Slider
		if($featured_slider_curr['eventcal_type'] == 'list') { $ecalu = 'selected'; } else $ecalu = '';	
		if($featured_slider_curr['eventcal_type'] == 'past') { $ecalp = 'selected'; } else $ecalp = '';
		if($featured_slider_curr['eventcal_type'] == 'all') { $ecala = 'selected'; } else $ecala = '';
		$eventcal_html='<option value="" selected >Select the Category</option>';
		if( is_plugin_active('the-events-calendar/the-events-calendar.php') ) { 
			$eventcalterms = get_terms('tribe_events_cat');
			$catgs = isset($featured_slider_curr['events_calcatg_slug'])?$featured_slider_curr['events_calcatg_slug']:'';
			$catgs_a = explode(",",$catgs);
			if($catgs == '') $csel = 'selected'; else $csel = '';
			$eventcal_html='<option value="" '.$csel.' >All Category</option>';
			foreach( $eventcalterms as $eventcalcat) {
				if($catgs != '' && in_array($eventcalcat->slug, $catgs_a)){$selected = 'selected';} else{$selected='';}
				$eventcal_html =$eventcal_html.'<option value="'.$eventcalcat->slug.'" '.$selected.'>'.$eventcalcat->name.'</option>';
			}
			$evcaltags = get_terms("post_tag");
			$tags = isset($featured_slider_curr['events_caltag_slug'])?$featured_slider_curr['events_caltag_slug']:'';
			$tags_a = explode(",",$tags);
			if($tags == '') $sel = 'selected'; else $sel = '';
			$evcaltag_html='<option value="" '.$sel.' >All Tags</option>';
			foreach( $evcaltags as $tags) {
				if($tags != '' && in_array($tags->slug, $tags_a)){ $selected = 'selected';} else {$selected='';}
				$evcaltag_html = $evcaltag_html.'<option value="'.$tags->slug.'" '.$selected.'>'.$tags->name.'</option>';
			}
		}
		$html .= '<label for="'.$featured_slider_options.'[eventcal_type]" class="featured_eventcal_type">'.__('Select Slider Type','featured-slider').'</label>
<select name="'.$featured_slider_options.'[eventcal_type]" id="eventcal_slider_preview" >
	<option value="list" '.$ecalu.' >'.__('Future Events','featured-slider').'</option>
	<option value="past"'.$ecalp.' >'.__('Past Events','featured-slider').'</option>
	<option value="all" '.$ecala.' >'.__('Recent Events','featured-slider').'</option>
</select>';
		$html .= '<label for="'.$featured_slider_options.'[events_calcatg_slug]" >'.__('Select Category','featured-slider').'</label>
<select id="featured_slider_eventcal_catslug" class="featured-multiselect" multiple>'.$eventcal_html.'</select><input type="hidden" name="'.$featured_slider_options.'[events_calcatg_slug]" value="'.$featured_slider_curr['events_calcatg_slug'].'" />';
		$html .= '<label for="'.$featured_slider_options.'[events_caltag_slug]" >'.__('Select Tag','featured-slider').'</label>
<select id="featured_slider_eventcal_catslug" class="featured-multiselect" multiple>'.$evcaltag_html.'</select><input type="hidden"  name="'.$featured_slider_options.'[events_caltag_slug]" value="'.$featured_slider_curr['events_caltag_slug'].'" />';
	} elseif( $slider_type == 7 ) {
		// Taxonomy Slider
		$post_types = get_post_types(); 
		$post_type = isset($featured_slider_curr['taxonomy_posttype'])?$featured_slider_curr['taxonomy_posttype']:'post';
		if($post_type == '') $post_type = 'post';
		$taxonomy_names = get_object_taxonomies( $post_type );
		$html .='<label for="'.$featured_slider_options.'[taxonomy_posttype]" >'.__('Post Type','featured-slider').'</label><select name="'.$featured_slider_options.'[taxonomy_posttype]" id="featured_taxonomy_posttype" >';
		foreach ( $post_types as $cpost_type ) {
			$ptselected =''; 
			if($post_type == $cpost_type) $ptselected="selected";
			$html .='<option value="'.$cpost_type.'" '.$ptselected.' >' . $cpost_type . '</option>';
		}
		$html .='</select>';
		$html .='<div class="sh-taxo"><label for="'.$featured_slider_options.'[taxonomy]" >'.__('Taxonomy','featured-slider').'</label>
	<select name="'.$featured_slider_options.'[taxonomy]" id="featured_taxonomy" class="taxo-update" >
	<option value="" >Select Taxonomy </option>';
	$taxo = isset($featured_slider_curr['taxonomy'])?$featured_slider_curr['taxonomy']:'';
	foreach ( $taxonomy_names as $taxonomy_name ) { 
		$taxoselected = '';
		if( $taxo == $taxonomy_name ) $taxoselected = 'selected';
		$html .='<option value="'.$taxonomy_name.'" '.$taxoselected.' >' . $taxonomy_name . '</option>';
	} 
	$html .='</select></div>';
	if($taxo != '') $term_style = 'display:block'; else $term_style = 'display:none';
	$html .='<div class="sh-term" style="'.$term_style.'" ><label for="'.$featured_slider_options.'[taxonomy_term]" >'.__('Term','featured-slider').'</label>
	<select id="featured_taxonomy_term" class="featured-multiselect" multiple >';
	$terms = get_terms( $taxo );
	$taxoterm = isset($featured_slider_curr['taxonomy_term'])?$featured_slider_curr['taxonomy_term']:'';
	$taxoterm = explode(",",$taxoterm);
	foreach ( $terms as $term ) { 
		$termselected = '';
		if(in_array($term->slug, $taxoterm)) $termselected = 'selected';
		$html .= '<option value="'.$term->slug.'" '.$termselected.' >' . $term->name . '</option>';
	}
	$html .='</select><input type="hidden" name="'.$featured_slider_options.'[taxonomy_term]" id="featured_taxonomy" value="'.$featured_slider_curr['taxonomy_term'].'"></div><input type="hidden" id="featured-option" value="'.$featured_slider_options.'">';
	$show = isset($featured_slider_curr['taxonomy_show'])?$featured_slider_curr['taxonomy_show']:'';
	if($show == '') $dsel = 'selected'; else $dsel = '';
	if($show == 'per_tax') $psel = 'selected'; else $psel = '';
	$html .='<label for="'.$featured_slider_options.'[taxonomy_show]" >'.__('Show','featured-slider').'</label>
		<select name="'.$featured_slider_options.'[taxonomy_show]" id="featured_taxonomy_show" class="featured-form-input" >
			<option value="" '.$dsel.' >'.__('Default','featured-slider').'</option>
			<option value="per_tax" '.$psel.' >'.__('One Per Tax','featured-slider').'</option>
		</select>';
	$operator = isset($featured_slider_curr['taxonomy_operator'])?$featured_slider_curr['taxonomy_operator']:'';
	if($operator == 'IN' || $operator == '') $isel = 'selected'; else $isel = '';
	if($operator == 'NOT IN') $nsel = 'selected'; else $nsel = '';
	if($operator == 'AND') $asel = 'selected'; else $asel = '';
	$html .='<label for="'.$featured_slider_options.'[taxonomy_operator]" >'.__('Operator','featured-slider').'</label>
		<select name="'.$featured_slider_options.'[taxonomy_operator]" id="featured_taxonomy_operator" >
			<option value="IN" '.$isel.' >'.__('IN','featured-slider').'</option>
			<option value="NOT IN" '.$nsel.' >'.__('NOT IN','featured-slider').'</option>
			<option value="AND" '.$asel.' >'.__('AND','featured-slider').'</option>
		</select>';
	$html .='<label for="'.$featured_slider_options.'[taxonomy_author]" >'.__('Author','featured-slider').'</label><select class="featured-multiselect" multiple >
	<option value="" >Select Author </option>';
	$auth = isset($featured_slider_curr['taxonomy_author'])?$featured_slider_curr['taxonomy_author']:'';			
	$auth = explode(",",$auth);		
	$blogusers = get_users();	
	// Array of WP_User objects.
	foreach ( $blogusers as $user ) {
		 $aselected = '';
		if(in_array($user->ID, $auth)) $aselected = 'selected';
		$html .='<option value="'.$user->ID.'" '.$aselected.' >' . $user->user_nicename . '</option>';
	}
	$html .='</select><input type="hidden" name="'.$featured_slider_options.'[taxonomy_author]" value="'.$featured_slider_curr['taxonomy_author'].'" />';
} elseif( $slider_type == 8 ) {
		// RSS feed Slider
		$html .='<label for="'.$featured_slider_options.'[rssfeed_feedurl]" >'.__('Feed Url','featured-slider').'</label><input type="text" name="'.$featured_slider_options.'[rssfeed_feedurl]" id="featured_rssfeed_feedurl" class="regular-text code" value="'.htmlentities( $featured_slider_curr['rssfeed_feedurl'] , ENT_QUOTES).'" placeholder="http://mashable.com/feed/" />';
		$html .='<label for="'.$featured_slider_options.'[rssfeed_id]" >'.__('RSS Slider Id','featured-slider').'</label><input type="number" name="'.$featured_slider_options.'[rssfeed_id]" id="featured_rssfeed_id" class="small-text code" value="'.htmlentities( $featured_slider_curr['rssfeed_id'] , ENT_QUOTES).'"/>';
		$html .='<label for="'.$featured_slider_options.'[rssfeed_default_image]" >'.__('Default Image','featured-slider').'</label><input type="text" name="'.$featured_slider_options.'[rssfeed_default_image]" id="featured_rssfeed_defimage" class="regular-text code" value="'.htmlentities( $featured_slider_curr['rssfeed_default_image'] , ENT_QUOTES).'" />';
		$html .='<label for="'.$featured_slider_options.'[rssfeed_image_class]" >'.__('Image Class','featured-slider').'</label><input type="text" name="'.$featured_slider_options.'[rssfeed_image_class]" id="featured_rssfeed_image_class" class="regular-text code" value="'. htmlentities( $featured_slider_curr['rssfeed_image_class'] , ENT_QUOTES).'" />';
		$source = $featured_slider_curr['rssfeed_src'];
		$html .='<label for="'.$featured_slider_options.'[rssfeed_src]" >'.__('Source','featured-slider').'</label>
		<select name="'.$featured_slider_options.'[rssfeed_src]" id="featured_rssfeed_src" class="rss-source">
			<option value="" '.selected($source,"").'>'. __('Other','featured-slider').'</option>
			<option value="smugmug" '.selected($source,"smugmug").'>'. __('Smugmug','featured-slider').'</option>
		</select>
		';
		$size_style=$feed_style="style='display:none;'";
		if($source == "smugmug" ) $size_style="style='table-cell;'";
		else  $feed_style="style='display:table-cell;'";
		$feed = $featured_slider_curr['rssfeed_feed'];
		$html .='<label for="'.$featured_slider_options.'[rssfeed_feed]" class="rss-feed" '.$feed_style.' >'.__('Feed','featured-slider').'</label>
		<select name="'.$featured_slider_options.'[rssfeed_feed]" id="featured_rssfeed_feed" class="rss-feed" '.$feed_style.'>
			<option value="" '.selected($feed,"").'>'.__('Other','featured-slider').'</option>
			<option value="atom" '. selected($feed,"atom").'>'.__('Atom','featured-slider').'</option>
		</select>
		';
		$size = $featured_slider_curr['rssfeed_size'];
		$html .='<label for="'.$featured_slider_options.'[rssfeed_size]" class="rss-size" '.$size_style.' >'.__('Size','featured-slider').'</label>
		<select name="'.$featured_slider_options.'[rssfeed_size]" id="featured_rssfeed_size" class="rss-size" '.$size_style.'>
			<option value="Ti" '.selected($size, "Ti").'>'.__('Tiny thumbnails','featured-slider').'</option>
			<option value="Th" '. selected($size, "Ti").'>'.__('Large thumbnails','featured-slider').'</option>
			<option value="S" '. selected($size, "S").'>'.__('Small','featured-slider').'</option>
			<option value="M" '. selected($size, "M").'>'.__('Medium','featured-slider').'</option>
			<option value="L" '. selected($size, "L").'>'.__('Other','featured-slider').'</option>
			<option value="XL" '. selected($size, "XL").'>'.__('Large','featured-slider').'</option>
			<option value="X2" '. selected($size, "X2").'>'.__('X2Large','featured-slider').'</option>
			<option value="X3" '. selected($size, "X3").'>'.__('X3Large','featured-slider').'</option>
			<option value="O" '. selected($size, "O").'>'.__('Original','featured-slider').'</option>
		</select>
		';
		$rsscontent = $featured_slider_curr['rssfeed_content'];
		$html .='<label for="'.$featured_slider_options.'[rssfeed_content]" >'.__('Scan child node content for images','featured-slider').'</label>
		<input type="checkbox" name="'.$featured_slider_options.'[rssfeed_content]" id="featured_rssfeed_content" value="1" '.checked("1",$rsscontent,false).'/>';
	} elseif( $slider_type == 9 ) {
		$html .='<label for="'.$featured_slider_options.'[postattch_id]" >'.__('Post Id','featured-slider').'</label><input type="text" name="'.$featured_slider_options.'[postattch_id]" id="featured_postattch_id" class="regular-text code" value="'.htmlentities( $featured_slider_curr['postattch_id'] , ENT_QUOTES).'" />';
	}  elseif( $slider_type == 10 ) {
		$gid = $featured_slider_curr['nextgen_gallery_id'];
		$galleriesOptions = get_featured_nextgen_galleries($gid); 
		$html .='<label for="'.$featured_slider_options.'[nextgen_gallery_id]" >'.__('Select Gallery','featured-slider').'</label>
		<select name="'.$featured_slider_options.'[nextgen_gallery_id]" id="featured_nextgen_galleryid" class="regular-text code havemoreinfo">
			'.$galleriesOptions.'
		</select>
		';
		$html .='<label for="'.$featured_slider_options.'[nextgen_anchor]" >'.__('Link','featured-slider').'</label>&nbsp;<input type="checkbox" name="'.$featured_slider_options.'[nextgen_anchor]" value="1" id="featured_nextgen_galleryid" '.checked('1', $featured_slider_curr['nextgen_anchor'],false).' />';	
	}
	$html .= '</fieldset></td>';
	echo $html;
	die();
}
function featured_tab_contents() {
	check_ajax_referer( 'featured-settings-nonce', 'settings_nonce' );
	$tab = isset($_POST['tab'])?$_POST['tab']:'';	
	$cntr = isset($_POST['cntr'])?$_POST['cntr']:'';
	$featured_slider_options='featured_slider_options'.$cntr;
	$featured_slider_curr=get_option($featured_slider_options);
	if(!isset($cntr) or empty($cntr)){$curr = 'Default';}
	else{$curr = $cntr;}
	$featured_slider_curr= populate_featured_current($featured_slider_curr);
	$html = '';
	if(get_transient( 'featured_undo_set' ) != false) { 
		$undo_style="display:inline-block;";	
	} else $undo_style="display:none;";
	if($tab == 'content') {	
		$curr_preview = $featured_slider_curr['preview'];
		$html .= '<div id="previewslider" class="sub_settings toggle_settings">
			<h2 class="sub-heading">'.__('Slider Live Preview and Source Panel','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" class="toggle_img"></h2> 

			<table class="form-table">
				<tr valign="top"> 
					<th scope="row"><label for="featured_slider_disable_preview">'.__('Disable Live Preview','featured-slider').'</label></th> 
					<td> 
						<div class="eb-switch eb-switchnone">
							<input type="hidden" id="featured_slider_disable_preview" name="'.$featured_slider_options.'[disable_preview]" class="hidden_check" value="'.$featured_slider_curr['disable_preview'].'">
							<input id="featured_disablepreview" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['disable_preview'],false).' >
							<label for="featured_disablepreview"></label>
						</div>
					</td>
				</tr>
				<tr valign="top" id="featured_preview">
					<th scope="row">'.__('Slider Type','featured-slider').'</th>
					<td>
						<select name="'.$featured_slider_options.'[preview]" id="featured_slider_preview">
							<option value="2" '.selected( 2, $curr_preview, false).' >'.__('Recent Posts Slider','featured-slider').'</option>
							<option value="1"'.selected( 1, $curr_preview, false).' >'.__('Category Slider','featured-slider').'</option>
<option value="0" '.selected( 0, $curr_preview, false).' >'.__('Custom Slider','featured-slider').'</option>';
							if( is_plugin_active('woocommerce/woocommerce.php') ) { 
								$html .= '<option value="3" '.selected( 3, $curr_preview, false).' >'.__('WooCommerce Slider','featured-slider').'</option>';
							}
							if( is_plugin_active('wp-e-commerce/wp-shopping-cart.php') ) {
								$html .= '<option value="4" '.selected( 4, $curr_preview, false).' >'.__('eCommerce Slider','featured-slider').'</option>';
							}
							if( is_plugin_active('events-manager/events-manager.php') ) {
								$html .= '<option value="5" '.selected( 5, $curr_preview, false).' >'.__('Events Manager Slider','featured-slider').'</option>';
							}
							if( is_plugin_active('the-events-calendar/the-events-calendar.php') ) { 
								$html .= '<option value="6" '.selected( 6, $curr_preview, false).' >'.__('Events Calender Slider','featured-slider').'</option>';
							}
							$html .= '<option value="7" '.selected( 7, $curr_preview, false).' >'.__('Taxonomy Slider','featured-slider').'</option>

							<option value="8" '.selected( 8, $curr_preview, false).' >'.__('RSS feed Slider','featured-slider').'</option>

							<option value="9" '.selected( 9, $curr_preview, false).' >'.__('Post Attachments Slider','featured-slider').'</option>';

							if( is_plugin_active('nextgen-gallery/nggallery.php') ) {
								$html .= '<option value="10" '.selected( 10, $curr_preview, false).' >'.__('NextGenGallery Slider','featured-slider').'</option>';
							}
						$html .= '</select>';
					$html .= '</td>
				</tr>
				<tr valign="top" class="featured_slider_params"> </tr>	
				<tr valign="top">
					<th scope="row">'.__('Skin','featured-slider').'</th>
					<td>
						<select name="'.$featured_slider_options.'[stylesheet]" id="featured_slider_stylesheet" class="featured-skin">';
						$directory = FEATURED_SLIDER_CSS_DIR;
						if ($handle = opendir($directory)) {
						    while (false !== ($file = readdir($handle))) { 
						     if($file != '.' and $file != '..') {
							$sel_file =''; 
							if ($featured_slider_curr['stylesheet'] == $file) $sel_file = 'selected';
							$html .= '<option value="'.$file.'" '.$sel_file.' >'.$file.'</option>';
						} }
						    closedir($handle);
						}
						$html .= '</select>
					</td>
				</tr>
				</table>
				<p class="submit">
					<input type="submit" name="save_settings" class="button-primary" value="'.__('Save and Preview Slider','featured-slider').'" />
					<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
				</p>
				<div class="settingsdiv">'.__('Shortcode','featured-slider').'</div>
				<div class="yellowdiv">';
					if($cntr=='') $set=' set="1"'; else $set=' set="'.$cntr.'"';
					$offset = '';
					if (isset($featured_slider_curr['offset']) && $featured_slider_curr['offset'] != "0" && !empty($featured_slider_curr['offset']) )
						$offset = ' offset="'.$featured_slider_curr['offset'].'"';
					if ($featured_slider_curr['preview'] == "0")
						$preview = '[featuredslider id="'.$featured_slider_curr['slider_id'].'"'.$set.$offset.']';
					elseif($featured_slider_curr['preview'] == "1")
						$preview = '[featuredcategory catg_slug="'.$featured_slider_curr['catg_slug'].'"'.$set.$offset.']';
					elseif($featured_slider_curr['preview'] == "3" ) {
						$woocat = $product_id = '';
						if(isset($featured_slider_curr['product_woocatg_slug']) && !empty($featured_slider_curr['product_woocatg_slug']) )
							$woocat = ' term="'.$featured_slider_curr['product_woocatg_slug'].'"';
						if(isset($featured_slider_curr['product_id']) && !empty($featured_slider_curr['product_id']) )
							$product_id = ' product_id="'.$featured_slider_curr['product_id'].'"';
						$preview = '[featuredwoocommerce type="'.$featured_slider_curr['woo_type'].'"'.$set.$offset.$product_id.$woocat.']';
					}
					elseif($featured_slider_curr['preview'] == "4" && $featured_slider_curr['ecom_type'] == "0")
						$preview = '[featuredtaxonomy post_type="wpsc-product"'.$set.$offset.']';
					elseif($featured_slider_curr['preview'] == "4" && $featured_slider_curr['ecom_type'] == "1")
						$preview = '[featuredtaxonomy post_type="wpsc-product" taxonomy="wpsc_product_category" term="'.$featured_slider_curr['product_ecomcatg_slug'].'" '.$set.$offset.']';
					elseif($featured_slider_curr['preview'] == "5") {
						$ecat = $etag = $scope = '';
						if(isset($featured_slider_curr['events_mancatg_slug']) && !empty($featured_slider_curr['events_mancatg_slug']) )
							$ecat = ' term="'.$featured_slider_curr['events_mancatg_slug'].'"';
						if(isset($featured_slider_curr['events_mantag_slug']) && !empty($featured_slider_curr['events_mantag_slug']) )
							$etag = ' tags="'.$featured_slider_curr['events_mantag_slug'].'"';
						if(isset($featured_slider_curr['event_type']) && !empty($featured_slider_curr['event_type']) )
							$scope = ' scope="'.$featured_slider_curr['event_type'].'"';
						$preview = '[featuredevent'.$scope.$set.$offset.$ecat.$etag.']';
					}
					elseif($featured_slider_curr['preview'] == "6") {
						$ecat = $etag = $scope = '';
						if(isset($featured_slider_curr['events_calcatg_slug']) && !empty($featured_slider_curr['events_calcatg_slug']) )
							$ecat = ' term="'.$featured_slider_curr['events_calcatg_slug'].'"';
						if(isset($featured_slider_curr['events_caltag_slug']) && !empty($featured_slider_curr['events_caltag_slug']) )
							$etag = ' tags="'.$featured_slider_curr['events_caltag_slug'].'"';
						if(isset($featured_slider_curr['eventcal_type']) && !empty($featured_slider_curr['eventcal_type']) )
							$scope = ' type="'.$featured_slider_curr['eventcal_type'].'"';
						$preview = '[featuredcalendar'.$scope.$set.$offset.$ecat.$etag.']';
					}
					elseif($featured_slider_curr['preview'] == "7") {
						$postype=$taxonomy=$term=$operator=$author=$show='';
						if(isset($featured_slider_curr['taxonomy_posttype']) && $featured_slider_curr['taxonomy_posttype'] != '' ) {
							$postype = ' post_type="'.$featured_slider_curr['taxonomy_posttype'].'"';	
						}
						if(($featured_slider_curr['taxonomy']) && $featured_slider_curr['taxonomy'] != '' ) {
							$taxonomy = ' taxonomy="'.$featured_slider_curr['taxonomy'].'"';
						}
						if(isset($featured_slider_curr['taxonomy_term']) && $featured_slider_curr['taxonomy_term'] != '' ) {
							$term = ' term="'.$featured_slider_curr['taxonomy_term'].'"';
						}
						if(isset($featured_slider_curr['taxonomy_operator']) && $featured_slider_curr['taxonomy_operator'] != '' ) {
							$operator = ' operator="'.$featured_slider_curr['taxonomy_operator'].'"';
						}
						if(isset($featured_slider_curr['taxonomy_author']) && $featured_slider_curr['taxonomy_author'] != '' ) {
							$author = ' author="'.$featured_slider_curr['taxonomy_author'].'"';
						}
						if(isset($featured_slider_curr['taxonomy_show']) && $featured_slider_curr['taxonomy_show'] != '' ) {
							$show = ' show="'.$featured_slider_curr['taxonomy_show'].'"';
						}		
						$preview = '[featuredtaxonomy'.$postype.$set.$offset.$taxonomy.$term.$operator.$author.$show.']';
					}
					elseif($featured_slider_curr['preview'] == "8") {
						$id=$feed=$feedurl=$default_image=$src=$order=$media=$content=$image_class=$size='';
						if(isset($featured_slider_curr['rssfeed_id']) && $featured_slider_curr['rssfeed_id'] != '' ) {
							$id = ' id="'.$featured_slider_curr['rssfeed_id'].'"';
						}
						if(isset($featured_slider_curr['rssfeed_feed']) && $featured_slider_curr['rssfeed_feed'] != '' ) {
							$feed = ' feed="'.$featured_slider_curr['rssfeed_feed'].'"';	
						}
						if(isset($featured_slider_curr['rssfeed_feedurl']) && $featured_slider_curr['rssfeed_feedurl'] != '' ) {
							$feedurl = ' feedurl="'.$featured_slider_curr['rssfeed_feedurl'].'"';	
						}
						if(isset($featured_slider_curr['rssfeed_default_image']) && $featured_slider_curr['rssfeed_default_image'] != '' ) {
							$default_image = ' default_image="'.$featured_slider_curr['rssfeed_default_image'].'"';	
						}
						if(isset($featured_slider_curr['rssfeed_src']) && $featured_slider_curr['rssfeed_src'] != '' ) {
							$src = ' src="'.$featured_slider_curr['rssfeed_src'].'"';	
						}
						if(isset($featured_slider_curr['rssfeed_order']) && $featured_slider_curr['rssfeed_order'] != '' ) {
							$order = ' order="'.$featured_slider_curr['rssfeed_order'].'"';	
						}
						if(isset($featured_slider_curr['rssfeed_media']) && $featured_slider_curr['rssfeed_media'] != '' ) {
							$media = ' media="'.$featured_slider_curr['rssfeed_media'].'"';
						}
						if(isset($featured_slider_curr['rssfeed_content']) && $featured_slider_curr['rssfeed_content'] != '' ) {
							$content = ' content="'.$featured_slider_curr['rssfeed_content'].'"';
						}
						if(isset($featured_slider_curr['rssfeed_image_class']) && $featured_slider_curr['rssfeed_image_class'] != '' ) {
							$image_class = ' image_class="'.$featured_slider_curr['rssfeed_image_class'].'"';
						}
						if(isset($featured_slider_curr['rssfeed_size']) && $featured_slider_curr['rssfeed_size'] != '' ) {
							$size = ' size="'.$featured_slider_curr['rssfeed_size'].'"';
						}
						$preview = '[featuredfeed'.$id.$feed.$feedurl.$set.$offset.$default_image.$src.$order.$media.$content.$image_class.$size.']';
					}
					elseif($featured_slider_curr['preview'] == "9") {
						$id='';
						if(isset($featured_slider_curr['postattch_id'])  && $featured_slider_curr['postattch_id'] != '' ) {
							$id = ' id="'.$featured_slider_curr['postattch_id'].'"';
						}
						$preview = '[featuredattachments'.$id.$set.$offset.']';
					}
					elseif($featured_slider_curr['preview'] == "10") {
						$gallery_id=$anchor='';
						if(isset($featured_slider_curr['nextgen_gallery_id'])) {
							$gallery_id = ' gallery_id="'.$featured_slider_curr['nextgen_gallery_id'].'"';	
						}
						if(isset($featured_slider_curr['nextgen_anchor']) && !empty($featured_slider_curr['nextgen_anchor']) ) {
							$anchor = ' anchor="'.$featured_slider_curr['nextgen_anchor'].'"';
						}	
						$preview = '[featuredngg'.$gallery_id.$set.$offset.$anchor.']';
					}
					else $preview = '[featuredrecent'.$set.$offset.']';
					$html .= $preview;
			$html .= '</div>
			<div class="settingsdiv">'.__('Template Tag','featured-slider').'</div>
			<div class="yellowdiv yellowdiv_txtleft">';
				if($cntr=='') $tset=' $set="1"'; else $tset=' $set="'.$cntr.'"';
				$toffset = '';
				if (isset($featured_slider_curr['offset']) && $featured_slider_curr['offset'] != "0" && !empty($featured_slider_curr['offset']) )
					$toffset = ',$offset="'.$featured_slider_curr['offset'].'"';
				if ($featured_slider_curr['preview'] == "0")
					$template_tag = '<code>&lt;?php if(function_exists("get_featured_slider")){get_featured_slider($slider_id="'.$featured_slider_curr['slider_id'].'",'.$tset.$toffset.');}?&gt;</code>';
				elseif($featured_slider_curr['preview'] == "1")
					$template_tag = '<code>&lt;?php if(function_exists("get_featured_slider_category")){get_featured_slider_category($catg_slug="'.$featured_slider_curr['catg_slug'].'",'.$tset.$toffset.');}?&gt;</code>';
				elseif($featured_slider_curr['preview'] == "3" ) {
					$args = '';
					if($cntr=='') $args .= 'set=1'; else $args .= 'set='.$cntr;
					$args .= '&type='. $featured_slider_curr['woo_type'];
					if (isset($featured_slider_curr['offset']) && $featured_slider_curr['offset'] != "0" && !empty($featured_slider_curr['offset']) )
					$args .= '&offset='.$featured_slider_curr['offset'];
					if(isset($featured_slider_curr['product_woocatg_slug']) && !empty($featured_slider_curr['product_woocatg_slug']) )
						$args .= '&term='.$featured_slider_curr['product_woocatg_slug'];
					if(isset($featured_slider_curr['product_id']) && !empty($featured_slider_curr['product_id']) )
						$args .= '&product_id='.$featured_slider_curr['product_id'];
					$template_tag = '<code>&lt;?php if(function_exists("get_featured_slider_woocommerce")){get_featured_slider_woocommerce( "'.$args.'" );}?&gt;</code>';
				}
				elseif($featured_slider_curr['preview'] == "4" && $featured_slider_curr['ecom_type'] == "0") {
					$args = '';
					if($cntr=='') $args .= 'set=1'; else $args .= 'set='.$cntr;
					if (isset($featured_slider_curr['offset']) && $featured_slider_curr['offset'] != "0" && !empty($featured_slider_curr['offset']) )
					$args .= '&offset='.$featured_slider_curr['offset'];
					$args .= '&post_type=wpsc-product';	
					$template_tag = '<code>&lt;?php if(function_exists("get_featured_slider_taxonomy")){get_featured_slider_taxonomy( "'.$args.'" );}?&gt;</code>';
				}
				elseif($featured_slider_curr['preview'] == "4" && $featured_slider_curr['ecom_type'] == "1") {
					$args = '';
					if($cntr=='') $args .= 'set=1'; else $args .= 'set='.$cntr;
					if (isset($featured_slider_curr['offset']) && $featured_slider_curr['offset'] != "0" && !empty($featured_slider_curr['offset']) )
					$args .= '&offset='.$featured_slider_curr['offset'];
					$args .= '&post_type=wpsc-product&taxonomy=wpsc_product_category';	
					if(isset($featured_slider_curr['product_ecomcatg_slug']) && $featured_slider_curr['product_ecomcatg_slug'] != '' ) {
						$args .= '&term='.$featured_slider_curr['product_ecomcatg_slug'];
					}
					$template_tag = '<code>&lt;?php if(function_exists("get_featured_slider_taxonomy")){get_featured_slider_taxonomy( "'.$args.'" );}?&gt;</code>';
				}
				elseif($featured_slider_curr['preview'] == "5") {
					$args = '';
					if($cntr=='') $args .= 'set=1'; else $args .= 'set='.$cntr;
					if(isset($featured_slider_curr['event_type']) && !empty($featured_slider_curr['event_type']) )
						$args .= '&scope='.$featured_slider_curr['event_type'];
					if (isset($featured_slider_curr['offset']) && $featured_slider_curr['offset'] != "0" && !empty($featured_slider_curr['offset']) )
					$args .= '&offset='.$featured_slider_curr['offset'];
					if(isset($featured_slider_curr['events_mancatg_slug']) && !empty($featured_slider_curr['events_mancatg_slug']) )
						$args .= '&term='.$featured_slider_curr['events_mancatg_slug'];
					if(isset($featured_slider_curr['events_mantag_slug']) && !empty($featured_slider_curr['events_mantag_slug']) )
						$args .= '&tags='.$featured_slider_curr['events_mantag_slug'];

					$template_tag = '<code>&lt;?php if(function_exists("get_featured_slider_event")){get_featured_slider_event( "'.$args.'" );}?&gt;</code>';
				}
				elseif($featured_slider_curr['preview'] == "6") {
					$args = '';
					if($cntr=='') $args .= 'set=1'; else $args .= 'set='.$cntr;
					if(isset($featured_slider_curr['eventcal_type']) && !empty($featured_slider_curr['eventcal_type']) )
						$args .= '&type='.$featured_slider_curr['eventcal_type'];
					if (isset($featured_slider_curr['offset']) && $featured_slider_curr['offset'] != "0" && !empty($featured_slider_curr['offset']) )
					$args .= '&offset='.$featured_slider_curr['offset'];
					if(isset($featured_slider_curr['events_calcatg_slug']) && !empty($featured_slider_curr['events_calcatg_slug']) )
						$args .= '&term='.$featured_slider_curr['events_calcatg_slug'];
					if(isset($featured_slider_curr['events_caltag_slug']) && !empty($featured_slider_curr['events_caltag_slug']) )
						$args .= '&tags='.$featured_slider_curr['events_caltag_slug'];

					$template_tag = '<code>&lt;?php if(function_exists("get_featured_slider_event_calender")){get_featured_slider_event_calender( "'.$args.'" );}?&gt;</code>';
				}
				elseif($featured_slider_curr['preview'] == "7") {
					$args = '';
					if($cntr=='') $args .= 'set=1'; else $args .= 'set='.$cntr;
					if (isset($featured_slider_curr['offset']) && $featured_slider_curr['offset'] != "0" && !empty($featured_slider_curr['offset']) )
					$args .= '&offset='.$featured_slider_curr['offset'];
					if(isset($featured_slider_curr['taxonomy_posttype']) && $featured_slider_curr['taxonomy_posttype'] != '' ) {
						$args .= '&post_type='.$featured_slider_curr['taxonomy_posttype'];	
					}
					if(($featured_slider_curr['taxonomy']) && $featured_slider_curr['taxonomy'] != '' ) {
						$args .= '&taxonomy='.$featured_slider_curr['taxonomy'];
					}
					if(isset($featured_slider_curr['taxonomy_term']) && $featured_slider_curr['taxonomy_term'] != '' ) {
						$args .= '&term='.$featured_slider_curr['taxonomy_term'];
					}
					if(isset($featured_slider_curr['taxonomy_operator']) && $featured_slider_curr['taxonomy_operator'] != '' ) {
						$args .= '&operator='.$featured_slider_curr['taxonomy_operator'];
					}
					if(isset($featured_slider_curr['taxonomy_author']) && $featured_slider_curr['taxonomy_author'] != '' ) {
						$args .= '&author='.$featured_slider_curr['taxonomy_author'];
					}
					if(isset($featured_slider_curr['taxonomy_show']) && $featured_slider_curr['taxonomy_show'] != '' ) {
						$args .= '&show='.$featured_slider_curr['taxonomy_show'];
					}		
					$template_tag = '<code>&lt;?php if(function_exists("get_featured_slider_taxonomy")){get_featured_slider_taxonomy( "'.$args.'" );}?&gt;</code>';
				}
				elseif($featured_slider_curr['preview'] == "8") {
					$args = '';
					if($cntr=='') $args .= 'set=1'; else $args .= 'set='.$cntr;
					if (isset($featured_slider_curr['offset']) && $featured_slider_curr['offset'] != "0" && !empty($featured_slider_curr['offset']) )
					$args .= '&offset='.$featured_slider_curr['offset'];
					if(isset($featured_slider_curr['rssfeed_id']) && $featured_slider_curr['rssfeed_id'] != '' ) {
						$args .= '&id='.$featured_slider_curr['rssfeed_id'];
					}
					if(isset($featured_slider_curr['rssfeed_feed']) && $featured_slider_curr['rssfeed_feed'] != '' ) {
						$args .= '&feed='.$featured_slider_curr['rssfeed_feed'];	
					}
					if(isset($featured_slider_curr['rssfeed_feedurl']) && $featured_slider_curr['rssfeed_feedurl'] != '' ) {
						$args .= '&feedurl='.$featured_slider_curr['rssfeed_feedurl'];	
					}
					if(isset($featured_slider_curr['rssfeed_default_image']) && $featured_slider_curr['rssfeed_default_image'] != '' ) {
						$args .= '&default_image='.$featured_slider_curr['rssfeed_default_image'];	
					}
					if(isset($featured_slider_curr['rssfeed_src']) && $featured_slider_curr['rssfeed_src'] != '' ) {
						$args .= '&src='.$featured_slider_curr['rssfeed_src'];	
					}
					if(isset($featured_slider_curr['rssfeed_order']) && $featured_slider_curr['rssfeed_order'] != '' ) {
						$args .= '&order='.$featured_slider_curr['rssfeed_order'];	
					}
					if(isset($featured_slider_curr['rssfeed_media']) && $featured_slider_curr['rssfeed_media'] != '' ) {
						$args .= '&media='.$featured_slider_curr['rssfeed_media'];
					}
					if(isset($featured_slider_curr['rssfeed_content']) && $featured_slider_curr['rssfeed_content'] != '' ) {
						$args .= '&content='.$featured_slider_curr['rssfeed_content'];
					}
					if(isset($featured_slider_curr['rssfeed_image_class']) && $featured_slider_curr['rssfeed_image_class'] != '' ) {
						$args .= '&image_class='.$featured_slider_curr['rssfeed_image_class'];
					}
					if(isset($featured_slider_curr['rssfeed_size']) && $featured_slider_curr['rssfeed_size'] != '' ) {
						$args .= '&size='.$featured_slider_curr['rssfeed_size'];
					}
					$template_tag = '<code>&lt;?php if(function_exists("get_featured_slider_feed")){get_featured_slider_feed( "'.$args.'" );}?&gt;</code>';

				}
				elseif($featured_slider_curr['preview'] == "9") {
					$args = '';
					if($cntr=='') $args .= 'set=1'; else $args .= 'set='.$cntr;
					if (isset($featured_slider_curr['offset']) && $featured_slider_curr['offset'] != "0" && !empty($featured_slider_curr['offset']) )
					$args .= '&offset='.$featured_slider_curr['offset'];
					if(isset($featured_slider_curr['postattch_id'])  && $featured_slider_curr['postattch_id'] != '' ) {
						$args .= '&id='.$featured_slider_curr['postattch_id'];
					}
					$template_tag = '<code>&lt;?php if(function_exists("get_featured_slider_attachments")){get_featured_slider_attachments( "'.$args.'" );}?&gt;</code>';
				}
				elseif($featured_slider_curr['preview'] == "10") {
					$args = '';
					if($cntr=='') $args .= 'set=1'; else $args .= 'set='.$cntr;
					if (isset($featured_slider_curr['offset']) && $featured_slider_curr['offset'] != "0" && !empty($featured_slider_curr['offset']) )
					$args .= '&offset='.$featured_slider_curr['offset'];
					if(isset($featured_slider_curr['nextgen_gallery_id'])) {
						$args .= '&gallery_id='.$featured_slider_curr['nextgen_gallery_id'];	
					}
					if(isset($featured_slider_curr['nextgen_anchor']) && !empty($featured_slider_curr['nextgen_anchor']) ) {
						$args .= '&anchor='.$featured_slider_curr['nextgen_anchor'];
					}
					$template_tag = '<code>&lt;?php if(function_exists("get_featured_slider_ngg")){get_featured_slider_ngg( "'.$args.'" );}?&gt;</code>';	
				} else
					$template_tag = '<code>&lt;?php if(function_exists("get_featured_slider_recent")){get_featured_slider_recent('.$tset.$toffset.');}?&gt;</code>';
				$html .= $template_tag;
				
			$html .= '</div>
		</div>';		 //contents end
	} elseif($tab == 'basic') {
	
		$curr_ease = $featured_slider_curr['easing'];
		$curr_tran = $featured_slider_curr['transition'];
		$html.='<div class="sub_settings  toggle_settings">
		<h2 class="sub-heading">'.__('Basic Settings','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" class="toggle_img"></h2> 

		<table class="form-table">
			<tr valign="top">
			<th scope="row">'.__('Transition','featured-slider').'</th>
				<td>
				<select name="'.$featured_slider_options.'[transition]" id="featured_slider_transition">
					<option value="scrollHorz" '.selected("scrollHorz",$curr_tran,false).' >'.__('Scroll Horizontally','featured-slider').'</option>
					<option value="fade" '.selected("fade",$curr_tran,false).' >'.__('Fade','featured-slider').'</option>
				</select>
				</td>
			</tr>
	
			 <tr valign="top">
				<th scope="row">'.__('Easing','featured-slider').'</th>
				<td>
				<select name="'.$featured_slider_options.'[easing]" >

					<option value="swing" '.selected("swing",$curr_ease,false).'>'.__('swing','featured-slider').'</option>
					<option value="easeInQuad" '.selected("easeInQuad",$curr_ease,false).'>'.__('easeInQuad','featured-slider').'</option>
					<option value="easeOutQuad" '.selected("easeOutQuad",$curr_ease,false).'>'.__('easeOutQuad','featured-slider').'</option>
					<option value="easeInOutQuad" '.selected("easeInOutQuad",$curr_ease,false).'>'.__('easeInOutQuad','featured-slider').'</option>
					<option value="easeInCubic" '.selected("easeInCubic",$curr_ease,false).'>'.__('easeInCubic','featured-slider').'</option>
					<option value="easeOutCubic" '.selected("easeOutCubic",$curr_ease,false).'>'.__('easeOutCubic','featured-slider').'</option>
					<option value="easeInOutCubic" '.selected("easeInOutCubic",$curr_ease,false).'>'.__('easeInOutCubic','featured-slider').'</option>
					<option value="easeInQuart" '.selected("easeInQuart",$curr_ease,false).'>'.__('easeInQuart','featured-slider').'</option>
					<option value="easeOutQuart" '.selected("easeOutQuart",$curr_ease,false).'>'.__('easeOutQuart','featured-slider').'</option>				
					<option value="easeInOutQuart" '.selected("easeInOutQuart",$curr_ease,false).'>'.__('easeInOutQuart','featured-slider').'</option>		
					<option value="easeInQuint" '.selected("easeInQuint",$curr_ease,false).'>'.__('easeInQuint','featured-slider').'</option>		
					<option value="easeOutQuint" '.selected("easeOutQuint",$curr_ease,false).'>'.__('easeOutQuint','featured-slider').'</option>		
					<option value="easeInOutQuint" '.selected("easeInOutQuint",$curr_ease,false).'>'.__('easeInOutQuint','featured-slider').'</option>		
					<option value="easeInSine" '.selected("easeInSine",$curr_ease,false).'>'.__('easeInSine','featured-slider').'</option>		
					<option value="easeOutSine" '.selected("easeOutSine",$curr_ease,false).'>'.__('easeOutSine','featured-slider').'</option>		
					<option value="easeInOutSine" '.selected("easeInOutSine",$curr_ease,false).'>'.__('easeInOutSine','featured-slider').'</option>		
					<option value="easeInExpo" '.selected("easeInExpo",$curr_ease,false).'>'.__('easeInExpo','featured-slider').'</option>		
					<option value="easeOutExpo" '.selected("easeOutExpo",$curr_ease,false).'>'.__('easeOutExpo','featured-slider').'</option>					
					<option value="easeInOutExpo" '.selected("easeInOutExpo",$curr_ease,false).'>'.__('easeInOutExpo','featured-slider').'</option>					
					<option value="easeInCirc" '.selected("easeInCirc",$curr_ease,false).'>'.__('easeInCirc','featured-slider').'</option>		
					<option value="easeOutCirc" '.selected("easeOutCirc",$curr_ease,false).'>'.__('easeOutCirc','featured-slider').'</option>		
					<option value="easeInOutCirc" '.selected("easeInOutCirc",$curr_ease,false).'>'.__('easeInOutCirc','featured-slider').'</option>		
					<option value="easeInElastic" '.selected("easeInElastic",$curr_ease,false).'>'.__('easeInElastic','featured-slider').'</option>		
					<option value="easeOutElastic" '.selected("easeOutElastic",$curr_ease,false).'>'.__('easeOutElastic','featured-slider').'</option>		
					<option value="easeInOutElastic" '.selected("easeInOutElastic",$curr_ease,false).'>'.__('easeInOutElastic','featured-slider').'</option>	
					<option value="easeInBack" '.selected("easeInBack",$curr_ease,false).'>'.__('easeInBack','featured-slider').'</option>	
					<option value="easeOutBack" '.selected("easeOutBack",$curr_ease,false).'>'.__('easeOutBack','featured-slider').'</option>	
					<option value="easeInOutBack" '.selected("easeInOutBack",$curr_ease,false).'>'.__('easeInOutBack','featured-slider').'</option>	
					<option value="easeInBounce" '.selected("easeInBounce",$curr_ease,false).'>'.__('easeInBounce','featured-slider').'</option>	
					<option value="easeOutBounce" '.selected("easeOutBounce",$curr_ease,false).'>'.__('easeOutBounce','featured-slider').'</option>	
					<option value="easeInOutBounce" '.selected("easeInOutBounce",$curr_ease,false).'>'.__('easeInOutBounce','featured-slider').'</option>	
				</select>
				</td>
				</tr>

			<tr valign="top">
				<th scope="row">'.__('Speed','featured-slider').'</th>
					<td><input type="number" name="'.$featured_slider_options.'[speed]" id="featured_slider_speed" class="small-text" value="'.$featured_slider_curr['speed'].'" />
					<span class="moreInfo">
					<div class="tooltip1">
						'.__('The duration of Slide Animation in milliseconds. Lower value indicates fast animation. Enter numeric values like 5 or 7.','featured-slider').'
					</div>
					</span>
				</td>
			</tr>

			<tr valign="top"> 
				<th scope="row">'.__('Auto-slide','featured-slider').'</th> 
				<td>
				<div class="eb-switch eb-switchnone">
					<input type="hidden" id="featured_slider_autostep" name="'.$featured_slider_options.'[autostep]" class="hidden_check" value="'. $featured_slider_curr['autostep'].'">
					<input id="featured_autostepsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['autostep'],false).' >
					<label for="featured_autostepsett"></label>
				</div>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">'.__('Time Between Transition','featured-slider').'</th>
				<td><input type="number" name="'.$featured_slider_options.'[time]" id="featured_slider_time" class="small-text" value="'. $featured_slider_curr['time'].'" />
				<span class="moreInfo">
				<div class="tooltip1">
					'.__('Enter number of secs you want the slider to stop before sliding to next slide.','featured-slider').'
				</div>
				</span>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">'.__('No. of Posts','featured-slider').'</th>
				<td><input type="number" name="'.$featured_slider_options.'[no_posts]" id="featured_slider_no_posts" class="small-text" value="'. $featured_slider_curr['no_posts'].'" /></td>
			</tr>

			<tr valign="top">
				<th scope="row">'.__('Max. Slider Width','featured-slider').'</th>
				<td><input type="number" min="50" name="'.$featured_slider_options.'[width]" id="featured_slider_width" class="small-text" value="'. $featured_slider_curr['width'].'" /></td>
			</tr>

			<tr valign="top">
			<th scope="row">'.__('Large Slide Width','featured-slider').'</th>
			<td><input type="number" name="'.$featured_slider_options.'[lswidth]" id="featured_slider_lswidth" class="small-text" value="'.$featured_slider_curr['lswidth'].'" min="50" max="100"/>&nbsp;'.__('%','featured-slider').'<small>'.__('Enter the value between 50 to 100','featured-slider').'</small></td>
			</tr>

			<tr valign="top">
				<th scope="row">'.__('Maximum Slider Height','featured-slider').'</th>
				<td><input type="number" name="'.$featured_slider_options.'[height]" id="featured_slider_height" class="small-text" value="'. $featured_slider_curr['height'].'" />&nbsp;'.__('px','featured-slider').'</td>
			</tr>
			
			<tr valign="top">
				<th scope="row">'.__('Background','featured-slider').'</th>
					<td><input type="text" name="'.$featured_slider_options.'[bg_color]" id="featured_slider_bg_color" value="'. $featured_slider_curr['bg_color'].'" class="wp-color-picker-field" data-default-color="#000000" />
				<br /> 
				 <div class="eb-switch eb-switchnone">
				 	<input type="hidden" id="featured_slider_bg_color" name="'.$featured_slider_options.'[bg]" class="hidden_check" value="'. $featured_slider_curr['bg'].'">
					<input id="featured_rev_bg" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['bg'],false).' >
					<label for="featured_rev_bg"></label>
				 </div>'.__(' Use Transparent Background','featured-slider').' </td>
			</tr>
	 
			<tr valign="top">
				<th scope="row">'.__('Sub Slide Border Thickness','featured-slider').'</th>
					<td><input type="number" name="'.$featured_slider_options.'[slide_border]" id="featured_slider_slide_border" class="small-text" value="'. $featured_slider_curr['slide_border'].'" />&nbsp;'.__('px (put 0 if no border is required)','featured-slider').'</td>
			</tr>

			<tr valign="top">
				<th scope="row">'.__('Sub Slide Border Color','featured-slider').'</th>
					<td>
					<input type="text"  name="'.$featured_slider_options.'[slide_brcolor]" id="featured_slider_slide_brcolor" value="'. $featured_slider_curr['slide_brcolor'].'" class="wp-color-picker-field" data-default-color="#ffffff" />
					</td>
			</tr>
			
			<tr valign="top">
				<th scope="row">'.__('Border Thickness','featured-slider').'</th>
					<td><input type="number" name="'.$featured_slider_options.'[border]" id="featured_slider_border" class="small-text" value="'. $featured_slider_curr['border'].'" />&nbsp;'.__('px (put 0 if no border is required)','featured-slider').'</td>
			</tr>

			<tr valign="top">
				<th scope="row">'.__('Border Color','featured-slider').'</th>
					<td>
					<input type="text"  name="'.$featured_slider_options.'[brcolor]" id="featured_slider_brcolor" value="'. $featured_slider_curr['brcolor'].'" class="wp-color-picker-field" data-default-color="#ffffff" />
					</td>
			</tr>
			
	 		<tr valign="top">
				<th scope="row">'.__('Fixed Blocks','featured-slider').'</th> 
				<td>
				<div class="eb-switch eb-switchnone">
					<input type="hidden" name="'.$featured_slider_options.'[fixblocks]" id="featured_slider_fixblocks" class="hidden_check" value="'.$featured_slider_curr['fixblocks'].'">
					<input id="featured_fixblocks" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['fixblocks'],false).'>
					<label for="featured_fixblocks"></label>
				</div>
				</td>
		 	</tr>
		 			
			<tr valign="top">
				<th scope="row">'.__('Block Location','featured-slider').'</th>
				<td><select name="'. $featured_slider_options.'[block_pos]" id="featured_slider_block_pos" >
					<option value="1" '.selected("1",$featured_slider_curr['block_pos'],false).' >'.__('Right','featured-slider').'</option>
					<option value="0" '.selected("0",$featured_slider_curr['block_pos'],false).' >'.__('Left','featured-slider').'</option>
				</select>
				</td>
			</tr>';
			
			if( $featured_slider_curr['stylesheet']== 'trio' ) {	
				$html.= '<tr valign="top">
					<th scope="row">'.__('Trio Blocks','featured-slider').'</th>
					<td><select name="'. $featured_slider_options.'[trio_block]" id="featured_slider_trio_block" >
						<option value="2" '.selected("2",$featured_slider_curr['trio_block'],false).' >'.__('Upper block','featured-slider').'</option>
						<option value="3" '.selected("3",$featured_slider_curr['trio_block'],false).' >'.__('Lower block','featured-slider').'</option>
						<option value="4" '.selected("4",$featured_slider_curr['trio_block'],false).' >'.__('List block','featured-slider').'</option>
					</select>
					</td>
				</tr>';
			}
	$html .='</table>
		<p class="submit">
			<input type="submit" name="save_settings" class="button-primary" value="'.__('Save Changes','featured-slider').'" />
			<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
		</p>
	</div>'.do_action('featured_addon_settings',$cntr,$featured_slider_options,$featured_slider_curr); 

	} elseif($tab == 'slides') {
	
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$featured_slider_curr['img_pick'][0]=(isset($featured_slider_curr['img_pick'][0]))?$featured_slider_curr['img_pick'][0]:'';
	$featured_slider_curr['img_pick'][2]=(isset($featured_slider_curr['img_pick'][2]))?$featured_slider_curr['img_pick'][2]:'';
	$featured_slider_curr['img_pick'][3]=(isset($featured_slider_curr['img_pick'][3]))?$featured_slider_curr['img_pick'][3]:'';
	$featured_slider_curr['img_pick'][5]=(isset($featured_slider_curr['img_pick'][5]))?$featured_slider_curr['img_pick'][5]:'';
		$img_style = '';
		if(isset($cntr) and $cntr>0) $img_style = 'style="display:none;"';
		$curr_crop = $featured_slider_curr['crop'];
		$curr_tele = $featured_slider_curr['mtitle_element'];
		$title_from = $featured_slider_curr['title_from'];
		
		$html.= '<div class="sub_settings  toggle_settings">
		<h2 class="sub-heading">'.__('Slider Title','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" id="minmax_img" class="toggle_img"></h2> 
		<p>'.__('Customize the looks of the main title of the Slideshow from here','featured-slider').'</p> 
		<table class="form-table settings-tbl">

			<tr valign="top">
				<th scope="row">'.__('Default Title Text','featured-slider').'</th>
				<td><input type="text" name="'.$featured_slider_options.'[title_text]" id="featured_slider_title_text" value="'.htmlentities($featured_slider_curr['title_text'], ENT_QUOTES).'" /></td>
			</tr>

			<tr valign="top">
				<th scope="row">'.__('Pick Slider Title From','featured-slider').'</th>
				<td><select name="'.$featured_slider_options.'[title_from]" >
					<option value="0" '.selected('0',$title_from,false).'>'.__('Default Title Text','featured-slider').'</option>
					<option value="1" '.selected('1',$title_from,false).'>'.__('Slider Name','featured-slider').'</option>
				</select>
				</td>
			</tr>

			<!-- code for new fonts -->
			<tr valign="top">
				<th scope="row">'.__('Font Type','featured-slider').'</th>
				<td>
					<input type="hidden" value="title_font" class="ftype_rname">
					<input type="hidden" value="title_fontg" class="ftype_gname">
					<input type="hidden" value="titlefont_custom" class="ftype_cname">
					<select name="'.$featured_slider_options.'[t_font]" id="featured_slider_t_font" class="main-font">
						<option value="regular" '.selected( $featured_slider_curr['t_font'], "regular",false ).' > Regular Fonts </option>
						<option value="google" '.selected( $featured_slider_curr['t_font'], "google",false ).' > Google Fonts </option>
						<option value="custom" '.selected( $featured_slider_curr['t_font'], "custom",false ).' > Custom Fonts </option>
					</select>
				</td>
			</tr>

		<tr><td class="load-fontdiv" colspan="2"></td></tr>
		<!-- code for new fonts -->

		<tr valign="top">
		<th scope="row">'.__('Font Color','featured-slider').'</th>
			<td>
				<input type="text"  name="'.$featured_slider_options.'[title_fcolor]" id="featured_slider_title_fcolor" value="'.$featured_slider_curr['title_fcolor'].'" class="wp-color-picker-field" data-default-color="#000000" />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">'.__('Font Size','featured-slider').'</th>
			<td><input type="number" name="'.$featured_slider_options.'[title_fsize]" id="featured_slider_title_fsize" class="small-text" value="'.$featured_slider_curr['title_fsize'].'" />&nbsp;'.__('px','featured-slider').'</td>
		</tr>

		<tr valign="top" class="font-style">
		<th scope="row">'.__('Font Style','featured-slider').'</th>
		<td><select name="'.$featured_slider_options.'[title_fstyle]" id="featured_slider_title_fstyle" >
			<option value="bold" '.selected("bold",$featured_slider_curr['title_fstyle'],false).' >'.__('Bold','featured-slider').'</option>
			<option value="bold italic" '.selected("bold italic",$featured_slider_curr['title_fstyle'],false).' >'.__('Bold Italic','featured-slider').'</option>
			<option value="italic" '.selected("italic",$featured_slider_curr['title_fstyle'],false).' >'.__('Italic','featured-slider').'</option>
			<option value="normal" '.selected("normal",$featured_slider_curr['title_fstyle'],false).' >'.__('Normal','featured-slider').'</option>
		</select>
		</td>
		</tr>
		</table>
		<p class="submit">
			<input type="submit" name="save_settings" class="button-primary" value="'.__('Save Changes','featured-slider') .'" />
			<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
		</p>
		</div>
		<div class="sub_settings_m toggle_settings" id="postitle_exmin_tab">
		<h2 class="sub-heading" id="postitle_exmin">'.__('Main Slide Post Title','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" id="minmax_img" class="toggle_img"></h2> 
<p>'.__('Customize the looks of the title of each of the sliding post here','featured-slider').'</p> 
<table class="form-table settings-tbl">
		<tr valign="top"> 
			<th scope="row">'.__('Slide title','featured-slider').'</th> 
			<td>
				<div class="eb-switchnone">
					<input type="hidden" name="'.$featured_slider_options.'[show_title]" id="featured_slider_show_title" class="hidden_check" value="'.$featured_slider_curr['show_title'].'">
					<input id="featured_showtitle" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['show_title'],false).'>
					<label for="featured_showtitle"></label>
				</div>
			</td>
		</tr>
		<!-- code for new fonts -->
		<tr valign="top">
		<th scope="row">'.__('Font Type','featured-slider').'</th>
		<td>
		<input type="hidden" value="ptitle_font" class="ftype_rname">
		<input type="hidden" value="ptitle_fontg" class="ftype_gname">
		<input type="hidden" value="ptitle_custom" class="ftype_cname">
		<select name="'.$featured_slider_options.'[pt_font]" id="featured_slider_pt_font" class="main-font">
			<option value="regular" '.selected( $featured_slider_curr['pt_font'], "regular",false ).' > Regular Fonts </option>
			<option value="google" '.selected( $featured_slider_curr['pt_font'], "google",false ).' > Google Fonts </option>
			<option value="custom" '.selected( $featured_slider_curr['pt_font'], "custom",false ).' > Custom Fonts </option>
		</select>
		</td>
		</tr>
		<tr><td class="load-fontdiv" colspan="2"></td></tr>
		
		<!-- code for new fonts -->
		<tr valign="top">
			<th scope="row">'.__('Font Color','featured-slider').'</th>
			<td>
			<input type="text"  name="'.$featured_slider_options.'[ptitle_fcolor]" id="featured_slider_ptitle_fcolor" value="'.$featured_slider_curr['ptitle_fcolor'].'" class="wp-color-picker-field" data-default-color="#000000" />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">'.__('Font Size','featured-slider').'</th>
			<td><input type="number" name="'.$featured_slider_options.'[ptitle_fsize]" id="featured_slider_ptitle_fsize" class="small-text" value="'.$featured_slider_curr['ptitle_fsize'].'" />&nbsp;'.__('px','featured-slider').'</td>
		</tr>

		<tr valign="top" class="font-style">
			<th scope="row">'.__('Font Style','featured-slider').'</th>
			<td><select name="'.$featured_slider_options.'[ptitle_fstyle]" id="featured_slider_ptitle_fstyle" >
				<option value="bold" '.selected("bold",$featured_slider_curr['ptitle_fstyle'],false).' >'.__('Bold','featured-slider').'</option>
				<option value="bold italic" '.selected("bold italic",$featured_slider_curr['ptitle_fstyle'],false).' >'.__('Bold Italic','featured-slider').'</option>
				<option value="italic" '.selected("italic",$featured_slider_curr['ptitle_fstyle'],false).' >'.__('Italic','featured-slider').'</option>
				<option value="normal" '.selected("normal",$featured_slider_curr['ptitle_fstyle'],false).' >'.__('Normal','featured-slider').'</option>
			</select>
			</td>
		</tr>
		
		<tr valign="top">
		<th scope="row">'.__('HTML element','featured-slider').'</th>
		<td><select name="'.$featured_slider_options.'[mtitle_element]" >
			<option value="1" '.selected("1",$curr_tele,false).' >h1</option>
			<option value="2" '.selected("2",$curr_tele,false).' >h2</option>
			<option value="3" '.selected("3",$curr_tele,false).' >h3</option>
			<option value="4" '.selected("4",$curr_tele,false).' >h4</option>
			<option value="5" '.selected("5",$curr_tele,false).' >h5</option>
			<option value="6" '.selected("6",$curr_tele,false).' >h6</option>
		</select>
		</td>
		</tr>
		<tr valign="top">
			<th scope="row">'.__('Transition','featured-slider').'</th>
			<td>';
				$tital_tran_name = $featured_slider_options.'[ptitle_transition]';
				$html .= get_featured_transitions($tital_tran_name,$featured_slider_curr['ptitle_transition']).'
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">'.__('Duration (seconds)','featured-slider').'</th>
			<td>
				<input type="text" name="'.$featured_slider_options.'[ptitle_duration]" id="featured_slider_ptitle_duration"  value="'.$featured_slider_curr['ptitle_duration'].'" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">'.__('Delay time (seconds)','featured-slider').'</th>
			<td>
				<input type="text" name="'.$featured_slider_options.'[ptitle_delay]" id="featured_slider_ptitle_delay"  value="'.$featured_slider_curr['ptitle_delay'].'" />
			</td>
		</tr>
		</table>
		<p class="submit">
			<input type="submit" name="save_settings" class="button-primary" value="'.__('Save Changes','featured-slider') .'" />
			<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
		</p>
</div>

		<!-- Added For sub slide post title -->
		<div class="sub_settings_m toggle_settings" id="sub_postitle_exmin_tab">
		<h2 class="sub-heading" id="sub_postitle_exmin">'.__('Sub Slide Post Title','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" id="minmax_img" class="toggle_img"></h2> 
		<p>'.__('Customize the looks of the title of each of the sliding post here','featured-slider').'</p> 
		<table class="form-table settings-tbl">
		<!-- code for new fonts -->
		<tr valign="top"> 
			<th scope="row">'.__('Sub Slide title','featured-slider').'</th> 
			<td>
				<div class="eb-switchnone">
					<input type="hidden" name="'.$featured_slider_options.'[show_sub_title]" id="featured_slider_show_sub_title" class="hidden_check" value="'.$featured_slider_curr['show_sub_title'].'">
					<input id="featured_showsubtitle" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['show_sub_title'],false).'>
					<label for="featured_showsubtitle"></label>
				</div>
			</td>
		</tr>
		<tr valign="top">
		<th scope="row">'.__('Font Type','featured-slider').'</th>
			<td>
			<input type="hidden" value="sub_ptitle_font" class="ftype_rname">
			<input type="hidden" value="sub_ptitle_fontg" class="ftype_gname">
			<input type="hidden" value="sub_ptitle_custom" class="ftype_cname">
			<select name="'.$featured_slider_options.'[sub_pt_font]" id="featured_slider_sub_pt_font" class="main-font">
				<option value="regular" '.selected( $featured_slider_curr['sub_pt_font'], "regular",false ).' > Regular Fonts </option>
				<option value="google" '.selected( $featured_slider_curr['sub_pt_font'], "google",false ).' > Google Fonts </option>
				<option value="custom" '.selected( $featured_slider_curr['sub_pt_font'], "custom",false ).' > Custom Fonts </option>
			</select>
			</td>
		</tr>
		<tr><td class="load-fontdiv" colspan="2"></td></tr>
		<!-- code for new fonts -->
		<tr valign="top">
			<th scope="row">'.__('Font Color','featured-slider').'</th>
			<td>
			<input type="text" name="'.$featured_slider_options.'[sub_ptitle_fcolor]" id="featured_slider_sub_ptitle_fcolor" value="'.$featured_slider_curr['sub_ptitle_fcolor'].'" class="wp-color-picker-field" data-default-color="#ffffff" />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">'.__('Font Size','featured-slider').'</th>
			<td><input type="number" name="'.$featured_slider_options.'[sub_ptitle_fsize]" id="featured_slider_sub_ptitle_fsize" class="small-text" value="'.$featured_slider_curr['sub_ptitle_fsize'].'" />&nbsp;'.__('px','featured-slider').'</td>
		</tr>

		<tr valign="top" class="font-style">
		<th scope="row">'.__('Font Style','featured-slider').'</th>
		<td><select name="'.$featured_slider_options.'[sub_ptitle_fstyle]" id="featured_slider_sub_ptitle_fstyle" >
					<option value="bold" '.selected("bold",$featured_slider_curr['sub_ptitle_fstyle'],false).' >'.__('Bold','featured-slider').'</option>
					<option value="bold italic" '.selected("bold italic",$featured_slider_curr['sub_ptitle_fstyle'],false).' >'.__('Bold Italic','featured-slider').'</option>
					<option value="italic" '.selected("italic",$featured_slider_curr['sub_ptitle_fstyle'],false).' >'.__('Italic','featured-slider').'</option>
					<option value="normal" '.selected("normal",$featured_slider_curr['sub_ptitle_fstyle'],false).' >'.__('Normal','featured-slider').'</option>
				</select>
		</td>
		</tr>

		<tr valign="top">
		<th scope="row">'.__('HTML element','featured-slider').'</th>
		<td>		
			<select name="'.$featured_slider_options.'[stitle_element]" >
				<option value="1" '.selected("1",$featured_slider_curr['stitle_element'], false).' >h1</option>
				<option value="2" '.selected("2",$featured_slider_curr['stitle_element'], false).' >h2</option>
				<option value="3" '.selected("3",$featured_slider_curr['stitle_element'], false).' >h3</option>
				<option value="4" '.selected("4",$featured_slider_curr['stitle_element'], false).' >h4</option>
				<option value="5" '.selected("5",$featured_slider_curr['stitle_element'], false).' >h5</option>
				<option value="6" '.selected("6",$featured_slider_curr['stitle_element'], false).' >h6</option>
			</select>	
		</td>
		</tr>

		<tr valign="top">
			<th scope="row">'.__('Transition','featured-slider').'</th>
			<td>';
				$tital_tran_name = $featured_slider_options.'[sub_ptitle_transition]';
				$html .=get_featured_transitions($tital_tran_name,$featured_slider_curr['sub_ptitle_transition']).'
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row">'.__('Duration (seconds)','featured-slider').'</th>
			<td>
				<input type="text" name="'.$featured_slider_options.'[sub_ptitle_duration]" id="featured_slider_sub_ptitle_duration" value="'.$featured_slider_curr['sub_ptitle_duration'].'" />
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row">'.__('Delay time (seconds)','featured-slider').'</th>
			<td>
				<input type="text" name="'.$featured_slider_options.'[sub_ptitle_delay]" id="featured_slider_sub_ptitle_delay" value="'.$featured_slider_curr['sub_ptitle_delay'].'" />
			</td>
		</tr>
		
		</table>
		<p class="submit">
			<input type="submit" name="save_settings" class="button-primary" value="'.__('Save Changes','featured-slider') .'" />
			<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
		</p>
		</div>

		<div class="sub_settings_m toggle_settings" id="thumbimg_exmin_tab">
		<h2 class="sub-heading" id="thumbimg_exmin">'.__('Slide Image','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" id="minmax_img" class="toggle_img"></h2> 
		<p>'.__('Customize the looks of the thumbnail image for each of the sliding post here','featured-slider').'</p> 
		<table class="form-table">

		<tr valign="top"> 
		<th scope="row">'.__('Image Source','featured-slider').' <small>'.__('(The first one is having priority over second, the second having priority on third and so on)','featured-slider').'</small></th> 
		<td>
		<fieldset>
		<div class="mdivsett">
					<div class="eb-switch eb-switchnone">
						<input type="hidden" name="'.$featured_slider_options.'[img_pick][0]" class="hidden_check" value="'.$featured_slider_curr['img_pick'][0].'">
						<input id="featured_customfldchksett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['img_pick'][0],false).'>
						<label for="featured_customfldchksett"></label>
					</div>';		
					if(!isset($cntr) or empty($cntr)){ 
						$html .= __('Custom field','featured-slider'); 
					} else { 
						$html .= __('(Set custom field name on Default Settings)','featured-slider'); }
					$html .= '<input type="text" name="'.$featured_slider_options.'[img_pick][1]" class="settingsminput" value="'.$featured_slider_curr['img_pick'][1].'" '.$img_style.' />
				</div>
				<div class="mdivsett">
					<div class="eb-switch eb-switchnone">
						<input type="hidden" name="'.$featured_slider_options.'[img_pick][2]" class="hidden_check" value="'.$featured_slider_curr['img_pick'][2].'">
						<input id="featured_featuredimgchksett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['img_pick'][2],false).'>
						<label for="featured_featuredimgchksett"></label>
					</div>
					<label>Featured Image</label>	
				</div>
				<div class="mdivsett">
					<div class="eb-switch eb-switchnone">
						<input type="hidden" name="'.$featured_slider_options.'[img_pick][3]" class="hidden_check" value="'.$featured_slider_curr['img_pick'][3].'">
						<input id="featured_attachedimgchksett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['img_pick'][3],false).'>
						<label for="featured_attachedimgchksett"></label>
					</div>
					<label>Attached image,order </label>
					<input type="text" name="'.$featured_slider_options.'[img_pick][4]" class="small-text" value="'.$featured_slider_curr['img_pick'][4].'" />	
				</div>

				<div class="mdivsett">
					<div class="eb-switch eb-switchnone">
						<input type="hidden" name="'.$featured_slider_options.'[img_pick][5]" class="hidden_check" value="'.$featured_slider_curr['img_pick'][5].'">
						<input id="featured_scanimgchksett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['img_pick'][5],false).'>
						<label for="featured_scanimgchksett"></label>
					</div>
					<label>Scan content</label>
				</div>
		</fieldset>	
		</td> 
		</tr> 

		<tr valign="top">
		<th scope="row">'.__('Fetched Image size','featured-slider').'
		</th>
		<td>
			<select name="'.$featured_slider_options.'[crop]" id="featured_slider_crop" class="havemoreinfo">
				<option value="0" '.selected('0',$curr_crop,false).'>'.__('Full','featured-slider').'</option>
				<option value="1" '.selected('1',$curr_crop,false).'>'.__('Large','featured-slider').'</option>
				<option value="2" '.selected('2',$curr_crop,false).' >'.__('Medium','featured-slider').'</option>
				<option value="3" '.selected('3',$curr_crop,false).' >'.__('Thumbnail','featured-slider').'</option>
			</select>
		<span class="moreInfo">
			<div class="tooltip1">
			'.__('This is for fast page load, in case you choose \'Custom Size\' setting from below, you would not like to extract \'full\' size image from the media library. In this case you can use, \'medium\' or \'thumbnail\' image. This is because, for every image upload to the media gallery WordPress creates four sizes of the same image. So you can choose which to load in the slider and then specify the actual size.','featured-slider').'
			</div>
		</span>
		</td>
		</tr>


		<tr valign="top">
		<th scope="row">'.__('Pure Image Slider','featured-slider').'</th>
		<td>
			<div class="eb-switch eb-switchnone havemoreinfo">
				<input type="hidden" name="'.$featured_slider_options.'[image_only]" id="featured_slider_image_only" class="hidden_check" value="'.$featured_slider_curr['image_only'].'">
				<input id="featured_imageonlysett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['image_only'],false).' >
				<label for="featured_imageonlysett"></label>
			</div>

			<span class="moreInfo">
				<div class="tooltip1">
				'.__('check this to convert Featured Slider to Image Slider with no content','featured-slider').'
				</div>
			</span>
		</td>
		</tr>

		<tr valign="top">
			<th scope="row">'.__('Image Title on Hover','featured-slider').'</th>
			<td>
				<div class="eb-switch eb-switchnone havemoreinfo">
					<input type="hidden" name="'.$featured_slider_options.'[image_title_text]" class="hidden_check" value="'.$featured_slider_curr['image_title_text'].'">
					<input id="featured_thoversett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['image_title_text'],false).'>
					<label for="featured_thoversett"></label>
				</div>
				<span class="moreInfo">
					<div class="tooltip1">
					'.__('If enabled, whenever user hovers the Slide Image, the image title attribute will be displayed.','featured-slider').'
					</div>
				</span>
			</td>
		</tr>

		<tr valign="top"> 
			<th scope="row">'.__( 'Enable Image Cropping','featured-slider' ).'</th> 
			<td>
				<div class="eb-switch eb-switchnone">
					<input type="hidden" name="'.$featured_slider_options.'[cropping]" class="hidden_check" value="'.$featured_slider_curr['cropping'].'">
					<input id="featured_imgcropsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['cropping'],false).'>
					<label for="featured_imgcropsett"></label>
				</div>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">'.__('Transition','featured-slider').'</th>
			<td>';
				$img_tran_name = $featured_slider_options.'[img_transition]';
				$html.=get_featured_transitions($img_tran_name,$featured_slider_curr['img_transition']).'
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">'.__('Duration (seconds)','featured-slider').'</th>
			<td>
				<input type="text" name="'.$featured_slider_options.'[img_duration]" id="featured_slider_img_duration" value="'.$featured_slider_curr['img_duration'].'" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">'.__('Delay time (seconds)','featured-slider').'</th>
			<td>
				<input type="text" name="'.$featured_slider_options.'[img_delay]" id="featured_slider_img_delay" value="'.$featured_slider_curr['img_delay'].'" />
			</td>
		</tr>
		<tr valign="top">
				<th scope="row">'.__('Default Image','featured-slider').'</th>
				<td>	
					<img id="default-img" src="'.$featured_slider_curr['default_image'].'" width="80" height="70" style="float: left;" />
					<input type="submit" name="default-image-upload" class="featured-upload-default" value="Upload" style="float: left;margin-left: -90px;margin-top: 75px;background: #dddddd;cursor:pointer;" >
					<input type="submit" name="default-image-reset" class="featured-reset-default" value="Reset" style="background: #dddddd;float: left;margin-top: 75px;margin-left: -30px;cursor:pointer;" >
					<input type="hidden" id="default-image-url" value="'.$default_featured_slider_settings['default_image'].'">
					<input type="hidden" name="'.$featured_slider_options.'[default_image]" id="featured_slider_default_image" class="regular-text code havemoreinfo" value="'.$featured_slider_curr['default_image'].'" />

					<span class="moreInfo">
						<div class="tooltip1">
						'.__('Enter the url of the default image i.e. the image to be displayed if there is no image available for the slide. By default, the url is <br />','featured-slider').'<span style="color:#0000ff;">'.$featured_slider_curr['default_image'].'</span>'.'
						</div>
					</span>
				</td>
		</tr>
		</table>
		<p class="submit">
			<input type="submit" name="save_settings" class="button-primary" value="'.__('Save Changes','featured-slider').'" />
			<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
		</p>
</div>

	<div class="sub_settings_m toggle_settings" id="postcontent_exmin_tab">
	<h2 class="sub-heading">'.__('Main Slide Content','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" class="toggle_img"></h2> 
	<p>'.__('Customize the looks of the content of each of the sliding post here','featured-slider').'</p>
	<table class="form-table settings-tbl">

		<tr valign="top"> 
			<th scope="row">'.__('Content','featured-slider').'</th> 
			<td>
				<div class="eb-switchnone">
					<input type="hidden" name="'.$featured_slider_options.'[show_content]" id="featured_slider_show_content" class="hidden_check" value="'.$featured_slider_curr['show_content'].'">
					<input id="featured_showcontentsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['show_content'],false).'>
					<label for="featured_showcontentsett"></label>
				</div>
			</td>
		</tr>


		<tr valign="top">
			<th scope="row">'.__('Font Type','featured-slider').'</th>
			<td>
				<input type="hidden" value="content_font" class="ftype_rname">
				<input type="hidden" value="content_fontg" class="ftype_gname">
				<input type="hidden" value="pcfont_custom" class="ftype_cname">
				<select name="'.$featured_slider_options.'[pc_font]" id="featured_slider_pc_font" class="main-font">
					<option value="regular" '.selected( $featured_slider_curr['pc_font'], "regular",false ).' > Regular Fonts </option>
					<option value="google" '.selected( $featured_slider_curr['pc_font'], "google",false ).' > Google Fonts </option>
					<option value="custom" '.selected( $featured_slider_curr['pc_font'], "custom",false ).' > Custom Fonts </option>
				</select>
			</td>
		</tr>
		<tr><td class="load-fontdiv" colspan="2"></td></tr>

			<tr valign="top">
				<th scope="row">'.__('Font Color','featured-slider').'</th>
				<td><input type="text" name="'.$featured_slider_options.'[content_fcolor]" id="featured_slider_content_fcolor" value="'.$featured_slider_curr['content_fcolor'].'" class="wp-color-picker-field" data-default-color="#ffffff" /></td>
			</tr>
			<tr valign="top">
				<th scope="row">'.__('Font Size','featured-slider').'</th>
				<td><input type="number" name="'.$featured_slider_options.'[content_fsize]" id="featured_slider_content_fsize" class="small-text" value="'.$featured_slider_curr['content_fsize'].'" min="1" />&nbsp;'.__('px','featured-slider').'</td>
			</tr>

			<tr valign="top" class="font-style">
						<th scope="row">'.__('Font Style','featured-slider').'</th>
						<td>
							<select name="'.$featured_slider_options.'[content_fstyle]" id="featured_slider_content_fstyle" >
								<option value="bold" '.selected("bold",$featured_slider_curr['content_fstyle'],false).' >'.__('Bold','featured-slider').'</option>
								<option value="bold italic" '.selected("bold",$featured_slider_curr['content_fstyle'],false).' >'.__('Bold Italic','featured-slider').'</option>
								<option value="italic" '.selected("bold",$featured_slider_curr['content_fstyle'],false).' >'.__('Italic','featured-slider').'</option>
								<option value="normal" '.selected("bold",$featured_slider_curr['content_fstyle'],false).' >'.__('Normal','featured-slider').'</option>
							</select>
						</td>
			</tr>

			<tr valign="top">
				<th scope="row">'.__('Source','featured-slider').'</th>
				<td><select name="'.$featured_slider_options.'[content_from]" id="featured_slider_content_from" >
					<option value="slider_content" '.selected("slider_content",$featured_slider_curr['content_from'],false).' >'.__('Slider Content Custom field','featured-slider').'</option>
					<option value="excerpt" '.selected("excerpt",$featured_slider_curr['content_from'],false).' >'.__('Post Excerpt','featured-slider').'</option>
					<option value="content" '.selected("content",$featured_slider_curr['content_from'],false).' >'.__('From Content','featured-slider').'</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">'.__('Length','featured-slider').'</th>
				<td>
					<div>
					<input id="featured_slider_climit" name="'.$featured_slider_options.'[climit]" type="radio" value="0" '.checked('0', $featured_slider_curr['climit'],false).'  />
					<label class="eb-mlabel">'.__(' words','featured-slider').'</label>
					<input type="number" name="'.$featured_slider_options.'[content_limit]" id="featured_slider_content_limit" class="small-text" value="'.$featured_slider_curr['content_limit'].'" min="1" />
					</div>
					<div class="eb-margindiv">
					<input id="featured_slider_climit" name="'.$featured_slider_options.'[climit]" type="radio" value="1" '.checked('1', $featured_slider_curr['climit'],false).'  />
					<label class="eb-mlabel">'.__(' Characters','featured-slider').'</label>
					<input type="number" name="'.$featured_slider_options.'[content_chars]" id="featured_slider_content_chars" class="small-text" value="'.$featured_slider_curr['content_chars'].'" min="1"/>
					</div>
				</td>	
			</tr>
			<tr valign="top">
				<th scope="row">'.__('Transition','featured-slider').'</th>
				<td>';
					$content_tran_name = $featured_slider_options.'[content_transition]';
					$html .= get_featured_transitions($content_tran_name,$featured_slider_curr['content_transition']).'
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">'.__('Duration (seconds)','featured-slider').'</th>
				<td>
					<input type="text" name="'.$featured_slider_options.'[content_duration]" id="featured_slider_content_duration" value="'.$featured_slider_curr['content_duration'].'" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">'.__('Delay time (seconds)','featured-slider').'</th>
				<td>
					<input type="text" name="'.$featured_slider_options.'[content_delay]" id="featured_slider_content_delay"  value="'.$featured_slider_curr['content_delay'].'" />
				</td>
			</tr>
	</table>
	<p class="submit">
		<input type="submit" class="button-primary" name="save_settings" value="'.__('Save Changes','featured-slider').'" />
		<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
	</p>
</div>

<!-- Meta fields -start -->

<div class="sub_settings_m toggle_settings">
<h2 class="sub-heading">'.__('Meta Fields Below Post Title','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" id="minmax_img" class="toggle_img"></h2> 
<p>'.__('Customize the content and looks of the Post Meta Info in the Slide in Navigation','featured-slider').'</p> 
<table class="form-table settings-tbl">

<tr valign="top"> 
	<th scope="row">'.__('Show Meta','featured-slider').'</th> 
	<td>
		<div class="eb-switchnone">
			<input type="hidden" name="'.$featured_slider_options.'[show_meta]" id="featured_slider_show_meta" class="hidden_check" value="'.$featured_slider_curr['show_meta'].'">
			<input id="featured_showmetasett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['show_meta'], false).'>
			<label for="featured_showmetasett"></label>
		</div>
	</td>
</tr>

<tr valign="top">
			<td>
			<tr>
			<th scope="row">'.__('Meta Field 1','featured-slider').'</th>
				<td>
				<fieldset><table>
				<tr><td style="padding:0"><label for="'. $featured_slider_options.'[meta1_fn]">Function</label></td><td style="padding:0"><input type="text" name="'. $featured_slider_options.'[meta1_fn]" class="regular-text code" value="'. $featured_slider_curr['meta1_fn'].'" /> </td> </tr>
				<tr><td style="padding:0"><label for="'. $featured_slider_options.'[meta1_parms]">Parameters</label></td><td style="padding:0"><input type="text" name="'. $featured_slider_options.'[meta1_parms]" class="regular-text code" value="'. $featured_slider_curr['meta1_parms'].'" /> </td> </tr>
				<tr><td style="padding:0"><label for="'. $featured_slider_options.'[meta1_before]">Before Text</label></td><td style="padding:0"><input type="text" name="'. $featured_slider_options.'[meta1_before]" class="regular-text code" value="'. $featured_slider_curr['meta1_before'].'" /> </td> </tr> 
				<tr><td style="padding:0"><label for="'. $featured_slider_options.'[meta1_after]">After Text</label></td><td style="padding:0"><input type="text" name="'. $featured_slider_options.'[meta1_after]" class="regular-text code" value="'. $featured_slider_curr['meta1_after'].'" /> </td> </tr>

				</table></fieldset>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">'.__('Meta Field 2','featured-slider').'</th>
				<td>
				<fieldset><table>
				<tr><td style="padding:0"><label for="'. $featured_slider_options.'[meta2_fn]">Function</label></td><td style="padding:0"><input type="text" name="'. $featured_slider_options.'[meta2_fn]" class="regular-text code" value="'. $featured_slider_curr['meta2_fn'].'" /> </td></tr> 
				<tr><td style="padding:0"><label for="'. $featured_slider_options.'[meta2_parms]">Parameters</label></td><td style="padding:0"><input type="text" name="'. $featured_slider_options.'[meta2_parms]" class="regular-text code" value="'. $featured_slider_curr['meta2_parms'].'" /> </td></tr>
				<tr><td style="padding:0"><label for="'. $featured_slider_options.'[meta2_before]">Before Text</label></td><td style="padding:0"><input type="text" name="'. $featured_slider_options.'[meta2_before]" class="regular-text code" value="'. $featured_slider_curr['meta2_before'].'" /></td></tr> 
				<tr><td style="padding:0"><label for="'. $featured_slider_options.'[meta2_after]">After Text</label></td><td style="padding:0"><input type="text" name="'. $featured_slider_options.'[meta2_after]" class="regular-text code" value="'. $featured_slider_curr['meta2_after'].'" /></td></tr>
				</table></fieldset>
				</td>
			</tr>

<!-- code for new fonts -->
			<tr valign="top">
				<th scope="row">'.__('Font Type','featured-slider').'</th>
				<td>
					<input type="hidden" value="meta_title_font" class="ftype_rname">
					<input type="hidden" value="meta_title_fontg" class="ftype_gname">
					<input type="hidden" value="mtfont_custom" class="ftype_cname">
					<select name="'. $featured_slider_options.'[mt_font]" id="featured_slider_mt_font" class="main-font">
						<option value="regular"'.selected( $featured_slider_curr['mt_font'], "regular",false ).' > Regular Fonts </option>
						<option value="google" '.selected( $featured_slider_curr['mt_font'], "google",false ).' > Google Fonts </option>
						<option value="custom" '.selected( $featured_slider_curr['mt_font'], "custom", false ).' > Custom Fonts </option>
					</select>
				</td>
			</tr>

<tr><td class="load-fontdiv font-meta" colspan="2"></td></tr>
<!-- code for new fonts -->
				<tr valign="top">
						<th scope="row">'.__('Font Color','featured-slider').'</th>
						<td><input type="text" name="'. $featured_slider_options.'[meta_title_fcolor]" id="featured_slider_meta_title_fcolor" value="'. $featured_slider_curr['meta_title_fcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" /></td>
					</tr>

					<tr valign="top">
						<th scope="row">'.__('Font Size','featured-slider').'</th>
						<td><input type="number" name="'. $featured_slider_options.'[meta_title_fsize]" id="featured_slider_meta_title_fsize" class="small-text" value="'. $featured_slider_curr['meta_title_fsize'].'" min="1" />&nbsp;'.__('px','featured-slider').'</td>
					</tr>

					<tr valign="top" class="font-style">
						<th scope="row">'.__('Font Style','featured-slider').'</th>
						<td><select name="'. $featured_slider_options.'[meta_title_fstyle]" id="featured_slider_meta_title_fstyle" >
						<option value="bold" '.selected("bold",$featured_slider_curr['meta_title_fstyle'],false).' >'.__('Bold','featured-slider').'</option>
						<option value="bold italic" '.selected("bold italic",$featured_slider_curr['meta_title_fstyle'],false).' >'.__('Bold Italic','featured-slider').'</option>
						<option value="italic" '.selected("italic",$featured_slider_curr['meta_title_fstyle'],false).' >'.__('Italic','featured-slider').'</option>
						<option value="normal" '.selected("normal",$featured_slider_curr['meta_title_fstyle'],false).' >'.__('Normal','featured-slider').'</option>
						</select>
						</td>
					</tr>
				</table>
</div>';

	} elseif($tab == 'miscellaneous') {
	
		$html.= '<div class="sub_settings  toggle_settings">
		<h2 class="sub-heading">'.__('Miscellaneous','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" id="minmax_img" class="toggle_img"></h2> 

			<table class="form-table">

				<tr valign="top">
					<th scope="row">'.__('Retain these html tags','featured-slider').'</th>
					<td><input type="text" name="'.$featured_slider_options.'[allowable_tags]" class="regular-text code" value="'.$featured_slider_curr['allowable_tags'].'" /></td>
				</tr>
				<tr valign="top">
					<th scope="row">'.__('Continue Reading Text','featured-slider').'</th>
					<td><input type="text" name="'.$featured_slider_options.'[more]" class="regular-text code" value="'.$featured_slider_curr['more'].'" /></td>
				</tr>
				
				<tr valign="top">
					<th scope="row">'.__('Continue Reading Text Color','featured-slider').'</th>
					<td><input type="text" name="'.$featured_slider_options.'[more_color]" id="featured_slider_more_color" value="'.$featured_slider_curr['more_color'].'" class="wp-color-picker-field" data-default-color="#ffffff" /></td>
				</tr>

				<tr valign="top">
				<th scope="row">'.__('Slide Link (\'a\' element) attributes  ','featured-slider').'</th>
					<td><input type="text" name="'.$featured_slider_options.'[a_attr]" class="regular-text code havemoreinfo" value="'.htmlentities( $featured_slider_curr['a_attr'] , ENT_QUOTES).'" />
					<span class="moreInfo">
							<div class="tooltip1">
							'.__('eg. target="_blank" rel="external nofollow"','featured-slider').'
							</div>
					</span>
					</td>
				</tr>

		<tr valign="top">
		<th scope="row">'.__('Lightbox Effect','featured-slider').'</th>
		<td>
		<div class="eb-switch eb-switchnone havemoreinfo">
			<input type="hidden" name="'.$featured_slider_options.'[pphoto]" class="featured_slider_pphoto hidden_check" value="'.$featured_slider_curr['pphoto'].'">
			<input id="featured_pphotosett" class="cmn-toggle eb-toggle-round featured_pphoto" type="checkbox" '.checked('1', $featured_slider_curr['pphoto'],false).'>
			<label for="featured_pphotosett"></label>
		</div>
		<span class="moreInfo">
			<div class="tooltip1">
			'.__('If checked, when user clicks the slide image, it will appear in a modal lightbox','featured-slider').'
			</div>
		</span>
		</td>
		</tr>';
			if($featured_slider_curr['pphoto'] == 1 ) $lbox_style = 'display:table-row';
			else $lbox_style = 'display:none';
			 
				$html .= '<tr valign="top" class="featured_slider_lbox_type" style="'.$lbox_style.'" >
					<th scope="row"  >'.__('Select LightBox','featured-slider').'</th>
					<td>

					<select name="'.$featured_slider_options.'[lbox_type]" >
						<option value="pphoto_box" '.selected("pphoto_box",$featured_slider_curr['lbox_type'],false).' >'.__('PrettyPhoto','featured-slider').'</option>
						<option value="nivo_box" '.selected("nivo_box",$featured_slider_curr['lbox_type'],false).' >'.__('Nivo box','featured-slider').'</option>
						<option value="photo_box" '.selected("photo_box",$featured_slider_curr['lbox_type'],false).' >'.__('Photo box','featured-slider').'</option>
						<option value="smooth_box" '.selected("smooth_box",$featured_slider_curr['lbox_type'],false).' >'.__('Smooth box','featured-slider').'</option>
						<option value="swipe_box" '.selected("swipe_box",$featured_slider_curr['lbox_type'],false).' >'.__('Swipe box','featured-slider').'</option>
					</select>
					</td>
				</tr>

				<tr valign="top">
				<th scope="row">'.__('Custom fields','featured-slider').'</th>
				<td><textarea name="'.$featured_slider_options.'[fields]"  rows="5" cols="30" class="regular-text code havemoreinfo">'.$featured_slider_curr['fields'].'</textarea>
				<span class="moreInfo">
						<div class="tooltip1">
						'.__('Separate different fields using commas eg. description,customfield2','featured-slider').'
						</div>
				</span>
				</td>
				</tr>
				 
				<tr valign="top">
				<th scope="row">'.__('Randomize Slides','featured-slider').'</th>
				<td>
				<div class="eb-switch eb-switchnone havemoreinfo">
					<input type="hidden" name="'.$featured_slider_options.'[rand]" id="featured_slider_rand" class="hidden_check" value="'.$featured_slider_curr['rand'].'">
					<input id="featured_randsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['rand'],false).'>
					<label for="featured_randsett"></label>
				</div>
				<span class="moreInfo">
					<div class="tooltip1">
					'.__('check this if you want the slides added to appear in random order.','featured-slider').'
					</div>
				</span>
				</td>
				</tr>
				
				<tr valign="top">
				<th scope="row">'.__('Do not link slide to any url','featured-slider').'</th>
				<td>
					<div class="eb-switch eb-switchnone">
						<input type="hidden" name="'.$featured_slider_options.'[donotlink]" id="featured_slider_donotlink" class="hidden_check" value="'.$featured_slider_curr['donotlink'].'">
						<input id="featured_donotlinksett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['donotlink'],false).' >
						<label for="featured_donotlinksett"></label>
					</div>
				</td>
				</tr>
				
				<tr valign="top">
					<th scope="row">'.__('Disable Slider on Mobiles and Tablets','featured-slider').'</th> 
					<td>
					<div class="eb-switch eb-switchnone">
						<input type="hidden" name="'.$featured_slider_options.'[disable_mobile]" id="featured_slider_disable_mobile" class="hidden_check" value="'.$featured_slider_curr['disable_mobile'].'">
						<input id="featured_disable_mobile" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['disable_mobile'],false).'>
						<label for="featured_disable_mobile"></label>
					</div>
					</td>
			 	</tr>

				<tr valign="top">
				<th scope="row">'.__('Enable FOUC','featured-slider').'</th>
				<td>
				<div class="eb-switch eb-switchnone havemoreinfo">
					<input type="hidden" name="'.$featured_slider_options.'[fouc]" id="featured_slider_fouc" class="hidden_check" value="'.$featured_slider_curr['fouc'].'">
					<input id="featured_foucsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['fouc'],false).' >
					<label for="featured_foucsett"></label>
				</div>
				<span class="moreInfo">
					<div class="tooltip1">
					'.__('check this if you would not want to disable Flash of Unstyled Content in the slider when the page is loaded.','featured-slider').'
					</div>
				</span>
				</td>
				</tr>
		</table>

		</div>';
	} elseif($tab == 'navarrow') {
		$html .= '<div class="sub_settings  toggle_settings">
		<h2 class="sub-heading">'.__('Navigational Arrows','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" id="minmax_img" class="toggle_img"></h2> 

		<table class="form-table">

		<tr valign="top">
		<th scope="row">'.__('Navigation Arrows','featured-slider').'</th>
		<td>
			<div class="eb-switchnone">
				<input type="hidden" name="'. $featured_slider_options.'[prev_next]" id="featured_slider_prev_next" class="hidden_check" value="'. $featured_slider_curr['prev_next'].'">
				<input id="featured_enablearrowsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['prev_next'],false).'>
				<label for="featured_enablearrowsett"></label>
			</div>
		</td>
		</tr>

		<tr valign="top" >
		<th scope="row">'.__('Select Navigation Arrow','featured-slider').'</th>
		<td style="background: #ddd;">';
		$directory = FEATURED_SLIDER_CSS_OUTER.'/buttons/';
		if ($handle = opendir($directory)) {
		    while (false !== ($file = readdir($handle))) { 
		     if($file != '.' and $file != '..') { 
		     $nexturl='var/buttons/'.$file.'/next.png';
		$html .= '<div class="arrows"><img src="'.featured_slider_plugin_url($nexturl).'" width="24" height="24"/>
				<input name="'.$featured_slider_options.'[buttons]" type="radio" id="featured_slider_buttons" class="arrows_input" value="'. $file.'" '.checked($file,$featured_slider_curr['buttons'],false).' /></div>';
		  } }
		    closedir($handle);
		}
		$html .= '<div class="svilla_cl"></div>
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row">'.__('Navigation Arrow Size','featured-slider').'</th>
		<td><input type="number" min="16" max="128" name="'.$featured_slider_options.'[nav_w]" id="featured_slider_nav_w" class="small-text" value="'.$featured_slider_curr['nav_w'].'"  />&nbsp; <span>X</span> <input type="number" min="16" max="128" name="'.$featured_slider_options.'[nav_h]" id="featured_slider_nav_h" class="small-text" value="'.$featured_slider_curr['nav_h'].'" />&nbsp;px</td>
		</tr>
		
		<tr valign="top">
		<th scope="row">'.__('Distance from left and right','featured-slider').'</th>
		<td><input type="number" name="'.$featured_slider_options.'[nav_margin]" id="featured_slider_nav_margin" class="small-text" value="'.$featured_slider_curr['nav_margin'].'" min="0" />&nbsp;px</td>
		</tr>
		</table>
		<p class="submit">
			<input type="submit" name="save_settings" class="button-primary" value="'.__('Save Changes','featured-slider').'" />
			<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />		
		</p>
		</div>';
	
	} elseif($tab == 'woo') {
	
		$html .= '<div class="sub_settings toggle_settings">
<h2 class="sub-heading">'.__('Add to cart','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" class="toggle_img"></h2> 

<table class="form-table">
<tr valign="top">
<th scope="row">'.__('Add to Cart','featured-slider').'</th>
<td>
	<div class="eb-switch eb-switchnone">
		<input type="hidden" name="'.$featured_slider_options.'[enable_wooaddtocart]" id="featured_slider_enable_wooaddtocart" class="hidden_check" value="'.$featured_slider_curr['enable_wooaddtocart'].'">
		<input id="featured_enablewooaddtocartsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['enable_wooaddtocart'],false).'>
		<label for="featured_enablewooaddtocartsett"></label>
	</div>
</td>
</tr>

<tr valign="top">
<th scope="row">'.__('Button Text','featured-slider').'</th>
<td><input type="text" name="'.$featured_slider_options.'[woo_adc_text]" id="featured_woo_adc_text" class="regular-text" value="'.$featured_slider_curr['woo_adc_text'].'" /></td>
</tr>

<tr valign="top">
<th scope="row">'.__('Button Color','featured-slider').'</th>
<td><input type="text" name="'.$featured_slider_options.'[woo_adc_color]" id="featured_woo_adc_color" value="'.$featured_slider_curr['woo_adc_color'].'" class="wp-color-picker-field" data-default-color="#3DB432" /></td>
</tr>

<tr valign="top">
<th scope="row">'.__('Button text Color','featured-slider').'</th>
<td><input type="text" name="'.$featured_slider_options.'[woo_adc_tcolor]" id="featured_woo_adc_tcolor" value="'.$featured_slider_curr['woo_adc_tcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" /></td>
</tr>

<tr valign="top">
<th scope="row">'.__('Button Size','featured-slider').'</th>
<td><input type="number" name="'.$featured_slider_options.'[woo_adc_fsize]" id="featured_woo_adc_fsize" class="small-text" value="'.$featured_slider_curr['woo_adc_fsize'].'" min="1" />&nbsp;'.__('px','featured-slider').'</td>
</tr>


<tr valign="top">
<th scope="row">'.__('Border Thickness','featured-slider').'</th>
<td><input type="number" name="'.$featured_slider_options.'[woo_adc_border]" id="featured_woo_adc_border" class="small-text" value="'.$featured_slider_curr['woo_adc_border'].'" min="0" />&nbsp;'.__('px  (put 0 if no border is required)','featured-slider').'</td>
</tr>

<tr valign="top">
<th scope="row">'.__('Border Color','featured-slider').'</th>
<td><input type="text" name="'.$featured_slider_options.'[woo_adc_brcolor]" id="featured_woo_adc_brcolor" value="'.$featured_slider_curr['woo_adc_brcolor'].'" class="wp-color-picker-field" data-default-color="#D8E7EE" /></td>
</tr>
</table>
<p class="submit">
	<input type="submit" class="button-primary" name="save_settings" value="'.__('Save Changes','featured-slider').'" />
	<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
</p>
</div>
<!-- Sale Strip -->
			<div class="sub_settings toggle_settings">
				<h2 class="sub-heading">'.__('Slide Sale strip','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" class="toggle_img"></h2> 
				<table class="form-table">
					<tr valign="top">
						<th scope="row">'.__('Sale Strip','featured-slider').'</th>
						<td>
							<div class="eb-switch eb-switchnone">
								<input type="hidden" name="'.$featured_slider_options.'[enable_woosalestrip]" id="featured_slider_enable_woosalestrip" class="hidden_check" value="'.$featured_slider_curr['enable_woosalestrip'].'">
								<input id="featured_enablewoosalestripsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['enable_woosalestrip'],false).'>
								<label for="featured_enablewoosalestripsett"></label>
							</div> 
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">'.__('Strip Text','featured-slider').'</th>
						<td><input type="text" name="'.$featured_slider_options.'[woo_sale_text]" id="featured_woo_saletext" class="regular-text" value="'.$featured_slider_curr['woo_sale_text'].'" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">'.__('Strip Color','featured-slider').'</th>
						<td><input type="text" name="'.$featured_slider_options.'[woo_sale_color]" id="featured_woo_salecolor" value="'.$featured_slider_curr['woo_sale_color'].'" class="wp-color-picker-field" data-default-color="#D8E7EE" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">'.__('Text Color','featured-slider').'</th>
						<td><input type="text" name="'.$featured_slider_options.'[woo_sale_tcolor]" id="featured_woo_saletcolor" value="'.$featured_slider_curr['woo_sale_tcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" /></td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" name="save_settings" value="'.__('Save Changes','featured-slider').'" />
					<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
				</p>
			</div>
			<!-- Sale Strip ends -->
<!-- slide price -->
<div class="sub_settings toggle_settings">
<h2 class="sub-heading">'.__('Regular price','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" class="toggle_img"></h2> 

<table class="form-table settings-tbl">

<tr valign="top">
<th scope="row">'.__('Regular Price','featured-slider').'</th>
<td>
	<div class="eb-switch eb-switchnone">
			<input type="hidden" name="'.$featured_slider_options.'[enable_wooregprice]" id="featured_slider_enable_wooregprice" class="hidden_check" value="'.$featured_slider_curr['enable_wooregprice'].'">
			<input id="featured_enablewooregpricesett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['enable_wooregprice'],false).'>
			<label for="featured_enablewooregpricesett"></label>
	</div> 
</td>
</tr>

<!-- code for new fonts -->
<tr valign="top">
<th scope="row">'.__('Font Type','featured-slider').'</th>
<td>
		<input type="hidden" value="slide_woo_price_font" class="ftype_rname">
		<input type="hidden" value="slide_woo_price_fontg" class="ftype_gname">
		<input type="hidden" value="slide_woo_price_custom" class="ftype_cname">
		<select name="'.$featured_slider_options.'[woo_font]" id="featured_slider_woo_font" class="main-font">
			<option value="regular" '.selected( $featured_slider_curr['woo_font'], "regular", false ).' > Regular Fonts </option>
			<option value="google" '.selected( $featured_slider_curr['woo_font'], "google", false ).' > Google Fonts </option>
			<option value="custom" '.selected( $featured_slider_curr['woo_font'], "custom", false ).' > Custom Fonts </option>
		</select>
</td>
</tr>

<tr><td class="load-fontdiv" colspan="2"></td></tr>
<!-- code for new fonts -->



<tr valign="top">
<th scope="row">'.__('Font Color','featured-slider').'</th>
<td><input type="text" name="'.$featured_slider_options.'[slide_woo_price_fcolor]" id="featured_slide_wooprice_fcolor" value="'.$featured_slider_curr['slide_woo_price_fcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" /></td>
</tr>

<tr valign="top">
<th scope="row">'.__('Font Size','featured-slider').'</th>
<td><input type="number" name="'.$featured_slider_options.'[slide_woo_price_fsize]" id="featured_slide_wooprice_fsize" class="small-text" value="'.$featured_slider_curr['slide_woo_price_fsize'].'" min="1" />&nbsp;'.__('px','featured-slider').'</td>
</tr>

<tr valign="top" class="font-style">
<th scope="row">'.__('Font Style','featured-slider').'</th>
	<td><select name="'.$featured_slider_options.'[slide_woo_price_fstyle]" id="featured_slide_wooprice_fstyle" >
	<option value="bold" '.selected("bold",$featured_slider_curr['slide_woo_price_fstyle'], false).' >'.__('Bold','featured-slider').'</option>
	<option value="bold italic" '.selected("bold italic",$featured_slider_curr['slide_woo_price_fstyle'], false).' >'.__('Bold Italic','featured-slider').'</option>
	<option value="italic" '.selected("italic",$featured_slider_curr['slide_woo_price_fstyle'], false).' >'.__('Italic','featured-slider').'</option>
	<option value="normal" '.selected("normal",$featured_slider_curr['slide_woo_price_fstyle'], false).' >'.__('Normal','featured-slider').'</option>
	</select>
	</td>
</tr>
</table>
<p class="submit">
	<input type="submit" class="button-primary" name="save_settings" value="'.__('Save Changes','featured-slider').'" />
	<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
</p>
</div>
<!-- slide price ends-->

<!-- slide sale price -->
<div class="sub_settings toggle_settings">
<h2 class="sub-heading">'.__('Sale price','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" class="toggle_img"></h2> 

<table class="form-table settings-tbl">

<tr valign="top"> 
<th scope="row">'.__('Sale Price','featured-slider').'</th>
<td>
	<div class="eb-switch eb-switchnone">
		<input type="hidden" name="'.$featured_slider_options.'[enable_woosprice]" id="featured_slider_enable_woosprice" class="hidden_check" value="'.$featured_slider_curr['enable_woosprice'].'">
		<input id="featured_enablewoospricesett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['enable_woosprice'],false).'>
		<label for="featured_enablewoospricesett"></label>
	</div> 
</td>
</tr>

<!-- code for new fonts -->
<tr valign="top">
<th scope="row">'.__('Font Type','featured-slider').'</th>
<td>
<input type="hidden" value="slide_woo_saleprice_font" class="ftype_rname">
<input type="hidden" value="slide_woo_saleprice_fontg" class="ftype_gname">
<input type="hidden" value="slide_woo_saleprice_custom" class="ftype_cname">
	<select name="'.$featured_slider_options.'[woosale_font]" id="featured_slider_woosale_font" class="main-font">
		<option value="regular" '.selected( $featured_slider_curr['woosale_font'], "regular", false ).' > Regular Fonts </option>
		<option value="google" '.selected( $featured_slider_curr['woosale_font'], "google", false ).' > Google Fonts </option>
		<option value="custom" '.selected( $featured_slider_curr['woosale_font'], "custom", false ).' > Custom Fonts </option>
	</select>
</td>
</tr>

<tr><td class="load-fontdiv" colspan="2"></td></tr>
<!-- code for new fonts -->


<tr valign="top">
<th scope="row">'.__('Font Color','featured-slider').'</th>
<td><input type="text" name="'.$featured_slider_options.'[slide_woo_saleprice_fcolor]" id="featured_slide_woosaleprice_fcolor" value="'.$featured_slider_curr['slide_woo_saleprice_fcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" /></td>
</tr>

<tr valign="top">
<th scope="row">'.__('Font Size','featured-slider').'</th>
<td><input type="number" name="'.$featured_slider_options.'[slide_woo_saleprice_fsize]" id="featured_slide_woosaleprice_fsize" class="small-text" value="'.$featured_slider_curr['slide_woo_saleprice_fsize'].'" min="1" />&nbsp;'.__('px','featured-slider').'</td>
</tr>

<tr valign="top" class="font-style">
<th scope="row">'.__('Font Style','featured-slider').'</th>
<td><select name="'.$featured_slider_options.'[slide_woo_saleprice_fstyle]" id="featured_slide_woosaleprice_fstyle" >
	<option value="bold" '.selected( "bold",$featured_slider_curr['slide_woo_saleprice_fstyle'], false).' >'.__('Bold','featured-slider').'</option>
	<option value="bold italic" '.selected( "bold italic",$featured_slider_curr['slide_woo_saleprice_fstyle'], false).' >'.__('Bold Italic','featured-slider').'</option>
	<option value="italic" '.selected( "italic",$featured_slider_curr['slide_woo_saleprice_fstyle'], false).' >'.__('Italic','featured-slider').'</option>
	<option value="normal" '.selected( "normal",$featured_slider_curr['slide_woo_saleprice_fstyle'], false).' >'.__('Normal','featured-slider').'</option>
	</select>
</td>
</tr>
</table>
<p class="submit">
	<input type="submit" class="button-primary" name="save_settings" value="'.__('Save Changes','featured-slider').'" />
	<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
</p>
</div>
<!-- slide sale price ends -->

<div class="sub_settings toggle_settings">
<h2 class="sub-heading">'.__('Category','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" class="toggle_img"></h2> 

<table class="form-table settings-tbl">

<tr valign="top"> 
<th scope="row">'.__('Category','featured-slider').'</th>
<td>
	<div class="eb-switch eb-switchnone">
		<input type="hidden" name="'.$featured_slider_options.'[enable_woocat]" id="featured_slider_enable_woocat" class="hidden_check" value="'.$featured_slider_curr['enable_woocat'].'">
		<input id="featured_enablewoocatsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['enable_woocat'],false).'>
		<label for="featured_enablewoocatsett"></label>
	</div> 
</td>
</tr>

<!-- code for new fonts -->
<tr valign="top">
<th scope="row">'.__('Font Type','featured-slider').'</th>
<td>
<input type="hidden" value="slide_woo_cat_font" class="ftype_rname">
<input type="hidden" value="slide_woo_cat_fontg" class="ftype_gname">
<input type="hidden" value="slide_woo_cat_custom" class="ftype_cname">
	<select name="'.$featured_slider_options.'[woocat_font]" id="featured_slider_woocat_font" class="main-font">
		<option value="regular" '.selected( $featured_slider_curr['woocat_font'], "regular", false ).' > Regular Fonts </option>
		<option value="google" '.selected( $featured_slider_curr['woocat_font'], "google", false ).' > Google Fonts </option>
		<option value="custom" '.selected( $featured_slider_curr['woocat_font'], "custom", false ).' > Custom Fonts </option>
	</select>
</td>
</tr>

<tr><td class="load-fontdiv" colspan="2"></td></tr>
<!-- code for new fonts -->



<tr valign="top">
<th scope="row">'.__('Font Color','featured-slider').'</th>
<td><input type="text" name="'.$featured_slider_options.'[slide_woo_cat_fcolor]" id="featured_slide_woocat_fcolor" value="'.$featured_slider_curr['slide_woo_cat_fcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" /></td>
</tr>

<tr valign="top">
<th scope="row">'.__('Font Size','featured-slider').'</th>
<td><input type="number" name="'.$featured_slider_options.'[slide_woo_cat_fsize]" id="featured_slide_woocat_fsize" class="small-text" value="'.$featured_slider_curr['slide_woo_cat_fsize'].'" min="1" />&nbsp;'.__('px','featured-slider').'</td>
</tr>

<tr valign="top" class="font-style">
<th scope="row">'.__('Font Style','featured-slider').'</th>
<td><select name="'.$featured_slider_options.'[slide_woo_cat_fstyle]" id="featured_slide_woocat_fstyle" >
	<option value="bold" '.selected("bold",$featured_slider_curr['slide_woo_cat_fstyle'], false).'>'.__('Bold','featured-slider').'</option>
	<option value="bold italic" '.selected("bold italic",$featured_slider_curr['slide_woo_cat_fstyle'], false).' >'.__('Bold Italic','featured-slider').'</option>
	<option value="italic" '.selected("italic",$featured_slider_curr['slide_woo_cat_fstyle'], false).' >'.__('Italic','featured-slider').'</option>
	<option value="normal" '.selected("normal",$featured_slider_curr['slide_woo_cat_fstyle'], false).' >'.__('Normal','featured-slider').'</option>
	</select>
</td>
</tr>
</table>
<p class="submit">
	<input type="submit" class="button-primary" name="save_settings" value="'.__('Save Changes','featured-slider').'" />
	<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
</p>
</div>
<!-- woo category ends -->

<div class="sub_settings toggle_settings">
<h2 class="sub-heading">'.__('Stars','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" class="toggle_img"></h2> 

<table class="form-table">

<tr valign="top">
<th scope="row">'.__('Stars','featured-slider').'</th>
<td>
	<div class="eb-switch eb-switchnone">
		<input type="hidden" name="'.$featured_slider_options.'[enable_woostar]" id="featured_slider_enable_woostar" class="hidden_check" value="'.$featured_slider_curr['enable_woostar'].'">
		<input id="featured_enablewoostarsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['enable_woostar'],false).'>
		<label for="featured_enablewoostarsett"></label>
	</div> 
</td>
</td>

<tr valign="top">
<th scope="row">'.__('Select Navigation Star','featured-slider').'</th>
<td style="display: flex;">';
$url='images/star/gold.png';
$url1='images/star/black.png';
$url2='images/star/red.png'; 
$url3='images/star/green.png';
$url4='images/star/grogreen.png';
$url5='images/star/yellow.png';
$url6='images/star/grored.png';
$url7='images/star/groyellow.png';
 
$html .= '<div class="arrows"><img src="'.featured_slider_plugin_url($url).'" width="16" height="16" />
	<input name="'.$featured_slider_options.'[nav_woo_star]" type="radio" id="featured_nav_woo_star1" class="woo_star" value="gold" '.checked('gold',$featured_slider_curr['nav_woo_star'],false).'/> </div>
		<div class="arrows"><img src="'.featured_slider_plugin_url($url1).'" width="16" height="16" />
		<input name="'.$featured_slider_options.'[nav_woo_star]" type="radio" id="featured_nav_woo_star2" class="woo_star" value="black" '.checked('black',$featured_slider_curr['nav_woo_star'],false).' /> </div>

		<div class="arrows"><img src="'.featured_slider_plugin_url($url2).'" width="16" height="16" />
		<input name="'.$featured_slider_options.'[nav_woo_star]" type="radio" id="featured_nav_woo_star3" class="woo_star" value="red" '.checked('red',$featured_slider_curr['nav_woo_star'],false).' /> </div>

		<div class="arrows"><img src="'.featured_slider_plugin_url($url3).'" width="16" height="16" />
		<input name="'.$featured_slider_options.'[nav_woo_star]" type="radio" id="featured_nav_woo_star4" class="woo_star" value="green" '.checked('green',$featured_slider_curr['nav_woo_star'],false).' /> </div>

		<div class="arrows"><img src="'.featured_slider_plugin_url($url4).'" width="16" height="16" />
		<input name="'.$featured_slider_options.'[nav_woo_star]" type="radio" id="featured_nav_woo_star5" class="woo_star" value="grogreen" '.checked('grogreen',$featured_slider_curr['nav_woo_star'],false).' /> </div>

		<div class="arrows"><img src="'.featured_slider_plugin_url($url5).'" width="16" height="16"/>
		<input name="'.$featured_slider_options.'[nav_woo_star]" type="radio" id="featured_nav_woo_star6" class="woo_star" value="yellow" '.checked('yellow',$featured_slider_curr['nav_woo_star'],false).' /> </div>

		<div class="arrows"><img src="'.featured_slider_plugin_url($url6).'" width="16" height="16"/>
		<input name="'.$featured_slider_options.'[nav_woo_star]" type="radio" id="featured_nav_woo_star7" class="woo_star" value="grored" '.checked('grored',$featured_slider_curr['nav_woo_star'],false).' /> </div>

		<div class="arrows"><img src="'.featured_slider_plugin_url($url7).'" width="16" height="16"/>
		<input name="'.$featured_slider_options.'[nav_woo_star]" type="radio" id="featured_nav_woo_star8" class="woo_star" value="groyellow" '.checked('groyellow',$featured_slider_curr['nav_woo_star'],false).' /> </div>
		</div>
		</td>
	</tr>
</table>
<p class="submit">
	<input type="submit" class="button-primary" name="save_settings" value="'.__('Save Changes','featured-slider').'" />
	<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
</p>
</div>';
	} elseif($tab == 'events') {	
	
		$html .= '<div class="sub_settings toggle_settings">
		<h2 class="sub-heading">'.__('Slide date-time','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" class="toggle_img"></h2> 

		<table class="form-table settings-tbl">

		<tr valign="top"> 
		<th scope="row">'.__('Event date-time','featured-slider').'</th>
			<td>
			<div class="eb-switch eb-switchnone">
				<input type="hidden" name="'.$featured_slider_options.'[enable_eventdt]" id="featured_slider_enable_eventdt" class="hidden_check" value="'.$featured_slider_curr['enable_eventdt'].'">
				<input id="featured_enableeventdtsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['enable_eventdt'],false).'>
				<label for="featured_enableeventdtsett"></label>
			</div> 
		</td>
		</tr>

		<!-- code for new fonts -->
		<tr valign="top">
		<th scope="row">'.__('Font Type','featured-slider').'</th>
		<td>
		<input type="hidden" value="slide_eventm_font" class="ftype_rname">
		<input type="hidden" value="slide_eventm_fontg" class="ftype_gname">
		<input type="hidden" value="slide_eventm_custom" class="ftype_cname">
		<select name="'.$featured_slider_options.'[eventmd_font]" id="featured_slider_eventmd_font" class="main-font">
				<option value="regular" '.selected( $featured_slider_curr['eventmd_font'], "regular", false ).' > Regular Fonts </option>
				<option value="google" '.selected( $featured_slider_curr['eventmd_font'], "google", false ).' > Google Fonts </option>
				<option value="custom" '.selected( $featured_slider_curr['eventmd_font'], "custom", false ).' > Custom Fonts </option>
		</select>
		</td>
		</tr>

		<tr><td class="load-fontdiv" colspan="2"></td></tr>
		<!-- code for new fonts -->

		<tr valign="top">
		<th scope="row">'.__('Font Color','featured-slider').'</th>
		<td><input type="text" name="'.$featured_slider_options.'[slide_eventm_fcolor]" id="featured_slide_woocat_fcolor" value="'. $featured_slider_curr['slide_eventm_fcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" /></td>
		</tr>

		<tr valign="top">
			<th scope="row">'.__('Font Size','featured-slider').'</th>
			<td><input type="number" name="'.$featured_slider_options.'[slide_eventm_fsize]" id="featured_slide_woocat_fsize" class="small-text" value="'.$featured_slider_curr['slide_eventm_fsize'].'" min="1" />&nbsp;'.__('px','featured-slider').'</td>
		</tr>

		<tr valign="top" class="font-style">
		<th scope="row">'.__('Font Style','featured-slider').'</th>
		<td><select name="'.$featured_slider_options.'[slide_eventm_fstyle]" id="featured_slide_eventm_fstyle" >
			<option value="bold" '.selected("bold",$featured_slider_curr['slide_eventm_fstyle'], false).' >'.__('Bold','featured-slider').'</option>
			<option value="bold italic" '.selected("bold italic",$featured_slider_curr['slide_eventm_fstyle'], false).' >'.__('Bold Italic','featured-slider').'</option>
			<option value="italic" '.selected("italic",$featured_slider_curr['slide_eventm_fstyle'], false).' >'.__('Italic','featured-slider').'</option>
			<option value="normal" '.selected("normal",$featured_slider_curr['slide_eventm_fstyle'], false).' >'.__('Normal','featured-slider').'</option>
							</select>
		</td>
		</tr>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" name="save_settings" value="'.__('Save Changes','featured-slider').'" />
			<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
		</p>
		</div>
		<!-- slide date-time end>


		<!-- event address-->
		<div class="sub_settings toggle_settings">
		<h2 class="sub-heading">'.__('Event address','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" class="toggle_img"></h2> 

		<table class="form-table settings-tbl">
		<tr valign="top"> 
		<th scope="row">'.__('Event Address','featured-slider').'</th>
		<td>
		<div class="eb-switch eb-switchnone">
			<input type="hidden" name="'.$featured_slider_options.'[enable_eventadd]" id="featured_slider_enable_eventadd" class="hidden_check" value="'. $featured_slider_curr['enable_eventadd'].'">
			<input id="featured_enableeventaddsett" class="cmn-toggle eb-toggle-round" type="checkbox" '. checked('1', $featured_slider_curr['enable_eventadd'],false).'>
			<label for="featured_enableeventaddsett"></label>
		</div> 
		</td>
		</tr>
		<!-- code for new fonts -->
		<tr valign="top">
		<th scope="row">'.__('Font Type','featured-slider').'</th>
		<td>
		<input type="hidden" value="eventm_addr_font" class="ftype_rname">
		<input type="hidden" value="eventm_addr_fontg" class="ftype_gname">
		<input type="hidden" value="eventm_addr_custom" class="ftype_cname">
			<select name="'.$featured_slider_options.'[event_addr_font]" id="featured_slider_event_addr_font" class="main-font">
				<option value="regular" '.selected( $featured_slider_curr['event_addr_font'], "regular", false ).' > Regular Fonts </option>
				<option value="google" '.selected( $featured_slider_curr['event_addr_font'], "google", false ).' > Google Fonts </option>
				<option value="custom" '.selected( $featured_slider_curr['event_addr_font'], "custom", false ).' > Custom Fonts </option>
			</select>
		</td>
		</tr>

		<tr><td class="load-fontdiv" colspan="2"></td></tr>
		<!-- code for new fonts -->
		<tr valign="top">
		<th scope="row">'.__('Font Color','featured-slider').'</th>
		<td><input type="text" name="'.$featured_slider_options.'[eventm_addr_fcolor]" id="featured_slider_eventm_fcolor" value="'. $featured_slider_curr['eventm_addr_fcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" /></td>
		</tr>

		<tr valign="top">
		<th scope="row">'.__('Font Size','featured-slider').'</th>
		<td><input type="number" name="'.$featured_slider_options.'[eventm_addr_fsize]" id="featured_slider_eventm_fsize" class="small-text" value="'. $featured_slider_curr['eventm_addr_fsize'].'" min="1" />&nbsp;'.__('px','featured-slider').'</td>
		</tr>

		<tr valign="top" class="font-style">
		<th scope="row">'.__('Font Style','featured-slider').'</th>
		<td><select name="'.$featured_slider_options.'[eventm_addr_fstyle]" id="featured_slide_eventm_fstyle" >
			<option value="bold" '.selected("bold",$featured_slider_curr['eventm_addr_fstyle'],false).'>'.__('Bold','featured-slider').'</option>
			<option value="bold italic" '.selected("bold italic",$featured_slider_curr['eventm_addr_fstyle'],false).' >'.__('Bold Italic','featured-slider').'</option>
			<option value="italic" '.selected("italic",$featured_slider_curr['eventm_addr_fstyle'],false).' >'.__('Italic','featured-slider').'</option>
			<option value="normal" '.selected("normal",$featured_slider_curr['eventm_addr_fstyle'],false).' >'.__('Normal','featured-slider').'</option>
		</select>
		</td>
		</tr>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" name="save_settings" value="'.__('Save Changes','featured-slider').'" />
			<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
		</p>
		</div>
		<!-- event address ends>

		<!-- event category-->
		<div class="sub_settings toggle_settings">
		<h2 class="sub-heading">'.__('Event Category','featured-slider').'<img src="'.featured_slider_plugin_url( 'images/close.png' ).'" class="toggle_img"></h2> 

		<table class="form-table settings-tbl">
		<tr valign="top"> 
		<th scope="row">'.__('Event Category','featured-slider').'</th>
		<td>
		<div class="eb-switch eb-switchnone">
			<input type="hidden" name="'.$featured_slider_options.'[enable_eventcat]" id="featured_slider_enable_eventcat" class="hidden_check" value="'.$featured_slider_curr['enable_eventcat'].'">
			<input id="featured_enableeventcatsett" class="cmn-toggle eb-toggle-round" type="checkbox" '.checked('1', $featured_slider_curr['enable_eventcat'],false).'>
			<label for="featured_enableeventcatsett"></label>
		</div>  
		</td>
		</tr>

		<!-- code for new fonts -->
		<tr valign="top">
		<th scope="row">'.__('Font Type','featured-slider').'</th>
		<td>
		<input type="hidden" value="eventm_cat_font" class="ftype_rname">
		<input type="hidden" value="eventm_cat_fontg" class="ftype_gname">
		<input type="hidden" value="eventm_cat_custom" class="ftype_cname">
		<select name="'.$featured_slider_options.'[event_cat_font]" id="featured_slider_event_cat_font" class="main-font">
			<option value="regular" '.selected( $featured_slider_curr['event_cat_font'], "regular", false ).' > Regular Fonts </option>
			<option value="google" '.selected( $featured_slider_curr['event_cat_font'], "google", false ).' > Google Fonts </option>
			<option value="custom" '.selected( $featured_slider_curr['event_cat_font'], "custom", false ).' > Custom Fonts </option>
		</select>
		</td>
		</tr>

		<tr><td class="load-fontdiv" colspan="2"></td></tr>
		<!-- code for new fonts -->

		<tr valign="top">
		<th scope="row">'.__('Font Color','featured-slider').'</th>
		<td><input type="text" name="'.$featured_slider_options.'[eventm_cat_fcolor]" id="featured_slide_eventm_fcolor" value="'. $featured_slider_curr['eventm_cat_fcolor'].'" class="wp-color-picker-field" data-default-color="#a6a6a6" /></td>
		</tr>

		<tr valign="top">
		<th scope="row">'.__('Font Size','featured-slider').'</th>
		<td><input type="number" name="'.$featured_slider_options.'[eventm_cat_fsize]" id="featured_slide_eventm_fsize" class="small-text" value="'. $featured_slider_curr['eventm_cat_fsize'].'" min="1" />&nbsp;'.__('px','featured-slider').'</td>
		</tr>

		<tr valign="top"  class="font-style">
		<th scope="row">'.__('Font Style','featured-slider').'</th>
		<td><select name="'.$featured_slider_options.'[eventm_cat_fstyle]" id="featured_slide_eventm_fstyle" >
			<option value="bold" '.selected("bold",$featured_slider_curr['eventm_cat_fstyle'], false).' >'.__('Bold','featured-slider').'</option>
			<option value="bold italic" '.selected("bold italic",$featured_slider_curr['eventm_cat_fstyle'], false).' >'.__('Bold Italic','featured-slider').'</option>
			<option value="italic" '.selected("italic",$featured_slider_curr['eventm_cat_fstyle'], false).' >'.__('Italic','featured-slider').'</option>
			<option value="normal" '.selected("normal",$featured_slider_curr['eventm_cat_fstyle'], false).' >'.__('Normal','featured-slider').'</option>
		</select>
		</td>
		</tr>
		</table>
		<p class="submit">
				<input type="submit" class="button-primary" name="save_settings" value="'.__('Save Changes','featured-slider').'" />
				<input type="submit" name="undo_settings" class="featured-undo" style="'.$undo_style.'" value="'.__('Undo','featured-slider').'" />
		</p>
		</div>
		<!-- event category ends-->';
	}
	echo $html;
	die();	              
} // featured_tab_contents end

?>
