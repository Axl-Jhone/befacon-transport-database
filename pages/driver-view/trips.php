<?php

   // PAGINATION LOGIC
    $current_route = isset($_GET['page']) ? $_GET['page'] : '';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $curr_page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    
    // Modal filter params
    $filter_driver_id = isset($_GET['filter_driver_id']) && $_GET['filter_driver_id'] !== '' ? (int)$_GET['filter_driver_id'] : 0;
    $filter_vehicle_type_id = isset($_GET['filter_vehicle_type_id']) && $_GET['filter_vehicle_type_id'] !== '' ? (int)$_GET['filter_vehicle_type_id'] : 0;
    $filter_trip_status_id = isset($_GET['filter_trip_status_id']) && $_GET['filter_trip_status_id'] !== '' ? (int)$_GET['filter_trip_status_id'] : 0;
    $filter_purpose_id = isset($_GET['filter_purpose_id']) && $_GET['filter_purpose_id'] !== '' ? (int)$_GET['filter_purpose_id'] : 0;
    $filter_origin = isset($_GET['filter_origin']) ? trim($_GET['filter_origin']) : '';
    $filter_destination = isset($_GET['filter_destination']) ? trim($_GET['filter_destination']) : '';
    $filter_departure = isset($_GET['filter_departure']) ? trim($_GET['filter_departure']) : '';
    $filter_arrival = isset($_GET['filter_arrival']) ? trim($_GET['filter_arrival']) : '';
    
    // Count active filters
    $activeFilters = 0;
    if ($filter_driver_id) $activeFilters++;
    if ($filter_vehicle_type_id) $activeFilters++;
    if ($filter_trip_status_id) $activeFilters++;
    if ($filter_purpose_id) $activeFilters++;
    if ($filter_origin !== '') $activeFilters++;
    if ($filter_destination !== '') $activeFilters++;
    if ($filter_departure !== '') $activeFilters++;
    if ($filter_arrival !== '') $activeFilters++;
    
    if ($curr_page < 1) $curr_page = 1;
    $offset = ($curr_page - 1) * $limit;

    // WHERE Clause
    $where = "";
    $clauses = [];

    $clauses[] = "t.driver_id = " . (int)$current_driver_id;

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

    if ($filter_driver_id) $clauses[] = "t.driver_id = $filter_driver_id";
    if ($filter_vehicle_type_id) $clauses[] = "vt.vehicle_type_id = $filter_vehicle_type_id";
    if ($filter_trip_status_id) $clauses[] = "t.trip_status_id = $filter_trip_status_id";
    if ($filter_purpose_id) $clauses[] = "t.purpose_id = $filter_purpose_id";
    if ($filter_origin !== '') { $esc = $conn->real_escape_string($filter_origin); $clauses[] = "t.origin LIKE '%$esc%'"; }
    if ($filter_destination !== '') { $esc = $conn->real_escape_string($filter_destination); $clauses[] = "t.destination LIKE '%$esc%'"; }
    if ($filter_departure !== '') { $esc = $conn->real_escape_string($filter_departure); $clauses[] = "t.sched_depart_datetime >= '$esc'"; }
    if ($filter_arrival !== '') { $esc = $conn->real_escape_string($filter_arrival); $clauses[] = "t.sched_arrival_datetime <= '$esc'"; }

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

    $current_driver_id = $_SESSION['driver_id'];

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
        <button class="filter" title="Filter" onclick="openModal('filter-search-template', 'Filter Trips')">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="#183a59" width="24" height="24">
                <path d="M440-160q-17 0-28.5-11.5T400-200v-240L168-736q-15-20-4.5-42t36.5-22h560q26 0 36.5 22t-4.5 42L560-440v240q0 17-11.5 28.5T520-160h-80Zm40-308 198-252H282l198 252Zm0 0Z"/>
            </svg>
            <?php if (!empty($activeFilters)): ?>
                <span class="filter-indicator"><?php echo $activeFilters; ?></span>
            <?php endif; ?>
        </button>

        <form id="searchForm" method="GET" action="">
            <input type="hidden" name="page" value="<?php echo htmlspecialchars($current_route); ?>">
            <input type="hidden" name="limit" id="limitInput" value="<?php echo $limit; ?>">
            <input type="hidden" name="p" id="pInput" value="<?php echo $curr_page; ?>">
            <input type="hidden" name="filter_driver_id" value="<?php echo htmlspecialchars($filter_driver_id); ?>">
            <input type="hidden" name="filter_vehicle_type_id" value="<?php echo htmlspecialchars($filter_vehicle_type_id); ?>">
            <input type="hidden" name="filter_trip_status_id" value="<?php echo htmlspecialchars($filter_trip_status_id); ?>">
            <input type="hidden" name="filter_purpose_id" value="<?php echo htmlspecialchars($filter_purpose_id); ?>">
            <input type="hidden" name="filter_origin" value="<?php echo htmlspecialchars($filter_origin); ?>">
            <input type="hidden" name="filter_destination" value="<?php echo htmlspecialchars($filter_destination); ?>">
            <input type="hidden" name="filter_departure" value="<?php echo htmlspecialchars($filter_departure); ?>">
            <input type="hidden" name="filter_arrival" value="<?php echo htmlspecialchars($filter_arrival); ?>">
            <input type="search" name="q" id="searchInput" class="search form-control" placeholder="Search..." value="<?php echo htmlspecialchars($q); ?>" maxlength="30">
        </form>
    </div>
    
    <div class="page-title">SCHEDULED TRIPS</div>
