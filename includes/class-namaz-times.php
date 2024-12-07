<?php

class Masjid_Namaz_Times {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_shortcode('masjid_namaz_times', [$this, 'display_namaz_times']);
    }

    public function add_settings_page() {
        add_menu_page(
            'Masjid Management Namaz Times',
            'MM Namaz Times',
            'manage_options',
            'masjid-namaz-times',
            [$this, 'render_settings_page'],
            'dashicons-clock',
            20
        );
    }

    public function render_settings_page() {
        if (isset($_POST['save_namaz_times'])) {
            $namaz_times = [
                'fajr' => sanitize_text_field($_POST['fajr']),
                'dhuhr' => sanitize_text_field($_POST['dhuhr']),
                'asr' => sanitize_text_field($_POST['asr']),
                'maghrib' => sanitize_text_field($_POST['maghrib']),
                'isha' => sanitize_text_field($_POST['isha']),
                'jummah' => sanitize_text_field($_POST['jummah']),
                'tahajjud' => sanitize_text_field($_POST['tahajjud']),
                'eid' => sanitize_text_field($_POST['eid']),
            ];
            update_option('masjid_namaz_times', $namaz_times);
        }

        $namaz_times = get_option('masjid_namaz_times', [
            'fajr' => '',
            'dhuhr' => '',
            'asr' => '',
            'maghrib' => '',
            'isha' => '',
            'jummah' => '',
            'tahajjud' => '',
            'eid' => '',
        ]);
        ?>
        <div class="wrap">
            <h1>Masjid Management | Namaz Times</h1>
            <h3> Menual Namaz Time Set </h3>
            <form method="post">
                <table class="form-table">
                    <tr><th>Fajr</th><td><input type="time" name="fajr" value="<?php echo isset($namaz_times['fajr']) ? esc_attr($namaz_times['fajr']) : ''; ?>" /></td></tr>
                    <tr><th>Dhuhr</th><td><input type="time" name="dhuhr" value="<?php echo isset($namaz_times['dhuhr']) ? esc_attr($namaz_times['dhuhr']) : ''; ?>" /></td></tr>
                    <tr><th>Asr</th><td><input type="time" name="asr" value="<?php echo isset($namaz_times['asr']) ? esc_attr($namaz_times['asr']) : ''; ?>" /></td></tr>
                    <tr><th>Maghrib</th><td><input type="time" name="maghrib" value="<?php echo isset($namaz_times['maghrib']) ? esc_attr($namaz_times['maghrib']) : ''; ?>" /></td></tr>
                    <tr><th>Isha</th><td><input type="time" name="isha" value="<?php echo isset($namaz_times['isha']) ? esc_attr($namaz_times['isha']) : ''; ?>" /></td></tr>
                    <tr><th>Jummah</th><td><input type="time" name="jummah" value="<?php echo isset($namaz_times['jummah']) ? esc_attr($namaz_times['jummah']) : ''; ?>" /></td></tr>
                    <tr><th>Tahajjud</th><td><input type="time" name="tahajjud" value="<?php echo isset($namaz_times['tahajjud']) ? esc_attr($namaz_times['tahajjud']) : ''; ?>" /></td></tr>
                    <tr><th>Eid Namaz</th><td><input type="time" name="eid" value="<?php echo isset($namaz_times['eid']) ? esc_attr($namaz_times['eid']) : ''; ?>" /></td></tr>
                </table>
                <p class="submit"><input type="submit" name="save_namaz_times" value="Save Changes" class="button-primary" /></p>
            </form>
        </div>
        <?php
    }

    public function display_namaz_times() {
        $namaz_times = get_option('masjid_namaz_times', []);

        ob_start();
        ?>
        <div class="masjid-container">
            <h3>Namaz Times</h3>
            <ul>
                <?php foreach ($namaz_times as $prayer => $time) : ?>
                    <?php if (!empty($time)) : ?>
                        <li>
                            <span><?php echo ucfirst($prayer); ?>:</span>
                            <?php echo esc_html($this->format_time($time)); ?>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }

    private function format_time($time) {
        $datetime = DateTime::createFromFormat('H:i', $time);
        return $datetime ? $datetime->format('g:i A') : $time; // Converts to 12-hour format with AM/PM
    }
    
}
