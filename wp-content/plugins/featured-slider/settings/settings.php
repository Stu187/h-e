<?php 
// Hook for adding admin menus
if ( is_admin() ){ // admin actions
  add_action('admin_menu', 'featured_slider_settings');
} 
// function for adding settings page to wp-admin
function featured_slider_settings() {	
	// Add a new submenu under Options:
	add_menu_page( 'Featured Slider', 'Featured Slider', 'manage_options','featured-slider-admin', 'featured_slider_create_new_slider');
	add_submenu_page('featured-slider-admin', 'Featured Sliders', 'Create New', 'manage_options', 'featured-slider-admin', 'featured_slider_create_new_slider');
	 $firstPage = add_submenu_page('featured-slider-admin', 'Manage Slider', 'Manage Slider', 'manage_options', 'manage-featured-slider', 'featured_slider_create_multiple_sliders');
	add_submenu_page('featured-slider-admin', 'Featured Slider Settings', 'Global Settings', 'manage_options', 'featured-slider-global-settings', 'featured_slider_gbl_settings');
	add_submenu_page('featured-slider-admin', 'Featured Slider Settings', 'Setting Sets', 'manage_options', 'featured-slider-settings', 'featured_slider_settings_page');
	add_submenu_page('featured-slider-admin', 'Featured Slider License Key', 'License', 'manage_options', 'featured-slider-license-key', 'featured_slider_license');
	add_submenu_page($firstPage, 'Featured Slider Easy Builder', 'Featured Slider Easy Builder', 'manage_options', 'featured-slider-easy-builder', 'featured_slider_easybuilder_page');
}
require_once (dirname (__FILE__) . '/global_settings.php');
require_once (dirname (__FILE__) . '/create-new.php');
require_once (dirname (__FILE__) . '/sliders.php');
require_once (dirname (__FILE__) . '/license.php');
require_once (FEATURED_SLIDER_INC_DIR.'fonts.php');

//Create Set & Export Settings
function featured_process_set_requests(){
	$default_featured_slider_settings=get_featured_slider_default_settings();
	$scounter=get_option('featured_slider_scounter');
	$cntr='';
	if(isset($_GET['scounter'])) $cntr = $_GET['scounter'];
	
	if(isset($_POST['create_set'])){
		if ($_POST['create_set']=='Create New Settings Set') {
		  $scounter++;
		  update_option('featured_slider_scounter',$scounter);
		  $options='featured_slider_options'.$scounter;
		  update_option($options,$default_featured_slider_settings);
		  $current_url = admin_url('admin.php?page=featured-slider-settings');
		  $current_url = add_query_arg('scounter',$scounter,$current_url);
		  wp_redirect( $current_url );
		  exit;
		}
	}

	//Export Settings
	if(isset($_POST['export'])){
		if ($_POST['export']=='Export') {
			@ob_end_clean();
			
			// required for IE, otherwise Content-Disposition may be ignored
			if(ini_get('zlib.output_compression'))
			ini_set('zlib.output_compression', 'Off');
			
			header('Content-Type: ' . "text/x-csv");
			header('Content-Disposition: attachment; filename="featured-settings-set-'.$cntr.'.csv"');
			header("Content-Transfer-Encoding: binary");
			header('Accept-Ranges: bytes');

			/* The three lines below basically make the
			download non-cacheable */
			header("Cache-control: private");
			header('Pragma: private');
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

			$exportTXT='';$i=0;
			$slider_options='featured_slider_options'.$cntr;
			$slider_curr=get_option($slider_options);
			foreach($slider_curr as $key=>$value){
				if($key != 'active_tab' ) {
					if($i>0) $exportTXT.="\n";
					if(!is_array($value)){
						$exportTXT.=$key.",".$value;
					}
					else {
						$exportTXT.=$key.',';
						$j=0;
						if($value) {
							foreach($value as $v){
								if($j>0) $exportTXT.="|";
								$exportTXT.=$v;
								$j++;
							}
						}
					}
					$i++;
				}
			}
			$exportTXT.="\n";
			$exportTXT.="slider_name,featured";
			print($exportTXT); 
			exit();
		}
	}	
}
add_action('load-featured-slider_page_featured-slider-settings','featured_process_set_requests');

