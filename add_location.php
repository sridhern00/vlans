<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $location = $_POST['location'];
    $static_ip = $_POST['static_ip'];

    // Example: Inserting the IP into a database
    $stmt = $pdo->prepare("INSERT INTO vlans (location, static_ip) VALUES (?, ?)");
    $stmt->execute([$location, $static_ip]);

    // Redirect back to index.php
    header("Location: index.php");
    exit();
}
?>
