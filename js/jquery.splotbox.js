/* Comparator: SPLOTbox Scripts
   code by Alan Levine @cogdog http://cogdog.info
   
   media uploader scripts somewhat lifted from
   http://mikejolley.com/2012/12/using-the-new-wordpress-3-5-media-uploader-in-plugins/ 
*/

// valid file extensions
var allowables = ['mp3' , 'm4a', 'ogg'];


function getAbsolutePath() {
    var loc = window.location;
    var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
    return loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
}

function isAllowableUploadLink(url) {

	// get extension -- h/t https://stackoverflow.com/a/12900504/2418186
	 var ext = url.slice((url.lastIndexOf(".") - 1 >>> 0) + 2);
	 
	 // check in array
	 return allowables.includes(ext.toLowerCase());

}

jQuery(document).ready(function() { 
	// called for via click of upload button in theme options
	
	
	// hide the test url button
    if( !jQuery("#wMediaURL").val() ) { jQuery("#testURL").hide(); }

	// show test url button if url field changed
	jQuery("#wMediaURL").blur(function() {
		if(this.value.replace(/\s/g, "") === "") {
		   // hide button
		   jQuery("#testURL").hide();
		} else {
			//show test button, set its href value
		   jQuery("#testURL").attr("href", jQuery("#wMediaURL").val());
		   jQuery("#testURL").show();
		   
		}
	});

	jQuery(document).on('click', '.upload_image_button', function(e){

		// disable default behavior
		e.preventDefault();

		// Create the media frame
		// use title and label passed from data-items in form button
	
		file_frame = wp.media.frames.file_frame = wp.media({
		  title: jQuery( this ).data( 'uploader_title' ),
		  button: {
			text: jQuery( this ).data( 'uploader_button_text' ),
		  },
		  multiple: false  // Set to true to allow multiple files to be selected
		});

		// fetch the type for this option so we can use it, comes from data-ctype value 
		// in form button
			
		// set up call back from image selection from media uploader
		file_frame.on( 'select', function() {
	
		// attachment object from upload
		attachment = file_frame.state().get('selection').first().toJSON();

		if ( isAllowableUploadLink(attachment.url) ) {
  
			// insert the attachment url into the hidden field for the option value
			jQuery("#wMediaURL").val(attachment.url);  

			// insert a title if there is one
			jQuery("#wTitle").val(attachment.title); 
	
			// insert a caption if there is a description in metadata
			jQuery("#wText").val(attachment.description); 
	
			// set link of url test button and show it
			jQuery("#testURL").attr("href", attachment.url);
			jQuery("#testURL").show();
		
		} else {
		
			alert('Sorry but ' + attachment.url + ' is not a valid file format. This site accepts only uploads for files with extensions of ' + allowables.join(', ') + '. Please try again.');
		
		
		}
		  
		});

		// Finally, open the modal
		file_frame.open();
	
	});
		
	jQuery('#wTags').suggest( getAbsolutePath() + "wp-admin/admin-ajax.php?action=ajax-tag-search&tax=post_tag", {multiple:true, multipleSep: ","});
	

});
