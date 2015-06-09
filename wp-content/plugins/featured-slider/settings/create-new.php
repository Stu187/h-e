<?php
function featured_process_create_new_requests() {
	// Create new Image slider
	if ( isset($_POST['addSave']) and ($_POST['addSave']=='Save') ) { 
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
			if(!featured_slider($id,$slider_id)) {
					$dt = date('Y-m-d H:i:s');
					$sql = "INSERT INTO ".$table_prefix.FEATURED_SLIDER_TABLE." (post_id, date, slider_id) VALUES ('$id', '$dt', '$slider_id')";
					$wpdb->query($sql);
			}
		}
		$urlarg = array();
		$current_url = admin_url('admin.php?page=featured-slider-easy-builder');
		$urlarg['id'] = $slider_id;
		$query_arg = add_query_arg( $urlarg ,$current_url);
		$current_url = $query_arg;
		wp_redirect( $current_url );
		exit;
	}
	if(isset ($_POST['step4-next']) || isset($_POST['step3-create']) ) { 
		global $wpdb,$table_prefix;
		$default_featured_slider_settings=get_featured_slider_default_settings();
		$slider_meta = $table_prefix.FEATURED_SLIDER_META;
		$type = $_POST["slider-type"];
		$slider_name = $_POST["new_slider_name"];
		$offset = $_POST["offset"];
		$param_array = array();
		if(isset ($_POST['step3-create'])) {
			$scounter = $_POST["set"];
		}
		if(isset ($_POST['step4-next'])) {
			$skin = $_POST['skin'];
			$layout = $_POST["featured-layout"];
			$layout_pureimg = $_POST["featured-check-pureimg"];
			$layout_blocks = $_POST["featured-check-blocks"];
			// Create New Setting Set
			$featured_curr_setting = array();
			foreach($default_featured_slider_settings as $key=>$value)
			{
				$featured_curr_setting[$key]= $value;
			}
			foreach($featured_curr_setting as $key=>$value)
			{
				if($key == "stylesheet") {
					$featured_curr_setting[$key] = $skin;
				} 
				elseif($layout == "left_sub") {
					if($key == "block_pos") {
						$featured_curr_setting[$key] = 0;
					} 
				}
				elseif($layout == "right_list") {
					if($key == "trio_block") {
						$featured_curr_setting[$key] = 4;
					} 
				}
				elseif($layout == "left_utwo") { 
					if($key == "trio_block") {
						$featured_curr_setting[$key] = 2;
					} 
					if($key == "block_pos") {
						$featured_curr_setting[$key] = 0;
					} 
				}
				elseif($layout == "left_ltwo") { 
					if($key == "trio_block") {
						$featured_curr_setting[$key] = 3;
					} 
					if($key == "block_pos") {
						$featured_curr_setting[$key] = 0;
					} 
				} 
				elseif($layout == "right_utwo") {
					if($key == "trio_block") {
						$featured_curr_setting[$key] = 2;
					} 
				}
				elseif($layout == "left_list") {
					if($key == "trio_block") {
						$featured_curr_setting[$key] = 4;
					} 
					if($key == "block_pos") {
						$featured_curr_setting[$key] = 0;
					} 
				}
				elseif($layout == "right_two_sub") { 
					if($key == "disable_thumbs") {
						$featured_curr_setting[$key] = 1;
					} 
				}
				if($layout_blocks == "1") { 
					if($key == "fixblocks") {
						$featured_curr_setting[$key] = 1;
					} 
					if($key == "prev_next") {
						$featured_curr_setting[$key] = 0;
					}
				}  
				if($layout_pureimg == "1") { 
					if($key == "image_only") {
						$featured_curr_setting[$key] = 1;
					}
				}
				if($type == 11 ||$type == 12 ||$type == 13) {
					if($key == "image_only") {
						$featured_curr_setting[$key] = 1;
					} 
				}
			}
			$scounter=get_option('featured_slider_scounter');
			$scounter++;
			update_option('featured_slider_scounter',$scounter);
			$options='featured_slider_options'.$scounter;
			update_option($options,$featured_curr_setting);
		}
		if($_POST['offset'] != '0' && $_POST['offset'] != '') {
			$param_array['offset']=$_POST['offset'];
		}
		if($type == '1' ) {
			if($_POST['catg_slug'] != '0' && $_POST['catg_slug'] != '') {
				$param_array['catg_slug']=$_POST['catg_slug'];
			}
		} elseif($type == '3' ) {
			if(isset($_POST['woo_slider_type']) && $_POST['woo_slider_type'] != '') {
				$param_array['woo_slider_type']=$_POST['woo_slider_type'];
			}
			if(isset($_POST['product_id']) && $_POST['product_id'] != '') {
				$param_array['product_id']=$_POST['product_id'];
			}
			if(isset($_POST['woo-catg']) && $_POST['woo-catg'] != '') {
				$param_array['woo-catg']=$_POST['woo-catg'];
			}
			$param_array['post_type']='product';
		} elseif($type == '4' ) {
			if(isset($_POST['ecom-catg']) && $_POST['ecom-catg'] != '') {
				$param_array['ecom-catg']=$_POST['ecom-catg'];
			}
			if(isset($_POST['ecom_slider_type']) && $_POST['ecom_slider_type'] != '') {
				$param_array['ecom_slider_type']=$_POST['ecom_slider_type'];
			}
			$param_array['post_type']='wpsc-product';
			
		} elseif($type == '5' ) {
			if(isset($_POST['eventm_slider_scope']) && $_POST['eventm_slider_scope'] != '') {
				$param_array['eventm_slider_scope']=$_POST['eventm_slider_scope'];
			}
			if(isset($_POST['eman-catg']) && $_POST['eman-catg'] != '') {
				$param_array['eman-catg']=$_POST['eman-catg'];
			}
			if(isset($_POST['eman-tags']) && $_POST['eman-tags'] != '') {
				$param_array['eman-tags']=$_POST['eman-tags'];
			}
			$param_array['post_type']='event';
		} elseif($type == '6' ) {
			if(isset($_POST['eventcal_slider_type']) && $_POST['eventcal_slider_type'] != '') {
				$param_array['eventcal_slider_type']=$_POST['eventcal_slider_type'];
			}
			if(isset($_POST['ecal-catg']) && $_POST['ecal-catg'] != '') {
				$param_array['ecal-catg']=$_POST['ecal-catg'];
			}
			if(isset($_POST['ecal-tags']) && $_POST['ecal-tags'] != '') {
				$param_array['ecal-tags']=$_POST['ecal-tags'];
			}
			$param_array['post_type']='tribe_events';
		} elseif($type == '7' ) {
			// Taxonomy Slider
			if(isset($_POST['taxo_posttype']) && $_POST['taxo_posttype'] != '') {
				$param_array['post_type']=$_POST['taxo_posttype'];
			}
			if(isset($_POST['taxonomy_name']) && $_POST['taxonomy_name'] != '') {
				$param_array['taxonomy_name']=$_POST['taxonomy_name'];
			}
			if(isset($_POST['taxonomy_term']) && $_POST['taxonomy_term'] != '') {
				$param_array['taxonomy_term']=$_POST['taxonomy_term'];
			}	
			if(isset($_POST['taxonomy_operator']) && $_POST['taxonomy_operator'] != '') {
				$param_array['taxonomy_operator']=$_POST['taxonomy_operator'];
			}
			if(isset($_POST['taxonomy_author']) && $_POST['taxonomy_author'] != '') {
				$param_array['taxonomy_author']=$_POST['taxonomy_author'];
			}
			if(isset($_POST['taxonomy_show']) && $_POST['taxonomy_show'] != '') {
				$param_array['taxonomy_show']=$_POST['taxonomy_show'];
			}		
		} elseif($type == '8' ) {
			if(isset($_POST['rssfeedid']) && $_POST['rssfeedid'] != '') {
				$param_array['feed_id']=$_POST['rssfeedid'];
			}
			if(isset($_POST['rssfeedurl']) && $_POST['rssfeedurl'] != '') {
				$param_array['feed_url']=$_POST['rssfeedurl'];
			}
			if(isset($_POST['rssfeedimg']) && $_POST['rssfeedimg'] != '') {
				$param_array['feed_img']=$_POST['rssfeedimg'];
			}	
			if(isset($_POST['feed']) && $_POST['feed'] != '') {
				$param_array['feed']=$_POST['feed'];
			}
			if(isset($_POST['rss-content']) && $_POST['rss-content'] != '') {
				$param_array['feed_content']=$_POST['rss-content'];
			}
			if(isset($_POST['rssfeed-src']) && $_POST['rssfeed-src'] != '') {
				$param_array['feed_src']=$_POST['rssfeed-src'];
			}	
			if(isset($_POST['rss-size']) && $_POST['rss-size'] != '') {
				$param_array['feed_size']=$_POST['rss-size'];
			}
			if(isset($_POST['rss-img-class']) && $_POST['rss-img-class'] != '') {
				$param_array['feed_imgclass']=$_POST['rss-img-class'];
			}	
		} elseif($type == '9' ) {
			if(isset($_POST['postattch-id']) && $_POST['postattch-id'] != '') {
				$param_array['postattch-id']=$_POST['postattch-id'];
			}
		} elseif($type == '10' ) {
			if(isset($_POST['nextgen-id']) && $_POST['nextgen-id'] != '') {
				$param_array['nextgen-id']=$_POST['nextgen-id'];
			}
			if(isset($_POST['nextgen-anchor']) && $_POST['nextgen-anchor'] != '') {
				$param_array['nextgen-anchor']=$_POST['nextgen-anchor'];
			}		
		} elseif($type == '11' ) {
			if(isset($_POST['yt-playlist-id']) && $_POST['yt-playlist-id'] != '') {
				$param_array['yt-playlist-id']=$_POST['yt-playlist-id'];
			}
		} elseif($type == '12' ) {
			if(isset($_POST['yt-search-term']) && $_POST['yt-search-term'] != '') {
				$param_array['yt-search-term']=$_POST['yt-search-term'];
			}
		} elseif($type == '13' ) {
			if(isset($_POST['vimeo-type']) && $_POST['vimeo-type'] != '') {
				$param_array['vimeo-type']=$_POST['vimeo-type'];
			}
			if(isset($_POST['vimeo-val']) && $_POST['vimeo-val'] != '') {
				$param_array['vimeo-val']=$_POST['vimeo-val'];
			}
		} elseif($type == '14' ) {
			if(isset($_POST['fb-pg-url']) && $_POST['fb-pg-url'] != '') {
				$param_array['fb-pg-url']=$_POST['fb-pg-url'];
			}
			if(isset($_POST['fb-album']) && $_POST['fb-album'] != '') {
				$param_array['fb-album']=$_POST['fb-album'];
			}
		} elseif($type == '15' ) {
			if(isset($_POST['user-name']) && $_POST['user-name'] != '') {
				$param_array['user-name']=$_POST['user-name'];
			}
		} elseif($type == '16' ) {
			if(isset($_POST['flickr-type']) && $_POST['flickr-type'] != '') {
				$param_array['flickr-type']=$_POST['flickr-type'];
			}
			if(isset($_POST['fl-id']) && $_POST['fl-id'] != '') {
				$param_array['fl-id']=$_POST['fl-id'];
			}
		} elseif($type == '18' ) {
			if(isset($_POST['feature']) && $_POST['feature'] != '') {
				$param_array['feature']=$_POST['feature'];
			}
			if(isset($_POST['pxuser']) && $_POST['pxuser'] != '') {
				$param_array['pxuser']=$_POST['pxuser'];
			}
		}
		$sparam = serialize($param_array);
		$param = $sparam;

		$sql = "INSERT INTO $slider_meta (slider_name,type,setid,param) VALUES('$slider_name',$type,$scounter,'$param');";
		$result = $wpdb->query($sql);
		$id = $wpdb->insert_id;
		$urlarg = array();
		if($type == '17') {
			$current_url = admin_url('admin.php?page=featured-slider-admin');
		}
		else {
			$current_url = admin_url('admin.php?page=featured-slider-easy-builder');
		}
		$urlarg['id'] = $id;
		$query_arg = add_query_arg( $urlarg ,$current_url);
		$current_url = $query_arg;
		wp_redirect( $current_url );
		exit;
		
	}
}
add_action('load-toplevel_page_featured-slider-admin','featured_process_create_new_requests');
function featured_slider_create_new_slider() {
	if (isset ($_POST['slider_type'])) {
		$slider_type = $_POST['slider_type'];
		?>
		<div class="featured-step2">
			<a href="<?php echo featured_sslider_admin_url( array( 'page' => 'featured-slider-admin' ) );?>" class="featured-show-all"> 
			<?php _e('Show All Types','featured-slider'); ?> </a>
			<input type="hidden" name="featured-loader" value="<?php echo admin_url('images/loading.gif');?>" />
			<form method="post" name="featured-create-new-step2" id="featured-create-new-step2" class="featured-step2-form featured-validate" >
			<?php
			if($slider_type == 2) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="2" checked ><i class="fa fa-align-justify"></i><span class="featured-icon-title"><?php _e('Recent Posts Slider','featured-slider'); ?></span>
				</div>
			<?php
			} elseif($slider_type == 1) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="1" checked ><i class="fa fa-align-justify"></i><span class="featured-icon-title"><?php _e('Category Slider','featured-slider'); ?></span>
				</div>
			<?php
			} elseif($slider_type == 0) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="0" checked ><i class="fa fa-align-justify"></i><span class="featured-icon-title"><?php _e('Custom Slider','featured-slider'); ?></span>
				</div>
			<?php
			} elseif($slider_type == 3) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="3" checked ><i class="fa fa-shopping-cart"></i></span><span class="featured-icon-title"><?php _e('WooCommerce Slider','featured-slider'); ?></span>
				</div>
			<?php
			} elseif($slider_type == 4) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="4" checked ><i class="fa fa-shopping-cart"></i></span><span class="featured-icon-title"><?php _e('ECommerce Slider','featured-slider'); ?></span>
				</div>
			<?php
			} elseif($slider_type == 5) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="5" checked ><i class="fa fa-align-justify"></i><span class="featured-icon-title"><?php _e('Event Manager Slider','featured-slider'); ?></span>
				</div>
			<?php
			} elseif($slider_type == 6) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="6" checked ><i class="fa fa-align-justify"></i><span class="featured-icon-title"><?php _e('Event Calender Slider','featured-slider'); ?></span>
				</div>
			<?php
			} elseif($slider_type == 7) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="7" checked ><i class="fa fa-align-justify"></i><span class="featured-icon-title"><?php _e('Taxonomy Slider','featured-slider'); ?></span>
				</div>
			<?php
			} elseif($slider_type == 17) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="17" checked ><i class="fa fa-align-justify"></i><span class="featured-icon-title"><?php _e('Image Slider','featured-slider'); ?></span>
				</div>
			<?php
			} elseif($slider_type == 8) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="8" checked ><i class="fa fa-rss-square"></i><span class="featured-icon-title"><?php _e('RSS Feed Slider','featured-slider'); ?></span>
				</div>
			<?php
			}  elseif($slider_type == 9) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="9" checked ><i class="fa fa-align-justify"></i><span class="featured-icon-title"><?php _e('Post Attachment Slider','featured-slider'); ?></span>
				</div>
			<?php
			} elseif($slider_type == 10) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="10" checked ><i class="fa fa-picture-o"></i><span class="featured-icon-title"><?php _e('NextGen Gallery Slider','featured-slider'); ?></span>
				</div>
			<?php
			} elseif($slider_type == 11) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="11" checked ><i class="fa fa-youtube"></i><span class="featured-icon-title"><?php _e('Youtube Playlist Slider','featured-slider'); ?></span>
				</div>
			<?php
			} elseif($slider_type == 12) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="12" checked ><i class="fa fa-youtube"></i><span class="featured-icon-title"><?php _e('YouTube Search Slider','featured-slider'); ?></span>
				</div>
			<?php
			} elseif($slider_type == 13) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="13" checked ><i class="fa fa-vimeo-square"></i><span class="featured-icon-title"><?php _e('Vimeo Slider','featured-slider'); ?></span>
				</div>
			<?php
			} elseif($slider_type == 14) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="14" checked ><i class="fa fa-facebook-square"></i><span class="featured-icon-title"><?php _e('Facebook Album Slider','featured-slider'); ?></span>
				</div>
			<?php
			} elseif($slider_type == 15) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="15" checked ><i class="fa fa-instagram"></i><span class="featured-icon-title"><?php _e('Instagram Slider','featured-slider'); ?></span>
				</div>
			<?php
			} elseif($slider_type == 16) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="16" checked ><i class="fa fa-flickr"></i><span class="featured-icon-title"><?php _e('Flickr Slider','featured-slider'); ?></span>
				</div>
			<?php
			} elseif($slider_type == 18) { ?>
				<div class="featured-col-row">
					<input type="radio" name="slider-type" value="18" checked ><img src="<?php echo featured_slider_plugin_url( 'images/500px.png' ); ?>" width="13" height="14" /><span class="featured-icon-title"><?php _e('500px Slider','featured-slider'); ?></span>
				</div>
			<?php
			}			
			?>
			<div class="featured-form-row"> 	
				<label><?php _e('Slider Name','featured-slider'); ?></label>			
				 <input type="text" name="new_slider_name" id="new_slider_name" class="featured-form-input" /> 
			</div>
			<div class="featured-form-row">
				<label><?php _e('Offset','featured-slider'); ?></label>
				<input type="number" name="offset" value="0" class="featured-form-input small" />
			</div>
			<?php if($slider_type == 1) { 
				//category Slider Param
				$categories = get_categories();
				$scat_html='<option value="" selected >Select the Category</option>';
				foreach ($categories as $category) { 
					 if( isset($param_array['catg_slug']) && $category->slug==$param_array['catg_slug']){$selected = 'selected';} else{$selected='';}
					 $scat_html =$scat_html.'<option value="'.$category->slug.'" '.$selected.'>'.$category->name.'</option>';
				}
			?>
				<div class="featured-form-row">
					<label><?php _e('Category','featured-slider'); ?></label>
					<select name="catg_slug" id="featured_slider_catslug" class="featured-form-input" ><?php echo $scat_html;?></select>
				</div>
			<?php } elseif($slider_type == 3 ) { 
				if( is_plugin_active('woocommerce/woocommerce.php') ) {
					$wooterms = get_terms('product_cat');
					$woocat_html='<option value="" selected >Select the Category</option>';
					foreach( $wooterms as $woocategory) {
						if( isset($param_array['woo-catg']) && $woocategory->slug==$param_array['woo-catg'] ){$selected = 'selected';} else{$selected='';}
						$woocat_html =$woocat_html.'<option value="'.$woocategory->slug.'" '.$selected.'>'. $woocategory->name .'</option>';
					}
				} 
			?>
			<div class="featured-form-row">
				<label><?php _e('Select Slider Type','featured-slider'); ?></label>
				<select name="woo_slider_type" id="woo-slider-type" class="featured-form-input" >
					<option value="recent" ><?php _e('Recent Product Slider','featured-slider'); ?></option>
					<option value="upsells" ><?php _e('Upsells Product Slider','featured-slider'); ?></option>
					<option value="crosssells" ><?php _e('Crosssells Product Slider','featured-slider'); ?></option>
					<option value="external" ><?php _e('External Product Slider','featured-slider'); ?></option>
					<option value="grouped" ><?php _e('Grouped Product Slider','featured-slider'); ?></option>
				</select>
			</div>
			<div class="featured-form-row woo-product" style="display:none;">
				<label><?php _e('Enter Product','featured-slider'); ?></label>
				<input id="products" class="featured-form-input" >
				<input id="product_id" name="product_id" value="" type="hidden" >
			</div>
			<div class="featured-form-row">
				<label><?php _e('Select Category','featured-slider'); ?></label>
				<select id="featured_slider_woo_catslug" name="woo-catg[]" multiple class="featured-form-input" ><?php echo $woocat_html;?></select>
			</div>
			<?php } elseif($slider_type == 4 ) { 
				if( is_plugin_active('wp-e-commerce/wp-shopping-cart.php') ) {
					$ecomterms = get_terms('wpsc_product_category');
					$ecomcat_html='<option value="" selected >Select the Category</option>';
					foreach( $ecomterms as $ecomcategory) {
						if( isset($param_array['ecom-catg']) && $ecomcategory->slug==$param_array['ecom-catg']){$selected = 'selected';} else{$selected='';}
						$ecomcat_html =$ecomcat_html.'<option value="'.$ecomcategory->slug.'" '.$selected.'>'.$ecomcategory->name.'</option>';
					}
				}
			?>
				<div class="featured-form-row">
					<label><?php _e('Select Slider Type','featured-slider'); ?></label>
					<select name="ecom_slider_type" id="ecom_slider_preview" onchange="catgtype(this.value);"  class="featured-form-input" >
						<option value="0" ><?php _e('eCom Recent Product Slider','featured-slider'); ?></option>
						<option value="1" ><?php _e('eCom Product Category Slider','featured-slider'); ?></option>
					</select>
				</div>
				<div class="featured-form-row featured_catg" style="display:none;">
					<label><?php _e('Select Category','featured-slider'); ?></label>
					<select id="featured_slider_ecom_catslug" name="ecom-catg" class="featured-form-input" ><?php echo $ecomcat_html;?></select>
				</div>
				
			<?php } elseif($slider_type == 5 ) { 
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
			?>
				<div class="featured-form-row">
					<label><?php _e('Select Slider Scope','featured-slider'); ?></label>
					<select name="eventm_slider_scope" id="eventm_slider_preview" class="featured-form-input" >
						<option value="future" ><?php _e('Future Events','featured-slider'); ?></option>
						<option value="past" ><?php _e('Past Events','featured-slider'); ?></option>
						<option value="all" ><?php _e('Recent Events','featured-slider'); ?></option>
					</select>
				</div>
				<div class="featured-form-row">
					<label><?php _e('Select Category','featured-slider'); ?></label>
					<select id="featured_slider_event_catslug" name="eman-catg[]" multiple class="featured-form-input" ><?php echo $eventcat_html;?></select>
				</div>
				<div class="featured-form-row">
					<label><?php _e('Select Tags','featured-slider'); ?></label>
					<select id="featured_slider_event_tags" name="eman-tags[]" multiple class="featured-form-input" ><?php echo $evtag_html;?></select>
				</div>
				
			<?php }  elseif($slider_type == 6 ) { 
				if( is_plugin_active('the-events-calendar/the-events-calendar.php') ) { 
					$eventcalterms = get_terms('tribe_events_cat');
					$eventcal_html='<option value="" selected >All Category</option>';
					foreach( $eventcalterms as $eventcalcat) {
						if( isset($param_array['ecal-catg']) && $eventcalcat->slug==$param_array['ecal-catg']){$selected = 'selected';} else{$selected='';}
						$eventcal_html =$eventcal_html.'<option value="'.$eventcalcat->slug.'" '.$selected.'>'.$eventcalcat->name.'</option>';
					}
					$evcaltags = get_terms("post_tag");
					$evcaltag_html='<option value="" selected >All Tags</option>';
					foreach( $evcaltags as $tags) {
						$evcaltag_html = $evcaltag_html.'<option value="'.$tags->slug.'">'.$tags->name.'</option>';
					} 
				}
			?>
				<div class="featured-form-row">
					<label><?php _e('Select Slider Type','featured-slider'); ?></label>
					<select name="eventcal_slider_type" id="eventcal_slider_preview" class="featured-form-input" >
						<option value="upcoming" ><?php _e('Future Events','featured-slider'); ?></option>
						<option value="past" ><?php _e('Past Events','featured-slider'); ?></option>
						<option value="all" ><?php _e('Recent Events','featured-slider'); ?></option>
					</select>
				</div>
				<div class="featured-form-row">
					<label><?php _e('Select Category','featured-slider'); ?></label>
					<select id="featured_slider_eventcal_catslug" name="ecal-catg[]" multiple class="featured-form-input" ><?php echo $eventcal_html;?></select>
				</div>
				<div class="featured-form-row">
					<label><?php _e('Select Tags','featured-slider'); ?></label>
					<select id="featured_slider_eventcal_tags" name="ecal-tags[]" multiple class="featured-form-input" ><?php echo $evcaltag_html;?></select>
				</div>
				
			<?php } elseif($slider_type == 7) { 
				$post_types = get_post_types(); 
				$taxonomy_names = get_object_taxonomies( 'post' );
				// Taxonomy Slider Params  
			?>
				<div class="featured-form-row">
					<label><?php _e('Post Type','featured-slider'); ?></label>
					<select name="taxo_posttype" id="featured_taxonomy_posttype" class="featured-form-input" >
					<?php foreach ( $post_types as $cpost_type ) { 
						echo '<option value="'.$cpost_type.'" >' . $cpost_type . '</option>';
					} ?>
					</select>
				</div>
				<div class="featured-form-row  sh-taxo">
					<label><?php _e('Taxonomy','featured-slider'); ?></label>
					<select name="taxonomy_name" id="featured_taxonomy" class="featured-form-input" >
						<option value="" >Select Taxonomy </option>
					<?php foreach ( $taxonomy_names as $taxonomy_name ) { 
						echo '<option value="'.$taxonomy_name.'" >' . $taxonomy_name . '</option>';
					} ?>
					</select>
				</div>
				<div class="featured-form-row sh-term" style="display:none;"></div>
				<div class="featured-form-row">
					<label><?php _e('Show','featured-slider'); ?></label>
					<select name="taxonomy_show" id="featured_taxonomy_show" class="featured-form-input" >
						<option value="" ><?php _e('Default','featured-slider'); ?></option>
						<option value="per_tax" ><?php _e('One Per Tax','featured-slider'); ?></option>
					</select>
				</div>
				<div class="featured-form-row">
					<label><?php _e('Operator','featured-slider'); ?></label>
					<select name="taxonomy_operator" id="featured_taxonomy_operator" class="featured-form-input" >
						<option value="IN" ><?php _e('IN','featured-slider'); ?></option>
						<option value="NOT IN" ><?php _e('NOT IN','featured-slider'); ?></option>
						<option value="AND" ><?php _e('AND','featured-slider'); ?></option>
					</select>
				</div>
				<div class="featured-form-row">
					<label><?php _e('Author','featured-slider'); ?></label>
					<select name="taxonomy_author[]" id="featured_taxonomy_author" class="featured-form-input" multiple >
					<?php 
						$blogusers = get_users();						
						// Array of WP_User objects.
						foreach ( $blogusers as $user ) {
							echo '<option value="'.$user->ID.'" >' . $user->user_nicename . '</option>';
						}
					?>
					</select>
				</div>
			<?php } elseif($slider_type == 8) { ?>
				<div class="featured-form-row">
					<label><?php _e('Feedurl','featured-slider'); ?></label>
					<input type="text" name="rssfeedurl" id="featured_rssfeed_feedurl" placeholder="http://mashable.com/feed/" class="featured-form-input large" /> 
				</div>
	
				<div class="featured-form-row">
					<label><?php _e('RSS Slider Id','featured-slider'); ?></label>
					<input type="number" name="rssfeedid" id="featured_rssfeed_id" class="featured-form-input small" /> 
				</div>

				<div class="featured-form-row">
					<label><?php _e('Default image','featured-slider'); ?></label>
					<input type="text" name="rssfeedimg" id="featured_rssfeed_defimage" placeholder="<?php echo featured_slider_plugin_url('/images/default_image.png');?>" class="featured-form-input large" /> 
				</div>

				<div class="featured-form-row">
					<label><?php _e('Image Class','featured-slider'); ?></label>
					<input type="text" name="rss-image-class" id="featured_rssfeed_image_class" class="featured-form-input" /> 
				</div>

				<div class="featured-form-row">
					<label><?php _e('Source','featured-slider'); ?></label>
					<select name="rssfeed-src" id="featured_rssfeed_src" class="featured-form-input rss-source">
						<option value=""><?php _e('Other','featured-slider');?></option>
						<option value="smugmug"><?php _e('Smugmug','featured-slider');?></option>
					</select>
				</div>

				<div class="featured-form-row rss-feed">
					<label><?php _e('Feed','featured-slider'); ?></label>
					<select name="feed" id="featured_rssfeed_feed" class="featured-form-input">
						<option value=""><?php _e('Other','featured-slider');?></option>
						<option value="atom"><?php _e('Atom','featured-slider');?></option>
					</select>
				</div>

				<div class="featured-form-row rss-size" style="display:none;">
					<label><?php _e('Size','featured-slider'); ?></label>
					<select name="rss-size" id="featured_rssfeed_size" class="featured-form-input">
						<option value="Ti"><?php _e('Tiny thumbnails','featured-slider');?></option>
						<option value="Th"><?php _e('Large thumbnails','featured-slider');?></option>
						<option value="S"><?php _e('Small','featured-slider');?></option>
						<option value="M"><?php _e('Medium','featured-slider');?></option>
						<option value="L"><?php _e('Other','featured-slider');?></option>
						<option value="XL"><?php _e('Large','featured-slider');?></option>
						<option value="X2"><?php _e('X2Large','featured-slider');?></option>
						<option value="X3"><?php _e('X3Large','featured-slider');?></option>
						<option value="O"><?php _e('Original','featured-slider');?></option>
					</select>
				</div>
				
				<div class="featured-form-row">
					<label><?php _e('Scan child node content for images','featured-slider'); ?></label>
					<input type="checkbox" name="rss-content" id="featured_rssfeed_content" class="featured-form-input" />
				</div>
			<?php } elseif($slider_type == 9) { ?>
				<div class="featured-form-row">
					<label><?php _e('Post Id','featured-slider'); ?></label>
					<input type="text" name="postattch-id" id="featured_postattch_id" class="featured-form-input" /> 
				</div>
			<?php } elseif($slider_type == 10) { 
				$galleriesOptions = get_featured_nextgen_galleries(); 
			?>
				<div class="featured-form-row">
					<label><?php _e('Select Gallery','featured-slider'); ?></label>
					<select name="nextgen-id" id="featured_nextgen_galleryid" class="featured-form-input">
						<?php echo $galleriesOptions; ?>
					</select>
				</div>
				<div class="featured-form-row">
					<label><?php _e('Link','featured-slider'); ?></label>
					<input type="checkbox" name="nextgen-anchor" id="featured_nextgen_anchor" value="1" class="featured-form-input" />
				</div>
			<?php } elseif($slider_type == 11) { ?>
				<div class="featured-form-row">
					<label><?php _e('Playlist id','featured-slider'); ?></label>
					<input type="text" name="yt-playlist-id" id="yt-playlist-id" class="featured-form-input" />
				</div>
			<?php
			} elseif($slider_type == 12) { ?>
				<div class="featured-form-row">
					<label><?php _e('Term','featured-slider'); ?></label>
					<input type="text" name="yt-search-term" id="yt-search-term" class="featured-form-input" />
				</div>
			<?php
			} elseif($slider_type == 13) { ?>
				<div class="featured-form-row">
					<label><?php _e('Select type','featured-slider'); ?></label>
					<select name="vimeo-type" class="vimeo-type featured-form-input" >
						<option value="channel"><?php _e('Channel','featured-slider'); ?></option>
						<option value="album"><?php _e('Album','featured-slider'); ?></option>
					</select>
				</div>
				<div class="featured-form-row">
					<label id="vimeo-lb"><?php _e('Channel Name','featured-slider'); ?></label>
					<input type="text" name="vimeo-val" id="vimeo-val" class="featured-form-input" />
				</div>
			<?php
			} elseif($slider_type == 14) { ?>
				<div class="featured-form-row">
					<label><?php _e('Page Url','featured-slider'); ?></label>
					<input type="text" name="fb-pg-url" id="fb-pg-url" class="featured-form-input" />
					<input type='submit' name='cfb_connect' value="<?php _e('Connect','featured-slider'); ?>" class="btn_save cfb_connect" />
				</div>
				<div class="featured-form-row fb-albums">
			
				</div>
			<?php
			} elseif($slider_type == 15) { ?>
				<div class="featured-form-row">
					<label><?php _e('User Name','featured-slider'); ?></label>
					<input type="text" name="user-name" id="user-name" class="featured-form-input" />
				</div>
			<?php
			} elseif($slider_type == 16) { ?>
				<div class="featured-form-row">
					<label><?php _e('Select type','featured-slider'); ?></label>
					<select name="flickr-type" class="flickr-type featured-form-input" >
						<option value="user"><?php _e('User','featured-slider'); ?></option>
						<option value="album"><?php _e('Album','featured-slider'); ?></option>
					</select>
				</div>
				<div class="featured-form-row">
					<label id="flickr-lb"><?php _e('User ID','featured-slider'); ?></label>
					<input type="text" name="fl-id" id="fl-user-id" class="featured-form-input" />
				</div>
			<?php
			} elseif($slider_type == 18) { ?>
				<div class="featured-form-row">
					<label><?php _e('Select type','featured-slider'); ?></label>
					<select name="feature" class="feature featured-form-input" >
						<option value="popular"><?php _e('Popular','featured-slider'); ?></option>
						<option value="highest_rated" ><?php _e('Highest Rated','featured-slider'); ?></option>
						<option value="upcoming" ><?php _e('Upcoming','featured-slider'); ?></option>
						<option value="editors" ><?php _e('Editors','featured-slider'); ?></option>';
						<option value="fresh_today" ><?php _e('Fresh Today','featured-slider'); ?></option>
						<option value="fresh_yesterday" ><?php _e('Fresh Yesterday','featured-slider'); ?></option>
						<option value="fresh_week" ><?php _e('Fresh Week','featured-slider'); ?></option>
						<option value="user" ><?php _e('User','featured-slider'); ?></option>
						<option value="user_favorites"><?php _e('User favorites','featured-slider'); ?></option>
					</select>
				</div>
				<div class="featured-form-row pxuser" style="display:none;">
					<label class="featured-form-label"><?php _e('User Name','featured-slider'); ?></label>
					<input type='text' name='pxuser' value="" class="featured-form-input" />
				</div>
			<?php
			} ?>
			<input type="hidden" name="featured-slider-nonce" id="featured-slider-nonce" value="<?php echo wp_create_nonce( 'featured-slider-nonce' ); ?>" />
			<input type="button" name="step2-prev" value="<?php _e('Back','featured-slider'); ?>" class="featured-btn-back" >
			<input type="submit" name="step2-next" value="<?php _e('Next','featured-slider'); ?>" class="featured-btn-next" >
			<div style="clear:left;"></div>
			</form>
		</div>
	<?php  
	} elseif(isset ($_POST['step2-next'])) {
		$slider_type = $_POST["slider-type"];
		$slider_name = $_POST["new_slider_name"];
		$offset = $_POST["offset"];
	?>
	<div class="featured-steps">
		<div class="featured-head">
			<span class="featured-step" ><?php _e('Step 3','featured-slider'); ?></span> <i class="fa fa-long-arrow-right"></i> <span class="featured-step-title"><?php _e('Select Skin','featured-slider'); ?></span>
		</div>
		<form method="post" name="featured-create-new-step3" id="featured-create-new-step3" class="featured-step3-form" >
			<div class="featured-col-row">
				<!-- <input type="radio" name="setting-type" value="old-set" > -->
				<a class="featured-old-set"><?php _e('Choose from already created setting sets','featured-slider'); ?></a>
				<div class="featured-old-settings" style="display:none;">
				<?php
				$scounter=get_option('featured_slider_scounter');
				for($i=1;$i<=$scounter;$i++){
					if ($i==1){ ?>
						<div class="featured-col-row" style="z-index:99;">
							<input type="radio" name="set" value="<?php echo $i; ?>" checked ><?php _e('Default Settings Set','featured-slider'); ?>
						</div>
				<?php }	else {
					  if($settings_set=get_option('featured_slider_options'.$i)){ ?>
						<div class="featured-col-row"  style="z-index:99;">
							<input type="radio" name="set" value="<?php echo $i; ?>" ><?php _e($settings_set['setname'].' (ID '.$i.')','featured-slider'); ?>
						</div>
						<?php
						}
					}
				}
				?>
				</div>
			</div>
			<div class="featured-col-row">
				<input type="radio" name="setting-type" value="new-set" checked ><?php _e('Create new setting set','featured-slider'); ?>
			</div>
			<div class="featured-select-skin">
				<?php
				$i = 0;
				$directory = FEATURED_SLIDER_CSS_DIR;
				if ($handle = opendir($directory)) {
				    	while (false !== ($file = readdir($handle))) { 
				    		$sel = "";
						if($file != '.' and $file != '..') { 
						     	$i++;
							if($i%2 == 0) $class=" margin"; else $class="";
							if ('default' == $file) $sel = "checked";
							?>	
							<div class="featured-skin-box<?php echo $class;?>">
								<img src="<?php echo featured_slider_plugin_url( 'images/'.$file.'.png' ); ?>" width="300" height="220" />
								<div class="featured-skin-content">
									<div class="featured-skin-title"><?php _e($file,'featured-slider'); ?></div>
									<div class="switch">
									    <input id="<?php echo $file;?>" class="cmn-toggle cmn-toggle-round" type="checkbox" value="<?php echo $file;?>" <?php echo $sel; ?> >
									    <label for="<?php echo $file;?>"></label>
									</div>
								</div>
							</div>
				<?php
						} 
					}
				    	closedir($handle);
				} ?>
				<input type="hidden" name="skin" value="default" class="skin" />
			</div>
			<input type="hidden" name="slider-type" value="<?php echo $slider_type;?>" /> 
			<input type="hidden" name="new_slider_name" value="<?php echo $slider_name;?>" /> 
			<input type="hidden" name="offset" value="<?php echo $offset;?>" />
			<?php if($slider_type == 1) { 
				$catg_slug = $_POST['catg_slug'];
			?>
				<input type="hidden" name="catg_slug" value="<?php echo $catg_slug;?>" /> 
			<?php } elseif($slider_type == 3) { 
				$woo_slider_type = $_POST['woo_slider_type'];
				$woocatgslug = isset($_POST['woo-catg'])?implode(",",$_POST['woo-catg']):'';
				if($woo_slider_type != 'recent' && $woo_slider_type != 'external') {
					$pid = isset($_POST['product_id'])?$_POST['product_id']:'';
			?>
					<input type="hidden" name="product_id" value="<?php echo $pid;?>" /> 
				<?php } ?>
				<input type="hidden" name="woo-catg" value="<?php echo $woocatgslug;?>" /> 
				<input type="hidden" name="woo_slider_type" value="<?php echo $woo_slider_type;?>" /> 
			<?php } elseif($slider_type == 4 ) { 
				$ecom_slider_type = $_POST['ecom_slider_type'];
				if($ecom_slider_type == 1) { $ecomcatg = $_POST['ecom-catg'];
			?>
				<input type="hidden" name="ecom-catg" value="<?php echo $ecomcatg;?>" /> 
			<?php 	} ?>
				<input type="hidden" name="ecom_slider_type" value="<?php echo $ecom_slider_type;?>" /> 
			<?php } elseif($slider_type == 5 ) { 
				$eventm_slider_scope = $_POST['eventm_slider_scope'];
				$eventmancatg = isset($_POST['eman-catg'])?implode(",",$_POST['eman-catg']):'';
				$eventmantag = isset($_POST['eman-tags'])?implode(",",$_POST['eman-tags']):'';
			?>
				<input type="hidden" name="eman-catg" value="<?php echo $eventmancatg;?>" /> 
				<input type="hidden" name="eman-tags" value="<?php echo $eventmantag;?>" /> 
				<input type="hidden" name="eventm_slider_scope" value="<?php echo $eventm_slider_scope;?>" /> 
			<?php }  elseif($slider_type == 6 ) { 
				$eventcal_slider_type = $_POST['eventcal_slider_type'];
				$calcatgslug =  isset($_POST['ecal-catg'])?implode(",",$_POST['ecal-catg']):'';
				$ecaltag = isset($_POST['ecal-tags'])?implode(",",$_POST['ecal-tags']):'';
			?>
				<input type="hidden" name="ecal-catg" value="<?php echo $calcatgslug;?>" /> 
				<input type="hidden" name="ecal-tags" value="<?php echo $ecaltag;?>" /> 
				<input type="hidden" name="eventcal_slider_type" value="<?php echo $eventcal_slider_type;?>" /> 
			<?php } elseif($slider_type == 7) { 
				$taxo_posttype = $_POST['taxo_posttype'];
				$taxonomy_name = $_POST['taxonomy_name'];
				$taxonomy_term = isset($_POST['taxonomy_term'])?implode(",",$_POST['taxonomy_term']):'';
				$taxonomy_operator = $_POST['taxonomy_operator'];
				$taxonomy_author = isset($_POST['taxonomy_author'])?implode(",",$_POST['taxonomy_author']):'';
				$taxonomy_show = $_POST['taxonomy_show'];
			?>
				<input type="hidden" name="taxo_posttype" value="<?php echo $taxo_posttype;?>" /> 
				<input type="hidden" name="taxonomy_name" value="<?php echo $taxonomy_name;?>" /> 
				<input type="hidden" name="taxonomy_term" value="<?php echo $taxonomy_term;?>" /> 
				<input type="hidden" name="taxonomy_operator" value="<?php echo $taxonomy_operator;?>" /> 
				<input type="hidden" name="taxonomy_author" value="<?php echo $taxonomy_author;?>" /> 
				<input type="hidden" name="taxonomy_show" value="<?php echo $taxonomy_show;?>" /> 
			<?php } elseif($slider_type == 8) { 
				$rssfeedid = isset($_POST['rssfeedid'])?$_POST['rssfeedid']:'';
				$rssfeedurl = isset($_POST['rssfeedurl'])?$_POST['rssfeedurl']:'';
				$rssfeedimg = isset($_POST['rssfeedimg'])?$_POST['rssfeedimg']:'';
				$feed = isset($_POST['feed'])?$_POST['feed']:'';
				$rsscontent = isset($_POST['rss-content'])?$_POST['rss-content']:'';
				$rssfeedsrc = isset($_POST['rssfeed-src'])?$_POST['rssfeed-src']:'';
				$rsssize = isset($_POST['rss-size'])?$_POST['rss-size']:'';
				$rssimageclass = isset($_POST['rss-image-class'])?$_POST['rss-image-class']:'';
			?>
				<input type="hidden" name="rssfeedid" value="<?php echo $rssfeedid;?>" /> 
				<input type="hidden" name="rssfeedurl" value="<?php echo $rssfeedurl;?>" /> 
				<input type="hidden" name="rssfeedimg" value="<?php echo $rssfeedimg;?>" /> 
				<input type="hidden" name="feed" value="<?php echo $feed;?>" /> 
				<input type="hidden" name="rss-content" value="<?php echo $rsscontent;?>" /> 
				<input type="hidden" name="rssfeed-src" value="<?php echo $rssfeedsrc;?>" /> 
				<input type="hidden" name="rss-size" value="<?php echo $rsssize;?>" /> 
				<input type="hidden" name="rss-image-class" value="<?php echo $rssimageclass;?>" /> 
			<?php } elseif($slider_type == 9) { 
				$postattchid = $_POST['postattch-id'];
			?>
				<input type="hidden" name="postattch-id" value="<?php echo $postattchid;?>" /> 
			<?php } elseif($slider_type == 10) { 
				$nextgenid = $_POST['nextgen-id'];
				$nextgenanchor = isset($_POST['nextgen-anchor'])?$_POST['nextgen-anchor']:'';
			?>
				<input type="hidden" name="nextgen-id" value="<?php echo $nextgenid;?>" /> 
				<input type="hidden" name="nextgen-anchor" value="<?php echo $nextgenanchor;?>" /> 
			<?php } elseif($slider_type == 11) { 
				$playlistid = $_POST['yt-playlist-id'];
			?>
				<input type="hidden" name="yt-playlist-id" value="<?php echo $playlistid;?>" /> 
			<?php } elseif($slider_type == 12) { 
				$searchterm = $_POST['yt-search-term'];
			?>
				<input type="hidden" name="yt-search-term" value="<?php echo $searchterm;?>" /> 
			<?php } elseif($slider_type == 13) { 
				$vimeotype = $_POST['vimeo-type'];
				$vimeoval = $_POST['vimeo-val'];
			?>
				<input type="hidden" name="vimeo-type" value="<?php echo $vimeotype;?>" /> 
				<input type="hidden" name="vimeo-val" value="<?php echo $vimeoval;?>" /> 
			<?php } elseif($slider_type == 14) { 
				$fbpage = $_POST['fb-pg-url'];
				$fbalbum = $_POST['fb-album'];
			?>
				<input type="hidden" name="fb-pg-url" value="<?php echo $fbpage;?>" /> 
				<input type="hidden" name="fb-album" value="<?php echo $fbalbum;?>" /> 
			<?php } elseif($slider_type == 15) { 
				$username = $_POST['user-name'];
			?>
				<input type="hidden" name="user-name" value="<?php echo $username;?>" /> 
			<?php } elseif($slider_type == 16) { 
				$flickrtype = $_POST['flickr-type'];
				$flickrid = $_POST['fl-id'];
			?>
				<input type="hidden" name="flickr-type" value="<?php echo $flickrtype;?>" /> 
				<input type="hidden" name="fl-id" value="<?php echo $flickrid;?>" /> 
			<?php } elseif($slider_type == 18) { 
				$feature = $_POST['feature'];
				if( $feature == "user" ||  $feature == "user_favorites" ) {
					$pxuser = $_POST['pxuser'];
			?>
					<input type="hidden" name="pxuser" value="<?php echo $pxuser;?>" /> 		
				<?php } ?>
				<input type="hidden" name="feature" value="<?php echo $feature;?>" /> 
			<?php } ?>
			<input type="button" name="step2-prev" value="<?php _e('Back','featured-slider'); ?>" class="featured-btn-back no-margin" >
			<input type="submit" name="step3-create" value="<?php _e('Next','featured-slider'); ?>" class="featured-btn-next" style="display: none;">
			<input type="submit" name="step3-next" value="<?php _e('Next','featured-slider'); ?>" class="featured-btn-next">
			<div style="clear:left;"></div>
		</form>	
	</div>
	<?php 
	} elseif(isset ($_POST['step3-next'])) { 
		$skin = $_POST['skin'];
		$slider_type = $_POST["slider-type"];
		$slider_name = $_POST["new_slider_name"];
		$offset = $_POST["offset"];
		$settype = $_POST['setting-type'];
		?>
	<div class="featured-steps">
		<div class="featured-head">
			<span class="featured-step" ><?php _e('Step 4','featured-slider'); ?></span> <i class="fa fa-long-arrow-right"></i> <span class="featured-step-title"><?php _e('Choose Layout','featured-slider'); ?></span>
		</div>
		<form method="post" name="featured-create-new-step4" id="featured-create-new-step4" class="featured-step4-form" >
			<div class="featured-col-selected">
				<input type="radio" name="skin" value="<?php echo $skin;?>" checked ><i class="fa fa-align-justify"></i><span class="featured-icon-title"><?php _e($skin,'featured-slider'); ?></span>
			</div>
				<div class="featured-skin-box">
					<div class="featured-skin-title"><?php _e('Image only','featured-slider'); ?></div>
					<div class="eb-switch havemoreinfo">
						<input id="featured_imgonly" name="featured-check-pureimg" class="cmn-toggle eb-toggle-round" type="checkbox" value="1" >
						<label for="featured_imgonly"></label>
					</div>
				</div>
				<div class="featured-skin-box margin">
					<div class="featured-skin-title"><?php _e('Blocks','featured-slider'); ?></div>
					<div class="eb-switch havemoreinfo">
						<input id="featured_blocks" name="featured-check-blocks" class="cmn-toggle eb-toggle-round" type="checkbox" value="1">
						<label for="featured_blocks"></label>
					</div>
				</div>
			<div class="featured-select-layout">
			<?php	if($skin!='trio') { 
					if ($skin=='default') {
						$rightb = 'default';
						$leftb = 'default_left';
					} else {
						$leftb = 'duo_left';
						$rightb =  'duo';
					} ?>
			
				<div class="featured-layout-box">
					<input type="radio" name="featured-layout" value="right_sub" class="featured-layout-radio" checked ><img src="<?php echo featured_slider_plugin_url( 'images/'.$rightb.'.png' ); ?>" width="300" class="featured-layout-img" />
					<div class="featured-layout-content">
						<div class="featured-layout-title"><?php _e('Right Sub-Slides','featured-slider'); ?></div>
					</div>
				</div>
				<div class="featured-layout-box margin">
					<input type="radio" name="featured-layout" class="featured-layout-radio" value="left_sub" ><img src="<?php echo featured_slider_plugin_url( 'images/'.$leftb.'.png' ); ?>" width="300" class="featured-layout-img" />
					<div class="featured-layout-content">
						<div class="featured-layout-title"><?php _e('Left Sub-Slides','featured-slider'); ?></div>
					</div>
				</div>
			<?php } else { ?>
				<div class="featured-layout-box">
					<input type="radio" name="featured-layout" value="right_list" class="featured-layout-radio" ><img src="<?php echo featured_slider_plugin_url( 'images/trio_list.png' ); ?>" width="300" class="featured-layout-img" />
					<div class="featured-layout-content">
						<div class="featured-layout-title"><?php _e('Right List','featured-slider'); ?></div>
					</div>
				</div>
				<div class="featured-layout-box margin">
					<input type="radio" name="featured-layout" class="featured-layout-radio" value="left_list" ><img src="<?php echo featured_slider_plugin_url( 'images/trio_list_left.png' ); ?>" width="300" class="featured-layout-img" />
					<div class="featured-layout-content">
						<div class="featured-layout-title"><?php _e('Left List','featured-slider'); ?></div>
					</div>
				</div>
				<div class="featured-layout-box">
					<input type="radio" name="featured-layout" value="right_utwo" class="featured-layout-radio" ><img src="<?php echo featured_slider_plugin_url( 'images/trio_right_bottom.png' ); ?>" width="300" class="featured-layout-img" />
					<div class="featured-layout-content">
						<div class="featured-layout-title"><?php _e('Right upper Two','featured-slider'); ?></div>
					</div>
				</div>
				<div class="featured-layout-box margin">
					<input type="radio" name="featured-layout" class="featured-layout-radio" value="left_ltwo" checked ><img src="<?php echo featured_slider_plugin_url( 'images/trio.png' ); ?>" width="300" class="featured-layout-img" />
					<div class="featured-layout-content">
						<div class="featured-layout-title"><?php _e('Right lower Two','featured-slider'); ?></div>
					</div>
				</div>
				<div class="featured-layout-box">
					<input type="radio" name="featured-layout" value="left_utwo" class="featured-layout-radio" ><img src="<?php echo featured_slider_plugin_url( 'images/trio_left_bottom.png' ); ?>" width="300" class="featured-layout-img" />
					<div class="featured-layout-content">
						<div class="featured-layout-title"><?php _e('Left upper Two','featured-slider'); ?></div>
					</div>
				</div>
				<div class="featured-layout-box margin">
					<input type="radio" name="featured-layout" class="featured-layout-radio" value="left_ltwo" ><img src="<?php echo featured_slider_plugin_url( 'images/trio_left.png' ); ?>" width="300" class="featured-layout-img" />
					<div class="featured-layout-content">
						<div class="featured-layout-title"><?php _e('Left lower Two','featured-slider'); ?></div>
					</div>
				</div>
			<?php } ?>
				
				<!--div class="featured-layout-box">
					<input type="radio" name="featured-layout" class="featured-layout-radio" value="pure_image" ><img src="<?php echo featured_slider_plugin_url( 'images/k2_default_pureimage.png' ); ?>" width="300" class="featured-layout-img" />
					<div class="featured-layout-content">
						<div class="featured-layout-title"><?php _e('Pure Image Slider','featured-slider'); ?></div>
					</div>
				</div-->
			</div>
			<input type="hidden" name="slider-type" value="<?php echo $slider_type;?>" /> 
			<input type="hidden" name="new_slider_name" id="new_slider_name" value="<?php echo $slider_name;?>" /> 
			<input type="hidden" name="offset" value="<?php echo $offset;?>" />
			<?php if($slider_type == 1) { 
				$catg_slug = $_POST['catg_slug'];
			?>
				<input type="hidden" name="catg_slug" value="<?php echo $catg_slug;?>" /> 
			<?php } elseif($slider_type == 3) { 
				$woo_slider_type = $_POST['woo_slider_type'];
				$woocatgslug = $_POST['woo-catg'];
				if($woo_slider_type != 'recent' && $woo_slider_type != 'external') {
					$pid = isset($_POST['product_id'])?$_POST['product_id']:'';
				?>
					<input type="hidden" name="product_id" value="<?php echo $pid;?>" /> 
				<?php } ?>
				<input type="hidden" name="woo-catg" value="<?php echo $woocatgslug;?>" /> 
				<input type="hidden" name="woo_slider_type" value="<?php echo $woo_slider_type;?>" />
			<?php } elseif($slider_type == 4 ) { 
				$ecom_slider_type = $_POST['ecom_slider_type'];
				if($ecom_slider_type == 1) { $ecomcatg = $_POST['ecom-catg'];
			?>
				<input type="hidden" name="ecom-catg" value="<?php echo $ecomcatg;?>" /> 
			<?php 	} ?>
				<input type="hidden" name="ecom_slider_type" value="<?php echo $ecom_slider_type;?>" /> 
			<?php } elseif($slider_type == 5 ) { 
				$eventm_slider_scope = $_POST['eventm_slider_scope'];
				$eventmancatg = isset($_POST['eman-catg'])?$_POST['eman-catg']:'';
				$eventmantag = isset($_POST['eman-tags'])?$_POST['eman-tags']:'';
			?>
				<input type="hidden" name="eman-catg" value="<?php echo $eventmancatg;?>" /> 
				<input type="hidden" name="eman-tags" value="<?php echo $eventmantag;?>" /> 
				<input type="hidden" name="eventm_slider_scope" value="<?php echo $eventm_slider_scope;?>" /> 
			<?php } elseif($slider_type == 6 ) { 
				$eventcal_slider_type = $_POST['eventcal_slider_type'];
				$calcatgslug =  isset($_POST['ecal-catg'])?$_POST['ecal-catg']:'';
				$ecaltag = isset($_POST['ecal-tags'])?$_POST['ecal-tags']:'';
			?>
				<input type="hidden" name="ecal-catg" value="<?php echo $calcatgslug;?>" /> 
				<input type="hidden" name="ecal-tags" value="<?php echo $ecaltag;?>" /> 
				<input type="hidden" name="eventcal_slider_type" value="<?php echo $eventcal_slider_type;?>" /> 
			<?php } elseif($slider_type == 7) { 
				$taxo_posttype = $_POST['taxo_posttype'];
				$taxonomy_name = $_POST['taxonomy_name'];
				$taxonomy_term = $_POST['taxonomy_term'];
				$taxonomy_operator = $_POST['taxonomy_operator'];
				$taxonomy_author = $_POST['taxonomy_author'];
				$taxonomy_show = $_POST['taxonomy_show'];
			?>
				<input type="hidden" name="taxo_posttype" value="<?php echo $taxo_posttype;?>" /> 
				<input type="hidden" name="taxonomy_name" value="<?php echo $taxonomy_name;?>" /> 
				<input type="hidden" name="taxonomy_term" value="<?php echo $taxonomy_term;?>" /> 
				<input type="hidden" name="taxonomy_operator" value="<?php echo $taxonomy_operator;?>" /> 
				<input type="hidden" name="taxonomy_author" value="<?php echo $taxonomy_author;?>" /> 
				<input type="hidden" name="taxonomy_show" value="<?php echo $taxonomy_show;?>" /> 
			<?php }  elseif($slider_type == 8) { 
				$rssfeedid = isset($_POST['rssfeedid'])?$_POST['rssfeedid']:'';
				$rssfeedurl = isset($_POST['rssfeedurl'])?$_POST['rssfeedurl']:'';
				$rssfeedimg = isset($_POST['rssfeedimg'])?$_POST['rssfeedimg']:'';
				$feed = isset($_POST['feed'])?$_POST['feed']:'';
				$rsscontent = isset($_POST['rss-content'])?$_POST['rss-content']:'';
				$rssfeedsrc = isset($_POST['rssfeed-src'])?$_POST['rssfeed-src']:'';
				$rsssize = isset($_POST['rss-size'])?$_POST['rss-size']:'';
				$rssimageclass = isset($_POST['rss-image-class'])?$_POST['rss-image-class']:'';
			?>
				<input type="hidden" name="rssfeedid" value="<?php echo $rssfeedid;?>" /> 
				<input type="hidden" name="rssfeedurl" value="<?php echo $rssfeedurl;?>" /> 
				<input type="hidden" name="rssfeedimg" value="<?php echo $rssfeedimg;?>" /> 
				<input type="hidden" name="feed" value="<?php echo $feed;?>" /> 
				<input type="hidden" name="rss-content" value="<?php echo $rsscontent;?>" /> 
				<input type="hidden" name="rssfeed-src" value="<?php echo $rssfeedsrc;?>" /> 
				<input type="hidden" name="rss-size" value="<?php echo $rsssize;?>" /> 
				<input type="hidden" name="rss-image-class" value="<?php echo $rssimageclass;?>" /> 
			<?php } elseif($slider_type == 9) { 
				$postattchid = $_POST['postattch-id'];
			?>
				<input type="hidden" name="postattch-id" value="<?php echo $postattchid;?>" /> 
			<?php }  elseif($slider_type == 10) { 
				$nextgenid = $_POST['nextgen-id'];
				$nextgenanchor = isset($_POST['nextgen-anchor'])?$_POST['nextgen-anchor']:'';
			?>
				<input type="hidden" name="nextgen-id" value="<?php echo $nextgenid;?>" /> 
				<input type="hidden" name="nextgen-anchor" value="<?php echo $nextgenanchor;?>" /> 
			<?php } elseif($slider_type == 11) { 
				$playlistid = $_POST['yt-playlist-id'];
			?>
				<input type="hidden" name="yt-playlist-id" value="<?php echo $playlistid;?>" /> 
			<?php } elseif($slider_type == 12) { 
				$searchterm = $_POST['yt-search-term'];
			?>
				<input type="hidden" name="yt-search-term" value="<?php echo $searchterm;?>" /> 
			<?php } elseif($slider_type == 13) { 
				$vimeotype = $_POST['vimeo-type'];
				$vimeoval = $_POST['vimeo-val'];
			?>
				<input type="hidden" name="vimeo-type" value="<?php echo $vimeotype;?>" /> 
				<input type="hidden" name="vimeo-val" value="<?php echo $vimeoval;?>" /> 
			<?php } elseif($slider_type == 14) { 
				$fbpage = $_POST['fb-pg-url'];
				$fbalbum = $_POST['fb-album'];
			?>
				<input type="hidden" name="fb-pg-url" value="<?php echo $fbpage;?>" /> 
				<input type="hidden" name="fb-album" value="<?php echo $fbalbum;?>" />
			<?php } elseif($slider_type == 15) { 
				$username = $_POST['user-name'];
			?>
				<input type="hidden" name="user-name" value="<?php echo $username;?>" /> 
			<?php } elseif($slider_type == 16) { 
				$flickrtype = $_POST['flickr-type'];
				$flickrid = $_POST['fl-id'];
			?>
				<input type="hidden" name="flickr-type" value="<?php echo $flickrtype;?>" /> 
				<input type="hidden" name="fl-id" value="<?php echo $flickrid;?>" /> 
			<?php } elseif($slider_type == 18) { 
				$feature = $_POST['feature'];
				if( $feature == "user" ||  $feature == "user_favorites" ) {
					$pxuser = $_POST['pxuser'];
			?>
					<input type="hidden" name="pxuser" value="<?php echo $pxuser;?>" /> 		
				<?php } ?>
				<input type="hidden" name="feature" value="<?php echo $feature;?>" /> 
			<?php } ?>
			<input type="button" name="step2-prev" value="<?php _e('Back','featured-slider'); ?>" class="featured-btn-back no-margin" >
			<input type="submit" name="step4-next" value="<?php _e('Next','featured-slider'); ?>" class="featured-btn-next" >
			<div style="clear:left;"></div>
		</form>
	</div>
	<?php } elseif(isset($_GET['id']) && $_GET['id'] != '') {
		wp_enqueue_script( 'media-uploader', featured_slider_plugin_url( 'js/media-uploader.js' ),array( 'jquery', 'iris' ), FEATURED_SLIDER_VER, false);
		$slider_id = $_GET['id'];
	 ?>
	<div class="featured-steps">
		<div class="featured-head">
			<span class="featured-step" ><?php _e('Step 5','featured-slider'); ?></span> <i class="fa fa-long-arrow-right"></i> <span class="featured-step-title"><?php _e('Upload Images','featured-slider'); ?></span>
		</div>
		<!-- image Slider start-->
			<div class="uploaded-images">
				<form method="post" class="addImgForm">
					<div style="clear:left;text-align: center;" class="image-uploader">
						<input type="submit" class="upload-button slider_images_upload featured-upload" name="slider_images_upload" value="Upload" />
					</div>
					<input type="hidden" name="current_slider_id" value="<?php echo $slider_id;?>" />
				</form>
			</div>
		<!-- image Slider end-->
		<form action="" method="post">
		<input type="hidden" name="reorder_posts_slider" value="1" />
		<input type="hidden" name="slider_posts" />
		
		<div id="sslider_sortable_<?php echo $_GET['id'];?>" style="color:#326078;overflow: auto;" class="featured-img-thumbs">    
	   	<?php  
	    	$slider_posts=featured_get_slider_posts_in_order($slider_id);?>
		<input type="hidden" name="current_slider_id" value="<?php echo $slider_id;?>" />
        
		<?php
		$count = 0;	
		if(isset($sliderset) && $sliderset != '1' ) $cntr = $sliderset; else $cntr = '';
		$featured_slider_options='featured_slider_options'.$cntr;
		$featured_slider_curr=get_option($featured_slider_options);
		foreach($slider_posts as $slider_post) {
			$slider_arr[] = $slider_post->post_id;
			$post = get_post($slider_post->post_id);	  
			if(isset($post) and isset($slider_arr)){
				if ( in_array($post->ID, $slider_arr) ) {
					$img = $isimage = '';
					$count++;
					/* ---------- Image Fetch Start --------- */
					$post_id = $post->ID;
					$isimage = wp_get_attachment_url( $post->ID , false );
					$img =  '<img src="'. wp_get_attachment_url( $post->ID , false ).'" width="80" />';
					if($isimage == '') $img = '<img src="'. featured_slider_plugin_url( 'images/default_image.png' ).'" width="80" />';
					/* ------------ Image Fetch End ----------- */
					$sslider_author = get_userdata($post->post_author);
					$sslider_author_dname = $sslider_author->display_name;
					$desc = $post->post_content;
					
				 	echo '<div id="'.$post->ID.'" class="featured-reorder"><input type="hidden" name="order[]" value="'.$post->ID.'" /><div>'.$img.'<a href="'. get_edit_post_link( $post->ID ).'" target="_blank"><span class="editcore"></span></a><a href="" onclick="return confirmDelete()" ><span class="delImgSlide" id="'.$post_id.'"></span></a></div><strong> ' . $post->post_title . '</strong></div>'; 
				}
			}
		}
		    
		if ($count == 0) {
		   // echo '<div>'.__( 'No posts/pages have been added to the Slider - You can add respective post/page to slider on the Edit screen for that Post/Page', 'featured-slider' ).'</div>';
		}
			
		echo '</div><div class="submit">';
		if ($count) { echo '<input type="submit" name="remove_selected" class="button-primary" value="'. __( 'Remove Selected', 'featured-slider' ).'" onclick="return confirmRemove()" /> <input type="submit" name="remove_all" class="button-primary" value="'. __( 'Remove All at Once', 'featured-slider' ).'" onclick="return confirmRemoveAll()" /><input type="submit" name="update_slides" class="btn_save" value="'. __( 'Save', 'featured-slider' ).'"  />';}
		echo '</div></form>';
	echo '</div>';
	
	} else {
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
?>
	<div class="featured-cn-wrap">
	<form method="post" name="featured-create-new" id="featured-create-new" >
		<div class="featured-head">
			<span class="featured-step" ><?php _e('Step 1','featured-slider'); ?></span> <i class="fa fa-long-arrow-right"></i> <span class="featured-step-title"><?php _e('Select Slider Type','featured-slider'); ?></span>
		</div>
		<div class="featured-col featured-vert-line">
			<div class="featured-col-head"><?php _e('WordPress Core','featured-slider'); ?></div>
			<div class="featured-col-row">
				<input type="radio" name="slider_type" value="2" ><i class="fa fa-align-justify"></i><span class="featured-icon-title"><?php _e('Recent Posts Slider','featured-slider'); ?></span>
			</div>
			<div class="featured-col-row">
				<input type="radio" name="slider_type" value="1" ><i class="fa fa-align-justify"></i><span class="featured-icon-title"><?php _e('Category Slider','featured-slider'); ?></span>
			</div>
			<div class="featured-col-row">
				<input type="radio" name="slider_type" value="0" ><i class="fa fa-align-justify"></i><span class="featured-icon-title"><?php _e('Custom Slider','featured-slider'); ?></span>
			</div>
			<div class="featured-col-row">
				<input type="radio" name="slider_type" value="7" ><i class="fa fa-align-justify"></i><span class="featured-icon-title"><?php _e('Taxonomy Slider','featured-slider'); ?></span>
			</div>
			<div class="featured-col-row">
				<input type="radio" name="slider_type" value="17" ><i class="fa fa-align-justify"></i><span class="featured-icon-title"><?php _e('Image Slider','featured-slider'); ?></span>
			</div>
			<div class="featured-col-head second"><?php _e('Social Network','featured-slider'); ?></div>
			<div class="featured-col-row">
				<?php if($fbkey == '') { $fbclass="class='no_key'"; } else { $fbclass=""; } ?>
				<input type="radio" name="slider_type" value="14" <?php echo $fbclass; ?> ><i class="fa fa-facebook-square"></i><span class="featured-icon-title"><?php _e('Facebook Album Slider','featured-slider'); ?></span>
				<?php if($fbkey == '') { ?> <i class="fa fa-lock" title="<?php _e('Add Facebook App Key on Global Settings');?>"></i> <?php } ?>
			</div>
			<div class="featured-col-row">
				<?php if($igkey == '') { $igclass="class='no_key'"; } else { $igclass=""; } ?>
				<input type="radio" name="slider_type" value="15" <?php echo $igclass; ?> ><i class="fa fa-instagram"></i><span class="featured-icon-title"><?php _e('Instagram Slider','featured-slider'); ?></span>
				<?php if($igkey == '') { ?> <i class="fa fa-lock" title="<?php _e('Add Instagram Client Id on Global Settings');?>"></i> <?php } ?>
			</div>
			<div class="featured-col-row">
				<?php if($flkey == '') { $flclass="class='no_key'"; } else { $flclass=""; } ?>
				<input type="radio" name="slider_type" value="16" <?php echo $flclass; ?> ><i class="fa fa-flickr"></i><span class="featured-icon-title"><?php _e('Flickr Slider','featured-slider'); ?></span>
				<?php if($flkey == '') { ?> <i class="fa fa-lock" title="<?php _e('Add Flickr API Key on Global Settings');?>"></i> <?php } ?>
			</div>
			<div class="featured-col-row">
				<?php if($pxkey == '') { $pxclass="class='no_key'"; } else { $pxclass=""; } ?>
				<input type="radio" name="slider_type" value="18" <?php echo $pxclass; ?> ><img src="<?php echo featured_slider_plugin_url( 'images/500px.png' ); ?>" width="13" height="14" /><span class="featured-icon-title"><?php _e('500px Slider','featured-slider'); ?></span>
				<?php if($pxkey == '') { ?> <i class="fa fa-lock" title="<?php _e('Add 500px Consumer Key on Global Settings');?>"></i> <?php } ?>
			</div>
			<div class="featured-col-head second"><?php _e('Videos','featured-slider'); ?></div>
			<div class="featured-col-row">
				<?php if($youtube_key == '') { $youtubeclass="class='no_key'"; } else { $youtubeclass=""; } ?>
				<input type="radio" name="slider_type" value="11" <?php echo $youtubeclass; ?> ><i class="fa fa-youtube"></i><span class="featured-icon-title"><?php _e('YouTube Playlist Slider','featured-slider'); ?> </span>
				<?php if($youtube_key == '') { ?> <i class="fa fa-lock" title="<?php _e('Add Youtube API Key on Global Settings');?>"></i> <?php } ?>
			</div>
			<div class="featured-col-row">
				<?php if($youtube_key == '') { $youtubeclass="class='no_key'"; } else { $youtubeclass=""; } ?>
				<input type="radio" name="slider_type" value="12" <?php echo $youtubeclass; ?> ><i class="fa fa-youtube"></i><span class="featured-icon-title"><?php _e('YouTube Search Slider','featured-slider'); ?></span>
				<?php if($youtube_key == '') { ?> <i class="fa fa-lock" title="<?php _e('Add Youtube API Key on Global Settings');?>"></i> <?php } ?>
			</div>
			<div class="featured-col-row">
				<input type="radio" name="slider_type" value="13" ><i class="fa fa-vimeo-square"></i></span><span class="featured-icon-title"><?php _e('Vimeo Slider','featured-slider'); ?></span>
			</div>
	
		</div>
		<div class="featured-col">
			<?php if(!is_plugin_active('nextgen-gallery/nggallery.php')) { 
				$nggclass="class='no_key'"; 
			} else { $nggclass = ""; } ?>
			<div class="featured-col-head"><?php _e('Gallary Integration','featured-slider'); ?></div>
			<div class="featured-col-row">
				<input type="radio" name="slider_type" value="10" <?php echo $nggclass; ?> ><i class="fa fa-picture-o"></i><span class="featured-icon-title"><?php _e('NextGen Gallery Slider','featured-slider'); ?></span>
				<?php if($nggclass != '') { ?> <i class="fa fa-lock" title="Install NextGen Gallery Plugin to use it"></i> <?php } ?>
			</div>
			
			<div class="featured-col-head second"><?php _e('Ecommerce','featured-slider'); ?></div>
			
			<?php if(!is_plugin_active('woocommerce/woocommerce.php') ) { 
				$wooclass = "class='no_key'" ;
				} else {
					$wooclass = "";
				} ?>
				<div class="featured-col-row">
					<input type="radio" name="slider_type" value="3" <?php echo $wooclass; ?> ><i class="fa fa-shopping-cart"></i><span class="featured-icon-title"><?php _e('WooCommerce Slider','featured-slider'); ?></span>
					<?php if($wooclass != '') { ?> <i class="fa fa-lock" title="Install WooCommerce Plugin to use it"></i> <?php } ?>
				</div>
			
			<?php if(!is_plugin_active('wp-e-commerce/wp-shopping-cart.php') ) { 
					$ecomclass = "class='no_key'" ;
				} else {
					$ecomclass = "";
				} ?>
				<div class="featured-col-row">
					<input type="radio" name="slider_type" value="4" <?php echo $ecomclass; ?> ><i class="fa fa-shopping-cart"></i><span class="featured-icon-title"><?php _e('WP Ecommerce Slider','featured-slider'); ?></span>
					<?php if($ecomclass != '') { ?> <i class="fa fa-lock" title="Install WP Ecommerce Plugin to use it"></i> <?php } ?>
				</div>
			
				<div class="featured-col-head second"><?php _e('Events','featured-slider'); ?></div>
			
			<?php if(!is_plugin_active('events-manager/events-manager.php') ) { 
					$emanclass = "class='no_key'" ;
				} else {
					$emanclass = "";
				} ?>
				<div class="featured-col-row">
					<input type="radio" name="slider_type" value="5" <?php echo $emanclass; ?> ><i class="fa fa-align-justify"></i><span class="featured-icon-title"><?php _e('Event Manager','featured-slider'); ?></span>
				<?php if($emanclass != '') { ?> <i class="fa fa-lock" title="Install Event Manager Plugin to use it"></i> <?php } ?>
				</div>
			
			<?php if(!is_plugin_active('the-events-calendar/the-events-calendar.php') ) { 
					$ecalclass = "class='no_key'" ;
				} else {
					$ecalclass = "";
				} ?>
				<div class="featured-col-row">
					<input type="radio" name="slider_type" value="6" <?php echo $ecalclass; ?> ><i class="fa fa-align-justify"></i><span class="featured-icon-title"><?php _e('Event Calender','featured-slider'); ?></span>
					<?php if($ecalclass != '') { ?> <i class="fa fa-lock" title="Install Event Calender Plugin to use it"></i> <?php } ?>
				</div>
			
			<div class="featured-col-head second"><?php _e('Miscellaneous','featured-slider'); ?></div>
			<div class="featured-col-row">
				<input type="radio" name="slider_type" value="9" ><i class="fa fa-align-justify"></i><span class="featured-icon-title"><?php _e('Post Attachments Slider','featured-slider'); ?></span>
			</div>
			<div class="featured-col-row">
				<input type="radio" name="slider_type" value="8" ><i class="fa fa-rss-square"></i><span class="featured-icon-title"><?php _e('RSS feed Slider','featured-slider'); ?></span>
			</div>
		</div>
	</form>
	<div style="clear:left;"></div>
	</div>
<?php
	}
	?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("input[name='setting-type']").click(function() {
			if(jQuery(this).val() == "new-set") {
				jQuery(".featured-select-skin").css({"display":"block"});
				jQuery("input[name='step3-create']").css({"display":"none"});
				jQuery("input[name='step3-next']").css({"display":"block"});
				jQuery(".featured-old-settings").slideUp("slow");
			}
			else {
				jQuery(".featured-select-skin").css({"display":"none"});
			}
		});
		jQuery(".cmn-toggle-round").click(function() {
			jQuery(".cmn-toggle-round").not(jQuery(this)).prop("checked",false);
			if(jQuery(this).prop("checked") == true ) {
				var skin = jQuery(this).val();
				jQuery("#featured-create-new-step3").find(".skin").val(skin);
			}
		});
		jQuery(".featured-old-set").click(function() {
			jQuery("input[name='setting-type']").prop("checked",false);
			jQuery(".featured-select-skin").css({"display":"none"});
			jQuery(".featured-old-settings").slideDown("slow");
			jQuery("input[name='step3-create']").css({"display":"block"});
			jQuery("input[name='step3-next']").css({"display":"none"});
		});
		if(jQuery(".cmn-toggle-round").prop("checked") == true ) {
			var skin = jQuery(".cmn-toggle-round").val();
			jQuery("#featured-create-new-step3").find(".skin").val(skin);
		}
		jQuery(".featured-btn-back").click(function() {
			history.go(-1);
		});
		jQuery(".feature").change(function() {
			if(jQuery(this).val() == 'user' || jQuery(this).val() == 'user_favorites' ) {
				jQuery(".pxuser").slideDown();
			} else {
				jQuery(".pxuser").slideUp( "slow" );
			}
		});
		/* To uncheck the radio on load */
		jQuery("#featured-create-new").find("input[type='radio']").prop("checked",false);
	});
	</script>
<?php
}
?>
