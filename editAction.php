<?php
// Include the database connection file
require_once("dbConnection.php");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the values from the POST request
    if (isset($_POST['subnet'], $_POST['description'], $_POST['vlan_id'], $_POST['location'])) {
        $subnet = $_POST['subnet'];
        $description = $_POST['description'];
        $vlan = $_POST['vlan_id'];
        $location = $_POST['location'];

        // Prepare the update query
        $stmt = $mysqli->prepare("UPDATE vlans SET description = ?, vlan_id = ?, location = ? WHERE subnet = ?");
        $stmt->bind_param("siss", $description, $vlan, $location, $subnet);

        // Execute the query
        if ($stmt->execute()) {
            // If successful, redirect to the main page (index.php)
            header("Location: index.php");
        } else {
            // If there's an error, display it
            echo "Error: Could not update VLAN with subnet $subnet";
        }

        // Close the statement and database connection
        $stmt->close();
        $mysqli->close();
    } else {
        echo "All fields are required.";
    }
} else {
    // If the request method is not POST, redirect to the main page
    header("Location: index.php");
}
?>