</div>

<!-- CONTENT TABLE -->
<div class="content-table">
    <table class="table-display">
        <colgroup></colgroup>
        <thead>
            <tr>
                <!-- CHANGED: "Trip ID" to "No." -->
                <th>Trip No.</th>
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
                <?php 
                    // 1. INIT COUNTER FOR VISUAL NUMBERING
                    $counter = 0;
                ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <?php 
                    // 2. CALCULATE DISPLAY NUMBER (Offset + current index)
                    $counter++;
                    $displayNum = $offset + $counter;

                    $modalData = [
                        'tripId'        => $row['trip_id'], // Keep real ID for logic
                        'vehicleTypeId' => $row['vehicle_type_id'],
                        'driverId'      => $row['driver_id'],
                        'vehicleId'     => $row['vehicle_id'],
                        'purposeId'     => $row['purpose_id'],
                        'statusId'      => $row['trip_status_id'],
                        'tripCostRaw'   => $row['trip_cost'], 
                        'departureRaw'  => date('Y-m-d\TH:i', strtotime($row['departure'])), 
                        'arrivalRaw'    => date('Y-m-d\TH:i', strtotime($row['arrival'])),
                        
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
                    <!-- 3. DISPLAY VISUAL NUMBER -->
                    <td><?php echo $displayNum; ?></td>
                    <td><?php echo $row['driver_name']; ?></td>
                    <td><?php echo $row['vehicle_type']; ?></td>
                    <td><?php echo $row['plate_no']; ?></td>
                    <td><?php echo $row['origin']; ?></td>
                    <td><?php echo $row['destination']; ?></td>
                    <td><?php echo $row['departure']; ?></td>
                    <td><?php echo $row['arrival']; ?></td>
                    <td><span class='status-text'><?php echo $row['trip_status']; ?></span></td>
                    
                    <td class='action-cell'>
                        <button class='action-icon view-btn' 
                                data-trip-info='<?php echo $safeJsonAttr; ?>'
                                onclick="openModal('view-details-template', 'Trip #<?php echo $row['trip_id']; ?> Details', JSON.parse(this.getAttribute('data-trip-info')))" 
                                title='View Details'>
                            <svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#e3e3e3'><path d='M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z'/></svg>
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
            <input type="hidden" name="filter_purpose_id" value="<?php echo htmlspecialchars($filter_purpose_id); ?>">
            <input type="hidden" name="filter_origin" value="<?php echo htmlspecialchars($filter_origin); ?>">
            <input type="hidden" name="filter_destination" value="<?php echo htmlspecialchars($filter_destination); ?>">
            <input type="hidden" name="filter_departure" value="<?php echo htmlspecialchars($filter_departure); ?>">
            <input type="hidden" name="filter_arrival" value="<?php echo htmlspecialchars($filter_arrival); ?>">
            
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
        <!-- CORRECTED LINKS FOR PAGINATION VARIABLES -->
        <a href="?page=<?php echo $current_route; ?>&p=<?php echo max(1, $curr_page-1); ?>&limit=<?php echo $limit; ?>&q=<?php echo urlencode($q); ?>&filter_driver_id=<?php echo urlencode($filter_driver_id); ?>&filter_vehicle_type_id=<?php echo urlencode($filter_vehicle_type_id); ?>&filter_trip_status_id=<?php echo urlencode($filter_trip_status_id); ?>&filter_purpose_id=<?php echo urlencode($filter_purpose_id); ?>" class="prev <?php echo ($curr_page <= 1) ? 'disabled' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="currentColor"><path d="M560-240 320-480l240-240 56 56-184 184 184 184-56 56Z"/></svg>
        </a>
        <?php 
        $adjacents = 1; 
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == 1 || $i == $total_pages || ($i >= $curr_page - $adjacents && $i <= $curr_page + $adjacents)) {
                echo '<a href="?page='.$current_route.'&p='.$i.'&limit='.$limit.'&q='.urlencode($q).'&filter_driver_id='.urlencode($filter_driver_id).'&filter_vehicle_type_id='.urlencode($filter_vehicle_type_id).'&filter_trip_status_id='.urlencode($filter_trip_status_id).'&filter_purpose_id='.urlencode($filter_purpose_id).'" class="'.(($curr_page == $i) ? 'active' : '').'">'.$i.'</a>';
            } elseif ($i == $curr_page - $adjacents - 1 || $i == $curr_page + $adjacents + 1) {
                echo '<span class="pagination-dots">...</span>';
            }
        }
        ?>
        <a href="?page=<?php echo $current_route; ?>&p=<?php echo min($total_pages, $curr_page+1); ?>&limit=<?php echo $limit; ?>&q=<?php echo urlencode($q); ?>&filter_driver_id=<?php echo urlencode($filter_driver_id); ?>&filter_vehicle_type_id=<?php echo urlencode($filter_vehicle_type_id); ?>&filter_trip_status_id=<?php echo urlencode($filter_trip_status_id); ?>&filter_purpose_id=<?php echo urlencode($filter_purpose_id); ?>" class="next <?php echo ($curr_page >= $total_pages) ? 'disabled' : ''; ?>">
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


