function wp_tags( taglist ) {
	var mystr = '';
	
	if (taglist === undefined) return '';
	
	var tagarray = taglist.split(',');
		
	for (i = 0; i < tagarray.length; i++) { 
		mystr += '<a href="#" rel="tag" class="label" onClick="return false;">' + tagarray[i] + '</a>, ';
    }
    return (mystr.substr(0, mystr.length-2)); 
}

function capitalizeEachWord(str) {
    return str.replace(/\w\S*/g, function(txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}

function decodeEntities(input) {
  var y = document.createElement('textarea');
  y.innerHTML = input;
  return y.value;
}

function get_all_licenses() {
	return {
				'--' : 'No license selected',
				'u': 'Rights Status Unknown',
				'c': 'All Rights Reserved (copyrighted)',
				'pd'	: 'Public Domain',
				'cc0'	: 'CC0 No Rights Reserved',
				'yt'	: 'YouTube Standard License',
				'cc-by': 'CC BY Creative Commons By Attribution',
				'cc-by-sa': 'CC BY SA Creative Commons Attribution-ShareAlike',
				'cc-by-nd': 'CC BY ND Creative Commons Attribution-NoDerivs',
				'cc-by-nc' : 'CC BY NC Creative Commons Attribution-NonCommercial',
				'cc-by-nc-sa' : 'CC BY NC SA Creative Commons Attribution-NonCommercial-ShareAlike',
				'cc-by-nc-nd' : 'CC By NC ND Creative Commons Attribution-NonCommercial-NoDerivs',
			};
}


function getLicenseName(lc) {

	var the_licenses = get_all_licenses();
	return ( the_licenses[lc]) ;
}

function get_attribution( license, work_title, work_creator) {
	// create an attribution string for the license

	var the_licenses = get_all_licenses();

	
	if ( work_creator == '') {
		var work_str =  '"' + work_title + '"';
	} else {
		var work_str = '"' + work_title + '" by or via "' + work_creator  + '" '
	}
		
	
	switch ( license ) {
	
		case '--': 
			return ( work_str +  '" license status: not selected.' );
			break;
			
		case '?': 	
			return ( work_str +  '" license status: unknown.' );
			break;
			
		case 'u': 	
			return ( work_str +   '" license status: unknown.' );
			break;
			
		case 'c': 	
			return ( work_str +  '" is &copy; All Rights Reserved.' );
			break;

		case 'yt': 	
			return ( work_str +  '" is made available under a YouTube Standard License.' );
			break;
		
		case 'cc0':
			return ( work_str +  ' is made available under the Creative Commons CC0 1.0 Universal Public Domain Dedication.');
			break;
	
		case 'pd':
			return ( work_str +  ' has been explicitly released into the public domain.');
			break;
		
		default:
			//find position in license where name of license starts
			var lstrx = the_licenses[license].indexOf('Creative Commons');
			
			return ( work_str + ' is licensed under a ' +  the_licenses[license].substring(lstrx)  + ' 4.0 International license.');
	}
}


function replaceURLWithHTMLLinks(text) {
	// h/t http://stackoverflow.com/a/19548526/2418186
    var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    return text.replace(exp,"<a href='$1'>$1</a>"); 
}

function nl2br (str, is_xhtml) {
	// h/t http://stackoverflow.com/a/7467863/2418186
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}


(function($) {
 
    // Initialize the Lightbox for any links with the 'fancybox' class
	$(".fancybox").fancybox({

		fitToView	: false,
		maxWidth	: 0.85 * window.innerWidth,
		autoHeight	: true,
		autoSize	: false,
		closeClick	: false,
		openEffect  : 'fade',
		closeEffect : 'fade',
		scrolling   : 'yes',
		afterLoad   : function() {

			// flag for the editor type
			if ( $('#wRichText').val() == 1) {
				// get content from tinymce editor
				
				// using visual editor?
				if ( $("#wp-wTextHTML-wrap").hasClass("tmce-active") ){
					wtext =  tinymce.get('wTextHTML').getContent();
					
				// using HTML editor
				} else {
					wtext =  $('#wTextHTML').val();
				}
				
			} else {
				// get content from textarea for plain caption
				wtext =  $('#wText').val();
			}


					
			if ( $('#wTags').val() == '') {
				tagd = ' ';
			} else {
				tagd = ', ';
			}

         	var wcats = [];

           $('input[name="wCats[]"]:checked').each(function() { 
           		wcats.push($(this).attr('data-checkbox-text'));
            });
            
            // make array from the two random titles
            var wrand_titles = $('#wRandTitles').val().split("|");

			
			// build output
			var wOutput = '<div class="post-container" style="width:100%"><div class="post hentry"><div class="featured-media">' + $('#embedMedia').val() + '</div><div class="post-inner"><div class="post-header"><p class="post-date">' + moment().format('MMMM D, YYYY')  + '</p><h1 class="post-title">' + $('#wTitle').val() + '</h1></div><div class="post-content"><p>' + wtext +'</p><p><strong>Shared by </strong>' + $('#wAuthor').val() + '<br>' ;
					
			if ( $('#wSource').val()) wOutput += '<strong>Media Credit:</strong> ' +  replaceURLWithHTMLLinks($('#wSource').val()) + '<br />';
			
			if ( $('#wLicense').val()) wOutput += '<strong>Reuse License:</strong> ' +  getLicenseName( $('#wLicense').val() )  + '<br />';			
			
			if ( $('#wAttributionPreview').val() == 1) wOutput += '<strong>Attribution Text:</strong><br /><textarea rows="2" onClick="this.select()" style="height:110px;">' +  get_attribution( $('#wLicense').val(), $('#wTitle').val(), $('#wSource').val()) + '</textarea><br />';
			
			wOutput += '<div class="clear"></div></div><div class="post-meta bottom"><div class="tab-selector"><ul><li><a class="active tab-post-meta-toggle" href="#"  onClick="return false;"><div class="genericon genericon-summary"></div><span>Item Info</span></a></li><div class="clear"></div></ul></div><div class="post-meta-tabs"><div class="post-meta-tabs-inner"><div class="tab-post-meta tab"><ul class="post-info-items fright">';
			
			if ( $('#wAuthor').val()) wOutput += '<li> <div class="genericon genericon-user"></div>' + $('#wAuthor').val() + '</li><li><div class="genericon genericon-time"></div><a href="#" onClick="return false;">' + moment().format('MMMM D, YYYY') + '</a></li>';

			if ( $('#wSource').val()) wOutput +=  '<li> <div class="genericon genericon-info"></div>' + $('#wSource').val() + '</li>';	
										
											
			if ( $('#wMediaLink').val() ) wOutput += '<li> <div class="genericon genericon-link"></div><a href="#"  onClick="return false;">' + $('#wMediaLink').val() + '</a></li>';
											

			if ( $('#wLicense').val()) wOutput += '<li> <div class="genericon genericon-flag"></div>' +  getLicenseName( $('#wLicense').val() )  + '</li>';	

			// doth we have categories?
			if (wcats.length) {
				wOutput += '<li> <div class="genericon genericon-category"></div> ' + wcats.join(", ") + '</li>';
			}

			// doth we have tags?
			if ($('#wTags').val()) wOutput += '<li><div class="genericon genericon-tag"></div>  ' + wp_tags( $('#wTags').val() ) + '</li>';
			
			wOutput += '</ul><div class="post-nav fleft"><a class="post-nav-prev" href="#"  onClick="return false;"><p>Previous Item</p><h4>' + wrand_titles[0] + '</h4></a><a class="post-nav-next" href="#"  onClick="return false;"><p>Next Item</p><h4>' + wrand_titles[1] + '</h4></a></div><div class="clear"></div></div></div></div></div></div>';
										
			this.content = wOutput;
			
		},
		helpers : {
			title: {
				type: 'outside',
				position: 'top'
			}
    	},
	}); 
	
})(jQuery);