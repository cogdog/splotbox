<?php
/*
Template Name: Sharing Page
*/

// set blanks
$wTitle = $wSource =  $wMediaURL = $wNotes = $wTags = $wText = $w_upload_status = '';
$wAccessCodeOk = $is_published = false; 
$audio_file_size = 0;
$errors = array();

$wCats = array( splotbox_option('def_cat')); // preload default category
$all_licenses = splotbox_get_licences(); // licenses
$splotbox_supports = splotbox_supports(); // sites supported by external URL

// see if we have an incoming clear the code form variable only on writing form
// ignored if options are not to use it

$wAccessCodeOk = isset( $_POST['wAccessCodeOk'] ) ? true : false;

// check that an access code is in play and it's not been yet passed
if ( !empty( splotbox_option('accesscode') ) AND !$wAccessCodeOk ) {

	// now see if we are to check the access code
	if ( isset( $_POST['splotbox_form_access_submitted'] ) 
	  AND wp_verify_nonce( $_POST['splotbox_form_access_submitted'], 'splotbox_form_access' ) ) {
	    
	   // grab the entered code from  form
		$wAccess = 	stripslashes( $_POST['wAccess'] );

		// Validation of the code
		if ( $wAccess != splotbox_option('accesscode') ) {
			$box_style = '<div class="notify notify-red"><span class="symbol icon-error"></span> ';
			$feedback_msg = '<p><strong>Incorrect Access Code</strong> - try again? Hint: ' . splotbox_option('accesshint') . '</p>'; 	
		} else {
			$wAccessCodeOk = true;
		}
	} else {
		$box_style = '<div class="notify"><span class="symbol icon-info"></span> ';
		$feedback_msg = '<p>An access code is required to use the writing form on ' . get_bloginfo('name') . '</p>';
	} // form check access code
} else {
	// set flag true just to clear all the other gates
	$wAccessCodeOk = true;
} // access code in  play check

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
	$wCats = 					( isset ($_POST['wCats'] ) ) ? $_POST['wCats'] : array();
	$wLicense = 				( isset ($_POST['wLicense'] ) ) ? $_POST['wLicense'] : '--'; 
	$wUploadMediaID =			$_POST['wUploadMedia'];
	$wMediaMethod = 			$_POST['wMediaMethod'];

	if ( isset ($_POST['post_id'] ) ) $post_id = $_POST['post_id'];
	
	// upload media if we got one
	if ($_FILES) {
		foreach ( $_FILES as $file => $array ) {
			$newupload = splotbox_insert_attachment( $file, $post->ID );
			if ( $newupload ) {
				$wUploadMediaID = $newupload;
				$w_upload_status = 'Media file uploaded. Choose another to replace it.';
				$wMediaURL	= wp_get_attachment_url($wUploadMediaID);	
			}
		}
	}	
	
	$wMediaType = 				url_is_media_type($wMediaURL); 	
					
	// let's do some validation, store an error message for each problem found
	
	if ( $wMediaURL == '' ) {
		$errors[] = '<strong>Media Missing</strong> - you can either upload an audio file or enter an external URL for where your media can be found'; 
	} elseif ( strpos( $wMediaURL, 'http') === false )  {	
		$errors[] = '<strong>Malformed Web Address</strong> - <code>' . $wMediaURL . '</code> does not appear to be a full URL; it must begin with <code>http://</code> or <code>https://</code>' ; 
	
	} elseif ( !($wMediaType) ) {
		$errors[] = '<strong>Unsupported Media URL</strong> - <code>' . $wMediaURL . '</code> is not a link to an audio file, image file, or a link to its entry on accepted sites: ' . $splotbox_supports; 
	}
	
	if ( $wTitle == '' ) $errors[] = '<strong>Title Missing</strong> - enter a descriptive title for this item.'; 

	if (  splotbox_option('use_caption') == '2' AND $wText == '' ) $errors[] = '<strong>Description Missing</strong> - please enter a description for this media item.';

	if (  splotbox_option('use_source') == '2' AND $wSource == '' ) $errors[] = '<strong>Source Missing</strong> - please provide a name or description for the source of this media item.';
	
	if (  splotbox_option('use_license') == '2' AND $wLicense == '--' ) $errors[] = '<strong>License Not Selected</strong> - select an appropriate license for this item.'; 
	
	if ( count($errors) > 0 ) {
		// form errors, build feedback string to display the errors
		$feedback_msg = 'Sorry, but there are a few errors in your information. Please correct and try again. We really want to add your item to ' . get_bloginfo( 'name' ) . ' <ul>';
		
		// Hah, each one is an oops, get it? 
		foreach ($errors as $oops) {
			$feedback_msg .= '<li>' . $oops . '</li>';
		}
		
		$feedback_msg .= '</ul>';
		
		// reset button states
		$previewBtnState = ' disabled';
		$submitBtnState = ' disabled';
		
		$box_style = '<div class="notify notify-red"><span class="symbol icon-error"></span> ';
		
	} else {
	
		// set notifications and display status
		if ( isset( $_POST['makeit'] ) ) {
			// final publish clicked
			
			// set status (will be either 'publish' or 'pending') for post based on theme settings
			$post_status = splotbox_option('new_item_status');		
			$box_style = '<div class="notify"><span class="symbol icon-info"></span> ';
			$is_published = true;
			
			if ( $post_status == 'publish' ) {
			
				// feed back for published item
				$feedback_msg = 'Your shared media item  <strong>' . $wTitle . '</strong> has been published!  You can <a href="'. get_permalink( $post_id ) . '">view it now</a>.  Or you can <a href="' . site_url()  . '">return to ' . get_bloginfo() . '</a>.';
				
				// for email
				$message = 'A media item <strong>"' . $wTitle . '"</strong> shared by <strong>' . $wAuthor . '</strong> has been published to ' . get_bloginfo() . '. You can <a href="'. site_url() . '/?p=' . $post_id  . '">see view it now</a>';
			
				
			} elseif ( $post_status == 'pending' ) { 
			
				$feedback_msg = 'Your shared media item  <strong>' . $wTitle . '</strong> is now in the queue for publishing.  You can <a href="'. site_url() . '/?p=' . $post_id . '&preview=true&ispre=1' . '"  target="_blank">preview it now</a> (link opens in a new window). It will appear on <strong>' . get_bloginfo() . '</strong> as soon as it has been reviewed. ';
				
				$message = 'A media item <strong>"' . $wTitle . '"</strong> shared by <strong>' . $wAuthor . '</strong> has been submitted to ' . get_bloginfo() . '. You can <a href="'. site_url() . '/?p=' . $post_id . 'preview=true' . '">preview it now</a>.<br /><br /> To  publish it, simply <a href="' . admin_url( 'edit.php?post_status=pending&post_type=post') . '">find it in the pending items</a> and change its status from <strong>Pending</strong> to <strong>Publish</strong>';
	
				
			} else {
				// draft
				$feedback_msg = 'Your shared media item  <strong>' . $wTitle . '</strong> has been submitted for review.  You can <a href="'. site_url() . '/?p=' . $post_id . '&preview=true&ispre=1"  target="_blank">preview it now</a> (link opens in a new window). It will appear on <strong>' . get_bloginfo() . '</strong> as soon as it has been approved. ';
				
				// for email
				$message = 'A media item <strong>"' . $wTitle . '"</strong> shared by <strong>' . $wAuthor . '</strong> has been submitted to ' . get_bloginfo() . '. You can <a href="'. site_url() . '/?p=' . $post_id . 'preview=true' . '">preview it now</a>.<br /><br /> To  publish it, simply <a href="' . admin_url( 'edit.php?post_status=draft&post_type=post') . '">find it in the drafts</a> and change its status from <strong>Draft</strong> to <strong>Publish</strong>';

			} // if $post_status
			
		} else {
			// updated button clicked
			$post_status = 'draft';
			$box_style = '<div class="notify notify-green"><span class="symbol icon-tick"></span> ';
			
			$feedback_msg = 'Your draft has been updated and can again be <a href="'. site_url() . '/?p=' . $post_id . '&preview=true&ispre=1' . '" target="_blank">previewed</a> to review changes. If it looks okay, then <a href="#theButtons">just scroll down</a> and click "Submit Item" to add it to' . get_bloginfo( 'name' ) . '.';
			
			// enable preview and submit buttons
			$previewBtnState = '';
			$submitBtnState = '';

		} // isset( $_POST['makeit'] )
		
		// update the basic post info
		
		$w_information = array(
			'post_title' => $wTitle,
			'post_content' => $wText,
			'post_status' => $post_status,
			'post_category' => $wCats		
		);

		// Is this a first draft?
		if ( $post_id == 0 ) {

			// insert as a new post
			$post_id = wp_insert_post( $w_information );
		
			//sets post-format
			set_post_format( $post_id, $wMediaType ); 
		
			// store media url
			add_post_meta( $post_id, 'media_url', $wMediaURL);
		
			// store the author as post meta data
			add_post_meta( $post_id, 'shared_by', $wAuthor );
		
			// store the name of person to credit
			add_post_meta( $post_id, 'credit', $wSource );

			// store the license code
			add_post_meta( $post_id, 'license', $wLicense );

			// store extra notes
			if ( $wNotes ) add_post_meta($post_id, 'extra_notes', $wNotes);
			
			// set featured image
			if ( $wUploadMediaID ) set_post_thumbnail( $post_id, $wUploadMediaID);
		
			// add the tags
			wp_set_post_tags( $post_id, $wTags);
			
			// feedback for first check of item
			$feedback_msg = 'A draft of your item has been saved. You can <a href="'. site_url() . '/?p=' . $post_id . '&preview=true&ispre=1' . '" target="_blank">preview it now</a>. This will open in a new tab/window. Or make any changes below,  check your information again and/or <a href="#theButtons">just scroll down</a> to submit it to ' . get_bloginfo() . '.';
			
		 } else {
			// the post exists, let's update and process the post

			// check if we have a publish button click
			if ( isset ( $_POST['makeit'] ) ) { // final processing
						
				if ( splotbox_option( 'notify' ) != '') {
				// Let's do some EMAIL!

					// who gets mail? They do.
					$to_recipients = explode( "," ,  splotbox_option( 'notify' ) );

					$subject = 'New Item Dropped into to ' . get_bloginfo();
		
					if ( $wNotes ) $message .= '<br /><br />There are some extra notes from the author:<blockquote>' . $wNotes . '</blockquote>';

					// turn on HTML mail
					add_filter( 'wp_mail_content_type', 'set_html_content_type' );

					// mail it!
					wp_mail( $to_recipients, $subject, $message);

					// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
					remove_filter( 'wp_mail_content_type', 'set_html_content_type' );	
						
				} // notifications for emailing
			
			} // isset( $_POST['makeit'] 

									
			// add the id to our array of post information so we can issue an update		
			$w_information['ID'] = $post_id;
			
			// check for possible podcast links for URL in audio items
			if ( $wMediaType == 'audio' and url_is_audio_link( $wMediaURL ) ) {
				// insert audio shortcode to make a link and force it into the podcast feed
				$w_information['post_content'].= "\n" . '[audio src="' . $wMediaURL . '"]' . "\n";
			}			

			// update the post
			wp_update_post( $w_information );
		
			//set post-format
			set_post_format( $post_id, $wMediaType ); 
		
			//  media url
			update_post_meta( $post_id, 'media_url', $wMediaURL);
		
			// author
			update_post_meta( $post_id, 'shared_by', $wAuthor );

			// credit
			update_post_meta( $post_id, 'credit', $wSource );
		
			// store the license code
			update_post_meta( $post_id, 'license', $wLicense );

			// extra notes
			if ( $wNotes ) update_post_meta($post_id, 'extra_notes', $wNotes);

			// tags
			wp_set_post_tags( $post_id, $wTags);			
		
		} // post_id = 0
	
	} // count errors


} elseif ( $wAccessCodeOk ) { 
	// first time entry
	
	// default welcome message
	$feedback_msg = splotbox_form_default_prompt() . '. Fields marked  <strong>*</strong> are required.';

	
	$wAuthor = 'Anonymous';
	$wLicense = '--';
	
	
	$wUploadMediaID = $post_id = 0;
	
	$wMediaMethod = "by_url";

	// not yet saved
	$is_published = false;
	$box_style = '<div class="notify"><span class="symbol icon-info"></span> ';

	// initial button states
	$previewBtnState = ' disabled';
	$submitBtnState = ' disabled';

}
// end form submmitted check

