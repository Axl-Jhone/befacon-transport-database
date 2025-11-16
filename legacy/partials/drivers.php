<div class="table-content">
        <table id="drivers" class="display">
            <thead>
                <tr>
                    <th colspan="8" id="table-title"><h1>Drivers</h1></th>
                </tr>
                <tr>
                    <th> Driver Id </th>
                    <th> Last Name </th>
                    <th> First Name </th>
                    <th> Middle Initial </th>
                    <th> Sex </th>
                    <th> Birth Date </th>
                    <th> Contact Number </th>
                    <th> Driver Status </th>
                </tr>
            </thead>

            <tbody>
    <?php
        $sql = "SELECT d.driver_id, d.driver_lname, d.driver_fname, d.driver_middleinitial,
               d.driver_sex, d.birthdate, d.contact_no, s.driver_status
        FROM driver_info AS d
        JOIN driver_status_data AS s 
        ON d.driver_status_id = s.driver_status_id";


        $result = $connection->query($sql);

        if (!$result) {
            die("Invalid query: " . $connection->error);
        }

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['driver_id']}</td>
                    <td>{$row['driver_lname']}</td>
                    <td>{$row['driver_fname']}</td>
                    <td>{$row['driver_middleinitial']}</td>
                    <td>{$row['driver_sex']}</td>
                    <td>{$row['birthdate']}</td>
                    <td>{$row['contact_no']}</td>
                    <td>{$row['driver_status']}</td>
                  </tr>";
        }
    ?>
</tbody>
        </table>
</div>