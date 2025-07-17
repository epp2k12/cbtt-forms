<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://webappdevz.com
 * @since      1.0.0
 *
 * @package    Cbtt_Forms
 * @subpackage Cbtt_Forms/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cbtt_Forms
 * @subpackage Cbtt_Forms/public
 * @author     Erwin Presbitero <epp2k12@gmail.com>
 */
class Cbtt_Forms_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cbtt_Forms_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cbtt_Forms_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cbtt-forms-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cbtt_Forms_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cbtt_Forms_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$dist_path = plugin_dir_path(__FILE__) . 'dist/assets/';
		$dist_url  = plugins_url('dist/assets/', __FILE__);

		// error_log( print_r( $dist_path, true ) );
    	// error_log( print_r( $dist_url, true ) );

		// var_dump($dist_path);
		// die();

		// Find the hashed CSS file
		$css_files = glob($dist_path . 'index-*.css');
		if ($css_files) {
			$css_file = basename($css_files[0]);
			wp_enqueue_style(
				'cbtt-forms-public-vue-styles',
				$dist_url . $css_file,
				array(),
				null
			);
		}

		wp_enqueue_script( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'js/cbtt-forms-public.js', 
			array( 'jquery' ), 
			$this->version, false 
		);
		
		wp_enqueue_script(
        	'vue-public-bundle',
      		plugin_dir_url( __FILE__ ) . 'dist/bundle.js',
        	[],
        	null,
        	true
    	);

		wp_localize_script(
			'vue-public-bundle',
			'cbttApp',
			[
				'siteUrl' => get_site_url(),
				'restUrl' => get_rest_url(),
				'nonce'   => wp_create_nonce('wp_rest'),
			]
		);	


		// wp_enqueue_style( $this->plugin_name . '_vue_styles', plugin_dir_url( __FILE__ ) . 'dist/assets/index-DHyL3zkc.css', array(), $this->version, 'all' );

	}

	public function create_tour_form_shortcode() {

		ob_start();
		?>
			<h1>THIS IS JUST A TEST SHORTCODE</h1>

		<?php
		return ob_get_clean();
	}

	public function create_custom_test_endpoint() {

		// var_dump("test only");
		// die();

		register_rest_route('cbtt/v1', '/get-posts', array(
        'methods' => 'GET',
        'callback' => array($this, 'cbtt_handle_test_endpoint'),
        'permission_callback' => '__return_true', // Adjust for security!
    	));

	}

	
	public function cbtt_handle_test_endpoint( $request ) {
		// Handle the request and return a response
		$posts = get_posts(array(
			'post_type' => 'post',
			'numberposts' => 5,
		));

		if (empty($posts)) {
			return new WP_Error('no_posts', 'No posts found', array('status' => 404));
		}

		return rest_ensure_response($posts);
	}

	// Register a custom endpoint for form submission
	public function create_custom_store_endpoint() {

		register_rest_route('cbtt/v1', '/submit-form', array(
			'methods' => 'POST',
			'callback' => array($this, 'cbtt_handle_form_submission'),
			'permission_callback' => array($this, 'cbtt_forms_permission_check'),
		));

	}
	// Secure permission callback: only allow requests with a valid nonce
	function cbtt_forms_permission_check($request) {
		// Accept nonce from header or request param
		$nonce = $request->get_header('X-WP-Nonce');
		if (!$nonce) {
			$nonce = $request->get_param('_wpnonce');
		}
		return wp_verify_nonce($nonce, 'wp_rest');
	}

	function cbtt_handle_form_submission($request) {
		global $wpdb;

		$params = $request->get_json_params();

		// Sanitize input
		$name      = sanitize_text_field($params['name'] ?? '');
		$email     = sanitize_email($params['email'] ?? '');
		$phone     = sanitize_text_field($params['phone'] ?? '');
		$tour_date = sanitize_text_field($params['tour_date'] ?? '');
		$message   = sanitize_textarea_field($params['message'] ?? '');

		// Validate required fields
		if (empty($name) || empty($email)) {
			return new WP_Error('missing_fields', 'Name and Email are required.', array('status' => 400));
		}

		$table = $wpdb->prefix . 'cbtt_forms';

		$result = $wpdb->insert(
			$table,
			[
				'name'      => $name,
				'email'     => $email,
				'phone'     => $phone,
				'tour_date' => $tour_date,
				'message'   => $message,
			],
			[ '%s', '%s', '%s', '%s', '%s' ]
		);

		if ($result === false) {
			return new WP_Error('db_error', 'Could not save data.', array('status' => 500));
		}
		
		// Send email
		$to = 'erwin.presbitero@gmail.com'; // Change to your recipient
		$subject = 'New Tour Form Submission';
		// $body = "Title: $params[title]\nName: $name\nEmail: $email\nPhone: $phone\nTour Date: $tour_date\nMessage: $message";
		$body = "New Tour Form Submission\n\n";
		$headers = ['Content-Type: text/plain; charset=UTF-8'];

		$mail_sent = wp_mail($to, $subject, $body, $headers);

		if (!$mail_sent) {
			// Log the error or handle it appropriately
			error_log('Failed to send email for form submission from: ' . $email);
			
			// You can choose to return an error or just continue with a warning
			return rest_ensure_response([
				'success' => true, 
				'message' => 'Form submitted but email failed to send!',
				'email_sent' => false
			]);
		}

		// Optionally store in DB as before...
		return rest_ensure_response(['success' => true, 'message' => 'Form submitted and email sent!']);

	}


	// Looking to send emails in production? Check out our Email API/SMTP product!
	public function mailtrap($phpmailer) {
		$phpmailer->isSMTP();
		$phpmailer->Host = 'sandbox.smtp.mailtrap.io';
		$phpmailer->SMTPAuth = true;
		$phpmailer->Port = 2525;
		$phpmailer->Username = 'b11fb12428949d';
		$phpmailer->Password = '8419e1a3363dba';
	}



}
