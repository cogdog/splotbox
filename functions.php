<?php

// run when this theme is activated
add_action('after_switch_theme', 'splotbox_setup');

function splotbox_setup () {
  // make sure our categories are present
  
  // create pages if they do not exist
  
  if (! get_page_by_path( 'share' ) ) {
  
  	// create the Write page if it does not exist
  	$page_data = array(
  		'post_title' 	=> 'Share',
  		'post_content'	=> 'Share some media into the box',
  		'post_name'		=> 'share',
  		'post_status'	=> 'publish',
  		'post_type'		=> 'page',
  		'post_author' 	=> 1,
  		'post_date' 	=> date('Y-m-d H:i:s'),
  		'page_template'	=> 'page-collect.php',
  	);
  	
  	wp_insert_post( $page_data );
  
  }

  if (! get_page_by_path( 'desk' ) ) {

  	// create the Write page if it does not exist
  	$page_data = array(
  		'post_title' 	=> 'Welcome Desk',
  		'post_content'	=> 'Welcome to the place to add your media gems to this collection.',
  		'post_name'		=> 'desk',
  		'post_status'	=> 'publish',
  		'post_type'		=> 'page',
  		'post_author' 	=> 1,
  		'post_date' 	=> date('Y-m-d H:i:s'),
  		'page_template'	=> 'page-desk.php',
  	);
  	
  	wp_insert_post( $page_data );
  
  }

  if (! get_page_by_path( 'random' ) ) {

  	// create the Write page if it does not exist
  	$page_data = array(
  		'post_title' 	=> 'Random',
  		'post_content'	=> '(Place holder for random page)',
  		'post_name'		=> 'random',
  		'post_status'	=> 'publish',
  		'post_type'		=> 'page',
  		'post_author' 	=> 1,
  		'post_date' 	=> date('Y-m-d H:i:s'),
  		'page_template'	=> 'page-random.php',
  	);
  	
  	wp_insert_post( $page_data );
  
  }
   
}


# -----------------------------------------------------------------
# Set up the table and put the napkins out
# -----------------------------------------------------------------

// we need to load the options this before the auto login so we can use the pass
add_action( 'after_setup_theme', 'splotbox_load_theme_options', 9 );

// change the name of admin menu items from "New Posts"
// -- h/t http://wordpress.stackexchange.com/questions/8427/change-order-of-custom-columns-for-edit-panels
// and of course the Codex http://codex.wordpress.org/Function_Reference/add_submenu_page

add_action( 'admin_menu', 'splotbox_change_post_label' );
add_action( 'init', 'splotbox_change_post_object' );

function splotbox_change_post_label() {
    global $menu;
    global $submenu;
    
    $thing_name = 'Item';
    
    $menu[5][0] = $thing_name . 's';
    $submenu['edit.php'][5][0] = 'All ' . $thing_name . 's';
    $submenu['edit.php'][10][0] = 'Add ' . $thing_name;
    $submenu['edit.php'][15][0] = $thing_name .' Categories';
    $submenu['edit.php'][16][0] = $thing_name .' Tags';
    echo '';
}
function splotbox_change_post_object() {

    $thing_name = 'Item';

    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $labels->name =  $thing_name . 's';;
    $labels->singular_name =  $thing_name;
    $labels->add_new = 'Add ' . $thing_name;
    $labels->add_new_item = 'Add ' . $thing_name;
    $labels->edit_item = 'Edit ' . $thing_name;
    $labels->new_item =  $thing_name;
    $labels->view_item = 'View ' . $thing_name;
    $labels->search_items = 'Search ' . $thing_name;
    $labels->not_found = 'No ' . $thing_name . ' found';
    $labels->not_found_in_trash = 'No ' .  $thing_name . ' found in Trash';
    $labels->all_items = 'All ' . $thing_name;
    $labels->menu_name =  $thing_name;
    $labels->name_admin_bar =  $thing_name;
}

add_filter('comment_form_defaults', 'splotbox_comment_mod');

function splotbox_comment_mod( $defaults ) {
	$defaults['title_reply'] = 'Provide Feedback';
	$defaults['logged_in_as'] = '';
	$defaults['title_reply_to'] = 'Provide Feedback for %s';
	return $defaults;
}


/* add audio post format to the mix */

add_action( 'after_setup_theme', 'splotbox_formats', 11 );

function splotbox_formats(){
     add_theme_support( 'post-formats', array( 'audio', 'video', 'aside', 'gallery', 'image', 'link', 'quote' ) );
}


// options for post order on front page
add_action( 'pre_get_posts', 'splotbox_order_items' );

function splotbox_order_items( $query ) {

	// just the main, please
	if ( $query->is_main_query() ) {

		// change sort order on home, archives, or search results
		if (  $query->is_home()  OR $query->is_archive() OR $query->is_search() ) {
	
			$query->set( 'orderby', splotbox_option('sort_by')  );
			$query->set( 'order', splotbox_option('sort_direction') );
		
		}
	}
}


# -----------------------------------------------------------------
# Options Panel for Admin
# -----------------------------------------------------------------

