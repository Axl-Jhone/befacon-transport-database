<?php
    // --- PAGINATION LOGIC ---
    $current_route = isset($_GET['page']) ? $_GET['page'] : '';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $curr_page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';

    // --- VEHICLE-SPECIFIC FILTER PARAMS ---
    $filter_vehicle_type_id = isset($_GET['filter_vehicle_type_id']) && $_GET['filter_vehicle_type_id'] !== '' ? (int)$_GET['filter_vehicle_type_id'] : 0;
    $filter_vehicle_status_id = isset($_GET['filter_vehicle_status_id']) && $_GET['filter_vehicle_status_id'] !== '' ? (int)$_GET['filter_vehicle_status_id'] : 0;
    $filter_condition_id = isset($_GET['filter_condition_id']) && $_GET['filter_condition_id'] !== '' ? (int)$_GET['filter_condition_id'] : 0;
    
    // NEW FILTERS
    $filter_access_id = isset($_GET['filter_access_id']) && $_GET['filter_access_id'] !== '' ? (int)$_GET['filter_access_id'] : 0;
    $filter_license_type_id = isset($_GET['filter_license_type_id']) && $_GET['filter_license_type_id'] !== '' ? (int)$_GET['filter_license_type_id'] : 0;
    $filter_current_location = isset($_GET['filter_current_location']) ? trim($_GET['filter_current_location']) : '';

    // Count active filters for UI indicator
    $activeFilters = 0;
    if ($filter_vehicle_type_id) $activeFilters++;
    if ($filter_vehicle_status_id) $activeFilters++;
    if ($filter_condition_id) $activeFilters++;
    if ($filter_access_id) $activeFilters++;
    if ($filter_license_type_id) $activeFilters++;
    if ($filter_current_location !== '') $activeFilters++;

    if ($curr_page < 1) $curr_page = 1;
    $offset = ($curr_page - 1) * $limit;

    // --- WHERE CLAUSE CONSTRUCTION ---
    $where = "";
    $clauses = [];

    if ($q !== '') {
        $qEsc = $conn->real_escape_string($q);
        $clauses[] = "(
            v.plate_no LIKE '%$qEsc%' OR 
            v.current_location LIKE '%$qEsc%' OR 
            vt.vehicle_type LIKE '%$qEsc%' OR 
            vc.vehicle_condition LIKE '%$qEsc%' OR 
            a.access_type LIKE '%$qEsc%' OR 
            l.license_type LIKE '%$qEsc%' OR 
            vs.vehicle_status LIKE '%$qEsc%'
        )";
    }

    if ($filter_vehicle_type_id) {
        $clauses[] = "v.vehicle_type_id = $filter_vehicle_type_id";
    }
    if ($filter_vehicle_status_id) {
        $clauses[] = "v.vehicle_status_id = $filter_vehicle_status_id";
    }
    if ($filter_condition_id) {
        $clauses[] = "v.vehicle_condition_id = $filter_condition_id";
    }
    // New Clause Logic
    if ($filter_access_id) {
        $clauses[] = "v.access_id = $filter_access_id";
    }
    if ($filter_license_type_id) {
        $clauses[] = "v.license_type_id = $filter_license_type_id";
    }
    if ($filter_current_location !== '') {
        $locEsc = $conn->real_escape_string($filter_current_location);
        $clauses[] = "v.current_location LIKE '%$locEsc%'";
    }

    if (!empty($clauses)) {
        $where = ' WHERE ' . implode(' AND ', $clauses);
    }

    // --- COUNT QUERY ---
    $count_sql = "
        SELECT COUNT(*) as total
        FROM vehicle_info v
        JOIN vehicle_type_data vt ON v.vehicle_type_id = vt.vehicle_type_id
        JOIN vehicle_condition_data vc ON v.vehicle_condition_id = vc.vehicle_condition_id
        JOIN license_type_data l ON v.license_type_id = l.license_type_id
        JOIN access_data a ON v.access_id = a.access_id
        JOIN vehicle_status_data vs ON v.vehicle_status_id = vs.vehicle_status_id
    " . $where;
    $count_result = $conn->query($count_sql);
    $count_row = $count_result->fetch_assoc();
    $total_rows = $count_row['total'];
    $total_pages = ceil($total_rows / $limit);
    $start_entry = ($total_rows > 0) ? $offset + 1 : 0;
    $end_entry   = min($offset + $limit, $total_rows);

    // --- DROPDOWN DATA (For Forms & Filters) ---
    $types_res      = $conn->query("SELECT vehicle_type_id, vehicle_type FROM vehicle_type_data");
    $conditions_res = $conn->query("SELECT vehicle_condition_id, vehicle_condition FROM vehicle_condition_data");
    $access_res     = $conn->query("SELECT access_id, access_type FROM access_data");
    $license_res    = $conn->query("SELECT license_type_id, license_type FROM license_type_data");
    $statuses_res   = $conn->query("SELECT vehicle_status_id, vehicle_status FROM vehicle_status_data");
    
    // --- DATA SELECTION QUERY ---
    $data = "
        SELECT
            v.vehicle_id,
            v.plate_no,
            v.current_location,
            v.vehicle_type_id,
            v.vehicle_condition_id,
            v.access_id,
            v.license_type_id,
            v.vehicle_status_id,
            vt.vehicle_type,
            vc.vehicle_condition,
            a.access_type,
            l.license_type,
            vs.vehicle_status
        FROM vehicle_info v
        JOIN vehicle_type_data vt ON v.vehicle_type_id = vt.vehicle_type_id
        JOIN vehicle_condition_data vc ON v.vehicle_condition_id = vc.vehicle_condition_id
        JOIN license_type_data l ON v.license_type_id = l.license_type_id
        JOIN access_data a ON v.access_id = a.access_id
        JOIN vehicle_status_data vs ON v.vehicle_status_id = vs.vehicle_status_id   
    " . $where . "
        ORDER BY v.vehicle_id ASC
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
    <title>Vehicles</title>

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
        <button class="filter" title="Filter" onclick="openModal('filter-vehicle-template', 'Filter Vehicles')">
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
            
            <!-- Hidden Filters to preserve state during search -->
            <input type="hidden" name="filter_vehicle_type_id" value="<?php echo htmlspecialchars($filter_vehicle_type_id); ?>">
            <input type="hidden" name="filter_vehicle_status_id" value="<?php echo htmlspecialchars($filter_vehicle_status_id); ?>">
            <input type="hidden" name="filter_condition_id" value="<?php echo htmlspecialchars($filter_condition_id); ?>">
            <input type="hidden" name="filter_access_id" value="<?php echo htmlspecialchars($filter_access_id); ?>">
            <input type="hidden" name="filter_license_type_id" value="<?php echo htmlspecialchars($filter_license_type_id); ?>">
            <input type="hidden" name="filter_current_location" value="<?php echo htmlspecialchars($filter_current_location); ?>">
            
            <input type="search" name="q" id="searchInput" class="search form-control" placeholder="Search vehicles..." value="<?php echo htmlspecialchars($q); ?>">
        </form>
    </div>
    
    <div class="page-title">VEHICLES LIST</div>
    
    <button class="add-button" onclick="openModal('vehicle-form-template', 'Add New Vehicle')">
        + Add Vehicle
    </button>
