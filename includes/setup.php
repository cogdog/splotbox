<?php

# -----------------------------------------------------------------
# Setup and intitialization
# -----------------------------------------------------------------


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
  		'page_template'	=> 'page-share.php',
  	);

  	wp_insert_post( $page_data );

  }


  if (! get_page_by_path( 'licensed' ) ) {

  	// create index page and archive for licenses.

  	$page_data = array(
  		'post_title' 	=> 'Items by License',
  		'post_content'	=> 'Browse the items in this SPLOTbox by license for reuse',
  		'post_name'		=> 'licensed',
  		'post_status'	=> 'publish',
  		'post_type'		=> 'page',
  		'post_author' 	=> 1,
  		'post_date' 	=> date('Y-m-d H:i:s', time() - 172800),
  		'page_template'	=> 'page-licensed.php',
  	);

  	wp_insert_post( $page_data );

  }

	// add rewrite rules, then flush to make sure they stick.
	splotbox_rewrite_rules();
	flush_rewrite_rules();
}


# -----------------------------------------------------------------
# Set up the table and put the napkins out
# -----------------------------------------------------------------

// we need to load the options this before the auto login so we can use the pass
add_action( 'after_setup_theme', 'splotbox_load_theme_options', 9 );

/* add audio post format to the mix */

add_action( 'after_setup_theme', 'splotbox_formats', 11 );

function splotbox_formats(){
     add_theme_support( 'post-formats', array( 'audio', 'video', 'aside', 'gallery', 'image', 'link', 'quote' ) );
}

// change the name of admin menu items from "New Posts"
// -- h/t https://wordpress.stackexchange.com/a/9224/14945

add_action( 'admin_menu', 'splotbox_change_post_label' );
add_action( 'init', 'splotbox_change_post_object' );

function splotbox_change_post_label() {
    global $menu;
    global $submenu;

    $thing_name = 'Item';

    $menu[5][0] = $thing_name . 's';
    $submenu['edit.php'][5][0] = 'All ' . $thing_name . 's';
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

# -----------------------------------------------------------------
# login fancy
# -----------------------------------------------------------------

// Add custom logo to entry screen... because we can
// While we are at it, use CSS to hide the back to blog and retried password links
add_action( 'login_enqueue_scripts', 'splot_login_logo' );

function splot_login_logo() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/site-login-logo.png);
            height:90px;
			width:320px;
			background-size: 320px 90px;
			background-repeat: no-repeat;
			padding-bottom: 0px;
        }
    </style>
<?php }


// Make logo link points to blog, not Wordpress.org Change Dat
// -- h/t http://www.sitepoint.com/design-a-stylized-custom-wordpress-login-screen/

add_filter( 'login_headerurl', 'splot_login_link' );

function splot_login_link( $url ) {
	return 'https://splot.ca/';
}

/* Customize message above registration form */

add_filter('login_message', 'splot_add_login_message');

function splot_add_login_message() {
	return '<p class="message">To do all that is SPLOT!</p>';
}

// login page title
add_filter( 'login_headertext', 'splot_login_logo_url_title' );

function splot_login_logo_url_title() {
	return 'The grand mystery of all things SPLOT';
}



# -----------------------------------------------------------------
# Comments
# -----------------------------------------------------------------

