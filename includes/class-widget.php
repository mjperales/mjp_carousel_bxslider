<?php
/**
 * Exit if accessed directly
 */
if( !defined( 'ABSPATH' ) ) {
	exit;
}

// Load our widget
add_action( 'widgets_init', function() {
	register_widget( 'Tcu_Carouselbx_Widget' );
});

/**
 * Adds a 'Carousel' widget to the widget interface
 *
 * @uses Tcu_Create_Slider
 * @author Mayra Perales <m.j.perales@tcu.edu>
 **/
class Tcu_Carouselbx_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress
	 */
	public function __construct() {
		parent::__construct(
				'tcu_carouselbx_slider',
				__('Add Carousel'),
				array( 'description' => __('Add a carousel') )
			);
	}

	/**
	 * Front-end display of widget
	 * @see WP_Widget::widget()
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		extract( $args );

		// Before widget
		echo $before_widget;

		// Display our title
		if( !empty( $instance['title'] ) ) {
			echo $before_title . $instance['title'] . $after_title;
		}

		// Display our carousel
		if( !empty( $instance['id'] ) ) {
			echo do_shortcode( '[tcu_carousel id="' . $instance['id'] . '"]' );
		}

		// After widget
		echo $after_widget;
	}

	/**
	 * Back-end widget form
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database
	 */
	public function form( $instance ) {

		// check our values
		if( $instance ) {
			$title = esc_attr( $instance['title'] );
			$id = esc_attr( $instance['id'] );
		} else {
			$title = '';
			$id = '';
		}

		// Get all our carousels
		$carousels = Tcu_Display_Slider::all_sliders(); ?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('id'); ?>"><?php _e('Select Carousel:'); ?></label>
			<select name="<?php echo $this->get_field_name('id'); ?>" id="<?php echo $this->get_field_id('id'); ?>" class="widefat">
				<?php foreach( $carousels as $carousel ) { ?>
					<option value="<?php echo esc_attr( $carousel['ID'] ); ?>" <?php if( isset( $instance['id'] ) ) selected( $instance['id'], $carousel['ID'] ) ?>><?php echo esc_html( $carousel['post_title'] ) . sprintf( __(' (ID #%s)', 'tcu_carouselbx_slider'), $carousel['ID'] ); ?></option>
				<?php } ?>
			</select>
		</p>
	<?php }

	/**
	 * Sanitize widget form values as they are saved
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved
	 * @param array $old_instance Previously saved values from database
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['id'] = intval( $new_instance['id'] );
		return $instance;
	}
}

?>