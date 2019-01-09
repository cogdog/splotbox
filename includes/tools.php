<?php
# -----------------------------------------------------------------
# Useful spanners and wrenches
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

	if ($rand_posts) {
		foreach ( $rand_posts as $apost ) {
			$rand_titles[] = $apost->post_title;
		}
	
		return ($rand_titles);
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

?>