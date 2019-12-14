<?php

# -----------------------------------------------------------------
# Audio and Video management
# -----------------------------------------------------------------

function splotbox_supports() {
	/* return a comma separated text list of all support media sites that are 
	   supported by URL entry, either built in WordPress embeds our others
	   added to this theme */

	$supported_sites = array();
	
	// all possible supported sites that are built into the theme
	$all_sites = array(
		'm_spark' => 'Adobe Spark Pages/Videos',
		'm_flickr' => 'Flickr',
		'm_giphy' => 'Giphy',
		'm_archive' => 'Internet Archive', 
		'm_mixcloud' => 'Mixcloud',
		'm_slideshare' => 'Slideshare',
		'm_soundcloud' => 'Soundcloud', 
		'm_speakerdeck' => 'Speaker Deck',
		'm_ted' => 'TED Talks',
		'm_vimeo' => 'Vimeo', 
		'm_youtube' => 'YouTube', 
	);	
	
	// pull names of ones activated in theme options
	foreach ($all_sites as $key => $value ) {
		if ( splotbox_option( $key ) ) $supported_sites[] = $value;
	}
	
	// check for extras from the helper plugin
	if ( function_exists('splotboxplus_exists') ) {
		$supported_sites = array_merge( $supported_sites, splotboxplus_supports() );
	}
	
	// alphabetize it
	sort($supported_sites);
	
	// return text string, separated by commas
	return implode( ', ', $supported_sites);

}

function url_is_media_type ( $url ) {
	// via URL checks, identify the media types

	// check for video
 	if ( url_is_video ( $url ) ) return 'video';
	// check  for audio
	if ( url_is_audio ( $url ) ) return 'audio';
	// check  for image
	if ( url_is_image ( $url ) ) return 'image';
	// for some reason if we end up here, return nada
	return '';
}


function url_is_audio ( $url ) {
	// tests urls to see if they point to a supported audio type

	$allowables = array();
	
	// all possible supported sites
	$all_sites = array(
		'm_mixcloud' => 'mixcloud.com',
		'm_soundcloud' => 'soundcloud.com',
	);	
	
	// pull names of ones activated in theme options
	foreach ($all_sites as $key => $value ) {
		if ( splotbox_option( $key ) ) $allowables[] = $value;
	}
	
	// check for more audio types provided in extender plugin
	if ( function_exists('splotboxplus_exists') ) {
		$allowables = array_merge( $allowables, splotboxplus_audio_allowables() );
	}	
	
	// walk the array til we get a match for an embeddable player
	foreach( $allowables as $fragment ) {
		if ( is_in_url( $fragment, $url )) return ( true );
	}	
	
	// see if it is a link to a valid  format
	if  ( url_is_audio_link ( $url ) ) return true;
	
	// no matches, not an audio for you
	return ( false );
	
}

function url_is_video ( $url ) {
	// tests urls to see if they point to a supported video type

	$allowables = array();
	
	// all possible supported sites
	$all_sites = array(
		'm_spark' => 'spark.adobe.com/page',
		'm_giphy' => 'giphy.com',
		'm_archive' => 'archive.org', 
		'm_slideshare' => 'slideshare.net',
		'm_speakerdeck' => 'speakerdeck.com',
		'm_ted' => 'ted.com/talk',
 		'm_youtube' => 'youtube.com/watch?',
		'm_vimeo' => 'vimeo.com',
	);	
	
	// pull names of ones activated in theme options
	foreach ($all_sites as $key => $value ) {
		if ( splotbox_option( $key ) ) $allowables[] = $value;
		
		// more than one matching patterns
		if  (splotbox_option( $key ) == 'm_youtube') $allowables[] = 'youtu.be';
		if  (splotbox_option( $key ) == 'm_spark') $allowables[] = 'spark.adobe.com/video';
	}
	
	// check for more videos in extender plugin
	if ( function_exists('splotboxplus_exists') ) {
		$allowables = array_merge( $allowables, splotboxplus_video_allowables() );
	}
	
	// walk the array til we get a match
	foreach( $allowables as $fragment ) {
		if ( is_in_url( $fragment, $url ) ) return ( true );
	}
	
	// no matches, not a video for you
	return ( false );
}


