<?php
class Featured_Slider_Simple_Widget extends WP_Widget {
	function Featured_Slider_Simple_Widget() {
		$widget_options = array('classname' => 'featured_slider_wclass', 'description' => 'Insert Featured Slider' );
		$this->WP_Widget('featured_sslider_wid', 'Featured Slider - Simple', $widget_options);
	}

	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		$title = apply_filters( 'widget_title', $instance['title'] );
		
		echo $before_widget;
		$slider_id = empty($instance['slider_id']) ? '1' : apply_filters('widget_slider_id', $instance['slider_id']);
		$set = empty($instance['set']) ? '' : apply_filters('widget_set', $instance['set']);
		if ( ! empty( $title ) ) echo $before_title . $title . $after_title; 
		 get_featured_slider($slider_id,$set);
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['slider_id'] = strip_tags($new_instance['slider_id']);
		$instance['set'] = strip_tags($new_instance['set']);
		
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title'=>'', 'slider_id' => '','set' => '' ) );
		$set = strip_tags($instance['set']);
		$scounter=get_option('featured_slider_scounter');

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '';
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>	
		<?php 		
		$slider_id = strip_tags($instance['slider_id']);		
		$sliders = featured_ss_get_sliders();
		$sname_html='<option value="0" selected >Select the Slider</option>';
	 	foreach ($sliders as $slider) { 
			 if($slider['slider_id']==$slider_id){$selected = 'selected';} else{$selected='';}
			 $sname_html =$sname_html.'<option value="'.$slider['slider_id'].'" '.$selected.'>'.$slider['slider_name'].'</option>';
		  } 
	?>
				<p><label for="<?php echo $this->get_field_id('slider_id'); ?>">Select Slider Name: <select class="widefat" id="<?php echo $this->get_field_id('slider_id'); ?>" name="<?php echo $this->get_field_name('slider_id'); ?>"><?php echo $sname_html;?></select></label></p>
  <?php     
         $sset_html='<option value="0" selected >Select the Settings</option>';
		  for($i=1;$i<=$scounter;$i++) { 
			 if($i==$set){$selected = 'selected';} else{$selected='';}
			   if($i==1){
			     $settings='Default Settings';
				 $sset_html =$sset_html.'<option value="'.$i.'" '.$selected.'>'.$settings.'</option>';
			   }
			   else{
				  if($settings_set=get_option('featured_slider_options'.$i))
					$sset_html =$sset_html.'<option value="'.$i.'" '.$selected.'>'.$settings_set['setname'].' (ID '.$i.')</option>';
			   }
		  } 
   ?>             
         <p><label for="<?php echo $this->get_field_id('set'); ?>">Select Settings to Apply: <select class="widefat" id="<?php echo $this->get_field_id('set'); ?>" name="<?php echo $this->get_field_name('set'); ?>"><?php echo $sset_html;?></select></label></p> 
         
<?php }
}
add_action( 'widgets_init', create_function('', 'return register_widget("Featured_Slider_Simple_Widget");') );

//Category Widget
class Featured_Slider_Category_Widget extends WP_Widget {
	function Featured_Slider_Category_Widget() {
		$widget_options = array('classname' => 'featured_sliderc_wclass', 'description' => 'Featured Category Slider' );
		$this->WP_Widget('featured_ssliderc_wid', 'Featured Slider - Category', $widget_options);
	}

	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
	   		
		$title = apply_filters( 'widget_title', $instance['title'] );
		
		echo $before_widget;
		
		$cat = empty($instance['cat']) ? '' : apply_filters('widget_cat', $instance['cat']);
		$sldr_title = empty($instance['sldr_title']) ? '' : apply_filters('widget_cat', $instance['sldr_title']);
		$set = empty($instance['set']) ? '' : apply_filters('widget_set', $instance['set']);

		if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
		 get_featured_slider_category($cat,$set,$sldr_title,$offset='0');
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
	   	$instance = $old_instance;
		
		$instance['cat'] = strip_tags($new_instance['cat']);
		$instance['sldr_title'] = strip_tags($new_instance['sldr_title']);
		$instance['set'] = strip_tags($new_instance['set']);
		
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

