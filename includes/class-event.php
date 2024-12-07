<?php
class Masjid_Events {
    public function __construct() {
        add_action('init', [$this, 'create_events_table']);
        add_action('admin_menu', [$this, 'add_events_admin_menu']);
        add_action('admin_post_add_masjid_event', [$this, 'handle_event_submission']);
        add_shortcode('masjid_events', [$this, 'display_events_with_countdown']);
    }

    // Create events table if not exists
    public function create_events_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'masjid_events';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            event_date DATETIME NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Add Admin Menu for Events
    public function add_events_admin_menu() {
        add_menu_page(
            'Masjid Events',
            'MM Masjid Events',
            'manage_options',
            'masjid-events',
            [$this, 'render_events_admin_page'],
            'dashicons-calendar',
            20
        );
    }

    // Handle Event Submission
    public function handle_event_submission() {
        if (!current_user_can('manage_options') || !isset($_POST['masjid_event_nonce']) || !wp_verify_nonce($_POST['masjid_event_nonce'], 'add_masjid_event')) {
            wp_die('Unauthorized access.');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'masjid_events';

        $title = sanitize_text_field($_POST['event_title']);
        $event_date = sanitize_text_field($_POST['event_date']);

        if (!empty($title) && !empty($event_date)) {
            $wpdb->insert($table_name, [
                'title' => $title,
                'event_date' => $event_date,
            ]);
        }

        wp_redirect(admin_url('admin.php?page=masjid-events'));
        exit;
    }

    // Render Admin Page
    public function render_events_admin_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'masjid_events';
        $events = $wpdb->get_results("SELECT * FROM $table_name ORDER BY event_date ASC");
        ?>
        <div class="wrap">
            <h1>Masjid Events</h1>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <table class="form-table">
                    <tr>
                        <th><label for="event_title">Event Title</label></th>
                        <td><input type="text" name="event_title" id="event_title" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="event_date">Event Date</label></th>
                        <td><input type="datetime-local" name="event_date" id="event_date" required></td>
                    </tr>
                </table>
                <?php wp_nonce_field('add_masjid_event', 'masjid_event_nonce'); ?>
                <input type="hidden" name="action" value="add_masjid_event">
                <?php submit_button('Add Event'); ?>
            </form>

            <h2>Upcoming Events</h2>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Event Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($events)): ?>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?php echo esc_html($event->title); ?></td>
                                <td><?php echo esc_html(date('F j, Y, g:i A', strtotime($event->event_date))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">No upcoming events found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    // Display Events with Countdown
    public function display_events_with_countdown() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'masjid_events';
        $events = $wpdb->get_results("SELECT * FROM $table_name WHERE event_date >= NOW() ORDER BY event_date ASC");

        if (empty($events)) {
            return '<p>No upcoming events found.</p>';
        }

        $output = '<div class="masjid-events-section">';
        foreach ($events as $event) {
            $event_title = esc_html($event->title);
            $event_date = esc_html($event->event_date);
            $event_date_js = date('Y-m-d H:i:s', strtotime($event_date));

            $output .= '<div class="event-item">';
            $output .= '<h3>' . $event_title . '</h3>';
            $output .= '<p>Event Date: ' . date('F j, Y, g:i A', strtotime($event_date)) . '</p>';
            $output .= '<div id="countdown-' . $event->id . '" class="event-countdown" data-event-date="' . $event_date_js . '"></div>';
            $output .= '</div>';
        }
        $output .= '</div>';

        // JavaScript for Countdown
        $output .= "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var countdownElements = document.querySelectorAll('.event-countdown');
                countdownElements.forEach(function(el) {
                    var eventDate = new Date(el.getAttribute('data-event-date')).getTime();
                    var countdownInterval = setInterval(function() {
                        var now = new Date().getTime();
                        var timeLeft = eventDate - now;

                        if (timeLeft < 0) {
                            clearInterval(countdownInterval);
                            el.innerHTML = 'Event Started!';
                            return;
                        }

                        var days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                        var hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        var minutes = Math.floor((timeLeft % (1000 * 60)) / (1000 * 60));
                        var seconds = Math.floor((timeLeft % 1000) / 1000);

                        el.innerHTML = days + 'd ' + hours + 'h ' + minutes + 'm ' + seconds + 's';
                    }, 1000);
                });
            });
        </script>";

        return $output;
    }
}

// Initialize the class
new Masjid_Events();
