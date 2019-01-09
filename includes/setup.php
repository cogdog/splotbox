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
// -- h/t https://wordpress.stackexchange.com/a/9224/14945

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

// -----  add allowable url parameters
add_filter('query_vars', 'splotbox_queryvars' );

function splotbox_queryvars( $qvars ) {
	$qvars[] = 'flavor'; // flag for type of license
	
	return $qvars;
}  

 
// -----  rewrite rules for licensed pretty urls
add_action('init', 'splotbox_rewrite_rules', 10, 0); 
      
function splotbox_rewrite_rules() {
	$license_page = get_page_by_path('licensed');
	
	if ( $license_page ) {
	
		// first rule for paged results
		add_rewrite_rule( '^licensed/([^/]+)/page/([0-9]{1,})/?',  'index.php?page_id=' . $license_page->ID . '&flavor=$matches[1]&paged=$matches[2]','top');	
	
		add_rewrite_rule( '^licensed/([^/]*)/?',  'index.php?page_id=' . $license_page->ID . '&flavor=$matches[1]','top');	
	}	
}

# -----------------------------------------------------------------
# Enqueue Scripts and Styles
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


	// register and enqueue styles for icons and google fonts because parent theme has it wired wrong
	wp_register_style( 'splotbox_googleFonts', '//fonts.googleapis.com/css?family=Fira+Sans:400,500,700,400italic,700italic|Playfair+Display:400,900|Crimson+Text:700,400italic,700italic,400' );
	wp_register_style( 'splotbox_genericons', get_stylesheet_directory_uri() . '/genericons/genericons.css' );

	wp_enqueue_style( 'splotbox_style', get_stylesheet_uri(), array( 'splotbox_googleFonts', 'splotbox_genericons' ) );
			    
 	// use these scripts just our sharing form page
 	if ( is_page('share') ) { 
    
		 // add media scripts if we are on our share page and not an admin
		 // after http://wordpress.stackexchange.com/a/116489/14945
    	 
		if (! is_admin() ) wp_enqueue_media();
		
		// Build in tag auto complete script
   		wp_enqueue_script( 'suggest' );

		// custom jquery for the uploader on the form
		wp_register_script( 'jquery.splotbox' , get_stylesheet_directory_uri() . '/js/jquery.splotbox.js', null , '1.0', TRUE );
		wp_enqueue_script( 'jquery.splotbox' );
		
		// add scripts for fancybox (used for previews of collected items) 
		//-- h/t http://code.tutsplus.com/tutorials/add-a-responsive-lightbox-to-your-wordpress-theme--wp-28100
		wp_register_script( 'fancybox', get_stylesheet_directory_uri() . '/includes/lightbox/js/jquery.fancybox.pack.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'fancybox' );

		// Lightbox formatting for preview screated with rich text editor
		wp_register_script( 'lightbox_preview', get_stylesheet_directory_uri() . '/includes/lightbox/js/lightbox_preview.js', array( 'fancybox' ), '1.1', null , '1.0', TRUE );
		wp_enqueue_script( 'lightbox_preview' );
	
		// fancybox styles
		wp_register_style( 'lightbox-style', get_stylesheet_directory_uri() . '/includes/lightbox/css/jquery.fancybox.css' );
		wp_enqueue_style( 'lightbox-style' );	
		
		// used to display formatted dates
		wp_register_script( 'moment' , get_stylesheet_directory_uri() . '/js/moment.js', null, '1.0', TRUE );
		wp_enqueue_script( 'moment' );		
		
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
  
 	return ( '<li><a href="' . $splot_home . '">Home</a></li><li><a href="' . $splot_home . 'share' . '">Share</a></li><li><a href="' . $splot_home . 'random' . '">Random</a></li>' );
  
}


?>