</div>

<div class="content-table">
    <table class="table-display">
        <colgroup></colgroup>
        <thead>
            <tr>
                <th>No.</th>
                <th>Vehicle Type</th>
                <th>Plate No.</th>
                <th>Access Type</th>
                <th>License Required</th>
                <th>Condition</th>
                <th>Current Location</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php 
                    $counter = 0;
                ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <?php 
                    $counter++;
                    $displayNum = $offset + $counter;

                    $modalData = [
                        'vehicleId'          => $row['vehicle_id'],
                        'vehicleTypeId'      => $row['vehicle_type_id'],
                        'vehicleConditionId' => $row['vehicle_condition_id'],
                        'accessId'           => $row['access_id'],
                        'licenseTypeId'      => $row['license_type_id'],
                        'vehicleStatusId'    => $row['vehicle_status_id'],
                        
                        'plateNo'            => $row['plate_no'],
                        'currentLocation'    => $row['current_location'],
                        'vehicleType'        => $row['vehicle_type'],
                        'vehicleCondition'   => $row['vehicle_condition'],
                        'accessType'         => $row['access_type'],
                        'licenseType'        => $row['license_type'],
                        'status'             => $row['vehicle_status'],
                    ];

                    $jsonStr = json_encode($modalData);
                    $safeJsonAttr = htmlspecialchars($jsonStr, ENT_QUOTES, 'UTF-8');
                    $deleteUrl = "../actions/delete_vehicle.php?id=" . $row['vehicle_id'];
                ?>

                <tr>
                    <td><?php echo $displayNum; ?></td>
                    <td><?php echo $row['vehicle_type']; ?></td>
                    <td><?php echo $row['plate_no']; ?></td>
                    <td><?php echo $row['access_type']; ?></td>
                    <td><?php echo $row['license_type']; ?></td>
                    <td><?php echo $row['vehicle_condition']; ?></td>
                    <td><?php echo $row['current_location']; ?></td>
                    <td><span class='status-text'><?php echo $row['vehicle_status']; ?></span></td>
                    
                    <td class='action-cell'>
                        <button class='action-icon view-btn' 
                                data-vehicle-info='<?php echo $safeJsonAttr; ?>'
                                onclick="openModal('view-details-template', 'Vehicle #<?php echo $row['vehicle_id']; ?> Details', JSON.parse(this.getAttribute('data-vehicle-info')))" 
                                title='View Details'>
                            <svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#e3e3e3'><path d='M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z'/></svg>
                        </button>
                        <button class='action-icon edit-btn' 
                                data-vehicle-info='<?php echo $safeJsonAttr; ?>'
                                onclick="openModal('vehicle-form-template', 'Edit Vehicle #<?php echo $row['vehicle_id']; ?>', JSON.parse(this.getAttribute('data-vehicle-info')))" 
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
                    <td colspan="9" style="text-align:center; color:#777; padding:18px; font-weight: bold;">No Vehicles Found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- PAGINATION -->
