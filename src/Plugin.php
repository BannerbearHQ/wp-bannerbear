<?php

namespace Bannerbear\WP;

/**
 * Contains the main plugin class.
 *
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin Class.
 *
 */
class Plugin {

	/**
	 * @var Output_Manager
	 */
	public $output_manager;

	/**
	 * @var Dynamic_Tags
	 */
	public $dynamic_tags;

	/**
	 * @var API_Controller
	 */
	public $api_controller;

	/**
	 * @var Admin
	 */
	public $admin;

	/**
	 * @var Plugin
	 */
	private static $instance = null;

	/**
	 * Get active instance
	 *
	 * @access public
	 * @since  1.0.0
	 * @return Plugin The main plugin instance.
	 */
	public static function instance() {

		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor.
	 */
	public function __construct() {

		// Load files.
		$this->load_files();

		// Init class properties.
		$this->output_manager = new Output_Manager();
		$this->dynamic_tags   = new Dynamic_Tags();
		$this->api_controller = new API_Controller();

		if ( is_admin() ) {
			$this->admin = new Admin();
		}

		add_action( 'plugins_loaded', array( $this, 'add_hooks' ), 5 );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function load_files() {
		$includes = plugin_dir_path( __FILE__ );

		// Functions.
		require_once $includes . 'functions.php';

	}

	/**
	 * Register relevant hooks.
	 */
	public function add_hooks() {

		/**
		 * Fires before the plugin inits.
		 *
		 * @param Plugin $plugin The plugin instance.
		 */
		do_action( 'before_init_bannerbear', $this );

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_block_type' ) );
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_gutenberg_assets' ) );

		// Init modules.
		$this->output_manager->add_hooks();
		$this->dynamic_tags->add_hooks();
		$this->api_controller->add_hooks();

		if ( is_admin() ) {
			$this->admin->add_hooks();
		}

		/**
		 * Fires after the plugin inits.
		 *
		 * @param Plugin $plugin
		 * @since 1.0.0
		 */
		do_action( 'init_bannerbear_manager', $this );

	}

	/**
	 * Load gutenberg files
	 *
	 */
	public function enqueue_gutenberg_assets() {

		$config = include plugin_dir_path( BANNERBEAR_PLUGIN_FILE ) . '/assets/js/block.asset.php';
		wp_enqueue_script(
			'bannerbear-block',
			plugins_url( 'assets/js/block.js', BANNERBEAR_PLUGIN_FILE ),
			$config['dependencies'],
			$config['version'],
			true
		);

		$data    = array();
		$default = 0;

		foreach ( $this->get_urls() as $url ) {
			$data[] = array(
				'label' => $url->post_title,
				'value' => $url->ID,
			);

			if ( ! $default ) {
				$default = $url->ID;
			}
		}

		wp_localize_script(
			'bannerbear-block',
			'bannerbear',
			array(
				'urls'    => $data,
				'default' => $default,
			)
		);
	}

	/**
	 * Register our dynamic image block type.
	 */
	public function register_block_type() {

		// Bail if register_block_type does not exist (available since WP 5.0)
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		/**
		 * Fires before the bannerbear block type is registered.
		 *
		 * @param Plugin $plugin
		 * @since 1.0.0
		 */
		do_action( 'before_register_bannerbear_block_type', $this );

		register_block_type( 'bannerbear/image' );

		/**
		 * Fires after the bannerbear block type is registered.
		 *
		 * @param Plugin $plugin
		 * @since 1.0.0
		 */
		do_action( 'register_bannerbear_block_type', $this );

	}

	/**
	 * Register our custom post type.
	 */
	public function register_post_type() {

		if ( ! is_blog_installed() || post_type_exists( 'bannerbear_url' ) ) {
			return;
		}

		/**
		 * Fires before the bannerbear post type is registered.
		 *
		 * @param Plugin $plugin
		 * @since 1.0.0
		 */
		do_action( 'before_register_bannerbear_url_post_type', $this );

		// Register post type.
		register_post_type(
			'bannerbear_url',
			apply_filters(
				'bannerbear_url_post_type_details',
				array(
					'labels'              => array(
						'name'               => _x( 'Signed URLs', 'Post type general name', 'bannerbear' ),
						'singular_name'      => _x( 'Signed URL', 'Post type singular name', 'bannerbear' ),
						'menu_name'          => _x( 'Signed URLs', 'Admin Menu text', 'bannerbear' ),
						'name_admin_bar'     => _x( 'Signed URL', 'Add New on Toolbar', 'bannerbear' ),
						'add_new'            => __( 'Add New', 'bannerbear' ),
						'add_new_item'       => __( 'Add New Signed URL', 'bannerbear' ),
						'new_item'           => __( 'New Signed URL', 'bannerbear' ),
						'edit_item'          => __( 'Edit Signed URL', 'bannerbear' ),
						'view_item'          => __( 'View Signed URL', 'bannerbear' ),
						'search_items'       => __( 'Search URLS', 'bannerbear' ),
						'parent_item_colon'  => __( 'Parent URL:', 'bannerbear' ),
						'not_found'          => __( 'No urls found.', 'bannerbear' ),
						'not_found_in_trash' => __( 'No urls found in Trash.', 'bannerbear' ),
					),
					'label'               => __( 'Signed URLs', 'bannerbear' ),
					'description'         => '',
					'public'              => false,
					'show_ui'             => true,
					'map_meta_cap'        => true,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'hierarchical'        => false,
					'query_var'           => false,
					'supports'            => array( 'title' ),
					'has_archive'         => false,
					'show_in_nav_menus'   => false,
					'show_in_rest'        => false,
					'show_in_menu'        => false,
					'can_export'          => false,
				)
			)
		);

		/**
		 * Fires after the bannerbear post type is registered.
		 *
		 * @param Plugin $plugin
		 * @since 1.0.0
		 */
		do_action( 'register_bannerbear_post_type', $this );

	}

	/**
	 * Register our signed URL widget.
	 */
	public function register_widget() {

		/**
		 * Fires before the bannerbear image widget is registered.
		 *
		 * @param Plugin $plugin
		 * @since 1.0.0
		 */
		do_action( 'before_register_bannerbear_widget', $this );

		// Displays a bannerbear image.
		register_widget( __NAMESPACE__ . '\\Bannerbear_Widget' );

		/**
		 * Fires after the bannerbear image widget is registered.
		 *
		 * @param Plugin $plugin
		 * @since 1.0.0
		 */
		do_action( 'register_bannerbear_widget', $this );

	}

	/**
	 * Displays a signed URL image.
	 *
	 * @param int $url_id An ID of a signed URL config.
	 * @param bool $echo Whether to display the image or return its HTML.
	 * @see Output_Manager::shortcode()
	 * @see bannerbear_image()
	 * @return string
	 */
	public function image( $url_id, $echo = true ) {

		$args = array( 'id' => $url_id );

		if ( ! $echo ) {
			return $this->output_manager->shortcode( $args );
		}

		$this->output_manager->display( $args );
	}

	/**
	 * Returns a signed URL.
	 *
	 * @param int $url_id An ID of a signed URL config.
	 * @see Output_Manager::get_signed_url()
	 * @see bannerbear_url()
	 * @return string
	 */
	public function url( $url_id ) {

		$args = array( 'id' => $url_id );

		return $this->output_manager->get_signed_url( $args );
	}

	/**
	 * Return all tags
	 *
	 * @return array
	 */
	public function get_tags() {
		return $this->dynamic_tags->all();
	}

	/**
	 * Fetches an array of all registered signed URL configs.
	 *
	 * @return \WP_Post[]
	 */
	public function get_urls() {
		static $urls;

		if ( ! isset( $urls ) ) {
			$urls = get_posts(
				array(
					'post_type'      => 'bannerbear_url',
					'posts_per_page' => -1,
					'post_status'    => 'publish',
				)
			);
		}

		return $urls;
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', BANNERBEAR_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( BANNERBEAR_PLUGIN_FILE ) );
	}

}
