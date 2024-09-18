<?php
include('includes/dbconnection.php');

if (isset($_POST['date']) && isset($_POST['serviceName'])) {
    $date = $_POST['date'];
    $serviceName = $_POST['serviceName'];
    
    // Fetch the estimated time for the service
    $query = mysqli_query($con, "SELECT EstimatedTime FROM tblservices WHERE ServiceName='$serviceName'");
    $result = mysqli_fetch_assoc($query);
    $estimatedTime = $result['EstimatedTime'];

    // Define available time slots
    $timeSlots = [
        '08:00', '08:15', '08:30', '08:45',
        '09:00', '09:15', '09:30', '09:45',
        '10:00', '10:15', '10:30', '10:45',
        '11:00', '11:15', '11:30', '11:45',
        '13:00', '13:15', '13:30', '13:45',
        '14:00', '14:15', '14:30', '14:45',
        '15:00', '15:15', '15:30', '15:45',
        '16:00', '16:15', '16:30', '16:45',
        '17:00', '17:15', '17:30', '17:45'
    ];

    // Fetch existing appointments for the given date
    $query = mysqli_query($con, "SELECT AptTime, EndTime FROM tblbook WHERE AptDate='$date' AND Status='Pending'");
    $existingAppointments = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $existingAppointments[] = ['start' => $row['AptTime'], 'end' => $row['EndTime']];
    }

    // Filter available time slots
    $availableSlots = [];
    foreach ($timeSlots as $slot) {
        $slotEnd = date("H:i", strtotime($slot) + $estimatedTime * 60);
        $isAvailable = true;
        foreach ($existingAppointments as $appointment) {
            if (($slot < $appointment['end'] && $slotEnd > $appointment['start']) || ($slot == $appointment['start'])) {
                $isAvailable = false;
                break;
            }
        }
        if ($isAvailable) {
            $availableSlots[] = $slot;
        }
    }

    // Generate HTML options
    foreach ($availableSlots as $slot) {
        echo "<option value=\"$slot\">$slot</option>";
    }
}
?>
