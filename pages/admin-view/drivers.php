<?php
    // Assume $conn is your database connection object included before this
    // require_once '../../includes/db_connect.php'; 

    // --- PAGINATION LOGIC ---
    $current_route = isset($_GET['page']) ? $_GET['page'] : '';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $curr_page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';

    // --- DRIVER-SPECIFIC FILTER PARAMS ---
    $filter_license_type_id = isset($_GET['filter_license_type_id']) && $_GET['filter_license_type_id'] !== '' ? (int)$_GET['filter_license_type_id'] : 0;
    $filter_driver_status_id = isset($_GET['filter_driver_status_id']) && $_GET['filter_driver_status_id'] !== '' ? (int)$_GET['filter_driver_status_id'] : 0;
    $filter_gender = isset($_GET['filter_gender']) ? trim($_GET['filter_gender']) : '';
    
    // Count active filters for UI indicator
    $activeFilters = 0;
    if ($filter_license_type_id) $activeFilters++;
    if ($filter_driver_status_id) $activeFilters++;
    if ($filter_gender !== '') $activeFilters++;

    if ($curr_page < 1) $curr_page = 1;
    $offset = ($curr_page - 1) * $limit;

    // --- WHERE CLAUSE CONSTRUCTION ---
    $where = "";
    $clauses = [];

    if ($q !== '') {
        $qEsc = $conn->real_escape_string($q);
        $clauses[] = "(
            d.driver_id LIKE '%$qEsc%' OR 
            CONCAT_WS(' ', d.driver_fname, COALESCE(d.driver_mi, ''), d.driver_lname) LIKE '%$qEsc%' OR 
            d.license_no LIKE '%$qEsc%' OR 
            lt.license_type LIKE '%$qEsc%' OR 
            ds.driver_status LIKE '%$qEsc%'
        )";
    }

    if ($filter_license_type_id) {
        $clauses[] = "d.license_type_id = $filter_license_type_id";
    }
    if ($filter_driver_status_id) {
        $clauses[] = "d.driver_status_id = $filter_driver_status_id";
    }
    if ($filter_gender !== '') {
        $genderEsc = $conn->real_escape_string($filter_gender);
        $clauses[] = "d.driver_sex = '$genderEsc'";
    }

    if (!empty($clauses)) {
        $where = ' WHERE ' . implode(' AND ', $clauses);
    }

    // --- COUNT QUERY ---
    $count_sql = "
        SELECT COUNT(*) as total
        FROM driver_info d
        JOIN license_type_data lt ON d.license_type_id = lt.license_type_id
        JOIN driver_status_data ds ON d.driver_status_id = ds.driver_status_id
    " . $where;
    $count_result = $conn->query($count_sql);
    $count_row = $count_result->fetch_assoc();
    $total_rows = $count_row['total'];
    $total_pages = ceil($total_rows / $limit);
    $start_entry = ($total_rows > 0) ? $offset + 1 : 0;
    $end_entry   = min($offset + $limit, $total_rows);

    // --- DROPDOWN DATA ---
    $license_types_res   = $conn->query("SELECT license_type_id, license_type FROM license_type_data");
    $statuses_res        = $conn->query("SELECT driver_status_id, driver_status FROM driver_status_data");
    
    // --- DATA SELECTION QUERY ---
    $data = "
        SELECT
            d.driver_id,
            d.driver_fname,
            d.driver_lname,
            d.driver_mi,
            CONCAT_WS (' ', d.driver_fname, CASE WHEN d.driver_mi IS NULL OR d.driver_mi = '' THEN NULL ELSE CONCAT (d.driver_mi, '.') END, d.driver_lname) AS driver_name, 
            d.driver_sex AS sex,
            d.birthdate,
            d.contact_no AS contact, 
            d.license_no,
            d.license_type_id, 
            d.driver_status_id,
            lt.license_type,
            ds.driver_status
        FROM driver_info d
        JOIN license_type_data lt ON d.license_type_id = lt.license_type_id
        JOIN driver_status_data ds ON d.driver_status_id = ds.driver_status_id
    " . $where . "
        ORDER BY d.driver_id ASC
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
    <title>Drivers</title>

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
        <button class="filter" title="Filter" onclick="openModal('filter-driver-template', 'Filter Drivers')">
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
            
            <!-- FILTER HIDDEN INPUTS -->
            <input type="hidden" name="filter_license_type_id" value="<?php echo htmlspecialchars($filter_license_type_id); ?>">
            <input type="hidden" name="filter_driver_status_id" value="<?php echo htmlspecialchars($filter_driver_status_id); ?>">
            <input type="hidden" name="filter_gender" value="<?php echo htmlspecialchars($filter_gender); ?>">
            
            <input type="search" name="q" id="searchInput" class="search form-control" placeholder="Search drivers..." value="<?php echo htmlspecialchars($q); ?>">
        </form>
    </div>
    
    <div class="page-title">DRIVERS LIST</div>
    
    <button class="add-button" onclick="openModal('driver-form-template', 'Add New Driver')">
        + Add Driver
    </button>
