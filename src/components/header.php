<?php
function displayDateTime()
{
    date_default_timezone_set('Asia/Manila');

    // Get the current date and time
    $currentDate = date('l, F j, Y'); // Includes the day of the week
    $currentTime = date('h:i:s A');

    // Output the date and time
    echo '<div class="date-time-component">';
    echo '<p class="date">' . $currentDate . '</p>';
    echo '<p class="time" id="time">' . $currentTime . '</p>';
    echo '</div>';
}
?>
<script>
function updateTime() {
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var seconds = now.getSeconds();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;
    var strTime = hours + ':' + minutes + ':' + seconds + ' ' + ampm;
    document.getElementById('time').innerHTML = strTime;
}

// Update the time every second
setInterval(updateTime, 1000);
</script>