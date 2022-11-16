<?php
/**
 * Shows how to embed the dynamic image.
 *
 * @var WP_Post $post
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<p>
	<label>
		<strong><?php esc_html_e( 'Shortcode', 'bannerbear' ); ?></strong>
		<input type="text" class="code" value="[bannerbear id=<?php echo (int) $post->ID; ?>]" readonly="readonly" onclick="this.select()" style="width: 100%;">
        <p class="description"><?php esc_html_e( 'Use this shortcode to display the image inside your post, page, or text widget content.', 'bannerbear' ); ?></p>
	</label>
</p>

<p>
	<label>
		<strong><?php esc_html_e( 'PHP Snippet', 'bannerbear' ); ?></strong>
		<input type="text" class="code" value="<?php echo esc_attr( sprintf( '<img src="<?php echo esc_url( bannerbear_url( %d ) ); ?>" />', (int) $post->ID ) ); ?>" readonly="readonly" onclick="this.select()" style="width: 100%;">
        <p class="description"><?php esc_html_e( 'Use this PHP snippet to display the image inside your theme template files.', 'bannerbear' ); ?></p>
	</label>
</p>

<p>
    <?php esc_html_e( 'Note: You can also use the Bannerbear widget to display a dynamic image in you site header, footer, sidebar and other widget areas.', 'bannerbear' ); ?>
</p>
