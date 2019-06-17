<div class="post-container">

	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
		<?php $media_url =  get_post_meta($post->ID, 'media_url', 1);?>
		
		<?php if ( has_post_thumbnail() ) : ?>
		
			<div class="featured-media">
			
				<?php if ( is_sticky() ) echo '<span class="sticky-post">' . __( 'Sticky post', 'garfunkel' ) . '</span>'; ?>
			
				<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
								
					<?php the_post_thumbnail( 'post-thumbnail' ); ?>
					
				</a>
						
			</div><!-- .featured-media -->
			
			
		<?php else : ?>
			<div class="featured-media">
			
			
			<?php if ( is_sticky() ) echo '<span class="sticky-post">' . __( 'Sticky post', 'garfunkel' ) . '</span>'; ?>
			
				
				<?php
				// can we embed this audio url?
				if ( is_url_embeddable( $media_url ) ) {

					// then do it
					// oEmbed part before <!--more--> tag
					$embed_code = wp_oembed_get( $media_url ); 
	
					echo $embed_code;
				} else {
				
					?>
					<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
					<?php
					// then we have an image file so get it's code
					echo splotbox_get_imageplayer( $media_url );
					?>
					
					</a>
					<?php
				}

				?>
				
			</div>
		

				
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
			
			<?php endif;?>
		
		
			<?php the_excerpt(); ?>
		
			<?php garfunkel_meta(); ?>
		
		</div><!-- .post-inner -->
	
	</div>

</div>