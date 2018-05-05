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

// Set up oembed for Archive.org videos
add_action( 'init', function() {

	wp_embed_register_handler( 
		'archiveorg', 
		'#https?://archive\.org/details/(.*)#i', 
		'wp_embed_handler_archiveorg' 
	);

} );


function wp_embed_handler_archiveorg( $matches, $attr, $url, $rawattr ) {

	$embed = sprintf(
			'<iframe src="https://archive.org/embed/%1$s" width="640" height="480" frameborder="0" webkitallowfullscreen="true" mozallowfullscreen="true" allowfullscreen></iframe>', 
			esc_attr( $matches[1] )
			
			);

		return apply_filters( 'embed_archiveorg', $embed, $matches, $attr, $url, $rawattr );
}

// -----  add allowable url parameters
add_filter('query_vars', 'splotbox_queryvars' );

function splotbox_queryvars( $qvars ) {
	$qvars[] = 'flavor'; // flag for type of license
	
	return $qvars;
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
# Customizer Stuff
# -----------------------------------------------------------------

add_action( 'customize_register', 'splotbox_register_theme_customizer' );


function splotbox_register_theme_customizer( $wp_customize ) {
	// Create custom panel.
	$wp_customize->add_panel( 'customize_collector', array(
		'priority'       => 500,
		'theme_supports' => '',
		'title'          => __( 'SPLOTbox', 'garfunkel'),
		'description'    => __( 'Customizer Stuff', 'garfunkel'),
	) );

	// Add section for the collect form
	$wp_customize->add_section( 'share_form' , array(
		'title'    => __('Share Form','garfunkel'),
		'panel'    => 'customize_collector',
		'priority' => 10
	) );
	
	// Add setting for default prompt
	$wp_customize->add_setting( 'default_prompt', array(
		 'default'           => __( 'Complete the form below to add an audio or video item to this collection', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Add control for default prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'default_prompt',
		    array(
		        'label'    => __( 'Default Prompt', 'garfunkel'),
		        'priority' => 10,
		        'description' => __( 'The opening message above the form.' ),
		        'section'  => 'share_form',
		        'settings' => 'default_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);
	
	// setting for title label
	$wp_customize->add_setting( 'item_title', array(
		 'default'           => __( 'Media Info', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control fortitle label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_title',
		    array(
		        'label'    => __( 'Title Label', 'garfunkel'),
		        'priority' => 14,
		        'description' => __( '' ),
		        'section'  => 'share_form',
		        'settings' => 'item_title',
		        'type'     => 'text'
		    )
	    )
	);
	
	// setting for title description
	$wp_customize->add_setting( 'item_title_prompt', array(
		 'default'           => __( 'Enter a descriptive title that works well as a headline for this item when listed on this site.', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for title description
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_title_prompt',
		    array(
		        'label'    => __( 'Title Prompt', 'garfunkel'),
		        'priority' => 15,
		        'description' => __( '' ),
		        'section'  => 'share_form',
		        'settings' => 'item_title_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);
	
	// setting for image upload label
	$wp_customize->add_setting( 'item_upload', array(
		 'default'           => __( 'Media Source', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for image upload  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_upload',
		    array(
		        'label'    => __( 'Media Source Label', 'garfunkel'),
		        'priority' => 11,
		        'description' => __( '' ),
		        'section'  => 'share_form',
		        'settings' => 'item_upload',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for image upload prompt
	$wp_customize->add_setting( 'item_upload_prompt', array(
		 'default'           => __( 'Upload your file by dragging its icon to the window that opens when clicking  <strong>Upload Media</strong> button. The uploader will automatically enter it\'s URL in the entry field above and will populate title and caption fields below if it finds appropriate metadata in the file.', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for image upload prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_upload_prompt',
		    array(
		        'label'    => __( 'Media Upload Prompt', 'garfunkel'),
		        'priority' => 12,
		        'description' => __( 'Directions for media uploads' ),
		        'section'  => 'share_form',
		        'settings' => 'item_upload_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);

	// setting for author  label
	$wp_customize->add_setting( 'item_author', array(
		 'default'           => __( 'Your Info', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for author  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_author',
		    array(
		        'label'    => __( 'Author Label', 'garfunkel'),
		        'priority' => 45,
		        'description' => __( '' ),
		        'section'  => 'share_form',
		        'settings' => 'item_author',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for author  label prompt
	$wp_customize->add_setting( 'item_author_prompt', array(
		 'default'           => __( 'Take credit for sharing this item by entering your name, twitter handle, secret agent name, or remain "Anonymous"', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for author  label prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_author_prompt',
		    array(
		        'label'    => __( 'Item Author Prompt', 'garfunkel'),
		        'priority' => 46,
		        'description' => __( 'Directions for the author/uploader credit' ),
		        'section'  => 'share_form',
		        'settings' => 'item_author_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);

	// setting for description  label
	$wp_customize->add_setting( 'item_description', array(
		 'default'           => __( 'Media Description', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for description  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_description', 
		    array(
		        'label'    => __( 'Description Label', 'garfunkel'),
		        'priority' => 18,
		        'description' => __( '' ),
		        'section'  => 'share_form',
		        'settings' => 'item_description',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for description  label prompt
	$wp_customize->add_setting( 'item_description_prompt', array(
		 'default'           => __( 'Enter a descriptive caption to include with the item.', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for description  label prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_description_prompt',
		    array(
		        'label'    => __( 'Item Description Prompt', 'garfunkel'),
		        'priority' => 19,
		        'description' => __( 'Directions for the description entry field' ),
		        'section'  => 'share_form',
		        'settings' => 'item_description_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);

	// setting for image source  label
	$wp_customize->add_setting( 'item_media_source', array(
		 'default'           => __( 'Creator of Media', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for image source  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_media_source',
		    array(
		        'label'    => __( 'Media Creator Label', 'garfunkel'),
		        'priority' => 32,
		        'description' => __( '' ),
		        'section'  => 'share_form',
		        'settings' => 'item_media_source',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for image source  prompt
	$wp_customize->add_setting( 'item_media_source_prompt', array(
		 'default'           => __( 'Enter a name of a person, publisher, organization, web site, etc to give credit for this item.', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for image source prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_media_source_prompt',
		    array(
		        'label'    => __( 'Media Source Prompt', 'garfunkel'),
		        'priority' => 33,
		        'description' => __( 'Directions for the media source field' ),
		        'section'  => 'share_form',
		        'settings' => 'item_media_source_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);

	// setting for license  label
	$wp_customize->add_setting( 'item_license', array(
		 'default'           => __( 'Choose a License', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for license  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_license',
		    array(
		        'label'    => __( 'License Label', 'garfunkel'),
		        'priority' => 36,
		        'description' => __( '' ),
		        'section'  => 'share_form',
		        'settings' => 'item_license',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for license  prompt
	$wp_customize->add_setting( 'item_license_prompt', array(
		 'default'           => __( 'If known indicate a license or copyright attached to this media. If this is your original piece of content, then select a license you wish to attach to it.', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for license prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_license_prompt',
		    array(
		        'label'    => __( 'Media License Prompt', 'garfunkel'),
		        'priority' => 37,
		        'description' => __( 'Directions for the license selection' ),
		        'section'  => 'share_form',
		        'settings' => 'item_license_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);

	// setting for categories  label
	$wp_customize->add_setting( 'item_categories', array(
		 'default'           => __( 'Categories', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for categories  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_categories',
		    array(
		        'label'    => __( 'Categories Label', 'garfunkel'),
		        'priority' => 22,
		        'description' => __( '' ),
		        'section'  => 'share_form',
		        'settings' => 'item_categories',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for categories  prompt
	$wp_customize->add_setting( 'item_categories_prompt', array(
		 'default'           => __( 'Check all categories that will help organize this item.', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for categories prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_categories_prompt',
		    array(
		        'label'    => __( 'Categories Prompt', 'garfunkel'),
		        'priority' => 23,
		        'description' => __( 'Directions for the categories selection' ),
		        'section'  => 'share_form',
		        'settings' => 'item_categories_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);
	
	// setting for tags  label
	$wp_customize->add_setting( 'item_tags', array(
		 'default'           => __( 'Tags', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for tags  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_tags',
		    array(
		        'label'    => __( 'Tags Label', 'garfunkel'),
		        'priority' => 25,
		        'description' => __( '' ),
		        'section'  => 'share_form',
		        'settings' => 'item_tags',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for tags  prompt
	$wp_customize->add_setting( 'item_tags_prompt', array(
		 'default'           => __( 'Descriptive tags, separate multiple ones with commas', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for tags prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_tags_prompt',
		    array(
		        'label'    => __( 'Tags Prompt', 'garfunkel'),
		        'priority' => 26,
		        'description' => __( 'Directions for tags entry' ),
		        'section'  => 'share_form',
		        'settings' => 'item_tags_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);

	// setting for editor notes  label
	$wp_customize->add_setting( 'item_editor_notes', array(
		 'default'           => __( 'Notes to the Editor', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for editor notes  label
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_editor_notes',
		    array(
		        'label'    => __( 'Editor Notes Label', 'garfunkel'),
		        'priority' => 50,
		        'description' => __( '' ),
		        'section'  => 'share_form',
		        'settings' => 'item_editor_notes',
		        'type'     => 'text'
		    )
	    )
	);

	// setting for editor notes  prompt
	$wp_customize->add_setting( 'item_editor_notes_prompt', array(
		 'default'           => __( 'Add any notes or messages to the site manager; this will not be part of what is published. If you wish to be contacted, leave an email address or twitter handle below. Otherwise you are completely anonymous.', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for editor notes prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_editor_notes_prompt',
		    array(
		        'label'    => __( 'Editor Notes Prompt', 'garfunkel'),
		        'priority' => 52,
		        'description' => __( '' ),
		        'section'  => 'share_form',
		        'settings' => 'item_editor_notes_prompt',
		        'type'     => 'textarea'
		    )
	    )
	);
			
 	// Sanitize text
	function sanitize_text( $text ) {
	    return sanitize_text_field( $text );
	}
}


function splotbox_form_default_prompt() {
	 if ( get_theme_mod( 'default_prompt') != "" ) {
	 	return get_theme_mod( 'default_prompt');
	 }	else {
	 	return 'Complete the form below to add an audio or video item to this collection';
	 }
}

function splotbox_form_item_title() {
	 if ( get_theme_mod( 'item_title') != "" ) {
	 	echo get_theme_mod( 'item_title');
	 }	else {
	 	echo 'Title for the Media';
	 }
}

function splotbox_form_item_title_prompt() {
	 if ( get_theme_mod( 'item_title_prompt') != "" ) {
	 	echo get_theme_mod( 'item_title_prompt');
	 }	else {
	 	echo 'Enter a descriptive title that works well as a headline for this item when listed on this site.';
	 }
}

function splotbox_form_item_upload() {
	 if ( get_theme_mod( 'item_upload') != "" ) {
	 	echo get_theme_mod( 'item_upload');
	 }	else {
	 	echo 'Media Source';
	 }
}

function splotbox_form_item_upload_prompt() {
	 if ( get_theme_mod( 'item_upload_prompt') != "" ) {
	 	echo get_theme_mod( 'item_upload_prompt');
	 }	else {
	 	echo 'Upload your file by dragging its icon to the window that opens when clicking  <strong>Upload Media</strong> button. The uploader will automatically enter it\'s URL in the entry field above and will populate title and caption fields below if it finds appropriate metadata in the file.';
	 }
}

function splotbox_form_item_author() {
	 if ( get_theme_mod( 'item_author') != "" ) {
	 	echo get_theme_mod( 'item_author');
	 }	else {
	 	echo 'Your Info';
	 }
}

function splotbox_form_item_author_prompt() {
	 if ( get_theme_mod( 'item_author_prompt') != "" ) {
	 	echo get_theme_mod( 'item_author_prompt');
	 }	else {
	 	echo 'Take credit for sharing this item by entering your name, twitter handle, secret agent name, or remain "Anonymous"';
	 }
}

function splotbox_form_item_description() {
	 if ( get_theme_mod( 'item_description') != "" ) {
	 	echo get_theme_mod( 'item_description');
	 }	else {
	 	echo 'Decscription';
	 }
}

function splotbox_form_item_description_prompt() {
	 if ( get_theme_mod( 'item_description_prompt') != "" ) {
	 	echo get_theme_mod( 'item_description_prompt');
	 }	else {
	 	echo 'Enter a descriptive caption to include with the item.';
	 }
}

function splotbox_form_item_media_source() {
	 if ( get_theme_mod( 'item_media_source') != "" ) {
	 	echo get_theme_mod( 'item_media_source');
	 }	else {
	 	echo 'Source of Media';
	 }
}

function splotbox_form_item_media_source_prompt() {
	 if ( get_theme_mod( 'item_media_source_prompt') != "" ) {
	 	echo get_theme_mod( 'item_media_source_prompt');
	 }	else {
	 	echo 'Enter a name of a person, publisher, organization, web site, etc to give credit for this item.';
	 }
}

function splotbox_form_item_license() {
	 if ( get_theme_mod( 'item_license') != "" ) {
	 	echo get_theme_mod( 'item_license');
	 }	else {
	 	echo 'Reuse License';
	 }
}

function splotbox_form_item_license_prompt() {
	 if ( get_theme_mod( 'item_license_prompt') != "" ) {
	 	echo get_theme_mod( 'item_license_prompt');
	 }	else {
	 	echo 'If known indicate a license or copyright attached to this media. If this is your original piece of content, then select a license you wish to attach to it.';
	 }
}

function splotbox_form_item_categories() {
	 if ( get_theme_mod( 'item_categories') != "" ) {
	 	echo get_theme_mod( 'item_categories');
	 }	else {
	 	echo 'Categories';
	 }
}

function splotbox_form_item_categories_prompt() {
	 if ( get_theme_mod( 'item_categories_prompt') != "" ) {
	 	echo get_theme_mod( 'item_categories_prompt');
	 }	else {
	 	echo 'Check all categories that will help organize this item.';
	 }
}

function splotbox_form_item_tags() {
	 if ( get_theme_mod( 'item_tags') != "" ) {
	 	echo get_theme_mod( 'item_tags');
	 }	else {
	 	echo 'Tags';
	 }
}

function splotbox_form_item_tags_prompt() {
	 if ( get_theme_mod( 'item_tags_prompt') != "" ) {
	 	echo get_theme_mod( 'item_tags_prompt');
	 }	else {
	 	echo 'Descriptive tags, separate multiple ones with commas';
	 }
}


function splotbox_form_item_editor_notes() {
	 if ( get_theme_mod( 'item_editor_notes') != "" ) {
	 	echo get_theme_mod( 'item_editor_notes');
	 }	else {
	 	echo 'Notes to the Editor';
	 }
}

function splotbox_form_item_editor_notes_prompt() {
	 if ( get_theme_mod( 'item_editor_notes_prompt') != "" ) {
	 	echo get_theme_mod( 'item_editor_notes_prompt');
	 }	else {
	 	echo 'Add any notes or messages to send to the site manager; this will not be part of what is published. If you wish to be contacted, leave an email address or twitter handle.';
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
						$work_str .  ' is &copy; All Rights Reserved.', 
						$work_str_html . ' is &copy; All Rights Reserved.'
					)
			 );
			break;


		case 'u': 	
			return ( array( 
						'The rights of ' . $work_str .  ' is unknown or not specified.', 
						'The rights of ' . $work_str_html . ' is unknown or not specified.'
					)
			 );
			break;

		case 'yt': 	
			return ( array( 
						$work_str .  ' is covered by a YouTube Standard License.', 
						$work_str_html . ' is covered by a <a href="https://www.youtube.com/t/terms">YouTube Standard License</a>.' 
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
	$splot_home = home_url('/');
  
 	return ( '<li><a href="' . $splot_home . '">Home</a></li><li><a href="' . $splot_home . 'share' . '">Share</a></li><li><a href="' . $splot_home . 'random' . '">Random</a></li>' );
  
}


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
	
	} else {
	
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
					'archive.org'
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