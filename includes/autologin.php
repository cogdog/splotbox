<?php

# -----------------------------------------------------------------
# login stuff - things to set up special user, prevent access to WP
# -----------------------------------------------------------------

// Add custom logo to entry screen... because we can
// While we are at it, use CSS to hide the back to blog and retried password links
add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_logo() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/site-login-logo.png);
            padding-bottom: 30px;
        }    
	#backtoblog {display:none;}
	#nav {display:none;}
    </style>
<?php }


// Make logo link points to blog, not Wordpress.org Change Dat
// -- h/t http://www.sitepoint.com/design-a-stylized-custom-wordpress-login-screen/

add_filter( 'login_headerurl', 'login_link' );

function login_link( $url ) {
	return get_bloginfo( 'url' );
}
 
 
// Auto Login

function splot_redirect_url() {
	// where to send them after login ok
	return ( site_url('/') . 'share' );
}

function splot_user_login( $user_login = 'sharer' ) {
	// login the special user account to allow authoring
	
	// check for the correct user
	$autologin_user = get_user_by( 'login', $user_login ); 
	
	if ( $autologin_user ) {
	
		// just in case we have old cookies
		wp_clear_auth_cookie(); 
		
		// set the user directly
		wp_set_current_user( $autologin_user->id, $autologin_user->user_login );
		
		// new cookie
		wp_set_auth_cookie( $autologin_user->id);
		
		// do the login
		do_action( 'wp_login', $autologin_user->user_login );
		
		// send 'em on their way
		wp_redirect( splot_redirect_url() );
		
	} else {
		// uh on, problem
		die ('Bad news. Looks like there is a missing account for "' . $user_login . '".');
	}
}

// remove admin tool bar for non-admins, remove access to dashboard
// -- h/t http://www.wpbeginner.com/wp-tutorials/how-to-disable-wordpress-admin-bar-for-all-users-except-administrators/

add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
	if ( !current_user_can('edit_others_posts')  ) {
	  show_admin_bar(false);
	}

}

function splotbox_author_user_check( $expected_user = 'sharer' ) {
// checks for the proper authoring account set up

	$auser = get_user_by( 'login', $expected_user );
		
	
	if ( !$auser) {
		return ('Authoring account not set up. You need to <a href="' . admin_url( 'user-new.php') . '">create a user account</a> with login name <strong>' . $expected_user . '</strong> with a role of <strong>Author</strong>. Make a killer strong password; no one uses it.');
	} elseif ( $auser->roles[0] != 'author') {
	
		// for multisite lets check if user is not member of blog
		if ( is_multisite() AND !is_user_member_of_blog( $auser->ID, get_current_blog_id() ) )  {
			return ('The user account <strong>' . $expected_user . '</strong> is set up but has not been added as a user to this site (and needs to have a role of <strong>Author</strong>). You can <a href="' . admin_url( 'user-edit.php?user_id=' . $auser->ID ) . '">edit it now</a>'); 
			
		} else {
		
			return ('The user account <strong>' . $expected_user . '</strong> is set up but needs to have it\'s role set to <strong>Author</strong>. You can <a href="' . admin_url( 'user-edit.php?user_id=' . $auser->ID ) . '">edit it now</a>'); 
		}	
		
	} else {
		return ('The authoring account <strong>' . $expected_user . '</strong> is correctly set up.');
	}
}

function splotbox_check_user( $allowed='sharer' ) {
	// checks if the current logged in user is who we expect
    
   $current_user = wp_get_current_user();
	
	// return check of match
	return ( $current_user->user_login == $allowed );
}

function splot_the_author() {
	// utility to put in template to show status of special logins
	// nothing is printed if there is not current user, 
	//   echos (1) if logged in user is the special account
	//   echos (0) if logged in user is the another account
	//   in both cases the code is linked to a logout script

	if ( is_user_logged_in() and !current_user_can( 'edit_others_posts' ) ) {
		$user_code = ( splotbox_check_user() ) ? 1 : 0;
		echo '<a href="' . wp_logout_url( site_url() ). '">(' . $user_code  .')</a>';
	}

}

?>