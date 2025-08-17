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
		
		// Enqueue public scripts
		wp_enqueue_script(
			'vue-public-bundle',
			plugin_dir_url( __FILE__ ) . 'dist/bundle.js', // Adjusted path to match Vite's output
			array('wp-i18n', 'wp-data', 'wp-element'), // Add WordPress dependencies
			'1.0.0', // Version for cache-busting
			true // Load in footer
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

	// not used -------------------------------------------------
	public function create_tour_form_shortcode() {

		ob_start();
		?>
			<h1>THIS IS JUST A TEST SHORTCODE</h1>

		<?php
		return ob_get_clean();
	}
	// -----------------------------------------------------------

	
	// Register a custom endpoint for testing --------------------
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
	// -----------------------------------------------------------



	/*	 
	 * Register a custom endpoint for form submission
	 * This endpoint will handle the form submission from ADVANCE contact forms
	 * will email and save the form data to the database
	 */
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
	// Handle form submission for Advance Contact Form
	function cbtt_handle_form_submission($request) {

		global $wpdb;
		$site_name = get_bloginfo('name'); // Get the site name from WordPress settings
		$params = $request->get_json_params();

		// Log the entire $request content
		error_log('REST Request JSON Payload: ' . print_r($params, true));
		// error_log('REST Request All Params: ' . print_r($request->get_params(), true));
		// error_log('REST Request Body: ' . $request->get_body());
		// error_log('REST Request Headers: ' . print_r($request->get_headers(), true));

		// --- Sanitize and Extract Input Data ---
		$package_id         = absint($params['id'] ?? 0); // the post ID

		$name               = sanitize_text_field($params['name'] ?? '');
		$email              = sanitize_email($params['email'] ?? '');
		$phone              = sanitize_text_field($params['contact'] ?? '');
		$tour_date          = sanitize_text_field($params['tour_date'] ?? '');
		$pickup_address     = sanitize_text_field($params['pickup'] ?? ''); // Assuming this is passed
		$special_requests   = sanitize_textarea_field($params['message'] ?? ''); // Assuming this is passed
		$package_name       = sanitize_text_field($params['title'] ?? ''); // e.g., "Whale Shark Snorkeling + Badian Canyoneering in Kawasan Falls"
		$local_guests       = absint($params['local'] ?? 0);
		$foreign_guests     = absint($params['foreign'] ?? 0);
		$local_guest_price  = floatval($params['local_price'] ?? 0); // Price per local guest
		$foreign_guest_price= floatval($params['foreign_price'] ?? 0); // Price per foreign guest
		$subtotal           = floatval($params['sub_total'] ?? 0);
		// $required_downpayment = floatval($params['required_downpayment'] ?? 0);
		$camera 			= floatval($params['camera'] ?? 0);
		$camera_price       = floatval($params['camera_price'] ?? 0);
		$scuba_diving_price = floatval($params['scuba_diving_price'] ?? 0);
		$lunch           	= floatval($params['lunch'] ?? 0);

		// Validate required fields
		if (empty($name) || empty($email) || empty($package_name) || empty($tour_date)) {
			return new WP_Error('missing_fields', 'Name, Email, Tour Date, and Package Name are required.', array('status' => 400));
		}

		// Define meta keys
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

		error_log('POST ID: ' . $package_id);

		// Retrieve post meta data
		$meta_data = [];
		if ($package_id) {
			foreach ($meta_keys as $key) {
				$meta_value = get_post_meta($package_id, $key, true);
				if ($meta_value !== '') { // Only include non-empty meta values
					$meta_data[$key] = $meta_value;
				}
			}
		}


		error_log('Meta Data: ' . print_r($meta_data, true));

		// save to DB ----------------------------------
		$table = $wpdb->prefix . 'cbtt_forms';
		$result = $wpdb->insert(
			$table,
			[
				'name'      => $name,
				'email'     => $email,
				'phone'     => $phone,
				'tour_date' => $tour_date,
				'message'   => $special_requests,
			],
			[ '%s', '%s', '%s', '%s', '%s' ]
		);
		if ($result === false) {
			return new WP_Error('db_error', 'Could not save data.', array('status' => 500));
		}
		// End save to DB ----------------------------------

		// for logo
		$company_logo_url = plugins_url( 'assets/images/sample-logo.jpg', dirname( __FILE__ ) . '/../cbtt-forms.php' );
		// error_log( 'Logo URL: ' . $company_logo_url );

		// Build email body
		$to = $email; // Change to your recipient
		$subject = 'Tour Booking Confirmation - ' . $package_name . ' - ' . $site_name;

		$body = '
			<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
				<div style="text-align: center; margin-bottom: 20px;">
					<img src="' . esc_url($company_logo_url) . '" alt="' . esc_attr($site_name) . ' Logo" style="max-width: 200px; height: auto; display: block; margin: 0 auto;">
				</div>
				<p>Dear ' . esc_html($name) . ',</p>
				<p>Thank you for booking your tour with <strong>' . esc_html($site_name) . '</strong>! We\'re thrilled to confirm your reservation for the <strong>' . esc_html($package_name) . '</strong> tour.</p>
				<p>Here are the details of your booking:</p>
				<ul>
					<li><strong>Tour Package:</strong> ' . esc_html($package_name) . '</li>
					<li><strong>Tour Date:</strong> ' . esc_html($tour_date) . '</li>
					<li><strong>Number of Guests:</strong> ' . esc_html($local_guests) . ' Local Guest(s), ' . esc_html($foreign_guests) . ' Foreign Guest(s)</li>
					<li><strong>Pickup Address:</strong> ' . esc_html($pickup_address) . '</li>
					<li><strong>Special Requests:</strong> ' . (esc_html($special_requests) ? esc_html($special_requests) : 'None') . '</li>
				</ul>
				<p><strong>Package Summary:</strong></p>
				<ul>';
					if ($local_guests > 0) {
						$body .= '<li>Local Guest(s): ' . esc_html($local_guests) . ' x ₱' . number_format($local_guest_price, 2) . '</li>';
					}
					if ($foreign_guests > 0) {
						$body .= '<li>Foreign Guest(s): ' . esc_html($foreign_guests) . ' x ₱' . number_format($foreign_guest_price, 2) . '</li>';
					}
					$body .= '
					<li><strong>SUBTOTAL:</strong> ₱' . number_format($subtotal, 2) . '</li>
					<li><strong>REQUIRED DOWNPAYMENT:</strong> ₱' . number_format($subtotal, 2) . '</li>
				</ul>
				<p>To secure your booking, please proceed with the required downpayment of <strong>₱' . number_format($subtotal, 2) . '</strong>. You can find our payment instructions <a href="[insert link to payment instructions page or details here]" style="color: #0073aa; text-decoration: none;">here</a>.</p>
				<p>Once your downpayment is received, we will send you a final confirmation and more details regarding your itinerary.</p>
				<p>If you have any questions or need further assistance, please do not hesitate to reply to this email or call us at <a href="tel:[Your Contact Number]" style="color: #0073aa; text-decoration: none;">[Your Contact Number]</a>.</p>
				<p>We look forward to providing you with an unforgettable experience!</p>
				<p>Best regards,</p>
				<p>The ' . esc_html($site_name) . ' Team<br>
				<a href="[Your Website]" style="color: #0073aa; text-decoration: none;">[Your Website]</a><br>
				<a href="tel:[Your Contact Number]" style="color: #0073aa; text-decoration: none;">[Your Contact Number]</a></p>
			</div>';

		$headers = [
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . $site_name . ' <support@cbtt.com>'  // Use a proper domain
		];
		$client_mail_sent = wp_mail($to, $subject, $body, $headers);

		if (!$client_mail_sent) {
			// Capture all possible error sources
			global $phpmailer;
				
			// PHPMailer errors
			if (isset($phpmailer->ErrorInfo) && !empty($phpmailer->ErrorInfo)) {
				error_log('PHPMailer ErrorInfo: ' . $phpmailer->ErrorInfo);
			}
				
			// WordPress mail errors
			$wp_error = error_get_last();
			if ($wp_error) {
				error_log('PHP Last Error: ' . print_r($wp_error, true));
			}
			
			// Check if wp_mail function exists and is working
			if (!function_exists('wp_mail')) {
				error_log('wp_mail function does not exist!');
			}
				
			error_log('Failed to send email for form submission from: ' . $email);
			error_log('Email details - To: ' . $to . ', Subject: ' . $subject);
			error_log('Headers: ' . print_r($headers, true));
				
			return rest_ensure_response([
				'success' => true, 
				'message' => 'Form submitted but email failed to send!',
				'email_sent' => false
			]);
		}

		// --- Prepare Support Email ---
		$support_to = 'support@mysite.com'; // Your support email address
		$support_subject = 'New Tour Booking - ' . $name . ' - ' . $package_name;
		$support_body = "New Tour Booking has been received with the following details:\n\n";
		$support_body .= "**Client Information:**\n";
		$support_body .= "* Name: " . $name . "\n";
		$support_body .= "* Email: " . $email . "\n";
		$support_body .= "* Phone: " . $phone . "\n";
		$support_body .= "* Pickup Address: " . $pickup_address . "\n";
		$support_body .= "* Special Requests: " . ($special_requests ? $special_requests : 'None') . "\n\n";
		$support_body .= "**Tour Package Details:**\n";
		$support_body .= "* Package Name: " . $package_name . "\n";
		$support_body .= "* Tour Date: " . $tour_date . "\n";
		$support_body .= "* Local Guests: " . $local_guests . "\n";
		$support_body .= "* Foreign Guests: " . $foreign_guests . "\n\n";
		$support_body .= "**Financials:**\n";
		$support_body .= "* Local Guest Rate: ₱" . number_format($local_guest_price, 2) . "\n";
		$support_body .= "* Foreign Guest Rate: ₱" . number_format($foreign_guest_price, 2) . "\n";
		$support_body .= "* Subtotal: ₱" . number_format($subtotal, 2) . "\n";
		// $support_body .= "* Required Downpayment: ₱" . number_format($required_downpayment, 2) . "\n\n";

		$support_body .= "\n\n";

		// Add Custom Fields to Support Email if available
		if (!empty($meta_data)) {
			$support_body .= "**Package Custom Fields (from Post ID: " . $package_id . "):**\n";
			foreach ($meta_data as $key => $value) {
				$support_body .= "* " . ucwords(str_replace('_', ' ', $key)) . ": " . ($value ? $value : 'N/A') . "\n";
			}
			$support_body .= "\n";
		}

		$support_body .= "Please log this booking and prepare for client follow-up regarding downpayment and final itinerary.\n\n";
		$support_body .= "Thank you,\n\n";
		$support_body .= $site_name . " System\n";

		$support_headers = [
			'Content-Type: text/plain; charset=UTF-8',
			'From: ' . $site_name . ' <no-reply@yourdomain.com>',
		];

		$support_mail_sent = wp_mail($support_to, $support_subject, $support_body, $support_headers);

		if (!$support_mail_sent) {
			error_log('Failed to send support email for form submission from: ' . $email);
			// Decide if you want to return an error here or continue
		}

		// --- Final Response ---
		if ($client_mail_sent && $support_mail_sent) {
			return rest_ensure_response(['success' => true, 'message' => 'Form submitted and emails sent successfully!']);
		} else {
			return rest_ensure_response([
				'success' => true, // Still true for form submission success
				'message' => 'Form submitted, but one or more emails failed to send. Check logs for details.',
				'client_email_sent' => $client_mail_sent,
				'support_email_sent' => $support_mail_sent
			]);
		}
	}
	// End Handle form submission for Advance Contact Form




	/*	 
	 * Register a custom endpoint for form submission
	 * This endpoint will handle the form submission from SIMPLE contact forms
	 * will email and save the form data to the database
	 */
	public function create_simple_form_endpoint() {
		register_rest_route('cbtt/v1', '/submit-simple-form', array(
			'methods' => 'POST',
			'callback' => array($this, 'cbtt_handle_simple_form_submission'),
			'permission_callback' => array($this, 'cbtt_forms_permission_check'),
		));
	}	

	function cbtt_handle_simple_form_submission($request) {

		global $wpdb;
		$site_name = get_bloginfo('name'); // Get the site name from WordPress settings
		$params = $request->get_json_params();

		// Log the entire $request content
		error_log('REST Request JSON Payload: ' . print_r($params, true));
		// error_log('REST Request All Params: ' . print_r($request->get_params(), true));
		// error_log('REST Request Body: ' . $request->get_body());
		// error_log('REST Request Headers: ' . print_r($request->get_headers(), true));

		// --- Sanitize and Extract Input Data ---
		$package_id         = absint($params['id'] ?? 0); // the post ID
		$name               = sanitize_text_field($params['name'] ?? '');
		$email              = sanitize_email($params['email'] ?? '');
		$phone              = sanitize_text_field($params['contact'] ?? '');
		$tour_date          = sanitize_text_field($params['tour_date'] ?? '');
		$pickup_address     = sanitize_text_field($params['pickup'] ?? ''); // Assuming this is passed
		$special_requests   = sanitize_textarea_field($params['message'] ?? ''); // Assuming this is passed
		$package_name       = sanitize_text_field($params['title'] ?? ''); // e.g., "Whale Shark Snorkeling + Badian Canyoneering in Kawasan Falls"
		$local_guests       = absint($params['local'] ?? 0);
		$foreign_guests     = absint($params['foreign'] ?? 0);
		$local_guest_price  = floatval($params['local_price'] ?? 0); // Price per local guest
		$foreign_guest_price= floatval($params['foreign_price'] ?? 0); // Price per foreign guest
		$subtotal           = floatval($params['sub_total'] ?? 0);
		$required_downpayment = floatval($params['required_downpayment'] ?? 0);


		// Validate required fields
		if (empty($name) || empty($email) || empty($package_name) || empty($tour_date)) {
			return new WP_Error('missing_fields', 'Name, Email, Tour Date, and Package Name are required.', array('status' => 400));
		}

		// Define meta keys
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

		error_log('POST ID: ' . $package_id);

		// // Retrieve post meta data
		$meta_data = [];
		if ($package_id) {
			foreach ($meta_keys as $key) {
				$meta_value = get_post_meta($package_id, $key, true);
				if ($meta_value !== '') { // Only include non-empty meta values
					$meta_data[$key] = $meta_value;
				}
			}
		}


		error_log('Meta Data: ' . print_r($meta_data, true));

		// save to DB ----------------------------------
		$table = $wpdb->prefix . 'cbtt_forms';
		$result = $wpdb->insert(
			$table,
			[
				'name'      => $name,
				'email'     => $email,
				'phone'     => $phone,
				'tour_date' => $tour_date,
				'message'   => $special_requests,
			],
			[ '%s', '%s', '%s', '%s', '%s' ]
		);
		if ($result === false) {
			return new WP_Error('db_error', 'Could not save data.', array('status' => 500));
		}
		// End save to DB ----------------------------------

		// for logo
		$company_logo_url = plugins_url( 'assets/images/sample-logo.jpg', dirname( __FILE__ ) . '/../cbtt-forms.php' );
		// error_log( 'Logo URL: ' . $company_logo_url );

		// Build email body
		$to = $email; // Change to your recipient
		$subject = 'Tour Booking Confirmation - ' . $package_name . ' - ' . $site_name;

		$body = '
			<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
				<div style="text-align: center; margin-bottom: 20px;">
					<img src="' . esc_url($company_logo_url) . '" alt="' . esc_attr($site_name) . ' Logo" style="max-width: 200px; height: auto; display: block; margin: 0 auto;">
				</div>
				<p>Dear ' . esc_html($name) . ',</p>

				<p>Thank you for booking your <strong>' . esc_html($package_name) . '</strong> tour with <strong>' . esc_html($site_name) . '</strong>! We\'re excited to confirm your reservation and look forward to providing you with an unforgettable adventure.</p>
				<p>Here are the details of your booking:</p>
				<ul>
					<li><strong>Tour Package:</strong> ' . esc_html($package_name) . '</li>
					<li><strong>Tour Date:</strong> ' . esc_html($tour_date) . '</li>
					<li><strong>Number of Guests:</strong> <strong>' . esc_html($local_guests) . '</strong> Local Guest(s), <strong>' . esc_html($foreign_guests) . '</strong> Foreign Guest(s)</li>
					<li><strong>Pickup Address:</strong> ' . esc_html($pickup_address) . '</li>
					<li><strong>Tour Notes/Requests:</strong> ' . (esc_html($special_requests) ? esc_html($special_requests) : 'None') . '</li>
				</ul>
				<p>We\'ll be sending a separate email with your <strong>official quotation</strong> and other important details/reminders shortly. Please keep an eye on your inbox.</p>
				<p>In the meantime, if you have any questions or need to make changes to your booking, please don\'t hesitate to reply to this email or give us a call at <a href="tel:[Your Contact Number]" style="color: #0073aa; text-decoration: none;">[Your Contact Number]</a>.</p>
				<p>We can\'t wait to see you!</p>
				<p>Best regards,</p>
				<p>The ' . esc_html($site_name) . ' Team<br>
				<a href="[Your Website]" style="color: #0073aa; text-decoration: none;">[Your Website]</a><br>
				<a href="tel:[Your Contact Number]" style="color: #0073aa; text-decoration: none;">[Your Contact Number]</a></p>
			</div>';
		$headers = [
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . $site_name . ' <support@cbtt.com>'  // Use a proper domain
		];
		$client_mail_sent = wp_mail($to, $subject, $body, $headers);

		if (!$client_mail_sent) {
			// Capture all possible error sources
			global $phpmailer;
				
			// PHPMailer errors
			if (isset($phpmailer->ErrorInfo) && !empty($phpmailer->ErrorInfo)) {
				error_log('PHPMailer ErrorInfo: ' . $phpmailer->ErrorInfo);
			}
				
			// WordPress mail errors
			$wp_error = error_get_last();
			if ($wp_error) {
				error_log('PHP Last Error: ' . print_r($wp_error, true));
			}
			
			// Check if wp_mail function exists and is working
			if (!function_exists('wp_mail')) {
				error_log('wp_mail function does not exist!');
			}
				
			error_log('Failed to send email for form submission from: ' . $email);
			error_log('Email details - To: ' . $to . ', Subject: ' . $subject);
			error_log('Headers: ' . print_r($headers, true));
				
			return rest_ensure_response([
				'success' => true, 
				'message' => 'Form submitted but email failed to send!',
				'email_sent' => false
			]);
		}

		// --- Prepare Support Email ---
		$support_to = 'support@mysite.com'; // Your support email address
		$support_subject = 'New Tour Booking - ' . $name . ' - ' . $package_name;
		$support_body = "New Tour Booking has been received with the following details:\n\n";
		$support_body .= "**Client Information:**\n";
		$support_body .= "* Name: " . $name . "\n";
		$support_body .= "* Email: " . $email . "\n";
		$support_body .= "* Phone: " . $phone . "\n";
		$support_body .= "* Pickup Address: " . $pickup_address . "\n";
		$support_body .= "* Special Requests: " . ($special_requests ? $special_requests : 'None') . "\n\n";
		$support_body .= "**Tour Package Details:**\n";
		$support_body .= "* Package Name: " . $package_name . "\n";
		$support_body .= "* Tour Date: " . $tour_date . "\n";
		$support_body .= "* Local Guests: " . $local_guests . "\n";
		$support_body .= "* Foreign Guests: " . $foreign_guests . "\n\n\n";
		$support_body .= "**Financials:**\n";
		$support_body .= "* Local Guest Rate: ₱" . number_format($local_guest_price, 2) . "\n";
		$support_body .= "* Foreign Guest Rate: ₱" . number_format($foreign_guest_price, 2) . "\n";
		$support_body .= "* Subtotal: ₱" . number_format($subtotal, 2) . "\n";
		// $support_body .= "* Required Downpayment: ₱" . number_format($required_downpayment, 2) . "\n\n";

		$support_body .= "\n\n";

		// Add Custom Fields to Support Email if available
		if (!empty($meta_data)) {
			$support_body .= "**Package Custom Fields (from Post ID: " . $package_id . "):**\n";
			foreach ($meta_data as $key => $value) {
				$support_body .= "* " . ucwords(str_replace('_', ' ', $key)) . ": " . ($value ? $value : 'N/A') . "\n";
			}
			$support_body .= "\n";
		}

		$support_body .= "Please log this booking and prepare for client follow-up regarding downpayment and final itinerary.\n\n";
		$support_body .= "Thank you,\n\n";
		$support_body .= $site_name . " System\n";

		$support_headers = [
			'Content-Type: text/plain; charset=UTF-8',
			'From: ' . $site_name . ' <no-reply@yourdomain.com>',
		];

		$support_mail_sent = wp_mail($support_to, $support_subject, $support_body, $support_headers);

		if (!$support_mail_sent) {
			error_log('Failed to send support email for form submission from: ' . $email);
			// Decide if you want to return an error here or continue
		}

		// --- Final Response ---
		if ($client_mail_sent && $support_mail_sent) {
			return rest_ensure_response(['success' => true, 'message' => 'Form submitted and emails sent successfully!']);
		} else {
			return rest_ensure_response([
				'success' => true, // Still true for form submission success
				'message' => 'Form submitted, but one or more emails failed to send. Check logs for details.',
				'client_email_sent' => $client_mail_sent,
				'support_email_sent' => $support_mail_sent
			]);
		}
	}


	// Use Mailtrap for testing
	public function mailtrap($phpmailer) {
		$phpmailer->isSMTP();
		$phpmailer->Host = 'sandbox.smtp.mailtrap.io';
		$phpmailer->SMTPAuth = true;
		$phpmailer->Port = 2525;
		$phpmailer->Username = 'b11fb12428949d';
		$phpmailer->Password = '8419e1a3363dba';
	}
	// ------------------------------------------------------------------------------

	// Use mailhog for local development
	public function mailhog($phpmailer) {
		// error_log('MailHog PHPMailer hook triggered');
		$phpmailer->isSMTP();
		$phpmailer->Host = '127.0.0.1';
		$phpmailer->Port = 1025;
		$phpmailer->SMTPAuth = false;
		$phpmailer->SMTPSecure = '';
		$phpmailer->SMTPAutoTLS = false;
		
		// Add detailed debugging
		// $phpmailer->SMTPDebug = 3; // Enable verbose debug output
		// $phpmailer->Debugoutput = function($str, $level) {
		// 	error_log("SMTP Debug Level $level: " . trim($str));
		// };
		
		// Set timeout values
		$phpmailer->Timeout = 10;
		$phpmailer->SMTPKeepAlive = false;
		// error_log('MailHog configuration complete - attempting connection to ' . $phpmailer->Host . ':' . $phpmailer->Port);
	}
	// ------------------------------------------------------------------------------










	

}