</div>

<div class="content-table">
    <table class="table-display">
        <colgroup></colgroup>
        <thead>
            <tr>
                <!-- VISUAL ROW NUMBER -->
                <th>Driver No.</th>
                <th>Driver Name</th>
                <th>License No.</th>
                <th>License Type</th>
                <th>Status</th>
                <th>Contact</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php 
                    // COUNTER FOR VISUAL ROW NUMBERS
                    $counter = 0;
                ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <?php 
                    // CALCULATE VISUAL ROW NUMBER
                    $counter++;
                    $displayNum = $offset + $counter;

                    // --- GENDER FIX ---
                    $sexValue = trim($row['sex']); 
                    $sexDisplay = ($sexValue === 'M') ? 'Male' : 'Female'; 

                    $modalData = [
                        'driverId'         => $row['driver_id'], // Keeps real ID for logic
                        'licenseTypeId'    => $row['license_type_id'],
                        'statusId'         => $row['driver_status_id'],
                        'driverFname'      => $row['driver_fname'],
                        'driverLname'      => $row['driver_lname'],
                        'driverMi'         => $row['driver_mi'],
                        'driverName'       => $row['driver_name'],
                        'contact'          => $row['contact'],
                        'licenseType'      => $row['license_type'],
                        'licenseNo'        => $row['license_no'],
                        'sex'              => $sexValue,   // "M" or "F" for Edit Form
                        'sexDisplay'       => $sexDisplay, // "Male" or "Female" for View
                        'birthdate'        => date("M j, Y", strtotime($row['birthdate'])),
                        'birthdateRaw'     => $row['birthdate'],
                        'status'           => $row['driver_status'],
                    ];

                    $jsonStr = json_encode($modalData);
                    $safeJsonAttr = htmlspecialchars($jsonStr, ENT_QUOTES, 'UTF-8');
                    $deleteUrl = "../actions/delete_driver.php?id=" . $row['driver_id'];
                ?>

                <tr>
                    <!-- DISPLAY VISUAL NUMBER INSTEAD OF ID -->
                    <td><?php echo $displayNum; ?></td>
                    <td><?php echo $row['driver_name']; ?></td>
                    <td><?php echo $row['license_no']; ?></td>
                    <td><?php echo $row['license_type']; ?></td>
                    <td><span class='status-text'><?php echo $row['driver_status']; ?></span></td>
                    <td><?php echo $row['contact']; ?></td>
                    
                    <td class='action-cell'>
                        <button class='action-icon view-btn' 
                                data-driver-info='<?php echo $safeJsonAttr; ?>'
                                onclick="openModal('view-details-template', 'Driver #<?php echo $row['driver_id']; ?> Details', JSON.parse(this.getAttribute('data-driver-info')))" 
                                title='View Details'>
                            <svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#e3e3e3'><path d='M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z'/></svg>
                        </button>
                        <button class='action-icon edit-btn' 
                                data-driver-info='<?php echo $safeJsonAttr; ?>'
                                onclick="openModal('driver-form-template', 'Edit Driver #<?php echo $row['driver_id']; ?>', JSON.parse(this.getAttribute('data-driver-info')))" 
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
                    <td colspan="7" style="text-align:center; color:#777; padding:18px; font-weight: bold;">No Drivers Found</td>
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
            
            <input type="hidden" name="filter_license_type_id" value="<?php echo htmlspecialchars($filter_license_type_id); ?>">
            <input type="hidden" name="filter_driver_status_id" value="<?php echo htmlspecialchars($filter_driver_status_id); ?>">
            <input type="hidden" name="filter_gender" value="<?php echo htmlspecialchars($filter_gender); ?>">
            
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
        <a href="?page=<?php echo $current_route; ?>&p=<?php echo max(1, $curr_page-1); ?>&limit=<?php echo $limit; ?>&q=<?php echo urlencode($q); ?>&filter_license_type_id=<?php echo urlencode($filter_license_type_id); ?>&filter_driver_status_id=<?php echo urlencode($filter_driver_status_id); ?>&filter_gender=<?php echo urlencode($filter_gender); ?>" class="prev <?php echo ($curr_page <= 1) ? 'disabled' : ''; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="currentColor"><path d="M560-240 320-480l240-240 56 56-184 184 184 184-56 56Z"/></svg>
        </a>
        <?php 
        $adjacents = 1; 
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == 1 || $i == $total_pages || ($i >= $curr_page - $adjacents && $i <= $curr_page + $adjacents)) {
                echo '<a href="?page='.$current_route.'&p='.$i.'&limit='.$limit.'&q='.urlencode($q).'&filter_license_type_id='.urlencode($filter_license_type_id).'&filter_driver_status_id='.urlencode($filter_driver_status_id).'&filter_gender='.urlencode($filter_gender).'" class="'.(($curr_page == $i) ? 'active' : '').'">'.$i.'</a>';
            } elseif ($i == $curr_page - $adjacents - 1 || $i == $curr_page + $adjacents + 1) {
                echo '<span class="pagination-dots">...</span>';
            }
        }
        ?>
        <a href="?page=<?php echo $current_route; ?>&p=<?php echo min($total_pages, $curr_page+1); ?>&limit=<?php echo $limit; ?>&q=<?php echo urlencode($q); ?>&filter_license_type_id=<?php echo urlencode($filter_license_type_id); ?>&filter_driver_status_id=<?php echo urlencode($filter_driver_status_id); ?>&filter_gender=<?php echo urlencode($filter_gender); ?>" class="next <?php echo ($curr_page >= $total_pages) ? 'disabled' : ''; ?>">
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
    <div class="driver-details-grid" style="padding: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        
        <div class="detail-section full-width" style="border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <h4 style="margin: 0 0 10px 0; color: #183a59;">Personal Information</h4>
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Name:</span>
                <span class="value" data-key="driverName"></span>
            </div>
            <!-- View Template uses sexDisplay to show "Male/Female" -->
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Gender:</span>
                <span class="value" data-key="sexDisplay"></span>
            </div>
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Birthdate:</span>
                <span class="value" data-key="birthdate"></span>
            </div>
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Contact No:</span>
                <span class="value" data-key="contact"></span>
            </div>
        </div>

        <div class="detail-section full-width" style="border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <h4 style="margin: 0 0 10px 0; color: #183a59;">License & Status</h4>
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">License No:</span>
                <span class="value" data-key="licenseNo"></span>
            </div>
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">License Type:</span>
                <span class="value" data-key="licenseType"></span>
            </div>
            <div class="detail-row">
                <span class="label" style="font-weight:bold; color:#666;">Current Status:</span>
                <span class="value badge" data-key="status" style="font-weight: bold;"></span>
            </div>
        </div>
    </div>

    <div class="modal-footer" style="padding: 15px; text-align: right; border-top: 1px solid #eee;">
        <button type="button" class="btn-secondary" onclick="closeModal()">Close</button>
    </div>
