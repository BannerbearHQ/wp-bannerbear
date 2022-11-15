<?php

namespace Bannerbear\WP;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class Dynamic_Tags
 *
 * @access private
 * @ignore
 */
class Dynamic_Tags {

	/**
	 * @var string The escape function for replacement values.
	 */
	protected $escape_function = null;

	/**
	 * @var array Array of registered dynamic content tags
	 */
	public $tags = array();

	/**
	 * Register core hooks.
	 */
	public function add_hooks() {
		add_filter( 'bannerbear_dynamic_tags_text', array( $this, 'replace_in_text_field' ) );
		add_filter( 'bannerbear_dynamic_tags_url', array( $this, 'replace_in_url' ) );
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Register template tags
	 */
	public function register() {

		// Global tags can go here
		$this->tags['date'] = array(
			// translators: %s is the current date.
			'description' => sprintf( __( 'The current date. Example: %s.', 'bannerbear' ), date_i18n( get_option( 'date_format' ) ) ),
			'replacement' => date_i18n( get_option( 'date_format' ) ),
		);

		$this->tags['time'] = array(
			// translators: %s is the current time.
			'description' => sprintf( __( 'The current time. Example: %s.', 'bannerbear' ), date_i18n( get_option( 'time_format' ) ) ),
			'replacement' => date_i18n( get_option( 'time_format' ) ),
		);

		$this->tags['language'] = array(
			// translators: %s is the current language.
			'description' => sprintf( __( 'The current language. Example: %s.', 'bannerbear' ), get_locale() ),
			'callback'    => 'get_locale',
			'no_args'     => true,
		);

		// The current user.
		$this->tags['user_id'] = array(
			'description' => __( "The current user's ID.", 'bannerbear' ),
			'callback'    => array( $this, 'get_user_property' ),
			'example'     => 'user_id default="0"',
		);

		$this->tags['display_name'] = array(
			'description' => __( "The current user's display name.", 'bannerbear' ),
			'callback'    => array( $this, 'get_user_property' ),
			'example'     => 'display_name default="Guest"',
		);

		$this->tags['user_email'] = array(
			'description' => __( "The current user's email.", 'bannerbear' ),
			'callback'    => array( $this, 'get_user_property' ),
		);

		$this->tags['user_url'] = array(
			'description' => __( "The current user's URL.", 'bannerbear' ),
			'callback'    => array( $this, 'get_user_property' ),
			'example'     => 'user_url default="' . home_url() . '"',
		);

		$this->tags['user_bio'] = array(
			'description' => __( "The current user's bio.", 'bannerbear' ),
			'callback'    => array( $this, 'get_user_property' ),
		);

		$this->tags['user_avatar'] = array(
			'description' => __( "The current user's avatar URL.", 'bannerbear' ),
			'callback'    => array( $this, 'get_user_property' ),
			'example'     => 'user_avatar size="96"',
		);

		$this->tags['user_meta'] = array(
			'description' => __( "Current user's custom field", 'bannerbear' ),
			'callback'    => array( $this, 'get_post_property' ),
			'example'     => "user_meta key='my_meta_key'",
		);

		// Post information.
		$this->tags['post_id'] = array(
			'description' => __( "The current post's ID.", 'bannerbear' ),
			'callback'    => array( $this, 'get_post_property' ),
		);

		$this->tags['post_title'] = array(
			'description' => __( 'The current post title.', 'bannerbear' ),
			'callback'    => array( $this, 'get_post_property' ),
		);

		$this->tags['post_url'] = array(
			'description' => __( "The current post's URL.", 'bannerbear' ),
			'callback'    => array( $this, 'get_post_property' ),
		);

		$this->tags['featured_image'] = array(
			'description' => __( "The current post's featured image.", 'bannerbear' ),
			'callback'    => array( $this, 'get_post_property' ),
		);

		$this->tags['post_excerpt'] = array(
			'description' => __( "The current post's excerpt.", 'bannerbear' ),
			'callback'    => array( $this, 'get_post_property' ),
		);

		$this->tags['post_date'] = array(
			'description' => __( "The current post's date.", 'bannerbear' ),
			'callback'    => array( $this, 'get_post_property' ),
		);

		$this->tags['post_modified'] = array(
			'description' => __( "The current post's modified date.", 'bannerbear' ),
			'callback'    => array( $this, 'get_post_property' ),
		);

		$this->tags['post_meta'] = array(
			'description' => __( "Current post's custom field", 'bannerbear' ),
			'callback'    => array( $this, 'get_post_property' ),
			'example'     => "post_meta key='my_meta_key'",
		);

		// Loop through all taxonomies and add them to the tags.
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
		foreach ( $taxonomies as $taxonomy ) {
			$labels = get_taxonomy_labels( $taxonomy );

			$this->tags[ 'taxonomy_' . $taxonomy->name ] = array(
				'description' => sprintf(
					// translators: %s is the taxonomy name.
					__( 'The current %s.', 'bannerbear' ),
					strtolower( $labels->singular_name ) . ' (' . $taxonomy->name . ')'
				),
				'callback'    => array( $this, 'get_post_property' ),
			);
		}

		// Post author.
		$this->tags['post_author_id'] = array(
			'description' => __( 'The current post author ID.', 'bannerbear' ),
			'callback'    => array( $this, 'get_post_property' ),
		);

		$this->tags['post_author_name'] = array(
			'description' => __( 'The current post author name.', 'bannerbear' ),
			'callback'    => array( $this, 'get_post_property' ),
		);

		$this->tags['post_author_email'] = array(
			'description' => __( 'The current post author email.', 'bannerbear' ),
			'callback'    => array( $this, 'get_post_property' ),
		);

		$this->tags['post_author_url'] = array(
			'description' => __( 'The current post author URL.', 'bannerbear' ),
			'callback'    => array( $this, 'get_post_property' ),
		);

		$this->tags['post_author_bio'] = array(
			'description' => __( 'The current post author bio.', 'bannerbear' ),
			'callback'    => array( $this, 'get_post_property' ),
		);

		$this->tags['post_author_avatar'] = array(
			'description' => __( 'The current post author avatar.', 'bannerbear' ),
			'callback'    => array( $this, 'get_post_property' ),
			'example'     => 'post_author_avatar size="96"',
		);

		$this->tags['author_meta'] = array(
			'description' => __( "Current author's custom field", 'bannerbear' ),
			'callback'    => array( $this, 'get_post_property' ),
			'example'     => "author_meta key='my_meta_key'",
		);
	}

	/**
	 * Registers a new tag
	 */
	public function add_tag( $tag, $details ) {
		$this->tags[ $tag ] = $details;
	}

	/**
	 * Removes a tag
	 */
	public function remove_tag( $tag ) {

		if ( isset( $this->tags[ $tag ] ) ) {
			unset( $this->tags[ $tag ] );
		}
	}

	/**
	 * @return array
	 */
	public function all() {
		return $this->tags;
	}

	/**
	 * @param array $matches
	 *
	 * @return string
	 */
	protected function replace_tag( $matches ) {
		$tags = $this->all();
		$tag  = $matches[1];

		// Abort if tag is not supported.
		if ( ! isset( $tags[ $tag ] ) ) {
			return $matches[0];
		}

		// Generate replacement.
		$config      = $tags[ $tag ];
		$replacement = '';

		// Parse attributes.
		$attributes = array();
		if ( isset( $matches[2] ) ) {
			$attribute_string = $matches[2];
			$attributes       = shortcode_parse_atts( $attribute_string );
		}

		if ( isset( $config['replacement'] ) ) {
			$replacement = $config['replacement'];
		} elseif ( isset( $config['callback'] ) ) {

			// call function
			if ( empty( $config['no_args'] ) ) {
				$replacement = call_user_func( $config['callback'], $attributes, $tag );
			} else {
				$replacement = call_user_func( $config['callback'] );
			}
		}

		if ( ( '' === $replacement || null === $replacement ) && isset( $attributes['default'] ) ) {
			$replacement = trim( $attributes['default'] );
		}

		if ( is_callable( $this->escape_function ) ) {
			$replacement = call_user_func( $this->escape_function, $replacement );
		}

		return $replacement;
	}

	/**
	 * @param string $string The string containing dynamic content tags.
	 * @param string $escape_function Escape mode for the replacement value. Leave empty for no escaping.
	 * @return string
	 */
	protected function replace( $string, $escape_function = '' ) {
		$this->escape_function = $escape_function;

		// Replace strings like this: {tagname attr="value"}.
		$string = preg_replace_callback( '/\{(\w+)(\ +(?:(?!\{)[^}\n])+)*\}/', array( $this, 'replace_tag' ), $string );

		// Call again to take care of nested variables.
		$string = preg_replace_callback( '/\{(\w+)(\ +(?:(?!\{)[^}\n])+)*\}/', array( $this, 'replace_tag' ), $string );
		return $string;
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public function replace_in_content( $string ) {
		return $this->replace( $string, 'wp_kses_post' );
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public function replace_in_html( $string ) {
		return $this->replace( $string, 'esc_html' );
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public function replace_in_attributes( $string ) {
		return $this->replace( $string, 'esc_attr' );
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public function replace_in_url( $string ) {
		return $this->replace( $string, 'urlencode' );
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public function replace_in_text_field( $string ) {
		return $this->replace( $string, 'sanitize_text_field' );
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public function replace_in_email( $string ) {
		return $this->replace( $string, 'sanitize_email' );
	}

	/**
	 * Retrieves a user property
	 *
	 * @param array $args
	 * @param string $property
	 * @return string
	 */
	protected function get_user_property( $args = array(), $property = '' ) {
		$default = isset( $args['default'] ) ? esc_html( $args['default'] ) : '';
		$user    = wp_get_current_user();

		if ( ! $user->exists() ) {
			return $default;
		}

		switch ( $property ) {
			case 'user_id':
				return (int) $user->ID;
			case 'user_email':
				return sanitize_email( $user->user_email );
			case 'display_name':
				return sanitize_text_field( $user->display_name );
			case 'user_url':
				$url = $user->user_url;
				return empty( $url ) ? $default : esc_url( $url );
			case 'user_bio':
				return wp_strip_all_tags( $user->description );
			case 'user_avatar':
				$size = isset( $args['size'] ) ? (int) $args['size'] : 96;
				return esc_url( get_avatar_url( $user->ID, array( 'size' => $size ) ) );
			case 'user_meta':
				$meta_key = isset( $args['meta_key'] ) ? sanitize_text_field( $args['meta_key'] ) : '';
				$meta     = get_user_meta( $user->ID, $meta_key, true );
				return empty( $meta_key ) || empty( $meta ) ? $default : esc_html( $meta );
			default:
				return $default;
		}

	}

	/**
	 * Retrieves a post property
	 *
	 * @param array $args
	 * @param string $property
	 * @return string
	 */
	protected function get_post_property( $args = array(), $property = '' ) {
		$default = isset( $args['default'] ) ? esc_html( $args['default'] ) : '';
		$post    = get_post();

		if ( empty( $post ) ) {
			return $default;
		}

		if ( 0 === strpos( $property, 'taxonomy_' ) ) {
			$taxonomy = substr( $property, 9 );
			$terms    = get_the_terms( $post, $taxonomy );
			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				return $default;
			}

			$term = array_shift( $terms );
			return esc_html( $term->name );
		}

		switch ( $property ) {
			case 'post_id':
				return (int) $post->ID;
			case 'post_title':
				return wp_strip_all_tags( $post->post_title );
			case 'post_url':
				return esc_url( get_permalink( $post->ID ) );
			case 'post_excerpt':
				return wp_strip_all_tags( $post->post_excerpt );
			case 'featured_image':
				$featured_image = get_the_post_thumbnail_url( $post->ID, 'full' );
				return empty( $featured_image ) ? $default : esc_url( $featured_image );
			case 'post_date':
				return date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) );
			case 'post_modified':
				return date_i18n( get_option( 'date_format' ), strtotime( $post->post_modified ) );
			case 'post_author_id':
				return (int) $post->post_author;
			case 'post_author_name':
				return sanitize_text_field( get_the_author_meta( 'display_name', $post->post_author ) );
			case 'post_author_email':
				return sanitize_email( get_the_author_meta( 'user_email', $post->post_author ) );
			case 'post_author_url':
				$url = get_the_author_meta( 'user_url', $post->post_author );
				return empty( $url ) ? $default : esc_url( $url );
			case 'post_author_bio':
				return wp_strip_all_tags( get_the_author_meta( 'description', $post->post_author ) );
			case 'post_author_avatar':
				$size = isset( $args['size'] ) ? (int) $args['size'] : 96;
				return esc_url( get_avatar_url( $post->post_author, array( 'size' => $size ) ) );
			case 'author_meta':
				$meta_key = isset( $args['author_meta'] ) ? sanitize_text_field( $args['author_meta'] ) : '';
				$meta     = get_user_meta( $post->post_author, $meta_key, true );
				return empty( $meta_key ) || empty( $meta ) ? $default : esc_html( $meta );
			case 'post_meta':
				$meta_key = isset( $args['meta_key'] ) ? sanitize_text_field( $args['meta_key'] ) : '';
				$meta     = get_post_meta( $post->ID, $meta_key, true );
				return empty( $meta_key ) || empty( $meta ) ? $default : esc_html( $meta );
			default:
				return $default;
		}

	}

}
