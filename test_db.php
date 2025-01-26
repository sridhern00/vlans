<?php
include 'dbConnection.php';

if (isset($db)) {
    echo "Database connection successful.";
} else {
    echo "Database connection failed.";
}
?>
