<?php
/**
 * Helper functions.
 *
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Returns a URL to a dynamic image.
 *
 * @param int $url_id An ID of a signed URL config.
 * @since 1.0.0
 * @return string
 */
function bannerbear_url( $url_id ) {
	return bannerbear()->url( $url_id );
}

/**
 * Displays a signed URL image.
 *
 * @param int $url_id An ID of a signed URL config.
 * @param bool $echo Whether to display the image or return its HTML.
 * @since 1.0.0
 * @return string
 */
function bannerbear_image( $url_id, $echo = true ) {
	return bannerbear()->image( $url_id, $echo );
}

/**
 * Returns the modifications for a given post.
 *
 * @param int $post_id
 * @since 1.0.0
 * @return array|WP_Error
 */
function bannerbear_get_allowed_modifications( $post_id ) {
	$cached = get_transient( 'bannerbear_allowed_modifications_' . $post_id );

	if ( false !== $cached ) {
		return $cached;
	}

	$bannerbear = get_post_meta( $post_id, 'bannerbear', true );

	if ( ! is_array( $bannerbear ) || empty( $bannerbear['template_id'] ) ) {
		return new WP_Error( 'no_bannerbear', __( 'No BannerBear template found for this url.', 'bannerbear' ) );
	}

	$template = wp_remote_get(
		'https://api.bannerbear.com/v2/templates/' . $bannerbear['template_id'],
		array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $bannerbear['api_key'],
			),
		)
	);

	if ( is_wp_error( $template ) ) {
		return $template;
	}

	$template = json_decode( wp_remote_retrieve_body( $template ), true );

	if ( isset( $template['error'] ) ) {
		return new WP_Error( 'bannerbear_error', $template['error'] );
	}

	if ( isset( $template['message'] ) ) {
		return new WP_Error( 'bannerbear_error', $template['message'] );
	}

	$allowed_modifications = isset( $template['available_modifications'] ) ? $template['available_modifications'] : array();

	set_transient( 'bannerbear_allowed_modifications_' . $post_id, $allowed_modifications, HOUR_IN_SECONDS );

	return $allowed_modifications;
}
