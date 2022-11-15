<?php
/**
 * Add a checkbox for featured image, OG, etc.
 *
 * @var WP_Post $post
 * @var array $metabox
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$bannerbear = get_post_meta( $post->ID, 'bannerbear', true );
$_post_type = $metabox['args']['post_type'];
$features   = isset( $bannerbear['post_types'][ $_post_type ] ) ? $bannerbear['post_types'][ $_post_type ] : array();
?>

<?php if ( post_type_supports( $_post_type, 'editor' ) ) : ?>

	<!-- Display before content -->
	<p>
		<label>
			<input
				type="checkbox"
				name="bannerbear[post_types][<?php echo esc_attr( $_post_type ); ?>][before_content]"
				value="1"
				<?php checked( ! empty( $features['before_content'] ) ); ?>
			/>
			<?php esc_html_e( 'Display before content', 'bannerbear' ); ?>
		</label>
	</p>

	<!-- Display after content -->
	<p>
		<label>
			<input
				type="checkbox"
				name="bannerbear[post_types][<?php echo esc_attr( $_post_type ); ?>][after_content]"
				value="1"
				<?php checked( ! empty( $features['after_content'] ) ); ?>
			/>
			<?php esc_html_e( 'Display after content', 'bannerbear' ); ?>
		</label>
	</p>

<?php endif; ?>

<!-- Use as OG image -->
<p>
	<label>
		<input
			type="checkbox"
			name="bannerbear[post_types][<?php echo esc_attr( $_post_type ); ?>][og_image]"
			value="1"
			<?php checked( ! empty( $features['og_image'] ) ); ?>
		/>
		<?php esc_html_e( 'Use as open graph image', 'bannerbear' ); ?>
	</label>
</p>
