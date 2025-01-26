<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subnet = $_POST['subnet'];
    $description = $_POST['description'];
    $vlan = $_POST['vlan'];
    $location = $_POST['location'];

    // Insert the new row into the database
    $stmt = $pdo->prepare("INSERT INTO vlans (subnet, description, vlan_id, location, static_ip) VALUES (?, ?, ?, ?, '')");
    $stmt->execute([$subnet, $description, $vlan, $location]);

    if ($stmt->rowCount()) {
        echo "VLAN added successfully.";
    } else {
        echo "Failed to add VLAN.";
    }
}
?>