<!-- FILTER SEARCH TEMPLATE (COMPLETE) -->
<template id="filter-search-template">
    <div class="filter-search-modal styled-form">
        <!-- <div class="form-group">
            <label>Driver</label>
            <select name="filter_driver_id">
                <option value="" <?php if(!$filter_driver_id) echo 'selected'; ?>>-- All Drivers --</option>
                <?php if($drivers_res) $drivers_res->data_seek(0); while($d = $drivers_res->fetch_assoc()): ?>
                    <option value="<?php echo $d['driver_id']; ?>" <?php if($filter_driver_id == $d['driver_id']) echo 'selected'; ?>><?php echo $d['full_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div> -->

        <div class="form-row-grid">
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
        </div>

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
                <input type="text" name="filter_origin" placeholder="Origin" value="<?php echo htmlspecialchars($filter_origin); ?>" maxlength="30">
            </div>
            <div class="form-group">
                <label>Destination</label>
                <input type="text" name="filter_destination" placeholder="Destination" value="<?php echo htmlspecialchars($filter_destination); ?>" maxlength="30">
            </div>
        </div>

        <div class="form-row-grid">
            <div class="form-group">
                <label>Departure (From)</label>
                <input type="datetime-local" name="filter_departure" value="<?php echo htmlspecialchars($filter_departure); ?>">
            </div>
            <div class="form-group">
                <label>Arrival (To)</label>
                <input type="datetime-local" name="filter_arrival" value="<?php echo htmlspecialchars($filter_arrival); ?>">
            </div>
        </div>

        <div class="modal-actions" style="margin-top: 20px;">
            <button type="button" class="btn-primary" onclick="(function(btn){
                const modal = btn.closest('.filter-search-modal');
                const params = new URLSearchParams(window.location.search);
                params.set('p', 1);
                const apply = (name) => {
                    const el = modal.querySelector(`[name='${name}']`);
                    if(el && el.value.trim()) params.set(name, el.value.trim());
                    else params.delete(name);
                };
                apply('filter_driver_id');
                apply('filter_vehicle_type_id');
                apply('filter_trip_status_id');
                apply('filter_purpose_id');
                apply('filter_origin');
                apply('filter_destination');
                apply('filter_departure');
                apply('filter_arrival');
                window.location = window.location.pathname + '?' + params.toString();
            })(this)">Apply Filters</button>
            
            <button type="button" class="btn-danger" onclick="(function(btn){
                const params = new URLSearchParams(window.location.search);
                const keys = [
                    'filter_driver_id', 'filter_vehicle_type_id', 'filter_trip_status_id', 'filter_purpose_id',
                    'filter_origin', 'filter_destination', 'filter_departure', 'filter_arrival'
                ];
                keys.forEach(k => params.delete(k));
                params.set('p', 1); 
                window.location = window.location.pathname + '?' + params.toString();
            })(this)">Clear Filters</button>

            <button type="button" onclick="closeModal()" class="btn-secondary">Cancel</button>
        </div>
    </div>
</template>
</body>
</html>