function splotbox_comment_mod( $defaults ) {
	$defaults['title_reply'] = 'Provide Feedback';
	$defaults['logged_in_as'] = '';
	$defaults['title_reply_to'] = 'Provide Feedback for %s';
	return $defaults;
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

// -----  add allowable url parameters
add_filter('query_vars', 'splotbox_queryvars' );

function splotbox_queryvars( $qvars ) {
	$qvars[] = 'flavor'; // flag for type of license
	$qvars[] = 'random'; // flag for random generator
	$qvars[] = 'elink'; // flag for get edit link
	$qvars[] = 'ispre'; // flag for preview when not logged in

	return $qvars;
}

# -----------------------------------------------------------------
# Query vars and Redirects
# -----------------------------------------------------------------


function splotbox_rewrite_rules() {

	$licensed_page_slug = splotbox_get_licensed_page();

	// first rule for paged results of licenses
	add_rewrite_rule( '^'. $licensed_page_slug . '/([^/]+)/page/([0-9]{1,})/?',  'index.php?page_id=' . splotbox_option('share_page') . '&flavor=$matches[1]&paged=$matches[2]','top');

	add_rewrite_rule( '^' . $licensed_page_slug . '/([^/]*)/?',  'index.php?page_id=' . splotbox_option('share_page') . '&flavor=$matches[1]','top');

	// let's go random
	add_rewrite_rule('random/?$', 'index.php?random=1', 'top');
}

/* set up function to handle redirects */

 add_action('template_redirect','splotbox_random_template');

 function splotbox_random_template() {

 	// manage redirect for /random
   if ( get_query_var('random') == 1 ) {
		 // set arguments for WP_Query on published posts to get 1 at random
		$args = array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => 1,
			'orderby' => 'rand'
		);

		// It's time! Go someplace random
		$my_random_post = new WP_Query ( $args );

		while ( $my_random_post->have_posts () ) {
		  $my_random_post->the_post ();

		  // redirect to the random post
		  wp_redirect ( get_permalink () );
		  exit;
		}
   }
 }


# -----------------------------------------------------------------
# Enqueue Scripts and Styles
# -----------------------------------------------------------------


add_action('wp_enqueue_scripts', 'add_splotbox_scripts');

function add_splotbox_scripts() {
	// hello parents!
    $parent_style = 'garfunkel_style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );

    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );


	// register and enqueue styles for icons and google fonts because parent theme has it wired wrong
	wp_enqueue_style( 'splotbox_googleFonts', '//fonts.googleapis.com/css?family=Fira+Sans:400,500,700,400italic,700italic|Playfair+Display:400,900|Crimson+Text:700,400italic,700italic,400' );
	wp_enqueue_style( 'splotbox_genericons', get_stylesheet_directory_uri() . '/genericons/genericons.css' );


 	// use these scripts just our sharing form page
 	 	if ( is_page( splotbox_get_share_page() ) ) { // use on just our form page

		 // add media scripts if we are on our share page and not an admin
		 // after http://wordpress.stackexchange.com/a/116489/14945

		if (! is_admin() ) wp_enqueue_media();

		// Build in tag auto complete script
   		wp_enqueue_script( 'suggest' );

   		// Autoembed functionality in rich text editor
   		// needs dependency on tiny_mce
   		// h/t https://wordpress.stackexchange.com/a/287623
   		wp_enqueue_script( 'mce-view', '', array('tiny_mce') );

 		// tinymce mods
		add_filter("mce_external_plugins", "splotbox_register_buttons");
		add_filter('mce_buttons','splotbox_tinymce_buttons');
		add_filter('mce_buttons_2','splotbox_tinymce_2_buttons');


		// custom jquery for the uploader on the form
		wp_register_script( 'jquery.splotbox' , get_stylesheet_directory_uri() . '/js/jquery.splotbox.js', null , false, true );

		// add a local variable for the site's home url
		wp_localize_script(
		  'jquery.splotbox',
		  'boxObject',
		  array(
		  	'ajaxUrl' => admin_url('admin-ajax.php'),
			'siteUrl' => esc_url(home_url()),
			'uploadMax' => splotbox_option('upload_max' ),
			'allowedMedia' => splotbox_option('use_upload_media')		  )
		);

		wp_enqueue_script( 'jquery.splotbox' );


		if ( splotbox_option('use_media_recorder') ) {
			// enqueues for the Media Recorder https://github.com/collab-project/videojs-record/
			// styles
			wp_enqueue_style( 'video-js', '//vjs.zencdn.net/7.6.6/video-js.min.css' );
			wp_enqueue_style( 'video-wavesurfer', '//unpkg.com/videojs-wavesurfer/dist/css/videojs.wavesurfer.min.css' );
			wp_enqueue_style( 'videojs-record', get_stylesheet_directory_uri() . '/css/videojs.record.min.css' );

			//scripts
			wp_register_script( 'video-min', '//vjs.zencdn.net/7.6.6/video.min.js' );
			wp_register_script( 'webrtc-adapter', '//unpkg.com/webrtc-adapter/out/adapter.js' );
			wp_register_script( 'wavesurfer', '//unpkg.com/wavesurfer.js/dist/wavesurfer.min.js' );
			wp_register_script( 'wavesurfer-microphone', '//unpkg.com/wavesurfer.js/dist/plugin/wavesurfer.microphone.min.js', array('wavesurfer') );
			wp_register_script( 'videojs-wavesurfer', '//unpkg.com/videojs-wavesurfer/dist/videojs.wavesurfer.min.js' , array('wavesurfer'));
			wp_register_script( 'videojs-record', get_stylesheet_directory_uri() . '/js/videojs.record.min.js' );
			wp_register_script( 'videojs-lame', get_stylesheet_directory_uri() . '/js/videojs.record.lamejs.min.js' );

			wp_enqueue_script( 'video-min' );
			wp_enqueue_script( 'webrtc-adapter' );
			wp_enqueue_script( 'wavesurfer' );
			wp_enqueue_script( 'wavesurfer-microphone' );
			wp_enqueue_script( 'videojs-wavesurfer' );
			wp_enqueue_script( 'videojs-record' );
			wp_enqueue_script( 'videojs-lame' );

			// videojs recorder scripts
			wp_register_script( 'splotbox-recorder' , get_stylesheet_directory_uri() . '/js/splotbox-recorder.js', null , false, true );

			// add a local variable for the site's home url
			wp_localize_script(
			  'splotbox-recorder',
			  'recorderObject',
			  array(
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'recordMax' => splotbox_option('max_record_time') * 60,
				'stylesheetUrl' => get_stylesheet_directory_uri(),
			  )
			);

			wp_enqueue_script( 'splotbox-recorder' );
		}
	}
}

