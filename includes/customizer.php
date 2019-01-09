<?php

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

	// setting for editor notes  prompt
	$wp_customize->add_setting( 'item_submit_buttons_prompt', array(
		 'default'           => __( 'First, verify the information for your media item by clicking "Check info." Once this is done, you can use the "Preview" button to see how it will look. When ready, click "Submit" to add it to this site.', 'garfunkel'),
		 'type' => 'theme_mod',
		 'sanitize_callback' => 'sanitize_text'
	) );
	
	// Control for editor notes prompt
	$wp_customize->add_control( new WP_Customize_Control(
	    $wp_customize,
		'item_submit_buttons_prompt',
		    array(
		        'label'    => __( 'Submit Buttons Prompt', 'garfunkel'),
		        'priority' => 54,
		        'description' => __( '' ),
		        'section'  => 'share_form',
		        'settings' => 'item_submit_buttons_prompt',
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
	 	echo 'If known, indicate a license or copyright attached to this media. If this is your own content, then select a license you wish to attach to it.';
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

function splotbox_form_item_submit_buttons() {
	 if ( get_theme_mod( 'item_submit_buttons') != "" ) {
	 	echo get_theme_mod( 'item_submit_buttons');
	 }	else {
	 	echo 'Share This Item';
	 }
}

function splotbox_form_item_submit_buttons_prompt() {
	 if ( get_theme_mod( 'item_submit_buttons_prompt') != "" ) {
	 	echo get_theme_mod( 'item_submit_buttons_prompt');
	 }	else {
	 	echo 'First, verify the information for your media item by clicking "Check info." Once this is done, you can use the "Preview" button to see how it will look. When ready, click "Submit" to add it to this site.';
	 }
}

/* Mainly used for Reclaim Hosting installs ---
 * This function assumes you have a Customizer export file in your theme directory
 * at 'data/customizer.dat'. That file must be created using the Customizer Export/Import
 * plugin found here... https://wordpress.org/plugins/customizer-export-import/
 * h/t - https://gist.github.com/fastlinemedia/9a8070b9a636e38b510f
 */
 
add_action( 'after_switch_theme', 'splot_import_customizer_settings' );
 
function splot_import_customizer_settings()
{
	// Check to see if the settings have already been imported.
	$template = get_template();
	$imported = get_option( $template . '_customizer_import', false );
	
	// Bail if already imported.
	if ( $imported ) {
		return;
	}
	
	// Get the path to the customizer export file.
	$path = trailingslashit( get_stylesheet_directory() ) . 'data/customizer.dat';
	
	// Return if the file doesn't exist.
	if ( ! file_exists( $path ) ) {
		return;
	}
	
	// Get the settings data.
	$data = @unserialize( file_get_contents( $path ) );
	
	// Return if something is wrong with the data.
	if ( 'array' != gettype( $data ) || ! isset( $data['mods'] ) ) {
		return;
	}
	
	// Import options.
	if ( isset( $data['options'] ) ) {
		foreach ( $data['options'] as $option_key => $option_value ) {
			update_option( $option_key, $option_value );
		}
	}
	
	// Import mods.
	foreach ( $data['mods'] as $key => $val ) {
		set_theme_mod( $key, $val );
	}
	
	// Set the option so we know these have already been imported.
	update_option( $template . '_customizer_import', true );
}


?>