<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "db_connect.php";

// latest 20 rows
$sql = "SELECT * FROM sensor_data ORDER BY id DESC LIMIT 20";
$result = mysqli_query($conn, $sql);

$rows = [];
$labels = [];
$temps = [];
$hums = [];

while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}

// reverse for oldest -> newest in chart/table
$rows = array_reverse($rows);

foreach ($rows as $row) {
    $labels[] = date('H:i:s', strtotime($row['created_at']));
    $temps[] = (float)$row['temperature'];
    $hums[] = (float)$row['humidity'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="10">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            padding: 20px;
            margin-bottom: 20px;
        }
        h1, h2 { margin-top: 0; }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border-bottom: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th { position: sticky; top: 0; background: #fff; }
        .table-wrap {
            max-height: 420px;
            overflow-y: auto;
        }
        .controls {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
            align-items: center;
        }
        input {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 220px;
        }
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .control-btn {
            padding: 8px 16px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 18px;
        }
        .control-btn:hover {
            background: #218838;
        }
        .logout-btn {
            padding: 8px 16px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
        }
        .logout-btn:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="header-bar">
            <div>
                <h1>TEMP&HUMIDITY MONITOR DASBOARD</h1>
                <p>Showing latest <strong>20 sensor readings</strong> on table.</p>
            </div>
            <div class="user-info">
                <a href="control.php" class="control-btn">⚙</a>
                <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Temperature & Humidity Trend</h2>
        <canvas id="sensorChart" height="100"></canvas>
    </div>

    <div class="card">
        <div class="controls">
            <h2 style="margin:0;"> Last 20 Records</h2>
            <input type="text" id="searchInput" placeholder="Search time or value...">
        </div>
        <div class="table-wrap">
            <table id="dataTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Temperature (°C)</th>
                        <th>Humidity (%)</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['temperature']; ?></td>
                        <td><?php echo $row['humidity']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('sensorChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [
            {
                label: 'Temperature (°C)',
                data: <?php echo json_encode($temps); ?>,
                tension: 0.35,
                fill: false
            },
            {
                label: 'Humidity (%)',
                data: <?php echo json_encode($hums); ?>,
                tension: 0.35,
                fill: false
            }
        ]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            tooltip: {
                enabled: true
            }
        }
    }
});

// simple interactive table search
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#dataTable tbody tr');

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>
</body>
</html>