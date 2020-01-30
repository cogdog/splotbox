/* Javascript code for theme options editing
   code by Alan Levine @cogdog http://cogdog.info

*/

jQuery(document).ready(function() {


			jQuery("input[type=text], textarea").each(function() {
				if (jQuery(this).val() == jQuery(this).attr("placeholder") || jQuery(this).val() == "")
					jQuery(this).css("color", "#999");
			});

			jQuery("input[type=text], textarea").focus(function() {
				if (jQuery(this).val() == jQuery(this).attr("placeholder") || jQuery(this).val() == "") {
					jQuery(this).val("");
					jQuery(this).css("color", "#000");
				}
			}).blur(function() {
				if (jQuery(this).val() == "" || jQuery(this).val() == jQuery(this).attr("placeholder")) {
					jQuery(this).val(jQuery(this).attr("placeholder"));
					jQuery(this).css("color", "#999");
				}
			});

			// This will make the "warning" checkbox class really stand out when checked.
			// I use it here for the Reset checkbox.
			jQuery(".warning").change(function() {
				if (jQuery(this).is(":checked"))
					jQuery(this).parent().css("background", "#c00").css("color", "#fff").css("fontWeight", "bold");
				else
					jQuery(this).parent().css("background", "none").css("color", "inherit").css("fontWeight", "normal");
			});


    	// makes the media checkboxes sync to the "Check all" one
      jQuery("#mtoggle").click(function(){
        jQuery('input.mtype[type=checkbox]').not(this).prop('checked', this.checked);
      });


      // if all media types selected, update the select all
      function checkAllMediaTypes() {
        if ( jQuery('input.mtype[type=checkbox]').not(':checked').length === 0 ) {
          jQuery("#mtoggle").prop('checked', true);
        }
      }

      // if any category checkbox is de-selected we turn off the select all one
      function toggleMediaTypesOff(obj) {
        if (! jQuery(obj).prop('checked')) jQuery("mtoggle").prop('checked', false);
      }

      // manage state of media types, if checked
      jQuery('input.mtype[type=checkbox]').click(function(){
        checkAllMediaTypes();
        toggleMediaTypesOff(this);

      });

      // run a check on each load
      checkAllMediaTypes();

});