<div class="content-footer">
    <div class="entries-container">
        <form method="GET" class="entries-form">
            <input type="hidden" name="page" value="<?php echo htmlspecialchars($current_route); ?>">
            <input type="hidden" name="q" value="<?php echo htmlspecialchars($q); ?>">
            
            <!-- Hidden Filters for Pagination -->
            <input type="hidden" name="filter_vehicle_type_id" value="<?php echo htmlspecialchars($filter_vehicle_type_id); ?>">
            <input type="hidden" name="filter_vehicle_status_id" value="<?php echo htmlspecialchars($filter_vehicle_status_id); ?>">
            <input type="hidden" name="filter_condition_id" value="<?php echo htmlspecialchars($filter_condition_id); ?>">
            <input type="hidden" name="filter_access_id" value="<?php echo htmlspecialchars($filter_access_id); ?>">
            <input type="hidden" name="filter_license_type_id" value="<?php echo htmlspecialchars($filter_license_type_id); ?>">
            <input type="hidden" name="filter_current_location" value="<?php echo htmlspecialchars($filter_current_location); ?>">
            
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
        <!-- Pass all filter params to pagination links -->
        <?php 
            $params = [
                'page' => $current_route,
                'limit' => $limit,
                'q' => $q,
                'filter_vehicle_type_id' => $filter_vehicle_type_id,
                'filter_vehicle_status_id' => $filter_vehicle_status_id,
                'filter_condition_id' => $filter_condition_id,
                'filter_access_id' => $filter_access_id,
                'filter_license_type_id' => $filter_license_type_id,
                'filter_current_location' => $filter_current_location,
            ];
            
            function buildUrl($p, $params) {
                $params['p'] = $p;
                return '?' . http_build_query($params);
            }
        ?>
        
        <a href="<?php echo buildUrl(max(1, $curr_page-1), $params); ?>" class="prev <?php echo ($curr_page <= 1) ? 'disabled' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="currentColor"><path d="M560-240 320-480l240-240 56 56-184 184 184 184-56 56Z"/></svg>
        </a>
        
        <?php 
        $adjacents = 1; 
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == 1 || $i == $total_pages || ($i >= $curr_page - $adjacents && $i <= $curr_page + $adjacents)) {
                echo '<a href="'.buildUrl($i, $params).'" class="'.(($curr_page == $i) ? 'active' : '').'">'.$i.'</a>';
            } elseif ($i == $curr_page - $adjacents - 1 || $i == $curr_page + $adjacents + 1) {
                echo '<span class="pagination-dots">...</span>';
            }
        }
        ?>
        
        <a href="<?php echo buildUrl(min($total_pages, $curr_page+1), $params); ?>" class="next <?php echo ($curr_page >= $total_pages) ? 'disabled' : ''; ?>">
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

