<?php



// 1. SET TIMEZONE TO PHILIPPINES
date_default_timezone_set('Asia/Manila');

// Assuming $conn is available...
$current_driver_id = $_SESSION['driver_id'];

// --- 2. FETCH DRIVER NAME ---
$fname_sql = "SELECT driver_fname FROM driver_info WHERE driver_id = ?";
$stmtname = $conn->prepare($fname_sql);
$stmtname->bind_param("i", $current_driver_id); 
$stmtname->execute();
$result_name = $stmtname->get_result();
$driver_fname = "Driver"; 
if ($result_name->num_rows > 0) {
    $row_name = $result_name->fetch_assoc();
    $driver_fname = htmlspecialchars($row_name['driver_fname']); 
}
$stmtname->close();

// --- 3. PREPARE DATES ---
$today = date('Y-m-d');
$first_day_of_month = date('Y-m-01');

// --- 4. FETCH STATISTICS ---
$stats = [
    'total_today' => 0,
    'total_month' => 0,
    'all_completed' => 0,
    'all_cancelled' => 0,
];

$stats_sql = "
    SELECT
        SUM(CASE WHEN DATE(sched_depart_datetime) = ? THEN 1 ELSE 0 END) AS total_today,
        SUM(CASE WHEN DATE(sched_depart_datetime) >= ? THEN 1 ELSE 0 END) AS total_month,
        SUM(CASE WHEN trip_status_id = 3 THEN 1 ELSE 0 END) AS all_completed,
        SUM(CASE WHEN trip_status_id = 4 THEN 1 ELSE 0 END) AS all_cancelled
    FROM trip_info
    WHERE driver_id = ?
";

$stmt_stats = $conn->prepare($stats_sql);
$stmt_stats->bind_param("ssi", $today, $first_day_of_month, $current_driver_id);
$stmt_stats->execute();
$result_stats = $stmt_stats->get_result();

if ($result_stats->num_rows > 0) {
    $stats_row = $result_stats->fetch_assoc();
    $stats['total_today']   = $stats_row['total_today'] ?? 0;
    $stats['total_month']   = $stats_row['total_month'] ?? 0;
    $stats['all_completed'] = $stats_row['all_completed'] ?? 0;
    $stats['all_cancelled'] = $stats_row['all_cancelled'] ?? 0;
}
$stmt_stats->close();
?>

<div class="dashboard-header">
    <h1>Welcome back, <?php echo $driver_fname; ?>!</h1>
    <div class="current-date">
        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Zm280 240q-17 0-28.5-11.5T440-440q0-17 11.5-28.5T480-480q17 0 28.5 11.5T520-440q0 17-11.5 28.5T480-400Zm-160 0q-17 0-28.5-11.5T280-440q0-17 11.5-28.5T320-480q17 0 28.5 11.5T360-440q0 17-11.5 28.5T320-400Zm320 0q-17 0-28.5-11.5T600-440q0-17 11.5-28.5T640-480q17 0 28.5 11.5T680-440q0 17-11.5 28.5T640-400ZM480-240q-17 0-28.5-11.5T440-280q0-17 11.5-28.5T480-320q17 0 28.5 11.5T520-280q0 17-11.5 28.5T480-240Zm-160 0q-17 0-28.5-11.5T280-280q0-17 11.5-28.5T320-320q17 0 28.5 11.5T360-280q0 17-11.5 28.5T320-240Zm320 0q-17 0-28.5-11.5T600-280q0-17 11.5-28.5T640-320q17 0 28.5 11.5T680-280q0 17-11.5 28.5T640-240Z"/></svg>
        <?php echo date('l, F j, Y | g:i A'); ?>
    </div>
</div>
    
<div class="dashboard-container">

    <h3 style="color: var(--primary-color); margin-bottom: 20px;">Trip Performance Summary</h3>
    
    <div class="stats-grid">
        <div class="stat-card today">
            <span class="stat-number"><?php echo $stats['total_today']; ?></span>
            <span class="stat-label">Trips Today</span>
        </div>

        <div class="stat-card">
            <span class="stat-number"><?php echo $stats['total_month']; ?></span>
            <span class="stat-label">This Month</span>
        </div>

        <div class="stat-card completed">
            <span class="stat-number"><?php echo $stats['all_completed']; ?></span>
            <span class="stat-label">Total Completed</span>
        </div>

        <div class="stat-card cancelled">
            <span class="stat-number"><?php echo $stats['all_cancelled']; ?></span>
            <span class="stat-label">Total Cancelled</span>
        </div>
    </div>

</div>