// This function displays the page content for the Featured Slider Options submenu
function featured_slider_settings_page() {
$gfeatured_slider = get_option('featured_slider_global_options');
$default_featured_slider_settings=get_featured_slider_default_settings();
$scounter=get_option('featured_slider_scounter');
if (isset($_GET['scounter']))$cntr = $_GET['scounter'];
else $cntr = '';

$new_settings_msg='';
/* Include settings file of each skin - strat */
$directory = FEATURED_SLIDER_CSS_DIR;
if ($handle = opendir($directory)) {
    while (false !== ($file = readdir($handle))) { 
     if($file != '.' and $file != '..') { 
		require_once ( dirname( dirname(__FILE__) ) . '/var/skins/'.$file.'/settings.php');
   } }
    closedir($handle);
}

/* Include settings file of each skin- end */

//Reset Settings
if (isset ($_POST['featured_reset_settings_submit'])) {
	if ( $_POST['featured_reset_settings']!='n' ) {
	  $featured_reset_settings=$_POST['featured_reset_settings'];
	  $options='featured_slider_options'.$cntr;
	  $optionsvalue=get_option($options);
	  if( $featured_reset_settings == 'g' ){
		$new_settings_value=$default_featured_slider_settings;
		$new_settings_value['setname']=isset($optionsvalue['setname'])?$optionsvalue['setname']:'Set';
		update_option($options,$new_settings_value);
	  }
	   elseif(!is_numeric($featured_reset_settings)){
		$skin=$featured_reset_settings;
		$new_settings_value=$default_featured_slider_settings;
		$skin_defaults_str='default_settings_'.$skin;
		global ${$skin_defaults_str};
		if(count(${$skin_defaults_str})>0){
			foreach(${$skin_defaults_str} as $key=>$value){
				$new_settings_value[$key]=$value;	
			}
			$new_settings_value['stylesheet']=$skin;
		}
		if(!isset($optionsvalue['setname']) or $optionsvalue['setname'] == '')
			$optionsvalue['setname']=$default_featured_slider_settings['setname'];
		$new_settings_value['setname']=$optionsvalue['setname'];		
		update_option($options,$new_settings_value);
	  }
	  else{
		if( $featured_reset_settings == '1' ){
			$new_settings_value=get_option('featured_slider_options');
			if(!isset($new_settings_value['setname']) or $new_settings_value['setname'] == '')
				$new_settings_value['setname']=$optionsvalue['setname'];
			update_option($options,	$new_settings_value );
		}
		else{
			$new_option_name='featured_slider_options'.$featured_reset_settings;
			$new_settings_value=get_option($new_option_name);
			if(!isset($new_settings_value['setname']) or $new_settings_value['setname'] == '')
				$new_settings_value['setname']=$optionsvalue['setname'];
			update_option($options,	$new_settings_value );
		}
	  }
	}
}

//Import Settings
if (isset ($_POST['import'])) {
	if ($_POST['import']=='Import') {
		global $wpdb;
		$imported_settings_message='';
		$csv_mimetypes = array('text/csv','text/x-csv','text/plain','application/csv','text/comma-separated-values','application/excel','application/vnd.ms-excel','application/vnd.msexcel','text/anytext','application/octet-stream','application/txt');
		if ($_FILES['settings_file']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['settings_file']['tmp_name']) && in_array($_FILES['settings_file']['type'], $csv_mimetypes) ) { 
			$imported_settings=file_get_contents($_FILES['settings_file']['tmp_name']); 
			$settings_arr=explode("\n",$imported_settings);
			$slider_settings=array();
			foreach($settings_arr as $settings_field){
				$s=explode(',',$settings_field);
				$inner=explode('|',$s[1]);
				if(strpos($s[0],'fontgsubset') !== false && empty($s[1]) ) {
					$slider_settings[$s[0]]=array();
				} elseif(strpos($s[0],'fontgsubset') !== false && count($inner) == 1 ) {
					$slider_settings[$s[0]]=array($s[1]);
				} else {
					if(count($inner)>1) $slider_settings[$s[0]]=$inner;
					else $slider_settings[$s[0]]=$s[1];
				}
			}
			$slider_settings['active_tab']=array('active_tabidx'=>'0','closed_sections'=>'');
			$options='featured_slider_options'.$cntr;
			
			if( $slider_settings['slider_name'] == 'featured' )	{
				update_option($options,$slider_settings);
				$new_settings_msg='<div id="message" class="updated fade" style="clear:left;"><h3>'.__('Settings imported successfully ','featured-slider').'</h3></div>';
				$imported_settings_message='<div style="clear:left;color:#006E2E;"><h3>'.__('Settings imported successfully ','featured-slider').'</h3></div>';
			}
			else {
				$new_settings_msg=$imported_settings_message='<div id="message" class="error fade" style="clear:left;"><h3>'.__('Settings imported do not match to Featured Slider Settings. Please check the file.','featured-slider').'</h3></div>';
				$imported_settings_message='<div style="clear:left;color:#ff0000;"><h3>'.__('Settings imported do not match to Featured Slider Settings. Please check the file.','featured-slider').'</h3></div>';
			}
		}
		else{
			$new_settings_msg=$imported_settings_message='<div id="message" class="error fade" style="clear:left;"><h3>'.__('Error in File, Settings not imported. Please check the file being imported. ','featured-slider').'</h3></div>';
			$imported_settings_message='<div style="clear:left;color:#ff0000;"><h3>'.__('Error in File, Settings not imported. Please check the file being imported. ','featured-slider').'</h3></div>';
		}
	}
}

