<?php
/**
 * Exit if access directly
 **/
if( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds Admin Slider Settings
 *
 * @author Mayra Perales <m.j.perales@tcu.edu>
 **/
class Tcu_Slider_Settings {

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	public function __construct() {

		add_action( 'admin_menu', array( &$this, 'create_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'define_admin_init' ) );
	}

	/**
	 * Create menu item
	 *
	 **/
	public function create_admin_menu() {

		add_submenu_page(
			'edit.php?post_type=tcu_box_slider',
			__('Carousel Settings'),
			__('Settings'),
			'manage_options',
			'tcu_boxslider_settings',
			array( $this, 'tcu_carousel_function_options' )
		);
	}

	/**
	 * Callback function for create_admin_menu
	 *
	 **/
	public function tcu_carousel_function_options() { ?>

		<div class="wrap">
			<h2>Carousel Box Slider</h2>
			<form action="options.php" method="POST">
				<?php settings_errors(); ?>
				<?php settings_fields( 'tcu_carousel_settings_group' ); ?>
				<?php do_settings_sections( 'tcu_bxslider_carousel' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
 	<?php }

	/**
	 * Register and define the form sections
	 *
	 **/
	public function define_admin_init() {

		register_setting(
			'tcu_carousel_settings_group',
			'tcu_carousel_settings',
			array( $this, 'validate_slider_settings' )
		);

		add_settings_section(
			'tcu_carousel_general_settings',
			'General Settings',
			array( $this, 'settings_form' ),
			'tcu_bxslider_carousel'
			);

		add_settings_field(
			'tcu_carousel_tran_string',
			'Slide Transition',
			array( $this, 'tcu_slide_transition' ),
			'tcu_bxslider_carousel',
			'tcu_carousel_general_settings'
		);

		add_settings_field(
			'tcu_carousel_arrows_string',
			'Show arrow controls?',
			array( $this, 'tcu_arrow_settings' ),
			'tcu_bxslider_carousel',
			'tcu_carousel_general_settings'
		);

		add_settings_field(
			'tcu_carousel_ticker_hover',
			'Pause slider on hover?',
			array( $this, 'tcu_auto_hover' ),
			'tcu_bxslider_carousel',
			'tcu_carousel_general_settings'
		);

		add_settings_field(
			'tcu_carousel_autocontrols_string',
			'Show auto controls?',
			array( $this, 'tcu_autocontrol_settings' ),
			'tcu_bxslider_carousel',
			'tcu_carousel_general_settings'
		);

		add_settings_field(
			'tcu_carousel_speed_string',
			'Slide Transition Speed',
			array( $this, 'tcu_speed_setting' ),
			'tcu_bxslider_carousel',
			'tcu_carousel_general_settings'
		);

		add_settings_field(
			'tcu_carousel_adaptive_height',
			'Adaptive Height',
			array( $this, 'tcu_adaptive_height' ),
			'tcu_bxslider_carousel',
			'tcu_carousel_general_settings'
		);
	}

	/**
	 * Render introduction text
	 *
	 **/
	public function settings_form() {

		echo '<p>Please select from the settings below to change default settings.</p>';
	}

	/**
	 * Render slide transition setting
	 *
	 **/
	public function tcu_slide_transition() {

		// get option 'slide_tran' value from the database
		$options = get_option( 'tcu_carousel_settings' );

		// echo the field
		echo '<p>The default type of transition to use to go from one slide to another one.</p>';
		echo '<select name="tcu_carousel_settings[slide_tran]">';
		echo '<option value="horizontal"' . selected( $options['slide_tran'], 'horizontal', false ) . '>Horizontal</option>';
		echo '<option value="vertical"' . selected( $options['slide_tran'], 'vertical', false ) . '>Vertical</option>';
		echo '<option value="fade"' . selected( $options['slide_tran'], 'fade', false ) . '>Fade</option>';
		echo '</select>';
		echo '<p><em>Default transition is horizontal</em></p>';
	}

	/**
	 * Render option to show arrows in the front end
	 *
	 **/
	public function tcu_arrow_settings() {
		// get option 'slider_arrows' value from the database
		$options = get_option( 'tcu_carousel_settings' );

		if ( !isset( $options['slider_arrows'] ) ) { $options['slider_arrows'] = false; }
			echo '<input name="tcu_carousel_settings[slider_arrows]" type="checkbox" value="true" class="code" ' . checked( 'true', $options['slider_arrows'], false ) . ' /> True';
			echo '<p>If checked, "Next" / "Prev" controls will be added</p>';
	}

	/**
	 * Render option to output Start/Stop controls
	 *
	 **/
	public function tcu_autocontrol_settings() {
		// get option 'auto_controls' value from the database
		$options = get_option( 'tcu_carousel_settings' );

		if ( !isset( $options['auto_controls'] ) ) { $options['auto_controls'] = false; }
			echo '<input name="tcu_carousel_settings[auto_controls]" type="checkbox" value="true" class="code" ' . checked( 'true', $options['auto_controls'], false ) . ' /> True';
			echo '<p>If checked, "Start" / "Stop" controls will be added</p>';
	}

	/**
	 * Render pause on hover option
	 *
	 **/
	public function tcu_auto_hover() {
		// get option 'auto_controls' value from the database
		$options = get_option( 'tcu_carousel_settings' );

		if ( !isset( $options['auto_hover'] ) ) { $options['auto_hover'] = false; }
			echo '<input name="tcu_carousel_settings[auto_hover]" type="checkbox" value="true" class="code" ' . checked( 'true', $options['auto_hover'], false ) . ' /> True';
			echo '<p>If checked, slider will pause when mouse hovers over slider.</p>';
	}

	/**
	 * Render slide transition duration option
	 *
	 **/
	public function tcu_speed_setting() {

		$options = get_option( 'tcu_carousel_settings' );

		echo '<input placeholder="500" id="tcu_carousel_speed_string" type="number" size="15" value="' . esc_attr( $options['speed'] ) . '" name="tcu_carousel_settings[speed]" class="small-text" />';
		echo '<p>Slide transition duration (in milliseconds)</p>';
	}

	/**
	 * Render adaptive height option
	 *
	 **/
	public function tcu_adaptive_height() {

		// get option 'adaptive_height' value from the database
		$options = get_option( 'tcu_carousel_settings' );

		if ( !isset( $options['adaptive_height'] ) ) { $options['adaptive_height'] = false; }
			echo '<input name="tcu_carousel_settings[adaptive_height]" type="checkbox" value="true" class="code" ' . checked( 'true', $options['adaptive_height'], false ) . ' /> True';
			echo '<p>Dynamically adjust slider height based on each slide\'s height</p>';
	}

	/**
	 * Validate input
	 *
	 * @param $input array The input data
	 **/
	public function validate_slider_settings( $input ) {

		// Create our array for storing the validated options
	    $output = array();

	    // Loop through each of the incoming options
	    foreach( $input as $key => $value ) {

	        // Check to see if the current option has a value. If so, process it.
	        if( isset( $input[$key] ) ) {

	            // Strip all HTML and PHP tags and properly handle quoted strings
	            $output[$key] = strip_tags( stripslashes( $input[ $key ] ) );

	        } // end if

	    } // end foreach

	    // Return the array processing any additional functions filtered by this action
	    return apply_filters( 'tcu_carousel_settings', $output, $input );
	}

}

?>