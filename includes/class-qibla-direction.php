<?php
class Masjid_Qibla_Direction {

    public function __construct() {
        // Register the shortcode for displaying the Qibla direction
        add_shortcode('qibla_direction', [$this, 'display_qibla_direction']);
        
        // Enqueue the styles and scripts for Qibla direction
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles_and_scripts']);
    }

    // Function to display Qibla direction
    public function display_qibla_direction($atts) {
        // Set default attributes for the shortcode
        $atts = shortcode_atts([
            'city' => 'Dhaka',
            'country' => 'Bangladesh',
        ], $atts);

        // Start the output buffer
        ob_start();

        ?>
        <div class="qibla-direction-container">
            <div class="qibla-direction-header">
                <h3>Qibla Direction</h3>
                <p>For <strong><?php echo esc_html($atts['city'] . ', ' . $atts['country']); ?></strong></p>
            </div>
            <div class="qibla-direction-body">
                <div id="qibla-direction-display">
                    <p id="qibla-direction-text">Calculating Qibla...</p>
                    <div id="qibla-arrow">
                        <div class="qibla-arrow-icon"></div>
                    </div>
                </div>
                <div id="qibla-location">
                    <p>Your location: <span id="user-location"></span></p>
                </div>
            </div>
            <div class="qibla-direction-footer">
                <p>Qibla direction based on your location. Powered by Masjid Management Plugin.</p>
            </div>
        </div>
        <?php

        // Return the content to display
        return ob_get_clean();
    }

    // Function to enqueue the styles and scripts
    public function enqueue_styles_and_scripts() {
        wp_enqueue_style('qibla-direction-styles', plugin_dir_url(__FILE__) . '../assets/css/qibla-direction.css');
        wp_enqueue_script('qibla-direction-script', plugin_dir_url(__FILE__) . '../assets/js/qibla-direction.js', ['jquery'], '', true);

        // Localize script to pass data from PHP to JavaScript
        wp_localize_script('qibla-direction-script', 'qibla_direction_data', [
            'city' => 'Dhaka',
            'country' => 'Bangladesh',
            'api_url' => 'https://api.aladhan.com/v1/qibla'
        ]);
    }
}
