<?php
if (!defined('WPINC')) {
    die;
}

class Cbtt_Custom_Fields {



    public function __construct() {

        // error_log("inside the Cbtt_custom_fields!");

        add_action('init', array($this, 'enable_custom_fields_in_post'));
        add_action('add_meta_boxes', array($this, 'custom_post_meta_box'));
        add_action('admin_init', array($this, 'custom_post_fields_admin_init'));
        add_action('save_post', array($this, 'save_custom_post_fields'));
        add_shortcode('custom_fields', array($this, 'custom_fields_shortcode'));
        add_action('init', array($this, 'register_tours_custom_fields'));

    }

		// Enable custom fields in post dashboard
		function enable_custom_fields_in_post() {
			add_post_type_support('post', 'custom-fields');
		}    

		// Add custom meta box only for posts in the 'Tours' category
		function custom_post_meta_box() {
			global $post;
			// Check if the post has the 'Tours' category
			if (isset($post) && has_term('tours', 'category', $post)) {
				add_meta_box(
					'cbtt_custom_post_fields',
					'Custom Post Fields (Tours)',
					[ $this, 'render_custom_post_fields' ],
					'post',
					'normal',
					'high'
				);
			}
		}


		// Re-check category on save to ensure meta box fields are saved only for 'Tours' category
		function custom_post_fields_admin_init() {
			global $post;
			// Add meta box dynamically when editing a post, in case category changes
			if (isset($post) && has_term('tours', 'category', $post)) {
				add_meta_box(
					'cbtt_custom_post_fields',
					'Custom Post Fields (Tours)',
					[ $this, 'render_custom_post_fields' ],
					'post',
					'normal',
					'high'
				);
			}
		}


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
				<label for="custom_price">Base Price (₱):</label><br>
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
				<label for="custom_camera">Camera Price (₱):</label><br>
				<input type="number" step="0.01" id="custom_camera" name="custom_camera" value="<?php echo esc_attr($camera); ?>" />
				<span class="description">Enter the camera rental price (e.g., 50.00).</span>
			</p>
			<p>
				<label for="custom_scuba_diving">Scuba Diving Price (₱):</label><br>
				<input type="number" step="0.01" id="custom_scuba_diving" name="custom_scuba_diving" value="<?php echo esc_attr($scuba_diving); ?>" />
				<span class="description">Enter the scuba diving price (e.g., 200.00).</span>
			</p>
			<p>
				<label for="custom_accommodation">Accommodation Price (₱):</label><br>
				<input type="number" step="0.01" id="custom_accommodation" name="custom_accommodation" value="<?php echo esc_attr($accommodation); ?>" />
				<span class="description">Enter the accommodation price (e.g., 300.00).</span>
			</p>
			<p>
				<label for="custom_accommodation_type">Accommodation Type :</label><br>
				<input type="text" id="custom_accommodation_type" name="custom_accommodation_type" value="<?php echo esc_attr($accommodation_type); ?>" />
				<span class="description">Enter the prefered type of accommodation (e.g. 5 star hotel).</span>
			</p>
			<p>
				<label for="custom_lunch">Lunch Price (₱):</label><br>
				<input type="number" step="0.01" id="custom_lunch" name="custom_lunch" value="<?php echo esc_attr($lunch); ?>" />
				<span class="description">Enter the lunch price (e.g., 30.00).</span>
			</p>
			<p>
				<label for="custom_dot_tour_guide">DOT Tour Guide Price (₱):</label><br>
				<input type="number" step="0.01" id="custom_dot_tour_guide" name="custom_dot_tour_guide" value="<?php echo esc_attr($dot_tour_guide); ?>" />
				<span class="description">Enter the DOT tour guide price (e.g., 150.00).</span>
			</p>
			<p>
				<label for="custom_other1">Other 1 Price (₱):</label><br>
				<input type="number" step="0.01" id="custom_other1" name="custom_other1" value="<?php echo esc_attr($other1); ?>" />
				<span class="description">Enter additional price 1 (e.g., 50.00).</span>
			</p>
			<p>
				<label for="custom_other2">Other 2 Price (₱):</label><br>
				<input type="number" step="0.01" id="custom_other2" name="custom_other2" value="<?php echo esc_attr($other2); ?>" />
				<span class="description">Enter additional price 2 (e.g., 50.00).</span>
			</p>
			<p>
				<label for="custom_other3">Other 3 Price (₱):</label><br>
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

}

