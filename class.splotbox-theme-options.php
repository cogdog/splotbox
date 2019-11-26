<?php
// manages all of the theme options
// heavy lifting via http://alisothegeek.com/2011/01/wordpress-settings-api-tutorial-1/
// Revision Oct 27, 2017 as jQuery update killed TAB UI

class splotbox_Theme_Options {

	/* Array of sections for the theme options page */
	private $sections;
	private $checkboxes;
	private $settings;

	/* Initialize */
	function __construct() {

		// This will keep track of the checkbox options for the validate_settings function.
		$this->checkboxes = array();
		$this->settings = array();
		$this->get_settings();
		
		$this->sections['general'] = __( 'All Settings' );


		// create a colllection of callbacks for each section heading
		foreach ( $this->sections as $slug => $title ) {
			$this->section_callbacks[$slug] = 'display_' . $slug;
		}
		

		// enqueue scripts for media uploader
        add_action( 'admin_enqueue_scripts', 'splotbox_enqueue_options_scripts' );
		
		add_action( 'admin_menu', array( &$this, 'add_pages' ) );
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		
		if ( ! get_option( 'splotbox_options' ) )
			$this->initialize_settings();
	}

	/* Add page(s) to the admin menu */
	public function add_pages() {
		$admin_page = add_theme_page( 'Splotbox Options', 'Splotbox Options', 'manage_options', 'splotbox-options', array( &$this, 'display_page' ) );
		
		// documents page, but don't add to menu		
		$docs_page = add_theme_page( 'Splotbox Documentation', '', 'manage_options', 'splotbox-docs', array( &$this, 'display_docs' ) );
			}

	/* HTML to display the theme options page */
	public function display_page() {
		echo '<div class="wrap">
		<h2>Splotbox Options</h2>';
		
		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true )
			echo '<div class="updated fade"><p>' . __( 'Theme options updated.' ) . '</p></div>';
				
		echo '<form action="options.php" method="post" enctype="multipart/form-data">';

			settings_fields( 'splotbox_options' );
			
			echo  '<h2 class="nav-tab-wrapper"><a class="nav-tab nav-tab-active" href="?page=splotbox-options">Settings</a>
	<a class="nav-tab" href="?page=splotbox-docs">Documentation</a></h2>';

		do_settings_sections( $_GET['page'] );

