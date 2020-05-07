<?php
# -----------------------------------------------------------------
# Page and Template Checks
# -----------------------------------------------------------------
function page_with_template_exists ( $template ) {
	// returns true if at least one Page exists that uses given template

	// look for pages that use the given template
	$seekpages = get_posts (array (
				'post_type' => 'page',
				'meta_key' => '_wp_page_template',
				'meta_value' => $template
			));
	 
	// did we find any?
	$pages_found = ( count ($seekpages) ) ? true : false ;
	
	// report to base
	return ($pages_found);
}

function get_pages_with_template ( $template ) {
	// returns array of pages with a given template
	
	// look for pages that use the given template
	$seekpages = get_posts (array (
				'post_type' => 'page',
				'meta_key' => '_wp_page_template',
				'meta_value' => $template,
				'posts_per_page' => -1
	));
	
	// holder for results
	$tpages = array(0 => 'Select Page');
	

	// Walk those results, store ID of pages found
	foreach ( $seekpages as $p ) {
		$tpages[$p->ID] = $p->post_title;
	}
	
	return $tpages;
}

function splotbox_get_share_page() {

	// return sluf for page set in theme options for sharing page (newer versions of SPLOT)
	if ( splotbox_option( 'share_page' ) )  {
		return ( get_post_field( 'post_name', get_post( splotbox_option( 'share_page' ) ) ) ); 
	} else {
		// older versions of SPLOT use the slug
		return ('share');
	}
}

function splotbox_get_licensed_page() {

	// return sluf for page set in theme options for licenses page (newer versions of SPLOT)
	if ( splotbox_option( 'licensed_page' ) )  {
		return ( get_post_field( 'post_name', get_post( splotbox_option( 'licensed_page' ) ) ) ); 
	} else {
		// older versions of SPLOT use the slug
		return ('licensed');
	}
}

function splotbox_get_license_page_id() {

	// return slug for page set in theme options for view by license page (newer versions of SPLOT)
	if (  splotbox_option( 'licensed_page' ) ) {
		return ( splotbox_option( 'licensed_page' ) );
	} else {
		// older versions of SPLOT use the slug
		return ( get_page_by_path('licensed')->ID );
	}
}


function splot_redirect_url() {
	// where to send visitors after login ok
	return ( home_url('/') . splotbox_get_share_page() );
}

# -----------------------------------------------------------------
# Media
# -----------------------------------------------------------------

// return the maxium upload file size in omething more useful than bytes
function splotbox_max_upload() {
	$maxupload = wp_max_upload_size() / 1000000;	
	return ( round( $maxupload ) . ' Mb');

}
								
// function to get the caption for an attachment (stored as post_excerpt)
// -- h/t http://wordpress.stackexchange.com/a/73894/14945

function get_attachment_caption_by_id( $post_id ) {
    $the_attachment = get_post( $post_id );
    return ( $the_attachment->post_excerpt ); 
}

// for uploading images 
function splotbox_insert_attachment( $file_handler, $post_id ) {
	
	if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) return (false);

	require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
	require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
	require_once( ABSPATH . "wp-admin" . '/includes/media.php' );

	$attach_id = media_handle_upload( $file_handler, $post_id );
	
	return ($attach_id);
	
}


# -----------------------------------------------------------------
# Useful spanners and wrenches
# -----------------------------------------------------------------

function splotbox_preview_notice() {
	return ('<div class="notify"><span class="symbol icon-info"></span>
This is a preview of your entry that shows how it will look when published. <a href="#" onclick="self.close();return false;">Close this window/tab</a> when done to return to the sharing form. Make any changes and click "Revise Draft" again or if it is ready, click "Publish Now".		
				</div>');
}


function splotbox_get_two_items() {

	// set arguments for WP_Query on published posts to get 2 at random
	$args = array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'posts_per_page' => 2,
		'orderby' => 'rand'
	);

	// It's time! get some posts
	$rand_posts = get_posts( $args );

	// see if we got anything and if so, that we got 2 items
	if ($rand_posts and count($rand_posts) == 2) {
		foreach ( $rand_posts as $apost ) {
			$rand_titles[] = $apost->post_title;
		}
	
		return ($rand_titles);
	} else {
		// just in case we did not get enought
		return (array('It came from Canada!', 'SPLOT is your Best Friend') );
	}
}


function set_html_content_type() {
	// from http://codex.wordpress.org/Function_Reference/wp_mail
	return 'text/html';
}

function br2nl ( $string )
// from http://php.net/manual/en/function.nl2br.php#115182
{
    return preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $string);
}

function make_links_clickable( $text ) {
//----	h/t http://stackoverflow.com/a/5341330/2418186
    return preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1">$1</a>', $text);
}


# -----------------------------------------------------------------
# API
# -----------------------------------------------------------------


// -----  expose post meta date to API
add_action( 'rest_api_init', 'splotbox_create_api_posts_meta_field' );
 
function splotbox_create_api_posts_meta_field() {
 
	register_rest_field( 'post', 'splot_meta', array(
								 'get_callback' => 'splotbox_get_splot_meta_for_api',
 								 'schema' => null,)
 	);
}
 
function splotbox_get_splot_meta_for_api( $object ) {
	//get the id of the post object array
	$post_id = $object['id'];

	// meta data fields we wish to make available
	$splot_meta_fields = ['author' => 'wAuthor', 'license' => 'wLicense', 'footer' => 'wFooter'];
	
	// array to hold stuff
	$splot_meta = [];
 
 	foreach ($splot_meta_fields as $meta_key =>  $meta_value) {
	 	//return the post meta for each field
	 	$splot_meta[$meta_key] =  get_post_meta( $post_id, $meta_value, true );
	 }
	 
	 return ($splot_meta);
}


// shortcode for spitting out an RSS feed icon (ie for category dscriptions)

add_shortcode("feedicon", "splotbox_feed_icon");

function splotbox_feed_icon()  {
	return '<img src="' . get_stylesheet_directory_uri() . '/images/feed.png" alt=""> ';
}

?>