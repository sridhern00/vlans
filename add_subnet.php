<?php
// Database connection
include 'dbConnection.php';

// Define database connection variables
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "vlan_management";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch locations for the dropdown
$locations = [];
$location_query = "SELECT DISTINCT location FROM vlans";
$location_result = $conn->query($location_query);
if ($location_result->num_rows > 0) {
    while ($row = $location_result->fetch_assoc()) {
        $locations[] = $row['location'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vlan_id = $_POST['vlan_id'];
    $location = $_POST['location'];
    $subnet = $_POST['subnet'];
    $description = $_POST['description'];

    // Input validation
    if (!empty($vlan_id) && !empty($location) && !empty($subnet) && !empty($description)) {
        // Insert subnet information into the database
        $query = "INSERT INTO vlans (vlan_id, location, subnet, description) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("ssss", $vlan_id, $location, $subnet, $description);

            if ($stmt->execute()) {
                echo "<p>Subnet added successfully.</p>";
            } else {
                echo "<p>Error: " . $stmt->error . "</p>";
            }

            $stmt->close();
        } else {
            echo "<p>Error preparing statement: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Please fill in all fields.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Subnet</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div>
<button onclick="window.location.href='index.php'">Go to Home Page</button>
</div>
    <h2>Add Subnet</h2>
    <form method="POST" action="">
        <label for="vlan_id">VLAN ID:</label>
        <input type="text" id="vlan_id" name="vlan_id" required><br><br>

        <label for="location">Location:</label>
        <select id="location" name="location" required>
            <option value="">Select Location</option>
            <?php foreach ($locations as $loc): ?>
                <option value="<?php echo $loc; ?>"><?php echo $loc; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="subnet">Subnet:</label>
        <input type="text" id="subnet" name="subnet" required><br><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea><br><br>

        <button type="submit">Add Subnet</button>
    </form>
</body>
</html>