//Delete Set
if (isset ($_POST['delete_set'])) {
	if ($_POST['delete_set']=='Delete this Set' and isset($cntr) and !empty($cntr)) {
	  $options='featured_slider_options'.$cntr;
	  delete_option($options);
	  $cntr='';
	}
}

$group='featured-slider-group'.$cntr;
$featured_slider_options='featured_slider_options'.$cntr;
$featured_slider_curr=get_option($featured_slider_options);
if(!isset($cntr) or empty($cntr)){$curr = 'Default';}
else{$curr = $cntr;}
$featured_slider_curr= populate_featured_current($featured_slider_curr);
/* Save Featured Slider Settings */

if(isset($_POST['save_settings']) && !empty($_POST['save_settings']) ) {
	if($_POST['option_page'] == $group && strpos($_POST['_wp_http_referer'],'featured-slider-settings') !== false && $_POST['action'] == 'update' ) {
		$options='featured_slider_options'.$cntr;
		foreach($featured_slider_curr as $key=>$value) {
			if(isset($_POST['featured_slider_options'.$cntr][$key])) {
				$post_value=$_POST['featured_slider_options'.$cntr][$key];
				if(is_string($_POST['featured_slider_options'.$cntr][$key])) $post_value = stripslashes($_POST['featured_slider_options'.$cntr][$key]);
				$new_settings_value[$key]=$post_value;
			} else {
				$new_settings_value[$key]=$value;
			}
		}
		if(isset($_POST['featured_slider_options'.$cntr]['stylesheet']) && $featured_slider_curr['stylesheet'] != $_POST['featured_slider_options'.$cntr]['stylesheet'] ) { 
			/* Poulate skin specific settings */	
			$skin = $_POST['featured_slider_options'.$cntr]['stylesheet'];
			$skin_defaults_str='default_settings_'.$skin;
			global ${$skin_defaults_str};
			if(count(${$skin_defaults_str})>0){
				foreach(${$skin_defaults_str} as $key=>$value){
					$new_settings_value[$key]=$value;	
				} 
			}
			/* END - Poulate skin specific settings */ 
		}
		update_option($options,$new_settings_value);
		/* Undo - Save previous Settings */
		set_transient( 'featured_undo_set', $featured_slider_curr);
		/* END Undo previous Settings */
		$featured_slider_curr=$new_settings_value;
	}
}
if(isset($_POST['undo_settings']) && get_transient( 'featured_undo_set' ) != false ) {
	$options='featured_slider_options'.$cntr;
	$new_settings_value = get_transient( 'featured_undo_set' ); 
	update_option($options,$new_settings_value);
	/* Undo - Save previous Settings */
	delete_transient( 'featured_undo_set' );
	/* END Undo previous Settings */
	$featured_slider_curr=$new_settings_value;
}
?>
<div class="wrap" style="clear:both;">
<form style="float:right;margin:10px 20px" action="" method="post">
<?php if(isset($cntr) and !empty($cntr)){ ?>
<input type="submit" class="button-primary" value="Delete this Set" name="delete_set"  onclick="return confirmSettingsDelete()" />
<?php } ?>
</form>
<h2 class="top_heading"><?php _e('Featured Slider Settings: ','featured-slider'); echo '<span>'.$curr.'</span>'; ?> </h2>
<div class="svilla_cl"></div>
<?php echo $new_settings_msg;?>
<?php 
if ($featured_slider_curr['disable_preview'] != '1'){
?>
<div id="settings_preview"><h2 class="heading"><?php _e('Preview','featured-slider'); ?></h2> 
<?php 
if(isset($featured_slider_curr['offset']) && $featured_slider_curr['offset'] != '') 
	$offset = $featured_slider_curr['offset']; 
else $offset = '0';

if(empty($cntr))$setCntr='1';
else $setCntr=$cntr;
if ($featured_slider_curr['preview'] == "0")
	get_featured_slider($featured_slider_curr['slider_id'],$setCntr,$offset);
elseif($featured_slider_curr['preview'] == "1")
	get_featured_slider_category($featured_slider_curr['catg_slug'],$setCntr,'',$offset);
elseif($featured_slider_curr['preview'] == "2")
	get_featured_slider_recent($setCntr,'',$offset);
elseif($featured_slider_curr['preview'] == "3") {
	$args['offset'] = $offset;
	if(isset($featured_slider_curr['product_woocatg_slug'])) {
		$args['term'] = isset($featured_slider_curr['product_woocatg_slug'])?$featured_slider_curr['product_woocatg_slug']:'';
		$args['taxonomy'] = 'product_cat';
	}
	$args['type'] = isset($featured_slider_curr['woo_type'])?$featured_slider_curr['woo_type']:'';
	$args['post_type']='product';
	$args['product_id']= isset($featured_slider_curr['product_id'])?$featured_slider_curr['product_id']:''; ;
	$args['set']=$setCntr;
	get_featured_slider_woocommerce($args);
}
elseif($featured_slider_curr['preview'] == "4") {
	$args['offset'] = $offset;
	if(isset($featured_slider_curr['ecom_type']) && $featured_slider_curr['ecom_type'] == '1' && isset($featured_slider_curr['product_ecomcatg_slug']) ) {
			$args['term'] = $featured_slider_curr['product_ecomcatg_slug'];
			$args['taxonomy'] = 'wpsc_product_category';
	}
	$args['post_type']='wpsc-product';
	$args['set']=$setCntr;
	$args['data']['type'] = 'ecom';
	get_featured_slider_taxonomy($args);
}
elseif($featured_slider_curr['preview'] == "5") {
	$args['offset'] = $offset;
	if(isset($featured_slider_curr['events_mancatg_slug']) && $featured_slider_curr['events_mancatg_slug'] != '' )
		$args['term'] = $featured_slider_curr['events_mancatg_slug'];
	if(isset($featured_slider_curr['events_mantag_slug']) && $featured_slider_curr['events_mantag_slug'] != '' ) 
		$args['tags'] = $featured_slider_curr['events_mantag_slug'];
	$args['scope'] = isset($featured_slider_curr['event_type'])?$featured_slider_curr['event_type']:'';
	$args['post_type']='event';
	$args['set']=$setCntr;
	get_featured_slider_event($args);
}
elseif($featured_slider_curr['preview'] == "6") {
	$args['offset'] = $offset;
	if(isset($featured_slider_curr['events_calcatg_slug']) && $featured_slider_curr['events_calcatg_slug'] != '' ) {
		$args['term'] = $featured_slider_curr['events_calcatg_slug'];
		$args['taxonomy'] = 'tribe_events_cat';
	}
	if(isset($featured_slider_curr['events_caltag_slug']) && $featured_slider_curr['events_caltag_slug'] != '' ) 
		$args['tags'] = $featured_slider_curr['events_caltag_slug'];
	$args['type'] = isset($featured_slider_curr['eventcal_type'])?$featured_slider_curr['eventcal_type']:'';
	$args['post_type']='tribe_events';
	$args['set']=$setCntr;
	get_featured_slider_event_calender($args);
}
elseif($featured_slider_curr['preview'] == "7") {
	$args=array(
		'post_type'=>$featured_slider_curr['taxonomy_posttype'],
		'taxonomy'=>$featured_slider_curr['taxonomy'],
		'term'=>$featured_slider_curr['taxonomy_term'],
		'set'=>$setCntr,
		'offset'=>$offset,
		'show'=>$featured_slider_curr['taxonomy_show'],
		'operator'=>$featured_slider_curr['taxonomy_operator'],
		'author'=>$featured_slider_curr['taxonomy_author']
	);
	get_featured_slider_taxonomy($args);
}
elseif($featured_slider_curr['preview'] == "8") {
	$args=array(
		'set'=>$setCntr,
		'offset'=>$offset,
		'feedurl'=>$featured_slider_curr['rssfeed_feedurl'],
		'default_image'=>$featured_slider_curr['rssfeed_default_image'],
		'title'=>'',
		'id'=>$featured_slider_curr['rssfeed_id'],
		'feed'=>$featured_slider_curr['rssfeed_feed'],
		'order'=>$featured_slider_curr['rssfeed_order'],
		'content'=>$featured_slider_curr['rssfeed_content'],  
		'media'=>$featured_slider_curr['rssfeed_media'],
		'src'=>$featured_slider_curr['rssfeed_src'],
		'size'=>$featured_slider_curr['rssfeed_size'],
		'image_class'=>$featured_slider_curr['rssfeed_image_class']
	);
	get_featured_slider_feed($args);
}
elseif($featured_slider_curr['preview'] == "9") {
	$args=array(
		'set'=>$setCntr,
		'offset'=>$offset,
		'id'=>$featured_slider_curr['postattch_id']
	);
	get_featured_slider_attachments($args);
}
elseif($featured_slider_curr['preview'] == "10") {
	$args=array(
		'gallery_id'=>$featured_slider_curr['nextgen_gallery_id'],
		'set'=>$setCntr,
		'offset'=>$offset,
		'anchor'=>$featured_slider_curr['nextgen_anchor']
	);
	get_featured_slider_ngg($args);
}
?></div>
<?php } ?>

