<?php
/**
 * Exit if access directly
 **/
if( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers our quote post type
 * Adds our custom meta boxes
 *
 * @author Mayra Perales <m.j.perales@tcu.edu>
 **/
class Tcu_Create_Slider {

	/**
	 * Post Type Name
	 *
	 **/
	protected $post_type_name = 'tcu_box_slider';

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	public function __construct() {

		if( !post_type_exists( $this->post_type_name ) ) {
			add_action('init', array( &$this, 'register_quote_post_type' ) );
		}

		add_action( 'add_meta_boxes', array( $this, 'do_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta' ) );
	}

	/**
	 * Register our post type
	 *
	 * @return void
	 **/
	public function register_quote_post_type() {

		register_post_type( 'tcu_box_slider',

			// let's now add all the options for this post type
			array(
				'labels'             => array(
				'name'               => __( 'Carousel', 'tcu_carousel_bxslider' ),
				'singular_name'      => __( 'Carousel', 'tcu_carousel_bxslider' ),
				'all_items'          => __( 'All Carousels', 'tcu_carousel_bxslider' ),
				'add_new'            => __( 'Add New', 'tcu_carousel_bxslider' ),
				'add_new_item'       => __( 'Add New Carousel', 'tcu_carousel_bxslider' ),
				'edit'               => __( 'Edit', 'tcu_carousel_bxslider' ),
				'edit_item'          => __( 'Edit Carousel', 'tcu_carousel_bxslider' ),
				'new_item'           => __( 'New Carousel', 'tcu_carousel_bxslider' ),
				'view_item'          => __( 'View Carousel', 'tcu_carousel_bxslider' ),
				'search_items'       => __( 'Search Carousels', 'tcu_carousel_bxslider' ),
				'not_found'          => __( 'Nothing found in the Database.', 'tcu_carousel_bxslider' ),
				'not_found_in_trash' => __( 'Nothing found in Trash', 'tcu_carousel_bxslider' )
				),
				'description'         => __( 'Easily add carousel show with HTML content', 'tcu_carousel_bxslider' ),
				'public'              => false,
				'has_archive'         => false,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'supports'            => array('title'),
				'menu_position'       => 20,
				'menu_icon'           => 'dashicons-format-aside',
				'exclude_from_search' => true
			)

		); /* end of register post type */
	}

	/**
	 * Add meta boxes
	 *
	 **/
	public function do_meta_boxes() {

		//add_meta_box($id, $title, $callback, $post_type, $context, $priority);
		add_meta_box(
			'tcu_box_slider',
			'Enter Content for Carousel',
			array( $this, 'html_admin_view' ),
			'tcu_box_slider',
			'normal',
			'default'
		);
	}

	/**
	 * Render meta box content
	 *
	 * @param $post array The post object
	 **/
	public function html_admin_view( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'tcucarouselslider', 'tcucarouselslider_nonce' );

		$content = get_post_meta( $post->ID, '_slider_content_tcu', true );

		if( empty( $content ) ) {
			$content = array('');
		} ?>

		<!-- Begin table inside a div -->
		<div class="tcu_bxslider_repeat" style="margin-top: 1em; margin-bottom: 1em;">
			<table style="width: 100%;">
				<thead>
					<tr>
						<td colspan="2" style="border: 1px solid #DDDDDD; padding: 1em;">
							<a style="float: left;" class="add-slide button" href="#">Add</a>
						</td>
					</tr>
				</thead>
				<tbody class="quote-container">
				<?php for( $i = 0; $i < count( $content ); $i++ ) { ?>
					<tr class="row-repeat">
						<td width="80%" style="border-left: 1px solid #DDDDDD; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; padding: 1em;">
							<dl>
								<dt><h4><?php _e( 'Enter Content:', 'tcu_carousel_bxslider' ); ?></h4></dt>
								<dd style="margin-left: 0;">
								<?php
									$settings = array( 'media_buttons' => true, 'textarea_rows' => 4, 'textarea_name' => '_slider_content_tcu[]', 'wpautop' => true );
									wp_editor( $content[$i], '_slider_content_tcu_' . $i, $settings );
								?>
								</dd>
							</dl>
						</td>
						<td style="vertical-align: top; border-bottom: 1px solid #DDDDDD; border-right: 1px solid #DDDDDD;">
							 <a class="remove-slide button" href="#">Remove</a>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div><!-- end of .tcu_bxslider_repeat -->
		<script>
			jQuery(document).ready(function($){

				var incrementID = <?php echo count( $content ) - 1; ?>;

				$(document).on('click', '.tcu_bxslider_repeat .add-slide', function(e) {

					e.preventDefault();
					incrementID++;

					var contentID = '_slider_content_tcu_' + incrementID;

					// find the end of our table rows
					var endOfList = $('.row-repeat:last-child');

					tableRow = '<tr class="row-repeat">' +
							   '<td width="80%" style="border-left: 1px solid #DDDDDD; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; padding: 1em;">' +
							   '<dl>' +
							   '<dt><h4>Enter Content:</h4></dt>' +
							   '<dd style="margin-left: 0;">' +
							   '<div id="wp-' + contentID + '-editor-tools" class="wp-editor-tools hide-if-no-js">' +
							   '<div id="wp-' + contentID + '-media-buttons" class="wp-media-buttons">' +
							   '<button type="button" id="insert-media-button" class="button insert-media add_media" data-editor="' + contentID + '">' +
							   '<span class="wp-media-buttons-icon"></span> Add Media</button>' +
							   '</div>' +
							   '</div>' +
							   '<textarea class="wp-editor-area" name="_slider_content_tcu[]" id="' + contentID + '"></textarea> ' +
							   '</dd>' +
							   '</dl>' +
							   '</td>' +
							   '<td style="vertical-align: top; border-bottom: 1px solid #DDDDDD; border-right: 1px solid #DDDDDD;">' +
							   '<a class="remove-slide button" href="#">Remove</a></td>' +
							   '</tr>';

					// insert our clone into the end of our table
					$( tableRow ).insertAfter( endOfList );
					tinyMCE.execCommand( 'mceAddEditor', true, contentID );

				});

				$(document).on('click', '.tcu_bxslider_repeat .remove-slide', function(e) {

					e.preventDefault();

					// find how many list items we have
					var tableRowNum = $('.row-repeat').length;

					if ( tableRowNum <= 1 ) {
						// disable remove button if we only have one list item
						$(this).prop( "disabled", false ).css("cursor", "not-allowed");
					} else {
						$(this).parents('.row-repeat').remove();
					}

				});
			});
		</script>
	<?php }

	/**
	 * Save our meta box data
	 *
	 * @param $post_id int The post ID of the post being saved
	 **/
	public function save_meta( $post_id ) {

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( !isset( $_POST['tcucarouselslider_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( !wp_verify_nonce( $_POST['tcucarouselslider_nonce'], 'tcucarouselslider' ) ) {
			return;
		}

		// If this is an autosave, form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Sanitize user input.
		$content = wp_kses_post( $_POST['_slider_content_tcu'] );
		update_post_meta( $post_id, '_slider_content_tcu', $content );
	}

}


?>