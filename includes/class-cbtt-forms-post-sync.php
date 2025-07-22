<?php

    class CBTT_Forms_Post_Sync {

        public function __construct() {

            // Hook into post publishing
            // add_action('save_post', array($this, 'sync_tour_post'), 10, 3);
            // add_action('save_post', [$this, 'sync_tour_post'], 99, 3);
            add_action( 'wp_after_insert_post', [ $this, 'sync_tour_post' ], 10, 4 );

            add_action( 'wp_trash_post', [ $this, 'delete_tour_from_table' ], 10, 1 );
            add_action( 'before_delete_post', [ $this, 'delete_tour_from_table' ], 10, 1 );
        }


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

        public function sync_tour_post4($post_id, $post, $update) {
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

        public function sync_tour_post5($post_id, $post, $update) {
            // Skip during autosave, revision, or AJAX
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

            // Get full category terms
            $categories = get_the_terms($post_id, 'category');
            if (empty($categories) || is_wp_error($categories)) {
                error_log("No categories found for post $post_id");
                return;
            }

            error_log("Full category data for post $post_id: " . print_r($categories, true));

            // Look for a category that matches "tour(s)"
            $tour_category = null;
            foreach ($categories as $category) {
                $slug  = strtolower($category->slug);
                $name  = strtolower($category->name);
                $id    = $category->term_id;

                if (in_array($slug, array('tours', 'tour'))) {
                    $tour_category = $category;
                    break;
                }

                if (in_array($name, array('tours', 'tour'))) {
                    $tour_category = $category;
                    break;
                }

                if ($id == 1) { // adjust category ID as needed
                    $tour_category = $category;
                    break;
                }
            }

            if (!$tour_category) {
                error_log("No qualifying tour category found for post $post_id. Categories: " .
                    print_r(array_map(function($c) { return $c->slug; }, $categories), true));
                return;
            }

            // Get price and discount
            $price = (float) get_post_meta($post_id, 'price', true) ?: 0.00;
            $discount = (float) get_post_meta($post_id, 'discount', true) ?: 0.00;

            $tour_data = array(
                'post_id'    => $post_id,
                'post_type'  => $post->post_type,
                'category'   => $tour_category->slug,
                'name'       => $post->post_title,
                'price'      => $price,
                'discount'   => $discount,
            );

            // Upsert operation: update if exists, otherwise insert
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $tours_table WHERE post_id = %d", $post_id
            ));

            if ($existing) {
                $result = $wpdb->update(
                    $tours_table,
                    $tour_data,
                    array('id' => $existing)
                );
            } else {
                $result = $wpdb->insert(
                    $tours_table,
                    $tour_data
                );
            }

            if (false === $result) {
                error_log("DB Error for post $post_id: " . $wpdb->last_error);
            } else {
                error_log("Successfully synced tour data for post $post_id");
            }
        }

        public function sync_tour_post6($post_id, $post, $update) {
            // Skip during autosave, revision, or AJAX
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

            // Get full category terms
            $categories = get_the_terms($post_id, 'category');
            if (empty($categories) || is_wp_error($categories)) {
                error_log("No categories found for post $post_id");
                return;
            }

            error_log("Full category data for post $post_id: " . print_r($categories, true));

            // Look for a category that matches "tour(s)"
            $tour_category = null;
            foreach ($categories as $category) {
                $slug  = strtolower($category->slug);
                $name  = strtolower($category->name);

                if (in_array($slug, ['tour', 'tours'])) {
                    $tour_category = $category;
                    break;
                }

                if (in_array($name, ['tour', 'tours'])) {
                    $tour_category = $category;
                    break;
                }
            }

            // If no qualifying category, exit early
            if (!$tour_category) {
                error_log("No qualifying tour category found for post $post_id. Categories: " .
                    print_r(array_map(function($c) { return $c->slug; }, $categories), true));
                return;
            }

            // Get price and discount
            $price = (float) get_post_meta($post_id, 'price', true) ?: 0.00;
            $discount = (float) get_post_meta($post_id, 'discount', true) ?: 0.00;

            $tour_data = [
                'post_id'    => $post_id,
                'post_type'  => $post->post_type,
                'category'   => $tour_category->slug,
                'name'       => $post->post_title,
                'price'      => $price,
                'discount'   => $discount,
            ];

            // Upsert operation: update if exists, otherwise insert
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $tours_table WHERE post_id = %d", $post_id
            ));

            if ($existing) {
                $result = $wpdb->update(
                    $tours_table,
                    $tour_data,
                    ['id' => $existing]
                );
            } else {
                $result = $wpdb->insert(
                    $tours_table,
                    $tour_data
                );
            }

            if (false === $result) {
                error_log("DB Error for post $post_id: " . $wpdb->last_error);
            } else {
                error_log("Successfully synced tour data for post $post_id");
            }
        }

        public function sync_tour_post( $post_id, $post, $update, $postarr ) {
            // Skip if not published
            if ( $post->post_status !== 'publish' ) {
                error_log("Post $post_id is not published (status: {$post->post_status})");
                return;
            }

            global $wpdb;
            $tours_table = $wpdb->prefix . 'tours';

            // Get categories
            $categories = get_the_terms( $post_id, 'category' );
            if ( empty( $categories ) || is_wp_error( $categories ) ) {
                error_log("No categories found for post $post_id");
                return;
            }

            error_log("Full category data for post $post_id: " . print_r( $categories, true ) );

            // Find a tour category
            $tour_category = null;
            foreach ( $categories as $category ) {
                $slug = strtolower( $category->slug );
                $name = strtolower( $category->name );

                if ( in_array( $slug, [ 'tour', 'tours' ] ) || in_array( $name, [ 'tour', 'tours' ] ) ) {
                    $tour_category = $category;
                    break;
                }
            }

            if ( ! $tour_category ) {
                error_log("No qualifying tour category found for post $post_id. Categories: " .
                    print_r( array_map( fn( $c ) => $c->slug, $categories ), true ) );
                return;
            }

            // Get price & discount
            $price = (float) get_post_meta( $post_id, 'price', true ) ?: 0.00;
            $discount = (float) get_post_meta( $post_id, 'discount', true ) ?: 0.00;

            $tour_data = [
                'post_id'   => $post_id,
                'post_type' => $post->post_type,
                'category'  => $tour_category->slug,
                'name'      => $post->post_title,
                'price'     => $price,
                'discount'  => $discount,
            ];

            // Upsert
            $existing = $wpdb->get_var( $wpdb->prepare(
                "SELECT id FROM $tours_table WHERE post_id = %d", $post_id
            ) );

            if ( $existing ) {
                $result = $wpdb->update( $tours_table, $tour_data, [ 'id' => $existing ] );
            } else {
                $result = $wpdb->insert( $tours_table, $tour_data );
            }

            if ( false === $result ) {
                error_log("DB Error for post $post_id: " . $wpdb->last_error);
            } else {
                error_log("Successfully synced tour data for post $post_id");
            }
        }


        public function delete_tour_from_table( $post_id ) {
            // Make sure it's a post and published before
            $post = get_post( $post_id );

            if ( ! $post || $post->post_type !== 'post' ) {
                return;
            }

            global $wpdb;
            $tours_table = $wpdb->prefix . 'tours';

            // Check if the post has a tours category
            $categories = get_the_terms( $post_id, 'category' );

            if ( empty( $categories ) || is_wp_error( $categories ) ) {
                error_log( "Post $post_id trashed — no categories found." );
                return;
            }

            $is_tour = false;

            foreach ( $categories as $category ) {
                $slug = strtolower( $category->slug );
                $name = strtolower( $category->name );

                if ( in_array( $slug, [ 'tour', 'tours' ] ) || in_array( $name, [ 'tour', 'tours' ] ) ) {
                    $is_tour = true;
                    break;
                }
            }

            if ( $is_tour ) {
                $deleted = $wpdb->delete(
                    $tours_table,
                    [ 'post_id' => $post_id ],
                    [ '%d' ]
                );

                if ( false === $deleted ) {
                    error_log( "Failed to delete tour data for post $post_id: {$wpdb->last_error}" );
                } else {
                    error_log( "Tour data deleted for post $post_id" );
                }
            } else {
                error_log( "Post $post_id trashed — not a tour, no action taken." );
            }
        }




    }

?>