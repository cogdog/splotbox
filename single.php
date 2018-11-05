<?php get_header(); ?>

<div class="wrapper">
										
	<div class="wrapper-inner section-inner thin">
	
		<div class="content">
												        
			<?php if ( have_posts() ) : while( have_posts() ) : the_post(); 
			
				$format = get_post_format();
				$wAuthor =  get_post_meta( $post->ID, 'shared_by', 1 );
				$wCredit = get_post_meta( $post->ID, 'credit', 1 );
				$wLicense = get_post_meta( $post->ID, 'license', 1 );
				$media_url = get_post_meta($post->ID, 'media_url', 1);
				
				?>
						
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<?php if ( $format == 'video' ) : ?> 
					
						<div class="featured-media">
			
							<?php
								
							// Fetch post content
							$content = get_post_field( 'post_content', get_the_ID() );
							
							// Get content parts
							$content_parts = get_extended( $content );

							// oEmbed part before <!--more--> tag
							$media_url = $content_parts['main'];
							
							// can we embed this audio url?
							if ( is_url_embeddable( $media_url ) ) {							

								// Use oEmbed for YouTube, et al
								$embed_code = wp_oembed_get( $media_url ); 
								
							} else {
								// then we have a video file so show it as a player
								
								echo splotbox_get_videoplayer( $media_url );
								
							}
													
							?>
						
						</div><!-- .featured-media -->

					<?php elseif ( $format == 'audio' ) : ?> 
					
						<div class="featured-media">
			
							<?php
				
							// Fetch post content
							$content = get_post_field( 'post_content', get_the_ID() );
				
							// Get content parts
							$content_parts = get_extended( $content );
							
							$media_url = $content_parts['main'];  // for reference below
							

							// can we embed this audio url?
							if ( is_url_embeddable( $media_url ) ) {

								// then do it
								// oEmbed part before <!--more--> tag
								$embed_code = wp_oembed_get( $media_url ); 
				
								echo $embed_code;

			
							} else {
								// then we have a sound file so show it as a player
								
								echo splotbox_get_audioplayer( $media_url );
								
							}
							

							?>
						
						</div><!-- .featured-media -->

					
					<?php elseif ( $format == 'quote' ) : ?> 
											
						<div class="post-quote">
							
							<?php
								
							// Fetch post content
							$content = get_post_field( 'post_content', get_the_ID() );
							
							// Get content parts
							$content_parts = get_extended( $content );
							
							// Output part before <!--more--> tag
							echo $content_parts['main'];
							
							?>
						
						</div><!-- .post-quote -->
						
					<?php elseif ( $format == 'link' ) : ?> 
					
						<div class="post-link">
			
							<?php
								
							// Fetch post content
							$content = get_post_field( 'post_content', get_the_ID() );
							
							// Get content parts
							$content_parts = get_extended( $content );
							
							// Output part before <!--more--> tag
							echo $content_parts['main'];
							
							?>
						
						</div><!-- .post-link -->
						
					<?php elseif ( $format == 'gallery' ) : ?> 
					
						<div class="featured-media">

							<?php garfunkel_flexslider( 'post-image' ); ?>
											
						</div><!-- .featured-media -->
				
					<?php elseif ( has_post_thumbnail() ) : ?>
					
						<div class="featured-media">
						
							<?php the_post_thumbnail( 'post-image' ); ?>
							
							<?php if ( ! empty( get_post( get_post_thumbnail_id() )->post_excerpt ) ) : ?>
											
								<div class="media-caption-container">
								
									<p class="media-caption"><?php echo get_post( get_post_thumbnail_id() )->post_excerpt; ?></p>
									
								</div>
								
							<?php endif; ?>
									
						</div><!-- .featured-media -->
					
					<?php endif; ?>
					
					<div class="post-inner">
					
						<div class="post-header">
						
							<p class="post-date"><?php the_time( get_option( 'date_format' ) ); ?><?php edit_post_link( __( 'Edit','garfunkel' ), '<span class="sep">/</span>' ); ?></p>
							
						    <?php the_title( '<h1 class="post-title">', '</h1>' ); ?>
						    
						</div><!-- .post-header -->
														                                    	    
						<div class="post-content">

							<?php 
							if ( $format == 'link' || $format == 'quote' || $format == 'video' ||  $format == 'audio') { 
								$content = $content_parts['extended'];
								$content = apply_filters( 'the_content', $content );
								echo $content;
							} else {
								the_content();
							}
							?>
							
							
							<?php 
								if (  url_is_audio_link ( $media_url )   ) echo '<p><a href="' . $media_url . '" class="download-link pretty-button pretty-button-gray" download>Download "' . get_the_title() . '"</a></p>';

			    		
			    		// Sharer
			    		echo '<p><strong>Shared by:</strong> ' . $wAuthor . '</p>';
			    		
			    		if ( ( splotbox_option('use_source') > 0 )  AND $wCredit ) echo '<p><strong>Item Credit:</strong> ' .  make_links_clickable($wCredit)  . '</p>';
			    		
			    		
			    		if  ( splotbox_option('use_license') > 0 ) {
			    			echo '<p><strong>Reuse License:</strong> ';
			    			splotbox_the_license( $wLicense );
			    			echo '</p>';
			    			
			    			// display attribution?
			    			if  ( splotbox_option( 'show_attribution' ) == 1 ) {
			    				$attributions = splotbox_attributor( $wLicense, get_the_title(), get_permalink(), $wCredit);?>
						
								<h4>Copy/Paste Text Attribution</h4>
								<textarea rows="2" onClick="this.select()" style="height:80px;"><?php echo $attributions[0]?></textarea>


								<h4>Copy/Paste HTML Attribution</h4>
								<textarea rows=5" onClick="this.select()" style="height:110px;"><?php echo $attributions[1]?></textarea>
							<?php
			    			}
			    		}
			    		
			    		
			    	?>

							<div class="clear"></div>
										        
						</div><!-- .post-content -->
						
						<?php 
						$args = array(
							'before'           => '<div class="clear"></div><p class="page-links"><span class="title">' . __( 'Pages:','garfunkel' ) . '</span>',
							'after'            => '</p>',
							'link_before'      => '<span>',
							'link_after'       => '</span>',
							'separator'        => '',
							'pagelink'         => '%',
							'echo'             => 1
						);
					
						wp_link_pages( $args ); 
						?>
						
					</div><!-- .post-inner -->
					            					
					<div class="post-meta bottom">
					
						<div class="tab-selector">
							
							<ul>
							
								<li>
									<a class="active tab-post-meta-toggle" href="#">
										<div class="genericon genericon-summary"></div>
										<span><?php _e( 'Item info', 'garfunkel' ); ?></span>
									</a>
								</li>
								<li>
									<a class="tab-comments-toggle" href="#">
										<div class="genericon genericon-comment"></div>
										<span><?php _e( 'Comments', 'garfunkel' ); ?></span>
									</a>
								</li>
							

							

								<div class="clear"></div>
								
							</ul>
							
						</div>
						
						<div class="post-meta-tabs">
						
							<div class="post-meta-tabs-inner">
							
								<div class="tab-comments tab">
								
									<?php 
										if ( splotbox_option('allow_comments') ) {
											comments_template( '', true ); 
										} else {
											echo '<p>Sorry, but comments are not enabled on this site.</p>';
										}
									
									?>
								
								</div><!-- .tab-comments -->
							
								
								<div class="tab-post-meta tab">
								
									<ul class="post-info-items fright">
									
										<?php if ($wAuthor):?>
										<li>
											<div class="genericon genericon-user"></div>
											<?php echo $wAuthor ?>
										</li>
										<?php endif?>
										
										<li>
											<div class="genericon genericon-time"></div>
											<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
												<?php the_date( get_option('date-format') ); ?>
											</a>
										</li>
										
										<?php 
											
											if ( $wCredit ) echo '<li><div class="genericon genericon-info"></div> ' .  make_links_clickable( $wCredit ) . '</li>';
											
											?>

										<?php if ($media_url):?>
										<li>
											<div class="genericon genericon-link"></div><a href="<?php echo $media_url; ?>" target="blank"><?php echo $media_url; ?></a>
										</li>
										<?php endif?>
										
										<?php if ($wLicense):?>										
										<li>
											<div class="genericon genericon-flag"></div>
											<?php echo splotbox_the_license( $wLicense ); ?>
										</li>										
										<?php endif?>
										
										<?php if ( splotbox_option('show_cats') ):?> 
											<li>
												<div class="genericon genericon-category"></div> 
												<?php the_category(', '); ?>
											</li>
										<?php endif?>
										
										<?php if ( has_tag() AND splotbox_option('show_tags') ) : ?>
											<li>
												<div class="genericon genericon-tag"></div>  
												<?php the_tags('', ', '); ?>
											</li>									
											
										<?php endif; ?>
									</ul>
								
									<div class="post-nav fleft">
									
										<?php
										$prev_post = get_previous_post();
										if ( ! empty( $prev_post ) ) : ?>
										
											<a class="post-nav-prev" title="<?php printf( __( 'Previous post: "%s"', 'garfunkel' ), esc_attr( get_the_title( $prev_post ) ) ); ?>" href="<?php echo get_permalink( $prev_post->ID ); ?>">
												<p><?php _e( 'Previous item', 'garfunkel' ); ?></p>
												<h4><?php echo get_the_title( $prev_post ); ?></h4>
											</a>
									
										<?php endif;
										
										$next_post = get_next_post();
										if ( ! empty( $next_post ) ) : ?>
											
											<a class="post-nav-next" title="<?php printf( __( 'Next post: "%s"', 'garfunkel' ), esc_attr( get_the_title( $next_post ) ) ); ?>" href="<?php echo get_permalink( $next_post->ID ); ?>">
												<p><?php _e( 'Next item', 'garfunkel' ); ?></p>
												<h4><?php echo get_the_title( $next_post ); ?></h4>
											</a>
									
										<?php endif; ?>
									
									</div>
									
									<div class="clear"></div>
								
								</div><!-- .tab-post-meta -->
																

								

							
							</div><!-- .post-meta-tabs-inner -->
						
						</div><!-- .post-meta-tabs -->
							
					</div><!-- .post-meta.bottom -->
					
					<div class="post-nav-fixed">
								
						<?php
						$prev_post = get_previous_post();
						if ( ! empty( $prev_post ) ) : ?>
						
							<a class="post-nav-prev" title="<?php printf( __( 'Previous item: "%s"', 'garfunkel' ), the_title_attribute( array( 'post' => $prev_post->ID ) ) ); ?>" href="<?php echo get_permalink( $prev_post->ID ); ?>">
								<span class="hidden"><?php _e( 'Previous item', 'garfunkel' ); ?></span>
								<span class="arrow">&laquo;</span>
							</a>
					
						<?php endif;
						
						$next_post = get_next_post();
						if ( ! empty( $next_post ) ) : ?>
							
							<a class="post-nav-next" title="<?php printf( __( 'Next item: "%s"', 'garfunkel' ), the_title_attribute( array( 'post' => $next_post->ID ) ) ); ?>" href="<?php echo get_permalink( $next_post->ID ); ?>">
								<span class="hidden"><?php _e( 'Next item', 'garfunkel' ); ?></span>
								<span class="arrow">&raquo;</span>
							</a>
					
						<?php endif; ?>
															
						<div class="clear"></div>
					
					</div><!-- .post-nav -->
												                        
			   	<?php endwhile; else: ?>
			
					<p><?php _e( "We couldn't find any items that matched your query. Please try again.", "garfunkel" ); ?></p>
				
				<?php endif; ?>    
				
				<?php get_sidebar(); ?>
						
			</div><!-- .post -->
		
		</div><!-- .content -->
		
		<div class="clear"></div>
		
	</div><!-- .wrapper-inner -->

</div><!-- .wrapper -->
		
<?php get_footer(); ?>