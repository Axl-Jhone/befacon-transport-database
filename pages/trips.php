<div class="content-header">
    <div class="filter-search">
        <button class="filter">Filter</button>
        <input type="search">
    </div>
    <div class="page-title">Title</div>
    <button class="add-button">
        Add <?php echo $page = $_GET['page'];?>
    </button>
</div>
<div class="content-table">
    <table class="trip-schedule">
        <colgroup>
        </colgroup>
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
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
<?php 
    // The SQL query and connection logic remain the same
    $data = "
    SELECT
        t.trip_id AS trip_id,
        CONCAT_WS (' ', 
            d.driver_fname,
            CASE
                WHEN d.driver_mi IS NULL OR d.driver_mi = ''
                THEN NULL
                ELSE CONCAT (d.driver_mi, '.')
            END,
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
    ORDER BY t.trip_id ASC";

    $result = $conn->query($data);

    if (!$result) {
        die ("Invalid query: " . $conn->error);
    }

    while ($row = $result->fetch_assoc()) {
        // --- 1. PREPARE THE DATA ARRAY for the Modal ---
        $tripDetails = [
            'tripId' => $row['trip_id'],
            'driverName' => $row['driver_name'],
            'contact' => $row['contact'],
            'vehicleType' => $row['vehicle_type'],
            'plateNo' => $row['plate_no'],
            'purpose' => $row['purpose'], 
            'totalCost' => '₱' . number_format($row['trip_cost'], 2), 
            'status' => $row['trip_status'], // Matches JS: document.getElementById('modalStatus')
            'route' => $row['origin'] . ' / ' . $row['destination'], 
            'departure' => $row['departure'],
            'arrival' => $row['arrival'] 
        ];

        // --- 2. ENCODE and ESCAPE the JSON PAYLOAD ---
        $tripDetailsJSON = json_encode($tripDetails);
        $escapedTripDetailsJSON = htmlspecialchars($tripDetailsJSON, ENT_QUOTES, 'UTF-8');

        // --- 3. OUTPUT THE TABLE ROW ---
        echo "
        <tr>
            <td>{$row['trip_id']}</td>
            <td>{$row['driver_name']}</td>
            <td>{$row['vehicle_type']}</td>
            <td>{$row['plate_no']}</td>
            <td>{$row['origin']}</td>
            <td>{$row['destination']}</td>
            <td>{$row['departure']}</td>
            <td>{$row['arrival']}</td>
            <td>{$row['trip_status']}</td>
            <td class='action-cell'>
                <button 
                    onclick=\"openUniversalModal('trip-view-template', 'Trip {$row['trip_id']} Details:', JSON.parse('{$escapedTripDetailsJSON}'))\" class='action-icon view-btn' title='View Details'>
                        <svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#e3e3e3'><path d='M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z'/></svg>
                </button>

                <button class='action-icon edit-btn' title='Edit Trip'>
                    <svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#e3e3e3'><path d='M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h357l-80 80H200v560h560v-278l80-80v358q0 33-23.5 56.5T760-120H200Zm280-360ZM360-360v-170l367-367q12-12 27-18t30-6q16 0 30.5 6t26.5 18l56 57q11 12 17 26.5t6 29.5q0 15-5.5 29.5T897-728L530-360H360Zm481-424-56-56 56 56ZM440-440h56l232-232-28-28-29-28-231 231v57Zm260-260-29-28 29 28 28 28-28-28Z'/></svg>
                </button>
                
                <button class='action-icon delete-btn' title='Delete Trip'>
                    <svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#e3e3e3'><path d='M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z'/></svg>
                </button>
                
            </td>
        </tr>";
    }
?>
</tbody>
    </table>
</div>
<div class="content-footer">
    <div class="entries">entries</div>
    <div class="pagination">page</div>
</div>