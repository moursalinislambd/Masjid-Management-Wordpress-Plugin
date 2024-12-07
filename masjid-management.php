<?php
/**
 * Plugin Name:       Masjid Management Wordpress Plugin
 * Plugin URI:        https://github.com/moursalinislambd/Masjid-Management-Wordpress-Plugin
 * Description:       The Masjid Management Plugin is a complete solution for managing various aspects of masjid operations through WordPress.This plugin provides features such as prayer time display, Qibla direction, islamic event management, masjid announcements, and more.  It's designed to help masjid administrators manage their daily operations easily and efficiently.
 * Version:           1.0
 * Requires at least: 5.0
 * Requires PHP:      7.0
 * Author:            Moursalin islam | OnexusDev
 * Author URI:        https://www.facebook.com/morsalinislam.bd
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       islamic-quotes-plugin
 * Domain Path:       /languages
 * Tags:              MasjidIslamic,Prayer Times, Qibla Direction, Iftar Times, Sahri Times, Event Management, Namaz Schedule, Ramadan Timetable, Dua and Zikr, Masjid Donation, Islamic Events, Islamic Management, Prayer Notifications, Hadith, Islamic Library, Quranic Verses,  Masjid Automation, Islamic Compass, WordPress Islamic Plugin,
 * Tested up to:      6.2
 * Stable tag:        1.0
 *
 * ------------------------------------------------------------------------
 * This plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * This plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this plugin. If not, see <https://www.gnu.org/licenses/>.
 * ------------------------------------------------------------------------
 */




define('MASJID_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MASJID_PLUGIN_URL', plugin_dir_url(__FILE__));

add_action('wp_enqueue_scripts', 'masjid_enqueue_styles');
function masjid_enqueue_styles() {
    wp_enqueue_style(
        'masjid-main-style', 
        plugin_dir_url(__FILE__) . 'assets/css/main.css', 
        [], 
        time() // Use `time()` to ensure no cache issues during development
    );
}


function load_qibla_direction_styles() {
    // Replace 'your-plugin-name' with the actual plugin name or path
    wp_enqueue_style('qibla-direction-styles', plugin_dir_url(__FILE__) . 'assets/css/qibla-direction.css');
}
add_action('wp_enqueue_scripts', 'load_qibla_direction_styles');


wp_enqueue_style('masjid-events-style', plugin_dir_url(__FILE__) . 'assets/css/event.css', [], time());



//devof of sahri and iftar times
function display_sahri_iftar_times() {
    $city = 'Dhaka';
    $country = 'Bangladesh';
    $method = 1; // Adjust to your desired calculation method
    $school = 1; // Adjust to your desired calculation method . Asr method=  0=Standard, 1=Hanafi Juristic


    $prayer_times = fetch_prayer_times($city, $country, $method, $school);

    if ($prayer_times) {
        $sahri_time = $prayer_times['Imsak']; // Time for Sahri ends
        $iftar_time = $prayer_times['Maghrib']; // Time for Iftar starts

        ob_start();
        ?>
        <div class="sahri-iftar-container">
            <h3>Sahri and Iftar Times</h3>
            <p><strong>Sahri Time:</strong> <?php echo esc_html($sahri_time); ?></p>
            <p><strong>Iftar Time:</strong> <?php echo esc_html($iftar_time); ?></p>
        </div>
        <?php
        return ob_get_clean();
    } else {
        return '<p>Unable to fetch prayer times. Please try again later.</p>';
    }
}
add_shortcode('sahri_iftar', 'display_sahri_iftar_times');


add_filter('plugin_row_meta', 'masjid_plugin_meta_links', 10, 2);

function masjid_plugin_meta_links($links, $file) {
    // Check if it's your plugin
    if ($file == plugin_basename(__FILE__)) {
        // Add custom links
        $custom_links = [
            '<a href="https://github.com/moursalinislambd/Masjid-Management-Wordpress-Plugin" target="_blank">Doc, FaQ & Update</a>',
            '<a href="https://github.com/moursalinislambd/Masjid-Management-Wordpress-Plugin/blob/2354ebc14970724c32c223b9b6af29133878c4c3/README.md" target="_blank">Documentation & ShortCode </a>',
            '<a href="https://bdislamicqa.xyz/wordpress-plugin/" target="_blank">Check update</a>',
        ];

        // Append the custom links
        $links = array_merge($links, $custom_links);
    }

    return $links;
}




// Include core classes
require_once MASJID_PLUGIN_DIR . 'includes/class-prayer-times.php';
require_once MASJID_PLUGIN_DIR . 'includes/class-announcements.php';
require_once MASJID_PLUGIN_DIR . 'includes/class-qibla-direction.php';
require_once MASJID_PLUGIN_DIR . 'includes/class-namaz-times.php';
require_once MASJID_PLUGIN_DIR . 'includes/class-sahri-iftar.php';
require_once MASJID_PLUGIN_DIR . 'includes/class-event.php';




function masjid_init() {
    new Masjid_Prayer_Times();
    new Masjid_Announcements();
    new Masjid_Qibla_Direction();
    new Masjid_Namaz_Times();

}
add_action('plugins_loaded', 'masjid_init');