<?php echo $new_settings_msg;?>

<div id="featured_settings" >
<form method="post" id="featured_slider_form" class="featured-settings-form">
<?php settings_fields($group); ?>

<?php
if(!isset($cntr) or empty($cntr)){}
else{?>
	<table class="form-table">
		<tr valign="top">
		<th scope="row"><h3><?php _e('Setting Set Name','featured-slider'); ?></h3></th>
		<td><h3><input type="text" name="<?php echo $featured_slider_options;?>[setname]" id="featured_slider_setname" class="regular-text" value="<?php echo $featured_slider_curr['setname']; ?>" /></h3></td>
		</tr>
	</table>
<?php }
?>

<div class="slider_tabs">

	  	<div class="honeyflower settings-tab tab-active"><a href="#content" id="content"><?php _e('Slider Preview','featured-slider'); ?></a></div>

		<div class="green settings-tab"><a href="#basic" id="basic"><?php _e('Basic Settings','featured-slider'); ?></a></div>

		<div class="blue settings-tab"><a href="#slides" id="slides"><?php _e('Slide Settings','featured-slider'); ?></a></div>

	    	<div class="orange settings-tab"><a href="#navarrow" id="navarrow"><?php _e('Navigation','featured-slider'); ?></a></div>

		<div class="gray settings-tab" id="featuredwoo"><a href="#woo" id="woo"><?php _e('eCommerce','featured-slider'); ?></a></div>
		<div class="jelly settings-tab" id="event_manager"><a href="#events" id="events"><?php _e('Event Settings','featured-slider'); ?></a></div>
		
		<div class="plum settings-tab"><a href="#miscellaneous" id="miscellaneous"><?php _e('Miscellaneous','featured-slider'); ?></a></div>