function url_is_image ( $url ) {
	// tests urls to see if they point to a supported image type

	$allowables = array();
	
	// all possible supported sites
	$all_sites = array(
		'm_flickr' => 'flickr.com/photos',
	);	
	
	// pull names of ones activated in theme options
	foreach ($all_sites as $key => $value ) {
		if ( splotbox_option( $key ) ) $allowables[] = $value;
	}

	// check for more videos in extender plugin
	if ( function_exists('splotboxplus_exists') ) {
		$allowables = array_merge( $allowables, splotboxplus_image_allowables() );
	}
	
	// walk the array til we get a match
	foreach( $allowables as $fragment ) {
		if ( is_in_url( $fragment, $url ) ) return ( true );
	}	
	
	// see if it is a link to a valid  format
	if  ( url_is_image_link ( $url ) ) return true;
	
	// no matches, not an image for you
	return ( false );
}


/* --- check URLs for allowable links via URL (check file extension) ---               */

function url_is_audio_link ( $url ) {

	$fileExtention 	= pathinfo ( $url, PATHINFO_EXTENSION ); 	// get file extension for url	
	$allowables 	= 	array( 'mp3', 'm4a', 'ogg'); 			// allowable file extensions
	
	// check the url file extension to ones we will allow
	return ( in_array( strtolower( $fileExtention) ,  $allowables  ) );
}

function url_is_image_link ( $url ) {

	$fileExtention 	= pathinfo ( $url, PATHINFO_EXTENSION ); 	// get file extension for url	
	$allowables 	= 	array( 'jpg', 'jpeg', 'png', 'gif'); 	// allowable file extensions
	
	// check the url file extension to ones we will allow
	return ( in_array( strtolower( $fileExtention) ,  $allowables  ) );
}

function url_is_video_link ( $url ) {

	$fileExtention 	= pathinfo ( $url, PATHINFO_EXTENSION ); 	// get file extension for url	
	$allowables 	= 	array( 'mp4'); 	// allowable file extensions
	
	// check the url file extension to ones we will allow
	return ( in_array( strtolower( $fileExtention) ,  $allowables  ) );
}

function is_url_embeddable( $url ) {
// test if URL matches the ones that Wordpress can do oembed on
// test by by string matching


	$all_sites = array(
		'm_giphy' => 'giphy.com', 
		'm_slideshare' => 'slideshare.net',
		'm_speakerdeck' => 'speakerdeck.com',
		'm_ted' => 'ted.com/talk',
 		'm_youtube' => 'youtube.com/watch?',
		'm_vimeo' => 'vimeo.com',
		'm_soundcloud' => 'soundcloud.com',
		'm_flickr' => 'flickr.com/photos',
		'm_mixcloud' => 'mixcloud.com',
	);	
	
	$allowed_embeds = array();	
	
	// pull names of ones activated in theme options
	foreach ($all_sites as $key => $value ) {
	
		if ( splotbox_option( $key ) ) $allowed_embeds[] = $value;
		// more than one matching patterns
		if  (splotbox_option( $key ) == 'm_youtube') $allowables[] = 'youtu.be';
	}

	// add ones made available in extender plugin
	if ( function_exists('splotboxplus_exists') ) {
		$allowed_embeds = array_merge( $allowed_embeds, splotboxplus_embed_allowables() );
	}
	
	// walk the array til we get a match
	foreach( $allowed_embeds as $fragment ) {
		if ( is_in_url( $fragment, $url ) ) return ( true );
	}	
	
	// no matches, no embeds for you
	return ( false );
}

function get_media_embedded ( $url ) {
	/* For each media type get final embed codes, first check for native embeds, 
	   then try a player */

	if ( url_is_media_type ( $url ) == 'video' ) {
	
		if ( is_url_embeddable( $url ) ) {							

			// Use oEmbed for YouTube, et al
			return (wp_oembed_get( $url )); 
				
		}  else {
			// build player
			return splotbox_get_videoplayer( $url );
			
		}
	} elseif ( url_is_media_type ( $url ) == 'audio' ) {
	
		if ( is_url_embeddable( $url ) ) {	
			// Use oEmbed for SoundCloud, mp3 et al
			return (wp_oembed_get( $url )); 
		} else {
			return splotbox_get_audioplayer( $url );
		}
		
	} elseif ( url_is_media_type ( $url ) == 'image' ) {
	
		if ( is_url_embeddable( $url ) ) {	
			// Use oEmbed for flickr
			return (wp_oembed_get( $url )); 
		} else {
			return splotbox_get_imageplayer( $url );
		}
		
	} else {
	
		return ('');
	}
} 


