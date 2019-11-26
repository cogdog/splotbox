<?php

# -----------------------------------------------------------------
# login stuff - things to set up special user, prevent access to WP
# -----------------------------------------------------------------

// Add custom logo to entry screen... because we can
// While we are at it, use CSS to hide the back to blog and retried password links
add_action( 'login_enqueue_scripts', 'splot_login_logo' );

function splot_login_logo() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/site-login-logo.png);
            height:90px;
			width:320px;
			background-size: 320px 90px;
			background-repeat: no-repeat;
			padding-bottom: 0px;
        } 
    </style>
<?php }


// Make logo link points to blog, not Wordpress.org Change Dat
// -- h/t http://www.sitepoint.com/design-a-stylized-custom-wordpress-login-screen/

add_filter( 'login_headerurl', 'splot_login_link' );

function splot_login_link( $url ) {
	return 'https://splot.ca/';
}

/* Customize message above registration form */

add_filter('login_message', 'splot_add_login_message');

function splot_add_login_message() {
	return '<p class="message">To do all that is SPLOT!</p>';
}

// login page title
add_filter( 'login_headertext', 'splot_login_logo_url_title' );

function splot_login_logo_url_title() {
	return 'The grand mystery of all things SPLOT';
}

?>