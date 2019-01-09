<?php

if ( !is_user_logged_in() ) {
	// not logged in? go directly to desk. do not collect $200
  	wp_redirect ( home_url('/') . 'desk' );
  	exit;
  	
} elseif ( !current_user_can( 'edit_others_posts' ) ) {
	// okay user, who are you? we know you are not an admin or editor
		
	// if the collector user not found, we send you to the desk too
	if ( !splotbox_check_user() ) {
		// now go to the desk and check in properly
	  	wp_redirect ( home_url('/') . 'desk'  );
  		exit;
  	}
}

// ------------------------ defaults ------------------------

// default welcome message
$feedback_msg = splotbox_form_default_prompt() . '. Fields marked  <strong>*</strong> are required.';

$wAuthor = 'Anonymous';
$wTitle = $wSource =  $wMediaURL = $wNotes = $wTags = $wText = '';
$wCats = array( splotbox_option('def_cat')); // preload default category
$wLicense = '--';
$all_licenses = splotbox_get_licences();

// not yet saved
$is_published = false;
$box_style = '<div class="notify"><span class="symbol icon-info"></span> ';

// initial button states
$previewBtnState = ' disabled=""';
$submitBtnState = ' disabled';

$wRandTitles = splotbox_get_two_items();

// ------------------- form processing ------------------------

