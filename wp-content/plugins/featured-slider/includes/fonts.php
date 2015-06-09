<?php 
function get_featured_google_font($name) {
	$fonts = get_featured_google_fonts();
	$font = $fonts[$name];
	return $font;
}
function get_featured_google_fonts() {
	// Variable to hold fonts;
	$fonts = array();
	$json  = array();
	$fontsfurl = featured_slider_plugin_url( 'includes/webfonts.json' );
	// Check if transient is set
	if ( false === get_transient( 'google_fonts_list' ) ) {
		//read fonts from webfonts.json
		$json  = wp_remote_fopen( $fontsfurl );
		$font_output = json_decode( $json, true );
		foreach ( $font_output['items'] as $item ) {
			$urls = array();
			// Get font properties from json array.
			foreach ( $item['variants'] as $variant ) {
				$name = str_replace( ' ', '+', $item['family'] );
				$urls[ $variant ] = "https://fonts.googleapis.com/css?family={$name}:{$variant}";
			}

			$atts = array( 
				'name'         => $item['family'],
				'category'     => $item['category'],
				'font_type'    => 'google',
				'font_weights' => $item['variants'],
				'subsets'      => $item['subsets'],
				'files'        => $item['files'],
				'urls'         => $urls
			);

			// Add this font to the fonts array
			$id           = str_replace( ' ', '+', $item['family'] );
			$fonts[ $id ] = $atts;

		}

		// Filter to allow us to modify the fonts array before saving the transient
		$fonts = apply_filters( 'get_featured_google_fonts_list', $fonts );
	
		// Set transient for google fonts
		set_transient( 'google_fonts_list', $fonts, 14 * DAY_IN_SECONDS );

	} else {
		$fonts = get_transient( 'google_fonts_list' );
	}

	return apply_filters( 'get_featured_google_fonts_list', $fonts );
}
function get_featured_google_fonts_html($name, $gid, $current_value) {
	$fonts = get_featured_google_fonts();
	//print_r($fonts);
	// Init subset array
	$featured_google_subsets = array(
		'display'     => array(),
		'handwriting' => array(),
		'monospace'   => array(),
		'sans-serif'  => array(),
		'serif'       => array(),
	);
	
	// Populate subsets
	foreach ( $fonts as $id => $properties ) {
		if ( ! empty( $properties['category'] ) ) {
			switch ( $properties['category'] ) {
				case 'display':
					$featured_google_subsets['display'][ $id ] = $properties;
					break;

				case 'handwriting':
					$featured_google_subsets['handwriting'][ $id ] = $properties;
					break;

				case 'monospace':
					$featured_google_subsets['monospace'][ $id ] = $properties;
					break;

				case 'sans-serif':
					$featured_google_subsets['sans-serif'][ $id ] = $properties;
					break;

				case 'serif':
					$featured_google_subsets['serif'][ $id ] = $properties;
					break;
			}
		}
	}
	//print_r($featured_google_subsets);
	
	$html='<select name="'.$name.'" id="'.$gid.'" class="google-fonts" autocomplete="off">
		<option value="">'. __( '&mdash; Google Fonts &mdash;', 'featured-slider' ).'</option>
		<!-- Google Serif -->';
		 if ( ! empty( $featured_google_subsets['serif'] ) ):
			$html.='<optgroup label="'. __( 'Google Serif Fonts', 'featured-slider' ).'" class="google_label">';
			 foreach ( $featured_google_subsets['serif'] as $id => $properties ) : 
					$html.='<option value="'.$id.'" data-font-type="google" '.selected( $current_value, $id, false ); 
					$html.='>'.$properties['name'].'</option>';
				
				endforeach; 
			$html.='</optgroup>';
		endif;

	$html.='<!-- Google Sans Serif -->';
	if ( ! empty( $featured_google_subsets['sans-serif'] ) ): 
		$html.='<optgroup label="'. __( 'Google Sans Serif Fonts', 'featured-slider' ).'" class="google_label">';
			foreach ( $featured_google_subsets['sans-serif'] as $id => $properties ) :
					$html.='<option value="'.$id.'" data-font-type="google" '.selected( $current_value, $id, false ); 
					$html.='>'.$properties['name'].'</option>';
				
			 endforeach; 
		$html.='</optgroup>';
	endif; 

	$html.='<!-- Google Display -->';
	if ( ! empty( $featured_google_subsets['display'] ) ): 
		$html.='<optgroup label="'. __( 'Google Display Fonts', 'featured-slider' ).'" class="google_label">';
			foreach ( $featured_google_subsets['display'] as $id => $properties ) :
					$html.='<option value="'.$id.'" data-font-type="google" '.selected( $current_value, $id, false ); 
					$html.='>'.$properties['name'].'</option>';
				
			 endforeach; 
		$html.='</optgroup>';
	endif; 
	
	$html.='<!-- Google Handwriting -->';
	if ( ! empty( $featured_google_subsets['handwriting'] ) ): 
		$html.='<optgroup label="'. __( 'Google Handwriting Fonts', 'featured-slider' ).'" class="google_label">';
			foreach ( $featured_google_subsets['handwriting'] as $id => $properties ) :
					$html.='<option value="'.$id.'" data-font-type="google" '.selected( $current_value, $id, false ); 
					$html.='>'.$properties['name'].'</option>';
				
			 endforeach; 
		$html.='</optgroup>';
	endif; 

	$html.='<!-- Google Monospace -->';
	if ( ! empty( $featured_google_subsets['monospace'] ) ): 
		$html.='<optgroup label="'. __( 'Google Monospace Fonts', 'featured-slider' ).'" class="google_label">';
			foreach ( $featured_google_subsets['monospace'] as $id => $properties ) :
					$html.='<option value="'.$id.'" data-font-type="google" '.selected( $current_value, $id, false ); 
					$html.='>'.$properties['name'].'</option>';
				
			 endforeach; 
		$html.='</optgroup>';
	endif; 
	$html.='</select>'; 
	return $html;
 }