// -----  Add admin menu link for Theme Options
add_action( 'wp_before_admin_bar_render', 'splotbox_options_to_admin' );

function splotbox_options_to_admin() {
    global $wp_admin_bar;
    
    // we can add a submenu item too
    $wp_admin_bar->add_menu( array(
        'parent' => '',
        'id' => 'splotbox-options',
        'title' => __('Splotbox Options'),
        'href' => admin_url( 'themes.php?page=splotbox-options')
    ) );
}


function splotbox_enqueue_options_scripts() {
	// Set up javascript for the theme options interface
	
	// media scripts needed for wordpress media uploaders
	// wp_enqueue_media();
	
	// custom jquery for the options admin screen
	wp_register_script( 'splotbox_options_js' , get_stylesheet_directory_uri() . '/js/jquery.splotbox-options.js', null , '1.0', TRUE );
	wp_enqueue_script( 'splotbox_options_js' );
	
}

function splotbox_load_theme_options() {
	// load theme options Settings

	if ( file_exists( get_stylesheet_directory()  . '/class.splotbox-theme-options.php' ) ) {
		include_once( get_stylesheet_directory()  . '/class.splotbox-theme-options.php' );		
	}
}


# -----------------------------------------------------------------
# login stuff
# -----------------------------------------------------------------

// Add custom logo to entry screen... because we can
// While we are at it, use CSS to hide the back to blog and retried password links
add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_logo() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/site-login-logo.png);
            padding-bottom: 30px;
        }    
	#backtoblog {display:none;}
	#nav {display:none;}
    </style>
<?php }


// Make logo link points to blog, not Wordpress.org Change Dat
// -- h/t http://www.sitepoint.com/design-a-stylized-custom-wordpress-login-screen/

add_filter( 'login_headerurl', 'login_link' );

function login_link( $url ) {
	return get_bloginfo( 'url' );
}
 
 
// Auto Login
// create a link that can automatically log in as a specific user, bypass login screen
// -- h/t  http://www.wpexplorer.com/automatic-wordpress-login-php/

add_action( 'after_setup_theme', 'splotbox_autologin');

function splotbox_autologin() {
	
	// URL Paramter to check for to trigger login
	if ( isset($_GET['autologin'] ) AND $_GET['autologin'] == 'sharer') {
	
		// ACCOUNT USERNAME TO LOGIN TO
		$creds['user_login'] = 'sharer';
		
		// ACCOUNT PASSWORD TO USE- stored as option
		$creds['user_password'] = splotbox_option('pkey');
			
		$creds['remember'] = true;
		$autologin_user = wp_signon( $creds, is_ssl() );
		
		if ( !is_wp_error($autologin_user) ) {
				wp_redirect ( site_url() . '/share' );
		} else {
				die ('Bad news! login error: ' . $autologin_user->get_error_message() );
		}
	}
}

// remove admin tool bar for non-admins, remove access to dashboard
// -- h/t http://www.wpbeginner.com/wp-tutorials/how-to-disable-wordpress-admin-bar-for-all-users-except-administrators/

add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
	if ( !current_user_can('edit_others_posts')  ) {
	  show_admin_bar(false);
	}

}

# -----------------------------------------------------------------
# Licensed to License
# -----------------------------------------------------------------


function splotbox_get_licences() {
	// return as an array the types of licenses available 
	
	return ( array (
				'c' => 'All Rights Reserved (copyrighted)',
				'u' => 'Unknown / Not Specified',
				'pd'	=> 'Public Domain',
				'yt'	=> 'YouTube Standard License',
				'cc0'	=> 'CC0 No Rights Reserved',
				'cc-by' => 'CC BY Creative Commons By Attribution',
				'cc-by-sa' => 'CC BY SA Creative Commons Attribution-ShareAlike',
				'cc-by-nd' => 'CC BY ND Creative Commons Attribution-NoDerivs',
				'cc-by-nc' => 'CC BY NC Creative Commons Attribution-NonCommercial',
				'cc-by-nc-sa' => 'CC BY NC SA Creative Commons Attribution-NonCommercial-ShareAlike',
				'cc-by-nc-nd' => 'CC By NC ND Creative Commons Attribution-NonCommercial-NoDerivs',
			)
		);
}


function splotbox_the_license( $lcode ) {
	// output the title of a license
	$all_licenses = splotbox_get_licences();
	
	echo $all_licenses[$lcode];
}