</div>
	<div class="featured-tabs-content"></div>
	<div style="clear: left;"></div>
	<p class="submit">
		<input type="submit" class="button-primary" name="save_settings" value="<?php _e('Save Changes') ?>" />
	</p>
	<?php 
		$active_idx = isset($featured_slider_curr['active_tab']['active_tabidx'])?$featured_slider_curr['active_tab']['active_tabidx']:'0';
		$closed_sec = isset($featured_slider_curr['active_tab']['closed_sections'])?$featured_slider_curr['active_tab']['closed_sections']:'';
	?>
<input type="hidden" name="<?php echo $featured_slider_options;?>[active_tab][active_tabidx]" id="featured_activetab" value="<?php echo $active_idx; ?>" />
<input type="hidden" name="<?php echo $featured_slider_options;?>[active_tab][closed_sections]" id="featured_closedsections" value="<?php echo $closed_sec; ?>" />
<input type="hidden" name="<?php echo $featured_slider_options;?>[active_accordion]" id="featured_active_accordion" value="<?php echo $featured_slider_curr['active_accordion']; ?>" />
<input type="hidden" name="hidden_urlpage" class="featured_urlpage" value="<?php echo $_GET['page'];?>" />
<input type="hidden" name="featured-hiddencntr" class="featured-hiddencntr" value="<?php echo $cntr; ?>" />
<input type="hidden" name="hidden_preview" id="hidden_preview" value="<?php echo $featured_slider_curr['preview']; ?>" />
<input type="hidden" name="hidden_category" id="hidden_category" value="<?php echo $featured_slider_curr['catg_slug']; ?>" />
<input type="hidden" name="hidden_sliderid" id="hidden_sliderid" value="<?php echo $featured_slider_curr['slider_id']; ?>" />
<input type="hidden" name="featured-settings-nonce" id="featured-settings-nonce" value="<?php echo wp_create_nonce( 'featured-settings-nonce' ); ?>" />
<input type="hidden" name="featured-slider-nonce" id="featured-slider-nonce" value="<?php echo wp_create_nonce( 'featured-slider-nonce' ); ?>" />
<input type="hidden" name="featured-google-nonce" id="featured-google-nonce" value="<?php echo wp_create_nonce( 'featured-google-nonce' ); ?>" />
<input type="hidden" id="featured_pluginurl" value="<?php echo featured_slider_plugin_url(); ?>" />
</form>
<!--Form to reset Settings set-->
<form style="float:left;" action="" method="post">
<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Reset Settings to','featured-slider'); ?></th>
<td><select name="featured_reset_settings" id="featured_slider_reset_settings" >
<option value="n" selected ><?php _e('None','featured-slider'); ?></option>
<option value="g" ><?php _e('Global Default','featured-slider'); ?></option>
<?php 
$directory = FEATURED_SLIDER_CSS_DIR;
if ($handle = opendir($directory)) {
    while (false !== ($file = readdir($handle))) { 
     if($file != '.' and $file != '..') { 
	if($file!="default") {?>
      <option value="<?php echo $file;?>"><?php echo "'".$file."' skin";?></option>
 <?php } } }
    closedir($handle);
}
?>
<?php 
for($i=1;$i<=$scounter;$i++){
	if ($i==1){
	  echo '<option value="'.$i.'" >'.__('Default Settings Set','featured-slider').'</option>';
	}
	else {
	  if($settings_set=get_option('featured_slider_options'.$i)){
		echo '<option value="'.$i.'" >'. (isset($settings_set['setname'])? ($settings_set['setname']) : '' ) .' (ID '.$i.')</option>';
	  }
	}
}
?>

