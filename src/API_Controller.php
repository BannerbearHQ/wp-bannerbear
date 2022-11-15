<?php

namespace Bannerbear\WP;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Handles API requests.
 *
 * @since 1.0.0
 */
class API_Controller extends \WP_REST_Controller {

	/**
	 * @var string
	 */
	public $namespace = 'bannerbear/v1';

	/**
	 * Add hooks
	 */
	public function add_hooks() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Registers the routes for the objects of the controller.
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/signed-url',
			// Save an API key.
			array(
				'methods'             => 'POST',
				'permission_callback' => array( $this, 'is_admin' ),
				'callback'            => array( $this, 'create_signed_url' ),
				'args'                => array(
					'api_key'       => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'template_id'   => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'template_name' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);
	}

	/**
	 * Checks if the current user is an admin.
	 *
	 * @return true|\WP_Error
	 */
	public function is_admin() {

		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		return new \WP_Error(
			'rest_forbidden',
			__( 'You do not have permission to access this resource.', 'bannerbear' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Creates a signed URL.
	 *
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 */
	public function create_signed_url( $request ) {

		$api_key       = $request->get_param( 'api_key' );
		$template_id   = $request->get_param( 'template_id' );
		$template_name = $request->get_param( 'template_name' );

		// Create a signed URL.
		$result = wp_remote_post(
			'https://api.bannerbear.com/v2/templates/' . $template_id . '/signed_bases',
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $api_key,
				),
			)
		);

		if ( is_wp_error( $result ) ) {
			return new \WP_Error(
				'rest_invalid_param',
				sprintf(
					/* translators: %s: error message */
					__( 'Error creating signed url: %s', 'bannerbear' ),
					$result->get_error_message()
				)
			);
		}

		$result = json_decode( wp_remote_retrieve_body( $result ) );

		if ( isset( $result->error ) ) {
			return new \WP_Error(
				'rest_invalid_param',
				sprintf(
					/* translators: %s: error message */
					__( 'Error creating signed url: %s', 'bannerbear' ),
					$result->error
				)
			);
		}

		if ( isset( $result->message ) ) {
			return new \WP_Error(
				'rest_invalid_param',
				sprintf(
					/* translators: %s: error message */
					__( 'Error creating signed url: %s', 'bannerbear' ),
					$result->message
				)
			);
		}

		if ( empty( $result->base_url ) ) {
			return new \WP_Error(
				'rest_invalid_param',
				__( 'Error creating signed url.', 'bannerbear' )
			);
		}

		$post = wp_insert_post(
			array(
				'post_title'  => empty( $template_name ) ? $template_id : $template_name,
				'post_status' => 'publish',
				'post_type'   => 'bannerbear_url',
				'meta_input'  => array(
					'bannerbear' => array(
						'api_key'       => $api_key,
						'template_id'   => $template_id,
						'uid'           => $result->uid,
						'base_url'      => $result->base_url,
						'modifications' => array(),
						'custom_fields' => array(),
						'post_types'    => array(),
					),
				),
			),
			true
		);

		if ( is_wp_error( $post ) ) {
			return new \WP_Error(
				'rest_invalid_param',
				sprintf(
					/* translators: %s: error message */
					__( 'Error creating signed url: %s', 'bannerbear' ),
					$post->get_error_message()
				)
			);
		}

		return rest_ensure_response(
			array(
				'edit_url' => get_edit_post_link( $post, 'raw' ),
			)
		);
	}

}