			echo '<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . __( 'Save Changes' ) . '" /></p>			
		</form>
		</div>
		
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			
			$("input[type=text], textarea").each(function() {
				if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "")
					$(this).css("color", "#999");
			});
			
			$("input[type=text], textarea").focus(function() {
				if ($(this).val() == $(this).attr("placeholder") || $(this).val() == "") {
					$(this).val("");
					$(this).css("color", "#000");
				}
			}).blur(function() {
				if ($(this).val() == "" || $(this).val() == $(this).attr("placeholder")) {
					$(this).val($(this).attr("placeholder"));
					$(this).css("color", "#999");
				}
			});
			
			// This will make the "warning" checkbox class really stand out when checked.
			// I use it here for the Reset checkbox.
			$(".warning").change(function() {
				if ($(this).is(":checked"))
					$(this).parent().css("background", "#c00").css("color", "#fff").css("fontWeight", "bold");
				else
					$(this).parent().css("background", "none").css("color", "inherit").css("fontWeight", "normal");
			});
		});
		</script>';	
	}

	/*  display documentation in a tab */
	public function display_docs() {	
		// This displays on the "Documentation" tab. 
		
	 	echo '<div class="wrap">
		<h1>Splotbox Documentation</h1>
		<h2 class="nav-tab-wrapper">
		<a class="nav-tab" href="?page=splotbox-options">Settings</a>
		<a class="nav-tab nav-tab-active" href="?page=splotbox-docs">Documentation</a></h2>';
		
		include( get_stylesheet_directory() . '/includes/splotbox-theme-options-docs.php');
		
		echo '</div>';		
	}


	/* Define all settings and their defaults */
	public function get_settings() {


		// for file upload checks
		$max_upload_size = round(wp_max_upload_size() / 1000000);

	
		/* General Settings
		===========================================*/


		// ------- access options
		$this->settings['access_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Access and Publishing Controls',
			'std'    => '',
			'type'    => 'heading'
		);


		$this->settings['accesscode'] = array(
			'title'   => __( 'Access Code' ),
			'desc'    => __( 'Set code to access the sharing tool; leave blank to allow open access (if you are worried about unwanted content being uploaded to your site, we recommend setting the <strong>Status for New Items</strong> below to <code>draft</code> or <code>Pending</code> so you can moderate submissions)' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'general'
		);
		
		
		$this->settings['accesshint'] = array(
			'title'   => __( 'Access Hint' ),
			'desc'    => __( 'Suggestion if someone cannot guess the code. Not super secure, but hey.' ),
			'std'     => 'Name of this site (lower the case, Ace!)',
			'type'    => 'text',
			'section' => 'general'
		);		


		$this->settings['pages_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Special Pages Setup',
			'std'    => 'Choose the pages for special purposes in the SPLOTbox',
			'type'    => 'heading'
		);

		// get all pages on site with template for the Sharing Form
		$found_pages = get_pages_with_template('page-share.php');
		$page_desc = 'Set the Page that should be used for the Sharing form.';
		
		// the function returns an array of id => page title, first item is the menu selection item
		if ( count( $found_pages ) > 1 ) {
			$page_std =  array_keys( $found_pages)[1];
		} else {
	
			$trypage = get_page_by_path('share');
		
			if ( $trypage ) {
				$page_std = $trypage->ID;
				$found_pages = array( 0 => 'Select Page', $page_std => $trypage->post_title );
		
			} else {
				$page_desc = 'No pages have been created with the Sharing Form template. This is required to enable access to the writing form. <a href="' . admin_url( 'post-new.php?post_type=page') . '">Create a new Page</a> and under <strong>Page Attributes</strong> select <code>Sharing Form</code> for the Template.'; 
				$page_std = '';
			}
	
		}

		$this->settings['share_page'] = array(
			'section' => 'general',
			'title'   => __( 'Page For Sharing Form'),
			'desc'    => $page_desc,
			'type'    => 'select',
			'std'     =>  $page_std,
			'choices' => $found_pages
		);
		
		// get all pages on site with template for the View by Licebse
		$found_pages = get_pages_with_template('page-licensed.php');
		$page_desc = 'Set the Page that should be used for viewing content by Rights/Licensing.';
		
		// the function returns an array of id => page title, first item is the menu selection item
		if ( count( $found_pages ) > 1 ) {
			$page_std =  array_keys( $found_pages)[1];
		} else {
	
			$trypage = get_page_by_path('licensed');
		
			if ( $trypage ) {
				$page_std = $trypage->ID;
				$found_pages = array( 0 => 'Select Page', $page_std => $trypage->post_title );
		
			} else {
				$page_desc = 'No pages have been created with the Licenses/Rights template. This is required to show a view of items bu the type of reuse licensed applied. <a href="' . admin_url( 'post-new.php?post_type=page') . '">Create a new Page</a> and under <strong>Page Attributes</strong> select <code>Licenses/Rights</code> for the Template.'; 
				$page_std = '';
			}
	
		}

		$this->settings['licensed_page'] = array(
			'section' => 'general',
			'title'   => __( 'Page For View by License/Rights'),
			'desc'    => $page_desc,
			'type'    => 'select',
			'std'     =>  $page_std,
			'choices' => $found_pages
		);
		// ------- publish options
		$this->settings['publish_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Publish Settings',
			'std'    => '',
			'type'    => 'heading'
		);

		$this->settings['new_item_status'] = array(
			'section' => 'general',
			'title'   => __( 'Status For New Items' ),
			'desc'    => __( 'Set to draft or pending to moderate submissions (depending what review flow you prefer.' ),
			'type'    => 'radio',
			'std'     => 'publish',
			'choices' => array(
				'publish' => 'Publish immediately',
				'pending' => 'Set as pending',
				'draft' => 'Keep as draft',
			)
		);	

		$this->settings['allow_comments'] = array(
			'section' => 'general',
			'title'   => __( 'Allow Comments?' ),
			'desc'    => __( 'Enable comments on items.' ),
			'type'    => 'checkbox',
			'std'     => 0 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);



		// ------- media options
		$this->settings['media_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Media Support',
			'std'    => '',
			'type'    => 'heading'
		);
		
		$this->settings['m_spark'] = array(
			'section' => 'general',
			'title'   => __( 'Media Supported By URL' ),
			'desc'    => __( 'Adobe Spark Pages/Videos  http://spark.adobe.com' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);

		$this->settings['m_flickr'] = array(
			'section' => 'general',
			'title'   => __( '' ),
			'desc'    => __( 'Flickr photos http://flickr.com' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['m_giphy'] = array(
			'section' => 'general',
			'title'   => __( '' ),
			'desc'    => __( 'Giphy gifs http://giphy.com' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['m_archive'] = array(
			'section' => 'general',
			'title'   => __( '' ),
			'desc'    => __( 'Internet Archive Audio and Video http://archive.org' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['m_mixcloud'] = array(
			'section' => 'general',
			'title'   => __( '' ),
			'desc'    => __( 'Mixcloud Audio http://mixcloud.com/' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);

		$this->settings['m_slideshare'] = array(
			'section' => 'general',
			'title'   => __( '' ),
			'desc'    => __( 'Slideshare Presentations http://slideshare.net/' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['m_soundcloud'] = array(
			'section' => 'general',
			'title'   => __( '' ),
			'desc'    => __( 'Soundcloud Audio http://soundcloud.com/' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);
		$this->settings['m_speakerdeck'] = array(
			'section' => 'general',
			'title'   => __( '' ),
			'desc'    => __( 'Speakerdeck Presentations http://speakerdeck.com/' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);

		$this->settings['m_ted'] = array(
			'section' => 'general',
			'title'   => __( '' ),
			'desc'    => __( 'Ted Talk Video http://ted.com/' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);

		
		$this->settings['m_vimeo'] = array(
			'section' => 'general',
			'title'   => __( '' ),
			'desc'    => __( 'Vimeo video http://vimeo.com' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);

		$this->settings['m_youtube'] = array(
			'section' => 'general',
			'title'   => __( '' ),
			'desc'    => __( 'Youtube video http://youtube.com/' ),
			'type'    => 'checkbox',
			'std'     => 1 // Set to 1 to be checked by default, 0 to be unchecked by default.
		);


		$this->settings['use_upload_media'] = array(
			'section' => 'general',
			'title'   => __( 'Allow media uploads for audio and images (if "no" then media can only be added by external URL)'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array (
							'0' => 'No',
							'1' => 'Yes'
					)
		);

		$this->settings['upload_max'] = array(
			'title'   => __( 'Maximum Upload File Size' ),
			'desc'    => __( 'Set limit for file uploads in Mb (maximum possible for this site is ' . $max_upload_size . ' Mb).' ),
			'std'     => $max_upload_size,
			'type'    => 'text',
			'section' => 'general'
		);


		// ------- sort options
		$this->settings['sort_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Item Sorting',
			'std'    => '',
			'type'    => 'heading'
		);


		$this->settings['sort_by'] = array(
			'section' => 'general',
			'title'   => __( 'Sort Items by'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'date',
			'choices' => array (
							'date' => 'Date Published',
							'title' => 'Title',
					)
		);
		
		$this->settings['sort_direction'] = array(
			'section' => 'general',
			'title'   => __( 'Sort Order'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => 'DESC',
			'choices' => array (
							'DESC' => 'Descending',
							'ASC' => 'Ascending',
					)
		);
		
		// ------- single item display
		$this->settings['single_item_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Single Item Display',
			'std'    => '',
			'type'    => 'heading'
		);


  		// Build array to hold options for select, an array of post categories
  		
  		
		$this->settings['show_cats'] = array(
			'section' => 'general',
			'title'   => __( 'Show the categories menu on share form and list on item displays?'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array (
							'0' => 'No',
							'1' => 'Yes'
					)
		);


  		// Build array to hold options for select, an array of post categories
		// Walk those cats, store as array index=ID 
		
	  	$all_cats = get_categories('hide_empty=0'); 
		foreach ( $all_cats as $item ) {
  			$cat_options[$item->term_id] =  $item->name;
  		}
 
		$this->settings['def_cat'] = array(
			'section' => 'general',
			'title'   => __( 'Default Category for New Item'),
			'desc'    => '',
			'type'    => 'select',
			'std'     => get_option('default_category'),
			'choices' => $cat_options
		);	

		$this->settings['show_tags'] = array(
			'section' => 'general',
			'title'   => __( 'Show the tags entry on collection form and list on item displays?'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array (
							'0' => 'No',
							'1' => 'Yes'
					)
		);



		
		$this->settings['use_caption'] = array(
			'section' => 'general',
			'title'   => __( 'Use description field on submission form and item display?'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array (
							'0' => 'No',
							'1' => 'Yes, but make it optional',
							'2' => 'Yes, and make it required'
					)
		);

		$this->settings['caption_field'] = array(
			'section' => 'general',
			'title'   => __( 'Caption Editing Field'),
			'desc'    => __( 'Use a plain text entry field or rich text editor.'),
			'type'    => 'radio',
			'std'     => 's',
			'choices' => array (
							's' => 'Simple plain text input field',
							'r' => 'Rich text editor'
					)
		);
			
		$this->settings['use_source'] = array(
			'section' => 'general',
			'title'   => __( 'Use source field (e.g. to provide credit for media) on submission form and item display?'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '1',
			'choices' => array (
							'0' => 'No',
							'1' => 'Yes, but make it optional',
							'2' => 'Yes, and make it required'
					)
		);

		// ------- single item display
		$this->settings['single_item_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Single Item Display',
			'std'    => '',
			'type'    => 'heading'
		);


		$this->settings['admin_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'Admin Settings',
			'std'    => '',
			'type'    => 'heading'
		);


		$this->settings['notify'] = array(
			'title'   => __( 'Notification Emails' ),
			'desc'    => __( 'Send notifications to these addresses (separate multiple wth commas). They must have an Editor Role on this site to be able to moderate' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'general'
		);

		// ------- licenseing
		$this->settings['License and Attribution'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'License and Attribution',
			'std'    => '',
			'type'    => 'heading'
		);

		$this->settings['use_license'] = array(
			'section' => 'general',
			'title'   => __( 'Use rights license on submission form and item display?'),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '0',
			'choices' => array (
							'0' => 'No',
							'1' => 'Yes, but make it optional',
							'2' => 'Yes, and make it required'
					)
		);
		
		$this->settings['show_attribution'] = array(
			'section' => 'general',
			'title'   => __( 'Cut and Paste Attribution' ),
			'desc'    => __( 'If license options used, show cut and paste attribution on single item displays?' ),
			'type'    => 'radio',
			'std'     => '0',
			'choices' => array(
				'0' => 'No',
				'1' => 'Yes',
			)
		);		
			
		/* Reset
		===========================================*/
		
		$this->settings['reset_heading'] = array(
			'section' => 'general',
			'title'   => '', // Not used for headings.
			'desc'	 => 'With Great Power Comes...',
			'std'    => '',
			'type'    => 'heading'
		);
		
		
		$this->settings['reset_theme'] = array(
			'section' => 'general',
			'title'   => __( 'Reset All Options' ),
			'type'    => 'checkbox',
			'std'     => 0,
			'class'   => 'warning', // Custom class for CSS
			'desc'    => __( 'Check this box and click "Save Changes" below to reset theme options to their defaults.' )
		);

		
	}
	
	public function display_general() {
		// section heading for general setttings
	
		echo '<p>These settings manage the behavior and appearance of your SPLOTbox site. See <a href="' . admin_url( 'themes.php?page=splotbox-docs') . '">the documentation</a> for help or visit the <a href="https://github.com/cogdog/splotbox" target="_blank">theme source on GitHub</a>.</p><p>If this kind of stuff has any value to you, please consider supporting me so I can do more!</p><p style="text-align:center"><a href="https://patreon.com/cogdog" target="_blank"><img src="https://cogdog.github.io/images/badge-patreon.png" alt="donate on patreon"></a> &nbsp; <a href="https://paypal.me/cogdog" target="_blank"><img src="https://cogdog.github.io/images/badge-paypal.png" alt="donate on paypal"></a></p> ';		
	}


	public function display_reset() {
		// section heading for reset section setttings
	}

	/* HTML output for individual settings */
	public function display_setting( $args = array() ) {

		extract( $args );

		$options = get_option( 'splotbox_options' );

		if ( ! isset( $options[$id] ) && $type != 'checkbox' )
			$options[$id] = $std;
		elseif ( ! isset( $options[$id] ) )
			$options[$id] = 0;

		$options['new_types'] = 'New Type Name'; // always reset
		
		$field_class = '';
		if ( $class != '' )
			$field_class = ' ' . $class;
			
			
		switch ( $type ) {
		
			case 'heading':
				echo '<tr><td colspan="2" class="alternate"><h3>' . $desc . '</h3><p>' . $std . '</p></td></tr>';
				break;

			case 'checkbox':

				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="splotbox_options[' . $id . ']" value="1" ' . checked( $options[$id], "1", false ) . ' /> <label for="' . $id . '">' . $desc . '</label>';

				break;

			case 'select':
				echo '<select class="select' . $field_class . '" name="splotbox_options[' . $id . ']">';

				foreach ( $choices as $value => $label )
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $options[$id], $value, false ) . '>' . $label . '</option>';

				echo '</select>';

				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;

			case 'radio':
				$i = 0;
				foreach ( $choices as $value => $label ) {
					echo '<input class="radio' . $field_class . '" type="radio" name="splotbox_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[$id], $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label>';
					if ( $i < count( $options ) - 1 )
						echo '<br />';
					$i++;
				}

				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;

			case 'textarea':
				echo '<textarea class="' . $field_class . '" id="' . $id . '" name="splotbox_options[' . $id . ']" placeholder="' . $std . '" rows="5" cols="60">' . format_for_editor( $options[$id] ) . '</textarea>';

				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;
				
			case 'medialoader':
			
			
				echo '<div id="uploader_' . $id . '">';		
				
				if ( $options[$id] )  {
					$front_img = wp_get_attachment_image_src( $options[$id], 'radcliffe' );
					echo '<img id="previewimage_' . $id . '" src="' . $front_img[0] . '" width="640" height="300" alt="default thumbnail" />';
				} else {
					echo '<img id="previewimage_' . $id . '" src="https://placehold.it/640x300" alt="default header image" />';
				}

				echo '<input type="hidden" name="splotbox_options[' . $id . ']" id="' . $id . '" value="' . $options[$id]  . '" />
  <br /><input type="button" class="upload_image_button button-primary" name="_splotbox_button' . $id .'" id="_splotbox_button' . $id .'" data-options_id="' . $id  . '" data-uploader_title="Set Default Header Image" data-uploader_button_text="Select Image" value="Set/Change Image" />
</div><!-- uploader -->';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;

			case 'password':
				echo '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="splotbox_options[' . $id . ']" value="' . esc_attr( $options[$id] ) . '" /> <input type="button" id="showHide" value="Show" /> ';

				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;

			case 'text':
			default:
				echo '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="splotbox_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';

				if ( $desc != '' ) {
				
					if ($id == 'def_thumb') $desc .= '<br /><a href="' . $options[$id] . '" target="_blank"><img src="' . $options[$id] . '" style="overflow: hidden;" width="' . $options["index_thumb_w"] . '"></a>';
					echo '<br /><span class="description">' . $desc . '</span>';
				}

				break;
		}
	}	
			


	/**
	 * Description for Docs section
	 *
	 * @since 1.0
	 */
	public function display_docs_section() {
		
		// This displays on the "Documentation" tab. 
		
		include( get_stylesheet_directory() . '/includes/splotbox-theme-options-docs.php');
		
		
	}

	/* Initialize settings to their default values */
	public function initialize_settings() {
	
		$default_settings = array();
		foreach ( $this->settings as $id => $setting ) {
			if ( $setting['type'] != 'heading' )
				$default_settings[$id] = $setting['std'];
		}
	
		update_option( 'splotbox_options', $default_settings );
	
	}


	/* Register settings via the WP Settings API */
	public function register_settings() {

		register_setting( 'splotbox_options', 'splotbox_options', array ( &$this, 'validate_settings' ) );


		foreach ( $this->sections as $slug => $title ) {
			add_settings_section( $slug, $title, array( &$this, $this->section_callbacks[$slug] ), 'splotbox-options' );
		}		

		$this->get_settings();
	
		foreach ( $this->settings as $id => $setting ) {
			$setting['id'] = $id;
			$this->create_setting( $setting );
		}

	}
	
	
	/* tool to create settings fields */
	public function create_setting( $args = array() ) {

		$defaults = array(
			'id'      => 'default_field',
			'title'   => 'Default Field',
			'desc'    => 'This is a default description.',
			'std'     => '',
			'type'    => 'text',
			'section' => 'general',
			'choices' => array(),
			'class'   => ''
		);

		extract( wp_parse_args( $args, $defaults ) );

		$field_args = array(
			'type'      => $type,
			'id'        => $id,
			'desc'      => $desc,
			'std'       => $std,
			'choices'   => $choices,
			'label_for' => $id,
			'class'     => $class
		);

		if ( $type == 'checkbox' )
			$this->checkboxes[] = $id;
				

		add_settings_field( $id, $title, array( $this, 'display_setting' ), 'splotbox-options', $section, $field_args );

	}
	
	public function validate_settings( $input ) {
		
		if ( ! isset( $input['reset_theme'] ) ) {
			$options = get_option( 'splotbox_options' );
			
			if ( $input['notify'] != $options['notify'] ) {
				$input['notify'] = str_replace(' ', '', $input['notify']);
			}
		
			foreach ( $this->checkboxes as $id ) {
				if ( isset( $options[$id] ) && ! isset( $input[$id] ) )
					unset( $options[$id] );
			}

			// make sure the max file upload is integer and less than max possible
			$max_upload_size = round(wp_max_upload_size() / 1000000);
			$input['upload_max'] = min( intval( $input['upload_max'] ), $max_upload_size  );
		
			return $input;
		}
		
		return false;
		
	}
 }
 
$theme_options = new splotbox_Theme_Options();

function splotbox_option( $option ) {
	$options = get_option( 'splotbox_options' );
	if ( isset( $options[$option] ) )
		return $options[$option];
	else
		return false;
}
?>