</select>
</td>
</tr>
</table>

<p class="submit">
<input name="featured_reset_settings_submit" type="submit" class="button-primary" value="<?php _e('Reset Settings') ?>" />
</p>
</form>
<input type="hidden" name="featured-loader" value="<?php echo admin_url('images/loading.gif');?>" />
<div class="svilla_cl"></div>

<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:0;" id="import">
<?php if (isset ($imported_settings_message))echo $imported_settings_message;?>
<h3><?php _e('Import Settings Set by uploading a Settings File','featured-slider'); ?></h3>
<form style="margin-right:10px;font-size:14px;" action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
<input type="file" name="settings_file" id="settings_file" style="font-size:13px;width:50%;padding:0 5px;" />
<input type="submit" value="Import" name="import"  onclick="return confirmSettingsImport()" title="<?php _e('Import Settings from a file','featured-slider'); ?>" class="button-primary" />
</form>
</div>

</div> <!--end of float left -->

<div id="poststuff" class="metabox-holder has-right-sidebar" style="float: left;max-width: 270px;">
<div class="postbox" style="margin:0 0 10px 0;"> 
	<h3 class="hndle"><span></span><?php _e('Quick Embed Shortcode','featured-slider'); ?></h3> 
	<div class="inside" id="shortcodeview">
	<?php if($cntr=='') $set=' set="1"'; else $set=' set="'.$cntr.'"';
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
	$preview = '[featuredtaxonomy post_type="wpsc-product" taxonomy="wpsc_product_category" '.$set.$offset.']';
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
echo $preview;
?>
</div>
</div>

<div class="postbox" style="margin:10px 0;"> 
	<h3 class="hndle"><span></span><?php _e('Quick Embed Template Tag','featured-slider'); ?></h3>
	<div class="inside" id="templateview">
	<?php 
 if($cntr=='') $tset=' $set="1"'; else $tset=' $set="'.$cntr.'"';
$toffset = '';
if (isset($featured_slider_curr['offset']) && $featured_slider_curr['offset'] != "0" && !empty($featured_slider_curr['offset']) )
	$toffset = ',$offset="'.$featured_slider_curr['offset'].'"';
