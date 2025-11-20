<?php
    // PAGINATION LOGIC
    $current_route = isset($_GET['page']) ? $_GET['page'] : '';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $curr_page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    // Modal filter params (driver, vehicle type, status) and extended filters
    $filter_driver_id = isset($_GET['filter_driver_id']) && $_GET['filter_driver_id'] !== '' ? (int)$_GET['filter_driver_id'] : 0;
    $filter_vehicle_type_id = isset($_GET['filter_vehicle_type_id']) && $_GET['filter_vehicle_type_id'] !== '' ? (int)$_GET['filter_vehicle_type_id'] : 0;
    $filter_trip_status_id = isset($_GET['filter_trip_status_id']) && $_GET['filter_trip_status_id'] !== '' ? (int)$_GET['filter_trip_status_id'] : 0;
    $filter_purpose_id = isset($_GET['filter_purpose_id']) && $_GET['filter_purpose_id'] !== '' ? (int)$_GET['filter_purpose_id'] : 0;
    $filter_origin = isset($_GET['filter_origin']) ? trim($_GET['filter_origin']) : '';
    $filter_destination = isset($_GET['filter_destination']) ? trim($_GET['filter_destination']) : '';
    $filter_departure = isset($_GET['filter_departure']) ? trim($_GET['filter_departure']) : '';
    $filter_arrival = isset($_GET['filter_arrival']) ? trim($_GET['filter_arrival']) : '';
    if ($curr_page < 1) $curr_page = 1;
    if ($curr_page < 1) $curr_page = 1;
    $offset = ($curr_page - 1) * $limit;

    // Prepare a WHERE clause when a search term or modal filters are provided
    $where = "";
    $clauses = [];

    if ($q !== '') {
        $qEsc = $conn->real_escape_string($q);
        $clauses[] = "(
            t.trip_id LIKE '%$qEsc%' OR 
            CONCAT_WS(' ', d.driver_fname, COALESCE(d.driver_mi, ''), d.driver_lname) LIKE '%$qEsc%' OR 
            v.plate_no LIKE '%$qEsc%' OR 
            vt.vehicle_type LIKE '%$qEsc%' OR 
            t.origin LIKE '%$qEsc%' OR 
            t.destination LIKE '%$qEsc%' OR 
            td.trip_status LIKE '%$qEsc%' OR 
            p.purpose LIKE '%$qEsc%'
        )";
    }

    if ($filter_driver_id) {
        $clauses[] = "t.driver_id = $filter_driver_id";
    }
    if ($filter_vehicle_type_id) {
        $clauses[] = "vt.vehicle_type_id = $filter_vehicle_type_id";
    }
    if ($filter_trip_status_id) {
        $clauses[] = "t.trip_status_id = $filter_trip_status_id";
    }
    if ($filter_purpose_id) {
        $clauses[] = "t.purpose_id = $filter_purpose_id";
    }
    if ($filter_origin !== '') {
        $originEsc = $conn->real_escape_string($filter_origin);
        $clauses[] = "t.origin LIKE '%$originEsc%'";
    }
    if ($filter_destination !== '') {
        $destEsc = $conn->real_escape_string($filter_destination);
        $clauses[] = "t.destination LIKE '%$destEsc%'";
    }
    if ($filter_departure !== '') {
        // expect YYYY-MM-DD or datetime; compare against sched_depart_datetime
        $fromEsc = $conn->real_escape_string($filter_departure);
        $clauses[] = "t.sched_depart_datetime >= '$fromEsc'";
    }
    if ($filter_arrival !== '') {
        $toEsc = $conn->real_escape_string($filter_arrival);
        $clauses[] = "t.sched_arrival_datetime <= '$toEsc'";
    }

    if (!empty($clauses)) {
        $where = ' WHERE ' . implode(' AND ', $clauses);
    }

    $count_sql = "
        SELECT COUNT(*) as total
        FROM trip_info t
        JOIN driver_info d ON t.driver_id = d.driver_id
        JOIN vehicle_info v ON t.vehicle_id = v.vehicle_id
        JOIN vehicle_type_data vt ON v.vehicle_type_id = vt.vehicle_type_id
        JOIN trip_status_data td ON t.trip_status_id = td.trip_status_id
        JOIN purpose_data p ON t.purpose_id = p.purpose_id
    " . $where;
    $count_result = $conn->query($count_sql);
    $count_row = $count_result->fetch_assoc();
    $total_rows = $count_row['total'];
    $total_pages = ceil($total_rows / $limit);
    $start_entry = ($total_rows > 0) ? $offset + 1 : 0;
    $end_entry   = min($offset + $limit, $total_rows);

    // DROPDOWN DATA
    $drivers_res  = $conn->query("SELECT driver_id, CONCAT(driver_fname, ' ', driver_lname) AS full_name FROM driver_info WHERE driver_status_id = 1");
    $types_res    = $conn->query("SELECT vehicle_type_id, vehicle_type FROM vehicle_type_data");
    $vehicles_res = $conn->query("SELECT vehicle_id, vehicle_type_id, plate_no FROM vehicle_info WHERE vehicle_status_id = 1");
    $purposes_res = $conn->query("SELECT purpose_id, purpose FROM purpose_data");
    $status_res   = $conn->query("SELECT trip_status_id, trip_status FROM trip_status_data");

    // DATA SELECTION QUERY
    $data = "
        SELECT
            t.trip_id AS trip_id,
            t.driver_id, t.vehicle_id, t.purpose_id, t.trip_status_id,
            vt.vehicle_type_id,
            CONCAT_WS (' ', d.driver_fname, CASE WHEN d.driver_mi IS NULL OR d.driver_mi = '' THEN NULL ELSE CONCAT (d.driver_mi, '.') END, d.driver_lname) AS driver_name, 
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
    " . $where . "
        ORDER BY t.trip_id ASC
        LIMIT $limit OFFSET $offset 
    ";

    $result = $conn->query($data);
    if (!$result) { die ("Invalid query: " . $conn->error); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduled Trips</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
    $(document).ready(function(){
        // Debounced submit for server-side search. Resets page to 1 on new search.
        var searchTimer;
        $('#searchInput').on('input', function(){
            clearTimeout(searchTimer);
            $('#pInput').val(1);
            searchTimer = setTimeout(function(){
                $('#searchForm').submit();
            }, 500);
        });
    });
    </script>
</head>
<body>

<!-- CONTENT HEADER -->
<div class="content-header">
    <div class="filter-search">
        <button class="filter" title="Filter">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="#183a59" width="24" height="24" onclick="openModal('filter-search-template', 'Filter Trips')">
                <path d="M440-160q-17 0-28.5-11.5T400-200v-240L168-736q-15-20-4.5-42t36.5-22h560q26 0 36.5 22t-4.5 42L560-440v240q0 17-11.5 28.5T520-160h-80Zm40-308 198-252H282l198 252Zm0 0Z"/>
            </svg>
        </button>

        <form id="searchForm" method="GET" action="">
            <input type="hidden" name="page" value="<?php echo htmlspecialchars($current_route); ?>">
            <input type="hidden" name="limit" id="limitInput" value="<?php echo $limit; ?>">
            <input type="hidden" name="p" id="pInput" value="<?php echo $curr_page; ?>">
            <!-- Preserve active filters when performing a search -->
            <input type="hidden" name="filter_driver_id" value="<?php echo htmlspecialchars($filter_driver_id); ?>">
            <input type="hidden" name="filter_vehicle_type_id" value="<?php echo htmlspecialchars($filter_vehicle_type_id); ?>">
            <input type="hidden" name="filter_trip_status_id" value="<?php echo htmlspecialchars($filter_trip_status_id); ?>">
            <input type="hidden" name="filter_purpose_id" value="<?php echo htmlspecialchars($filter_purpose_id); ?>">
            <input type="hidden" name="filter_origin" value="<?php echo htmlspecialchars($filter_origin); ?>">
            <input type="hidden" name="filter_destination" value="<?php echo htmlspecialchars($filter_destination); ?>">
            <input type="hidden" name="filter_departure" value="<?php echo htmlspecialchars($filter_departure); ?>">
            <input type="hidden" name="filter_arrival" value="<?php echo htmlspecialchars($filter_arrival); ?>">
            <input type="search" name="q" id="searchInput" class="search form-control" placeholder="Search..." value="<?php echo htmlspecialchars($q); ?>">
        </form>
    </div>
    
    <div class="page-title">SCHEDULED TRIPS</div>
    
    <button class="add-button" onclick="openModal('trip-form-template', 'Add New Trip')">
        + Add Trip
    </button>
</div>

<!-- CONTENT TABLE -->
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
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <?php 
                    $modalData = [
                        // Edit Mode IDs
                        'tripId'        => $row['trip_id'],
                        'vehicleTypeId' => $row['vehicle_type_id'],
                        'driverId'      => $row['driver_id'],
                        'vehicleId'     => $row['vehicle_id'],
                        'purposeId'     => $row['purpose_id'],
                        'statusId'      => $row['trip_status_id'],
                        'tripCostRaw'   => $row['trip_cost'], 
                        'departureRaw'  => date('Y-m-d\TH:i', strtotime($row['departure'])), 
                        'arrivalRaw'    => date('Y-m-d\TH:i', strtotime($row['arrival'])),
                        
                        // View Mode Data
                        'driverName'    => $row['driver_name'],
                        'contact'       => $row['contact'],
                        'vehicleType'   => $row['vehicle_type'],
                        'plateNo'       => $row['plate_no'],
                        'origin'        => $row['origin'],
                        'destination'   => $row['destination'],
                        'route'         => $row['origin'] . ' ➝ ' . $row['destination'], 
                        'departure'     => date("M j, Y g:i A", strtotime($row['departure'])), 
                        'arrival'       => date("M j, Y g:i A", strtotime($row['arrival'])),
                        'status'        => $row['trip_status'],
                        'purpose'       => $row['purpose'],
                        'totalCost'     => '₱' . number_format($row['trip_cost'], 2)
                    ];

                    $jsonStr = json_encode($modalData);
                    $safeJsonAttr = htmlspecialchars($jsonStr, ENT_QUOTES, 'UTF-8');
                    
                    $deleteUrl = "../actions/delete_trip.php?id=" . $row['trip_id'];
                ?>

                <tr>
                    <!-- DATA COLUMNS -->
                    <td><?php echo $row['trip_id']; ?></td>
                    <td><?php echo $row['driver_name']; ?></td>
                    <td><?php echo $row['vehicle_type']; ?></td>
                    <td><?php echo $row['plate_no']; ?></td>
                    <td><?php echo $row['origin']; ?></td>
                    <td><?php echo $row['destination']; ?></td>
                    <td><?php echo $row['departure']; ?></td>
                    <td><?php echo $row['arrival']; ?></td>
                    <td><span class='status-text'><?php echo $row['trip_status']; ?></span></td>
                    
                    <!-- ACTIONS COLUMN -->
                    <td class='action-cell'>
                        <button class='action-icon view-btn' 
                                data-trip-info='<?php echo $safeJsonAttr; ?>'
                                onclick="openModal('view-details-template', 'Trip #<?php echo $row['trip_id']; ?> Details', JSON.parse(this.getAttribute('data-trip-info')))" 
                                title='View Details'>
                            <svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#e3e3e3'><path d='M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z'/></svg>
                        </button>
                        <button class='action-icon edit-btn' 
                                data-trip-info='<?php echo $safeJsonAttr; ?>'
                                onclick="openModal('trip-form-template', 'Edit Trip #<?php echo $row['trip_id']; ?>', JSON.parse(this.getAttribute('data-trip-info')))" 
                                title='Edit'>
                            <svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#e3e3e3'><path d='M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h357l-80 80H200v560h560v-278l80-80v358q0 33-23.5 56.5T760-120H200Zm280-360ZM360-360v-170l367-367q12-12 27-18t30-6q16 0 30.5 6t26.5 18l56 57q11 12 17 26.5t6 29.5q0 15-5.5 29.5T897-728L530-360H360Zm481-424-56-56 56 56ZM440-440h56l232-232-28-28-29-28-231 231v57Zm260-260-29-28 29 28 28 28-28-28Z'/></svg>
                        </button>
                        <button class='action-icon delete-btn' 
                                onclick="openModal('delete-template', 'Confirm Delete', null, '<?php echo $deleteUrl; ?>')" 
                                title='Delete'>
                            <svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#e3e3e3'><path d='M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z'/></svg>
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" style="text-align:center; color:#777; padding:18px; font-weight: bold;">No Data Found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- PAGINATION DISPLAY -->
<div class="content-footer">
    <div class="entries-container">
        <form method="GET" class="entries-form">
            <input type="hidden" name="page" value="<?php echo htmlspecialchars($current_route); ?>">
            <input type="hidden" name="q" value="<?php echo htmlspecialchars($q); ?>">
            <input type="hidden" name="filter_driver_id" value="<?php echo htmlspecialchars($filter_driver_id); ?>">
            <input type="hidden" name="filter_vehicle_type_id" value="<?php echo htmlspecialchars($filter_vehicle_type_id); ?>">
            <input type="hidden" name="filter_trip_status_id" value="<?php echo htmlspecialchars($filter_trip_status_id); ?>">
            <label>Show</label>
            <select name="limit" onchange="this.form.submit()">
                <option value="5"  <?php if($limit == 5) echo 'selected'; ?>>5</option>
                <option value="10" <?php if($limit == 10) echo 'selected'; ?>>10</option>
                <option value="15" <?php if($limit == 15) echo 'selected'; ?>>15</option>
                <option value="20" <?php if($limit == 20) echo 'selected'; ?>>20</option>
            </select>
            <span>entries</span>
        </form>

        <div class="entries-info">
            Showing <span><?php echo $start_entry; ?></span> 
            to <span><?php echo $end_entry; ?></span> 
            of <span><?php echo $total_rows; ?></span> entries
        </div>
    </div>

    <div class="pagination">
        <a href="?page=<?php echo $current_route; ?>&p=<?php echo max(1, $curr_page-1); ?>&limit=<?php echo $limit; ?>&q=<?php echo urlencode($q); ?>&filter_driver_id=<?php echo urlencode($filter_driver_id); ?>&filter_vehicle_type_id=<?php echo urlencode($filter_vehicle_type_id); ?>&filter_trip_status_id=<?php echo urlencode($filter_trip_status_id); ?>" class="prev <?php echo ($curr_page <= 1) ? 'disabled' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="currentColor"><path d="M560-240 320-480l240-240 56 56-184 184 184 184-56 56Z"/></svg>
        </a>
        <?php 
        $adjacents = 1; 
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == 1 || $i == $total_pages || ($i >= $curr_page - $adjacents && $i <= $curr_page + $adjacents)) {
                echo '<a href="?page='.$current_route.'&p='.$i.'&limit='.$limit.'&q='.urlencode($q).'&filter_driver_id='.urlencode($filter_driver_id).'&filter_vehicle_type_id='.urlencode($filter_vehicle_type_id).'&filter_trip_status_id='.urlencode($filter_trip_status_id).'" class="'.(($curr_page == $i) ? 'active' : '').'">'.$i.'</a>';
            } elseif ($i == $curr_page - $adjacents - 1 || $i == $curr_page + $adjacents + 1) {
                echo '<span class="pagination-dots">...</span>';
            }
        }
        ?>
        <a href="?page=<?php echo $current_route; ?>&p=<?php echo min($total_pages, $curr_page+1); ?>&limit=<?php echo $limit; ?>&q=<?php echo urlencode($q); ?>&filter_driver_id=<?php echo urlencode($filter_driver_id); ?>&filter_vehicle_type_id=<?php echo urlencode($filter_vehicle_type_id); ?>&filter_trip_status_id=<?php echo urlencode($filter_trip_status_id); ?>" class="next <?php echo ($curr_page >= $total_pages) ? 'disabled' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="currentColor"><path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/></svg>
        </a>
    </div>