function splotbox_get_audioplayer( $url ) {
	// audio player for files or ones added via plugin
	
	if ( url_is_audio_link( $url ) ) {
		// output the audio player for playing via URL
		return ('
	<audio controls="controls" class="audio-player">
		<source src="' . $url . '" />
	</audio>' . "\n");
	
	} elseif ( function_exists('splotboxplus_exists') ) {
		return splotboxplus_get_mediaplayer( $url );
	} else {
		return '';
	}

}

function splotbox_get_imageplayer( $url ) {
	// output the img code for displaying an image
	if ( url_is_image_link( $url ) ) {	
			return ('<img width="1140" height="760" src="' . $url . '" class="attachment-post-image size-post-image wp-post-image" alt="" />');
	} elseif ( function_exists('splotboxplus_exists') ) {
		return splotboxplus_get_imageplayer( $url );
	} else {
		return '';
	}
}


function splotbox_get_videoplayer( $url ) {
	// convert the video URL to a site specific iframe / player code
	
	$videoplayer = '';
	
	if ( is_in_url( 'archive.org', $url ) ) {
	
		// Internet Archive
	
		// use function to get media type via API
		$iamediatype =  splotbox_get_iarchive_type ( $url );

		$archiveorg_url = str_replace ( 'details' , 'embed' , $url );
	
		// use smaller player for audio
		if ($iamediatype == 'audio') {
			$videoplayer = '<iframe src="' . $archiveorg_url . '" width="100%" height="40" frameborder="0" webkitallowfullscreen="true" mozallowfullscreen="true" class="ia-audio" allowfullscreen></iframe>';
			
		} else {
			$videoplayer = '<iframe src="' . $archiveorg_url . '" width="100%" height="480" frameborder="0" webkitallowfullscreen="true" mozallowfullscreen="true" allowfullscreen class="ia-video"></iframe>';
		}
	
	} elseif  ( is_in_url( 'spark.adobe.com/video/', $url ) ) {
	
		// Adobe Spark Video
			
		// get string position right before ID
		$pos = strpos( $url, 'video/');
		$spark_id = substr($url, $pos + 6) ;
		$spark_id = rtrim( $spark_id, '/');
		
		// spark video iframe code
		$videoplayer = '<iframe src="https://spark.adobe.com/video/' . $spark_id . '/embed"  width="960" height="540" frameborder="0" allowfullscreen></iframe>';

	} elseif  ( is_in_url( 'spark.adobe.com/page/', $url ) ) {
	
		// Adobe Spark Page
	
		// get string position right before ID
		$pos = strpos( $url, 'page/');
		$spark_id = substr($url, $pos + 5) ;
		$spark_id = rtrim( $spark_id, '/');
		
		// spark page embed code
		$videoplayer = '<script id="asp-embed-script" data-zindex="1000000" type="text/javascript" charset="utf-8" src="https://spark.adobe.com/page-embed.js"></script><a class="asp-embed-link" href="https://spark.adobe.com/page/' . $spark_id . '/" target="_blank"><img src="https://spark.adobe.com/page/' . $spark_id . '/embed.jpg" alt="" style="width:100%" border="0" /></a>';

	} elseif ( function_exists('splotboxplus_exists') ) {
	
		// check for any plugin provided embeds
		
		$videoplayer = splotboxplus_get_mediaplayer( $url );
	} 
	
	return ( $videoplayer );
}

// check if $url contacts a string (like domain name) 
function is_in_url ( $pattern, $url ) {

	if ( strpos( $url, $pattern) === false ) {
		return (false);
	} else {
		return (true);
	}
}

function splotbox_get_iarchive_type ( $url )  {


	global $post;
	
	// get post status
	$post_status = get_post_status($post->ID);
	
	// look for post meta saved
	$ia_media_type = get_post_meta($post->ID, 'ia_media_type', 1);
	
	// If this item is published and we have a value, then return it. Cheap cache.
	if ( $post_status=='publish' AND $ia_media_type ) return  $ia_media_type;

	// strip any trailing '/'
	$url = rtrim( $url, '/');
	
	// find char position of id in URL after "details/"
	$pos = strpos( $url, 'details/');
	
	// extract ID
	$iaid = substr($url, $pos + 8);
	
	// construct URL for Internet Archive metadata API URL
	// see http://blog.archive.org/2013/07/04/metadata-api/
	$json_url = 'https://archive.org/metadata/' . $iaid . '/metadata';
	
	// get some json, jason!
	$json = file_get_contents( $json_url );
	
	if ( $json ) {
		$data = json_decode( $json, TRUE );
		// return the results from one item result, media type 
		
		// set post meta
		update_post_meta( $post->ID, 'ia_media_type', $data['result']['mediatype']);
		return ($data['result']['mediatype']);

	} else {
		// error, error, Jason
		return( false );
	}
}

?>