if ($featured_slider_curr['preview'] == "0")
	echo '<code>&lt;?php if(function_exists("get_featured_slider")){get_featured_slider($slider_id="'.$featured_slider_curr['slider_id'].'",'.$tset.$toffset.');}?&gt;</code>';
elseif($featured_slider_curr['preview'] == "1")
	echo '<code>&lt;?php if(function_exists("get_featured_slider_category")){get_featured_slider_category($catg_slug="'.$featured_slider_curr['catg_slug'].'",'.$tset.$toffset.');}?&gt;</code>';
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
	echo '<code>&lt;?php if(function_exists("get_featured_slider_woocommerce")){get_featured_slider_woocommerce( "'.$args.'" );}?&gt;</code>';
}
elseif($featured_slider_curr['preview'] == "4" && $featured_slider_curr['ecom_type'] == "0")
	$preview = '[featuredtaxonomy taxonomy="wpsc_product_category" post_type="wpsc-product"'.$set.$offset.']';
elseif($featured_slider_curr['preview'] == "4" && $featured_slider_curr['ecom_type'] == "1")
	$preview = '[featuredtaxonomy post_type="wpsc-product" taxonomy="wpsc_product_category" term="'.$featured_slider_curr['product_ecomcatg_slug'].'" '.$set.$offset.']';
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
	
	echo '<code>&lt;?php if(function_exists("get_featured_slider_event")){get_featured_slider_event( "'.$args.'" );}?&gt;</code>';
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
	
	echo '<code>&lt;?php if(function_exists("get_featured_slider_event_calender")){get_featured_slider_event_calender( "'.$args.'" );}?&gt;</code>';
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
	echo '<code>&lt;?php if(function_exists("get_featured_slider_taxonomy")){get_featured_slider_taxonomy( "'.$args.'" );}?&gt;</code>';
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
	echo '<code>&lt;?php if(function_exists("get_featured_slider_feed")){get_featured_slider_feed( "'.$args.'" );}?&gt;</code>';
	
}
elseif($featured_slider_curr['preview'] == "9") {
	$args = '';
	if($cntr=='') $args .= 'set=1'; else $args .= 'set='.$cntr;
	if (isset($featured_slider_curr['offset']) && $featured_slider_curr['offset'] != "0" && !empty($featured_slider_curr['offset']) )
	$args .= '&offset='.$featured_slider_curr['offset'];
	if(isset($featured_slider_curr['postattch_id'])  && $featured_slider_curr['postattch_id'] != '' ) {
		$args .= '&id='.$featured_slider_curr['postattch_id'];
	}
	echo '<code>&lt;?php if(function_exists("get_featured_slider_attachments")){get_featured_slider_attachments( "'.$args.'" );}?&gt;</code>';
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
	echo '<code>&lt;?php if(function_exists("get_featured_slider_ngg")){get_featured_slider_ngg( "'.$args.'" );}?&gt;</code>';	
} else
	echo '<code>&lt;?php if(function_exists("get_featured_slider_recent")){get_featured_slider_recent('.$tset.$toffset.');}?&gt;</code>';
?>	
</div>
</div>


<?php $url = featured_sslider_admin_url( array( 'page' => 'featured-slider-admin' ) );?>
<form style="margin-right:10px;font-size:14px;width:100%;" action="" method="post">
<a href="<?php echo $url; ?>" title="<?php _e('Go to Sliders page where you can re-order the slide posts, delete the slides from the slider etc.','featured-slider'); ?>" class="svilla_button svilla_gray_button"><?php _e('Go to Sliders Admin','featured-slider'); ?></a>
<input type="submit" class="svilla_button" value="Create New Settings Set" name="create_set"  onclick="return confirmSettingsCreate()" /> <br />
<input type="submit" value="Export" name="export" title="<?php _e('Export this Settings Set to a file','featured-slider'); ?>" class="svilla_button" />
<a href="#import" title="<?php _e('Go to Import Settings Form','featured-slider'); ?>" class="svilla_button">Import</a>
<div class="svilla_cl"></div>
</form>
<div class="svilla_cl"></div>

<div class="postbox" style="margin:10px 0;"> 
			  <h3 class="hndle"><span></span><?php _e('Available Settings Sets','featured-slider'); ?></h3> 
			  <div class="inside">
