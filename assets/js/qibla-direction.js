document.addEventListener('DOMContentLoaded', function () {
    // Ensure that geolocation is available in the browser
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var userLat = position.coords.latitude;
            var userLon = position.coords.longitude;

            // Qibla direction calculation for a fixed location (e.g., Kaaba)
            var kaabaLat = 21.4225;  // Latitude of the Kaaba
            var kaabaLon = 39.8262;  // Longitude of the Kaaba

            var qiblaAngle = calculateQiblaAngle(userLat, userLon, kaabaLat, kaabaLon);
            
            // Display Qibla angle and update the compass
            updateCompass(qiblaAngle);

            // Display the user's location
            var locationText = document.getElementById('user-location');
            locationText.textContent = userLat.toFixed(4) + ', ' + userLon.toFixed(4);
        }, function(error) {
            alert("Geolocation error: " + error.message);
        });
    } else {
        alert("Geolocation is not supported by this browser.");
    }

    // Function to calculate Qibla angle using the Haversine formula
    function calculateQiblaAngle(userLat, userLon, kaabaLat, kaabaLon) {
        var lat1 = toRadians(userLat);
        var lon1 = toRadians(userLon);
        var lat2 = toRadians(kaabaLat);
        var lon2 = toRadians(kaabaLon);

        var deltaLon = lon2 - lon1;

        var y = Math.sin(deltaLon) * Math.cos(lat2);
        var x = Math.cos(lat1) * Math.sin(lat2) - Math.sin(lat1) * Math.cos(lat2) * Math.cos(deltaLon);
        var qiblaAngle = Math.atan2(y, x);

        // Convert the angle from radians to degrees and ensure it's between 0 and 360
        var qiblaAngleDeg = (toDegrees(qiblaAngle) + 360) % 360;
        return qiblaAngleDeg;
    }

    // Convert degrees to radians
    function toRadians(degrees) {
        return degrees * Math.PI / 180;
    }

    // Convert radians to degrees
    function toDegrees(radians) {
        return radians * 180 / Math.PI;
    }

    // Update the compass with the Qibla direction
    function updateCompass(angle) {
        var compassArrow = document.querySelector('.qibla-arrow-icon');
        if (compassArrow) {
            compassArrow.style.transform = 'rotate(' + angle + 'deg)';
            document.getElementById('qibla-direction-text').textContent = "Qibla direction: " + angle.toFixed(2) + "°";
        }
    }
});
