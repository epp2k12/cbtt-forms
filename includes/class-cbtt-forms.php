<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://webappdevz.com
 * @since      1.0.0
 *
 * @package    Cbtt_Forms
 * @subpackage Cbtt_Forms/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Cbtt_Forms
 * @subpackage Cbtt_Forms/includes
 * @author     Erwin Presbitero <epp2k12@gmail.com>
 */
class Cbtt_Forms {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Cbtt_Forms_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'CBTT_FORMS_VERSION' ) ) {
			$this->version = CBTT_FORMS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'cbtt-forms';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cbtt_Forms_Loader. Orchestrates the hooks of the plugin.
	 * - Cbtt_Forms_i18n. Defines internationalization functionality.
	 * - Cbtt_Forms_Admin. Defines all hooks for the admin area.
	 * - Cbtt_Forms_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbtt-forms-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbtt-forms-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cbtt-forms-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cbtt-forms-public.php';


		// new classes 
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tour-form-shortcode.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbtt-forms-post-sync.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbtt-custom-fields.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-posts-shortcode.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-shared-tour.php';

		$this->loader = new Cbtt_Forms_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cbtt_Forms_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Cbtt_Forms_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Cbtt_Forms_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );// Enable custom fields in post dashboard
		
		// Enable custom fields in post dashboard
		function enable_custom_fields_in_post() {
			add_post_type_support('post', 'custom-fields');
		}
		add_action('init', 'enable_custom_fields_in_post');

		new Cbtt_Custom_Fields();


	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Cbtt_Forms_Public( $this->get_plugin_name(), $this->get_version() );

		// remove this on production
		// $this->loader->add_action('phpmailer_init', $plugin_public, 'mailtrap');
		// $this->loader->add_action('phpmailer_init', $plugin_public, 'mailhog', 999);

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$plugin_shortcode = new Tour_Form_Shortcode($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action( 'rest_api_init', $plugin_public, 'create_custom_test_endpoint' );
		$this->loader->add_action( 'rest_api_init', $plugin_public, 'create_custom_store_endpoint' );
		$this->loader->add_action( 'rest_api_init', $plugin_public, 'create_simple_form_endpoint' );		

		// Initialize post sync
        new CBTT_Forms_Post_Sync();
		new Posts_Shortcode($this->get_plugin_name(), $this->get_version());
		new Shared_Tour($this->get_plugin_name(), $this->get_version());

		// add_shortcode('tour_form', array($plugin_public, 'create_tour_form_shortcode'));

	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Cbtt_Forms_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