<?php 
for($i=1;$i<=$scounter;$i++){
   if ($i==1){
      echo '<h4><a href="'.featured_sslider_admin_url( array( 'page' => 'featured-slider-settings' ) ).'" title="(Settings Set ID '.$i.')">Default Settings (ID '.$i.')</a></h4>';
   }
   else {
      if($settings_set=get_option('featured_slider_options'.$i)){
		echo '<h4><a href="'.featured_sslider_admin_url( array( 'page' => 'featured-slider-settings' ) ).'&scounter='.$i.'" title="(Settings Set ID '.$i.')">'. (isset($settings_set['setname'])? ($settings_set['setname']) : '' ) .' (ID '.$i.')</a></h4>';
	  }
   }
}
?>
</div></div>

<?php if ($gfeatured_slider['support'] == "1"){ ?>
	<div class="postbox"> 
		<div style="background:#eee;line-height:200%"><a style="text-decoration:none;font-weight:bold;font-size:100%;color:#990000" href="http://guides.slidervilla.com/featured-slider/" title="Click here to read how to use the plugin and frequently asked questions about the plugin" target="_blank"> ==> Usage Guide and General FAQs</a></div>
	</div>
          
	<div class="postbox"> 
	  <h3 class="hndle"><span><?php _e('About this Plugin:','featured-slider'); ?></span></h3> 
	  <div class="inside">
		<ul>
		<li><a href="http://slidervilla.com/featured-slider/" title="<?php _e('Featured Slider Homepage','featured-slider'); ?>
" ><?php _e('Plugin Homepage','featured-slider'); ?></a></li>
		<li><a href="http://support.slidervilla.com/" title="<?php _e('Support Forum','featured-slider'); ?>
" ><?php _e('Support Forum','featured-slider'); ?></a></li>
		<li><a href="http://guides.slidervilla.com/featured-slider/" title="<?php _e('Usage Guide','featured-slider'); ?>
" ><?php _e('Usage Guide','featured-slider'); ?></a></li>
		<li><strong>Current Version: <?php echo FEATURED_SLIDER_VER;?></strong></li>
		</ul> 
	  </div> 
	</div> 
<?php } ?>
                 
</div> <!--end of poststuff --> 

<div style="clear:left;"></div>
<div style="clear:right;"></div>

</div> <!--end of float wrap -->
<?php	
}
//WPML intigration
function featured_update_options_function($option, $oldval, $newval){
	if( function_exists('icl_plugin_action_links') && function_exists('icl_register_string') ) {
		global $wpdb;
		$matches = $wpdb->get_results( "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'featured_slider_options%'" );
		$opnamearr = array();
		foreach( $matches as $match ) {
			$opnamearr[] = $match->option_name;
		}
		if( in_array($option,$opnamearr) ) { //check if options are of featured slider 
			if( isset($newval["more"]) && !empty($newval["more"]) ) {
				icl_register_string( $context = 'featured-slider-settings', $name = '['.$option.']more', $value = $newval["more"] );
			}
			if( isset($newval["meta1_before"]) && !empty($newval["meta1_before"]) ) {
				icl_register_string( $context = 'featured-slider-settings', $name = '['.$option.']meta1_before', $value = $newval["meta1_before"] );
			}
			if( isset($newval["meta1_after"]) && !empty($newval["meta1_after"]) ) {
				icl_register_string( $context = 'featured-slider-settings', $name = '['.$option.']meta1_after', $value = $newval["meta1_after"] );
			}
			if( isset($newval["meta2_before"]) && !empty($newval["meta2_before"]) ) {
				icl_register_string( $context = 'featured-slider-settings', $name = '['.$option.']meta2_before', $value = $newval["meta2_before"] );
			}
			if( isset($newval["meta2_after"]) && !empty($newval["meta2_after"]) ) {
				icl_register_string( $context = 'featured-slider-settings', $name = '['.$option.']meta2_after', $value = $newval["meta2_after"] );
			}
			if( isset($newval["woo_adc_text"]) && !empty($newval["woo_adc_text"]) ) {
				icl_register_string( $context = 'featured-slider-settings', $name = '['.$option.']woo_adc_text', $value = $newval["woo_adc_text"] );
			}
			if( isset($newval["woo_sale_text"]) && !empty($newval["woo_sale_text"]) ) {
				icl_register_string( $context = 'featured-slider-settings', $name = '['.$option.']woo_sale_text', $value = $newval["woo_sale_text"] );
			}
		}
	}
}
add_action("update_option", "featured_update_options_function", 10, 3);
?>
