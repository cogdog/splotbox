<?php get_header();?>


<div class="wrapper">

	<div class="wrapper-inner section-inner">

		<div class="page-title">

			<?php if ( is_day() ) : ?>
				<h5><?php printf(
						_n(
							'%s Item From Date',
							'%s Items From Date',
							$wp_query->found_posts,
							'garfunkel'
						),
						number_format_i18n( $wp_query->found_posts )
					);?> <?php echo get_the_date(); ?></h5>

			<?php elseif ( is_month() ) : ?>
				<h5><?php printf(
						_n(
							'%s Item From Month',
							'%s Items From Month',
							$wp_query->found_posts,
							'garfunkel'
						),
						number_format_i18n( $wp_query->found_posts )
					);?>  <?php echo get_the_date('F Y'); ?></h5>

			<?php elseif ( is_year() ) : ?>
				<h5><?php printf(
						_n(
							'%s Item From Year',
							'%s Items From Year',
							$wp_query->found_posts,
							'garfunkel'
						),
						number_format_i18n( $wp_query->found_posts )
					);?>  <?php echo get_the_date('Y'); ?></h5>

			<?php elseif ( is_category() ) : ?>
				<h5><?php printf(
						_n(
							'%s Item From Category',
							'%s Items From Category',
							$wp_query->found_posts,
							'garfunkel'
						),
						number_format_i18n( $wp_query->found_posts )
					);?> "<?php echo single_cat_title( '', false ); ?>"</h5>

			<?php elseif ( is_tag() ) : ?>
				<h5><?php printf(
						_n(
							'%s Item Tagged',
							'%s Items Tagged',
							$wp_query->found_posts,
							'garfunkel'
						),
						number_format_i18n( $wp_query->found_posts )
					);?> "<?php echo single_tag_title( '', false ); ?>"</h5>

				<?php
				$tag_description = tag_description();
				if ( ! empty( $tag_description ) ) {
					echo apply_filters( 'tag_archive_meta', '<div class="page-description">' . $tag_description . '</div>' );
				}
				?>

			<?php elseif ( is_tax( 'post_format' ) ) : ?>
				<h5><?php printf(
						_n(
							'%s Item of Media Type',
							'%s Items of Media Type',
							$wp_query->found_posts,
							'garfunkel'
						),
						number_format_i18n( $wp_query->found_posts )
					);?> "<?php echo single_cat_title( '', false ); ?>"</h5>


			<?php elseif ( is_author() ) :
				$curauth = ( isset( $_GET['author_name'] ) ) ? get_user_by( 'slug', $author_name) : get_userdata( intval( $author ) ); ?>
				<h5><?php printf(
						_n(
							'%s Item by Author',
							'%s Items by Author',
							$wp_query->found_posts,
							'garfunkel'
						),
						number_format_i18n( $wp_query->found_posts )
					);?> <?php echo $curauth->display_name; ?></h5>

			<?php else : ?>
				<h5><?php printf(
						_n(
							'%s Item in Archive',
							'%s Items in Archive',
							$wp_query->found_posts,
							'garfunkel'
						),
						number_format_i18n( $wp_query->found_posts )
					);?> </h5>

			<?php endif?>

		</div><!-- .page-title .archive-title -->

		<div class="content">

			<?php if ( have_posts() ) : ?>

				<div class="posts">

					<?php while ( have_posts() ) : the_post(); ?>

						<?php get_template_part( 'content', get_post_format() ); ?>

					<?php endwhile; ?>

				</div><!-- .posts -->

				<?php if ( $wp_query->max_num_pages > 1 ) : ?>

					<div class="archive-nav">

						<?php echo get_next_posts_link( '&laquo; ' . __( 'Older Items', 'garfunkel' ) ); ?>

						<?php echo get_previous_posts_link( __( 'Newer Items', 'garfunkel' ) . ' &raquo;' ); ?>

						<div class="clear"></div>

					</div><!-- .post-nav archive-nav -->

					<div class="clear"></div>

				<?php endif; ?>

			<?php endif; ?>

		</div><!-- .content -->

	<?php get_sidebar(); ?>

	</div><!-- .wrapper-inner -->

</div><!-- .wrapper -->

<?php get_footer(); ?>
