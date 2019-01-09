<?php

# -----------------------------------------------------------------
# Audio and Video management
# -----------------------------------------------------------------

function splotbox_get_audioplayer( $url ) {
	// output the  audio player
	
	$audioplayer = '
<audio controls="controls" class="audio-player">
	<source src="' . $url . '" />
</audio>' . "\n";
	return ($audioplayer);
}

function splotbox_get_videoplayer( $url ) {
	// output the  video player
	
	if ( is_in_url( 'archive.org', $url ) ) {
	
		$archiveorg_url = str_replace ( 'details' , 'embed' , $url );
	
		$videoplayer = '<iframe src="' . $archiveorg_url . '" width="640" height="480" frameborder="0" webkitallowfullscreen="true" mozallowfullscreen="true" allowfullscreen></iframe>';
	
	} elseif  ( is_in_url( 'spark.adobe.com/video/', $url ) ) {
			
		// get string position right before ID
		$pos = strpos( $url, 'video/');
		$spark_id = substr($url, $pos + 6) ;
		$spark_id = rtrim( $spark_id, '/');
		
		// spark video iframe code
		$videoplayer = '<iframe src="https://spark.adobe.com/video/' . $spark_id . '/embed"  width="960" height="540" frameborder="0" allowfullscreen></iframe>';

	} elseif  ( is_in_url( 'spark.adobe.com/page/', $url ) ) {
	
		// get string position right before ID
		$pos = strpos( $url, 'page/');
		$spark_id = substr($url, $pos + 5) ;
		$spark_id = rtrim( $spark_id, '/');
		
		// spark page embed code
		$videoplayer = '<script id="asp-embed-script" data-zindex="1000000" type="text/javascript" charset="utf-8" src="https://spark.adobe.com/page-embed.js"></script><a class="asp-embed-link" href="https://spark.adobe.com/page/' . $spark_id . '/" target="_blank"><img src="https://spark.adobe.com/page/' . $spark_id . '/embed.jpg" alt="" style="width:100%" border="0" /></a>';
	
	} else {
		// general videoplayer for mp4 video (does not seem to work, but hey, future coding here
		$videoplayer = '
<video controls="controls" class="video-player">
	<source src="' . $url . '" type="video/mp4" />
</video>' . "\n";

	}
	
	return ($videoplayer);
}


function url_is_media_type ( $url ) {

	// check for video
 	if ( url_is_video ( $url ) ) return 'video';
	// check  for audio
	if ( url_is_audio ( $url ) ) return 'audio';

}


function url_is_audio ( $url ) {
// tests urls to see if they point to an audio type

	if ( is_in_url( 'soundcloud.com', $url ) or url_is_audio_link( $url ) ) return true;
	
}

function url_is_audio_link ( $url ) {

	$fileExtention 	= pathinfo ( $url, PATHINFO_EXTENSION ); 	// get file extension for url	
	$allowables 	= 	array( 'mp3', 'm4a', 'ogg'); 	// allowable file extensions
	
	// check the url file extension to ones we will allow
	return ( in_array( strtolower( $fileExtention) ,  $allowables  ) );
}

function url_is_video_link ( $url ) {

	$fileExtention 	= pathinfo ( $url, PATHINFO_EXTENSION ); 	// get file extension for url	
	$allowables 	= 	array( 'mp4'); 	// allowable file extensions
	
	// check the url file extension to ones we will allow
	return ( in_array( strtolower( $fileExtention) ,  $allowables  ) );
}


// check if $url contacts a string (like domain name) 
function is_in_url ( $pattern, $url ) {

	if ( strpos( $url, $pattern) === false ) {
		return (false);
	} else {
		return (true);
	}
}

function url_is_video ( $url ) {

	$allowables = array(
					'youtube.com/watch?',
					'youtu.be',
					'vimeo.com',
					'archive.org',
					'spark.adobe.com/page',
					'spark.adobe.com/video'
	);

	// walk the array til we get a match
	foreach( $allowables as $fragment ) {
  		if  (strpos( $url, $fragment ) !== false ) {
			return ( true );
		}
	}	
	
	// see if it is a link to a valid video format
	if  ( url_is_video_link ( $url ) ) return true;
	
	// no matches, not a video for you
	return ( false );
}

function is_url_embeddable( $url ) {
// test if URL matches the ones that Wordpress can do oembed on
// test by by string matching
	
	$allowed_embeds = array(
					'youtube.com/watch?',
					'youtu.be',
					'vimeo.com', 
					'soundcloud.com'
	);
	
	// walk the array til we get a match
	foreach( $allowed_embeds as $fragment ) {
  		if  (strpos( $url, $fragment ) !== false ) {
			return ( true );
		}
	}	
	
	// no matches, no embeds for you
	return ( false );
}

function get_media_embedded ( $url ) {

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
		
	} else {
	
		return ('');
	}
} 

?>