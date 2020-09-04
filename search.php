<?php get_header(); ?>

	<div class="wrapper">

		<div class="wrapper-inner section-inner">

			<div class="page-title">

					<h5><?php printf(
						_n(
							'%s Item found for "%s"',
							'%s Items found for "%s"',
							$wp_query->found_posts,
							'garfunkel'
						),
						number_format_i18n( $wp_query->found_posts ),
						get_search_query());?>
					</h5>

				</div>

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

						</div>

					<?php endif; ?>

				<?php else : ?>

					<div class="section-inner">

						<div class="post-content">

							<p><?php _e( 'No results. Try again, would you kindly?', 'garfunkel' ); ?></p>

						</div><!-- .post-content -->

					</div><!-- .section-inner -->

					<div class="clear"></div>

				<?php endif; ?>

			</div><!-- .content -->

			<div class="clear"></div>

		<?php get_sidebar(); ?>

		</div><!-- .wrapper-inner -->

	</div><!-- .wrapper -->

<?php get_footer(); ?>
