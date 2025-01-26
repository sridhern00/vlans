<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vlan_id = $_POST['vlan_id'];

    // Delete the VLAN based on ID
    $stmt = $pdo->prepare("DELETE FROM vlans WHERE vlan_id = ?");
    $stmt->execute([$vlan_id]);

    // Redirect back to index.php
    header("Location: index.php");
    exit();
}
?>
