<?php
/**
 * Exit if accessed directly
 **/
if( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Alias for displaying a carousel shortcode
 *
 * @param $id int The carousel's ID
 **/
if( ! function_exists('tcu_display_carousel') ) {

	function tcu_display_carousel( $id ) {

		echo do_shortcode( '[tcu_carousel id="' . $id . '"]' );
	}

}



?>