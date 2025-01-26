<?php
// Include the database connection file
require_once("dbConnection.php");

// Check if 'subnet' is set in the URL parameter
if (isset($_GET['subnet'])) {
    // Get subnet from URL parameter
    $subnet = $_GET['subnet'];

    // Prepare and execute the select query
    $stmt = $mysqli->prepare("SELECT * FROM vlans WHERE subnet = ?");
    $stmt->bind_param("s", $subnet);

    if ($stmt->execute()) {
        // Get the result
        $result = $stmt->get_result();

        // Fetch the next row of a result set as an associative array
        if ($result && $result->num_rows > 0) {
            $resultData = $result->fetch_assoc();
            $subnet = $resultData['subnet'];
            $description = $resultData['description'];
            $vlan = $resultData['vlan_id'];
            $location = $resultData['location'];
        } else {
            echo "No VLAN found with the given Subnet.";
            exit();
        }
    } else {
        echo "Error executing query.";
        exit();
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Subnet parameter is missing.";
    exit();
}

// Close the database connection
$mysqli->close();
?>

<html>
<head>    
    <title>Edit VLAN</title>
</head>

<body>
    <h2>Edit VLAN</h2>
    <p><a href="index.php">Home</a></p>

    <form name="edit" method="post" action="editAction.php">
        <table border="0">
            <tr> 
                <td>Subnet</td>
                <td><input type="text" name="subnet" value="<?php echo htmlspecialchars($subnet); ?>" readonly></td>
            </tr>
            <tr> 
                <td>Description</td>
                <td><input type="text" name="description" value="<?php echo htmlspecialchars($description); ?>"></td>
            </tr>
            <tr> 
                <td>VLAN ID</td>
                <td><input type="text" name="vlan_id" value="<?php echo htmlspecialchars($vlan); ?>"></td>
            </tr>
            <tr> 
                <td>Location</td>
                <td><input type="text" name="location" value="<?php echo htmlspecialchars($location); ?>"></td>
            </tr>
            <tr>
                <td><input type="hidden" name="subnet" value="<?php echo htmlspecialchars($subnet); ?>"></td>
                <td><input type="submit" name="update" value="Update"></td>
            </tr>
        </table>
    </form>
</body>
</html>
