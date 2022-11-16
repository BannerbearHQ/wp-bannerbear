<?php

namespace Bannerbear\WP;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Admin Handler.
 *
 * @since 1.0.0
 */
class Admin extends \WP_REST_Controller {

	/**
	 * Add hooks.
	 */
	public function add_hooks() {

		// Add menus.
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 0 );

		// Load styles.
		add_action( 'admin_enqueue_scripts', array( $this, 'load_styles' ) );

		// Load scripts in admin footer.
		add_action( 'admin_footer', array( $this, 'load_scripts' ), 0 );

		// Meta box.
		add_action( 'add_meta_boxes_bannerbear_url', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_edited_form' ), 10, 2 );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );

		// Custom columns.
		add_filter( 'manage_bannerbear_url_posts_columns', array( $this, 'manage_posts_columns' ) );
		add_action( 'manage_bannerbear_url_posts_custom_column', array( $this, 'manage_posts_custom_column' ), 10, 2 );
	}

	/**
	 * Adds the admin menu.
	 */
	public function admin_menu() {

		add_menu_page(
			__( 'Bannerbear', 'bannerbear' ),
			__( 'Bannerbear', 'bannerbear' ),
			'manage_options',
			'edit.php?post_type=bannerbear_url',
			null,
			'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDIwMDEwOTA0Ly9FTiIKICJodHRwOi8vd3d3LnczLm9yZy9UUi8yMDAxL1JFQy1TVkctMjAwMTA5MDQvRFREL3N2ZzEwLmR0ZCI+CjxzdmcgdmVyc2lvbj0iMS4wIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiB3aWR0aD0iNzc0LjAwMDAwMHB0IiBoZWlnaHQ9Ijc3NC4wMDAwMDBwdCIgdmlld0JveD0iMCAwIDc3NC4wMDAwMDAgNzc0LjAwMDAwMCIKIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIG1lZXQiPgoKPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMC4wMDAwMDAsNzc0LjAwMDAwMCkgc2NhbGUoMC4xMDAwMDAsLTAuMTAwMDAwKSIKZmlsbD0iIzAwMDAwMCIgc3Ryb2tlPSJub25lIj4KPHBhdGggZD0iTTM3MzUgNzcyOSBjLTc1NiAtMTA5IC0xNDk1IC0xMDQ1IC0yMDU5IC0yNjA3IC0yNjYgLTczNyAtNDUxIC0xNTQ0Ci00OTcgLTIxNjcgLTY4IC05MjMgMzA2IC0xNzkwIDEwMTYgLTIzNTggNjM3IC01MTEgMTQ0OCAtNzAzIDIyNTYgLTUzNiA1NjcKMTE3IDExMjUgNDUxIDE1MDEgODk5IDM0OCA0MTQgNTUyIDg3OCA2MzAgMTQzMCAxOSAxNDIgMTcgNTc4IC01IDc2MCAtMTE2Cjk1NyAtNDQ5IDIwNTQgLTg5MyAyOTQyIC00MDMgODA2IC04NzEgMTM0NCAtMTM0NyAxNTUwIC0xOTMgODQgLTQwOSAxMTUgLTYwMgo4N3ogbTM0MCAtNDE5IGM0MTggLTEzMiA4NDEgLTYxNiAxMjM1IC0xNDE1IDQzMiAtODc3IDc4NSAtMjA2MCA4NjEgLTI4ODggMTYKLTE3OSA2IC01MzAgLTE5IC02NjcgLTQ1IC0yNDAgLTEwMiAtNDEyIC0yMDcgLTYyNyAtMzQ2IC03MDYgLTEwMTIgLTExODEKLTE3OTUgLTEyNzkgLTEzNSAtMTcgLTM4MSAtMTggLTUxOCAtMyAtNzc1IDg0IC0xNDU2IDU1NyAtMTgxMSAxMjU4IC0xNTcgMzExCi0yMzQgNjEyIC0yNDggOTY2IC0yOCA3NTUgMzU3IDIxOTMgODYyIDMyMjAgNDE3IDg0OCA4NjkgMTM1MiAxMzAwIDE0NTEgODYKMjAgMjUxIDEyIDM0MCAtMTZ6Ii8+CjxwYXRoIGQ9Ik0zNjkwIDU0ODkgYy0xNjIgLTkgLTM1NCAtNDIgLTUwNSAtODUgLTMzNyAtOTcgLTU1OCAtMjU2IC02MjcgLTQ1MgotNjAgLTE2OCA0MyAtNDQzIDI1NyAtNjg3IDIzNiAtMjY5IDUwOCAtNDQ3IDc5MyAtNTE2IGw5MiAtMjIgMCAtNDA3IGMwIC00NTMKLTEgLTQ2MyAtNjYgLTUzOSAtMTE0IC0xMzMgLTMxNiAtMTQwIC00MzUgLTE0IC01MiA1NCAtNzkgMTEzIC04NiAxODggLTExCjEyMSAtNzEgMTg1IC0xNzMgMTg1IC0xMDEgMCAtMTYxIC01OSAtMTY4IC0xNjYgLTYgLTkzIDEzIC0xNzUgNjUgLTI4NCAxNzkKLTM3MyA2NTkgLTQ3NSA5ODAgLTIwOSBsNjMgNTEgNDkgLTQ0IGM2NSAtNTkgMTcyIC0xMTQgMjY3IC0xMzggMzg3IC05OSA3ODEKMjAyIDc4OCA2MDAgMSA2MSAtMyA4MSAtMjEgMTEwIC02NiAxMDYgLTIzMiAxMDYgLTI5MiAwIC0xMiAtMjEgLTI1IC03MCAtMzAKLTEwOCAtMjEgLTE2NyAtMTM3IC0yNzQgLTI5OCAtMjc0IC0xMTIgMSAtMjE5IDY5IC0yNjcgMTcyIC0yMCA0MyAtMjEgNjIgLTI0CjQ2MCBsLTMgNDE1IDEwNiAyNyBjMjQ5IDY0IDQ3NyAxOTkgNjg1IDQwOCAyNjEgMjYwIDQwNCA1NDcgMzY4IDczOSAtMzAgMTY1Ci0xNjAgMzAyIC0zODkgNDEwIC0yMjcgMTA3IC00NjIgMTYxIC03ODMgMTgxIC0xNjUgMTAgLTE2NiAxMCAtMzQ2IC0xeiIvPgo8cGF0aCBkPSJNMTEzNyA2ODk2IGMtMTAyIC0yOCAtMTg0IC0xMjEgLTIwNyAtMjMzIC01NyAtMjc0IDI3OSAtNDY2IDQ4OAotMjc4IDcxIDY0IDk2IDEyMCA5NiAyMjAgMSA5MSAtMTggMTQ1IC03MyAyMDggLTY3IDc1IC0yMDEgMTEyIC0zMDQgODN6Ii8+CjxwYXRoIGQ9Ik02NDY3IDY5MDAgYy0xNDEgLTM2IC0yMjcgLTE0NCAtMjI3IC0yODYgMCAtMTI3IDQ3IC0yMTIgMTUxIC0yNjkKNDggLTI3IDYzIC0zMCAxMzkgLTMwIDk3IDAgMTUyIDIxIDIxNCA4MSA2NCA2MiA4MSAxMDYgODEgMjA5IDAgNzggLTQgOTcgLTI3CjE0MCAtMzMgNjMgLTgwIDEwOCAtMTM5IDEzNCAtNTUgMjQgLTE0MCAzNCAtMTkyIDIxeiIvPgo8L2c+Cjwvc3ZnPgo='
		);

	}

