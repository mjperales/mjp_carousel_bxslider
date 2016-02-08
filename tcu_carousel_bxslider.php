<?php
/*
	Plugin Name: TCU - Carousel Box Slider
	Description: Easily add a box slider to your theme. Fully responsive with horizontal, vertical, and fade modes. Slides can contain images, video, or HTML content. Includes a widget to easily add a box slider in any sidebar or widgetized area.
	Version: 2.0.1
	Author: Website & Social Media Management
	Author URI: http://mkc.tcu.edu/web-management.asp

	Copyright 2013 MarvinLabs (contact@marvinlabs.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

/**
 * Exit if accessed directly
 **/
if( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * let's get started!
 **/
if( class_exists('Tcu_Carousel_Box_Slider') ) {
	new Tcu_Carousel_Box_Slider;
}

/**
 * Registers plugin and loads functionality
 *
 * @author Mayra Perales <m.j.perales@tcu.edu>
 *
 **/
class Tcu_Carousel_Box_Slider {

	/**
	 * Our plugin version
	 *
	 * @var string
	 **/
	public static $version = '2.0.1';

	/**
	 * Our slider object
	 *
	 * @var object
	 */
	private $slider;

	/**
	 * Our admin page object
	 *
	 * @var object
	 */
	private $admin_page;

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	public function __construct() {

		// load our include files
		$this->load_files();

		// Activation and uninstall hooks
		register_activation_hook( __FILE__, array( &$this, 'activate_me' ) );
		register_deactivation_hook(  __FILE__, array( __CLASS__, 'uninstall_me' ) );

		// load all our css and js files
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

		// Instatiate slider class
		$this->create_slider();

		// Instatiate Settings page
		$this->create_settings_page();

		// Instatiate Settings page
		$this->display_slider();

	}

	/**
	 * Instantiate Tcu_Create_Slider class
	 *
	 **/
	protected function create_slider() {

		$this->slider = new Tcu_Create_Slider;

		return $this->slider;
	}

	/**
	 * Instantiate Tcu_Slide_Settings class
	 *
	 **/
	protected function create_settings_page () {

		$this->admin_page = new Tcu_Slider_Settings;

		return $this->admin_page;
	}

	/**
	 * Instantiate Tcu_Display_Slider
	 *
	 **/
	protected function display_slider () {

		$this->display_slider = new Tcu_Display_Slider;

		return $this->display_slider;
	}

	/**
	 * Activate plugin
	 *
	 * @return void
	 **/
	public function activate_me() {

		// Flush our rewrites
		flush_rewrite_rules();
	}

	/**
	 * uninstall plugin
	 *
	 * @return void
	 **/
	public static function uninstall_me() {

		// Flush our rewrites
		flush_rewrite_rules();

		$option_name = 'tcu_carousel_settings';

		// delete our settings
		delete_option( $option_name );

		// For site options in multisite
		delete_site_option( $option_name );

	}

	/**
	 * Load all our dependencies
	 *
	 * @return void
	 **/
	protected function load_files() {

		// load our slider class
		require_once( plugin_dir_path( __FILE__ ) . '/includes/class-create-slider.php' );

		// load our settings class
		require_once( plugin_dir_path( __FILE__ ) . 'includes/class-slider-settings.php' );

		// load our display class
		require_once( plugin_dir_path( __FILE__ ) . 'includes/class-display-slider.php' );

		// load our widget
		require_once( plugin_dir_path( __FILE__ ) . 'includes/class-widget.php' );

		// load our helper functions
		require_once( plugin_dir_path( __FILE__ ) . 'includes/helpers.php' );
	}


	/**
	 * Register our scripts
	 *
	 * @return void
	 **/
	public function register_scripts() {

		//$handle, $src, $deps, $ver, $in_footer
		wp_register_style( 'bx-slider-css', plugins_url('/css/bxslider-integration.css', __FILE__), array(), '', 'all' );
		wp_register_script('bxslider', plugins_url('/js/jquery.bxslider.min.js', __FILE__) , array('jquery'), '1.0', true);

		wp_enqueue_script('bxslider');
		wp_enqueue_style('bx-slider-css');


	}

}

?>