function splotbox_attributor( $license, $work_title, $work_link, $work_creator='') {

	$all_licenses = splotbox_get_licences();
		
	$work_str = ( $work_creator == '') ? '"' . $work_title . '"' : '"' . $work_title . '" by ' . $work_creator;
	
	$work_str_html = ( $work_creator == '') ? '<a href="' . $work_link .'">"' . $work_title . '"</a>' : '<a href="' . $work_link .'">"' . $work_title . '"</a> by ' . $work_creator;
	
	
	
	switch ( $license ) {

		case 'c': 	
			return ( array( 
						$work_str .  '" is &copy; All Rights Reserved.', 
						$work_str_html . '" is &copy; All Rights Reserved.'
					)
			 );
			break;


		case 'u': 	
			return ( array( 
						$work_str .  '" is unknown or not specified.', 
						$work_str_html . '" is unknown or not specified.'
					)
			 );
			break;

		case 'yt': 	
			return ( array( 
						$work_str .  '" is covered by a YouTube Standard License.', 
						$work_str_html . '" is covered by a <a href="https://www.youtube.com/t/terms">YouTube Standard License</a>.' 
					)
			 );
			break;
		
		case 'cc0':
			return ( array( 
						$work_str . ' is made available under the Creative Commons CC0 1.0 Universal Public Domain Dedication.',
						$work_str_html .  ' is made available under the <a href="https://creativecommons.org/publicdomain/zero/1.0/">Creative Commons CC0 1.0 Universal Public Domain Dedication</a>.'	
					)
			 );
		
			break;
	
		case 'pd':
			return ( array( 
				$work_str . ' has been explicitly released into the public domain.',
				$work_str_html . ' has been explicitly released into the public domain.'
				)
			 );
			break;
		
		default:
			//find position in license where name of license starts
			$lstrx = strpos( $all_licenses[$license] , 'Creative Commons');
		
			return ( array( 
					$work_str . ' is licensed under a ' .  substr( $all_licenses[$license] , $lstrx)  . ' 4.0 International license.',
					$work_str_html . ' is licensed under a <a href="https://creativecommons.org/licenses/' . $license . '/4.0/">' .  substr( $all_licenses[$license] , $lstrx)  . ' 4.0 International</a> license.'		
				)
			 );
	}


}

# -----------------------------------------------------------------
# Enqueue Scipts and Styles
# -----------------------------------------------------------------


add_action('wp_enqueue_scripts', 'add_splotbox_scripts');

function add_splotbox_scripts() {	 
    $parent_style = 'garfunkel_style'; 
    
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );

 	// use these scripts just our form page
 	if ( is_page('share') ) { 
    
		 // add media scripts if we are on our maker page and not an admin
		 // after http://wordpress.stackexchange.com/a/116489/14945
    	 
		if (! is_admin() ) wp_enqueue_media();
		
		// Build in tag auto complete script
   		wp_enqueue_script( 'suggest' );

		// custom jquery for the uploader on the form
		wp_register_script( 'jquery.splotbox' , get_stylesheet_directory_uri() . '/js/jquery.splotbox.js', null , '1.0', TRUE );
		wp_enqueue_script( 'jquery.splotbox' );
		
	}

}

// create a basic menu if one has not been define for primary
function splot_default_menu() {

	// site home with trailing slash
	$splot_home = home_url('/');
  
 	return ( '<li><a href="' . $splot_home . '">Home</a></li><li><a href="' . $splot_home . 'share' . '">Share</a></li><li><a href="' . $splot_home . 'random' . '">Random</a></li>' );
  
}



# --------------------------------------



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
	
	$videoplayer = '
<video controls="controls" class="video-player">
	<source src="' . $url . '" type="video/mp4" />
</video>' . "\n";
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
					'vimeo.com'
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
					'soundcloud.com',
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


function splotbox_author_user_check( $expected_user = 'sharer' ) {
// checks for the proper authoring account set up

	$auser = get_user_by( 'login', $expected_user );
		
	
	if ( !$auser) {
		return ('Authoring account not set up. You need to <a href="' . admin_url( 'user-new.php') . '">create a user account</a> with login name <strong>' . $expected_user . '</strong> with a role of <strong>Author</strong>. Make a killer strong password; no one uses it.');
	} elseif ( $auser->roles[0] != 'author') {
	
		// for multisite lets check if user is not member of blog
		if ( is_multisite() AND !is_user_member_of_blog( $auser->ID, get_current_blog_id() ) )  {
			return ('The user account <strong>' . $expected_user . '</strong> is set up but has not been added as a user to this site (and needs to have a role of <strong>Author</strong>). You can <a href="' . admin_url( 'user-edit.php?user_id=' . $auser->ID ) . '">edit it now</a>'); 
			
		} else {
		
			return ('The user account <strong>' . $expected_user . '</strong> is set up but needs to have it\'s role set to <strong>Author</strong>. You can <a href="' . admin_url( 'user-edit.php?user_id=' . $auser->ID ) . '">edit it now</a>'); 
		}
		
		
		
	} else {
		return ('The authoring account <strong>' . $expected_user . '</strong> is correctly set up.');
	}
}


function splotbox_check_user( $allowed='sharer' ) {
	// checks if the current logged in user is who we expect
    
   $current_user = wp_get_current_user();
	
	// return check of match
	return ( $current_user->user_login == $allowed );
}

function splot_the_author() {
	// utility to put in template to show status of special logins
	// nothing is printed if there is not current user, 
	//   echos (1) if logged in user is the special account
	//   echos (0) if logged in user is the another account
	//   in both cases the code is linked to a logout script

	if ( is_user_logged_in() and !current_user_can( 'edit_others_posts' ) ) {
		$user_code = ( splotbox_check_user() ) ? 1 : 0;
		echo '<a href="' . wp_logout_url( site_url() ). '">(' . $user_code  .')</a>';
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