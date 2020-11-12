<?php

# -----------------------------------------------------------------
# Identity License Page With Body Class
# -----------------------------------------------------------------

add_filter( 'body_class', 'splotbox_body_class');

function splotbox_body_class( $classes ) {
     if ( is_page(splotbox_get_license_page_id() ) )
          $classes[] = 'licensed';

     return $classes;
}


# -----------------------------------------------------------------
# Licensed to Licenses
# -----------------------------------------------------------------


function splotbox_get_licences() {
	// return as an array the types of licenses available

	return ( array (
				'c' => 'All Rights Reserved (copyrighted)',
				'u' => 'Unknown / Not Specified',
				'pd'	=> 'Public Domain',
				'yt'	=> 'YouTube Standard License',
				'cc0'	=> 'CC0 No Rights Reserved',
				'cc-by' => 'CC BY Creative Commons By Attribution',
				'cc-by-sa' => 'CC BY SA Creative Commons Attribution-ShareAlike',
				'cc-by-nd' => 'CC BY ND Creative Commons Attribution-NoDerivs',
				'cc-by-nc' => 'CC BY NC Creative Commons Attribution-NonCommercial',
				'cc-by-nc-sa' => 'CC BY NC SA Creative Commons Attribution-NonCommercial-ShareAlike',
				'cc-by-nc-nd' => 'CC By NC ND Creative Commons Attribution-NonCommercial-NoDerivs',
			)
		);
}


function splotbox_the_license( $lcode = '--' ) {
	// output the title of a license

	// passed by form with no menu selected
	if ($lcode == '--') return '';

	$all_licenses = splotbox_get_licences();

	echo $all_licenses[$lcode];
}

function splotbox_attributor( $license, $work_title, $work_link, $work_creator='') {

	// passed by form with no menu selected
	if ($license == '--') return (['n/a','n/a']);

	$all_licenses = splotbox_get_licences();

	$work_str = ( $work_creator == '') ? '"' . $work_title . '"' : '"' . $work_title . '" by ' . $work_creator;

	$work_str_html = ( $work_creator == '') ? '<a href="' . $work_link .'">"' . $work_title . '"</a>' : '<a href="' . $work_link .'">"' . $work_title . '"</a> by ' . $work_creator;

	switch ( $license ) {

		case 'c':
			return ( array(
						$work_str .  ' is &copy; All Rights Reserved.',
						$work_str_html . ' is &copy; All Rights Reserved.'
					)
			 );
			break;


		case 'u':
			return ( array(
						'The rights of ' . $work_str .  ' is unknown or not specified.',
						'The rights of ' . $work_str_html . ' is unknown or not specified.'
					)
			 );
			break;

		case 'yt':
			return ( array(
						$work_str .  ' is covered by a YouTube Standard License.',
						$work_str_html . ' is covered by a <a href="https://www.youtube.com/t/terms">YouTube Standard License</a>.'
					)
			 );
			break;

		case 'cc0':
			return ( array(
						$work_str . ' is made available under the Creative Commons CC0 1.0 Universal Public Domain Dedication.',
						$work_str_html .  ' is made available under the <a href="https://creativecommons.org/publicdomain/zero/1.0/">Creative Commons CC0 1.0 Universal Public Domain Dedication</a>.'
					)
			 );

			break;

		case 'pd':
			return ( array(
				$work_str . ' has been explicitly released into the public domain.',
				$work_str_html . ' has been explicitly released into the public domain.'
				)
			 );
			break;

		default:
			//find position in license where name of license starts
			$lstrx = strpos( $all_licenses[$license] , 'Creative Commons');

			return ( array(
					$work_str . ' is licensed under a ' .  substr( $all_licenses[$license] , $lstrx)  . ' 4.0 International license.',
					$work_str_html . ' is licensed under a <a href="https://creativecommons.org/licenses/' . $license . '/4.0/">' .  substr( $all_licenses[$license] , $lstrx)  . ' 4.0 International</a> license.'
				)
			 );
	}
}

function splotbox_get_license_count( $the_license ) {


	$lic_query = new WP_Query( array( 'post_status' => 'publish', 'meta_key' => 'license', 'meta_value' =>  $the_license ) );

   return $lic_query->found_posts;

}

// shortcode for generating a list of content by license, useful for widgets

add_shortcode("licensed", "splotbox_license_list");

function splotbox_license_list( $atts )  {

	if ( splotbox_option('use_license') > 0 ) {

		extract(shortcode_atts( array( "show" => 'used' ), $atts ));

		// all allowable licenses for this theme
		$all_licenses = splotbox_get_licences();

		$output = '<ul>';

		foreach ( $all_licenses as $abbrev => $title) {

			// get number of items with this license
			$lcount = splotbox_get_license_count( $abbrev );

			// show if we have some
			if ( $lcount > 0 or $show == 'all'  ) {
				$output .=  '<li><a href="' . site_url() . '/licensed/' . $abbrev . '">' . $title . '</a> (' . $lcount . ")</li>\n";
			}
		}

		$output .=  '</ul>';

	} else {

		$output = 'The current settings for this site are to not use licenses; the site administrator can enable this feature from the <code>SPLOTbox Options.</code>';
	}

	return $output;
}

?>