<!-- VIEW DETAILS -->
<template id="view-details-template">
    <div class="trip-details-grid" style="padding: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div class="detail-section full-width" style="border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <h4 style="margin: 0 0 10px 0; color: #183a59;">Vehicle Identity</h4>
            <div class="detail-row"><span class="label">Plate No:</span><span class="value" data-key="plateNo"></span></div>
            <div class="detail-row"><span class="label">Vehicle Type:</span><span class="value" data-key="vehicleType"></span></div>
            <div class="detail-row"><span class="label">Access Type:</span><span class="value" data-key="accessType"></span></div>
        </div>
        <div class="detail-section" style="border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <h4 style="margin: 0 0 10px 0; color: #183a59;">Status & Condition</h4>
            <div class="detail-row"><span class="label">Status:</span><span class="value badge" data-key="status"></span></div>
            <div class="detail-row"><span class="label">Condition:</span><span class="value" data-key="vehicleCondition"></span></div>
        </div>
        <div class="detail-section" style="border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <h4 style="margin: 0 0 10px 0; color: #183a59;">Other Info</h4>
            <div class="detail-row"><span class="label">Current Loc:</span><span class="value" data-key="currentLocation"></span></div>
            <div class="detail-row"><span class="label">License Req:</span><span class="value" data-key="licenseType"></span></div>
        </div>
    </div>
    <div class="modal-footer" style="padding: 15px; text-align: right; border-top: 1px solid #eee;">
        <button type="button" class="btn-secondary" onclick="closeModal()">Close</button>
    </div>
</template>

<!-- ADD/EDIT FORM -->
<template id="vehicle-form-template">
    <form action="../actions/save_vehicle.php" method="POST" class="styled-form">
        <input type="hidden" name="vehicle_id" data-key="vehicleId">
        
        <div class="form-row-grid">
            <div class="form-group">
                <label>Plate Number</label>
                <input type="text" name="plate_no" data-key="plateNo" required placeholder="ABC 1234" pattern="[A-Za-z]{3}\s?\d{4}" title="Plate number must be in the format: 3 letters followed by 4 digits (e.g., ABC123)." oninput="this.value = this.value.toUpperCase()">
            </div>
            <div class="form-group">
                <label>Vehicle Type</label>
                <select name="vehicle_type_id" data-key="vehicleTypeId" required>
                    <option value="" disabled selected>-- Select Type --</option>
                    <?php if(isset($types_res)) $types_res->data_seek(0); while($t = $types_res->fetch_assoc()): ?>
                        <option value="<?php echo $t['vehicle_type_id']; ?>"><?php echo $t['vehicle_type']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="form-row-grid">
            <div class="form-group">
                <label>Access Type</label>
                <select name="access_id" data-key="accessId" required>
                    <option value="" disabled selected>-- Select Access --</option>
                    <?php if(isset($access_res)) $access_res->data_seek(0); while($a = $access_res->fetch_assoc()): ?>
                        <option value="<?php echo $a['access_id']; ?>"><?php echo $a['access_type']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>License Required</label>
                <select name="license_type_id" data-key="licenseTypeId" required>
                    <option value="" disabled selected>-- Select License --</option>
                    <?php if(isset($license_res)) $license_res->data_seek(0); while($l = $license_res->fetch_assoc()): ?>
                        <option value="<?php echo $l['license_type_id']; ?>"><?php echo $l['license_type']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="form-row-grid">
            <div class="form-group">
                <label>Condition</label>
                <select name="vehicle_condition_id" data-key="vehicleConditionId" required>
                    <option value="" disabled selected>-- Select Condition --</option>
                    <?php if(isset($conditions_res)) $conditions_res->data_seek(0); while($c = $conditions_res->fetch_assoc()): ?>
                        <option value="<?php echo $c['vehicle_condition_id']; ?>"><?php echo $c['vehicle_condition']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="vehicle_status_id" data-key="vehicleStatusId" required>
                    <option value="" disabled selected>-- Select Status --</option>
                    <?php if(isset($statuses_res)) $statuses_res->data_seek(0); while($s = $statuses_res->fetch_assoc()): ?>
                        <option value="<?php echo $s['vehicle_status_id']; ?>"><?php echo $s['vehicle_status']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Current Location</label>
            <input type="text" name="current_location" data-key="currentLocation" placeholder="e.g. Garage, On Trip">
        </div>

        <div class="modal-actions">
            <button type="button" onclick="closeModal()" class="btn-secondary">Cancel</button>
            <button type="submit" class="btn-save">Save Vehicle</button>
        </div>
    </form>
</template>

