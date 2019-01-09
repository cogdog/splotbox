<?php
// ------------------------ check vars ------------------------

$page_id = $post->ID;

// all allowable licenses for this theme
$all_licenses = splotbox_get_licences();

if ( isset( $wp_query->query_vars['flavor'] ) ) {
	$license_flavor = $wp_query->query_vars['flavor'];

	// make sure we have something in the set of allowed ones; otherwise set to none
	if ( ! array_key_exists ( $license_flavor, $all_licenses ) ) $license_flavor = 'none';
	
} else {
	// no license in query string
	$license_flavor = 'none';
}

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
?>

<?php get_header(); ?>

<div class="wrapper">
										
	<div class="wrapper-inner section-inner thin">
	
	<?php if ($license_flavor == 'none') :?>
	
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
				
							<?php if ( splotbox_option('use_license') > 0 ):?>
								<ul>
								<?php
				
									foreach ( $all_licenses as $abbrev => $title) {
									
										// get number of items with this license
										$lcount = splotbox_get_license_count( $abbrev ); 
										
										// show if we have some
										if ( $lcount > 0 ) {
											echo '<li><a href="' . site_url() . '/licensed/' . $abbrev . '">' . $title . '</a> (' . $lcount . ")</li>\n";
										}
									}
				
								?>
								</ul>
							<?php else:?>
				
								<p>The current settings for this site are to not use or display licenses; the site administration can enable this feature from the <code>SPLOTbox Options.</code> </p>
				
				
							<?php endif?>
							<?php edit_post_link( __( 'Edit', 'garfunkel' ) . ' &rarr;', '<div class="clear"></div>'); ?>
																											
						</div><!-- .post-content -->
						
						<?php endwhile; else: ?>
			
						<p><?php _e( "We couldn't find any content. Please try again.", "garfunkel" ); ?></p>
		
						<?php endif; ?>
		
						<div class="clear"></div>
					
					</div><!-- .post-inner -->
			</div><!-- .post -->
			
		<?php else:?>
	
			<?php
				$args = array(
				'meta_key'   => 'license',
				'meta_value' => $license_flavor,
				'paged'         => $paged,
			);
	
			$my_query = new WP_Query( $args );

			// Pagination steps
			$temp_query = $wp_query;
			$wp_query   = NULL;
			$wp_query   = $my_query;		

			?>
			
		<div class="page-title">
		

			<h5><?php echo $my_query->found_posts?> Items Licensed <?php echo $all_licenses[$license_flavor]; ?> 
			
			<?php
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			
			if ( "1" < $my_query->max_num_pages ) : ?>
			
				<span><?php printf( __('Page %s of %s', 'garfunkel'), $paged, $my_query->max_num_pages ); ?></span>
				
				<div class="clear"></div>
			
			<?php endif; ?></h5>
				
		
		</div> <!-- /page-title -->

		<div class="content">
		
			<?php if ( $my_query->have_posts() ) : ?>
		
				<div class="posts">
				
					<?php while ( $my_query->have_posts() ) : $my_query->the_post(); ?>
					
						<?php get_template_part( 'content', get_post_format() ); ?>							
						
					<?php endwhile; ?>
								
				</div><!-- .posts -->
							
				<?php if ( $my_query->max_num_pages > 1 ) : ?>
				
					<div class="archive-nav">
					
						<?php echo get_next_posts_link( '&laquo; ' . __( 'Older Items', 'garfunkel' ) ); ?>
							
						<?php echo get_previous_posts_link( __( 'Newer Items', 'garfunkel' ) . ' &raquo;' ); ?>
						
						<?php
						// Reset postdata
						wp_reset_postdata();

						// Reset main query object
						$wp_query = NULL;
						$wp_query = $temp_query;
						?>

						
						<div class="clear"></div>
						
					</div><!-- .post-nav archive-nav -->
					
					<div class="clear"></div>
					
				<?php endif; ?>
						
			<?php endif; ?>
			
			
		
		</div><!-- .post -->
	
	<?php endif; ?>	
	
	<?php get_sidebar(); ?>
		
	</div><!-- .content -->

</div><!-- .wrapper-inner -->

</div> <!-- .wrapper -->
								
<?php get_footer(); ?>