// verify that a form was submitted and it passes the nonce check
if ( isset( $_POST['splotbox_form_make_submitted'] ) && wp_verify_nonce( $_POST['splotbox_form_make_submitted'], 'splotbox_form_make' ) ) {
 
 		// grab the variables from the form
 		$wTitle = 					sanitize_text_field( stripslashes( $_POST['wTitle'] ) );
 		$wAuthor = 					( isset ($_POST['wAuthor'] ) ) ? sanitize_text_field( stripslashes($_POST['wAuthor']) ) : 'Anonymous';		
 		$wTags = 					sanitize_text_field( $_POST['wTags'] );	
 		$wText = 					wp_kses_post( $_POST['wText'] );
 		$wSource = 					sanitize_text_field( $_POST['wSource']  );
 		$wNotes = 					sanitize_text_field( stripslashes( $_POST['wNotes'] ) );
 		$wMediaURL = 				trim($_POST['wMediaURL']);
 		$wMediaType = 				url_is_media_type($wMediaURL); 	
 		$wCats = 					( isset ($_POST['wCats'] ) ) ? $_POST['wCats'] : array();
 		$wLicense = 				$_POST['wLicense'];
 		if ( isset ($_POST['post_id'] ) ) $post_id = $_POST['post_id'];
 		 				
 		// let's do some validation, store an error message for each problem found
 		$errors = array();
		
 		if ( $wMediaURL == '' ) {
 			$errors[] = '<strong>Media Missing</strong> - you can either upload an audio file or enter an external URL for where your media can be found'; 
 		} elseif ( strpos( $wMediaURL, 'http') === false )  {	
 			$errors[] = '<strong>Malformed Web Address</strong> - <code>' . $wMediaURL . '</code> does not appear to be a full URL; it must begin with <code>http://</code> or <code>https://</code>' ; 
 		
 		} elseif ( !($wMediaType) ) {
 			$errors[] = '<strong>Wrong Media Type</strong> - <code>' . $wMediaURL . '</code> is not a link to an audio file, a SoundCloud track, or video from YouTube, Vimeo, the Internet Archive or an Adobe Spark item.' ; 
 		}
 		
 		if ( $wTitle == '' ) $errors[] = '<strong>Title Missing</strong> - enter a descriptive title for this item.'; 

 		if (  splotbox_option('use_caption') == '2' AND $wText == '' ) $errors[] = '<strong>Description Missing</strong> - please enter a description for this media item.';
 
  		if (  splotbox_option('use_source') == '2' AND $wSource == '' ) $errors[] = '<strong>Source Missing</strong> - please provide a name or description for the source of this media item.';
  		
  		if (  splotbox_option('use_license') == '2' AND $wLicense == '--' ) $errors[] = '<strong>License Not Selected</strong> - select an appropriate license for this item.'; 
 		
 		if ( count($errors) > 0 ) {
 			// form errors, build feedback string to display the errors
 			$feedback_msg = 'Sorry, but there are a few errors in your information. Please correct and try again. We really want to add your item! <ul>';
 			
 			// Hah, each one is an oops, get it? 
 			foreach ($errors as $oops) {
 				$feedback_msg .= '<li>' . $oops . '</li>';
 			}
 			
 			$feedback_msg .= '</ul>';
 			
 			// reset button states
			$previewBtnState = ' disabled=""';
			$submitBtnState = ' disabled';
 			
 			$box_style = '<div class="notify notify-red"><span class="symbol icon-error"></span> ';
 			
 		} else {
 		
 			// the info is okay, enable preview and submit buttons
			$previewBtnState = '';
			$submitBtnState = '';
 			
 			$feedback_msg = 'Your information looks ready to submit to ' . get_bloginfo( 'name' ) . '. You can first <a href="#preview" class="fancybox" title="Preview of your item. Close this overlay, make any changes, than click \'Share It\'" >preview</a> to review your entry or if you are ready, just scroll down and click "Submit Item" to share it.';
 			
 			$box_style = '<div class="notify"><span class="symbol icon-info"></span> ';
 			
 			
 			// Now process form only if submit button used
 			if ( isset ( $_POST['makeit'] ) ) {
 				// good enough, let's put this in the box! 
 			 			
				$w_information = array(
					'post_title' => $wTitle,
					'post_content' => $wText,
					'post_status' => splotbox_option('new_item_status'),
					'post_category' => $wCats		
				);

				// insert as a new post
				$post_id = wp_insert_post( $w_information );
			
			
				//sets to 'audio' post-format
				set_post_format( $post_id, $wMediaType ); 
			
				// store audio url
				add_post_meta( $post_id, 'media_url', $wMediaURL);
			
				// store the author as post meta data
				add_post_meta( $post_id, 'shared_by', $wAuthor );
			
				// store the name of person to credit
				add_post_meta( $post_id, 'credit', $wSource );

				// store the license code
				add_post_meta( $post_id, 'license', $wLicense );

				// store extra notes
				if ( $wNotes ) add_post_meta($post_id, 'extra_notes', $wNotes);
			
				// add the tags
				wp_set_post_tags( $post_id, $wTags);
		

				if ( splotbox_option('new_item_status') == 'publish' ) {
					// feed back for published item
					$feedback_msg = 'Your shared media item  <strong>' . $wTitle . '</strong> has been published!  You can <a href="'. get_permalink( $post_id ) . '">view it now</a>.  Or you can <a href="' . site_url()  . '">return to ' . get_bloginfo() . '</a>.';
			
				} else {
					// feed back for item left in draft
					$feedback_msg = 'Your entry for <strong>' . $wTitle . '</strong> has been submitted as a draft. Once it has been approved by a moderator, everyone can see it at <a href="' . site_url()  . '">return to ' . get_bloginfo() . '</a>.';	
	
			
				}		

				// logout the special user
				if ( splotbox_check_user()=== true ) wp_logout();
			
			
				if ( splotbox_option( 'notify' ) != '') {
				// Let's do some EMAIL!
		
					// who gets mail? They do.
					$to_recipients = explode( "," ,  splotbox_option( 'notify' ) );
		
					$subject = 'New Item Dropped into to ' . get_bloginfo();
		
					if ( splotbox_option('new_item_status') == 'publish' ) {
						$message = 'A media item <strong>"' . $wTitle . '"</strong> shared by <strong>' . $wAuthor . '</strong> has been published to ' . get_bloginfo() . '. You can <a href="'. site_url() . '/?p=' . $post_id  . '">see view it now</a>';
				

					} else {
						$message = 'An media item <strong>"' . $wTitle . '"</strong> shared by <strong>' . $wAuthor . '</strong> has been submitted to ' . get_bloginfo() . '. You can <a href="'. site_url() . '/?p=' . $post_id . 'preview=true' . '">preview it now</a>.<br /><br /> To  publish it, simply <a href="' . admin_url( 'edit.php?post_status=draft&post_type=post') . '">find it in the drafts</a> and change it\'s status from <strong>Draft</strong> to <strong>Publish</strong>';
					}
				
					if ( $wNotes ) $message .= '<br /><br />There are some extra notes from the author:<blockquote>' . $wNotes . '</blockquote>';
		
					// turn on HTML mail
					add_filter( 'wp_mail_content_type', 'set_html_content_type' );
		
					// mail it!
					wp_mail( $to_recipients, $subject, $message);
		
					// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
					remove_filter( 'wp_mail_content_type', 'set_html_content_type' );	
			
					}
											
				// set the gate	open, we are done.
			
				$is_published = true;
				$box_style = '<div class="notify notify-green"><span class="symbol icon-tick"></span> ';	
			} // we are submitting
			
		} // count errors		
		
} // end form submmitted check
?>

