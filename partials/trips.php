<div class="trips-content">
    <div class="content-title">Trips</div>
    <div class="table-display">
        <table>
            <thead>
                <tr>
                    <th colspan="11" id="table-title"><h1>Trips</h1></th>
                    <th>Add Trip</th>
                </tr>
                <tr>
                    <th>Trip Name</th>
                    <th>Driver</th>
                    <th>Vehicle</th>
                    <th>Purpose</th>
                    <th>Origin</th>
                    <th>Destination</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Status</th>
                    <th>Total Cost</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
    <?php
        $sql = "
            SELECT
                t.trip_ID AS trip_id,
                CONCAT(d.driver_fname, ' ', d.driver_middleinitial, '. ', d.driver_lname) AS driver_name,
                CONCAT(vt.vehicle_type, ' - ', v.plate_no) AS vehicle_name,
                p.purpose AS purpose_name,
                ts.origin,
                ts.destination,
                ts.sched_depart_time AS departure_datetime,
                ts.sched_arrival_time AS arrival_datetime, 
                tsd.trip_status,
                tc.total_cost
            FROM trip_info t
            JOIN driver_info d ON t.driver_id = d.driver_id
            JOIN vehicle_info v ON t.vehicle_id = v.vehicle_id
            JOIN vehicle_type_data vt ON v.vehicle_type_id = vt.vehicle_type_id
            JOIN purpose_data p ON t.purpose_id = p.purpose_id
            JOIN trip_schedule_info ts ON t.trip_id = ts.trip_id
            JOIN trip_status_data tsd ON t.trip_status_id = tsd.trip_status_id
            JOIN trip_cost_info tc ON t.trip_id = tc.trip_id
        ";

        $result = $connection->query($sql);

        if (!$result) {
            die("Invalid query: " . $connection->error);
        }

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['trip_id']}</td>
                    <td>{$row['driver_name']}</td>
                    <td>{$row['vehicle_name']}</td>
                    <td>{$row['purpose_name']}</td>
                    <td>{$row['origin']}</td>
                    <td>{$row['destination']}</td>
                    <td>{$row['departure_datetime']}</td>
                    <td>{$row['arrival_datetime']}</td>
                    <td>{$row['trip_status']}</td>
                    <td>{$row['total_cost']}</td>
                    <td>
                        <!-- You can add action buttons here -->
                    </td>
                  </tr>";
        }
    ?>
</tbody>
        </table>
    </div>
</div>