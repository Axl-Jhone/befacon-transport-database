<div class="table-content">
        <table id="vehicles" class="display">
            <thead>
                <tr>
                    <th colspan="7" id="table-title"><h1>Vehicles</h1></th>
                </tr>
                <tr>
                    <th>Vehicle ID</th>
                    <th>Vehicle Type</th>
                    <th>Plate No.</th>
                    <th>Vehicle Condition</th>
                    <th>Access</th>
                    <th>Vehicle Status</th>
                    <th>Current Location</th>
                </tr>
            </thead>

            <tbody>
    <?php
        $sql = "
            SELECT 
                v.vehicle_id,
                vt.vehicle_type,
                v.plate_no,
                vc.vehicle_condition,
                ad.access_type,
                vs.vehicle_status,
                vl.current_location
            FROM vehicle_info AS v
            JOIN vehicle_type_data AS vt 
                ON v.vehicle_type_id = vt.vehicle_type_id
            JOIN vehicle_condition_data AS vc 
                ON v.vehicle_condition_id = vc.vehicle_condition_id
            JOIN access_data as ad
                ON v.access_id = ad.access_id
            JOIN vehicle_location_info AS vl
                ON v.vehicle_id = vl.vehicle_id
            JOIN vehicle_status_data AS vs 
                ON vl.vehicle_status_id = vs.vehicle_status_id
        ";

        $result = $connection->query($sql);

        if (!$result) {
            die("Invalid query: " . $connection->error);
        }

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['vehicle_id']}</td>
                    <td>{$row['vehicle_type']}</td>
                    <td>{$row['plate_no']}</td>
                    <td>{$row['vehicle_condition']}</td>
                    <td>{$row['access_type']}</td>
                    <td>{$row['vehicle_status']}</td>
                    <td>{$row['current_location']}</td>
                  </tr>";
        }
    ?>
            </tbody>
        </table>
</div>