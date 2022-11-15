<?php

namespace Bannerbear\WP;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Registers the bannerbear widget
 *
 * @since 1.0.0
 */
class Bannerbear_Widget extends \WP_Widget {

	/**
	 * Class Constructor.
	 */
	public function __construct() {

		// Register widget.
		parent::__construct(
			'bannerbear', // Base ID
			'Bannerbear', // Name
			array(
				'description' => __( 'Displays a dynamic image', 'bannerbear' ),
			)
		);

	}

	/**
	 * Displays the widget on the front end
	 *
	 * @see WP_Widget::widget()
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 * @param string $args     Widget arguments.
	 * @param string $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		// Ensure $instance is an array.
		if ( ! is_array( $instance ) ) {
			$instance = array();
		}

		// Abort if no image ID is set.
		if ( ! empty( $instance['id'] ) ) {
			return;
		}

		// Display opening wrapper.
		echo $args['before_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		// Display title.
		if ( ! empty( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
			echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		}

		// Display the widget.
		bannerbear_image( $instance );

		// Display the closing wrapper.
		echo $args['after_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Returns a list of all published urls
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      int[]
	 */
	public function get_urls() {

		$urls = get_posts(
			array(
				'numberposts' => -1,
				'post_status' => array( 'publish' ),
				'post_type'   => 'bannerbear_url',
			)
		);
		$data = array();

		foreach ( $urls as $url ) {
			$data[] = array(
				'label' => $url->post_title,
				'value' => $url->ID,
			);
		}

		return $data;
	}

	/**
	 * Output widget settings
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 * @param       array $settings Previously saved values from database.
	 */
	public function form( $settings ) {

		// ensure $settings is an array
		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		$urls  = $this->get_urls();
		$url   = isset( $settings['id'] ) ? $settings['id'] : 0;
		$title = isset( $settings['title'] ) ? $settings['title'] : '';

		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'bannerbear' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>"><?php esc_html_e( 'Signed URL:', 'bannerbear' ); ?></label>

			<select name="<?php echo esc_attr( $this->get_field_name( 'form' ) ); ?>" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>">
				<option value="0" <?php selected( empty( $url ) ); ?>><?php esc_html_e( 'Select URL', 'bannerbear' ); ?></option>
				<?php foreach ( $urls as $_url ) : ?>
					<option value="<?php echo esc_attr( $_url['value'] ); ?>" <?php selected( $url, $_url['value'] ); ?>><?php echo esc_attr( $_url['label'] ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p class="description">
			<?php
				printf(
					// translators: %1$s is a link to the signed URL editor.
					wp_kses_post( __( 'You can edit or create signed URLs in the <a href="%s">Signed URLs</a> page.', 'bannerbear' ) ),
					esc_url( admin_url( 'edit.php?post_type=bannerbear_url' ) )
				);
			?>
		</p>

		<?php
	}

	/**
	 * Saves widget settings
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      array
	 * @param       array $new_instance new instance options.
	 * @param       array $old_instance old instance options.
	 */
	public function update( $new_instance, $old_instance ) {

		if ( ! empty( $new_instance['title'] ) ) {
			$new_instance['title'] = sanitize_text_field( $new_instance['title'] );
		}

		if ( ! empty( $new_instance['id'] ) ) {
			$new_instance['id'] = intval( $new_instance['id'] );
		}

		return $new_instance;
	}

}