</div>

<!-- MODAL SHELL -->
<div id="universal-modal" class="modal-overlay hidden">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modal-title">Title</h3>
            <button onclick="closeModal()" class="close-btn">&times;</button>
        </div>
        <div id="modal-body"></div>
    </div>
</div>

<!-- TEMPLATES -->
<!-- VIEW DETAILS TEMPLATE -->
<template id="view-details-template">
    <div class="trip-details-grid" style="padding: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        
        <div class="detail-section" style="border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <h4 style="margin: 0 0 10px 0; color: #183a59;">Driver Information</h4>
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Name:</span>
                <span class="value" data-key="driverName"></span>
            </div>
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Contact:</span>
                <span class="value" data-key="contact"></span>
            </div>
        </div>

        <div class="detail-section" style="border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <h4 style="margin: 0 0 10px 0; color: #183a59;">Vehicle Information</h4>
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Type:</span>
                <span class="value" data-key="vehicleType"></span>
            </div>
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Plate No:</span>
                <span class="value" data-key="plateNo"></span>
            </div>
        </div>

        <div class="detail-section full-width" style="border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <h4 style="margin: 0 0 10px 0; color: #183a59;">Trip Schedule</h4>
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Route:</span>
                <span class="value" data-key="route"></span>
            </div>
            
            <div class="schedule-grid" style="display: flex; gap: 20px; margin-top: 10px;">
                <div>
                    <span class="label small-label" style="font-size: 0.85em; color: #888;">Departure:</span><br>
                    <strong class="value" data-key="departure"></strong>
                </div>
                <div>
                    <span class="label small-label" style="font-size: 0.85em; color: #888;">Arrival:</span><br>
                    <strong class="value" data-key="arrival"></strong>
                </div>
            </div>
        </div>

        <div class="detail-section full-width financial-section">
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Status:</span>
                <span class="value badge" data-key="status"></span>
            </div>
            
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Purpose:</span>
                <span class="value" data-key="purpose"></span>
            </div>

            <div class="detail-row cost-row" style="margin-top: 10px; font-size: 1.1em;">
                <span class="label" style="font-weight:bold; color:#666;">Total Cost:</span>
                <span class="value cost-highlight" data-key="totalCost" style="color: #28a745; font-weight: bold;"></span>
            </div>
        </div>

    </div>

    <div class="modal-footer" style="padding: 15px; text-align: right; border-top: 1px solid #eee;">
        <button type="button" class="btn-secondary" onclick="closeModal()">Close</button>
    </div>
