<?php
if (!defined('WPINC')) {
    die;
}

class Shared_Tour{

    protected $plugin_name;
    protected $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        // Register the shortcode with the correct callback method.
        add_shortcode('cbtt_shared_tour_form', array($this, 'render_shortcode_shared_tours'));
        add_action('init', array($this, 'register_shared_tours_post_type'));
        add_action('add_meta_boxes', array($this, 'shared_tours_meta_box'));
        add_action('save_post_shared_tours', array($this, 'save_shared_tours_meta'));

    }

    public function render_shortcode_shared_tours($atts) {
        global $post;
        $post_title = get_the_title();
        $post_id = get_the_ID();

        ob_start();
        ?>
        <div id="cbtt-shared-tours" data-post-title="<?php echo esc_attr($post_title); ?>" data-initial-route="/shared-tours" data-post-id="<?php echo esc_attr($post_id); ?>"></div>
        <?php
        return ob_get_clean();
    }

    function register_shared_tours_post_type() {
        $labels = array(
            'name'               => __('Shared Tours'),
            'singular_name'      => __('Shared Tour'),
            'menu_name'          => __('Shared Tours'),
            'add_new'            => __('Add New'),
            'add_new_item'       => __('Add New Shared Tour'),
            'edit_item'          => __('Edit Shared Tour'),
            'new_item'           => __('New Shared Tour'),
            'view_item'          => __('View Shared Tour'),
            'search_items'       => __('Search Shared Tours'),
            'not_found'          => __('No tours found'),
            'not_found_in_trash' => __('No tours found in Trash'),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'has_archive'         => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'shared-tours', 'with_front' => false), // Add 'with_front' => false
            'capability_type'     => 'post',
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
            'menu_icon'           => 'dashicons-palmtree',
        );

        register_post_type('shared_tours', $args);
    }

    // Add meta box for Shared Tour details
    function shared_tours_meta_box() {
        add_meta_box(
            'shared_tours_details',
            __('Tour Details'),
            [ $this, 'render_shared_tours_meta_box' ],
            'shared_tours',
            'normal',
            'high'
        );
    }

    // Render the meta box
    function render_shared_tours_meta_box($post) {
        wp_nonce_field('shared_tours_save_meta', 'shared_tours_nonce');

        $fields = array(
            'tour_id'        => __('Tour ID', 'textdomain'),
            'tour_price'     => __('Price', 'textdomain'),
            'tour_discount'  => __('Discount', 'textdomain'),
            'date_from'      => __('Date From', 'textdomain'),
            'date_to'        => __('Date To', 'textdomain'),
            'no_pax'         => __('No. of Pax', 'textdomain'),
            'tour_description' => __('Description', 'textdomain'),
            'itinerary'      => __('Itinerary', 'textdomain'),
            'expectation'    => __('Expectation', 'textdomain'),
            'add_ons'        => __('Add-ons', 'textdomain'),
        );

        foreach ($fields as $key => $label) {
            $value = get_post_meta($post->ID, $key, true);
            echo '<div style="margin-bottom: 10px;">';
            echo '<label for="' . esc_attr($key) . '">' . esc_html($label) . '</label><br>';
            if ($key === 'itinerary' || $key === 'expectation') {
                wp_editor($value, $key, array('textarea_rows' => 5));
            } else {
                echo '<input type="text" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" style="width: 100%;">';
            }
            echo '</div>';
        }
    }

    // Save meta box data
    function save_shared_tours_meta($post_id) {
        if (!isset($_POST['shared_tours_nonce']) || !wp_verify_nonce($_POST['shared_tours_nonce'], 'shared_tours_save_meta')) {
            return;
        }

        $fields = array(
            'tour_id',
            'tour_price',
            'tour_discount',
            'date_from',
            'date_to',
            'no_pax',
            'tour_description',
            'itinerary',
            'expectation',
            'add_ons',
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
}

