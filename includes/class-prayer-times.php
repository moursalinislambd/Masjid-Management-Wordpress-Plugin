<?php

class Masjid_Prayer_Times {
    private $api_url = 'https://api.aladhan.com/v1/timingsByCity';

    public function __construct() {
        add_shortcode('masjid_prayer_times', [$this, 'display_prayer_times']);
    }

    public function fetch_prayer_times($city, $country, $method, $school) {
        // Define the parameters for the API request
        $params = [
            'city' => $city,
            'country' => $country,
            'method' => $method,
            'school' => $school
        ];

        // Create the full URL
        $url = $this->api_url . '?' . http_build_query($params);

        // Make the API request
        $response = wp_remote_get($url);

        // Check for errors in the response
        if (is_wp_error($response)) {
            return ['error' => 'Unable to fetch prayer times.'];
        }

        // Decode the JSON response
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Check if the response is valid
        if (isset($data['code']) && $data['code'] == 200) {
            return $data['data']['timings'];
        } else {
            return ['error' => 'Invalid response from API.'];
        }
    }

    public function display_prayer_times($atts) {
        // Set default attributes
        $atts = shortcode_atts([
            'city' => 'Dhaka',
            'country' => 'Bangladesh',
            'method' => 1, // Default to University of Islamic Sciences, Karachi
            'school' => 1 // Default to Hanafi (1 = Hanafi, 0 = Shafi)
        ], $atts);

        // Fetch prayer times based on the passed attributes
        $prayer_times = $this->fetch_prayer_times($atts['city'], $atts['country'], $atts['method'], $atts['school']);

        // Check for errors and display if any
        if (isset($prayer_times['error'])) {
            return '<div class="prayer-times-error">' . esc_html($prayer_times['error']) . '</div>';
        }

        // Start the output buffer to display the prayer times
        ob_start();
        ?>
        <div class="masjid-container">
            <h3>Prayer Times for <?php echo esc_html($atts['city'] . ', ' . $atts['country']); ?></h3>
            <ul>
                <?php foreach ($prayer_times as $prayer => $time) : ?>
                    <?php if ($prayer !== 'Sunset' && $prayer !== 'Midnight') : // Exclude unnecessary times ?>
                        <li>
                            <span><?php echo esc_html($this->format_prayer_name($prayer)); ?>:</span>
                            <?php echo esc_html($this->format_time($time)); ?>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
        // Return the output buffer
        return ob_get_clean();
    }

    private function format_time($time) {
        // Convert the 24-hour time to 12-hour format with AM/PM
        $datetime = DateTime::createFromFormat('H:i', $time);
        return $datetime ? $datetime->format('g:i A') : $time; // Converts to 12-hour format (AM/PM)
    }

    private function format_prayer_name($name) {
        // Capitalize prayer names to make them more readable
        $names = [
            'Fajr' => 'Fajr',
            'Dhuhr' => 'Dhuhr',
            'Asr' => 'Asr',
            'Maghrib' => 'Maghrib',
            'Isha' => 'Isha',
            'Sunrise' => 'Sunrise',
            'Sunset' => 'Sunset',
            'Imsak' => 'Imsak',
            'Midnight' => 'Midnight'
        ];
        return $names[$name] ?? ucfirst($name); // Return the formatted prayer name
    }
    
}
