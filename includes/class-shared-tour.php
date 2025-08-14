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
        add_shortcode('display_shared_tours', array($this, 'display_shared_tours_shortcode'));

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

    /**
     * Shortcode to display all shared tours
     */
    public function display_shared_tours_shortcode($atts) {

        $columns = 5;
        // Shortcode attributes with defaults
        $atts = shortcode_atts(array(
            'posts_per_page' => -1,  // Show all by default
            'orderby'        => 'date',
            'order'          => 'DESC',
            'layout'         => 'grid', // 'grid' or 'list'
            'columns'        => 5, // Default to 5 posts per row
        ), $atts);

        // Query arguments
        $args = array(
            'post_type'      => 'shared_tours',
            'posts_per_page' => $atts['posts_per_page'],
            'orderby'        => $atts['orderby'],
            'order'          => $atts['order'],
        );

        $tours = new WP_Query($args);

        ob_start();

        if ($tours->have_posts()) {
            $column_width = 100 / $atts['columns'];
            echo '
            <style>
                .shared-tours-container {
                    border: 2px solid #e0e0e0;
                    border-radius: 8px;
                    padding: 15px;
                    margin-bottom: 20px;
                }
                .shared-tours-list {
                    display: flex;
                    flex-wrap: wrap;
                    list-style: none;
                    margin: 0;
                    padding: 0;
                    gap: 15px;
                    justify-content: flex-start;
                }
                .shared-tours-list li {
                    flex: 0 0 calc(' . $column_width . '% - 15px);
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    text-align: center;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    justify-content: space-between;
                    transition: all 0.3s ease;
                }
                .shared-tours-list li:hover {
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    transform: translateY(-2px);
                }
                .tour-thumbnail-shortcode {
                    width: 100%;
                    height: auto;
                    border-radius: 5px;
                    margin-bottom: 10px;
                    object-fit: cover;
                }
                .shared-tours-list li h3 {
                    margin-top: 0;
                    margin-bottom: 10px;
                    font-size: 1.1rem;
                    line-height: 1.4;
                }
                .tour-meta {
                    margin-bottom: 10px;
                    font-size: 0.9rem;
                    color: #666;
                }
                .tour-button {
                    display: inline-block;
                    margin-top: auto;
                    padding: 8px 16px;
                    background-color: #0073aa;
                    color: #fff;
                    text-decoration: none !important;
                    border-radius: 5px;
                    font-weight: bold;
                    transition: background-color 0.3s ease;
                }
                .tour-button:hover {
                    background-color: #005177;
                    color: #fff;
                }
                @media (max-width: 768px) {
                    .shared-tours-list li {
                        flex: 0 0 calc(50% - 15px);
                    }
                }
            </style>';

            echo '<div class="shared-tours-container">';
            echo '<h2>Shared Tours</h2>';
            echo '<ul class="shared-tours-list">';
            
            while ($tours->have_posts()) : $tours->the_post();
                $price = get_post_meta(get_the_ID(), 'tour_price', true);
                $discount = get_post_meta(get_the_ID(), 'tour_discount', true);
                $date_from = get_post_meta(get_the_ID(), 'date_from', true);
                $date_to = get_post_meta(get_the_ID(), 'date_to', true);
                
                echo '<li>';
                if (has_post_thumbnail()) {
                    $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                    echo '<img src="' . esc_url($thumbnail_url) . '" alt="' . esc_attr(get_the_title()) . '" class="tour-thumbnail-shortcode" />';
                }
                
                echo '<h3>' . esc_html(get_the_title()) . '</h3>';
                
                echo '<div class="tour-meta">';
                if ($price) {
                    echo '<p>Price: $' . esc_html($price) . '</p>';
                }
                if ($discount) {
                    echo '<p>Discount: ' . esc_html($discount) . '%</p>';
                }
                if ($date_from && $date_to) {
                    echo '<p>' . esc_html($date_from) . ' - ' . esc_html($date_to) . '</p>';
                }
                echo '</div>';
                
                echo '<a href="' . esc_url(get_permalink()) . '" class="tour-button">Book Now</a>';
                echo '</li>';
            endwhile;
            
            echo '</ul>';
            echo '</div>';
            
            wp_reset_postdata();
        } else {
            echo '<p>No shared tours found.</p>';
        }

        return ob_get_clean();
    }

}

