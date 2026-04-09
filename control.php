<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['device']) && isset($_GET['state'])) {
    $device = $_GET['device'];
    $state = $_GET['state'] == '1' ? 1 : 0;

    $allowed = ['south_led', 'north_led', 'fan1', 'fan2'];
    if (in_array($device, $allowed)) {
        mysqli_query($conn, "UPDATE device_control SET $device=$state WHERE id=1");
    }

    header("Location: control.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM device_control WHERE id=1");
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Home Control</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: auto;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .back-btn {
            display: inline-block;
            padding: 8px 12px;
            background: transparent;
            color: #333;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .back-btn:hover {
            background: #f0f0f0;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            padding: 30px;
            text-align: center;
        }
        .card h2 {
            color: #333;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 20px;
        }
        .card p {
            color: #666;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .card p strong {
            color: #28a745;
            font-size: 18px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 5px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
        }
        .btn.on {
            background: #28a745;
            color: white;
        }
        .btn.on:hover {
            background: #218838;
        }
        .btn.off {
            background: #dc3545;
            color: white;
        }
        .btn.off:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" class="back-btn">‹</a>
    <h1>Smart Home Device Control</h1>

    <div class="grid">
        <?php
        $devices = [
            'south_led' => 'South Outdoor Light',
            'north_led' => 'North Outdoor Light',
            'fan1' => 'Fan 1',
            'fan2' => 'Fan 2'
        ];

        foreach ($devices as $key => $label):
        ?>
        <div class="card">
            <h2><?php echo $label; ?></h2>
            <p>Status: <strong><?php echo $row[$key] ? 'ON' : 'OFF'; ?></strong></p>
            <a class="btn on" href="?device=<?php echo $key; ?>&state=1">ON</a>
            <a class="btn off" href="?device=<?php echo $key; ?>&state=0">OFF</a>
        </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>