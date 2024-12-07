<?php

class Masjid_Announcements {
    public function __construct() {
        add_action('init', [$this, 'register_announcements_post_type']);
        add_shortcode('masjid_announcements', [$this, 'display_announcements']);
    }

    public function register_announcements_post_type() {
        register_post_type('masjid_announcements', [
            'labels' => [
                'name' => 'MM Announcements',
                'singular_name' => 'Announcement',
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'announcements'],
            'supports' => ['title', 'editor'],
        ]);
    }

    public function display_announcements() {
        $query = new WP_Query([
            'post_type' => 'masjid_announcements',
            'posts_per_page' => 5,
        ]);

        if (!$query->have_posts()) {
            return '<p>No announcements at this time.</p>';
        }

        ob_start();
        ?>
        <div class="masjid-container">
            <h3>Latest Announcements</h3>
            <ul>
                <?php while ($query->have_posts()): $query->the_post(); ?>
                    <li>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        <p><?php the_excerpt(); ?></p>
                    </li>
                <?php endwhile; wp_reset_postdata(); ?>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }
}
