<?php

if ( !is_user_logged_in() ) {
	// already not logged in? go to desk.
  	wp_redirect ( home_url('/') . 'desk' );
  	exit;
  	
} elseif ( !current_user_can( 'edit_others_posts' ) ) {
	// okay user, who are you? we know you are not an admin or editor
		
	// if the collector user not found, we send you to the desk
	if ( !splotbox_check_user() ) {
		// now go to the desk and check in properly
	  	wp_redirect ( home_url('/') . 'desk'  );
  		exit;
  	}
}

		

// ------------------------ defaults ------------------------

// default welcome message
$feedback_msg = 'Complete the form below to add an audio or video item to this collection.<br /><br /> <span class="required">*</span> indicates required entries.';

$wAuthor = 'Anonymous';
				
$wCats = array( splotbox_option('def_cat')); // preload default category
$wLicense = '--';
$all_licenses = splotbox_get_licences();

// not yet saved
$is_published = false;
$box_style = '<div class="notify"><span class="symbol icon-info"></span> ';


// ------------------- form processing ------------------------

// verify that a form was submitted and it passes the nonce check
if ( isset( $_POST['splotbox_form_make_submitted'] ) && wp_verify_nonce( $_POST['splotbox_form_make_submitted'], 'splotbox_form_make' ) ) {
 
 		// grab the variables from the form
 		$wTitle = 					sanitize_text_field( stripslashes( $_POST['wTitle'] ) );
 		$wAuthor = 					( isset ($_POST['wAuthor'] ) ) ? sanitize_text_field( stripslashes($_POST['wAuthor']) ) : 'Anonymous';		
 		$wTags = 					sanitize_text_field( $_POST['wTags'] );	
 		$wText = 					wp_kses_post( $_POST['wText'] );
 		$wCredit = 					sanitize_text_field( $_POST['wCredit']  );
 		$wNotes = 					sanitize_text_field( stripslashes( $_POST['wNotes'] ) );
 		$wMediaURL = 				trim($_POST['wMediaURL']);
 		$wMediaType = 				url_is_media_type($wMediaURL); 	
 		$wCats = 					( isset ($_POST['wCats'] ) ) ? $_POST['wCats'] : array();
 		$wLicense = 				$_POST['wLicense'];

 		// let's do some validation, store an error message for each problem found
 		$errors = array();
 		 				
 		// let's do some validation, store an error message for each problem found
 		$errors = array();
		
 		if ( $wMediaURL == '' ) {
 			$errors[] = '<strong>URL Missing</strong> - you can either upload an audio file or enter an external URL for where your media can be found'; 
 		} elseif ( strpos( $wMediaURL, 'http') === false )  {	
 			$errors[] = '<strong>Malformed URL</strong> - <code>' . $wMediaURL . '</code> does not appear to be a full URL; it must begin with <code>http://</code> or <code>https://</code>' ; 
 		
 		} elseif ( !($wMediaType) ) {
 			$errors[] = '<strong>Wrong Media Type</strong> - <code>' . $wMediaURL . '</code> is not a link to an audio file, a SoundCloud track, or video from YouTube, Vimeo, or Animoto' ; 
 		}
 		
 		if ( $wTitle == '' ) $errors[] = '<strong>Title Missing</strong> - enter an interesting title.'; 

 		if (  splotbox_option('use_caption') == '2' AND $wText == '' ) $errors[] = '<strong>Description Missing</strong> - please enter a description for this media item.';
 
  		if (  splotbox_option('use_source') == '2' AND $wCredit == '' ) $errors[] = '<strong>Source Missing</strong> - please the name or description for the source of this media content.';
  		
  		if (  splotbox_option('use_license') == '2' AND $wLicense == '--' ) $errors[] = '<strong>License Not Selected</strong> - select an appropriate license for this item.'; 


 		
 		if ( count($errors) > 0 ) {
 			// form errors, build feedback string to display the errors
 			$feedback_msg = 'Sorry, but there are a few errors in your information. Please correct and try again. We really want to add your item.<ul>';
 			
 			// Hah, each one is an oops, get it? 
 			foreach ($errors as $oops) {
 				$feedback_msg .= '<li>' . $oops . '</li>';
 			}
 			
 			$feedback_msg .= '</ul>';
 			
 			$box_style = '<div class="notify notify-red"><span class="symbol icon-error"></span> ';
 			
 		} else {
 			
 			// good enough, let's make a post! 
 			 			
			$w_information = array(
				'post_title' => $wTitle,
				'post_content' => $wMediaURL . "\n<!--more-->\n\n" .  $wText,
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
			add_post_meta( $post_id, 'credit', $wCredit );

			// store the license code
			add_post_meta( $post_id, 'license', $wLicense );

			// store extra notes
			if ( $wNotes ) add_post_meta($post_id, 'extra_notes', $wNotes);
			
			// add the tags
			wp_set_post_tags( $post_id, $wTags);
		

			if ( splotbox_option('new_item_status') == 'publish' ) {
				// feed back for published item
				$feedback_msg = 'Your shared media item  <strong>' . $wTitle . '</strong> has been published!  You can <a href="'. site_url() . '/?p=' . $post_id   . '">view it now</a>. Or you can <a href="' . site_url() . '/share">share another</a>.';
			
			} else {
				// feed back for item left in draft
				$feedback_msg = 'Your shared media item <strong>' . $wTitle . '</strong> has been submitted as a draft. You can <a href="'.  site_url() . '/?p=' . $post_id   . '">preview it now</a>. Once it has been approved by a moderator, everyone else can see it.';	
			
			}		


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

				<fieldset>
					<legend>Media Source</legend>
					
					<label for="wMediaURL"><?php _e('Enter Audio or Video URL', 'garfunkel' ) ?> <span class="required">*</span></label><br />
					<p>Embed a media player for audio or video content that exists at certain external URLs. For audio content this includes any valid web link to a sound file (links to <code>.mp3 .m4a .ogg</code> files). URLs can also be used for content in Soundcloud, YouTube or Vimeo. Check the embed settings on the source to make sure there are no restrictions.</p>
					
					<p>Enter a full web address for the item (including http:// or https://) <a href="<?php echo $wMediaURL?>" class="pretty-button pretty-button-gray" id="testURL" target="_blank">Test Link</a></p>
					<input type="text" name="wMediaURL" id="wMediaURL" class="required" value="<?php echo $wMediaURL; ?>" tabindex="1" />


					<?php if (  splotbox_option('use_upload_media')  == '1') :?>
					
					<label for="headerImage" class="sublabel"><?php _e('Or Upload a File', 'garfunkel') ?></label>
					<p>Only audio files of type <code>.mp3 .m4a .ogg</code> less than <?php echo splotbox_max_upload(); ?> can be uploaded to this site. </p>
					<div class="uploader">
						<input type="button" id="wFeatureImage_button"  class="btn btn-success btn-medium  upload_image_button" name="_wImage_button"  data-uploader_title="Add a New Media File" data-uploader_button_text="Select Media" value="Upload Media" tabindex="2" />
						
						</div>
						
						<p>Upload your file by dragging its icon to the window that opens when clicking  <strong>Upload Media</strong> button. The uploader will automatically enter it's URL in the entry field above and will populate title and caption fields below if it finds appropriate metadata in the file. <br clear="left"></p>
						
						<?php endif?>
				</fieldset>						

				<fieldset>
					<legend>Media Info</legend>
					<label for="wTitle"><?php _e('Title for the Item', 'garfunkel' ) ?> <span class="required">*</span></label><br />
					<p>An interesting title goes a long way; it's the headline when it appears on this site.</p>
					<input type="text" name="wTitle" id="wTitle" class="required" value="<?php echo $wTitle; ?>" tabindex="4" />
				
					<?php if (  splotbox_option('use_caption') > '0'):	
  						$required = (splotbox_option('use_caption') == 2) ? '<span class="required">*</span>' : '';
  					?>
  				
					<label for="wText"><?php _e('Description', 'garfunkel') ?> <?php echo $required?></label>
					
						<p><?php echo splotbox_option('caption_prompt')?></p>
	
	
						<?php if (  splotbox_option('caption_field') == 's'):?>	
							<textarea name="wText" id="wText" rows="15"  tabindex="4"><?php echo stripslashes( $wText );?></textarea>
							
						<?php else:?>
							
						<?php
						// set up for inserting the WP post editor
						$settings = array( 'textarea_name' => 'wText', 'editor_height' => '300',  'tabindex'  => "3", 'media_buttons' => false);

						wp_editor(  stripslashes( $wText ), 'wtext', $settings );
						
						?>	
						<?php endif?>

					<?php endif?>

					<label for="wCats"><?php _e( 'Categories', 'garfunkel' ) ?></label>
					<p>Check all that apply.</p>
					<?php 
					
					// set up arguments to get all categories 
					$args = array(
						'hide_empty'               => 0,
					); 
					
					$article_cats = get_categories( $args );

					foreach ( $article_cats as $acat ) {
					
						$checked = ( in_array( $acat->term_id, $wCats) ) ? ' checked="checked"' : '';
						
						echo '<br /><input type="checkbox" name="wCats[]" tabindex="4" value="' . $acat->term_id . '"' . $checked . '> ' . $acat->name;
					}
					
					?>
					
					<label for="wTags"><?php _e( 'Tags', 'garfunkel' ) ?></label>
					<p>Descriptive tags, separate multiple ones with commas</p>
					
					<input type="text" name="wTags" id="wTags" value="<?php echo $wTags; ?>" tabindex="5"  />
				</fieldset>
				

				<?php if ( splotbox_option('use_source') OR splotbox_option('use_license') ):?>

				<fieldset>
					<legend>Source and License</legend>
					
					
					
					<?php if (  splotbox_option('use_source') > '0'):	
  						$required = (splotbox_option('use_source') == 2) ? '<span class="required">*</span>' : '';
  						
  					?>
					<label for="wCredit"><?php _e('Creator of Media', 'garfunkel' ) ?>  <?php echo $required?></label><br />
					<p>Enter a name of a person, publisher, organization, web site, etc to give credit for this item.</p>
					<input type="text" name="wCredit" id="wCredit" class="required" value="<?php echo $wCredit; ?>" tabindex="6" />
					
					<?php endif?>


					<?php if (  splotbox_option('use_license') > '0'):	
  						$required = (splotbox_option('use_license') == 2) ? '<span class="required">*</span>' : '';
  					?>
  					
					<label for="wLicense"><?php _e('License for Reuse', 'garfunkel' ) ?> <?php echo $required?></label><br />
					<p>If known indicate a license or copyright attached to this media. If this is your original piece of content, then select a license you wish to attach to it.</p>
					<select name="wLicense" id="wLicense" tabindex="7" />
					<option value="--">Select a License</option>
					<?php
						foreach ($all_licenses as $key => $value) {
							$selected = ( $key == $wLicense ) ? ' selected' : '';
							echo '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
						}
					?>
					
					</select>
					
					<?php endif?>
					
				</fieldset>	
				<?php endif?>				

				<fieldset>
					<legend>Your Info</legend>
					<label for="wAuthor"><?php _e('Who is Adding this Item? (also know as "you")', 'garfunkel' ) ?> <span class="required">*</span></label><br />
					<p>Take credit for sharing this item by entering your name, twitter handle, secret agent name, or remain "Anonymous".</p>
					<input type="text" name="wAuthor" class="required" id="wAuthor"  value="<?php echo $wAuthor; ?>" tabindex="8" />
					
					
					<label for="wNotes"><?php _e('Notes to the Editor', 'wpbootstrap') ?></label>						
						<p>Add any notes or messages to the site manager; this will not be part of what is published. If you wish to be contacted, leave an email address or twitter handle below. Otherwise you are completely anonymous.</p>
						<textarea name="wNotes" id="wNotes" rows="10"  tabindex="9"><?php echo stripslashes($wNotes);?></textarea>

				</fieldset>	

			
				<fieldset>	
				<legend>Share This Item</legend>
				<p>Review your information, because once you click the button below, it is sent to the site.</p>
				<?php  wp_nonce_field( 'splotbox_form_make', 'splotbox_form_make_submitted' ); ?>
				
				<input type="submit" value="Submit Form" id="makeit" name="makeit" tabindex="12">
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