get_header(); 

?>

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
			    	
		    		<?php echo $box_style . $feedback_msg . '</div>';?>   
		    				
	<?php if (!$wAccessCodeOk) : // show the access code form ?>
	
		<form  id="splotboxform" method="post" action="">
			<fieldset>
				<label for="wAccess">Access Code</label><br />
				<p>Enter the special code to access the sharing form</p>
				<input type="text" name="wAccess" id="wAccess" class="required" value="<?php echo $wAccess?>"  />
			</fieldset>	
		
			<fieldset> 
			<?php wp_nonce_field( 'splotbox_form_access', 'splotbox_form_access_submitted' )?>
			
			<input type="submit" class="pretty-button pretty-button-final" value="Check Code" id="checkit" name="checkit">
			</fieldset>
		</form>
			
	<?php elseif ( !$is_published ) : // show form it has not been published ?>
			
		<form  id="splotboxform" method="post" action="" enctype="multipart/form-data">
		
				
			

				<fieldset id="theUpload">
					<legend><?php splotbox_form_item_upload() ?></legend>
					
					<?php if ( splotbox_option('use_upload_media')  == '1') :?>
						<label for="wMediaMethod"><?php _e('Method of Sharing', 'garfunkel' ) ?> </label><br />
						
						<input type="radio" name="wMediaMethod" value="by_url" <?php if ( $wMediaMethod == "by_url" ) echo " checked"?>> <strong>By URL</strong> <span class="descrip"> for audio, video, or image content<?php if ( !empty($splotbox_supports)) echo ' that is published on sites including <span class="supports">' . $splotbox_supports . '</span>'?>.</span><br />
						<input type="radio" name="wMediaMethod" value="by_upload" <?php if ( $wMediaMethod == "by_upload" ) echo " checked"?>> <strong>By Upload</strong> <span class="descrip">audio or image file</span>
					
					
					<?php endif?>
					
					
					<div id="media_by_url" <?php if ( $wMediaMethod == "by_upload" ) echo ' style="display:none;"'?>>
					
						<label for="wMediaURL"><?php _e('Enter Media URL', 'garfunkel' ) ?> <span class="required">*</span></label><br />
						<p>Embed a media player for audio, video, or image content for audio, video, or image content<?php if ( !empty($splotbox_supports)) echo ' that is published on sites including <span class="supports">' . $splotbox_supports . '</span>'?>.  The URL you enter should be the one that displays the content from the service. For audio and image content you can also use a direct web address to a media file -- one that links directly to <code>.mp3 .m4a .ogg .jpg .png .gif</code> files).</p>
					
						<p>Enter a full web address for the item (including http:// or https://)</p>
						<input type="text" name="wMediaURL" id="wMediaURL" class="required pstate" value="<?php echo $wMediaURL; ?>"/> 
					
						<?php $testbuttonclass = ( empty($wMediaURL) ) ? ' disabled' : '';?>
					
						<p>It's a good idea to  <a href="<?php echo $wMediaURL?>" class="pretty-button pretty-button-gray<?php echo $testbuttonclass?>" id="testURL" target="_blank">Test Link</a>  to make sure it works!</p>
					</div>


					<?php if ( splotbox_option('use_upload_media')  == '1') :?>
					
					
					<div id="media_by_upload" <?php if ( $wMediaMethod == "by_url" ) echo ' style="display:none;"'?>>
					
						<label for="headerImage"><?php _e('Upload a File', 'garfunkel') ?></label>

						<p>Audio files of type <code>.mp3 .m4a .ogg</code> or image files of type <code>.jpg .png .gif</code> less than <?php echo splotbox_max_upload(); ?> can be uploaded to this site. </p>
						<div class="uploader">
						
							<input id="wUploadMedia" name="wUploadMedia" type="hidden" value="<?php echo $wUploadMediaID?>" />
						
							<?php
								if ($wUploadMediaID) {
									$defthumb = wp_get_attachment_image( $wUploadMediaID, 'thumbnail', true, array( "id" => "mediathumb" ) );
								} else {
									$defthumb = '<img src="https://placehold.it/150x150?text=Media+holder" alt="" width="150" height="150" id="mediathumb">';
				
								}
							?>
						
							<?php echo $defthumb?>
						
							</div>
						
							<p><?php splotbox_form_item_upload_prompt() ?> <span id="uploadresponse"><?php echo $w_upload_status?></span><br clear="left"></p>
							<p id="footlocker"></p>
							<p id="idlocker"></p>
					
							<div id="splotdropzone">
								<input type="file" accept="image/*,audio/*" name="wUploadFile" id="wUploadFile">
								<p id="dropmessage">Drag file or click to select file to upload</p>
							</div>
						</div>
						
						
						<?php endif?>
						
						
					
				</fieldset>						

				<fieldset id="theMedia">
					<legend>Media Info</legend>
					<label for="wTitle"><?php splotbox_form_item_title() ?> <span class="required">*</span></label><br />
					<p><?php splotbox_form_item_title_prompt() ?> </p>
					<input type="text" name="wTitle" id="wTitle" class="required pstate" value="<?php echo $wTitle; ?>" />
				
					<?php if (  splotbox_option('use_caption') > '0'):	
  						$required = (splotbox_option('use_caption') == 2) ? '<span class="required">*</span>' : '';
  					?>
  				
					<label for="wText"><?php splotbox_form_item_description() ?> <?php echo $required?></label>
					
						<p><?php splotbox_form_item_description_prompt()?> </p>
	
						<?php if ( splotbox_option('caption_field') == 's'): // which text editor ?>	
							
							<textarea name="wText" id="wText" rows="15" class="pstate"><?php echo stripslashes( $wText );?></textarea>
							
							
						<?php else:?>
													
							<?php
							// set up for inserting the WP rich text editor
							$settings = array( 
								'textarea_name' => 'wText', 
								'editor_class' => 'pstate',
								'editor_height' => '300', 
								'media_buttons' => false
							);

							wp_editor(  stripslashes( $wText ), 'wText', $settings );
						
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
						
							echo '<br /><input type="checkbox" name="wCats[]" class="pstate" value="' . $acat->term_id . '" data-checkbox-text="' . $acat->name . '" ' .  $checked . '> ' . $acat->name;
						}
					
						?>
						</fieldset>
					<?php endif?>
					
					<?php if (splotbox_option('show_tags') ):?> 
						<label for="wTags"><?php  splotbox_form_item_tags() ?></label>
						<p><?php  splotbox_form_item_tags_prompt() ?></p>
					
						<input type="text" name="wTags" id="wTags" class="pstate" value="<?php echo $wTags; ?>"  />
					
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
						<input type="text" name="wSource" id="wSource" class="required pstate" value="<?php echo $wSource; ?>" />
					
					<?php endif?>


					<?php if ( splotbox_option('use_license') > '0'):	
  						$required = (splotbox_option('use_license') == 2) ? '<span class="required">*</span>' : '';
  					?>
  					
						<label for="wLicense"><?php splotbox_form_item_license() ?> <?php echo $required?></label><br />
						<p><?php splotbox_form_item_license_prompt() ?></p>
						<select name="wLicense" id="wLicense" class="pstate" />
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

				<fieldset  id="theAuthor">
					<legend>Your Info</legend>
					<label for="wAuthor"><?php splotbox_form_item_author()?> <span class="required">*</span></label><br />
					<p><?php splotbox_form_item_author_prompt()?></p>
					<input type="text" name="wAuthor" class="required pstate" id="wAuthor"  value="<?php echo $wAuthor; ?>" />
					
					
					<label for="wNotes"><?php splotbox_form_item_editor_notes() ?></label>						
						<p><?php splotbox_form_item_editor_notes_prompt() ?></p>
						<textarea name="wNotes" id="wNotes" class="pstate" rows="10" ><?php echo stripslashes($wNotes);?></textarea>

				</fieldset>	

			
				<fieldset id="theButtons">	
				<label for="theButtons"><?php splotbox_form_item_submit_buttons() ?></label>
				
				<?php  wp_nonce_field( 'splotbox_form_make', 'splotbox_form_make_submitted' ); ?>
				
				<p><?php splotbox_form_item_submit_buttons_prompt() ?></p>
				
				
				<input type="submit" value="Check Info" id="checkit" name="checkit">
				
				
				
				<a href="<?php echo site_url() . '/?p=' . $post_id . '&preview=true&ispre=1'?>" title="Preview of your item."  id="wPreview" class="fbutton<?php echo $previewBtnState?>" target="_blank">Preview</a>
				
				<input type="submit" value="Submit Item" id="makeit" name="makeit" <?php echo $submitBtnState?>>
				
				<input name="post_id" type="hidden" value="<?php echo $post_id?>" />
				<input name="wAccessCodeOk" type="hidden" value="true" />				
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