<?php

// 1. SET TIMEZONE TO PHILIPPINES
date_default_timezone_set('Asia/Manila');

// --- 2. PREPARE DATES ---
$today = date('Y-m-d');
$first_day_of_month = date('Y-m-01');

// --- 3. FETCH STATISTICS ---

// A. TRIP STATISTICS (Global - No driver_id filter)
$trip_stats = [
    'today' => 0, 'month' => 0, 'year' => 0, 'completed' => 0, 'cancelled' => 0
];
$trip_sql = "
    SELECT
        SUM(CASE WHEN DATE(sched_depart_datetime) = '$today' THEN 1 ELSE 0 END) AS today,
        SUM(CASE WHEN DATE(sched_depart_datetime) >= '$first_day_of_month' THEN 1 ELSE 0 END) AS month,
        SUM(CASE WHEN trip_status_id = 3 THEN 1 ELSE 0 END) AS completed, -- ID 3 = Completed
        SUM(CASE WHEN trip_status_id = 4 THEN 1 ELSE 0 END) AS cancelled -- ID 4 = Cancelled
    FROM trip_info";
$trip_res = $conn->query($trip_sql);
if($trip_res) $trip_stats = $trip_res->fetch_assoc();


// B. VEHICLE STATISTICS
// Assumption: vehicle_status_id -> 1=Available, 2=In Use, 3=Maintenance
$vehicle_stats = [
    'total' => 0, 'in_use' => 0, 'available' => 0, 'maintenance' => 0
];
$veh_sql = "
    SELECT
        COUNT(*) as total,
        SUM(CASE WHEN vehicle_status_id = 2 THEN 1 ELSE 0 END) as in_use,
        SUM(CASE WHEN vehicle_status_id = 1 THEN 1 ELSE 0 END) as available,
        SUM(CASE WHEN vehicle_status_id = 3 THEN 1 ELSE 0 END) as maintenance
    FROM vehicle_info";
$veh_res = $conn->query($veh_sql);
if($veh_res) $vehicle_stats = $veh_res->fetch_assoc();


// C. DRIVER STATISTICS
// Assumption: driver_status_id -> 1=Available, 2=On Duty
$driver_stats = [
    'total' => 0, 'on_duty' => 0, 'available' => 0
];
$driver_sql = "
    SELECT
        COUNT(*) as total,
        SUM(CASE WHEN driver_status_id = 2 THEN 1 ELSE 0 END) as on_duty,
        SUM(CASE WHEN driver_status_id = 1 THEN 1 ELSE 0 END) as available
    FROM driver_info";
$driver_res = $conn->query($driver_sql);
if($driver_res) $driver_stats = $driver_res->fetch_assoc();

?>

<div class="dashboard-header">
    <h1>Dashboard Overview</h1>
    <div class="current-date">
        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Zm280 240q-17 0-28.5-11.5T440-440q0-17 11.5-28.5T480-480q17 0 28.5 11.5T520-440q0 17-11.5 28.5T480-400Zm-160 0q-17 0-28.5-11.5T280-440q0-17 11.5-28.5T320-480q17 0 28.5 11.5T360-440q0 17-11.5 28.5T320-400Zm320 0q-17 0-28.5-11.5T600-440q0-17 11.5-28.5T640-480q17 0 28.5 11.5T680-440q0 17-11.5 28.5T640-400ZM480-240q-17 0-28.5-11.5T440-280q0-17 11.5-28.5T480-320q17 0 28.5 11.5T520-280q0 17-11.5 28.5T480-240Zm-160 0q-17 0-28.5-11.5T280-280q0-17 11.5-28.5T320-320q17 0 28.5 11.5T360-280q0 17-11.5 28.5T320-240Zm320 0q-17 0-28.5-11.5T600-280q0-17 11.5-28.5T640-320q17 0 28.5 11.5T680-280q0 17-11.5 28.5T640-240Z"/></svg>
        <?php echo date('l, F j, Y | g:i A'); ?>
    </div>
</div>
    

<div class="dashboard-container">
    <h2 class="section-title">Trip Statistics</h2>
    <div class="stats-row">
        <div class="stat-card blue">
            <span class="stat-number"><?php echo $trip_stats['today'] ?? 0; ?></span>
            <span class="stat-label">Trips Today</span>
        </div>
        <div class="stat-card blue">
            <span class="stat-number"><?php echo $trip_stats['month'] ?? 0; ?></span>
            <span class="stat-label">This Month</span>
        </div>

        <div class="stat-card green">
            <span class="stat-number"><?php echo $trip_stats['completed'] ?? 0; ?></span>
            <span class="stat-label">Completed</span>
        </div>
        <div class="stat-card red">
            <span class="stat-number"><?php echo $trip_stats['cancelled'] ?? 0; ?></span>
            <span class="stat-label">Cancelled</span>
        </div>
    </div>


    <h2 class="section-title">Vehicle Status</h2>
    <div class="stats-row">
        <div class="stat-card blue">
            <span class="stat-number"><?php echo $vehicle_stats['total'] ?? 0; ?></span>
            <span class="stat-label">Total Vehicles</span>
        </div>
        <div class="stat-card yellow">
            <span class="stat-number"><?php echo $vehicle_stats['in_use'] ?? 0; ?></span>
            <span class="stat-label">Currently In Use</span>
        </div>
        <div class="stat-card green">
            <span class="stat-number"><?php echo $vehicle_stats['available'] ?? 0; ?></span>
            <span class="stat-label">Available</span>
        </div>
        <div class="stat-card red">
            <span class="stat-number"><?php echo $vehicle_stats['maintenance'] ?? 0; ?></span>
            <span class="stat-label">Need Maintenance</span>
        </div>
    </div>


    <h2 class="section-title">Driver Status</h2>
    <div class="stats-row">
        <div class="stat-card blue">
            <span class="stat-number"><?php echo $driver_stats['total'] ?? 0; ?></span>
            <span class="stat-label">Total Drivers</span>
        </div>
        <div class="stat-card yellow">
            <span class="stat-number"><?php echo $driver_stats['on_duty'] ?? 0; ?></span>
            <span class="stat-label">Currently On Duty</span>
        </div>
        <div class="stat-card green">
            <span class="stat-number"><?php echo $driver_stats['available'] ?? 0; ?></span>
            <span class="stat-label">Drivers Available</span>
        </div>
    </div>

</div>

</body>
</html>