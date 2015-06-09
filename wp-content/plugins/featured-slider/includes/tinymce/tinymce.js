function featuredSliderInit() {
	tinyMCEPopup.resizeToInnerSize();
}
function insertFeaturedShortcode(args) {
	var defaults={
		createdSlider	:0,
		shortCode	:''
	}
	options=jQuery.extend({},defaults,args);
	var tagtext = '';
	if(options.createdSlider == '1') {
		tagtext = options.shortCode;
	} else {
		var end_tag_atts='';
		jQuery.each(jQuery("#svslider").serializeArray(),function(index,field){
			if(field.name!='slider' && field.name!='fb-url' && field.value){
				if(field.name=="slider_type") {
					end_tag_atts=end_tag_atts + field.value;
				} else {
					end_tag_atts=end_tag_atts + ' ' + field.name + '="' + field.value + '"';
				}
			}
		})
		tagtext = tagtext + "[" + end_tag_atts + "]";
	}
	
	if(window.tinyMCE) {
		//TODO: For QTranslate we should use here 'qtrans_textarea_content' instead 'content'
		//execInstanceCommand is undefined from tinymce version 4
		if (typeof window.tinyMCE.execInstanceCommand != 'undefined') {
			window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
        }
		else {
			if (typeof window.tinyMCE.execCommand != 'undefined') {
				window.tinyMCE.get('content').execCommand('mceInsertContent', false, tagtext);
			}
        }
		//window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		//Peforms a clean up of the current editor HTML. 
		//tinyMCEPopup.editor.execCommand('mceCleanup');
		//Repaints the editor. Sometimes the browser has graphic glitches. 
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}
