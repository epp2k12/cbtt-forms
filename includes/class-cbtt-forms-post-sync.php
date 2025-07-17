<?php

    class CBTT_Forms_Post_Sync {

        public function __construct() {

            // Hook into post publishing
            add_action('save_post', array($this, 'sync_tour_post'), 10, 3);
        }

        // public function sync_tour_post($post_id, $post, $update) {
        //     // Skip autosaves, revisions, and non-published posts
        //     if (wp_is_post_autosave($post_id) || 
        //         wp_is_post_revision($post_id) || 
        //         $post->post_status !== 'publish') {
        //         return;
        //     }

        //     // Debug: Log the post ID being processed
        //     error_log("Processing post ID: $post_id");

        //     // Check if post has 'tours' category - more robust check
        //     $has_tour_category = false;
        //     $categories = get_the_category($post_id);
            
        //     // Debug: Log all categories
        //     error_log("Post categories: " . print_r($categories, true));
            
        //     foreach ($categories as $category) {
        //         if ($category->slug === 'tours' || $category->name === 'tours') {
        //             $has_tour_category = true;
        //             break;
        //         }
        //     }

        //     if ($has_tour_category) {
        //         global $wpdb;
                
        //         $tours_table = $wpdb->prefix . 'tours';
                
        //         // Get the first category
        //         $main_category = !empty($categories) ? $categories[0]->slug : 'uncategorized';
                
        //         // Check if this post already exists in tours table
        //         $existing_tour = $wpdb->get_row($wpdb->prepare(
        //             "SELECT id FROM $tours_table WHERE post_id = %d", 
        //             $post_id
        //         ));
                
        //         // Debug: Log existing tour status
        //         error_log("Existing tour: " . print_r($existing_tour, true));
                
        //         $tour_data = array(
        //             'post_id' => $post_id,
        //             'post_type' => $post->post_type,
        //             'category' => $main_category,
        //             'name' => $post->post_title,
        //             'price' => 0.00,
        //             'discount' => 0.00
        //         );
                
        //         if ($existing_tour) {
        //             // Update existing tour
        //             $result = $wpdb->update(
        //                 $tours_table,
        //                 $tour_data,
        //                 array('id' => $existing_tour->id)
        //             );
                    
        //             // Debug: Log update result
        //             error_log("Update result: " . ($result !== false ? 'Success' : 'Failed'));
        //         } else {
        //             // Insert new tour
        //             $result = $wpdb->insert($tours_table, $tour_data);
                    
        //             // Debug: Log insert result and last error
        //             error_log("Insert result: " . ($result !== false ? 'Success' : 'Failed'));
        //             error_log("Last error: " . $wpdb->last_error);
        //         }
        //     } else {
        //         error_log("Post does not have 'tours' category");
        //     }
        // }

        public function sync_tour_post1($post_id, $post, $update) {
            // Skip autosaves, revisions, and non-published posts
            if (wp_is_post_autosave($post_id) || 
                wp_is_post_revision($post_id)) {
                return;
            }

            // Use a later hook or check post status differently
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
            if (defined('DOING_AJAX') && DOING_AJAX) return;
            
            // Wait until the post is actually published
            if ($post->post_status !== 'publish') return;

            global $wpdb;
            $tours_table = $wpdb->prefix . 'tours';

            // More robust category checking
            $tour_categories = array('tours', 'tour', 'Tours', 'Tour'); // Common variations
            $has_tour_category = false;
            $main_category = 'uncategorized';

            // Method 1: Check by category ID first
            $tour_cat_id = get_cat_ID('tours');
            if ($tour_cat_id && has_category($tour_cat_id, $post_id)) {
                $has_tour_category = true;
            }
            // Method 2: Check all category variations
            else {
                $categories = get_the_category($post_id);
                foreach ($categories as $category) {
                    if (in_array(strtolower($category->slug), array_map('strtolower', $tour_categories))) {
                        $has_tour_category = true;
                        $main_category = $category->slug;
                        break;
                    }
                }
            }

            if ($has_tour_category) {
                // Get price and discount from custom fields if available
                $price = get_post_meta($post_id, 'price', true) ?: 0.00;
                $discount = get_post_meta($post_id, 'discount', true) ?: 0.00;

                $tour_data = array(
                    'post_id' => $post_id,
                    'post_type' => $post->post_type,
                    'category' => $main_category,
                    'name' => $post->post_title,
                    'price' => floatval($price),
                    'discount' => floatval($discount)
                );

                // Check for existing tour
                $existing_tour = $wpdb->get_row($wpdb->prepare(
                    "SELECT id FROM $tours_table WHERE post_id = %d", 
                    $post_id
                ));

                if ($existing_tour) {
                    $result = $wpdb->update(
                        $tours_table,
                        $tour_data,
                        array('id' => $existing_tour->id)
                    );
                } else {
                    $result = $wpdb->insert($tours_table, $tour_data);
                }

                // Debug output
                error_log("Tour sync result for post $post_id: " . ($result ? 'Success' : 'Failed'));
                if (!$result) {
                    error_log("Database error: " . $wpdb->last_error);
                }
            } else {
                error_log("Post $post_id does not have a tour category. Available categories: " . 
                        print_r(wp_get_post_categories($post_id), true));
            }
        }

        public function sync_tour_post($post_id, $post, $update) {
            // Skip during autosave, revision, or AJAX requests
            if (wp_is_post_autosave($post_id) || 
                wp_is_post_revision($post_id) || 
                (defined('DOING_AJAX') && DOING_AJAX)) {
                return;
            }

            // Only process when the post is published
            if ($post->post_status !== 'publish') {
                error_log("Post $post_id is not published (status: {$post->post_status})");
                return;
            }

            global $wpdb;
            $tours_table = $wpdb->prefix . 'tours';

            // Get ALL category information (not just IDs)
            $categories = wp_get_post_categories($post_id, array('fields' => 'all'));
            error_log("Full category data for post $post_id: " . print_r($categories, true));

            // Check if any category is a "tour" category (multiple ways)
            $tour_category = null;
            foreach ($categories as $category) {
                // Check by slug
                if (in_array(strtolower($category->slug), array('tours', 'tour'))) {
                    $tour_category = $category;
                    break;
                }
                // Check by name
                if (in_array(strtolower($category->name), array('tours', 'tour'))) {
                    $tour_category = $category;
                    break;
                }
                // Check by ID if you know your tour category ID
                if ($category->term_id == 1) { // Change 1 to your actual tour category ID
                    $tour_category = $category;
                    break;
                }
            }

            if ($tour_category) {
                // Get or calculate price and discount
                $price = (float) get_post_meta($post_id, 'price', true) ?: 0.00;
                $discount = (float) get_post_meta($post_id, 'discount', true) ?: 0.00;

                $tour_data = array(
                    'post_id' => $post_id,
                    'post_type' => $post->post_type,
                    'category' => $tour_category->slug,
                    'name' => $post->post_title,
                    'price' => $price,
                    'discount' => $discount
                );

                // Upsert operation (insert or update)
                $existing = $wpdb->get_var($wpdb->prepare(
                    "SELECT id FROM $tours_table WHERE post_id = %d", $post_id
                ));

                if ($existing) {
                    $result = $wpdb->update($tours_table, $tour_data, array('id' => $existing));
                } else {
                    $result = $wpdb->insert($tours_table, $tour_data);
                }

                if (false === $result) {
                    error_log("DB Error for post $post_id: " . $wpdb->last_error);
                } else {
                    error_log("Successfully synced tour data for post $post_id");
                }
            } else {
                error_log("No qualifying tour category found for post $post_id. Categories: " . 
                        print_r(array_map(function($c) { return $c->slug; }, $categories), true));
            }
        }


        public function sync_tour_post3($post_id, $post, $update) {
            // Skip during autosave, revision, or AJAX requests
            if (wp_is_post_autosave($post_id) || 
                wp_is_post_revision($post_id) || 
                (defined('DOING_AJAX') && DOING_AJAX)) {
                return;
            }

            // Wait until post is fully published
            if ($post->post_status !== 'publish') {
                error_log("Skipping post $post_id - status: {$post->post_status}");
                return;
            }

            global $wpdb;
            $tours_table = $wpdb->prefix . 'tours';

            // 1. Get the tour category ID (set this to your actual tour category ID)
            $tour_category_id = 2; // CHANGE THIS to your actual tour category ID
            
            // 2. Verify the post has the tour category
            if (!has_category($tour_category_id, $post_id)) {
                error_log("Post $post_id is not in tour category");
                return;
            }

            // 3. Get the assigned category object
            $tour_category = get_term($tour_category_id, 'category');
            if (is_wp_error($tour_category) || !$tour_category) {
                error_log("Tour category not found for ID: $tour_category_id");
                return;
            }

            // 4. Prepare tour data
            $price = (float) get_post_meta($post_id, 'price', true) ?: 0.00;
            $discount = (float) get_post_meta($post_id, 'discount', true) ?: 0.00;

            $tour_data = array(
                'post_id' => $post_id,
                'post_type' => $post->post_type,
                'category' => $tour_category->slug,
                'name' => $post->post_title,
                'price' => $price,
                'discount' => $discount
            );

            // 5. Upsert operation
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $tours_table WHERE post_id = %d", $post_id
            ));

            if ($existing) {
                $result = $wpdb->update($tours_table, $tour_data, array('id' => $existing));
            } else {
                $result = $wpdb->insert($tours_table, $tour_data);
            }

            // 6. Handle results
            if (false === $result) {
                error_log("DB Error for post $post_id: " . $wpdb->last_error);
            } else {
                error_log("Successfully synced tour data for post $post_id with category '{$tour_category->slug}'");
            }
        }


    }

?>