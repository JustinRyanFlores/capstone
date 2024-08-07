<?php
function displayDateTime()
{
    date_default_timezone_set('Asia/Manila');

    // Get the current date and time
    $currentDate = date('l, F j, Y'); // Includes the day of the week
    $currentTime = date('h:i A');

    // Output the date and time
    echo '<div class="date-time-component">';
    echo '<p class="date">' . $currentDate . '</p>';
    echo '<p class="time">' . $currentTime . '</p>';
    echo '</div>';
}