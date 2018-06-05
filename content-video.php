<div class="post-container">

	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
		<?php if ( $pos = strpos( $post->post_content, '<!--more-->' ) ) : ?>

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
		
					echo $embed_code;
					
				} else {
					// then we have a sound file so show it as a player
					
					echo splotbox_get_videoplayer( $media_url );
					
				}
					
				?>
				
			</div><!-- .featured-media -->
			
		<?php endif; ?>
		
		<?php if ( is_sticky() ) : ?>
				
			<div class="is-sticky">
				<div class="genericon genericon-pinned"></div>
			</div>
		
		<?php endif; ?>
		
		<div class="post-inner">
		
			<?php if ( get_the_title() ) : ?>
		
				<div class="post-header">
					
				    <h2 class="post-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				    	    
				</div><!-- .post-header -->
			
			<?php endif; 
			
			if ( $pos = strpos( $post->post_content, '<!--more-->' ) ) {
				echo '<p class="post-excerpt">' . wp_strip_all_tags( mb_strimwidth( $content_parts['extended'], 0, 200, '...' ), true ) . '</p>';
				
				
			} else {
				the_excerpt();
			}
			
			garfunkel_meta(); ?>
		
		</div><!-- .post-inner -->
	
	</div>

</div>