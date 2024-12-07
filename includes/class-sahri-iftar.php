<?php

function fetch_prayer_times($city = 'Dhaka', $country = 'Bangladesh', $method = 2) {
    $api_url = "https://api.aladhan.com/v1/timingsByCity?city=$city&country=$country&method=$method";

    $response = wp_remote_get($api_url);
    
    if (is_wp_error($response)) {
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['data']['timings'])) {
        $timings = $data['data']['timings'];

        // Convert timings to 12-hour format
        foreach ($timings as $key => $time) {
            $timings[$key] = date("g:i A", strtotime($time));
        }

        return $timings;
    }

    return false;
}


?>
