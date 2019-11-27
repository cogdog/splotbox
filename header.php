<!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

	<head>
		
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" >
		 
		<?php wp_head(); ?>
	
	</head>
	
	<body <?php body_class(); ?>>
	
		<div class="navigation">
		
			<div class="section-inner">
				
				<ul class="main-menu">
				
					<?php 

					if ( has_nav_menu( 'primary' ) ) {

						$nav_menu_args = array( 
							'container' 		=> '', 
							'items_wrap' 		=> '%3$s',
							'theme_location' 	=> 'primary', 
							'walker' 			=> new garfunkel_nav_walker
						);
																		
						wp_nav_menu( $nav_menu_args ); 
					
					// test if primary menu location is not set	
					} elseif ( !splot_is_menu_location_used() ) {
						echo splot_default_menu();
					// normal make menus from pages	
				
					} else {
					
						wp_list_pages( array(
					
							'container' => '',
							'title_li' => ''
					
						));
					
					}
					?>
					
					<li><form method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="search-field" class="genericon genericon-search"></label>
	<input type="search" value="" placeholder="<?php _e( 'Search ', 'garfunkel' ); ?>" name="s" class="search-field" id="search-field" /> 
</form></li>
					
					
											
				</ul><!-- .main-menu -->
				
				<?php get_template_part( 'menu', 'social' ); ?>
			 
			<div class="clear"></div>
			 
			</div><!-- .section-inner -->
			
			<div class="mobile-menu-container">
			
				<ul class="mobile-menu">
					
					<?php 
					if ( has_nav_menu( 'primary' ) ) {					
						wp_nav_menu( $nav_menu_args); 
						
					} elseif ( !splot_is_menu_location_used() ) {
						echo splot_default_menu();
					
					// normal make menus from pages	
					} else {
						wp_list_pages( $list_pages_args );
					} 
					?>
					<li><form method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="search-field" class="genericon genericon-search"></label>
	<input type="search" value="" placeholder="<?php _e( 'Search ', 'garfunkel' ); ?>" name="s" class="search-field" id="search-field" /> 
</form></li>
					
					
				
				</ul><!-- .mobile-menu -->
				
				<?php get_template_part( 'menu', 'social' ); ?>
										
			</div><!-- .mobile-menu-container -->
				 			
		</div><!-- .navigation -->
		
		<div class="title-section">

			<?php $header_image_url = get_header_image() ?: get_template_directory_uri() . '/images/bg.jpg'; ?>
			
			<div class="bg-image master" style="background-image: url( <?php echo $header_image_url; ?> );"></div>
			
			<div class="bg-shader master"></div>
		
			<div class="section-inner">
			
				<div class="toggle-container">
			
					<a class="nav-toggle" title="<?php _e( 'Click to view the navigation', 'garfunkel' ); ?>" href="#">
				
						<div class="bars">
						
							<div class="bar"></div>
							<div class="bar"></div>
							<div class="bar"></div>
							
							<div class="clear"></div>
						
						</div>
						
						<p>
							<span class="menu"><?php _e( 'Menu', 'garfunkel' ); ?></span>
							<span class="close"><?php _e( 'Close', 'garfunkel' ); ?></span>
						</p>
						
						<div class="clear"></div>
					
					</a>
				
				</div><!-- .toggle-container -->
		
				<?php if ( get_theme_mod( 'garfunkel_logo' ) ) : ?>
					
					<div class="blog-logo">
					
				        <a class="logo" href='<?php echo esc_url( site_url( '/' ) ); ?>' title='<?php echo esc_attr( get_bloginfo( 'title' ) ); ?> &mdash; <?php echo esc_attr( get_bloginfo( 'description' ) ); ?>' rel='home'>
				        	<img src='<?php echo esc_url( get_theme_mod( 'garfunkel_logo' ) ); ?>' alt='<?php echo esc_attr( get_bloginfo( 'title' ) ); ?>'>
				        </a>
			        
					</div>
			
				<?php elseif ( get_bloginfo( 'description' ) || get_bloginfo( 'title' ) ) : ?>
								
					<h1 class="blog-title">
						<a href="<?php echo esc_url( site_url() ); ?>" title="<?php echo esc_attr( get_bloginfo( 'title' ) ); ?> &mdash; <?php echo esc_attr( get_bloginfo( 'description' ) ); ?>" rel="home"><?php echo esc_attr( get_bloginfo( 'title' ) ); ?></a>
					</h1>
					
					<?php if ( get_bloginfo( 'description' ) ) : ?>
					
						<h3 class="blog-subtitle"><?php echo esc_attr( get_bloginfo( 'description' ) ); ?></h3>
						
					<?php endif; ?>
										
				<?php endif; ?>
			
			</div>
		
		</div>