</template>

<!-- ADD/EDIT FORM TEMPLATE -->
<template id="driver-form-template">
<form action="../actions/save_driver.php" method="POST" class="styled-form">

    <input type="hidden" name="driver_id" data-key="driverId">

    <div class="form-row-grid" style="grid-template-columns: 2fr 2fr 1fr;">
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="driver_fname" data-key="driverFname" required>
        </div>

        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="driver_lname" data-key="driverLname" required>
        </div>

        <div class="form-group">
            <label>M.I.</label>
            <input type="text" name="driver_mi" data-key="driverMi" maxlength="2">
        </div>
    </div>

    <div class="form-row-grid">
        <div class="form-group">
            <label>Contact No.</label>
            <input
                type="tel"
                name="contact_no"
                data-key="contact"
                placeholder="09XXXXXXXXX"
                maxlength="11"
                pattern="09\d{9}"
                title="Contact number must be 09 followed by 9 digits (11 digits total)."
                required>
        </div>

        <div class="form-group">
            <label>Birthdate</label>
            <input type="date" name="birthdate" data-key="birthdateRaw" required>
        </div>
    </div>

    <div class="form-row-grid">
        <div class="form-group">
            <label>License No.</label>
            <input
                type="text"
                name="license_no"
                data-key="licenseNo"
                placeholder="LXXXXXXXXXXX"
                maxlength="12"
                pattern="[Ll]\d{11}"
                title="License number must start with 'L' followed by 11 digits."
                required>
        </div>

        <div class="form-group">
            <label>License Type</label>
            <select name="license_type_id" data-key="licenseTypeId" required>
                <option value="" disabled selected>-- Select Type --</option>
                <?php if (isset($license_types_res)) $license_types_res->data_seek(0); while ($lt = $license_types_res->fetch_assoc()): ?>
                    <option value="<?php echo $lt['license_type_id']; ?>">
                        <?php echo $lt['license_type']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>

    <div class="form-row-grid">
        <div class="form-group">
            <label>Gender</label>
            <select name="driver_sex" data-key="sex" required>
                <option value="" disabled selected>-- Select Gender --</option>
                <option value="M">Male</option>
                <option value="F">Female</option>
            </select>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="driver_status_id" data-key="statusId" required>
                <option value="" disabled selected>-- Select Status --</option>
                <?php if (isset($statuses_res)) $statuses_res->data_seek(0); while ($s = $statuses_res->fetch_assoc()): ?>
                    <option value="<?php echo $s['driver_status_id']; ?>">
                        <?php echo $s['driver_status']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>

    <div class="modal-actions">
        <button type="button" onclick="closeModal()" class="btn-secondary">Cancel</button>
        <button type="submit" class="btn-save">Save Driver</button>
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