</template>

<!-- ADD/EDIT FORM TEMPLATE -->
<template id="trip-form-template">
    <form action="../actions/save_trip.php" method="POST" class="styled-form" onsubmit="return validateDates(event)">
        <input type="hidden" name="trip_id" data-key="tripId">
        <div class="form-group">
            <label>Driver</label>
            <select name="driver_id" data-key="driverId" required>
                <option value="" disabled selected>-- Select Driver --</option>
                <?php if($drivers_res) $drivers_res->data_seek(0); while($d = $drivers_res->fetch_assoc()): ?>
                    <option value="<?php echo $d['driver_id']; ?>"><?php echo $d['full_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-row-grid">
            <div class="form-group">
                <label>Vehicle Type</label>
                <select id="type-select" name="vehicle_type_id" data-key="vehicleTypeId" onchange="filterPlates()" required>
                    <option value="" disabled selected>-- Select Type --</option>
                    <?php if($types_res) $types_res->data_seek(0); while($t = $types_res->fetch_assoc()): ?>
                        <option value="<?php echo $t['vehicle_type_id']; ?>"><?php echo $t['vehicle_type']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Plate Number</label>
                <select id="plate-select" name="vehicle_id" data-key="vehicleId" disabled required>
                    <option value="">Select Type First</option>
                    <?php if($vehicles_res) $vehicles_res->data_seek(0); while($v = $vehicles_res->fetch_assoc()): ?>
                        <option value="<?php echo $v['vehicle_id']; ?>" data-type-id="<?php echo $v['vehicle_type_id']; ?>">
                            <?php echo $v['plate_no']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <div class="form-row-grid">
            <div class="form-group"><label>Origin</label><input type="text" name="origin" data-key="origin" required></div>
            <div class="form-group"><label>Destination</label><input type="text" name="destination" data-key="destination" required></div>
        </div>
        <div class="form-row-grid">
            <div class="form-group"><label>Departure</label><input type="datetime-local" name="sched_depart_datetime" data-key="departureRaw" required></div>
            <div class="form-group"><label>Arrival</label><input type="datetime-local" name="sched_arrival_datetime" data-key="arrivalRaw" required></div>
        </div>
        <div class="form-row-grid">
            <div class="form-group">
                <label>Purpose</label>
                <select name="purpose_id" data-key="purposeId" required>
                    <?php if($purposes_res) $purposes_res->data_seek(0); while($p = $purposes_res->fetch_assoc()): ?>
                        <option value="<?php echo $p['purpose_id']; ?>"><?php echo $p['purpose']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="trip_status_id" data-key="statusId" required>
                    <?php if($status_res) $status_res->data_seek(0); while($s = $status_res->fetch_assoc()): ?>
                        <option value="<?php echo $s['trip_status_id']; ?>"><?php echo $s['trip_status']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <div class="form-group"><label>Cost</label><input type="number" name="trip_cost" data-key="tripCostRaw" step="0.01"></div>
        <div class="modal-actions">
            <button type="button" onclick="closeModal()" class="btn-secondary">Cancel</button>
            <button type="submit" class="btn-save">Save Trip</button>
        </div>
    </form>
</template>

<!-- DELETE TEMPLATE -->
<template id="delete-template">
    <div class="delete-warning-box">
        <div class="warning-icon">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="m40-120 440-760 440 760H40Zm138-80h604L480-720 178-200Zm302-40q17 0 28.5-11.5T520-280q0-17-11.5-28.5T480-320q-17 0-28.5 11.5T440-280q0 17 11.5 28.5T480-240Zm-40-120h80v-200h-80v200Zm40-100Z"/></svg>
        </div>
        <h3>Confirm Delete</h3>
        <p class="warning-text">Are you sure? This cannot be undone.</p>
        <div class="delete-actions">
            <button onclick="closeModal()" class="btn-secondary">Cancel</button>
            <a id="confirm-delete-btn" href="#" class="btn-danger">Delete</a>
        </div>
    </div>
</template>

<!-- FILTER SEARCH TEMPLATE -->
<template id="filter-search-template">
    <div class="filter-search-modal styled-form">
        <!-- <div class="form-row-grid"> -->
            <div class="form-group">
                <label>Driver</label>
                <select name="filter_driver_id">
                    <option value="" <?php if(!$filter_driver_id) echo 'selected'; ?>>-- All Drivers --</option>
                    <?php if($drivers_res) $drivers_res->data_seek(0); while($d = $drivers_res->fetch_assoc()): ?>
                        <option value="<?php echo $d['driver_id']; ?>" <?php if($filter_driver_id == $d['driver_id']) echo 'selected'; ?>><?php echo $d['full_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Vehicle Type</label>
                <select name="filter_vehicle_type_id">
                    <option value="" <?php if(!$filter_vehicle_type_id) echo 'selected'; ?>>-- All Types --</option>
                    <?php if($types_res) $types_res->data_seek(0); while($t = $types_res->fetch_assoc()): ?>
                        <option value="<?php echo $t['vehicle_type_id']; ?>" <?php if($filter_vehicle_type_id == $t['vehicle_type_id']) echo 'selected'; ?>><?php echo $t['vehicle_type']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="filter_trip_status_id">
                    <option value="" <?php if(!$filter_trip_status_id) echo 'selected'; ?>>-- All Statuses --</option>
                    <?php if($status_res) $status_res->data_seek(0); while($s = $status_res->fetch_assoc()): ?>
                        <option value="<?php echo $s['trip_status_id']; ?>" <?php if($filter_trip_status_id == $s['trip_status_id']) echo 'selected'; ?>><?php echo $s['trip_status']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        <!-- </div> -->
            <div class="form-group">
                <label>Purpose</label>
                <select name="filter_purpose_id">
                    <option value="" <?php if(!$filter_purpose_id) echo 'selected'; ?>>-- All Purposes --</option>
                    <?php if($purposes_res) $purposes_res->data_seek(0); while($p = $purposes_res->fetch_assoc()): ?>
                        <option value="<?php echo $p['purpose_id']; ?>" <?php if($filter_purpose_id == $p['purpose_id']) echo 'selected'; ?>><?php echo $p['purpose']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

        <div class="form-row-grid">
            <div class="form-group">
                <label>Origin</label>
                <input type="text" name="filter_origin" placeholder="Origin" value="<?php echo htmlspecialchars($filter_origin); ?>">
            </div>

            <div class="form-group">
                <label>Destination</label>
                <input type="text" name="filter_destination" placeholder="Destination" value="<?php echo htmlspecialchars($filter_destination); ?>">
            </div>
        </div>

        <div class="form-row-grid">
            <div class="form-group">
                <label>Departure</label>
                <input type="datetime-local" name="filter_departure" value="<?php echo htmlspecialchars($filter_departure); ?>">
            </div>
            <div class="form-group">
                <label>Arrival</label>
                <input type="datetime-local" name="filter_arrival" value="<?php echo htmlspecialchars($filter_arrival); ?>">
            </div>
        </div>

        <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:8px;">
            <button type="button" class="btn-secondary" onclick="(function(btn){
                const modal = btn.closest('.filter-search-modal');
                const params = new URLSearchParams(window.location.search);
                params.set('p', 1);
                const dv = modal.querySelector('select[name=\'filter_driver_id\']').value;
                const tv = modal.querySelector('select[name=\'filter_vehicle_type_id\']').value;
                const sv = modal.querySelector('select[name=\'filter_trip_status_id\']').value;
                const pv = modal.querySelector('select[name=\'filter_purpose_id\']').value;
                const ov = modal.querySelector('input[name=\'filter_origin\']').value.trim();
                const dvn = modal.querySelector('input[name=\'filter_destination\']').value.trim();
                const ff = modal.querySelector('input[name=\'filter_departure\']').value;
                const ft = modal.querySelector('input[name=\'filter_arrival\']').value;
                if (dv) params.set('filter_driver_id', dv); else params.delete('filter_driver_id');
                if (tv) params.set('filter_vehicle_type_id', tv); else params.delete('filter_vehicle_type_id');
                if (sv) params.set('filter_trip_status_id', sv); else params.delete('filter_trip_status_id');
                if (pv) params.set('filter_purpose_id', pv); else params.delete('filter_purpose_id');
                if (ov) params.set('filter_origin', ov); else params.delete('filter_origin');
                if (dvn) params.set('filter_destination', dvn); else params.delete('filter_destination');
                if (ff) params.set('filter_departure', ff); else params.delete('filter_departure');
                if (ft) params.set('filter_arrival', ft); else params.delete('filter_arrival');
                window.location = window.location.pathname + '?' + params.toString();
            })(this)">Apply</button>

            <button type="button" class="btn-secondary" onclick="(function(btn){
                const params = new URLSearchParams(window.location.search);
                params.delete('filter_driver_id');
                params.delete('filter_vehicle_type_id');
                params.delete('filter_trip_status_id');
                params.delete('filter_purpose_id');
                params.delete('filter_origin');
                params.delete('filter_destination');
                params.delete('filter_departure');
                params.delete('filter_arrival');
                params.set('p', 1);
                window.location = window.location.pathname + '?' + params.toString();
            })(this)">Clear</button>

            <button type="button" onclick="closeModal()" class="btn-secondary">Cancel</button>
        </div>
    </div>
</template>

</body>
</html>