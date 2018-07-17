<?php get_header(); ?>

<div class="wrapper">

	<div class="wrapper-inner section-inner">

		<?php
		$paged = get_query_var( 'paged' ) ?: 1;
		$total_post_count = wp_count_posts();
		$published_post_count = $total_post_count->publish;
		$total_pages = ceil( $published_post_count / $posts_per_page );
		
		if ( 1 < $paged ) : ?>
		
			<div class="page-title">
			
				<h5><?php printf( __( 'Page %s of %s', 'garfunkel' ), $paged, $wp_query->max_num_pages ); ?></h5>
				
			</div>
			
			<div class="clear"></div>
		
		<?php endif; ?>
	
		<div class="content">
																			                    
			<?php if ( have_posts() ) : ?>
			
				<div class="posts" id="posts">
						
					<?php while ( have_posts() ) : the_post();
					
						get_template_part( 'content', get_post_format() );
					
					endwhile;
					
				endif; ?>
				
				<div class="clear"></div>
				
			</div><!-- .posts -->
				
		</div><!-- .content -->
		
		<?php if ( $wp_query->max_num_pages > 1 ) : ?>
			
			<div class="archive-nav section-inner">
						
				<?php echo get_next_posts_link( '&larr; ' . __( 'Older items', 'garfunkel' ) ); ?>
							
				<?php echo get_previous_posts_link( __( 'Newer items', 'garfunkel' ) . ' &rarr;' ); ?>
				
				<div class="clear"></div>
				
			</div><!-- .archive-nav -->
		
		<?php endif; ?>
		
		<?php get_sidebar(); ?>
	
	</div><!-- .wrapper-inner -->
	
</div><!-- .wrapper -->
	              	        
<?php get_footer(); ?>