# -----------------------------------------------------------------
# Menu Setup
# -----------------------------------------------------------------

// checks to see if a menu location is used.
function splot_is_menu_location_used( $location = 'primary' ) {

	// get locations of all menus
	$menulocations = get_nav_menu_locations();

	// get all nav menus
	$navmenus = wp_get_nav_menus();

	// if either is empty we have no menus to use
	if ( empty( $menulocations ) OR empty( $navmenus ) ) return false;

	// othewise look for the menu location in the list
	return in_array( $location , $menulocations);
}

// create a basic menu if one has not been define for primary
function splot_default_menu() {

	// site home with trailing slash
	$splot_home = site_url('/');

 	return ( '<li><a href="' . $splot_home . '">Home</a></li><li><a href="' . $splot_home . splotbox_get_share_page() . '">Share</a></li><li><a href="' . $splot_home . 'random' . '">Random</a></li>' );
}




# -----------------------------------------------------------------
# Allow Previews
# -----------------------------------------------------------------

function  splotbox_show_drafts( $query ) {
// show drafts only for single previews
    if ( is_user_logged_in() || is_feed() || !is_single() )
        return;

    $query->set( 'post_status', array( 'publish', 'draft' ) );
}

add_action( 'pre_get_posts', 'splotbox_show_drafts' );

// enable previews of posts for non-logged in users
// ----- h/t https://wordpress.stackexchange.com/a/164088/14945

add_filter( 'the_posts', 'splotbox_reveal_previews', 10, 2 );

function splotbox_reveal_previews( $posts, $wp_query ) {

    //making sure the post is a preview to avoid showing published private posts
    if ( !is_preview() )
        return $posts;

    if ( is_user_logged_in() )
    	 return $posts;

    if ( count( $posts ) )
        return $posts;

    if ( !empty( $wp_query->query['p'] ) ) {
        return array ( get_post( $wp_query->query['p'] ) );
    }
}

function splotbox_is_preview() {
	return ( get_query_var( 'ispre', 0 ) == 1);
}

# -----------------------------------------------------------------
# Tiny-MCE mods
# -----------------------------------------------------------------