<!-- DELETE TEMPLATE -->
<template id="delete-template">
    <div class="delete-warning-box">
        <div class="warning-icon">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="m40-120 440-760 440 760H40Zm138-80h604L480-720 178-200Zm302-40q17 0 28.5-11.5T520-280q0-17-11.5-28.5T480-320q-17 0-28.5 11.5T440-280q0 17 11.5 28.5T480-240Zm-40-120h80v-200h-80v200Zm40-100Z"/></svg>
        </div>
        <h3>Confirm Delete</h3>
        <p class="warning-text">Are you sure? This action cannot be undone.</p>
        <div class="delete-actions">
            <button onclick="closeModal()" class="btn-secondary">Cancel</button>
            <a id="confirm-delete-btn" href="#" class="btn-danger">Delete</a>
        </div>
    </div>
</template>

<!-- FILTER TEMPLATE (UPDATED) -->
<template id="filter-vehicle-template">
    <div class="filter-search-modal styled-form">
        <div class="form-row-grid">
            <div class="form-group">
                <label>Vehicle Type</label>
                <select name="filter_vehicle_type_id">
                    <option value="" <?php if(!$filter_vehicle_type_id) echo 'selected'; ?>>-- All Types --</option>
                    <?php if(isset($types_res)) $types_res->data_seek(0); while($t = $types_res->fetch_assoc()): ?>
                        <option value="<?php echo $t['vehicle_type_id']; ?>" <?php if($filter_vehicle_type_id == $t['vehicle_type_id']) echo 'selected'; ?>><?php echo $t['vehicle_type']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="filter_vehicle_status_id">
                    <option value="" <?php if(!$filter_vehicle_status_id) echo 'selected'; ?>>-- All Statuses --</option>
                    <?php if(isset($statuses_res)) $statuses_res->data_seek(0); while($s = $statuses_res->fetch_assoc()): ?>
                        <option value="<?php echo $s['vehicle_status_id']; ?>" <?php if($filter_vehicle_status_id == $s['vehicle_status_id']) echo 'selected'; ?>><?php echo $s['vehicle_status']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        
        <div class="form-row-grid">
            <div class="form-group">
                <label>Condition</label>
                <select name="filter_condition_id">
                    <option value="" <?php if(!$filter_condition_id) echo 'selected'; ?>>-- All Conditions --</option>
                    <?php if(isset($conditions_res)) $conditions_res->data_seek(0); while($c = $conditions_res->fetch_assoc()): ?>
                        <option value="<?php echo $c['vehicle_condition_id']; ?>" <?php if($filter_condition_id == $c['vehicle_condition_id']) echo 'selected'; ?>><?php echo $c['vehicle_condition']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Access Type</label>
                <select name="filter_access_id">
                    <option value="" <?php if(!$filter_access_id) echo 'selected'; ?>>-- All Access Types --</option>
                    <?php if(isset($access_res)) $access_res->data_seek(0); while($a = $access_res->fetch_assoc()): ?>
                        <option value="<?php echo $a['access_id']; ?>" <?php if($filter_access_id == $a['access_id']) echo 'selected'; ?>><?php echo $a['access_type']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="form-row-grid">
            <div class="form-group">
                <label>License Type</label>
                <select name="filter_license_type_id">
                    <option value="" <?php if(!$filter_license_type_id) echo 'selected'; ?>>-- All License Types --</option>
                    <?php if(isset($license_res)) $license_res->data_seek(0); while($l = $license_res->fetch_assoc()): ?>
                        <option value="<?php echo $l['license_type_id']; ?>" <?php if($filter_license_type_id == $l['license_type_id']) echo 'selected'; ?>><?php echo $l['license_type']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Current Location</label>
                <input type="text" name="filter_current_location" value="<?php echo htmlspecialchars($filter_current_location); ?>" placeholder="e.g. Garage">
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
                
                apply('filter_vehicle_type_id');
                apply('filter_vehicle_status_id');
                apply('filter_condition_id');
                apply('filter_access_id');
                apply('filter_license_type_id');
                apply('filter_current_location');
                
                window.location = window.location.pathname + '?' + params.toString();
            })(this)">Apply Filters</button>
            
            <button type="button" class="btn-danger" onclick="(function(btn){
                const params = new URLSearchParams(window.location.search);
                params.delete('filter_vehicle_type_id');
                params.delete('filter_vehicle_status_id');
                params.delete('filter_condition_id');
                params.delete('filter_access_id');
                params.delete('filter_license_type_id');
                params.delete('filter_current_location');
                params.set('p', 1); 
                window.location = window.location.pathname + '?' + params.toString();
            })(this)">Clear Filters</button>

            <button type="button" onclick="closeModal()" class="btn-secondary">Cancel</button>
        </div>
    </div>
</template>

<script src="model_controller.js"></script>
</body>
</html>