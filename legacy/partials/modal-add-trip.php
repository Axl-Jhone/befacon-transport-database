<?php
require("db_connect.php");

// Initialize variable
$driver_name = "";

// Fetch driver options from DB
$drivers = [];

try {
    $driver_sql = "SELECT driver_id, 
                    CONCAT_WS(' ', 
                        driver_fname,
                        CASE 
                            WHEN driver_middleinitial IS NULL OR driver_middleinitial = '' 
                            THEN NULL 
                            ELSE CONCAT(driver_middleinitial, '.')
                        END,
                        driver_lname
                    ) AS driver_name
                FROM driver_info
                ORDER BY driver_lname ASC";

    $drivers = $connection->query($driver_sql);
} catch (Exception $e) {
    echo "Error fetching driver data: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $driver_name = $_POST["driver_name"];

    // Just for testing, display the selected driver
    if (!empty($driver_name)) {
        echo "<p>Selected Driver ID: " . htmlspecialchars($driver_name) . "</p>";
    }
}
?>

<!-- Modal -->
<div class="modal-container" id="modal_wrapper">
    <div class="modal">
        <h1>Add Trip (Driver Test)</h1>
        <form method="POST" action="">
            <table class="trip-form-table">
                <tr>
                    <th>Driver</th>
                    <td>
                        <select name="driver_name" required>
                            <option value="">-- Select Driver --</option>
                            <?php if ($drivers && $drivers->num_rows > 0): ?>
                                <?php while($row = $drivers->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($row['driver_id']) ?>">
                                        <?= htmlspecialchars($row['driver_name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <option value="">No drivers found</option>
                            <?php endif; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th>Vehicle</th>
                    <td>
                        <select name="vehicle_name" required>
                        <option value="">-- Select Vehicle --</option>
                        <!-- Loop vehicle rows here -->
                        </select>
                    </td>
                </tr>

                <tr>
                    <th>Purpose</th>
                    <td>
                        <select name="purpose_name" required>
                        <option value="">-- Select Purpose --</option>
                        <!-- Loop purpose rows here -->
                        </select>
                    </td>
                </tr>

                <tr>
                    <th>Origin</th>
                    <td><input type="text" name="origin" required></td>
                </tr>

                <tr>
                    <th>Destination</th>
                    <td><input type="text" name="destination" required></td>
                </tr>

                <tr>
                    <th>Departure</th>
                    <td><input type="datetime-local" name="departure_datetime" required></td>
                </tr>

                <tr>
                    <th>Arrival</th>
                    <td><input type="datetime-local" name="arrival_datetime" required></td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>
                        <select name="trip_status" required>
                        <option value="">-- Select Status --</option>
                        <!-- Loop status rows here -->
                        </select>
                    </td>
                </tr>

                <tr>
                    <th>Total Cost</th>
                    <td><input type="number" step="0.01" name="total_cost" required></td>
                </tr>
            </table>

            <div class="modal-buttons">
                <button type="button" class="action-button" id="close">Cancel</button>
                <button type="submit" class="action-button">Add Trip</button>
            </div>
            </form>
    </div>
</div>
