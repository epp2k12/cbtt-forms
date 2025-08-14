<?php
if (!defined('WPINC')) {
    die;
}

class Posts_Shortcode {

    protected $plugin_name;
    protected $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        // Register the shortcode with the correct callback method.
        add_shortcode('posts_by_category', array($this, 'display_posts_by_category_shortcode'));
    }

    /**
     * Shortcode to display all posts from a specific category in a grid layout.
     *
     * Usage: [posts_by_category category="tours"]
     * [posts_by_category category="news"]
     *
     * @param array $atts Shortcode attributes.
     * @return string The HTML output of the posts.
     */
    public function display_posts_by_category_shortcode($atts) {
        // Set default attributes for the shortcode.
        $atts = shortcode_atts(
            array(
                'category'       => '',
                'columns'        => 5, // Default to 5 posts per row
                'posts_per_page' => -1, // Show all posts by default
            ),
            $atts,
            'posts_by_category'
        );

        // Get and sanitize the shortcode attributes.
        $category_slug = sanitize_text_field($atts['category']);
        $columns = intval($atts['columns']);

        if (empty($category_slug)) {
            return '<p>Error: Please specify a category for the shortcode, e.g., [posts_by_category category="news"].</p>';
        }

        $args = array(
            'category_name'  => $category_slug,
            'posts_per_page' => $atts['posts_per_page'], // This tells WordPress to get ALL posts in the category.
            'post_status'    => 'publish',
        );

        $posts = get_posts($args);

        ob_start();

        // Check if there are any posts to display.
        if ($posts) {
            // Include CSS for the grid layout within the shortcode output.
            $column_width = 100 / $columns;
            echo '
            <style>
                .category-posts-container {
                    border: 2px solid #e0e0e0;
                    border-radius: 8px;
                    padding: 15px;
                    margin-bottom: 20px;
                }
                .category-posts-list {
                    display: flex;
                    flex-wrap: wrap;
                    list-style: none;
                    margin: 0;
                    padding: 0;
                    gap: 15px; /* Spacing between grid items */
                    justify-content: flex-start; /* This is the key change to align items to the left */
                }
                .category-posts-list li {
                    flex: 0 0 calc(' . $column_width . '% - 15px); /* Changed flex-grow to 0 to prevent stretching */
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    text-align: center;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    justify-content: space-between; /* To push the button to the bottom */
                    transition: all 0.3s ease; /* Add transition for a smooth hover effect */
                }
                .category-posts-list li:hover {
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add shadow on hover */
                    transform: translateY(-2px); /* A subtle lift effect on hover */
                }
                .post-thumbnail-shortcode {
                    width: 100%;
                    height: auto;
                    border-radius: 5px;
                    margin-bottom: 10px;
                    object-fit: cover;
                }
                .category-posts-list li h3 {
                    margin-top: 0;
                    margin-bottom: 10px;
                    font-size: 1.1rem;
                    line-height: 1.4;
                }
                /* New, more specific rule to remove the underline */
                .category-posts-list li .book-now-button {
                    display: inline-block;
                    margin-top: auto; /* Pushes the button to the bottom */
                    padding: 8px 16px;
                    background-color: #0073aa;
                    color: #fff;
                    text-decoration: none !important; /* The important rule ensures this style is applied */
                    border-radius: 5px;
                    font-weight: bold;
                    transition: background-color 0.3s ease;
                }
                .category-posts-list li .book-now-button:hover {
                    background-color: #005177;
                }

                /* Responsive styles for smaller screens (up to 768px wide) */
                @media (max-width: 768px) {
                    .category-posts-list li {
                        flex: 0 0 calc(50% - 15px); /* Two columns per row with gap, no stretching */
                    }
                }
            </style>';

            echo '<div class="category-posts-container">';
            echo '<h2>' . esc_html(ucwords(str_replace('-', ' ', $category_slug))) . '</h2>';

            echo '<ul class="category-posts-list">';
            foreach ($posts as $post) {
                setup_postdata($post);

                echo '<li>';
                if (has_post_thumbnail($post->ID)) {
                    $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'thumbnail');
                    echo '<img src="' . esc_url($thumbnail_url) . '" alt="' . esc_attr(get_the_title($post->ID)) . '" class="post-thumbnail-shortcode" />';
                }
                
                // Display the post title
                echo '<h3>' . esc_html(get_the_title($post->ID)) . '</h3>';

                // Display the new "Book now!" button
                echo '<a href="' . esc_url(get_permalink($post->ID)) . '" class="book-now-button">Book Now!</a>';
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';

            wp_reset_postdata();
        } else {
            echo '<p>No posts found in the "' . esc_html($category_slug) . '" category.</p>';
        }

        return ob_get_clean();
    }
}
// This needs to be outside the class to instantiate it.
// $posts_shortcode_instance = new Posts_Shortcode('my-plugin-name', '1.0.0');
