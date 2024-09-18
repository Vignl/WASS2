<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (empty($_SESSION['bpmsuid'])) {
    header('location:logout.php');
} else {
    if (isset($_POST['submit'])) {
        $uid = $_SESSION['bpmsuid'];
        $adate = $_POST['adate'];
        $atime = $_POST['atime'];
        $category = $_POST['category'];
        $msg = $_POST['message'];

        // Get estimated time for the selected service
        $serviceQuery = mysqli_query($con, "SELECT EstimatedTime FROM tblservices WHERE ServiceName='$category'");
        $serviceResult = mysqli_fetch_assoc($serviceQuery);
        $estimatedTime = $serviceResult['EstimatedTime'];

        // Calculate end time based on estimated time
        $endTime = date("H:i", strtotime($atime) + $estimatedTime * 60);

        // Generate an appointment number with the current year as a prefix
        $currentYear = date('Y');
        $lastAptNumberQuery = mysqli_query($con, "SELECT MAX(AptNumber) as maxAptNumber FROM tblbook");
        $lastAptNumberResult = mysqli_fetch_assoc($lastAptNumberQuery);
        $lastAptNumber = $lastAptNumberResult['maxAptNumber'] ?? 0;
        $incrementalAptNumber = $lastAptNumber % 10000 + 1; // Ensure it's within 4 digits
        $aptnumber = $currentYear . str_pad($incrementalAptNumber, 4, '0', STR_PAD_LEFT);

        // Check if there is an existing appointment at the specified date and time
        $existingAppointmentQuery = mysqli_query($con, "SELECT * FROM tblbook WHERE AptDate='$adate' AND ((AptTime < '$endTime' AND EndTime > '$atime') OR (AptTime = '$atime')) AND (Status='Pending' OR Status='Accepted')");

        if (mysqli_num_rows($existingAppointmentQuery) > 0) {
            $msg = "Appointment with the same date and time already exists. Please choose a different date and time.";
        } else {
            // Insert the new appointment
            $query = mysqli_query($con, "INSERT INTO tblbook(UserID, AptNumber, AptDate, AptTime, EndTime, ServiceName, Message) VALUES ('$uid', '$aptnumber', '$adate', '$atime', '$endTime', '$category', '$msg')");

            if ($query) {
                $ret = mysqli_query($con, "SELECT AptNumber FROM tblbook WHERE UserID='$uid' ORDER BY ID DESC LIMIT 1;");
                $result = mysqli_fetch_array($ret);
                $_SESSION['aptno'] = $result['AptNumber'];
                echo "<script>window.location.href='thank-you.php'</script>";
            } else {
                $msg = "Failed to book an appointment. Please try again.";
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>Book an Appointment</title>
    <link type="image/png" sizes="32x32" rel="icon" href="https://cdn-icons-png.flaticon.com/128/2441/2441054.png">
    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/style-starter.css">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Slab:400,700,700i&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
</head>

<body id="home">
    <?php include_once('includes/header.php'); ?>

    <script src="assets/js/jquery-3.3.1.min.js"></script>
    <!-- Common jquery plugin -->
    <!--bootstrap working-->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- //bootstrap working-->
    <!-- disable body scroll which navbar is in active -->
    <script>
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function () {
        scrollFunction()
    };

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("movetop").style.display = "block";
        } else {
            document.getElementById("movetop").style.display = "none";
        }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
    
    $(function () {
        $('.navbar-toggler').click(function () {
            $('body').toggleClass('noscroll');
        });
    });
</script>


    <section class="w3l-inner-banner-main">
        <div class="about-inner contact ">
            <div class="container">
                <div class="main-titles-head text-center"></div>
            </div>
        </div>
        <div class="breadcrumbs-sub">
            <div class="container">
                <ul class="breadcrumbs-custom-path">
                    <li class="right-side propClone"><a href="index.php" class="">Home <span class="fa fa-angle-right" aria-hidden="true"></span></a> <p></li>
                    <li class="active ">
                        Book Appointment</li>
                </ul>
            </div>
        </div>
        </div>
    </section>
    <!-- breadcrumbs //-->
    <section class="w3l-contact-info-main" id="contact">
        <div class="contact-sec	 ">
            <div class="container">

                <div class="d-grid contact-view">
                    <div class="cont-details">
                        <?php
                        $ret = mysqli_query($con, "select * from tblpage where PageType='contactus' ");
                        $cnt = 1;
                        while ($row = mysqli_fetch_array($ret)) {
                        ?>
                            <div class="cont-top">
                                <div class="cont-left text-center">
                                    <span class="fa fa-phone text-primary"></span>
                                </div>
                                <div class="cont-right">
                                    <h6>Call Us</h6>
                                    <p class="para"><a href="tel:+44 99 555 42">+<?php echo $row['MobileNumber']; ?></a></p>
                                </div>
                            </div>
                            <div class="cont-top margin-up">
                                <div class="cont-left text-center">
                                    <span class="fa fa-envelope-o text-primary"></span>
                                </div>
                                <div class="cont-right">
                                    <h6>Email Us</h6>
                                    <p class="para"><a href="mailto:example@mail.com" class="mail"><?php echo $row['Email']; ?></a></p>
                                </div>
                            </div>
                            <div class="cont-top margin-up">
                                <div class="cont-left text-center">
                                    <span class="fa fa-map-marker text-primary"></span>
                                </div>
                                <div class="cont-right">
                                    <h6>Address</h6>
                                    <p class="para"> <?php echo $row['PageDescription']; ?></p>
                                </div>
                            </div>
                            <div class="cont-top margin-up">
                                <div class="cont-left text-center">
                                    <span class="fa fa-map-marker text-primary"></span>
                                </div>
                                <div class="cont-right">
                                    <h6>Time</h6>
                                    <?php
                                    // Convert StartTime and EndTime to "10 AM" and "3 PM" format
                                    $startTime = date("h:i A", strtotime($row['StartTime']));
                                    $endTime = date("h:i A", strtotime($row['EndTime']));
                                    ?>
                                    <p class="para"> <?php echo $startTime; ?> to <?php echo $endTime; ?></p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="map-content-9 mt-lg-0 mt-4">
                        <form method="post">
                            <h4 style="padding-bottom: 20px;text-align: center;color: #28a745;">Book an appointment</h4>
                            <div style="padding-top: 30px;">
                                <label>Service:</label>
                                <select class="form-control" name="category" id="category" required="true">
                                    <option value="" disabled selected>Select a Service</option>
                                    <?php
                                    $ret = mysqli_query($con, "SELECT * FROM tblservices WHERE IsEnabled = 1");
                                    while ($row = mysqli_fetch_array($ret)) {
                                        // Concatenate service name with duration inside parentheses
                                        $serviceNameWithDuration = $row['ServiceName'] . " (" . $row['EstimatedTime'] . " minutes)";
                                        echo "<option value='" . $row['ServiceName'] . "' data-time='" . $row['EstimatedTime'] . "'>" . $serviceNameWithDuration . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div style="padding-top: 10px;">
                                <p style="font-size:16px; color:red" align="center"> <?php if ($msg) {
                                                                                            echo $msg;
                                                                                        }  ?> </p>
                                <label>Appointment Date</label>
                                <input type="date" class="form-control appointment_date" placeholder="Date" name="adate" id='adate' required="true">
                            </div>
                            <div>
                                <?php
                                $ret = mysqli_query($con, "SELECT * from tblpage WHERE PageType='contactus' ");
                                $cnt = 1;
                                while ($row = mysqli_fetch_array($ret)) {
                                ?>
                                  <div style="padding-top: 20px;">
                                    <label>Appointment Time</label>
                                    <select class="form-control appointment_time" name="atime" id="atime" required="true">
                                        <?php
                                        // Retrieve business hours from the database
                                        $businessHoursQuery = mysqli_query($con, "SELECT StartTime, EndTime FROM tblpage WHERE PageType='contactus'");
                                        $businessHoursResult = mysqli_fetch_assoc($businessHoursQuery);
                                        $startTime = strtotime($businessHoursResult['StartTime']);
                                        $endTime = strtotime($businessHoursResult['EndTime']);

                                        // Calculate end time for each available slot based on estimated time
                                        while ($startTime < $endTime) {
                                            $slotEndTime = strtotime("+{$estimatedTime} minutes", $startTime);

                                            // Check if this slot overlaps with any existing appointment
                                            $overlapQuery = mysqli_query($con, "SELECT * FROM tblbook WHERE AptDate='$adate' AND ((AptTime < '" . date("H:i", $slotEndTime) . "' AND EndTime > '" . date("H:i", $startTime) . "') OR (AptTime = '" . date("H:i", $startTime) . "')) AND (Status='Pending' OR Status='Accepted')");

                                            if (mysqli_num_rows($overlapQuery) == 0) {
                                                // No overlap found, so this slot is available
                                                echo '<option value="' . date("H:i", $startTime) . '">' . date("h:i A", $startTime) . '</option>';
                                            } else {
                                                // Slot is not available, gray it out
                                                echo '<option value="' . date("H:i", $startTime) . '" disabled style="color: #ccc">' . date("h:i A", $startTime) . ' (Not Available)</option>';
                                            }

                                            $startTime = strtotime('+30 minutes', $startTime); // Keep the increment fixed at 30 minutes
                                        }
                                        ?>
                                    </select>
                                </div>
                                <?php } ?>
                            </div>
                            <button type="submit" class="btn btn-contact" name="submit">Make an Appointment</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <?php include_once('includes/footer.php'); ?>
    <!-- move top -->
    <button onclick="topFunction()" id="movetop" title="Go to top">
        <span class="fa fa-long-arrow-up"></span>
    </button>
    <script>
        $(document).ready(function() {
            $('#category').change(function() {
                var serviceName = $(this).val();
                if (serviceName != '') {
                    $.ajax({
                        url: 'get_estimated_time.php', // Modify the URL to the actual PHP script
                        method: 'POST',
                        data: {serviceName: serviceName},
                        success: function(response) {
                            var estimatedTime = parseInt(response);
                            var selectedTime = $('#atime').val();
                            if (selectedTime != '') {
                                var endTime = new Date("1970-01-01T" + selectedTime + "Z");
                                endTime.setMinutes(endTime.getMinutes() + estimatedTime);
                                var hours = endTime.getUTCHours().toString().padStart(2, '0');
                                var minutes = endTime.getUTCMinutes().toString().padStart(2, '0');
                                $('#endtime').val(hours + ':' + minutes);
                            }
                        }
                    });
                }
            });

            $('#atime').change(function() {
                var selectedTime = $(this).val();
                var estimatedTime = $('#category').find(':selected').data('time');
                if (estimatedTime && selectedTime != '') {
                    var startTime = new Date("1970-01-01T" + selectedTime + "Z");
                    var endTime = new Date(startTime.getTime() + estimatedTime * 60000); // Convert estimated time to milliseconds
                    var hours = endTime.getUTCHours().toString().padStart(2, '0');
                    var minutes = endTime.getUTCMinutes().toString().padStart(2, '0');
                    $('#endtime').val(hours + ':' + minutes);
                }
            });
        });
    </script>
    <script>
        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function () {
            scrollFunction()
        };

        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementById("movetop").style.display = "block";
            } else {
                document.getElementById("movetop").style.display = "none";
            }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
        $(function () {
            var dtToday = new Date();

            var month = dtToday.getMonth() + 1;
            var day = dtToday.getDate();
            var year = dtToday.getFullYear();
            if (month < 10)
                month = '0' + month.toString();
            if (day < 10)
                day = '0' + day.toString();

            var maxDate = year + '-' + month + '-' + day;
            $('#adate').attr('min', maxDate);
        });
    </script>

</body>

</html>
