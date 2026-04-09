<?php
include "db_connect.php";

$result = mysqli_query($conn, "SELECT * FROM device_control WHERE id=1");
$row = mysqli_fetch_assoc($result);

if ($row) {
    echo $row['south_led'] . "," .
         $row['north_led'] . "," .
         $row['fan1'] . "," .
         $row['fan2'];
} else {
    // fallback default values
    echo "0,0,0,0";
}
?>