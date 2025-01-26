<?php
include 'dbConnection.php'; // Include database connection

// Fetch all static IPs
$stmt = $mysqli->prepare("SELECT subnet, static_ip FROM vlans");
$stmt->execute();
$result = $stmt->get_result();

$subnets = [];
while ($row = $result->fetch_assoc()) {
    $subnets[$row['subnet']] = $row['static_ip'];
}

echo json_encode($subnets);

$stmt->close();
$mysqli->close();
?>