function get_featured_google_font_weight($currfont, $name, $id, $current_value) {
	$html = '';
	$html.='<select name="'.$name.'" id="'.$id.'" class="google-fw" autocomplete="off">';
	$fonts = get_featured_google_fonts();
	if(isset($fonts[$currfont])) {
		$font = $fonts[$currfont];
		$html.='<option value="">'. __( "&mdash; Font Weight &mdash;", "featured-slider" ) .'</option>';
		foreach ( $font['font_weights'] as $key => $value ) :
			$html.='<option value="'.$value.'" '.selected( $current_value, $value, false ). '>'.$value.'</option>';
		endforeach;
	} else {
		$html.='<option value="">'. __( '&mdash; Font Weight &mdash;', 'featured-slider' ).'</option>';
	}
	$html.='</select>';
	return $html;
}
//google font subset 
function get_featured_google_font_subset_html($currfont, $subsetname, $subsetid, $current_value) {
	$html = '';
	$html.='<select name="'.$subsetname.'" id="'.$subsetid.'" class="google-fsubset" autocomplete="off" multiple>';
	$fonts = get_featured_google_fonts();
	//print_r($fonts[$currfont]);
	if(isset($fonts[$currfont])) {
		$font = $fonts[$currfont];
		//print_r($font['subsets']);
		foreach ( $font['subsets'] as $key => $value ) :
			if( is_array($current_value) && in_array( $value , $current_value )) {
				$sel = 'selected="selected"';
			} else {
				$sel = '';
			}
			$html.='<option value="'.$value.'" '.$sel. '>'.$value.'</option>';
		endforeach;
	} else {
		$html.='<option value="">'. __( '&mdash; Font Subset &mdash;', 'featured-slider' ).'</option>';
	}
	$html.='</select>';
	return $html;
}
//default fonts
function get_featured_default_fonts($name, $id, $class, $current_value) {
	$default_fonts='<select name="'.$name.'" id="'.$id.'" class="'.$class.'">
	<option value="Arial,Helvetica,sans-serif"'. selected( $current_value, "Arial,Helvetica,sans-serif", false ); 
	$default_fonts.='>Arial,Helvetica,sans-serif</option>
	<option value="Verdana,Geneva,sans-serif"'. selected( $current_value, "Verdana,Geneva,sans-serif", false ); 
	$default_fonts.='>Verdana,Geneva,sans-serif</option>
	<option value="Tahoma,Geneva,sans-serif"'. selected( $current_value, "Tahoma,Geneva,sans-serif", false );
	$default_fonts.='>Tahoma,Geneva,sans-serif</option>
	<option value="Trebuchet MS,sans-serif"'. selected( $current_value, "Trebuchet MS,sans-serif", false ); 
	$default_fonts.='>Trebuchet MS,sans-serif</option>
	<option value="\'Century Gothic\',\'Avant Garde\',sans-serif"'. selected( $current_value, "'Century Gothic','Avant Garde',sans-serif", false ); 
	$default_fonts.='>\'Century Gothic\',\'Avant Garde\',sans-serif</option>
	<option value="\'Arial Narrow\',sans-serif"'. selected( $current_value, "'Arial Narrow',sans-serif", false ); 
	$default_fonts.='>\'Arial Narrow\',sans-serif</option>
	<option value="\'Arial Black\',sans-serif"'. selected( $current_value, "'Arial Black',sans-serif", false ); 
	$default_fonts.='>\'Arial Black\',sans-serif</option>
	<option value="\'Gills Sans MT\',\'Gills Sans\',sans-serif"'. selected( $current_value, "'Gills Sans MT','Gills Sans',sans-serif", false ); 
	$default_fonts.='>\'Gills Sans MT\',\'Gills Sans\',sans-serif</option>
	<option value="\'Times New Roman\',Times,serif"'. selected( $current_value, "'Times New Roman',Times,serif", false ); 
	$default_fonts.='>\'Times New Roman\',Times,serif</option>
	<option value="Georgia,serif"'. selected( $current_value, "Georgia,serif", false ); 
	$default_fonts.='>Georgia,serif</option>
	<option value="Garamond,serif"'. selected( $current_value, "Garamond,serif", false ); 
	$default_fonts.='>Garamond,serif</option>
	<option value="\'Century Schoolbook\',\'New Century Schoolbook\',serif"'. selected( $current_value, "'Century Schoolbook','New Century Schoolbook',serif", false ); 
	$default_fonts.='>\'Century Schoolbook\',\'New Century Schoolbook\',serif</option>
	<option value="\'Bookman Old Style\',Bookman,serif"'. selected( $current_value, "'Bookman Old Style',Bookman,serif", false ); 
	$default_fonts.='>\'Bookman Old Style\',Bookman,serif</option>
	<option value="\'Comic Sans MS\',cursive"'. selected( $current_value, "'Comic Sans MS',cursive", false ); 
	$default_fonts.='>\'Comic Sans MS\',cursive</option>
	<option value="\'Courier New\',Courier,monospace"'. selected( $current_value, "'Courier New',Courier,monospace", false ); 
	$default_fonts.='>\'Courier New\',Courier,monospace</option>
	<option value="\'Copperplate Gothic Bold\',Copperplate,fantasy"'. selected( $current_value, "'Copperplate Gothic Bold',Copperplate,fantasy", false ); 
	$default_fonts.='>\'Copperplate Gothic Bold\',Copperplate,fantasy</option>
	<option value="Impact,fantasy"'. selected( $current_value, "Impact,fantasy", false ); 
	$default_fonts.='>Impact,fantasy</option>
	<option value="sans-serif"'. selected( $current_value, "sans-serif", false ); 
	$default_fonts.='>sans-serif</option>
	<option value="serif"'. selected( $current_value, "serif", false ); 
	$default_fonts.='>serif</option>
	<option value="cursive"'. selected( $current_value, "cursive", false ); 
	$default_fonts.='>cursive</option>
	<option value="monospace"'. selected( $current_value, "monospace", false ); 
	$default_fonts.='>monospace</option>
	<option value="fantasy"'. selected( $current_value, "fantasy", false ); 
	$default_fonts.='>fantasy</option>
	</select>';
	return $default_fonts;
}
function get_featured_custom_font_html($name, $id, $current_value) {
	$html = '';
	$html.='<input type="text" name="'.$name.'" id="'.$id.'" value="'.$current_value.'">';
	return $html;
}
