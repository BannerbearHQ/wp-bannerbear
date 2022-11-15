<?php

namespace Bannerbear\WP;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Keeps track of each signed URL we display.
 *
 * @since 1.0.0
 */
class Output_Manager {

	/**
	 * @var int The # of URLs outputted
	 */
	public $count = 0;

	/**
	 * @var string
	 */
	public static $shortcode = 'bannerbear';

	/**
	 * Add hooks
	 */
	public function add_hooks() {
		add_action( 'init', array( $this, 'register_shortcode' ) );
		add_filter( 'the_content', array( $this, 'the_content' ) );
		add_action( 'wp_head', array( $this, 'display_og_image' ) );
	}

	/**
	 * Registers the [bannerbear] shortcode
	 */
	public function register_shortcode() {
		add_shortcode( self::$shortcode, array( $this, 'shortcode' ) );
	}

	/**
	 * @param array $attributes
	 * @param string $content
	 * @return string
	 */
	public function shortcode( $atts = array() ) {
		ob_start();
		$this->display( $atts );
		return ob_get_clean();
	}

	/**
	 * Displays a dynamic image based on the passed args.
	 *
	 * @param array $atts The atts with which to display the dynamic image.
	 */
	public function display( $atts = array() ) {

		if ( ! is_array( $atts ) ) {
			$atts = array();
		}

		$atts = shortcode_atts(
			array(
				'id'        => '', // The signed url config
				'el'        => 'img',
				'alt'       => '',
				'html_id'   => '',
				'className' => '',
				'property'  => 'og:image',
			),
			$atts,
			self::$shortcode
		);

		// Abort if we don't have an id.
		if ( empty( $atts['id'] ) ) {
			return;
		}

		return $this->render_image( $atts, $this->get_signed_url( $atts ) );
	}

	/**
	 * Fetches the signed URL to display.
	 *
	 * @param array $atts
	 * @return string
	 */
	public function get_signed_url( $atts ) {

		// Fetch the signed URL config.
		$config = get_post( $atts['id'] );

		// Check the post type is 'bannerbear_url'.
		if ( empty( $config ) || 'bannerbear_url' !== $config->post_type ) {
			return '';
		}

		// Prepare the config from the post meta.
		$config = get_post_meta( $config->ID, 'bannerbear', true );

		// Check the config is valid.
		if ( empty( $config ) || ! is_array( $config ) || empty( $config['api_key'] ) || empty( $config['base_url'] ) ) {
			return '';
		}

		// Prepare args.
		$modifications = wp_json_encode( $this->prepare_modifications( $config ) );
		$query         = '?modifications=' . rtrim( strtr( base64_encode( $modifications ), '+/', '-_' ), '=' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$base_url      = $config['base_url'];
		$api_key       = $config['api_key'];
		$signature     = hash_hmac( 'sha256', $base_url . $query, $api_key );

		// Return the signed URL.
		return $base_url . $query . '&s=' . $signature;
	}

	/**
	 * Replaces post vars with their values.
	 *
	 * @param array $config
	 * @return array
	 */
	public function prepare_modifications( $config ) {

		$new           = array();
		$modifications = isset( $config['modifications'] ) ? $config['modifications'] : array();
		$custom_fields = isset( $config['custom_fields'] ) ? $config['custom_fields'] : array();

		foreach ( $modifications as $name => $vars ) {

			$modification = array(
				'name' => $name,
			);

			foreach ( $vars as $var => $value ) {

				// Abort if no value.
				if ( '' === $value ) {
					continue;
				}

				// Maybe set the custom field key.
				if ( in_array( $value, array( '{post_meta}', '{user_meta}', '{author_meta}' ), true ) ) {

					if ( empty( $custom_fields[ $name ][ $var ] ) ) {
						continue;
					}

					$value = str_replace( '}', ' meta_key="' . $custom_fields[ $name ][ $var ] . '"}', $value );
				}

				// Replace post vars.
				$modification[ $var ] = apply_filters( 'bannerbear_dynamic_tags_text', $value );
			}

			if ( 1 < count( $modification ) ) {
				$new[] = $modification;
			}
		}

		return $new;
	}

	/**
	 * Displays a dynamic image.
	 *
	 * @param array $args The args with which to display dynamic image.
	 * @param string $url The url to the image.
	 *
	 * @return string
	 */
	protected function render_image( $args, $url ) {

		// Increment count.
		$this->count++;

		// Prepare atts.
		$atts = array(
			'id'    => empty( $args['html_id'] ) ? 'bannerbear__img-' . absint( $this->count ) : $args['html_id'],
			'class' => 'bannerbear__img',
			'style' => 'max-width: 100%;',
		);

		// For image, we need to add the src attribute.
		if ( 'img' === $args['el'] ) {
			$atts['src'] = $url;
			$atts['alt'] = empty( $args['alt'] ) ? get_the_title( $args['id'] ) : $args['alt'];
		}

		// Add custom classes.
		if ( ! empty( $atts['className'] ) ) {
			$atts['class'] .= ' ' . $atts['className'];
		}

		// For meta, we need to add the property attribute.
		if ( 'meta' === $args['el'] ) {
			unset( $atts['style'] );
			unset( $atts['class'] );
			$atts['property'] = $args['property'];
			$atts['content']  = $url;
		}

		// Display the image or meta tag.
		?>
			<<?php echo esc_attr( $args['el'] ); ?>
				<?php foreach ( $atts as $key => $value ) : ?>
					<?php echo esc_attr( $key ); ?>="<?php echo esc_attr( $value ); ?>"
				<?php endforeach; ?>
			/>
		<?php
	}

	/**
	 * Optinally display images before / after content.
	 *
	 * @param string $content The content.
	 */
	public function the_content( $content ) {
		global $post;

		// Maybe abort early.
		if ( empty( $post ) || is_admin() || ! is_singular() || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		$urls = bannerbear()->get_urls();

		foreach ( $urls as $url ) {
			$config    = get_post_meta( $url->ID, 'bannerbear', true );
			$post_type = $post->post_type;

			if ( ! empty( $config['post_types'][ $post_type ]['before_content'] ) ) {
				$content = $this->shortcode( array( 'id' => $url->ID ) ) . $content;
			}

			if ( ! empty( $config['post_types'][ $post_type ]['after_content'] ) ) {
				$content .= $this->shortcode( array( 'id' => $url->ID ) );
			}
		}

		return $content;
	}

	/**
	 * Display an Open Graph image.
	 */
	public function display_og_image() {

		$post = get_post();

		// Maybe abort early.
		if ( empty( $post ) || is_admin() || ! is_singular() ) {
			return;
		}

		$urls = bannerbear()->get_urls();

		foreach ( $urls as $url ) {
			$config    = get_post_meta( $url->ID, 'bannerbear', true );
			$post_type = $post->post_type;

			if ( ! empty( $config['post_types'][ $post_type ]['og_image'] ) ) {
				$this->display(
					array(
						'id'       => $url->ID,
						'el'       => 'meta',
						'property' => 'og:image',
					)
				);
				break;
			}
		}
	}
}
