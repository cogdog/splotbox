<?php get_header(); ?>

<div class="wrapper">

	<div class="wrapper-inner section-inner">
	
		<div class="page-title">
			
			<?php if ( is_day() ) : ?>
				<h5><?php _e( 'Media by Date:', 'garfunkel' ); ?> <?php echo get_the_date(); ?></h5>

			<?php elseif ( is_month() ) : ?>
				<h5><?php _e( 'Media by Month:', 'garfunkel' ); ?> <?php echo get_the_date('F Y'); ?></h5>

			<?php elseif ( is_year() ) : ?>
				<h5><?php _e( 'Media by Year:', 'garfunkel' ); ?> <?php echo get_the_date('Y'); ?></h5>

			<?php elseif ( is_category() ) : ?>
				<h5><?php _e( 'Media on the Category:', 'garfunkel' ); ?> <?php echo single_cat_title( '', false ); ?></h5>

			<?php elseif ( is_tag() ) : ?>
				<h5><?php _e( 'Media Tagged:', 'garfunkel' ); ?> <?php echo single_tag_title( '', false ); ?></h5>
				
			<?php elseif ( is_author() ) :
				$curauth = ( isset( $_GET['author_name'] ) ) ? get_user_by( 'slug', $author_name) : get_userdata( intval( $author ) ); ?>
				<h5><?php _e( 'Author:', 'garfunkel' ); ?> <?php echo $curauth->display_name; ?></h5>

			<?php else : ?>
				<h5><?php _e( 'Media Archive', 'garfunkel' ); ?></h5>

			<?php endif; 
			
			$tag_description = tag_description();
			if ( ! empty( $tag_description ) ) {
				echo apply_filters( 'tag_archive_meta', '<div class="page-description">' . $tag_description . '</div>' );
			}
			?>
			
		</div><!-- .page-title -->
		
		<div class="content">
		
			<?php if ( have_posts() ) : ?>
		
				<div class="posts">
				
					<?php while ( have_posts() ) : the_post(); ?>
					
						<?php get_template_part( 'content', get_post_format() ); ?>							
						
					<?php endwhile; ?>
								
				</div><!-- .posts -->
							
				<?php if ( $wp_query->max_num_pages > 1 ) : ?>
				
					<div class="archive-nav">
					
						<?php echo get_next_posts_link( '&laquo; ' . __( 'Older items', 'garfunkel' ) ); ?>
							
						<?php echo get_previous_posts_link( __( 'Newer items', 'garfunkel' ) . ' &raquo;' ); ?>
						
						<div class="clear"></div>
						
					</div><!-- .post-nav archive-nav -->
					
					<div class="clear"></div>
					
				<?php endif; ?>
						
			<?php endif; ?>
		
		</div><!-- .content -->
	
	</div><!-- .wrapper-inner -->

</div><!-- .wrapper -->

<?php get_footer(); ?>