<?php get_header(); ?>

<div class="wrapper">
										
	<div class="wrapper-inner section-inner thin">
	
		<div class="content">
	
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		
				<div class="post">
				
					<?php if ( has_post_thumbnail() ) : ?>
						
						<div class="featured-media">
						
							<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
							
								<?php the_post_thumbnail( 'post-image' ); ?>
								
								<?php if ( ! empty( get_post( get_post_thumbnail_id() )->post_excerpt ) ) : ?>
												
									<div class="media-caption-container">
									
										<p class="media-caption"><?php echo get_post( get_post_thumbnail_id() )->post_excerpt; ?></p>
										
									</div>
									
								<?php endif; ?>
								
							</a>
									
						</div><!-- .featured-media -->
							
					<?php endif; ?>
					
					<div class="post-inner">
					
						<div class="post-header">
													
							<?php the_title( '<h1 class="post-title">', '</h1>' ); ?>
						    				    
					    </div><!-- .post-header -->
					   				        			        		                
						<div class="post-content">
									                                        
			    	<?php the_content(); ?>
	
			    	<?php 
					if ( !is_user_logged_in() ) :?>
						<a href="<?php echo get_bloginfo('url')?>/wp-login.php?autologin=sharer">activate lasers</a>
					<?php endif?>
		    	
		    		<?php echo $box_style . $feedback_msg . '</div>';?>   
		    				
			    	<?php wp_link_pages('before=<div class="clear"></div><p class="page-links">' . __('Pages:','fukasawa') . ' &after=</p>&seperator= <span class="sep">/</span> '); ?>


	<?php if ( is_user_logged_in() and !$is_published ) : // show form if logged in and it has not been published ?>
			
		<form  id="splotboxform" method="post" action="" enctype="multipart/form-data">

				<fieldset id="theUpload">
					<legend><?php splotbox_form_item_upload() ?></legend>
					
					<label for="wMediaURL"><?php _e('Enter Audio or Video URL', 'garfunkel' ) ?> <span class="required">*</span></label><br />
					<p>Embed a media player for audio or video content that is published on Soundcloud, YouTube, Vimeo, the Internet Archive, or Adobe Spark Pages/Videos).  The URL you enter should be the one that displays the content from the service. For audio content you can also use  valid web link to a sound file (links to <code>.mp3 .m4a .ogg</code> files).</p>
					
					<p>Enter a full web address for the item (including http:// or https://)</p>
					<input type="text" name="wMediaURL" id="wMediaURL" class="required" value="<?php echo $wMediaURL; ?>" tabindex="1" /> 
					<p>It's a good idea to  <a href="<?php echo $wMediaURL?>" class="pretty-button pretty-button-gray" id="testURL" target="_blank">Test Link</a>  to make sure it works!</p>


					<?php if (  splotbox_option('use_upload_media')  == '1') :?>
					
					<label for="headerImage" class="sublabel"><?php _e('Or Upload a File', 'garfunkel') ?></label>
					<p>Only audio files of type <code>.mp3 .m4a .ogg</code> less than <?php echo splotbox_max_upload(); ?> can be uploaded to this site. </p>
					<div class="uploader">
						<input type="button" id="wFeatureImage_button"  class="btn btn-success btn-medium  upload_image_button" name="_wImage_button"  data-uploader_title="Add a New Media File" data-uploader_button_text="Select Media" value="Upload Media" tabindex="2" />
						
						</div>
						
						<p><?php splotbox_form_item_upload_prompt() ?><br clear="left"></p>
						
						<?php endif?>
						
						
					
				</fieldset>						

				<fieldset id="theMedia">
					<legend>Media Info</legend>
					<label for="wTitle"><?php splotbox_form_item_title() ?> <span class="required">*</span></label><br />
					<p><?php splotbox_form_item_title_prompt() ?> </p>
					<input type="text" name="wTitle" id="wTitle" class="required" value="<?php echo $wTitle; ?>" tabindex="4" />
				
					<?php if (  splotbox_option('use_caption') > '0'):	
  						$required = (splotbox_option('use_caption') == 2) ? '<span class="required">*</span>' : '';
  					?>
  				
					<label for="wText"><?php splotbox_form_item_description() ?> <?php echo $required?></label>
					
						<p><?php splotbox_form_item_description_prompt()?> </p>
	
	
						<?php if (  splotbox_option('caption_field') == 's'):?>	
							<textarea name="wText" id="wText" rows="15"  tabindex="4"><?php echo stripslashes( $wText );?></textarea>
							
						<input id="wRichText" type="hidden" value="0">
							
						<?php else:?>
						
						<input id="wRichText" type="hidden" value="1">
							
						<?php
						// set up for inserting the WP post editor
						$settings = array( 'textarea_name' => 'wText', 'editor_height' => '300',  'tabindex'  => "3", 'media_buttons' => false);

						wp_editor(  stripslashes( $wText ), 'wTextHTML', $settings );
						
						?>	
						
						<?php endif?>

					<?php endif?>


					<?php if (splotbox_option('show_cats') ):?> 
						<fieldset id="theCats">
						<label for="wCats"><?php splotbox_form_item_categories() ?></label>
						<p><?php splotbox_form_item_categories_prompt() ?></p>
						<?php 
					
						// set up arguments to get all categories 
						$args = array(
							'hide_empty'               => 0,
						); 
					
						$article_cats = get_categories( $args );

						foreach ( $article_cats as $acat ) {
					
							$checked = ( in_array( $acat->term_id, $wCats) ) ? ' checked="checked"' : '';
						
							echo '<br /><input type="checkbox" name="wCats[]" tabindex="4" value="' . $acat->term_id . '" data-checkbox-text="' . $acat->name . '" ' .  $checked . '> ' . $acat->name;
						}
					
						?>
						</fieldset>
					<?php endif?>
					
					<?php if (splotbox_option('show_tags') ):?> 
						<label for="wTags"><?php  splotbox_form_item_tags() ?></label>
						<p><?php  splotbox_form_item_tags_prompt() ?></p>
					
						<input type="text" name="wTags" id="wTags" value="<?php echo $wTags; ?>" tabindex="5"  />
					
					<?php endif?>
				</fieldset>
				

				<?php if ( splotbox_option('use_source') OR splotbox_option('use_license') ):?>

				<fieldset id="theAttribution">
					<legend>Media Attribution / License</legend>
					
					
					
					<?php if (  splotbox_option('use_source') > '0'):	
  						$required = (splotbox_option('use_source') == 2) ? '<span class="required">*</span>' : '';
  						
  					?>
						<label for="wSource"><?php splotbox_form_item_media_source() ?>   <?php echo $required?></label><br />
						<p><?php splotbox_form_item_media_source_prompt() ?></p>
						<input type="text" name="wSource" id="wSource" class="required" value="<?php echo $wSource; ?>" tabindex="6" />
					
					<?php endif?>


					<?php if ( splotbox_option('use_license') > '0'):	
  						$required = (splotbox_option('use_license') == 2) ? '<span class="required">*</span>' : '';
  					?>
  					
						<label for="wLicense"><?php splotbox_form_item_license() ?> <?php echo $required?></label><br />
						<p><?php splotbox_form_item_license_prompt() ?></p>
						<select name="wLicense" id="wLicense" tabindex="7" />
						<option value="--">Select a License</option>
						<?php
							foreach ($all_licenses as $key => $value) {
								$selected = ( $key == $wLicense ) ? ' selected' : '';
								echo '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
							}
						?>
					
						</select>
						
				<?php if ( splotbox_option( 'show_attribution' ) == 1 ): ?>
					<input id="wAttributionPreview" type="hidden" value="1">
				<?php endif?>						
					
					<?php endif?>
					
				</fieldset>	
				<?php endif?>				

				<fieldset  id="theAuthor">
					<legend>Your Info</legend>
					<label for="wAuthor"><?php splotbox_form_item_author()?> <span class="required">*</span></label><br />
					<p><?php splotbox_form_item_author_prompt()?></p>
					<input type="text" name="wAuthor" class="required" id="wAuthor"  value="<?php echo $wAuthor; ?>" tabindex="8" />
					
					
					<label for="wNotes"><?php splotbox_form_item_editor_notes() ?></label>						
						<p><?php splotbox_form_item_editor_notes_prompt() ?></p>
						<textarea name="wNotes" id="wNotes" rows="10"  tabindex="9"><?php echo stripslashes($wNotes);?></textarea>

				</fieldset>	

			
				<fieldset id="theButtons">	
				<label for="theButtons"><?php splotbox_form_item_submit_buttons() ?></label>
				
				<?php  wp_nonce_field( 'splotbox_form_make', 'splotbox_form_make_submitted' ); ?>
				
				<p><?php splotbox_form_item_submit_buttons_prompt() ?></p>
				
				
				<input type="submit" value="Check Info" id="checkit" name="checkit" tabindex="12">
				
				<a href="#preview" class="fancybox" title="Preview of your item. Close this overlay, make any changes, than click 'Share It'"  tabindex="14" <?php echo $previewBtnState?>>Preview</a>
				
				<input type="submit" value="Submit Item" id="makeit" name="makeit" tabindex="14" <?php echo $submitBtnState?>>
				
				<input type="hidden" id="embedMedia" value="<?php echo htmlentities( get_media_embedded ( $wMediaURL ))?>" />	
				<input type="hidden" id="wMediaLink" value="<?php echo $wMediaURL?>" />	
				<input type="hidden" id="wRandTitles" value="<?php echo implode( '|', $wRandTitles)?>" />	
				
				</fieldset>
			
						
		</form>
	<?php endif?>
																            			                        
						</div><!-- .post-content -->
						
						<div class="clear"></div>
					
					</div><!-- .post-inner -->
										
					<?php get_sidebar(); ?>
									
				</div><!-- .post -->
			
			<?php endwhile; else: ?>
			
				<p><?php _e( "We couldn't find any posts that matched your query. Please try again.", "garfunkel" ); ?></p>
		
			<?php endif; ?>
		
			<div class="clear"></div>
			
		</div><!-- .content -->
		
	</div><!-- .section-inner -->

</div><!-- .wrapper -->
								
<?php get_footer(); ?>