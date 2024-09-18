<?php
// Include your database connection file
include('includes/dbconnection.php');

// Check if the selected service is sent via POST
if (isset($_POST['serviceName'])) {
    $serviceName = $_POST['serviceName'];
    
    // Prepare and execute the query to fetch the estimated time for the selected service
    $query = mysqli_prepare($con, "SELECT EstimatedTime FROM tblservices WHERE ServiceName = ?");
    mysqli_stmt_bind_param($query, 's', $serviceName);
    mysqli_stmt_execute($query);
    mysqli_stmt_bind_result($query, $estimatedTime);
    mysqli_stmt_fetch($query);
    mysqli_stmt_close($query);

    if ($estimatedTime) {
        // Return the estimated time for the selected service
        echo $estimatedTime;
    } else {
        // Default estimated time if the selected service is not found
        echo "0";
    }
} else {
    // Handle the case where the service name is not sent via POST
    echo "0";
}

// Close the database connection
mysqli_close($con);
?>
