<?php
// =========================================================
// 1. PAGINATION & LIMIT LOGIC
// =========================================================

// A. PRESERVE THE ROUTING (Fix for "Page Not Found")
// We capture 'trips' from ?page=trips so we can keep it in the URL
$current_route = isset($_GET['page']) ? $_GET['page'] : '';

// B. Get the Limit (Default to 5)
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

// C. Get the Current Page Number (CHANGED from 'page' to 'p' to avoid conflict)
// We use 'p' for the page number (1, 2, 3...) instead of 'page'
$curr_page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($curr_page < 1) $curr_page = 1;

// D. Calculate Offset
$offset = ($curr_page - 1) * $limit;

// E. Get Total Records
$count_sql = "SELECT COUNT(*) as total FROM trip_info"; 
$count_result = $conn->query($count_sql);
$count_row = $count_result->fetch_assoc();
$total_rows = $count_row['total'];

// F. Calculate Total Pages
$total_pages = ceil($total_rows / $limit);

// G. Prepare Display Numbers (e.g., "Showing 1 to 5")
$start_entry = ($total_rows > 0) ? $offset + 1 : 0;
$end_entry   = min($offset + $limit, $total_rows);

$data = "
    SELECT
        t.trip_id AS trip_id,
        CONCAT_WS (' ', 
            d.driver_fname,
            CASE WHEN d.driver_mi IS NULL OR d.driver_mi = '' THEN NULL ELSE CONCAT (d.driver_mi, '.') END,
            d.driver_lname) AS driver_name, 
        vt.vehicle_type AS vehicle_type,
        v.plate_no AS plate_no,
        t.origin AS origin,
        t.destination AS destination,
        t.sched_depart_datetime AS departure,
        t.sched_arrival_datetime AS arrival,
        t.trip_cost AS trip_cost,
        p.purpose AS purpose,
        d.contact_no AS contact,
        td.trip_status AS trip_status
    FROM trip_info t
    JOIN driver_info d ON t.driver_id = d.driver_id
    JOIN vehicle_info v ON t.vehicle_id = v.vehicle_id
    JOIN vehicle_type_data vt ON v.vehicle_type_id = vt.vehicle_type_id
    JOIN trip_status_data td ON t.trip_status_id = td.trip_status_id
    JOIN purpose_data p ON t.purpose_id = p.purpose_id 
    ORDER BY t.trip_id ASC
    LIMIT $limit OFFSET $offset 
";

$result = $conn->query($data);
if (!$result) { die ("Invalid query: " . $conn->error); }
?>

<div class="content-header">
    <div class="filter-search">
        <button class="filter" title="Filter">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="#183a59" width="24" height="24">
                <path d="M440-160q-17 0-28.5-11.5T400-200v-240L168-736q-15-20-4.5-42t36.5-22h560q26 0 36.5 22t-4.5 42L560-440v240q0 17-11.5 28.5T520-160h-80Zm40-308 198-252H282l198 252Zm0 0Z"/>
            </svg>
        </button>
        <input type="search" placeholder="Search...">
    </div>
    
    <div class="page-title">SCHEDULED TRIPS</div>
    
    <button class="add-button">
        + Add Trip
    </button>
</div>

<div class="content-table">
    <table class="trip-schedule">
        <colgroup></colgroup>
        <thead>
            <tr>
                <th>Trip ID</th>
                <th>Driver</th>
                <th>Vehicle Type</th>
                <th>Plate No.</th>
                <th>Origin</th>
                <th>Destination</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php 
                $modalData = [
                    'tripId'      => $row['trip_id'],
                    'driverName'  => $row['driver_name'],
                    'contact'     => $row['contact'],
                    'vehicleType' => $row['vehicle_type'],
                    'plateNo'     => $row['plate_no'],
                    'origin'      => $row['origin'],
                    'destination' => $row['destination'],
                    'route'       => $row['origin'] . ' ➝ ' . $row['destination'], 
                    'departure'   => date("M j, Y g:i A", strtotime($row['departure'])), 
                    'arrival'     => date("M j, Y g:i A", strtotime($row['arrival'])),
                    'status'      => $row['trip_status'],
                    'purpose'     => $row['purpose'],
                    'totalCost'   => '₱' . number_format($row['trip_cost'], 2)
                ];
                $safeJson = htmlspecialchars(json_encode($modalData), ENT_QUOTES, 'UTF-8');
                $deleteUrl = "actions/delete_trip.php?id=" . $row['trip_id'];
            ?>
            <tr>
                <td><?php echo $row['trip_id']; ?></td>
                <td><?php echo $row['driver_name']; ?></td>
                <td><?php echo $row['vehicle_type']; ?></td>
                <td><?php echo $row['plate_no']; ?></td>
                <td><?php echo $row['origin']; ?></td>
                <td><?php echo $row['destination']; ?></td>
                <td><?php echo $row['departure']; ?></td>
                <td><?php echo $row['arrival']; ?></td>
                <td><span class='status-text'><?php echo $row['trip_status']; ?></span></td>
                
                <td class='action-cell'>
                    <button class='action-icon view-btn' onclick="openModal('view-details-template', 'Trip #<?php echo $row['trip_id']; ?> Details', JSON.parse('<?php echo $safeJson; ?>'))" title='View Details'>
                        <svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#e3e3e3'><path d='M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z'/></svg>
                    </button>
                    <button class='action-icon edit-btn' onclick="openModal('trip-form-template', 'Edit Trip #<?php echo $row['trip_id']; ?>', JSON.parse('<?php echo $safeJson; ?>'))" title='Edit'>
                        <svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#e3e3e3'><path d='M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h357l-80 80H200v560h560v-278l80-80v358q0 33-23.5 56.5T760-120H200Zm280-360ZM360-360v-170l367-367q12-12 27-18t30-6q16 0 30.5 6t26.5 18l56 57q11 12 17 26.5t6 29.5q0 15-5.5 29.5T897-728L530-360H360Zm481-424-56-56 56 56ZM440-440h56l232-232-28-28-29-28-231 231v57Zm260-260-29-28 29 28 28 28-28-28Z'/></svg>
                    </button>
                    <button class='action-icon delete-btn' onclick="openModal('delete-template', 'Confirm Delete', null, '<?php echo $deleteUrl; ?>')" title='Delete'>
                        <svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#e3e3e3'><path d='M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z'/></svg>
                    </button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

