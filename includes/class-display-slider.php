<?php
/**
 * Exit if accessed directly
 **/
 if( !defined( 'ABSPATH' ) ) {
	exit;
 }

/**
 * Class to easily display carousel
 *
 * @author Mayra Perales <m.j.perales@tcu.edu>
 **/
class Tcu_Display_Slider {

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	public function __construct() {

		// [tcu_carousel id="1"]
		add_shortcode( 'tcu_carousel', array( $this, 'create_shortcode' ) );

		// print thickbox
		add_action( 'admin_footer', array( $this, 'media_thickbox' ) );

		// print media button
		add_action( 'media_buttons', array( $this, 'media_button' ), 999 );

		// Add jQuery settings into the header
		add_action( 'wp_head', array( $this, 'add_jquery' ) );

	}

	/**
	 * Sets up an array on our carousel object
	 *
	 * @param array/object $data The array of data
	 * @return array
	 **/
	public static function set_array( $data ) {

		foreach( $data as $key => $value ) {
			$key = $value;
		}

		return $data;
	}

	/**
	 * Query our carousels
	 *
	 * @param $args array Arguments to be merged with the posts query
	 * @return WP_Query
	 **/
	public static function query_carousels( $args = array() ) {

		$carousels = array();

		// Our default query arguments
		$defaults = array(
			'post_status'   => 'publish',
			'post_type'     => Tcu_Create_Slider::$post_type_name,
			'orderby'       => 'ID',
			'order'         => 'ASC',
			'no_found_rows' => true
		);

		// Merge the query arguments
		$args = array_merge( (array) $defaults, (array) $args );

		// Execute the query
		$query = get_posts( $args );

		return $query;
	}

	/**
	 * Get all carousels
	 *
	 * @return $output array
	 **/
	public static function all_sliders() {

		// Query our post type
		$carousels = self::query_carousels();

		$output = array();

		// Loop through each carousel post
		foreach( $carousels as $post ) {

			// Set up our array
			$slider = self::set_array( get_object_vars( $post ) );
			$output[] = $slider;
		}

		wp_reset_postdata();

		return $output;
	}

	/**
	 * Grab carousel by the post ID
	 *
	 * @param $id int The slider ID
	 * @return $output array
	 **/
	public static function get_by_id( $id ) {

		$post = get_post( $id );

		// Check if $post exists
		if( !$post ) {
			return false;
		}

		// Check we are using the correct post_type
		if( $post->post_type != Tcu_Create_Slider::$post_type_name ) {
			return false;
		}

		$output = array();

		// Set up our array
		$slider = self::set_array( get_object_vars( $post ) );
		$output[] = $slider;

		return $output;
	}

	/**
	 * Let's add auto embed into our custom meta data
	 *
	 * @param $content string The content within each slide
	 * @return $content string Embeded content
	 **/
	public static function auto_embed( $content ) {

		if( isset( $GLOBALS['wp_embed'] ) ) {
    		$content = $GLOBALS['wp_embed']->autoembed( $content );
    	}

    	return $content;
	}

