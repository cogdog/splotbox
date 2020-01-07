<?php

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

	// add a customizer link that opens the sharing form
    $wp_admin_bar->add_menu( array(
        'parent' => 'customize',
        'id' => 'splotbox-customize',
        'title' => __('Sharing Form'),
        'href' => admin_url( 'customize.php?url='. splot_redirect_url())
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


?>