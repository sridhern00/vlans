<?php
// Include database connection
include 'dbConnection.php';

// Set response header for JSON
header('Content-Type: application/json');

try {
    // Validate required parameters
    if (!isset($_POST['subnet']) || !isset($_POST['static_ips'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters: subnet or static_ips']);
        exit;
    }

    // Retrieve and sanitize POST data
    $subnet = trim($_POST['subnet']);
    $static_ips = trim($_POST['static_ips']);

    // Log received data (for debugging)
    error_log("Received subnet: $subnet");
    error_log("Received static_ips: $static_ips");

    // Validate inputs
    if (empty($subnet) || empty($static_ips)) {
        echo json_encode(['success' => false, 'message' => 'Subnet or Static IPs cannot be empty']);
        exit;
    }

    // Prepare and execute the SQL query
    $query = "UPDATE vlans SET static_ip = :static_ips WHERE subnet = :subnet";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':static_ips', $static_ips, PDO::PARAM_STR);
    $stmt->bindParam(':subnet', $subnet, PDO::PARAM_STR);

    // Execute the query
    if (!$stmt->execute()) {
        // Log SQL errors
        $errorInfo = $stmt->errorInfo();
        error_log("SQL Error: " . print_r($errorInfo, true));
        echo json_encode(['success' => false, 'message' => 'Database update failed', 'error' => $errorInfo]);
        exit;
    }

    // Check if any rows were updated
    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'No matching subnet found in the database']);
        exit;
    }

    // Success response
    echo json_encode(['success' => true, 'message' => 'Static IPs updated successfully']);
} catch (Exception $e) {
    // Log unexpected errors
    error_log("Exception: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Unexpected error occurred']);
}
?>
