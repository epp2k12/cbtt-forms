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

        add_shortcode('cbtt_tour_form', array($this, 'render_shortcode'));
    }

    public function render_shortcode($atts) {

        global $post;
        $post_title = get_the_title();

        ob_start();
        ?>
        <div id="vue-contact-form" data-post-title="<?php echo $post_title; ?>"></div>
        <?php
        return ob_get_clean();
    }

}

