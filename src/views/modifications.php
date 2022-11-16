<?php
/**
 * Lets the user map custom fields to BannerBear dynamic tags.
 *
 * @var WP_Post $post
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$available_modifications = bannerbear_get_allowed_modifications( $post->ID );

if ( is_wp_error( $available_modifications ) ) {
	?>
	<p><?php echo esc_html( $available_modifications->get_error_message() ); ?></p>
	<?php
	return;
}

$bannerbear            = get_post_meta( $post->ID, 'bannerbear', true );
$current_modifications = empty( $bannerbear['modifications'] ) ? array() : $bannerbear['modifications'];
$custom_fields         = empty( $bannerbear['custom_fields'] ) ? array() : $bannerbear['custom_fields'];
$dynamic_tags          = wp_list_pluck( bannerbear()->get_tags(), 'description' );

wp_nonce_field( 'bannerbear', 'bannerbear-nonce' );
?>

<?php if ( ! empty( $available_modifications ) ) : ?>
	<table class="form-table">
		<tbody>
		<?php foreach ( $available_modifications as $modification ) : ?>

			<?php foreach ( $modification as $key => $value ) : ?>
				<?php if ( 'name' !== $key ) : ?>
					<tr>
						<th scope="row">
							<label for="bannerbear-modification-<?php echo esc_attr( $modification['name'] ); ?>__<?php echo esc_attr( $key ); ?>">
								<?php echo esc_html( $modification['name'] ); ?> (<?php echo esc_html( $key ); ?>)
							</label>
						</th>
						<td>
							<select name="bannerbear[modifications][<?php echo esc_attr( $modification['name'] ); ?>][<?php echo esc_attr( $key ); ?>]" id="bannerbear-modification-<?php echo esc_attr( $modification['name'] ); ?>__<?php echo esc_attr( $key ); ?>" class="regular-text">

								<?php
									$current_value = isset( $current_modifications[ $modification['name'] ][ $key ] ) ? $current_modifications[ $modification['name'] ][ $key ] : '';
									$custom_field  = isset( $custom_fields[ $modification['name'] ][ $key ] ) ? $custom_fields[ $modification['name'] ][ $key ] : '';
								?>

								<option value="" <?php selected( $current_value, '' ); ?>><?php esc_html_e( 'Default', 'bannerbear' ); ?></option>
								<?php foreach ( $dynamic_tags as $dynamic_tag => $description ) : ?>
									<option value="{<?php echo esc_attr( $dynamic_tag ); ?>}" <?php selected( $current_value, '{' . $dynamic_tag . '}' ); ?>>
										<?php echo esc_html( $description ); ?>
									</option>
								<?php endforeach; ?>
							</select>

							<!-- An input box for custom fields -->
							<input
								type="text"
								name="bannerbear[custom_fields][<?php echo esc_attr( $modification['name'] ); ?>][<?php echo esc_attr( $key ); ?>]"
								id="bannerbear-custom-fields-<?php echo esc_attr( $modification['name'] ); ?>__<?php echo esc_attr( $key ); ?>"
								class="regular-text"
								style="display: none;"
								placeholder="<?php esc_attr_e( 'Meta Key', 'bannerbear' ); ?>"
								value="<?php echo isset( $custom_fields[ $modification['name'] ][ $key ] ) ? esc_attr( $custom_fields[ $modification['name'] ][ $key ] ) : ''; ?>" />
						</td>
					</tr>
				<?php endif; ?>
			<?php endforeach; ?>

		<?php endforeach; ?>
		</tbody>
	</table>
<?php else : ?>
	<p><?php esc_html_e( 'No modifications available for this template.', 'bannerbear' ); ?></p>
<?php endif; ?>

<script>
	(function ($) {
		$(document).ready(function () {
			// Show the custom field input box when the user selects the custom field tag.
			$('select[id^="bannerbear-modification-"]').on('change', function () {
				var $this = $(this);
				var $customFieldInput = $this.siblings('input[id^="bannerbear-custom-fields-"]');

				if ('{post_meta}' === $this.val() || '{user_meta}' === $this.val() || '{author_meta}' === $this.val()) {
					$customFieldInput.show();
				} else {
					$customFieldInput.hide();
				}
			});

			$('select[id^="bannerbear-modification-"]').trigger('change');
		});
	})(jQuery);
</script>