	/**
	 * Loads styles.
	 */
	public function load_styles() {
		$screen = get_current_screen();

		// Load wp-components styles on our pages.
		if ( $screen && false !== strpos( $screen->id, 'bannerbear' ) ) {
			wp_enqueue_style( 'wp-components' );
		}

	}

	/**
	 * Loads scripts.
	 */
	public function load_scripts() {
		$screen = get_current_screen();

		// Create signed URLs.
		if ( $screen && 'edit-bannerbear_url' === $screen->id ) {
			include plugin_dir_path( __FILE__ ) . 'views/create-template.php';

			$config = include plugin_dir_path( BANNERBEAR_PLUGIN_FILE ) . '/assets/js/create-signed-url.asset.php';
			wp_enqueue_script(
				'bannerbear-create-signed-url',
				plugins_url( 'assets/js/create-signed-url.js', BANNERBEAR_PLUGIN_FILE ),
				$config['dependencies'],
				$config['version'],
				true
			);

			wp_localize_script(
				'bannerbear-create-signed-url',
				'bannerbearCreateSignedUrl',
				array(
					'placeholder' => plugin_dir_url( BANNERBEAR_PLUGIN_FILE ) . 'assets/images/placeholder.png',
				)
			);

		}
	}

	/**
	 * Registers the form editing metabox.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {

		add_meta_box(
			'bannerbear_url-map_fields',
			__( 'Modifications', 'bannerbear' ),
			array( $this, 'display_modifications_mb' ),
			null,
			'normal',
			'high'
		);

		add_meta_box(
			'bannerbear_url-embed',
			__( 'Embed', 'bannerbear' ),
			array( $this, 'display_embed_mb' ),
			null,
			'side',
			'low'
		);

		foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) {
			add_meta_box(
				'bannerbear_url-' . $post_type->name,
				$post_type->label,
				array( $this, 'display_post_type_mb' ),
				null,
				'side',
				'default',
				array( 'post_type' => $post_type->name )
			);
		}
	}

	/**
	 * Saves submitted data.
	 *
	 * @param  int    $post_id Post ID.
	 * @param  \WP_Post $post Post object.
	 * @since  1.0.0
	 */
	public function save_edited_form( $post_id, $post ) {

		// Do not save for ajax requests.
		if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
			return;
		}

		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) || did_action( 'bannerbear_save_metabox' ) ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves.
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the nonce.
		if ( empty( $_POST['bannerbear-nonce'] ) || ! wp_verify_nonce( $_POST['bannerbear-nonce'], 'bannerbear' ) ) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events.
		if ( empty( $_POST['bannerbear'] ) || empty( $_POST['post_ID'] ) || absint( $_POST['post_ID'] ) !== $post_id ) {
			return;
		}

		// Check user has permission to edit.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Prepare data being saved.
		$new      = wp_kses_post_deep( wp_unslash( $_POST['bannerbear'] ) );
		$existing = get_post_meta( $post_id, 'bannerbear', true );
		$existing = is_array( $existing ) ? $existing : array();

		$existing['modifications'] = isset( $new['modifications'] ) ? $new['modifications'] : array();
		$existing['post_types']    = isset( $new['post_types'] ) ? $new['post_types'] : array();
		$existing['custom_fields'] = isset( $new['custom_fields'] ) ? $new['custom_fields'] : array();

		// Save data.
		update_post_meta( $post_id, 'bannerbear', $existing );

		do_action( 'bannerbear_save_metabox', $post_id );
	}

	/**
	 * Filter our updated/trashed post messages
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array $messages
	 */
	public function post_updated_messages( $messages ) {

		$messages['bannerbear_url'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'URL updated.', 'bannerbear' ),
			2  => __( 'Custom field updated.', 'bannerbear' ),
			3  => __( 'Custom field deleted.', 'bannerbear' ),
			4  => __( 'URL updated.', 'bannerbear' ),
			5  => __( 'URL restored.', 'bannerbear' ),
			6  => __( 'URL published.', 'bannerbear' ),
			7  => __( 'URL saved.', 'bannerbear' ),
			8  => __( 'URL submitted.', 'bannerbear' ),
			9  => __( 'URL scheduled.', 'bannerbear' ),
			10 => __( 'URL draft updated.', 'bannerbear' ),
		);

		return $messages;
	}

	/**
	 * Registers custom columns.
	 *
	 * @param  array $columns Columns.
	 * @return array
	 */
	public function manage_posts_columns( $columns ) {

		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'title'       => __( 'Title', 'bannerbear' ),
			'template_id' => __( 'Template ID', 'bannerbear' ),
			'url_base'    => __( 'URL Base', 'bannerbear' ),
			'date'        => __( 'Date', 'bannerbear' ),
		);

		return $columns;
	}

	/**
	 * Displays custom columns.
	 *
	 * @param  string $column  Column name.
	 * @param  int    $post_id Post ID.
	 * @return void
	 */
	public function manage_posts_custom_column( $column, $post_id ) {

		$meta = get_post_meta( $post_id, 'bannerbear', true );

		switch ( $column ) {
			case 'template_id':
				echo isset( $meta['template_id'] ) ? esc_html( $meta['template_id'] ) : '&mdash;';
				break;
			case 'url_base':
				echo isset( $meta['base_url'] ) ? esc_html( $meta['base_url'] ) : '&mdash;';
				break;
		}
	}

	/**
	 * Displays the embed metabox.
	 *
	 * @since 1.0.0
	 * @param \WP_Post $post
	 */
	public function display_embed_mb( $post ) {
		include plugin_dir_path( __FILE__ ) . 'views/embed.php';
	}

	/**
	 * Displays the modifications metabox.
	 *
	 * @since 1.0.0
	 * @param \WP_Post $post
	 */
	public function display_modifications_mb( $post ) {
		include plugin_dir_path( __FILE__ ) . 'views/modifications.php';
	}

	/**
	 * Displays the post type metabox.
	 *
	 * @since 1.0.0
	 * @param \WP_Post $post
	 * @param array    $metabox
	 */
	public function display_post_type_mb( $post, $metabox ) {
		include plugin_dir_path( __FILE__ ) . 'views/post-type.php';
	}
}