	/**
	 * Render our carousel's HTML
	 *
	 * @param $carousel object The WP_Query
	 * @return HTML Shell
	 **/
	public static function render_html( $carousel ) {

		// Get our slides
		$slides = get_post_meta( $carousel[0]['ID'], '_slider_content_tcu', true );

		$html  = '<div class="carousel-bxslider">';
		$html .= '<div class="carousel" id="carousel-' . $carousel[0]['ID'] . '">';

		foreach( $slides as $slide ) {
			$embed_content = self::auto_embed( $slide );
			$html .= '<div class="cf">' . wpautop( do_shortcode( $embed_content ) )  .'</div>';
		}

		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Let's create our shortcode
	 *
	 * @param $atts
	 **/
	public static function create_shortcode( $atts ) {

		extract( shortcode_atts( array( 'id' => false ), $atts ) );

		if( !empty( $id ) ) {

			$slider = self::get_by_id( $id );
			$html = self::render_html( $slider );
		}

		return $html;
	}

	/**
	 * Prints the "Add Carousel" media thickbox
	 *
	 * @return void
	 **/
	public function media_thickbox() {

		global $pagenow;

		if( ( 'post.php' || 'post-new.php' ) != $pagenow ) {
			return;
		}

		// let's get all our carousels
		$carousels = self::all_sliders(); ?>

		<style type="text/css">
			.section {
				padding: 15px 15px 0 15px;
			}
		</style>

		<script type="text/javascript">
			/**
			 * Send shortcode to the editor
			 */

			var insertCarousel = function() {

			 	var id = jQuery('#tcu-carousel').val();

			 	// alert if no carousel was selected
			 	if( '-1' === id ) {
			 		return alert( "<?php _e('Please select a carousel', 'tcu_carousel_bxslider') ?>" );
			 	}

			 	// Send shortcode to editor
			 	send_to_editor( '[tcu_carousel id="' + id + '"]' );

			 	// close thickbox
			 	tb_remove();
			 }
		</script>

		<div id="select-tcu-carousel" style="display: none;">
			<div class="section">
				<h2><?php _e( 'Add a Carousel', 'tcu_carousel_bxslider' ); ?></h2>
				<span><?php _e( 'Select a carousel to insert from the box below.', 'tcu_carousel_bxslider' ); ?></span>
			</div>

			<div class="section">
				<select name="tcu_carousel" id="tcu-carousel">
					<option value="-1">
						<?php _e( 'Select carousel', 'tcu_carousel_bxslider' ); ?>
					</option>
					<?php
						foreach( $carousels as $carousel ) {
							echo "<option value=\"{$carousel['ID']}\">{$carousel['post_title']} (ID #{$carousel['ID']})</option>";
						}
					?>
				</select>
			</div>

			<div class="section">
				<button id="insert-carousel" class="button-primary" onClick="insertCarousel();"><?php _e( 'Insert Carousel', 'tcu_carousel_bxslider' ); ?></button>
				<button id="close-carousel-thickbox" class="button-secondary" style="margin-left: 5px;" onClick="tb_remove();"><a><?php _e( 'Close', 'tcu_carousel_bxslider' ); ?></a></button>
			</div>
		</div>
	<?php }

	/**
	 * Add media button
	 *
	 *@param $editor_id int The editor ID
	 **/
	public function media_button( $editor_id ) { ?>

		<style type="text/css">
			.wp-media-buttons .insert-carousel span.wp-media-buttons-icon {
				margin-top: -2px;
			}
			.wp-media-buttons .insert-carousel span.wp-media-buttons-icon:before {
				content: "\f123";
				font: 400 18px/1 dashicons;
				speak: none;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
			}
		</style>

		<a href="#TB_inline?width=480&amp;inlineId=select-tcu-carousel" class="button thickbox insert-carousel" data-editor="<?php echo esc_attr( $editor_id ); ?>" title="<?php _e( 'Add a carousel', 'tcu_carousel_bxslider' ); ?>">
			<span class="wp-media-buttons-icon dashicons dashicons-format-image"></span><?php _e( ' Add carousel', 'tcu_carousel_bxslider' ); ?>
		</a>
	<?php }

	/**
	 * Add jQuery into the header
	 *
	 **/
	public function add_jquery() {

		// get option values from the database
		$options = get_option('tcu_carousel_settings', false);

		// Default settings
		$defaults = array(
				'slide_tran'      => 'horizontal',
				'speed'           => '500',
				'auto_controls'   => 'false',
				'auto_hover'      => 'false',
				'slider_arrows'   => 'false',
				'adaptive_height' => 'false'
			);

		if(  !( $options == false ) ) {
			// Let's merge our defaults
			$defaults = array_merge( (array) $defaults, (array) $options );
		} ?>

		<script type="text/javascript">
			jQuery(document).ready(function($) {
			 	// Let's grab our carousel's ID
				 jQuery( '.carousel-bxslider' ).each( function( index ){
			 		var currentSlider = jQuery(this).find('.carousel').attr('id');

			 		jQuery('#' + currentSlider).bxSlider({
						mode: <?php echo '"' . $defaults['slide_tran'] . '"'; ?>,
						speed: <?php echo $defaults['speed']; ?>,
						auto: true,
						autoControls: <?php echo $defaults["auto_controls"]; ?>,
						autoHover: <?php echo $defaults["auto_hover"]; ?>,
					 	controls: <?php echo $defaults["slider_arrows"]; ?>,
						adaptiveHeight: <?php echo $defaults["adaptive_height"]; ?>,
						infiniteLoop: true,
						useCSS: false,
						responsive: true
					});
				 });
			});
		</script>
	<?php }
}

?>