	function form($instance) {
	   	$scounter=get_option('featured_slider_scounter');

		$instance = wp_parse_args( (array) $instance, array( 'cat' => '','title' => '','set' => '','sldr_title'=>'' ) );
		$cat = strip_tags($instance['cat']);
		$sldr_title = strip_tags($instance['sldr_title']);
		$set = strip_tags($instance['set']);

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '';
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>	
		<?php
			
		$categories = get_categories();
		$scat_html='<option value="" selected >Select the Category</option>';
 
		foreach ($categories as $category) { 
		 if($category->slug==$cat){$selected = 'selected';} else{$selected='';}
		 $scat_html =$scat_html.'<option value="'.$category->slug.'" '.$selected.'>'. $category->name .'</option>';
		} 
	?>
		  <p><label for="<?php echo $this->get_field_id('sldr_title'); ?>">Slider Title: <input class="widefat" id="<?php echo $this->get_field_id('sldr_title'); ?>" name="<?php echo $this->get_field_name('sldr_title'); ?>" type="text" value="<?php echo $sldr_title;?>" /></label></p>
		  <p><label for="<?php echo $this->get_field_id('cat'); ?>">Select Category for Slider: <select class="widefat" id="<?php echo $this->get_field_id('cat'); ?>" name="<?php echo $this->get_field_name('cat'); ?>"><?php echo $scat_html;?></select></label></p>
  
  <?php  
          $sset_html='<option value="0" selected >Select the Settings</option>';
		  for($i=1;$i<=$scounter;$i++) { 
			 if($i==$set){$selected = 'selected';} else{$selected='';}
			   if($i==1){
			     $settings='Default Settings';
				 $sset_html =$sset_html.'<option value="'.$i.'" '.$selected.'>'.$settings.'</option>';
			   }
			   else{
			       if($settings_set=get_option('featured_slider_options'.$i))
					$sset_html =$sset_html.'<option value="'.$i.'" '.$selected.'>'.$settings_set['setname'].' (ID '.$i.')</option>';
			   }
		  } 
   ?>             
         <p><label for="<?php echo $this->get_field_id('set'); ?>">Select Settings to Apply: <select class="widefat" id="<?php echo $this->get_field_id('set'); ?>" name="<?php echo $this->get_field_name('set'); ?>"><?php echo $sset_html;?></select></label></p> 
         
<?php }
}
add_action( 'widgets_init', create_function('', 'return register_widget("Featured_Slider_Category_Widget");') );

//Recent Posts Widget
class Featured_Slider_Recent_Widget extends WP_Widget {
	function Featured_Slider_Recent_Widget() {
		$widget_options = array('classname' => 'featured_sliderr_wclass', 'description' => 'Featured Recent Posts Slider' );
		$this->WP_Widget('featured_ssliderr_wid', 'Featured Slider - Recent Posts', $widget_options);
	}

	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
	   	$title = apply_filters( 'widget_title', $instance['title'] );
		
		echo $before_widget;
		
		$sldr_title = empty($instance['sldr_title']) ? '' : apply_filters('widget_cat', $instance['sldr_title']);
		$set = empty($instance['set']) ? '' : apply_filters('widget_set', $instance['set']);

		if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
		 get_featured_slider_recent($set,$sldr_title,$offset='0');
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
	   	$instance = $old_instance;
		
		$instance['sldr_title'] = strip_tags($new_instance['sldr_title']);
		$instance['set'] = strip_tags($new_instance['set']);
		
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

	function form($instance) {
	   	$scounter=get_option('featured_slider_scounter');

		$instance = wp_parse_args( (array) $instance, array( 'title' => '','set' => '','sldr_title'=>'' ) );
		$sldr_title = strip_tags($instance['sldr_title']);
		$set = strip_tags($instance['set']);
		
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '';
		}
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title;?>" /></label></p>
				
		<p><label for="<?php echo $this->get_field_id('sldr_title'); ?>">Slider Title: <input class="widefat" id="<?php echo $this->get_field_id('sldr_title'); ?>" name="<?php echo $this->get_field_name('sldr_title'); ?>" type="text" value="<?php echo $sldr_title;?>" /></label></p>	
  
  <?php  
          $sset_html='<option value="0" selected >Select the Settings</option>';
		  for($i=1;$i<=$scounter;$i++) { 
			 if($i==$set){$selected = 'selected';} else{$selected='';}
			   if($i==1){
			     $settings='Default Settings';
				 $sset_html =$sset_html.'<option value="'.$i.'" '.$selected.'>'.$settings.'</option>';
			   }
			   else{
			       if($settings_set=get_option('featured_slider_options'.$i))
					$sset_html =$sset_html.'<option value="'.$i.'" '.$selected.'>'.$settings_set['setname'].' (ID '.$i.')</option>';
			   }
		  } 
   ?>             
         <p><label for="<?php echo $this->get_field_id('set'); ?>">Select Settings to Apply: <select class="widefat" id="<?php echo $this->get_field_id('set'); ?>" name="<?php echo $this->get_field_name('set'); ?>"><?php echo $sset_html;?></select></label></p> 
         
<?php }
}
add_action( 'widgets_init', create_function('', 'return register_widget("Featured_Slider_Recent_Widget");') );
?>
