<?php
// Include the database connection file
require_once("dbConnection.php");

// Ensure connection is valid before continuing
if (!$mysqli) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if 'subnet' is set in the URL parameter
if (isset($_GET['subnet'])) {
    // Get subnet parameter value from URL
    $subnet = $_GET['subnet'];

    // Prepare the delete query
    $stmt = $mysqli->prepare("DELETE FROM vlans WHERE subnet = ?");
    if ($stmt) {
        $stmt->bind_param("s", $subnet);

        // Execute the query
        if ($stmt->execute()) {
            // If successful, redirect to the main page (index.php)
            header("Location: index.php");
        } else {
            // If there's an error executing the statement, display it
            echo "Error: Could not delete VLAN with subnet $subnet";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing the statement.";
    }

    // Close the database connection
    $mysqli->close();
} else {
    // If subnet is not provided in URL, redirect to the main page
    header("Location: index.php");
}
?>
