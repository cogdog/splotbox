/* SPLOTbox Scripts
   code by Alan Levine @cogdog http://cog.dog
   
	upload for input field style by CSS to be a drop zone
*/

// valid file extensions
var allowables = ['mp3' , 'm4a', 'ogg', 'jpg' , 'png', 'jpeg','gif'];

function isAllowableUploadLink(url) {
	// get extension -- h/t https://stackoverflow.com/a/12900504/2418186
	 var ext = url.slice((url.lastIndexOf(".") - 1 >>> 0) + 2);
	 
	 // check in array
	 return allowables.includes(ext.toLowerCase());
}


var image_exts = ['jpg' ,'jpeg', 'png' ,'gif'];

function isImageFile(fname) {
	// get extension -- h/t https://stackoverflow.com/a/190878/2418186
	 var ext = fname.split('.').pop();
	 	 
	 // check in array
	 return image_exts.includes(ext.toLowerCase());
}


	jQuery('#wTags').suggest( boxObject.siteURL + "wp-admin/admin-ajax.php?action=ajax-tag-search&tax=post_tag", {multiple:true, multipleSep: ","});


jQuery(document).ready(function() { 

	jQuery('#splotdropzone input').change(function () {

		if (this.value) {
			// prompt for drop area
		
			// get the file size
			let file_size_MB = (this.files[0].size / 1000000.0).toFixed(2);
		
			if ( file_size_MB >  parseFloat(boxObject.uploadMax)) { 
				alert('Error: The size of your file, ' + file_size_MB + ' Mb, is greater than the maximum allowed for this site (' + boxObject.uploadMax + ' Mb). Try a different file or see if you can shrink the size of this one.');
				jQuery('#wUploadMedia').val("");
			} else {
			
				// store the last media URL  in hidden field
				jQuery("#footlocker").text(jQuery("#wMediaURL").val());
				
				// store the last media ID  in hidden field
				jQuery("#idlocker").text(jQuery("#wFeatureImage").val());
				
				// clear mediaURL
				jQuery("#wMediaURL").val('');
				
				
				if ( isImageFile(this.value) ) {
					
					// put file name in dropzone
					jQuery('#dropmessage').text('Selected Image: ' + this.value.substring(12));
	
					// generate a preview of image in the thumbnail source
					// h/t https://codepen.io/waqasy/pen/rkuJf
					if (this.files && this.files[0]) {
						var freader = new FileReader();

						freader.onload = function (e) {
							jQuery('#mediathumb').attr('src', e.target.result);
						};

						freader.readAsDataURL(this.files[0]);			
					
						 // update status 
						jQuery("#uploadresponse").html('Image selected. When you <strong>Save/Update</strong> below this file will be uploaded (' + file_size_MB + ' Mb).');

					} else {
						// no image files received?
						 reset_dropzone();
					}
					
				} else {
					// audio file
					if (this.files && this.files[0]) {
					
						jQuery('#dropmessage').text('Selected Audio: ' + this.value.substring(12));
						jQuery("#uploadresponse").html('Audio selected. When you <strong>Save/Update</strong> below this file will be uploaded (' + file_size_MB + ' Mb).');
					} else {
						// no audio files received?
						 reset_dropzone();
					}				
				} // is image
			} // file size check

			
		} else {
			// cancel clicked
			reset_dropzone();
		}
	});

	jQuery("#mediathumb").click(function(){
		jQuery("#splotdropzone input").click();
	});	
	
	function reset_dropzone() {
		//reset thumbnail preview
		jQuery('#mediathumb').attr('src', 'https://placehold.it/150x150?text=Media+holder');

		// clear status field
		jQuery("#uploadresponse").text('');

		// reset drop zone prompt
		jQuery('#dropmessage').text('Drag file or click to select one to upload'); 
		
		 // return the media URL in hidden div
		jQuery("#wMediaURL").val(jQuery("#footlocker").text());
		
		// restore the media ID
		jQuery("#wFeatureImage").text(jQuery("#idlocker").val());

	}


	// hide the test url button
    if( !jQuery("#wMediaURL").val() ) { 
    	jQuery("#testURL").removeAttr('href'); 
    	jQuery("#testURL").addClass('disabled');
    }
    
    jQuery("input" ).change(function() {
    	jQuery("#wPreview").addClass('disabled');
	});
	
	
	jQuery("input[type=radio][name=wMediaMethod]" ).change(function() {
		jQuery("#media_by_url").toggle("slow");
		jQuery("#media_by_upload").toggle("slow");
	});
	


	// show test url button if url field changed
	jQuery("#wMediaURL").change(function() {

		if(this.value.replace(/\s/g, "") === "") {
		   // hide button
		   jQuery("#testURL").removeAttr('href');
		   jQuery("#testURL").addClass('disabled');
		} else {
			//show test button, set its href value
			jQuery("#testURL").removeClass('disabled');
			jQuery("#testURL").css( 'cursor', 'pointer' );
		    jQuery("#testURL").attr("href", jQuery("#wMediaURL").val());
		   
		}
	});	

});
