<?php
include "db_connect.php";

$temp = $_GET['temperature'];
$hum = $_GET['humidity'];

$sql = "INSERT INTO sensor_data (temperature, humidity)
        VALUES ('$temp', '$hum')";

if (mysqli_query($conn, $sql)) {
    echo "Data saved successfully";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