<!-- FILTER SEARCH TEMPLATE -->
<template id="filter-driver-template">
    <form method="GET" action="" class="styled-form filter-form">
        <input type="hidden" name="page" value="<?php echo htmlspecialchars($current_route); ?>">
        <input type="hidden" name="limit" value="<?php echo htmlspecialchars($limit); ?>">
        <input type="hidden" name="p" value="1"> <input type="hidden" name="q" value="<?php echo htmlspecialchars($q); ?>">
        
        <div class="form-row-grid">
            <div class="form-group">
                <label for="filter_license_type_id">License Type</label>
                <select name="filter_license_type_id" id="filter_license_type_id">
                    <option value="">-- All Types --</option>
                    <?php 
                        if(isset($license_types_res)) $license_types_res->data_seek(0); 
                        while($lt = $license_types_res->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $lt['license_type_id']; ?>" 
                                <?php echo ($filter_license_type_id == $lt['license_type_id']) ? 'selected' : ''; ?>>
                            <?php echo $lt['license_type']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="filter_driver_status_id">Driver Status</label>
                <select name="filter_driver_status_id" id="filter_driver_status_id">
                    <option value="">-- All Statuses --</option>
                    <?php 
                        if(isset($statuses_res)) $statuses_res->data_seek(0); 
                        while($s = $statuses_res->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $s['driver_status_id']; ?>"
                                <?php echo ($filter_driver_status_id == $s['driver_status_id']) ? 'selected' : ''; ?>>
                            <?php echo $s['driver_status']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="filter_gender">Gender</label>
            <select name="filter_gender" id="filter_gender">
                <option value="">-- All Genders --</option>
                <!-- Use M/F here too to match filter params -->
                <option value="M" <?php echo ($filter_gender === 'M') ? 'selected' : ''; ?>>Male</option>
                <option value="F" <?php echo ($filter_gender === 'F') ? 'selected' : ''; ?>>Female</option>
            </select>
        </div>

        <div class="modal-actions" style="margin-top: 20px;">
            <button type="button" class="btn-primary" onclick="(function(btn){
                const modal = btn.closest('.styled-form');
                const params = new URLSearchParams(window.location.search);
                
                // Reset page
                params.set('p', 1);
                
                // Get filter values
                const licenseV = modal.querySelector('select[name=\'filter_license_type_id\']').value;
                const statusV = modal.querySelector('select[name=\'filter_driver_status_id\']').value;
                const genderV = modal.querySelector('select[name=\'filter_gender\']').value.trim();

                // Set/Delete parameters
                if (licenseV) params.set('filter_license_type_id', licenseV); else params.delete('filter_license_type_id');
                if (statusV) params.set('filter_driver_status_id', statusV); else params.delete('filter_driver_status_id');
                if (genderV) params.set('filter_gender', genderV); else params.delete('filter_gender');
                
                window.location = window.location.pathname + '?' + params.toString();
            })(this)">Apply Filters</button>
            
            <button type="button" class="btn-danger" onclick="(function(btn){
                const params = new URLSearchParams(window.location.search);
                params.delete('filter_license_type_id');
                params.delete('filter_driver_status_id');
                params.delete('filter_gender');
                params.set('p', 1); // Reset page
                window.location = window.location.pathname + '?' + params.toString();
            })(this)">Clear Filters</button>

            <button type="button" onclick="closeModal()" class="btn-secondary">Cancel</button>
        </div>
    </form>
</template>
</body>
</html>