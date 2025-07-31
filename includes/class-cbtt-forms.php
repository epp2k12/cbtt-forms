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

		// Add custom meta box only for posts in the 'Tours' category
		function custom_post_meta_box() {
			global $post;
			// Check if the post has the 'Tours' category
			if (isset($post) && has_term('tours', 'category', $post)) {
				add_meta_box(
					'cbtt_custom_post_fields',
					'Custom Post Fields (Tours)',
					'render_custom_post_fields',
					'post',
					'normal',
					'high'
				);
			}
		}
		add_action('add_meta_boxes', 'custom_post_meta_box');

		// Re-check category on save to ensure meta box fields are saved only for 'Tours' category
		function custom_post_fields_admin_init() {
			global $post;
			// Add meta box dynamically when editing a post, in case category changes
			if (isset($post) && has_term('tours', 'category', $post)) {
				add_meta_box(
					'cbtt_custom_post_fields',
					'Custom Post Fields (Tours)',
					'render_custom_post_fields',
					'post',
					'normal',
					'high'
				);
			}
		}
		add_action('admin_init', 'custom_post_fields_admin_init');

		// Render meta box content
		function render_custom_post_fields($post) {
			// Retrieve existing values
			$price = get_post_meta($post->ID, '_custom_price', true);
			$discount = get_post_meta($post->ID, '_custom_discount', true);
			$children = get_post_meta($post->ID, '_custom_children', true);
			$senior = get_post_meta($post->ID, '_custom_senior', true);
			$pwd_discount = get_post_meta($post->ID, '_custom_pwd_discount', true);
			$camera = get_post_meta($post->ID, '_custom_camera', true);
			$scuba_diving = get_post_meta($post->ID, '_custom_scuba_diving', true);
			$accommodation = get_post_meta($post->ID, '_custom_accommodation', true);
			$accommodation_type = get_post_meta($post->ID, '_custom_accommodation_type', true);
			$lunch = get_post_meta($post->ID, '_custom_lunch', true);
			$dot_tour_guide = get_post_meta($post->ID, '_custom_dot_tour_guide', true);
			$other1 = get_post_meta($post->ID, '_custom_other1', true);
			$other2 = get_post_meta($post->ID, '_custom_other2', true);
			$other3 = get_post_meta($post->ID, '_custom_other3', true);

			// Set default values if empty
			if (empty($price)) {
				$price = '5000';
			}
			if (empty($discount)) {
				$discount = '5';
			}
			if (empty($children)) {
				$children = '5';
			}
			if (empty($senior)) {
				$senior = '20';
			}
			if (empty($pwd_discount)) {
				$pwd_discount = '20';
			}
			if (empty($camera)) {
				$camera = '0';
			}
			if (empty($scuba_diving)) {
				$scuba_diving = '0';
			}
			if (empty($accommodation)) {
				$accommodation = '0';
			}
			if (empty($accommodation_type)) {
				$accommodation_type = '0';
			}
			if (empty($lunch)) {
				$lunch = '0';
			}
			if (empty($dot_tour_guide)) {
				$dot_tour_guide = '0';
			}
			if (empty($other1)) {
				$other1 = '0';
			}
			if (empty($other2)) {
				$other2 = '0';
			}
			if (empty($other3)) {
				$other3 = '0';
			}

			// Nonce for security
			wp_nonce_field('custom_post_save', 'custom_post_nonce');
			?>
			<p>
				<label for="custom_price">Base Price ($):</label><br>
				<input type="number" step="0.01" id="custom_price" name="custom_price" value="<?php echo esc_attr($price); ?>" />
				<span class="description">Enter the base price (e.g., 5000.00).</span>
			</p>
			<p>
				<label for="custom_discount">Discount (%):</label><br>
				<input type="number" step="0.01" id="custom_discount" name="custom_discount" value="<?php echo esc_attr($discount); ?>" />
				<span class="description">Enter the discount percentage (e.g., 5.00).</span>
			</p>
			<p>
				<label for="custom_children">Children Discount (%):</label><br>
				<input type="number" step="0.01" id="custom_children" name="custom_children" value="<?php echo esc_attr($children); ?>" />
				<span class="description">Enter the discount percentage (e.g., 5.00).</span>
			</p>
			<p>
				<label for="custom_senior">Senior Discount (%):</label><br>
				<input type="number" step="0.01" id="custom_senior" name="custom_senior" value="<?php echo esc_attr($senior); ?>" />
				<span class="description">Enter the discount percentage (e.g., 20.00).</span>
			</p>
			<p>
				<label for="custom_pwd_discount">PWD Discount (%):</label><br>
				<input type="number" step="0.01" id="custom_pwd_discount" name="custom_pwd_discount" value="<?php echo esc_attr($pwd_discount); ?>" />
				<span class="description">Enter the discount percentage (e.g., 20.00).</span>
			</p>
			<p>
				<label for="custom_camera">Camera Price ($):</label><br>
				<input type="number" step="0.01" id="custom_camera" name="custom_camera" value="<?php echo esc_attr($camera); ?>" />
				<span class="description">Enter the camera rental price (e.g., 50.00).</span>
			</p>
			<p>
				<label for="custom_scuba_diving">Scuba Diving Price ($):</label><br>
				<input type="number" step="0.01" id="custom_scuba_diving" name="custom_scuba_diving" value="<?php echo esc_attr($scuba_diving); ?>" />
				<span class="description">Enter the scuba diving price (e.g., 200.00).</span>
			</p>
			<p>
				<label for="custom_accommodation">Accommodation Price ($):</label><br>
				<input type="number" step="0.01" id="custom_accommodation" name="custom_accommodation" value="<?php echo esc_attr($accommodation); ?>" />
				<span class="description">Enter the accommodation price (e.g., 300.00).</span>
			</p>
			<p>
				<label for="custom_accommodation_type">Accommodation Type Price ($):</label><br>
				<input type="number" step="0.01" id="custom_accommodation_type" name="custom_accommodation_type" value="<?php echo esc_attr($accommodation_type); ?>" />
				<span class="description">Enter the price for accommodation type (e.g., 100.00).</span>
			</p>
			<p>
				<label for="custom_lunch">Lunch Price ($):</label><br>
				<input type="number" step="0.01" id="custom_lunch" name="custom_lunch" value="<?php echo esc_attr($lunch); ?>" />
				<span class="description">Enter the lunch price (e.g., 30.00).</span>
			</p>
			<p>
				<label for="custom_dot_tour_guide">DOT Tour Guide Price ($):</label><br>
				<input type="number" step="0.01" id="custom_dot_tour_guide" name="custom_dot_tour_guide" value="<?php echo esc_attr($dot_tour_guide); ?>" />
				<span class="description">Enter the DOT tour guide price (e.g., 150.00).</span>
			</p>
			<p>
				<label for="custom_other1">Other 1 Price ($):</label><br>
				<input type="number" step="0.01" id="custom_other1" name="custom_other1" value="<?php echo esc_attr($other1); ?>" />
				<span class="description">Enter additional price 1 (e.g., 50.00).</span>
			</p>
			<p>
				<label for="custom_other2">Other 2 Price ($):</label><br>
				<input type="number" step="0.01" id="custom_other2" name="custom_other2" value="<?php echo esc_attr($other2); ?>" />
				<span class="description">Enter additional price 2 (e.g., 50.00).</span>
			</p>
			<p>
				<label for="custom_other3">Other 3 Price ($):</label><br>
				<input type="number" step="0.01" id="custom_other3" name="custom_other3" value="<?php echo esc_attr($other3); ?>" />
				<span class="description">Enter additional price 3 (e.g., 50.00).</span>
			</p>
			<?php
		}

		// Save meta box data
		function save_custom_post_fields($post_id) {
			// Verify nonce
			if (!isset($_POST['custom_post_nonce']) || !wp_verify_nonce($_POST['custom_post_nonce'], 'custom_post_save')) {
				return;
			}
			// Check user permissions
			if (!current_user_can('edit_post', $post_id)) {
				return;
			}
			// Only save if post is in 'Tours' category
			if (has_term('tours', 'category', $post_id)) {
				$meta_fields = [
					'custom_price' => '_custom_price',
					'custom_discount' => '_custom_discount',
					'custom_children' => '_custom_children',
					'custom_senior' => '_custom_senior',
					'custom_pwd_discount' => '_custom_pwd_discount',
					'custom_camera' => '_custom_camera',
					'custom_scuba_diving' => '_custom_scuba_diving',
					'custom_accommodation' => '_custom_accommodation',
					'custom_accommodation_type' => '_custom_accommodation_type',
					'custom_lunch' => '_custom_lunch',
					'custom_dot_tour_guide' => '_custom_dot_tour_guide',
					'custom_other1' => '_custom_other1',
					'custom_other2' => '_custom_other2',
					'custom_other3' => '_custom_other3',
				];
				foreach ($meta_fields as $post_field => $meta_key) {
					if (isset($_POST[$post_field])) {
						update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$post_field]));
					}
				}
			} else {
				// Delete meta if post is no longer in 'Tours' category
				$meta_keys = [
					'_custom_price',
					'_custom_discount',
					'_custom_children',
					'_custom_senior',
					'_custom_pwd_discount',
					'_custom_camera',
					'_custom_scuba_diving',
					'_custom_accommodation',
					'_custom_accommodation_type',
					'_custom_lunch',
					'_custom_dot_tour_guide',
					'_custom_other1',
					'_custom_other2',
					'_custom_other3',
				];
				foreach ($meta_keys as $meta_key) {
					delete_post_meta($post_id, $meta_key);
				}
			}
		}
		add_action('save_post', 'save_custom_post_fields');

		// Optional: Shortcode to display custom fields
		function custom_fields_shortcode($atts) {
			$post_id = get_the_ID();
			if (has_term('tours', 'category', $post_id)) {
				$price = get_post_meta($post_id, '_custom_price', true);
				$discount = get_post_meta($post_id, '_custom_discount', true);
				$children = get_post_meta($post_id, '_custom_children', true);
				$senior = get_post_meta($post_id, '_custom_senior', true);
				$pwd_discount = get_post_meta($post_id, '_custom_pwd_discount', true);
				$camera = get_post_meta($post_id, '_custom_camera', true);
				$scuba_diving = get_post_meta($post_id, '_custom_scuba_diving', true);
				$accommodation = get_post_meta($post_id, '_custom_accommodation', true);
				$accommodation_type = get_post_meta($post_id, '_custom_accommodation_type', true);
				$lunch = get_post_meta($post_id, '_custom_lunch', true);
				$dot_tour_guide = get_post_meta($post_id, '_custom_dot_tour_guide', true);
				$other1 = get_post_meta($post_id, '_custom_other1', true);
				$other2 = get_post_meta($post_id, '_custom_other2', true);
				$other3 = get_post_meta($post_id, '_custom_other3', true);

				$output = '';
				if ($price) {
					$output .= '<p><strong>Base Price:</strong> $' . esc_html($price) . '</p>';
				}
				if ($discount) {
					$output .= '<p><strong>Discount:</strong> ' . esc_html($discount) . '%</p>';
				}
				if ($children) {
					$output .= '<p><strong>Children Discount:</strong> ' . esc_html($children) . '%</p>';
				}
				if ($senior) {
					$output .= '<p><strong>Senior Discount:</strong> ' . esc_html($senior) . '%</p>';
				}
				if ($pwd_discount) {
					$output .= '<p><strong>PWD Discount:</strong> ' . esc_html($pwd_discount) . '%</p>';
				}
				if ($camera) {
					$output .= '<p><strong>Camera Price:</strong> $' . esc_html($camera) . '</p>';
				}
				if ($scuba_diving) {
					$output .= '<p><strong>Scuba Diving Price:</strong> $' . esc_html($scuba_diving) . '</p>';
				}
				if ($accommodation) {
					$output .= '<p><strong>Accommodation Price:</strong> $' . esc_html($accommodation) . '</p>';
				}
				if ($accommodation_type) {
					$output .= '<p><strong>Accommodation Type Price:</strong> $' . esc_html($accommodation_type) . '</p>';
				}
				if ($lunch) {
					$output .= '<p><strong>Lunch Price:</strong> $' . esc_html($lunch) . '</p>';
				}
				if ($dot_tour_guide) {
					$output .= '<p><strong>DOT Tour Guide Price:</strong> $' . esc_html($dot_tour_guide) . '</p>';
				}
				if ($other1) {
					$output .= '<p><strong>Other 1 Price:</strong> $' . esc_html($other1) . '</p>';
				}
				if ($other2) {
					$output .= '<p><strong>Other 2 Price:</strong> $' . esc_html($other2) . '</p>';
				}
				if ($other3) {
					$output .= '<p><strong>Other 3 Price:</strong> $' . esc_html($other3) . '</p>';
				}
				return $output;
			}
			return '';
		}
		add_shortcode('custom_fields', 'custom_fields_shortcode');

		// Register custom fields for the REST API
		function register_tours_custom_fields() {
			$meta_fields = [
				'_custom_price',
				'_custom_discount',
				'_custom_children',
				'_custom_senior',
				'_custom_pwd_discount',
				'_custom_camera',
				'_custom_scuba_diving',
				'_custom_accommodation',
				'_custom_accommodation_type',
				'_custom_lunch',
				'_custom_dot_tour_guide',
				'_custom_other1',
				'_custom_other2',
				'_custom_other3',
			];

			foreach ($meta_fields as $meta_key) {
				register_post_meta(
					'post',
					$meta_key,
					[
						'show_in_rest' => true,
						'single' => true,
						'type' => 'string', // Use string to handle decimal values
						'auth_callback' => function() {
							return current_user_can('edit_posts');
						},
					]
				);
			}
		}
		add_action('init', 'register_tours_custom_fields');

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
		$this->loader->add_action('phpmailer_init', $plugin_public, 'mailhog', 999);

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$plugin_shortcode = new Tour_Form_Shortcode($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action( 'rest_api_init', $plugin_public, 'create_custom_test_endpoint' );
		$this->loader->add_action( 'rest_api_init', $plugin_public, 'create_custom_store_endpoint' );

		// Initialize post sync
        new CBTT_Forms_Post_Sync();

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
