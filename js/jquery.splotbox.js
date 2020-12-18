/* SPLOTbox Scripts
   code by Alan Levine @cogdog http://cog.dog

	upload for input field style by CSS to be a drop zone
*/

// valid file extensions for image and audio
var image_exts = ['jpg' ,'jpeg', 'png' ,'gif'];
var audio_exts = ['mp3' , 'm4a', 'ogg'];

// all allowed extensions, combine above
var allowables = image_exts.concat(audio_exts);

// type of upload radio buttons
var upload_types = ['by_url', 'by_upload', 'by_recorder'];

function isAllowedFile(fname) {
	// get extension -- h/t https://stackoverflow.com/a/190878/2418186
	 var ext = fname.split('.').pop();

	 // check in array
	 return allowables.includes(ext.toLowerCase());
}

function isImageFile(fname) {
	// get extension -- h/t https://stackoverflow.com/a/190878/2418186
	 var ext = fname.split('.').pop();

	 // check in array
	 return image_exts.includes(ext.toLowerCase());
}

function isAudioFile(fname) {
	// get extension -- h/t https://stackoverflow.com/a/190878/2418186
	 var ext = fname.split('.').pop();

	 // check in array
	 return audio_exts.includes(ext.toLowerCase());
}

jQuery('#wTags').suggest( boxObject.ajaxUrl + "?action=ajax-tag-search&tax=post_tag", {multiple:true, multipleSep: ","});


jQuery(document).ready(function() {

	jQuery('#splotdropzone input').change(function () {

		if (this.value) {
			// check for errors
			var error_str = '';

			// image files only allowed
			if ( boxObject.allowedMedia == 2 &&  !isImageFile(this.value.substring(12)) ) {
				 error_str = 'The selected file "' + this.value.substring(12) + '" is not an image file. Try again?';
			}

			// audio files only allowed
			if ( boxObject.allowedMedia == 3 &&  !isAudioFile(this.value.substring(12)) ) {
				 error_str = 'The selected file "' + this.value.substring(12) + '" is not an audio file. Try again?';
			}

			// check the file size
			let file_size_MB = (this.files[0].size / 1000000.0).toFixed(2);

			if ( file_size_MB >  parseFloat(boxObject.uploadMax)) {
				error_str = 'The size of your file, ' + file_size_MB + ' Mb, is greater than the maximum allowed for this site (' + boxObject.uploadMax + ' Mb). Try a different file or see if you can shrink the size of this one.';
			}

			// we have no errors
			if (!error_str) {

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
						jQuery("#uploadresponse").html('File selected. When you <strong>Save/Update</strong> below this file will be uploaded (' + file_size_MB + ' Mb).');

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

			} else {
				// errors!

				jQuery('#wUploadMedia').val("");
				alert('Error!' + error_str);
				reset_dropzone();

			} // !error_str


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

    // disable preview button if anything changes, forces a re-check
    jQuery("input" ).change(function() {
    	jQuery("#wPreview").addClass('disabled');
	});

	// toggle upload modes
	jQuery("input[type=radio][name=wMediaMethod]" ).click(function() {

		// cycle through our array of possible media types
		upload_types.forEach (
			uploadhow => {
				// this is the clicked button
  				if (uploadhow == this.value) {
  					// show the matching div

  					// for media recorder we have to toggle visibility
  					if (uploadhow == "by_recorder") {
  					    jQuery("#wUploadRecording").css("display", "none");
  					    jQuery("#media_by_recorder").css("visibility", "visible");
  					} else {
  					    // other stuff we can show

  					     jQuery("#media_" + uploadhow).show("slow");
  					}

  				} else {
  					// hide the div

  					// for media recorder we have to toggle visibility
  					if (uploadhow == "by_recorder") {
  					    jQuery("#media_by_recorder").css("visibility", "hidden");
  					 } else {
                // other stuff we can hide
                jQuery("#media_" + uploadhow).hide();
             }
  				}
			});
	});

	// show test url button if url field changed
	jQuery("#wMediaURL").change(function() {

		if(this.value.replace(/\s/g, "") === "") {
		   // hide button
		   jQuery("#testURL").removeAttr('href');
		   jQuery("#testURL").addClass('disabled');
		   jQuery("#alt_by_link").hide();
		} else {
			//show test button, set its href value
			jQuery("#testURL").removeClass('disabled');
			jQuery("#testURL").css( 'cursor', 'pointer' );
		  jQuery("#testURL").attr("href", jQuery("#wMediaURL").val());
		}
	});

});
