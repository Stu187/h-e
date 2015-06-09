jQuery(function () {
  jQuery('.moreInfo').each(function () {
    // options
    var distance = 10;
    var time = 250;
    var hideDelay = 200;

    var hideDelayTimer = null;

    // tracker
    var beingShown = false;
    var shown = false;
    
    var trigger = jQuery('.trigger', this);
    var tooltip = jQuery('.tooltip', this).css('opacity', 0);
	
    // set the mouseover and mouseout on both element
    jQuery([trigger.get(0), tooltip.get(0)]).mouseover(function () {
      // stops the hide event if we move from the trigger to the tooltip element
      if (hideDelayTimer) clearTimeout(hideDelayTimer);

      // don't trigger the animation again if we're being shown, or already visible
      if (beingShown || shown) {
        return;
      } else {
        beingShown = true;

        // reset position of tooltip box
        tooltip.css({
          display: 'block' // brings the tooltip back in to view
        })

        // (we're using chaining on the tooltip) now animate it's opacity and position
        .animate({
          /*top: '-=' + distance + 'px',*/
          opacity: 1
        }, time, 'swing', function() {
          // once the animation is complete, set the tracker variables
          beingShown = false;
          shown = true;
        });
      }
    }).mouseout(function () {
      // reset the timer if we get fired again - avoids double animations
      if (hideDelayTimer) clearTimeout(hideDelayTimer);
      
      // store the timer so that it can be cleared in the mouseover if required
      hideDelayTimer = setTimeout(function () {
        hideDelayTimer = null;
        tooltip.animate({
          /*top: '-=' + distance + 'px',*/
          opacity: 0
        }, time, 'swing', function () {
          // once the animate is complete, set the tracker variables
          shown = false;
          // hide the tooltip entirely after the effect (opacity alone doesn't do the job)
          tooltip.css('display', 'none');
        });
      }, hideDelay);
    });
  });
	/* Added for validations - Start */	
	jQuery('#featured_slider_form').submit(function(event) { 
		var offset=jQuery("#featured_slider_offset").val();
		if((offset=='' || offset < 0 || isNaN(offset)) && offset != undefined) {
			alert("Offset should be a number greater than 0!"); 
			jQuery("#featured_slider_offset").addClass('error');
			jQuery("html,body").animate({scrollTop:jQuery('#featured_slider_offset').offset().top-50}, 600);
			return false;
		}
		var sliderType = jQuery("#featured_slider_preview").val();
		if( sliderType == 0 ) { 
			var sid=jQuery("#featured_slider_id").val();
			if(( sid == "" || sid == 0 ) && sid != undefined) {
				alert("Please Select The Slider");
				jQuery("#featured_slider_id").addClass('ps-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("#featured_slider_id").offset().top-50}, 600);
				return false;
			}
		} if( sliderType == 1 ) { 
			var catg_slug=jQuery("#featured_slider_catslug").val();
			if( catg_slug == "" && catg_slug != undefined ) {
				alert("Select the category whose posts you want to show!"); 
				jQuery("#featured_slider_catslug").addClass('ps-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("#featured_slider_catslug").offset().top-50}, 600);
				return false;
			}
		} if( sliderType == 3 ) { 
			var wootype = jQuery("#woo_slider_preview").val();
			if(( wootype == "upsells" || wootype == "crosssells" || wootype == "grouped") && wootype != undefined ) {
				var product_id = jQuery("#product_id").val();
				if(product_id == '') {
					alert("Please Enter the Product");
					jQuery("#products").addClass('ps-create-error');
					jQuery("html,body").animate({scrollTop:jQuery("#products").offset().top-50}, 600);
					return false;
				}
			}
		} if( sliderType == 4 ) { 
			var ecomType =jQuery("#ecom_slider_preview").val();
			if(ecomType == 1 && ecomType != undefined) {
				var catg_slug=jQuery("#featured_slider_ecom_catslug").val();
				if( catg_slug == "" ) {
					alert("Please Select The Category"); 
					jQuery("#featured_slider_ecom_catslug").addClass('ps-create-error');
					jQuery("html,body").animate({scrollTop:jQuery("#featured_slider_ecom_catslug").offset().top-50}, 600);
					return false;
				}	
			}
		} if( sliderType == 7 ) { 
			var postType =jQuery("#featured_taxonomy_posttype").val();
			if(postType == "" && postType != undefined) {
				alert("Please Select The Post Type"); 
				jQuery("#featured_taxonomy_posttype").addClass('ps-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("#featured_taxonomy_posttype").offset().top-50}, 600);
				return false;
			}
			var taxo =jQuery("#featured_taxonomy").val();
			if(taxo == "" && taxo != undefined) {
				alert("Please Select The Taxonomy"); 
				jQuery("#featured_taxonomy").addClass('ps-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("#featured_taxonomy").offset().top-50}, 600);
				return false;
			}
			var term =jQuery("#featured_taxonomy_term option:selected").length;
			if(term < 1) {
				alert("Please Select The Term");
				jQuery("#featured_taxonomy_term").addClass('ps-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("#featured_taxonomy_term").offset().top-50}, 600);
				return false;
			}
		} if( sliderType == 8 ) { 
			var rssfeedurl =jQuery("#featured_rssfeed_feedurl").val();
			if( rssfeedurl == "" && rssfeedurl != undefined ) {
				alert("Please Enter the Feed Url"); 
				jQuery("#featured_rssfeed_feedurl").addClass('ps-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("#featured_rssfeed_feedurl").offset().top-50}, 600);
				return false;
			}
		} if( sliderType == 9 ) { 
			var attachId =jQuery("#featured_postattch_id").val();
			if(( attachId == "" || attachId < 0 || isNaN(attachId) && attachId != undefined)) {
				alert("Please Enter the Post Id "); 
				jQuery("#featured_postattch_id").addClass('ps-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("#featured_postattch_id").offset().top-50}, 600);
				return false;
			}
		} if( sliderType == 10 ) { 
			var nggId =jQuery("#featured_nextgen_galleryid").val();
			if(( nggId == "" || nggId < 0 || isNaN(nggId)) && nggId != undefined) {
				alert("Please Enter the NextGen Gallery ID"); 
				jQuery("#featured_nextgen_galleryid").addClass('ps-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("#featured_nextgen_galleryid").offset().top-50}, 600);
				return false;
			}
		}
		var speed=jQuery("#featured_slider_speed").val();
		if((speed=='' || speed <= 0 || isNaN(speed)) && speed != undefined) {
				alert("Speed of Transition should be a number greater than 0!"); 
				jQuery("#featured_slider_speed").addClass('error');
				jQuery("html,body").animate({scrollTop:jQuery('#featured_slider_speed').offset().top-50}, 600);
				return false;
			}	
		var time=jQuery("#featured_slider_time").val();
		if((time=='' || time <= 0 || isNaN(time) && time != undefined)) {
				alert("Time between Transitions should be a number greater than 0!"); 
				jQuery("#featured_slider_time").addClass('error');
				jQuery("html,body").animate({scrollTop:jQuery('#featured_slider_time').offset().top-50}, 600);
				return false;
			}
		var posts=jQuery("#featured_slider_no_posts").val();
		if((posts=='' || posts <= 0 || isNaN(posts) && posts != undefined)) {
				alert("Number of Posts in the Featured Slider should be a number greater than 0!"); 
				jQuery("#featured_slider_no_posts").addClass('error');
				jQuery("html,body").animate({scrollTop:jQuery('#featured_slider_no_posts').offset().top-50}, 600);
				return false;
			}
		var width=jQuery("#featured_slider_width").val();
		if((width=='' || width <= 0 || isNaN(width) && width != undefined)) {
				alert("Maximum Slider Width should be a number greater than or equal to 0!"); 
				jQuery("#featured_slider_width").addClass('error');
				jQuery("html,body").animate({scrollTop:jQuery('#featured_slider_width').offset().top-50}, 600);
				return false;
			}
		var lswidth=jQuery("#featured_slider_lswidth").val();
		if((lswidth=='' || lswidth < 50 || lswidth >100 || isNaN(width) && width != undefined)) {
				alert("Large Slide Width should be a number greater than 50 and less than 100!"); 
				jQuery("#featured_slider_lswidth").addClass('error');
				jQuery("html,body").animate({scrollTop:jQuery('#featured_slider_lswidth').offset().top-50}, 600);
				return false;
			}
		var height=jQuery("#featured_slider_height").val();
		if((height=='' || height <= 0 || isNaN(height) && height != undefined)) {
				alert("Maximum Slider Height should be a number greater than 0!"); 
				jQuery("#featured_slider_height").addClass('error');
				jQuery("html,body").animate({scrollTop:jQuery('#featured_slider_height').offset().top-50}, 600);
				return false;
			}
		var nav_width=jQuery("#featured_slider_nav_w").val();
		if((nav_width=='' || nav_width <= 0 || isNaN(nav_width) && nav_width != undefined)) {
				alert("Navigation arrow width should be greater than 16!"); 
				jQuery("#featured_slider_nav_w").addClass('error');
				jQuery("html,body").animate({scrollTop:jQuery('#featured_slider_nav_w').offset().top-50}, 600);
				return false;
			}
		var nav_height=jQuery("#featured_slider_nav_h").val();
		if((nav_height=='' || nav_height <= 0 || isNaN(nav_height) && nav_height != undefined)) {
				alert("Navigation arrow height should be greater than 16!"); 
				jQuery("#featured_slider_nav_h").addClass('error');
				jQuery("html,body").animate({scrollTop:jQuery('#featured_slider_nav_h').offset().top-50}, 600);
				return false;
			}
		//for Quick embed shortcode popup
		var slider_id = jQuery("#featured_slider_id").val(),	
		    hiddensliderid=jQuery("#hidden_sliderid").val(),		
		    slider_catslug=jQuery("#featured_slider_catslug").val(),
		    hiddencatslug=jQuery("#hidden_category").val(),
		    prev=jQuery("#featured_slider_preview").val(),
		    hiddenpreview=jQuery("#hidden_preview").val(),
		    new_save=jQuery("#oldnew").val();
		if(prev=='1' && slider_catslug=='') {
			alert("Select the category whose posts you want to show!"); 
			jQuery("#featured_slider_catslug").addClass('error');
			jQuery("html,body").animate({scrollTop:jQuery('#featured_slider_catslug').offset().top-50}, 600);
			return false;
		}
		if(prev=='0') {
			if(slider_id=='' || isNaN(slider_id) || slider_id<=0){
				alert("Select the slider name!"); 
				jQuery("#featured_slider_id").addClass('error');
				jQuery("html,body").animate({scrollTop:jQuery('#featured_slider_id').offset().top-50}, 600);
				return false;
			}
		}
		if(hiddenpreview != prev || new_save=='1' || slider_id != hiddensliderid || slider_catslug != hiddencatslug ) jQuery('#featuredpopup').val("1");					
		else jQuery('#featuredpopup').val("0");	
	});
	/* Added for validations - end */
       
       /* Added for slider sub type preview */

	var ecompreview=jQuery("#ecom_slider_preview").val();
	if(ecompreview=='0') {
		jQuery("#featured_slider_form .form-table .featured_ecom_catg").hide();
	}	
	else if(ecompreview=='1'){
		jQuery("#featured_slider_form .form-table .featured_ecom_catg").css("display","block");
	}
});
/* Added for slider sub type preview */
function catgtype(catg_type){
	if(catg_type=='0') {
		jQuery(".featured_catg").hide();
	}	
	else if(catg_type=='1') { 
		if(jQuery(".featured_catg").hasClass("featured-row") == true ) jQuery(".featured_catg").css("display","table-row");
		else jQuery(".featured_catg").css("display","block");
	}
}
function ecomtype(ecom_type) {
	if(ecom_type=='0') {
		jQuery(".featured_ecom_catg").hide();
	}	
	else if(ecom_type=='1') {
		jQuery(".featured_ecom_catg").css("display","block");
	}
}
jQuery(document).ready(function(){
	/* START - JQuery for AJAX Settings tab */
	// For on ready
	var pageCheck = jQuery("input[name='hidden_urlpage']").val();
	if(pageCheck != undefined) {
		var flag = '0';
		var activeIdx = jQuery( "#featured_activetab, .featured_activetab" ).val();
		jQuery(".settings-tab").removeClass("tab-active");
		jQuery(".settings-tab:eq("+activeIdx+")").addClass("tab-active");
		var cntr = jQuery("input[name='featured-hiddencntr']").val();
		var tab = jQuery(".tab-active a").attr("id");
		var settings_nonce = jQuery("#featured-settings-nonce").val();
		var data = {
			'action': 'featured_tab_contents',
			'cntr':cntr,
			'tab':tab,
			'settings_nonce':settings_nonce
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery(".featured-tabs-content").html(response);
		}).always( function() { 
			var cnxt=jQuery(".featured-tabs-content");
		   	bindSettingsBehaviour(cnxt);
		});
	}
	jQuery(".settings-tab a").click(function() {
		var proceed = true;
		if(flag == 1) {
			 var agree=confirm("The changes you made will be lost if you navigate away from this tab. Please Save the Settings and move on");
			if (agree)
				proceed = true ;
			else
				proceed = false ;
		}
		if(proceed == true) {
			flag = 0;
			var activeIdx = jQuery(".settings-tab a").index(this);
			jQuery( "#featured_activetab, .featured_activetab" ).val(activeIdx);
			jQuery(".settings-tab").removeClass("tab-active");
			jQuery(this).parent(".settings-tab").addClass("tab-active");
			var cntr = jQuery("input[name='featured-hiddencntr']").val();
			var tab = jQuery(this).attr("id");
			var settings_nonce = jQuery("#featured-settings-nonce").val();
			var data = {
				'action': 'featured_tab_contents',
				'cntr':cntr,
				'tab':tab,
				'settings_nonce':settings_nonce
			};
			jQuery.post(ajaxurl, data, function(response) {
				jQuery(".featured-tabs-content").html(response);
			}).always( function() { 
				var cnxt=jQuery(".featured-tabs-content");
			   	bindSettingsBehaviour(cnxt);
			});
		}
		return false;
		});
		/* Easy Bulder AJAX Settings Load */
		if(pageCheck != undefined && pageCheck == 'featured-slider-easy-builder') {
			var cntr = jQuery("input[name='featured-hiddencntr']").val();
			var tab = jQuery("#featured_active_accordion").val();
			var eb_settings_nonce = jQuery("#featured-eb-settings-nonce").val();
			var data = {
				'action': 'featured_eb_settings',
				'cntr':cntr,
				'tab':tab,
				'eb_settings_nonce':eb_settings_nonce
			};
			jQuery.post(ajaxurl, data, function(response) {
				jQuery(".featured-eb-"+tab).html(response);
			}).always( function() { 
				var cnxt=jQuery(".featured-eb-"+tab);
			   	bindSettingsBehaviour(cnxt);
			});
		}

		jQuery('.featured-right-accordion').click(function() {
				var currAccordion = jQuery(this);
		setTimeout(function() {
			if(currAccordion.hasClass("featured-right-open")) {
				var id = currAccordion.attr('id');
				jQuery("#featured_active_accordion").val(id);
			}
		}, 2000);
		var cntr = jQuery("input[name='featured-hiddencntr']").val();
		var tab = jQuery(this).attr("id");
		var eb_settings_nonce = jQuery("#featured-eb-settings-nonce").val();
		var data = {
			'action': 'featured_eb_settings',
			'cntr':cntr,
			'tab':tab,
			'eb_settings_nonce':eb_settings_nonce
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery(".featured-eb-"+tab).html(response);
		}).always( function() { 
			var cnxt=jQuery(".featured-eb-"+tab);
		   	bindSettingsBehaviour(cnxt);
		});
	});
	var bindSettingsBehaviour = function(scope){
		/* Checke fields are changed on content tab or not */
		jQuery(".featured-settings-form *").change(function() {
			flag = '1';
		});
		/* active sections - start */
		var closed_sections = jQuery("#featured_closedsections").val();
		var pluginUrl = jQuery("#featured_pluginurl").val()+'/';
		if(closed_sections != undefined) {
			var closedsecarr = closed_sections.split(",");
			 jQuery(".sub-heading").each(function () {
				if( jQuery.inArray(jQuery(this).text(),closedsecarr) != -1 ) {
					jQuery(this).addClass("closed");
					var wrap=jQuery(this).parent('.toggle_settings');
					var imgclass=wrap.find(".toggle_img");
					imgclass.attr("src", imgclass.attr("src").replace(pluginUrl+"images/close.png", pluginUrl+"images/info.png"));
				}
			});
			/* active sections - end */
			var wrap=jQuery(".closed").parent('.toggle_settings'),
			tabcontent=wrap.find("p, table.form-table, code, div.settingsdiv, div.yellowdiv");
			tabcontent.toggle();
		}
		/* Addede for settings tab collapse and expand - start */
		jQuery(".sub-heading", scope).on("click", function(){
			var wrap=jQuery(this).parent('.toggle_settings'),
			tabcontent=wrap.find("p, table.form-table, code, div.settingsdiv, div.yellowdiv");
			/* active sections - start */
			jQuery(this).toggleClass("closed");
			var sectionstr = jQuery("#featured_closedsections");
			if(jQuery(this).hasClass("closed")) {
				if(sectionstr.val() !='' ) {
					sectionstr.val(sectionstr.val()+','+jQuery(this).text());	
				} else {
					sectionstr.val(jQuery(this).text());
				}
			} else {
				var res;
				res = sectionstr.val().replace(jQuery(this).text()+",", "");
				res = res.replace(","+jQuery(this).text(), "");
				res = res.replace(jQuery(this).text(), "");
				sectionstr.val(res);
			}
			/* active sections - end */
			tabcontent.toggle();
			//jQuery(".tooltip1").css('display','none');
			var imgclass=wrap.find(".toggle_img");
			if (tabcontent.css('display') == 'none') {
				imgclass.attr("src", imgclass.attr("src").replace(pluginUrl+"images/close.png", pluginUrl+"images/info.png"));
			} else {
				imgclass.attr("src", imgclass.attr("src").replace(pluginUrl+"images/info.png", pluginUrl+"images/close.png"));
			}
		});
		/* Added for settings tab collapse and expand - end */	
		/**
		* -------------------------------------------------------------------------------------
		* JS for Settings Panel Preview Params AJAX Load
		* -------------------------------------------------------------------------------------
		**/
		var featured_preview = jQuery("#featured_slider_preview").val();
		if(featured_preview != undefined) {
			var featured_data = {};
			var settings_nonce = jQuery("#featured-settings-nonce").val();
			featured_data['action'] = 'featured_preview_params';
			featured_data['slider_type'] = featured_preview;
			featured_data['cntr'] = jQuery(".featured-hiddencntr").val();
			featured_data['settings_nonce'] = settings_nonce;
			jQuery.post(ajaxurl, featured_data, function(response) {
				jQuery(".featured_slider_params").html(response);
			}).always(function() {
				var cnxt=jQuery(".featured_slider_params");
				bindPreviewParams(cnxt);
			});
		} 
		jQuery("#featured_slider_preview", scope).change(function() {
			jQuery(".featured_slider_params").empty();
			jQuery(".featured_slider_params").append('<td class="featured-loader" colspan="2" style="background: url('+featuredLoader+') 50% 50% ;background-repeat: no-repeat;width: 100px;height: 20px;margin: 15px auto;"></td>');
			var settings_nonce = jQuery("#featured-settings-nonce").val();
			var data = {};
			data['action'] = 'featured_preview_params';
			data['slider_type'] = jQuery(this).val();
			data['cntr'] = jQuery(".featured-hiddencntr").val();
			data['settings_nonce'] = settings_nonce;
			jQuery.post(ajaxurl, data, function(response) {
				jQuery(".featured_slider_params").find("featured-loader").remove();
				jQuery(".featured_slider_params").html(response);
			}).always(function() {
				var cnxt=jQuery(".featured_slider_params");
				bindPreviewParams(cnxt);
			});
			return false;
		});
	
		/* This function loads second level of fonts on load depending on first level of fonts - start */
		jQuery( ".main-font", scope ).each(function() {
			var font_type = jQuery(this).val();
			var currpage = jQuery(".featured_urlpage").val();
			var currcounter = jQuery(".featured-hiddencntr").val();
			var nm;
			if(font_type == 'regular') nm = jQuery(this).siblings(".ftype_rname").val();
			if(font_type == 'google') nm = jQuery(this).siblings(".ftype_gname").val();
			if(font_type == 'custom') nm = jQuery(this).siblings(".ftype_cname").val();
			var parentid = jQuery(this).attr('id');
			var google_fonts = jQuery("#featured-google-nonce").val();
			var data = {
				'action': 'featured_load_fontsdiv',
				'font_type': font_type,
				'parentid': parentid,
				'nm':nm,
				'currpage' : currpage,
				'currcounter' : currcounter,
				'google_fonts':google_fonts
			};
			jQuery.post(ajaxurl, data, function(response) {
				jQuery("#"+data['parentid']).parents(".settings-tbl").find(".load-fontdiv").html(response);
				if( data['font_type'] == 'google' ) {
					jQuery("#"+data['parentid']).parents(".settings-tbl").find(".font-style").css('display','none');
				}
				else {
					var fontStyle = jQuery("#"+data['parentid']).parents(".settings-tbl").find(".font-style");
					if(fontStyle.hasClass("ebs-row")) fontStyle.css('display','block');
					else fontStyle.css('display','table-row');
				}
			}).always( function() { 
				var cnxt=jQuery("#"+data['parentid']).parents(".settings-tbl").find(".load-fontdiv");
			   	bindgoogleBehaviour(cnxt);
			});
		});
		/* This function loads second level of fonts on load depending on first level of fonts - end */

		/* This function loads second level of fonts on change of first level of fonts - start */
		jQuery(".main-font", scope).change(function(){
			var font_type = jQuery(this).val();
			var currpage = jQuery(".featured_urlpage").val();
			var currcounter = jQuery(".featured-hiddencntr").val();
			var nm;
			if(font_type == 'regular') nm = jQuery(this).siblings(".ftype_rname").val();
			if(font_type == 'google') nm = jQuery(this).siblings(".ftype_gname").val();
			if(font_type == 'custom') nm = jQuery(this).siblings(".ftype_cname").val();
			var parentid = jQuery(this).attr('id');
			var google_fonts = jQuery("#featured-google-nonce").val();
			var data = {
				'action': 'featured_load_fontsdiv',
				'font_type': font_type,
				'parentid': parentid,
				'nm':nm,
				'currpage' : currpage,
				'currcounter' : currcounter,
				'google_fonts':google_fonts
			};
			jQuery.post(ajaxurl, data, function(response) {
				jQuery("#"+data['parentid']).parents(".settings-tbl").find(".load-fontdiv").html(response);
				if( data['font_type'] == 'google' ) {
					jQuery("#"+data['parentid']).parents(".settings-tbl").find(".font-style").css('display','none');
				}
				else {
					var fontStyle = jQuery("#"+data['parentid']).parents(".settings-tbl").find(".font-style");
					if(fontStyle.hasClass("ebs-row")) fontStyle.css('display','block');
					else fontStyle.css('display','table-row');
				}
			}).always( function() { 
				var cnxt=jQuery("#"+data['parentid']).parents(".settings-tbl").find(".load-fontdiv");
			   	bindgoogleBehaviour(cnxt);
			
			});
		});
		/* Default Image Setting - Upload */
		jQuery('.featured-upload-default').on("click",function(event) {
			var frame;
			event.preventDefault();
			// If the media frame already exists, reopen it.
			if ( frame ) {
				frame.open();
				return;
			}
			// Create the media frame.
			frame = wp.media({
				title: 'Upload/Select Images',
				multiple: false,
				button: {
					text: 'Set Default Image',
					close: false
				}
			});
			frame.on( 'select', function() {
				// Grab the selected attachment.
				var attachments = frame.state().get('selection').toArray();
				frame.close();
				if(attachments.length>0){
					var imgurl = attachments[0].attributes.url;
					jQuery("#default-img").attr("src",imgurl);
					jQuery("#featured_slider_default_image").val(imgurl);
				}
			});
			// Finally, open the modal.
			frame.open();
			return false;
		});
		jQuery('.featured-reset-default').on("click",function() {
			var imgurl = jQuery("#default-image-url").val();
			jQuery("#default-img").attr("src",imgurl);
			jQuery("#featured_slider_default_image").val(imgurl);
			return false;
		});
		// Tooltip
		jQuery('.havemoreinfo').hover(
			function(e) {
				jQuery(this).next('.moreInfo').find('.tooltip1').fadeIn(400);
		
			},
			function(e) {
				jQuery(this).next('.moreInfo').find('.tooltip1').fadeOut( "fast" );
		});
		jQuery( ".featured_pphoto" ).change( function() {
			if(jQuery(this).prop('checked') == true ) {
		    		 jQuery( ".featured_slider_lbox_type" ).slideDown( "slow" );
		    	}
		    	else  jQuery( ".featured_slider_lbox_type" ).slideUp( "slow" );
		});
		jQuery(".eb-toggle-round").click(function() {
			if(jQuery(this).prop("checked")==true) {
				jQuery(this).prev('.hidden_check').val(1);
			} else {
				jQuery(this).prev('.hidden_check').val(0);
			}
		});
		// for color picker
		jQuery('.wp-color-picker-field').wpColorPicker();
		/* Show or hide settings field as slider type changes */
		var featured_preview = jQuery("#hidden_preview").val();
		if(featured_preview=='3' || featured_preview=='4') {
			jQuery( "#postcontent" ).hide();
			jQuery( "#navmeta" ).hide();
			jQuery( "#event_manager" ).hide();
			jQuery( "#featuredwoo" ).show();
		} else if(featured_preview=='5' || featured_preview=='6') {
			jQuery( "#navmeta" ).hide();
			jQuery( "#featuredwoo" ).hide();
			jQuery( "#event_manager" ).show();
		} else {
			jQuery( "#featuredwoo" ).hide();
			jQuery( "#event_manager" ).hide();
			jQuery( "#postcontent" ).show();
			jQuery( "#navmeta" ).show();
		}
	} // bind ends

	/* This function loads second level of google fonts on change of first level of google fonts - start */
	var bindgoogleBehaviour = function(scope) {
		jQuery(".google-fonts", scope).change(function(){
			var font = jQuery(this).val();
			var parentid = jQuery(this).attr('id');
			var fname = jQuery(this).parents(".settings-tbl").find(".google-fw").attr('name');
			var fid = jQuery(this).parents(".settings-tbl").find(".google-fw").attr('id');
			var fsubsetnm = jQuery(this).parents(".settings-tbl").find(".google-fsubset").attr('name');
			var fsubsetid = jQuery(this).parents(".settings-tbl").find(".google-fsubset").attr('id');
			var google_fonts = jQuery("#featured-google-nonce").val();
			var data = {
				'action': 'featured_disp_gfweight',
				'font': font,
				'fname': fname,
				'fid': fid,
				'parentid': parentid,
				'fsubsetnm': fsubsetnm,
				'fsubsetid': fsubsetid,
				'google_fonts':google_fonts
			};
			jQuery.post(ajaxurl, data, function(response) {
				var res = JSON.parse(response);
				jQuery("#"+data['parentid']).parents(".settings-tbl").find(".google-fontsweight").html(res[0]);
				jQuery("#"+data['parentid']).parents(".settings-tbl").find(".google-fontsubset").html(res[1]);
			});
		});
	}
	// DataTables Call on manage Sliders page
	if(jQuery("#featured_sliders_create").hasClass("wrap")) {
		jQuery("#featured-manage-slider").DataTable({
			responsive: true	
		});
	}
	/* Vimeo Slider */
	jQuery(".vimeo-type").change(function() {
		var val = jQuery(this).val();
		if(val == "channel") {
			jQuery("#vimeo-lb").text("Channel Name");
		} else if(val == "album") {
			jQuery("#vimeo-lb").text("ID");
		} 
	});
	var val = jQuery(".vimeo-type").val();
	if(val == "channel") {
		jQuery("#vimeo-lb").text("Channel Name");
	} else if(val == "album") {
		jQuery("#vimeo-lb").text("Album ID");
	}
	/* Flicker Slider */
	jQuery(".flickr-type").change(function() {
		var val = jQuery(this).val();
		if(val == "user") {
			jQuery("#flickr-lb").text("User ID");
		} else if(val == "album") {
			jQuery("#flickr-lb").text("Album ID");
		} 
	});
	var val = jQuery(".flickr-type").val();
	if(val == "user") {
		jQuery("#flickr-lb").text("User ID");
	} else if(val == "album") {
		jQuery("#flickr-lb").text("Album ID");
	} 
	/* Form Validations */
	jQuery('.featured-validate').submit(function(event) { 
		if(jQuery("#new_slider_name").val() == "" ) {
			alert("Please Enter the Slider Name");
			jQuery("#new_slider_name").addClass('featured-create-error');
			jQuery("html,body").animate({scrollTop:jQuery('#new_slider_name').offset().top-50}, 600);
			return false;
		}
		var offset=jQuery("input[name='offset']").val();
		if(offset < 0 || isNaN(offset)) {
			alert("Offset should be a number greater than 0!"); 
			jQuery("input[name='offset']").addClass('featured-create-error');
			jQuery("html,body").animate({scrollTop:jQuery("input[name='offset']").offset().top-50}, 600);
			return false;
		}
		var sliderType = jQuery("input[name='slider-type']").val();
		if( sliderType == 1 ) { 
			var catg_slug=jQuery("select[name='catg_slug']").val();
			if( catg_slug == "" ) {
				alert("Please Select The Category");
				jQuery("select[name='catg_slug']").addClass('featured-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("select[name='catg_slug']").offset().top-50}, 600);
				return false;
			}
		}  else if( sliderType == 3 ) { 
			var wootype = jQuery("#woo-slider-type").val();
			if( wootype == "upsells" || wootype == "crosssells" || wootype == "grouped" ) {
				var product_id = jQuery("#product_id").val();
				if(product_id == '') {
					alert("Please Enter the Product");
					jQuery("#products").addClass('featured-create-error');
					jQuery("html,body").animate({scrollTop:jQuery("#products").offset().top-50}, 600);
					return false;
				}
			}
		} else if( sliderType == 4 ) { 
			var ecomType =jQuery("select[name='ecom_slider_type']").val();
			if(ecomType == 1) {
				var catg_slug=jQuery("select[name='ecom-catg']").val();
				if( catg_slug == "" ) {
					alert("Please Select The Category"); 
					jQuery("select[name='ecom-catg']").addClass('featured-create-error');
					jQuery("html,body").animate({scrollTop:jQuery("select[name='ecom-catg']").offset().top-50}, 600);
					return false;
				}	
			}
		} else if( sliderType == 7 ) { 
			var postType =jQuery("select[name='taxo_posttype']").val();
			if(postType == "") {
				alert("Please Select The Post Type"); 
				jQuery("select[name='taxo_posttype']").addClass('featured-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("select[name='taxo_posttype']").offset().top-50}, 600);
				return false;
			}
			var taxo =jQuery("select[name='taxonomy_name']").val();
			if(taxo == "") {
				alert("Please Select The Taxonomy"); 
				jQuery("select[name='taxonomy_name']").addClass('featured-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("select[name='taxonomy_name']").offset().top-50}, 600);
				return false;
			}
			var term =jQuery("#featured_taxonomy_term option:selected").length;
			if(term < 1) {
				alert("Please Select The Term");
				jQuery("#featured_taxonomy_term").addClass('featured-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("#featured_taxonomy_term").offset().top-50}, 600);
				return false;
			}
		} else if( sliderType == 8 ) { 
			var rssfeedurl =jQuery("input[name='rssfeedurl']").val();
			if( rssfeedurl == "" ) {
				alert("Please Enter the Feed Url"); 
				jQuery("input[name='rssfeedurl']").addClass('featured-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("input[name='rssfeedurl']").offset().top-50}, 600);
				return false;
			}
		} else if( sliderType == 9 ) { 
			var attachId =jQuery("input[name='postattch-id']").val();
			if( attachId == "" || attachId < 0 || isNaN(attachId)) {
				alert("Please Enter the Post Id "); 
				jQuery("input[name='postattch-id']").addClass('featured-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("input[name='postattch-id']").offset().top-50}, 600);
				return false;
			}
		} else if( sliderType == 10 ) { 
			var nggId =jQuery("select[name='nextgen-id']").val();
			if( nggId == "" || nggId < 0 || isNaN(nggId)) {
				alert("Please Enter the NextGen Gallery ID"); 
				jQuery("select[name='nextgen-id']").addClass('featured-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("select[name='nextgen-id']").offset().top-50}, 600);
				return false;
			}
		} else if( sliderType == 11 ) { 
			var ytID =jQuery("input[name='yt-playlist-id']").val();
			if( ytID == "" ) {
				alert("Please Enter the Playlist ID");
				jQuery("input[name='yt-playlist-id']").addClass('featured-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("input[name='yt-playlist-id']").offset().top-50}, 600);
				return false;
			}
		} else if( sliderType == 12 ) { 
			var ytTerm =jQuery("input[name='yt-search-term']").val();
			if( ytTerm == "" ) {
				alert("Please Enter the Search Term");
				jQuery("input[name='yt-search-term']").addClass('featured-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("input[name='yt-search-term']").offset().top-50}, 600);
				return false;
			}
		} else if( sliderType == 13 ) { 
			var vimeoVal =jQuery("input[name='vimeo-val']").val();
			var vimeoType =jQuery("select[name='vimeo-type']").val();
			if( vimeoVal == "" ) {
				if(vimeoType == "channel" ) var msg = "Please Enter the Channel Name";
				else  var msg = "Please Enter the Album ID";
				alert(msg); 
				jQuery("input[name='vimeo-val']").addClass('featured-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("input[name='vimeo-val']").offset().top-50}, 600);
				return false;
			}
		}  else if( sliderType == 14 ) { 
			var fbUrl =jQuery("input[name='fb-pg-url']").val();
			var fbAlbum =jQuery("select[name='fb-album']").val();
			if( fbUrl == "" ) {
				alert("Please Enter the Page Url and Click on Connect Button"); 
				jQuery("input[name='fb-pg-url']").addClass('featured-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("input[name='fb-pg-url']").offset().top-50}, 600);
				return false;
			} else if( fbAlbum == undefined ) {
				alert("Please Click on Connect Button and Select Album");
				return false; 
			}
		} else if( sliderType == 15 ) { 
			var userName =jQuery("input[name='user-name']").val();
			if( userName == "" ) {
				alert("Please Enter the User Name"); 
				jQuery("input[name='user-name']").addClass('featured-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("input[name='user-name']").offset().top-50}, 600);
				return false;
			}
		} else if( sliderType == 16 ) { 
			var flId =jQuery("input[name='fl-id']").val();
			var flType =jQuery("select[name='flickr-type']").val();
			if( flId == "" ) {
				if(flType =="album") var msg = "Please Enter the Album ID";
				else  var msg = "Please Enter the User ID";
				alert(msg); 
				jQuery("input[name='fl-id']").addClass('featured-create-error');
				jQuery("html,body").animate({scrollTop:jQuery("input[name='fl-id']").offset().top-50}, 600);
				return false;
			}
		} else if( sliderType == 18 ) { 
			var feature =jQuery("select[name='feature']").val();
			if( feature == "user" || feature == "user_favorites" ) {
				var pxuser =jQuery("input[name='pxuser']").val();
				if(feature == "user" ) var msg = "Please Enter the User Name";
				else  var msg = "Please Enter the User Favorites Name";
				if(pxuser == "") {
					alert(msg); 
					jQuery("input[name='pxuser']").addClass('featured-create-error');
					jQuery("html,body").animate({scrollTop:jQuery("input[name='pxuser']").offset().top-50}, 600);
					return false;
				}
			}
		}
	
	});
	/* END - Form Validation */
	
	/* Taxonomyy Addon */
	var bindTaxBehaviors = function(scope) {
		jQuery("#featured_taxonomy",scope).change(function() { 
			var featured_slider_pg = jQuery("#featured-slider-nonce").val();
			var data = {};
			if(jQuery(this).hasClass("taxo-update") == true)
			data['update'] = 'true';
			data['preview'] = '';
			data['taxo'] = jQuery(this).val();
			data['action'] = 'featured_show_term';
			data['featured_slider_pg'] = featured_slider_pg;
			jQuery.post(ajaxurl, data, function(response) { 
				jQuery(".sh-term").fadeIn("slow");
				jQuery(".sh-term").html(response);
			});
			return false;
		});
	} 
	jQuery("#featured_taxonomy_posttype").change(function() {
		var featured_slider_pg = jQuery("#featured-slider-nonce").val();
		var data = {};
		if(jQuery(this).hasClass("taxo-update") == true)
		data['update'] = 'true';
		data['post_type'] = jQuery(this).val();
		data['action'] = 'featured_show_taxonomy';
		data['featured_slider_pg']=featured_slider_pg;
		jQuery.post(ajaxurl, data, function(response) {
			jQuery(".sh-taxo").html(response);
		}).always(function() {
			var cnxt=jQuery(".sh-taxo");
		   	bindTaxBehaviors(cnxt);
		});
		return false;
	});
	
	jQuery("#featured_taxonomy").change(function() {
		var featured_slider_pg = jQuery("#featured-slider-nonce").val();
		var data = {};
		if(jQuery(this).hasClass("taxo-update") == true)
		data['update'] = 'true';
		data['preview'] = '';
		data['taxo'] = jQuery(this).val();
		data['action'] = 'featured_show_term';
		data['featured_slider_pg'] = featured_slider_pg;
		jQuery.post(ajaxurl, data, function(response) { 
			jQuery(".sh-term").fadeIn("slow");
			jQuery(".sh-term").html(response);
		});
		return false;
	});
	/* Autocomplete JS for WooCommerce Slider */
	if(jQuery(".featured-validate input[name='slider-type']").val() == '3') {
		jQuery("#products").autocomplete({
			source: function( request, response ) {
			var featured_slider_pg = jQuery("#featured-slider-nonce").val();
			var data = {};
			data['type'] = jQuery("select[name='woo_slider_type']").val();
			data['term']=request.term;
			data['action']='featured_woo_product';
			data['featured_slider_pg'] = featured_slider_pg;
				jQuery.ajax({
					url: ajaxurl,
					data: data,
					method: "post",
					dataType: "json",
					success: function( data ) {
						if(data.length != 0 ) {
							response( jQuery.map( data.product, function( item ) {
								return {
								label: item.title,
								value: item.title,
								ID: item.ID
								}
							}));
						}
					}
				});
			},
			minLength:1,
			select: function(event,ui) {
				jQuery("#product_id").val(ui.item.ID);
			} 
		});
	}
	/* WooCommerece Show Product Id  Field on basis of slider type */
	jQuery("select[name='woo_slider_type']").change(function() {
		var sliderType = jQuery(this).val();
		if( sliderType != "recent" && sliderType != "external" ) {
			if(jQuery(".woo-product").hasClass("featured-row") ) jQuery(".woo-product").css("display","table-row");
			else jQuery(".woo-product").css("display","block");
		} else {
			jQuery(".woo-product").css("display","none");
		}
	});
	jQuery(".eb-toggle-round").click(function() {
			//alert(jQuery(this).prop("checked"));
			if(jQuery(this).prop("checked")==true) {
				jQuery(this).prev('.hidden_check').val(1);
			} else {
				jQuery(this).prev('.hidden_check').val(0);
			}
	});
	
	/**
	* -------------------------------------------------------------------------------------
	* JS for Ajax and custom slider
	* -------------------------------------------------------------------------------------
	**/
	var featuredLoader = jQuery("input[name='featured-loader']").val();
	var featuredSliderId = parseInt(jQuery("input[name='featured-sliderid']").val());

	/* Hide Footer Upgrade Notice */
	jQuery("#footer-upgrade").hide();
	
	/* Rename Slider */
	jQuery(".edit-slider-name").hover(function() {
		if(jQuery(this).find("#new_slider_name").prop("readonly") == true)
		jQuery(this).find(".fa-edit").stop( true, true ).fadeIn("slow");
	},function(){ 
		jQuery(this).find(".fa-edit").fadeOut("slow"); 
	});
	jQuery(".fa-edit").click(function() {
		jQuery(this).fadeOut("fast"); 
		jQuery(this).prev("#new_slider_name").removeAttr("readonly");
	});
	/* Rename Setting Set */
	jQuery(".rename_set").click(function() {
		jQuery(".rename_set_wrap").fadeIn("slow");
		return false;
	});
	jQuery("#featured_setting_id").change( function() {
		jQuery("#change_setting").submit();
	});
	jQuery(".cfb_connect").click(function() {
		var url = jQuery("#fb-pg-url").val();
		if (/^(https?:\/\/)?((w{3}\.)?)facebook.com\/.*/i.test(url) == false) {
			alert("Please Enter correct  facebook page URL and Click on Connect Button"); 
			jQuery("input[name='fb-pg-url']").addClass('featured-create-error');
			jQuery("html,body").animate({scrollTop:jQuery("input[name='fb-pg-url']").offset().top-50}, 600);
			return false;
		}
		jQuery(".fb-albums").empty();
		jQuery(".fb-albums").append('<div class="featured-loader" style="background: url('+featuredLoader+') 50% 50% ;background-repeat: no-repeat;width: 100px;height: 20px;margin: 15px auto;"></div>');
		var featured_slider_pg = jQuery("#featured-slider-nonce").val();
		var data = {};
		if(jQuery(this).hasClass("eb") == false) data['page'] = 'create_new';
		data['page_url'] = jQuery("#fb-pg-url").val();
		data['action'] = 'featured_shfb_album';
		data['featured_slider_pg'] = featured_slider_pg;
		jQuery.post(ajaxurl, data, function(response) { 
			jQuery(".fb-albums").find(".featured-loader").remove();
			jQuery(".fb-albums").html(response);
		});
		return false;
	});
	/* Custom Slider Popup Script */
	var bindPreviewThumbs = function(scope) {
		var featuredSlides = jQuery(".featured-reorder").length;
		if( parseInt(featuredSlides) <= 0 ) 
			jQuery(".btn-remove").hide();
		else jQuery(".btn-remove").show();
		jQuery(".editSlide",scope).click(function(){
			jQuery(this).parents(".featured-reorder").addClass("featured-open");
			jQuery(this).parents(".featured-reorder").find(".editSlide,.delSlide").css({"left":"6%"});
			jQuery(this).parents(".featured-reorder").animate({"width":"96%"},"slow");
			jQuery(this).siblings(".featured_slideDetails").show();
		
		});
		jQuery(".featured-reorder",scope).hover(function(){ 
			jQuery(this).find('img').css('opacity','0.6');jQuery(this).find('.editSlide,.delSlide,.editcore').fadeIn(500);},
			function(){jQuery(this).find('img').css('opacity','1');jQuery(this).find('.editSlide,.delSlide,.editcore').fadeOut('fast');}
		);
		jQuery(".delSlide").click(function() {
			var agree=confirm("This will remove selected slide from Slider.");
			if (agree) {
				var preview_html = jQuery("#featured-preview-nonce").val();
				var data = {};
				data['slider_id'] = featuredSliderId;
				data['post_id'] = parseInt(jQuery(this).attr('id'));
				data['action'] = 'featured_delete_slide';
				data['preview_html'] = preview_html;
				jQuery.post(ajaxurl, data, function(response) { 
					var data = {
						'action': 'featured_slider_preview',
						'slider_id': featuredSliderId,
						'preview_html':preview_html
					};
					jQuery.post(ajaxurl, data, function(response) {
						var result = response.split("featuredSplit");
						jQuery(".featured_preview").html(result[0]);
						jQuery(".featured-thumbs").html(result[1]);
						jQuery(".featured_slider").css("display","block");
					}).always(function() {
					   	var cnxt=jQuery(".featured-thumbs");
				   		bindPreviewThumbs(cnxt);
					});
				});
			} 
			return false;
		});
		var postArr = Array();
		jQuery(".featured-reorder",scope).click(function(){
			jQuery(this).toggleClass("featured-slide-selected");
			if(jQuery(this).hasClass("featured-slide-selected")) {
				var id = jQuery(this).attr("ID"); 
				postArr.push(id);
				jQuery("input[name='slider_posts']").val(postArr);
			}
			else {
				var id = jQuery(this).attr("ID"); 
				var index = postArr.indexOf(id);
				postArr.splice(index, 1);
				//postArr.push(id);
				jQuery("input[name='slider_posts']").val(postArr);
			
			}
		});
		return false;
	};
	/* Easy BUilder - Call AJAX on Load */
	if(featuredSliderId != undefined ) {
		var sliderType = jQuery("input[name='slider-type']").val();
		// For Custom Slider only
		if(parseInt(sliderType) == 0) {
			var preview_html = jQuery("#featured-preview-nonce").val();
			var data = {
				'action': 'featured_slider_preview',
				'slider_id': featuredSliderId,
				'preview_html':preview_html
			};
			jQuery.post(ajaxurl, data, function(response) {
				var result = response.split("featuredSplit");
				jQuery(".featured-thumbs").html(result[1]);
			}).always(function() {
			   	var cnxt=jQuery(".featured-thumbs");
		   		bindPreviewThumbs(cnxt);
			}); 
		}
	}

	jQuery(".add-slides").click(function() {
		jQuery(".eb-cs-blank").click();
	});
	jQuery(".eb-cs-blank").click(function(){
		var add_slides = jQuery("#featured-add-slides-nonce").val();
		var data = {
			'action': 'featured_add_form',
			'slider_id': featuredSliderId,
			'add_slides':add_slides
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery(".eb-cs-right").html(response);
		}).always(function() {
			var cnxt=jQuery(".eb-cs-right");
		   	bindBehaviors(cnxt);
		});
	});
	jQuery(".show-all-types").click(function(){
		var eb_settings_nonce = jQuery("#featured-eb-settings-nonce").val();
		var sname = jQuery("#new_slider_name").val();
		var data = {
			'action': 'featured_change_type',
			'slider_id': featuredSliderId,
			'sname' : sname,
			'eb_settings_nonce':eb_settings_nonce
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery(".featured-change-type").html(response);
			/* Disable Current slider type Start */
			var currSliderType = jQuery("input[name='slider-type']").val();
			jQuery(".updt-sldr-type").each(function() {
				if(jQuery(this).val() == currSliderType ){
					jQuery(this).prop('disabled', true);
				}
			});
			/* Disable Current slider type END */
		}).always(function() {
			var cnxt=jQuery(".featured-change-type");
		   	bindChangeSliderBehaviors(cnxt);
		});
	});
	/* Bind behaviors for change slider type */
	var bindChangeSliderBehaviors = function(scope) {
		jQuery(".updt-sldr-type").click(function(){
			if(jQuery(this).hasClass("no_key") == false) { 
				var eb_settings_nonce = jQuery("#featured-eb-settings-nonce").val();
				var data = {
					'action': 'featured_show_params',
					'eb_settings_nonce':eb_settings_nonce
				};
				jQuery('#featured-update-type').serializeArray().map(function(item) {
					data[item.name] = item.value;
				});
				jQuery.post(ajaxurl, data, function(response) {
					jQuery(".featured-change-type").html(response);
				}).always(function() {
					var cnxt=jQuery(".featured-change-type");
				   	bindChangeSliderBehaviors(cnxt);
					bindPreviewParams(cnxt);
				});
				return false;
			} else {
				var slider = parseInt(jQuery(this).val());
				var plugins = Array(3,4,5,6,10);
				if(plugins.indexOf(slider) != -1 ) {
					var plugin = jQuery(this).parent(".featured-col-row").find(".featured-icon-title").text();
					pluginName = plugin.replace("Slider","");
					var msg = "Please Activate the "+pluginName+" Plugin to use it";
				} else {
					var sliderTxt = jQuery(this).parent(".featured-col-row").find(".featured-icon-title").text();
					sliderName = sliderTxt.split(' ')[ 0 ];
					var msg = "Please Add API Key for "+sliderName+" on Global Settings";
				}
			
				if(jQuery(this).parent(".featured-col-row").find(".featured-help").length == 0 ) {
					jQuery(this).parent(".featured-col-row").append("<div class='featured-help'>"+msg+"</div>").delay(3000).queue(function() { jQuery(this).find(".featured-help").fadeOut("slow", function() { jQuery(this).remove(); }); });
				}
			}
		});
		jQuery(".featured-updt-btn-back").click(function(){
			jQuery(".show-all-types").click();
			return false;
		});
		jQuery(".cfb_connect").click(function() {
			var url = jQuery("#fb-pg-url").val();
			if (/^(https?:\/\/)?((w{3}\.)?)facebook.com\/.*/i.test(url) == false) {
				alert("Please Enter correct  facebook page URL and Click on Connect Button"); 
				jQuery("input[name='fb-pg-url']").addClass('featured-create-error');
				return false;
			}
			jQuery(".fb-albums").empty();
			jQuery(".fb-albums").append('<div class="featured-loader" style="background: url('+featuredLoader+') 50% 50% ;background-repeat: no-repeat;width: 100px;height: 20px;margin: 15px auto;"></div>');
			var featured_slider_pg = jQuery("#featured-slider-nonce").val();
			var data = {};
			if(jQuery(this).hasClass("eb") == false) data['page'] = 'create_new';
			data['page_url'] = jQuery("#fb-pg-url").val();
			data['action'] = 'featured_shfb_album';
			data['featured_slider_pg'] = featured_slider_pg;
			jQuery.post(ajaxurl, data, function(response) { 
				jQuery(".fb-albums").find(".featured-loader").remove();
				jQuery(".fb-albums").html(response);
			});
			return false;
		});
		jQuery(".feature", scope).change(function() {
			if(jQuery(this).val() == 'user' || jQuery(this).val() == 'user_favorites' ) {
				jQuery(".pxuser").slideDown();
			} else {
				jQuery(".pxuser").slideUp( "slow" );
			}
		});
		
		/* Vimeo Slider */
		jQuery(".vimeo-type").change(function() {
			var val = jQuery(this).val();
			if(val == "channel") {
				jQuery("#vimeo-lb").text("Channel Name");
			} else if(val == "album") {
				jQuery("#vimeo-lb").text("ID");
			} 
		});
		/* Flicker Slider */
		jQuery(".flickr-type").change(function() {
			var val = jQuery(this).val();
			if(val == "user") {
				jQuery("#flickr-lb").text("User ID");
			} else if(val == "album") {
				jQuery("#flickr-lb").text("Album ID");
			} 
		});
		/* Change Slider Types */
		jQuery(".update-type").click(function() {
			/* Validate Before Update */
			if(jQuery("#update_slider_name").val() == "" ) {
				alert("Please Enter the Slider Name");
				jQuery("#new_slider_name").addClass('featured-create-error');
				return false;
			}
			var offset=jQuery("input[name='offset']").val();
			if(offset < 0 || isNaN(offset)) {
				alert("Offset should be a number greater than 0!"); 
				jQuery("input[name='offset']").addClass('featured-create-error');
				return false;
			}
			var sliderType = jQuery("input[name='update-slider-type']").val();
			if( sliderType == 1 ) { 
				var catg_slug=jQuery("select[name='catg_slug']").val();
				if( catg_slug == "" ) {
					alert("Please Select The Category");
					jQuery("select[name='catg_slug']").addClass('featured-create-error');
					return false;
				}
			}  else if( sliderType == 3 ) { 
				var wootype = jQuery("#woo-slider-type").val();
				if( wootype == "upsells" || wootype == "crosssells" || wootype == "grouped" ) {
					var product_id = jQuery("#product_id").val();
					if(product_id == '') {
						alert("Please Enter the Product");
						jQuery("#products").addClass('featured-create-error');
						return false;
					}
				}
			} else if( sliderType == 4 ) { 
				var ecomType =jQuery("select[name='ecom_slider_type']").val();
				if(ecomType == 1) {
					var catg_slug=jQuery("select[name='ecom-catg']").val();
					if( catg_slug == "" ) {
						alert("Please Select The Category"); 
						jQuery("select[name='ecom-catg']").addClass('featured-create-error');
						return false;
					}	
				}
			} else if( sliderType == 7 ) { 
				var postType =jQuery("select[name='taxo_posttype']").val();
				if(postType == "") {
					alert("Please Select The Post Type"); 
					jQuery("select[name='taxo_posttype']").addClass('featured-create-error');
					return false;
				}
				var taxo =jQuery("select[name='taxonomy_name']").val();
				if(taxo == "") {
					alert("Please Select The Taxonomy"); 
					jQuery("select[name='taxonomy_name']").addClass('featured-create-error');
					return false;
				}
				var term =jQuery("#featured_taxonomy_term option:selected").length;
				if(term < 1) {
					alert("Please Select The Term");
					jQuery("#featured_taxonomy_term").addClass('featured-create-error');
					return false;
				}
			} else if( sliderType == 8 ) { 
				var rssfeedurl =jQuery("input[name='rssfeedurl']").val();
				if( rssfeedurl == "" ) {
					alert("Please Enter the Feed Url"); 
					jQuery("input[name='rssfeedurl']").addClass('featured-create-error');
					return false;
				}
			} else if( sliderType == 9 ) { 
				var attachId =jQuery("input[name='postattch-id']").val();
				if( attachId == "" || attachId < 0 || isNaN(attachId)) {
					alert("Please Enter the Post Id "); 
					jQuery("input[name='postattch-id']").addClass('featured-create-error');
					return false;
				}
			} else if( sliderType == 10 ) { 
				var nggId =jQuery("select[name='nextgen-id']").val();;
				if( nggId == "" || nggId < 0 || isNaN(nggId)) {
					alert("Please Enter the NextGen Gallery ID"); 
					jQuery("input[name='nextgen-id']").addClass('featured-create-error');
					return false;
				}
			} else if( sliderType == 11 ) { 
				var ytID =jQuery("input[name='yt-playlist-id']").val();
				if( ytID == "" ) {
					alert("Please Enter the Playlist ID");
					jQuery("input[name='yt-playlist-id']").addClass('featured-create-error');
					return false;
				}
			} else if( sliderType == 12 ) { 
				var ytTerm =jQuery("input[name='yt-search-term']").val();
				if( ytTerm == "" ) {
					alert("Please Enter the Search Term");
					jQuery("input[name='yt-search-term']").addClass('featured-create-error');
					return false;
				}
			} else if( sliderType == 13 ) { 
				var vimeoVal =jQuery("input[name='vimeo-val']").val();
				var vimeoType =jQuery("select[name='vimeo-type']").val();
				if( vimeoVal == "" ) {
					if(vimeoType == "channel" ) var msg = "Please Enter the Channel Name";
					else  var msg = "Please Enter the Album ID";
					alert(msg); 
					jQuery("input[name='vimeo-val']").addClass('featured-create-error');
					return false;
				}
			}  else if( sliderType == 14 ) { 
				var fbUrl =jQuery("input[name='fb-pg-url']").val();
				var fbAlbum =jQuery("select[name='fb-album']").val();
				if( fbUrl == "" ) {
					alert("Please Enter the Page Url and Click on Connect Button"); 
					jQuery("input[name='fb-pg-url']").addClass('featured-create-error');
					return false;
				} 
				else if( fbAlbum == undefined ) {
					alert("Please Click on Connect Button and Select Album");
					return false; 
				}
			} else if( sliderType == 15 ) { 
				var userName =jQuery("input[name='user-name']").val();
				if( userName == "" ) {
					alert("Please Enter the User Name"); 
					jQuery("input[name='user-name']").addClass('featured-create-error');
					return false;
				}
			} else if( sliderType == 16 ) { 
				var flId =jQuery("input[name='fl-id']").val();
				var flType =jQuery("select[name='flickr-type']").val();
				if( flId == "" ) {
					if(flType =="album") var msg = "Please Enter the Album ID";
					else  var msg = "Please Enter the User ID";
					alert(msg); 
					jQuery("input[name='fl-id']").addClass('featured-create-error');
					return false;
				}
			} else if( sliderType == 18 ) { 
				var feature =jQuery("select[name='feature']").val();
				if( feature == "user" || feature == "user_favorites" ) {
					var pxuser =jQuery("input[name='pxuser']").val();
					if(feature == "user" ) var msg = "Please Enter the User Name";
					else  var msg = "Please Enter the User Favorites Name";
					if(pxuser == "") {
						alert(msg); 
						jQuery("input[name='pxuser']").addClass('featured-create-error');
						return false;
					}
				}
			}
			/* validation End */
			var eb_settings_nonce = jQuery("#featured-eb-settings-nonce").val();
			var data = {};
			jQuery('form#featured-update-step2').serializeArray().map(function(item) {
			    data[item.name] = item.value;
			});
			data['action'] = 'featured_update_slider_type';
			data['eb_settings_nonce'] = eb_settings_nonce;
				//console.log(data); return false;
			jQuery.post(ajaxurl, data, function(response) { 
				tb_remove();
				window.location.href = response;
			});
			return false;
		});
	};
	/* End - Bind behaviors for change slider type */

	/* Start Bind Behaviors */
	var bindBehaviors = function(scope) { 
		jQuery(".pageclk", scope).click(function() {
			paged = jQuery(this).attr("id");	
			type = jQuery(".eb-cs-right").find(".post_type").val();
			var custom = jQuery("select").hasClass("sel_post_type");
			var add_slides = jQuery("#featured-add-slides-nonce").val();
			var data = {
				'action': 'featured_show_posts',
				'post_type': type,
				'sliderid': featuredSliderId,
				'paged': paged,
				'add_slides':add_slides
			};
			if(custom == true) data['custom'] = true;
			jQuery.post(ajaxurl, data, function(response) { 
				jQuery(".eb-cs-right").html(response);
			}).always(function() {
			   	var cnxt=jQuery(".eb-cs-right");
		   		bindBehaviors(cnxt);
			});
			return false;
		});
		jQuery(".sel_post_type", scope).change(function() {
			var type = jQuery(this).val();
			var add_slides = jQuery("#featured-add-slides-nonce").val();
			var data = {
				'action': 'featured_show_posts',
				'post_type': type,
				'sliderid': featuredSliderId,
				'custom': true,
				'add_slides':add_slides
			};
			jQuery.post(ajaxurl, data, function(response) {
				jQuery(".eb-cs-right").html(response);
			}).always(function() {
			   	var cnxt=jQuery(".eb-cs-right");
		   		bindBehaviors(cnxt);
			});
			return false;
		});
		jQuery(".add_posts", scope).click(function(){
			var data = {};
			var add_slides = jQuery("#featured-add-slides-nonce").val();
			var posts = new Array();
			jQuery('#eb-wp-posts').serializeArray().map(function(item) {
				if(item.name == "post_id[]")
					posts.push(item.value);
				else  data[item.name] = item.value;
			});
			data['post_id[]'] = posts;
			data['action'] = 'featured_insert_posts';
			data['add_slides'] = add_slides;
			jQuery.post(ajaxurl, data, function(response) {
				tb_remove();
				jQuery("html,body").animate({scrollTop:jQuery('.featured_preview').offset().top-50}, 600);
				jQuery(".featured_preview").prepend('<div class="featured-loader" style="opacity: 0.8; background: #ffffff url('+featuredLoader+') 50% 50% ;background-repeat: no-repeat;width: 72%;height: 350px;top: 0%;margin: 55px auto;position: absolute;z-index: 99;"></div>');
				var preview_html = jQuery("#featured-preview-nonce").val();
				var data = {
					'action': 'featured_slider_preview',
					'slider_id': featuredSliderId,
					'preview_html':preview_html
				};
				jQuery.post(ajaxurl, data, function(response) {
					jQuery(".featured_preview").find(".featured-loader").fadeOut("fast", function(){ jQuery(this).remove();});
					var result = response.split("featuredSplit");
					jQuery(".featured_preview").html(result[0]);
					jQuery(".featured-thumbs").html(result[1]);
					jQuery(".featured_slider").css("display","block");
				}).always(function() {
				   	var cnxt=jQuery(".featured-thumbs");
			   		bindPreviewThumbs(cnxt);
				});
			});
			return false;
		});
		jQuery(".fb_connect", scope).click(function() {
			jQuery(".eb-cs-right").append('<div class="featured-loader" style="background: #ffffff url('+featuredLoader+') 50% 50% ;background-repeat: no-repeat;width: 100px;height: 20px;margin: 15px auto;"></div>');
			var data = {};
			var add_slides = jQuery("#featured-add-slides-nonce").val();
			jQuery('#fb_connect').serializeArray().map(function(item) {
				if( item.name != 'fb_album' )
					data[item.name] = item.value;
			});
			data['action'] = 'featured_show_fb';
			data['add_slides'] = add_slides;
			jQuery.post(ajaxurl, data, function(response) { 
				jQuery(".eb-cs-right").find(".featured-loader").remove();
				jQuery(".eb-cs-right").html(response);
			}).always(function() {
			   	var cnxt=jQuery(".eb-cs-right");
		   		bindBehaviors(cnxt);
			});
			return false;
		});
		jQuery(".px_connect", scope).click(function() {
				jQuery(".eb-cs-right").prepend('<div class="featured-loader" style="opacity: 0.8; background: #ffffff url('+featuredLoader+') 50% 50% ;background-repeat: no-repeat;width: 76%;height: 84%;margin: 55px auto;position: absolute;"></div>');
			var data = {};
			var add_slides = jQuery("#featured-add-slides-nonce").val();
			jQuery('#px_connect').serializeArray().map(function(item) {
				data[item.name] = item.value;
			});
			data['action'] = 'featured_show_px';
			data['add_slides'] = add_slides;
			jQuery.post(ajaxurl, data, function(response) { 
				jQuery(".eb-cs-right").find(".featured-loader").remove();
				jQuery(".eb-cs-right").html(response);
			}).always(function() {
			   	var cnxt=jQuery(".eb-cs-right");
		   		bindBehaviors(cnxt);	
			});
			return false;
		});
		jQuery(".it_connect", scope).click(function() {
			jQuery(".eb-cs-right").prepend('<div class="featured-loader" style="opacity: 0.8; background: #ffffff url('+featuredLoader+') 50% 50% ;background-repeat: no-repeat;width: 76%;height: 84%;margin: 55px auto;position: absolute;"></div>');
			var data = {};
			var add_slides = jQuery("#featured-add-slides-nonce").val();
			jQuery('#it_connect').serializeArray().map(function(item) {
				data[item.name] = item.value;
			});
			data['action'] = 'featured_show_it';
			data['add_slides'] = add_slides	;
			jQuery.post(ajaxurl, data, function(response) { 
				jQuery(".eb-cs-right").find(".featured-loader").remove();
				jQuery(".eb-cs-right").html(response);
			}).always(function() {
			   	var cnxt=jQuery(".eb-cs-right");
		   		bindBehaviors(cnxt);
			});
			return false;
		});
		jQuery(".flickr_connect", scope).click(function() {
				jQuery(".eb-cs-right").prepend('<div class="featured-loader" style="opacity: 0.8; background: #ffffff url('+featuredLoader+') 50% 50% ;background-repeat: no-repeat;width: 76%;height: 84%;margin: 55px auto;position: absolute;"></div>');
			var data = {};
			var add_slides = jQuery("#featured-add-slides-nonce").val();
			jQuery('#flickr_connect').serializeArray().map(function(item) {
				data[item.name] = item.value;
			});
			data['action'] = 'featured_show_flickr';
			data['add_slides'] = add_slides;
			jQuery.post(ajaxurl, data, function(response) { 
				jQuery(".eb-cs-right").find(".featured-loader").remove();
				jQuery(".eb-cs-right").html(response);
			}).always(function() {
			   	var cnxt=jQuery(".eb-cs-right");
		   		bindBehaviors(cnxt);
			});
			return false;
		});
		jQuery(".feature", scope).change(function() {
			if(jQuery(this).val() == 'user' || jQuery(this).val() == 'user_favorites' ) {
				jQuery(".pxuser").slideDown();
			} else {
				jQuery(".pxuser").slideUp( "slow" );
			}
		});
		jQuery(".featured-img-box", scope).click(function() {
			jQuery(this).toggleClass("featured-img-box-selected");
			if(jQuery(this).hasClass("featured-img-box-selected")) {
				var src = jQuery(this).find("img").attr("src");
				jQuery(this).append("<input type='hidden' name='img_url' value="+src+" / >");
			} else {
				jQuery(this).find("input[type='hidden']").remove();
			}
		});
		jQuery(".fb_albums", scope).change(function() {
			jQuery(".fb-img-wrap").empty();
				jQuery(".eb-cs-right").append('<div class="featured-loader" style="background: #ffffff url('+featuredLoader+') 50% 50% ;background-repeat: no-repeat;width: 100px;height: 20px;margin: 15px auto;"></div>');
			var data = {};
			var add_slides = jQuery("#featured-add-slides-nonce").val();
			var img_url = new Array();
			jQuery('#fb_connect').serializeArray().map(function(item) {
				if(item.name == 'img_url' )
					img_url.push(item.value);
				else data[item.name] = item.value;
			});
			data['img_url'] = img_url;
			data['action'] = 'featured_show_fb';
			data['add_slides'] = add_slides;							
			jQuery.post(ajaxurl, data, function(response) { 
				jQuery(".eb-cs-right").find(".featured-loader").remove();
				jQuery(".eb-cs-right").html(response);
			}).always(function() {
			   	var cnxt=jQuery(".eb-cs-right");
		   		bindBehaviors(cnxt);
			});
			return false;
			
		});
		jQuery(".featured_fip_insert", scope).click(function() {
			var customPost = jQuery("input[name='custom_post']").val();
			if(customPost != '1') {
				jQuery("#featured-error-msg").html("<span>To insert social slide, select 'Yes' for 'Create \"SliderVilla Slides\" Custom Post Type' on Global Settings</span>").fadeIn( "slow" );
				return false;
			}
			var add_slides = jQuery("#featured-add-slides-nonce").val();
			var data = {};
			var img_url = new Array();
			var frmId = jQuery(this).parents("form").attr("id");
			jQuery('#'+frmId).serializeArray().map(function(item) {
				if(item.name == 'img_url' )
					img_url.push(item.value);
				else data[item.name] = item.value;
			});
			data['img_url'] = img_url;
			data['action'] = 'featured_fip_insert';
			data['type'] = frmId;
			data['add_slides'] = add_slides;
			jQuery.post(ajaxurl, data, function(response) { 
				tb_remove();
				jQuery("html,body").animate({scrollTop:jQuery('.featured_preview').offset().top-50}, 600);
				jQuery(".featured_preview").prepend('<div class="featured-loader" style="opacity: 0.8; background: #ffffff url('+featuredLoader+') 50% 50% ;background-repeat: no-repeat;width: 72%;height: 350px;top: 0%;margin: 55px auto;position: absolute;z-index: 99;"></div>');
				var preview_html = jQuery("#featured-preview-nonce").val();
				var data = {
					'action': 'featured_slider_preview',
					'slider_id': featuredSliderId,
					'preview_html':preview_html
				};
				jQuery.post(ajaxurl, data, function(response) {
					jQuery(".featured_preview").find(".featured-loader").fadeOut("fast", function(){ jQuery(this).remove();});
					var result = response.split("featuredSplit");
					jQuery(".featured_preview").html(result[0]);
					jQuery(".featured-thumbs").html(result[1]);
					jQuery(".featured_slider").css("display","block");
				}).always(function() {
				   	var cnxt=jQuery(".featured-thumbs");
			   		bindPreviewThumbs(cnxt);
				});
			});
			return false;
		});
		jQuery(".featured_insert_video", scope).click(function() {
			var customPost = jQuery("input[name='custom_post']").val();
			if(customPost != '1') {
				jQuery("#featured-error-msg").html("<span>To insert Video, select 'Yes' for 'Create \"SliderVilla Slides\" Custom Post Type' on Global Settings</span>").fadeIn( "slow" );
				return false;
			}
			var add_slides = jQuery("#featured-add-slides-nonce").val();
			var data = {};
			var video_url = new Array();
			var video_title = new Array();
			jQuery('#featured_insert_video').serializeArray().map(function(item) { 
				if(item.name == "video_title") {
					video_title.push(item.value);
				}
				else if(item.name == "video_url")
					video_url.push(item.value);
				else data[item.name] = item.value;
			});
			data['video_title'] = video_title;
			data['video_url'] = video_url;
			data['action'] = 'featured_insert_video';
			data['add_slides'] = add_slides;
			jQuery.post(ajaxurl, data, function(response) {
				tb_remove();
				window.location.href=window.location.href;
			});
			return false;
		});
		jQuery(".add_video", scope).click(function(){
			var formData = jQuery("#featured_insert_video .featured-video-slide").last().html();	
			jQuery(".featured-video-wrap").append("<div class='featured-video-slide'>"+formData+"</div>");
		});
		jQuery('.featured_upload_button', scope).on("click",function(event) {
			var currUpload = jQuery(this).prev('.slide_image');
			var frame;
			event.preventDefault();
			// If the media frame already exists, reopen it.
			if ( frame ) {
				frame.open();
				return;
			}
			// Create the media frame.
			frame = wp.media({
				title: 'Upload/Select Images',
				multiple: false,
				button: {
					text: 'Add to Slider',
					close: false
				}
			});
			frame.on( 'select', function() {
				// Grab the selected attachment.
				var attachments = frame.state().get('selection').toArray();
				frame.close();
				if(attachments.length>0){
					var imgurl = attachments[0].attributes.url;
					currUpload.val(imgurl);
				}
			});
			// Finally, open the modal.
			frame.open();
			return false;
		});
		jQuery(".btn-insert", scope).click(function(){
			var customPost = jQuery("input[name='custom_post']").val();
			if(customPost != '1') {
				jQuery("#featured-error-msg").html("<span>To insert blank slide, select 'Yes' for 'Create \"SliderVilla Slides\" Custom Post Type' on Global Settings</span>").fadeIn( "slow" );
				return false;
			}
			var add_slides = jQuery("#featured-add-slides-nonce").val();
			var data = {};
			var slide_title = new Array();
			var slide_desc = new Array();
			var slide_url = new Array();
			var slide_image = new Array();
			jQuery('form.add-new-slide').serializeArray().map(function(item) {
				if(item.name == "slide_title") {
					slide_title.push(item.value);
				}
				else if(item.name == "slide_desc")
					slide_desc.push(item.value);
				else if(item.name == "slide_url")
					slide_url.push(item.value);
				else if(item.name == "slide_image")
					slide_image.push(item.value);
				else data[item.name] = item.value;
			});
			data['slide_title'] = slide_title;
			data['slide_desc'] = slide_desc;
			data['slide_url'] = slide_url;
			data['slide_image'] = slide_image;
			data['action'] = 'featured_insert_slide';
			data['add_slides'] = add_slides;
			jQuery.post(ajaxurl, data, function(response) {
				tb_remove();
				jQuery("html,body").animate({scrollTop:jQuery('.featured_preview').offset().top-50}, 600);
				jQuery(".featured_preview").prepend('<div class="featured-loader" style="opacity: 0.8; background: #ffffff url('+featuredLoader+') 50% 50% ;background-repeat: no-repeat;width: 72%;height: 350px;top: 0%;margin: 55px auto;position: absolute;z-index: 99;"></div>');
				var preview_html = jQuery("#featured-preview-nonce").val();
				var data = {
					'action': 'featured_slider_preview',
					'slider_id': featuredSliderId,
					'preview_html':preview_html
				};
				jQuery.post(ajaxurl, data, function(response) {
					jQuery(".featured_preview").find(".featured-loader").fadeOut("fast", function(){ jQuery(this).remove();});
					var result = response.split("featuredSplit");
					jQuery(".featured_preview").html(result[0]);
					jQuery(".featured-thumbs").html(result[1]);
					jQuery(".featured_slider").css("display","block");
				}).always(function() {
				   	var cnxt=jQuery(".featured-thumbs");
			   		bindPreviewThumbs(cnxt);
				});
			});
		
			return false;
		});
		jQuery(".add_more", scope).on("click",function() {
			var formData = jQuery(".featured-slide-content").last().html();
			var cnt = jQuery(".featured-slide-content").length;
			if(cnt%2 == '1' ) var cls = ' odd'; 
			else var cls = '';
			var cnxt=jQuery("<div class='featured-slide-content"+cls+"'>"+formData+"</div>").appendTo(".featured-slide");
			bindBehaviors(cnxt);
		});
		/* Add Media to custom slider */
		// Show Edit and delete slides on hover 
		jQuery('.addedImg').hover(function(){ 
			jQuery(this).find('img').css('opacity','0.6');
			jQuery(this).find('.addedImgEdit,.addedImgDel').fadeIn(500);
		}, function(){
			jQuery(this).find('img').css('opacity','1');
			jQuery(this).find('.addedImgEdit,.addedImgDel').fadeOut('fast');}
		);
		jQuery('.addedImgEdit').click(function(){
			var imgDetails=jQuery(this).parent('.imgCont').parent('.addedImg').find('.ImgDetails');
			var imgWrapper=jQuery(this).parents('.uploaded-images');
			imgDetails.width((imgWrapper.width() - 220));
			imgDetails.fadeToggle("slow");
		});	
		jQuery('.addedImgDel').click(function(){
			jQuery(this).parent('.imgCont').parent('.addedImg').fadeOut(400,function(){jQuery(this).remove();});
		});
		jQuery(".media-insert", scope).click(function(){
			var add_slides = jQuery("#featured-add-slides-nonce").val();
			var data = {};
			var imgID = new Array();
			jQuery('form.addImgForm').serializeArray().map(function(item) {
				if(item.name == "imgID[]") {
					imgID.push(item.value);
				}
				else data[item.name] = item.value;
			});
			data['imgID'] = imgID;
			data['action'] = 'featured_insert_media';
			data['add_slides'] = add_slides;
			jQuery.post(ajaxurl, data, function(response) {
				tb_remove();
				jQuery("html,body").animate({scrollTop:jQuery('.featured_preview').offset().top-50}, 600);
				jQuery(".featured_preview").prepend('<div class="featured-loader" style="opacity: 0.8; background: #ffffff url('+featuredLoader+') 50% 50% ;background-repeat: no-repeat;width: 72%;height: 350px;top: 0%;margin: 55px auto;position: absolute;z-index: 99;"></div>');
				var preview_html = jQuery("#featured-preview-nonce").val();
				var data = {
					'action': 'featured_slider_preview',
					'slider_id': featuredSliderId,
					'preview_html':preview_html
				};
				jQuery.post(ajaxurl, data, function(response) {
					jQuery(".featured_preview").find(".featured-loader").fadeOut("fast", function(){ jQuery(this).remove();});
					var result = response.split("featuredSplit");
					jQuery(".featured_preview").html(result[0]);
					jQuery(".featured-thumbs").html(result[1]);
					jQuery(".featured_slider").css("display","block");
				}).always(function() {
				   	var cnxt=jQuery(".featured-thumbs");
			   		bindPreviewThumbs(cnxt);
				});
			});
			return false;
		});
		/* END - Add Media to custom slider */
	};
	/* End Bind Behaviors */
	jQuery(".feature").change(function() {
		if(jQuery(this).val() == 'user' || jQuery(this).val() == 'user_favorites' ) {
			jQuery(".pxuser").slideDown();
		} else {
			jQuery(".pxuser").slideUp( "slow" );
		}
	});
	jQuery(".eb-cs-post").click(function(){
		var type = jQuery(this).attr("id");
		var add_slides = jQuery("#featured-add-slides-nonce").val();
		var data = {
			'post_type': type,
			'sliderid': featuredSliderId,
			'action': 'featured_show_posts',
			'add_slides':add_slides
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery(".eb-cs-right").html(response);
		}).always(function() {
			var cnxt=jQuery(".eb-cs-right");
		   	bindBehaviors(cnxt);
		});
	});
	jQuery(".eb-cs-video").click(function() {
		var type = jQuery(this).attr("id");
		var add_slides = jQuery("#featured-add-slides-nonce").val();
		var data = {
			'sliderid': featuredSliderId,
			'action': 'featured_add_video',
			'type':type,
			'add_slides':add_slides
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery(".eb-cs-right").html(response);
		}).always(function() {
			var cnxt=jQuery(".eb-cs-right");
		   	bindBehaviors(cnxt);
		});
	});
	jQuery(".eb-cs-px").click(function() {
		jQuery(".eb-cs-right").empty();
		jQuery(".eb-cs-right").prepend('<div class="featured-loader" style="opacity: 0.8; background: #ffffff url('+featuredLoader+') 50% 50% ;background-repeat: no-repeat;width: 76%;height: 84%;margin: 55px auto;position: absolute;"></div>');
		var add_slides = jQuery("#featured-add-slides-nonce").val();
		var data = {
			'sliderid': featuredSliderId,
			'action': 'featured_show_px',
			'add_slides':add_slides
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery(".eb-cs-right").find(".featured-loader").remove();
			jQuery(".eb-cs-right").html(response);
		}).always(function() {
		   	var cnxt=jQuery(".eb-cs-right");
		   	bindBehaviors(cnxt);
		});
	});
	jQuery(".eb-cs-fb").click(function() {
		var add_slides = jQuery("#featured-add-slides-nonce").val();
		var data = {
			'sliderid': featuredSliderId,
			'action': 'featured_show_fb',
			'add_slides':add_slides
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery(".eb-cs-right").html(response);
		}).always(function() {
		   	var cnxt=jQuery(".eb-cs-right");
		   	bindBehaviors(cnxt);
		});
	});
	jQuery(".eb-cs-fl").click(function() {
		var add_slides = jQuery("#featured-add-slides-nonce").val();
		var data = {
			'sliderid': featuredSliderId,
			'action': 'featured_show_flickr',
			'add_slides':add_slides
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery(".eb-cs-right").html(response);
		}).always(function() {
		   	var cnxt=jQuery(".eb-cs-right");
		   	bindBehaviors(cnxt);
		});
	});
	jQuery(".eb-cs-it").click(function() {
		var add_slides = jQuery("#featured-add-slides-nonce").val();
		var data = {
			'sliderid': featuredSliderId,
			'action': 'featured_show_it',
			'add_slides':add_slides
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery(".eb-cs-right").html(response);
		}).always(function() {
		   	var cnxt=jQuery(".eb-cs-right");
		   	bindBehaviors(cnxt);
		});
	});
	jQuery(".eb-cs-pt").click(function(){
		var add_slides = jQuery("#featured-add-slides-nonce").val();
		var data = {
			'sliderid': featuredSliderId,
			'action': 'featured_show_post_type',
			'add_slides':add_slides
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery(".eb-cs-right").html(response);
		}).always(function() {
		   	var cnxt=jQuery(".eb-cs-right");
		   	bindBehaviors(cnxt);
		});
	});
	jQuery(".eb-cs-media").click(function(event) {
		var frame;
		event.preventDefault();
		// If the media frame already exists, reopen it.
		if ( frame ) {
			frame.open();
			return;
		}
		// Create the media frame.
		frame = wp.media({
			// Set the title of the modal.
			title: 'Upload/Select Images',
			multiple: true,
			// Customize the submit button.
			button: {
				// Set the text of the button.
				text: 'Add to Slider',
				// Tell the button not to close the modal, since we're
				// going to refresh the page when the image is selected.
				close: false
			}
		});
		frame.on( 'select', function() {
			// Grab the selected attachment.
			var attachments = frame.state().get('selection').toArray();
			frame.close();
			if(attachments.length>0){
				var imgdiv='', html='';
				for(i=0;i<attachments.length;i++){
					var imgId=parseInt(attachments[i].id);
					imgdiv+='<div class="addedImg"><input type="hidden" name="imgID[]" value="'+imgId+'" /\><div class="imgCont"><img title="'+attachments[i].attributes.title+'" src="'+attachments[i].attributes.url+'" width="200"  /\><span class="addedImgEdit"></span><span class="addedImgDel"></span></div><div class="ImgDetails" style="display:none;"><div class="fL"><span class="imgTitle"><input placeholder="Title" title="Enter Image Title" type="text" name="title['+imgId+']" value="'+attachments[i].attributes.title+'" /\></span><span class="imgDesc"><textarea placeholder="Description" title="Enter Image Description" rows=3 name="desc['+imgId+']">'+attachments[i].attributes.description+'</textarea></span></div><div class="fR"><span class="imgLink"><input type="text" placeholder="Link To" value="" name="link['+imgId+']" /\></span><span class="imgNoLink"><strong>Do not link to any url: &nbsp; </strong><input type="checkbox" value="1" name="nolink['+imgId+']" /\></span></div></div></div>';
				};
				html = '<div class="uploaded-images">';
				html += '<form method="post" class="addImgForm">';
				html += imgdiv;
				html += '<div style="clear:left;margin-top:10px;" class="image-uploader">';
				html += '<input type="submit" class="btn_save media-insert" name="insert" value="Insert" /\>';
				html += '</div>';
				html += '<input type="hidden" name="current_slider_id" value="'+featuredSliderId+'" /\>';
				html += '</form>';
				html += '</div>';
				jQuery(".eb-cs-right").html(html);
				var cnxt=jQuery(".eb-cs-right");
		   		bindBehaviors(cnxt);
			}
		});
		// Finally, open the modal.
		frame.open();
	});
	jQuery(".eb-cs-tab").click(function() {
		jQuery(".eb-cs-tab").removeClass("featured-active");
		jQuery(this).addClass("featured-active");
	});
	/* Custom Slider END */
	
	
	/* END JS for Ajax and custom slider */

	/**
	* -------------------------------------------------------------------------------------
	* JS for Settings Panel Preview Params AJAX Load
	* -------------------------------------------------------------------------------------
	**/
	var bindPreviewParams = function(scope) { 
		// WooCommerce Slider show/hide product autocomplete field
		jQuery(".featured_woo_type", scope).change(function() {
			var sliderType = jQuery(this).val();
			if( sliderType != "recent" && sliderType != "external" ) {
				jQuery(".woo-product").css("display","block");
			} else {
				jQuery(".woo-product").css("display","none");
			}
		});
		jQuery("#products", scope).autocomplete({ 
			source: function( request, response ) {
			var featured_slider_pg = jQuery("#featured-slider-nonce").val();
			var data = {};
			data['type'] = jQuery(".featured_woo_type").val();
			data['term']=request.term;
			data['action']='featured_woo_product';
			data['featured_slider_pg']=featured_slider_pg;
				jQuery.ajax({
					url: ajaxurl,
					data: data,
					method: "post",
					dataType: "json",
					success: function( data ) {
						if(data.length != 0 ) {
							response( jQuery.map( data.product, function( item ) {
								
								return {
								label: item.title,
								value: item.title,
								ID: item.ID
								}
							}));
						}
					}
				});
			},
			minLength:1,
			select: function(event,ui) { 
				jQuery("#product_id").val(ui.item.ID);
			} 
		}); 
		jQuery(".featured-multiselect", scope).focusout(function() {
			var sel = jQuery(this)[0]; 
			var terms = [],opt;
			// loop through options in select list
			for (var i=0, len=sel.options.length; i<len; i++) {
				opt = sel.options[i];
				// check if selected
				if ( opt.selected ) {
					terms.push(opt.value);
				}
			}
			terms = terms.join();
			jQuery(this).next("input[type='hidden']").val(terms);
		});
		jQuery("#featured_taxonomy_posttype", scope).change(function() {
			var featured_slider_pg = jQuery("#featured-slider-nonce").val();
			var data = {};
			data['post_type'] = jQuery(this).val();
			data['option'] = jQuery("#featured-option").val();
			data['action'] = 'featured_show_taxonomy';
			data['featured_slider_pg'] = featured_slider_pg;
			jQuery.post(ajaxurl, data, function(response) { 
				jQuery(".sh-taxo").html(response);
			}).always(function() {
				var cnxt=jQuery(".sh-taxo");
			   	bindPreviewParams(cnxt);
			});
			return false;
		});
		jQuery("#featured_taxonomy", scope).change(function() { 
			var featured_slider_pg = jQuery("#featured-slider-nonce").val();
			var data = {};
			data['preview'] = 'true';
			data['option'] = jQuery("#featured-option").val();
			data['taxo'] = jQuery(this).val();
			data['action'] = 'featured_show_term';
			data['featured_slider_pg'] = featured_slider_pg;
			jQuery.post(ajaxurl, data, function(response) { 
				jQuery(".sh-term").fadeIn("slow");
				jQuery(".sh-term").html(response);
			}).always(function() {
				var cnxt=jQuery(".sh-term");
			   	bindPreviewParams(cnxt);
			});
			return false;
		});
		jQuery(".rss-source").change(function() {
			if(jQuery(this).val() == 'smugmug') {
				jQuery(".rss-size").show();
				jQuery(".rss-feed").hide();
			} else {
				jQuery(".rss-size").hide();
				jQuery(".rss-feed").show();
			}
		});
	};
	/* END - Preview params */
	/* Lock and Activation message for create new slider and update slider */
	jQuery("input[name='slider_type']").click(function() {
		if(jQuery(this).hasClass("no_key") == false) { 
			jQuery("#featured-create-new").submit();
			jQuery(".update-type").show();
		} else {
			var slider = parseInt(jQuery(this).val());
			var plugins = Array(3,4,5,6,10);
			if(plugins.indexOf(slider) != -1 ) {
				var plugin = jQuery(this).parent(".featured-col-row").find(".featured-icon-title").text();
				pluginName = plugin.replace("Slider","");
				var msg = "Please Activate the "+pluginName+" Plugin to use it";
			} else {
				var sliderTxt = jQuery(this).parent(".featured-col-row").find(".featured-icon-title").text();
				sliderName = sliderTxt.split(' ')[ 0 ];
				var msg = "Please Add API Key for "+sliderName+" From Global Settings";
			}
			
			if(jQuery(this).parent(".featured-col-row").find(".featured-help").length == 0 ) {
				jQuery(this).parent(".featured-col-row").append("<div class='featured-help'>"+msg+"</div>").delay(3000).queue(function() { jQuery(this).find(".featured-help").fadeOut("slow", function() { jQuery(this).remove(); }); });
			}
			jQuery(".update-type").hide();
		}
	});
	
	var old_tb_remove = window.tb_remove;

	tb_remove = function() {
		jQuery("#TB_imageOff").unbind("click");
		jQuery("#TB_closeWindowButton").unbind("click");
		jQuery("#TB_window").css({'-webkit-animation-name': 'svzoomOut','animation-name': 'svzoomOut'}).fadeOut("slow",function(){jQuery('#TB_window,#TB_overlay,#TB_HideSelect').trigger("tb_unload").unbind().remove();});
		jQuery( 'body' ).removeClass( 'modal-open' );
		jQuery("#TB_load").remove();
		if (typeof document.body.style.maxHeight == "undefined") {//if IE 6
			jQuery("body","html").css({height: "auto", width: "auto"});
			jQuery("html").css("overflow","");
		}
		jQuery(document).unbind('.thickbox');
		return false;
	};	
});
