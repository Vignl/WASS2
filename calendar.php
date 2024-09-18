<?php
session_start();
error_reporting(0);

include('includes/dbconnection.php');

$appointments = [];
$query = "SELECT ServiceName, AptDate, AptTime FROM tblbook WHERE Status = 'Accepted'";
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    // Format the time to show only hours and AM/PM
    $time = date("h:i A", strtotime($row['AptTime']));
    // Add the formatted time to the appointment data
    $row['AptTimeFormatted'] = $time;
    $appointments[] = $row;
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Calendar</title>
    <link type="image/png" sizes="32x32" rel="icon" href="https://cdn-icons-png.flaticon.com/128/2441/2441054.png">

    <link rel="stylesheet" href="assets/css/style-starter.css">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Slab:400,700,700i&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.11/index.global.min.js'></script>
    <style>
        #calendar {
            max-width: 700px;
            margin: 0 auto;
        }
        .fc-title {
        font-size: 10px; /* Adjust the font size as needed */
    }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: [
            <?php
            foreach ($appointments as $appointment) {
                $title = $appointment['ServiceName'];
                $start = $appointment['AptDate'] . 'T' . $appointment['AptTime'];
                $time = $appointment['AptTimeFormatted']; // Use the formatted time
                echo "{ title: '{$title}', start: '{$start}', time: '{$time}' },";
            }
            ?>
        ],
        eventContent: function(arg) {
            return {
                html: '<div class="fc-title">' + arg.event.title + '<br>' + arg.event.extendedProps.time + '</div>'
            };
        }
    });

    calendar.render();
});
    </script>
</head>
<body>
    <?php include_once('includes/header.php');?>
        <div style="padding-top: 5%; padding-bottom: 5%" id='calendar'></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <?php include_once('includes/footer.php');?>
</body>
</html>