add_filter( 'tiny_mce_before_init', 'splotbox_tinymce_settings' );

function splotbox_tinymce_settings( $settings ) {

	// $settings['file_picker_types'] = 'image';
	$settings['images_upload_handler'] = 'function (blobInfo, success, failure) {
    var xhr, formData;

    xhr = new XMLHttpRequest();
    xhr.withCredentials = false;
    xhr.open(\'POST\', \'' . admin_url('admin-ajax.php') . '\');

    xhr.onload = function() {
      var json;

      if (xhr.status != 200) {
        failure(\'HTTP Error: \' + xhr.status);
        return;
      }

      json = JSON.parse(xhr.responseText);

      if (!json || typeof json.location != \'string\') {
        failure(\'Invalid JSON: \' + xhr.responseText);
        return;
      }

      success(json.location);
    };

    formData = new FormData();
    formData.append(\'file\', blobInfo.blob(), blobInfo.filename());
	formData.append(\'action\', \'splotbox_upload_action\');
    xhr.send(formData);
  }';


	return $settings;
}



function splotbox_register_buttons( $plugin_array ) {
	$plugin_array['imgbutton'] = get_stylesheet_directory_uri() . '/js/image-button.js';
	return $plugin_array;
}

// remove  buttons from the visual editor

function splotbox_tinymce_buttons($buttons) {
	//Remove the more button
	$remove = 'wp_more';

	// Find the array key and then unset
	if ( ( $key = array_search($remove,$buttons) ) !== false )
		unset($buttons[$key]);

	// now add the image button in, and the second one that acts like a label
	$buttons[] = 'image';
	$buttons[] = 'imgbutton';

	return $buttons;
 }

// remove  more buttons from the visual editor


function splotbox_tinymce_2_buttons( $buttons)  {
	//Remove the keybord shortcut and paste text buttons
	$remove = array('wp_help','pastetext');

	return array_diff($buttons,$remove);
 }


// this is the handler used in the tiny_mce editor to manage image upload
add_action( 'wp_ajax_nopriv_splotbox_upload_action', 'splotbox_upload_action' ); //allow on front-end
add_action( 'wp_ajax_splotbox_upload_action', 'splotbox_upload_action' );


function splotbox_upload_action() {
	// for image files (it was the first kind so lazy name for function)

    $newupload = 0;

    if ( !empty($_FILES) ) {
        $files = $_FILES;
        foreach($files as $file) {
            $newfile = array (
                    'name' => $file['name'],
                    'type' => $file['type'],
                    'tmp_name' => $file['tmp_name'],
                    'error' => $file['error'],
                    'size' => $file['size']
            );

            $_FILES = array('upload'=>$newfile);
            foreach($_FILES as $file => $array) {
                $newupload = media_handle_upload( $file, 0);
            }
        }
    }
    echo json_encode( array('id'=> $newupload, 'location' => wp_get_attachment_image_src( $newupload, 'large' )[0], 'caption' => get_attachment_caption_by_id( $newupload ) ) );
    die();
}

// enable ajax for audio uploads
add_action( 'wp_ajax_nopriv_splotbox_upload_audio_action', 'splotbox_upload_audio_action' ); //allow on front-end
add_action( 'wp_ajax_splotbox_upload_audio_action', 'splotbox_upload_audio_action' );

function splotbox_upload_audio_action() {
	// for audio files

    $newupload = 0;

    if ( !empty($_FILES) ) {
        $files = $_FILES;
        foreach($files as $file) {
            $newfile = array (
                    'name' => $file['name'],
                    'type' => $file['type'],
                    'tmp_name' => $file['tmp_name'],
                    'error' => $file['error'],
                    'size' => $file['size']
            );

            $_FILES = array('upload'=>$newfile);
            foreach($_FILES as $file => $array) {
                $newupload = media_handle_upload( $file, 0);
            }
        }
    }
    echo json_encode( array('id'=> $newupload, 'location' => wp_get_attachment_url($newupload) ) );
    die();
}


?>
