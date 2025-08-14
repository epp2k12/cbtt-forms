<?php
if (!defined('WPINC')) {
    die;
}

class Tour_Form_Shortcode {

    protected $plugin_name;
    protected $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_shortcode('cbtt_tour_form', array($this, 'render_contact_form'));
        add_shortcode('cbtt_tour_fast_form', array($this, 'render_shortcode_fast_contact_form'));
        add_shortcode('cbtt_tour_simple_form', array($this, 'render_shortcode_simple_contact_form'));
    }

    public function render_contact_form($atts) {

        global $post;
        $post_title = get_the_title();
        $post_id = get_the_ID();

        ob_start();
        ?>
        <div id="vue-contact-form" data-post-title="<?php echo esc_attr($post_title); ?>" data-initial-route="/" data-post-id="<?php echo esc_attr($post_id); ?>"></div>
        <?php
        return ob_get_clean();
    }

    public function render_shortcode_fast_contact_form($atts) {

        global $post;
        $post_title = get_the_title();
        $post_id = get_the_ID();

        // var_dump($post_title, $post_id); // Debugging line to check values
        // die();

        ob_start();
        ?>
        <div id="vue-fast-contact-form" data-post-title="<?php echo esc_attr($post_title); ?>" data-initial-route="/fast-contact-form" data-post-id="<?php echo esc_attr($post_id); ?>"></div>
        <?php
        return ob_get_clean();
    }


    public function render_shortcode_simple_contact_form($atts) {

        global $post;
        $post_title = get_the_title();
        $post_id = get_the_ID();

        ob_start();
        ?>
        <div id="cbtt-simple-contact-form" data-post-title="<?php echo esc_attr($post_title); ?>" data-initial-route="/simple-contact-form" data-post-id="<?php echo esc_attr($post_id); ?>"></div>
        <?php
        return ob_get_clean();
    }



    

}

