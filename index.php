<?php
// Include the database connection
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subnet Assignment Management</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            margin: 20px;
            background-color: #f5f5f5;
            color: #333;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        .container {
            display: flex;
            width: 100%;
            height: 100%;
        }
        .sidebar {
            width: 30%;
            padding: 20px;
            background-color: #f0f0f0;
            border-right: 1px solid #ccc;
            box-sizing: border-box;
        }
        .content {
            width: 70%;
            padding: 20px;
            box-sizing: border-box;
            overflow-y: auto;
        }
        h2 {
            text-align: center;
            color: #444;
            text-decoration: underline;
        }
        select, input[type="text"], button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            cursor: pointer;
        }
        th, td {
            padding: 10px 15px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #eee;
            color: #333;
            font-weight: bold;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #e1e1e1;
        }
        .highlighted {
            background-color: #90ee90 !important;
        }
        textarea {
            width: 100%;
            height: calc(100% - 190px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            font-family: 'Courier New', Courier, monospace;
            overflow-y: scroll;
            resize: none;
            margin-top: 10px;
        }
        .delete-button, .save-button, .add-button {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .delete-button {
            background-color: red;
        }
        .save-button {
            background-color: green;
        }
        #addRowModal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            z-index: 1000;
            width: 400px;
        }
        #addRowModal h3 {
            margin-top: 0;
        }
        #modalOverlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Subnet Assignment</h2>
            <form>
                <label for="locationDropdown">Select a Location:</label>
                <select id="locationDropdown" name="location">
                    <option value="" disabled selected>Select a location</option>
                    <option value="chennai">Chennai</option>
                    <option value="bangalore">Bangalore</option>
                    <option value="pune">Pune</option>
                    <option value="singapore">Singapore</option>
                </select><br><br>

                <label for="newIP">Add Static IP for VLAN:</label>
                <input type="text" id="newIP" name="static_ip" placeholder="Enter IP Address"><br><br>

                <button type="button" class="save-button" onclick="addStaticIP()">Save IP</button>
            </form>

            <button class="delete-button" onclick="removeSelectedIP()">Remove Selected IP</button>

            <label for="staticIPs">Assigned Static IPs:</label>
            <textarea id="staticIPs" readonly onclick="selectIP(event)"></textarea>
        </div>
        
        <div class="content">
        <a href="add_subnet.php">Add Subnet</a>

            <table id="subnetTable">
                <thead>
                    <tr>
                        <th>IP Subnet</th>
                        <th>Description</th>
                        <th>VLAN</th>
                        <th>Available IPs</th>
                        <th>Used IPs</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch and display VLAN subnets from the database
                    $stmt = $pdo->query("SELECT * FROM vlans");
                    while ($row = $stmt->fetch()) {
                        $static_ips = isset($row['static_ip']) ? $row['static_ip'] : ''; // Default to an empty string if 'static_ip' is not set
                        echo "<tr class='{$row['location']}' data-static_ips='{$static_ips}' data-subnet='{$row['subnet']}'>";
                        echo "<td>{$row['subnet']}</td>";
                        echo "<td>{$row['description']}</td>";
                        echo "<td>{$row['vlan_id']}</td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td><a href=\"edit.php?subnet={$row['subnet']}\">Edit</a> | 
                        <a href=\"delete_one.php?subnet={$row['subnet']}\" onClick=\"return confirm('Are you sure you want to delete this subnet?')\">Delete</a></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rows = document.querySelectorAll('#subnetTable tbody tr');

            function showLocationProjects() {
                rows.forEach(row => row.style.display = 'none'); // Hide all rows initially

                var selectedLocation = document.getElementById('locationDropdown').value;

                if (selectedLocation) {
                    var locationRows = document.querySelectorAll('tbody .' + selectedLocation);
                    locationRows.forEach(row => row.style.display = ''); // Show rows that match the selected location
                }
            }

            document.getElementById('locationDropdown').addEventListener('change', showLocationProjects);

            let selectedRow = null;
            let selectedIP = null;

            function calculateTotalIPs(subnet) {
                const mask = parseInt(subnet.split('/')[1], 10);
                return Math.pow(2, 32 - mask);
            }

            function updateIPs(row) {
                const subnet = row.cells[0].textContent.trim();
                const totalIPs = calculateTotalIPs(subnet);
                const assignedIPs = row.getAttribute('data-static_ips').split('\n').filter(ip => ip !== '').length;

                row.cells[3].textContent = totalIPs - assignedIPs; // Available IPs
                row.cells[4].textContent = assignedIPs; // Used IPs
            }

            function loadIPs() {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "fetch_ips.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const subnets = JSON.parse(xhr.responseText);
            rows.forEach(row => {
                const subnet = row.getAttribute('data-subnet');
                if (subnets[subnet]) {
                    row.setAttribute('data-static_ips', subnets[subnet]);
                    updateIPs(row);
                }
            });
        }
    };
    xhr.send();
}


function saveIPs() {
    // Select all rows in the table
    const rows = document.querySelectorAll('table tbody tr');

    rows.forEach(row => {
        const subnet = row.getAttribute('data-subnet');
        const static_ips = row.getAttribute('data-static_ips');

        console.log(`Saving: Subnet = ${subnet}, Static IPs = ${static_ips}`);

        // Send data to the backend
        fetch("update_ips.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `subnet=${encodeURIComponent(subnet)}&static_ips=${encodeURIComponent(static_ips)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log(`Static IPs updated for subnet: ${subnet}`);
                } else {
                    console.error(`Failed to update subnet ${subnet}: ${data.message}`);
                }
            })
            .catch(error => console.error(`Error updating subnet ${subnet}:`, error));
    });
}



            loadIPs();

            rows.forEach(row => {
                row.addEventListener('click', function() {
                    if (selectedRow) {
                        selectedRow.classList.remove('highlighted');
                    }
                    this.classList.add('highlighted');
                    const static_ips = this.getAttribute('data-static_ips').replace(/\n/g, '\n');
                    document.getElementById('staticIPs').value = static_ips;
                    selectedRow = this;
                });
            });

            function addStaticIP() {
                const newIP = document.getElementById('newIP').value.trim();
                if (newIP && selectedRow) {
                    const currentIPs = selectedRow.getAttribute('data-static_ips');
                    const updatedIPs = currentIPs ? currentIPs + '\n' + newIP : newIP;
                    selectedRow.setAttribute('data-static_ips', updatedIPs);
                    document.getElementById('staticIPs').value = updatedIPs.replace(/\n/g, '\n');
                    document.getElementById('newIP').value = '';
                    updateIPs(selectedRow);
                    saveIPs();
                    updateDatabase(selectedRow.getAttribute('data-subnet'), updatedIPs);
                }
            }

            function updateDatabase(subnet, updatedIPs) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "update_ips.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        console.log(xhr.responseText);
                    }
                };
                xhr.send("subnet=" + subnet + "&static_ip=" + encodeURIComponent(updatedIPs));
            }

            function selectIP(event) {
                const textarea = event.target;
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const selectedText = textarea.value.substring(start, end).trim();

                if (selectedText) {
                    selectedIP = selectedText;
                    alert(`Selected IP: ${selectedIP}`);
                }
            }

            function removeSelectedIP() {

                se
                if (selectedRow && selectedIP) { // Ensure both a row and IP are selected
                    let currentIPs = selectedRow.getAttribute('data-static_ips').split('\n');
                    currentIPs = currentIPs.filter(ip => ip !== selectedIP);
                    const updatedIPs = currentIPs.join('\n');
                    selectedRow.setAttribute('data-static_ips', updatedIPs);
                    document.getElementById('staticIPs').value = updatedIPs.replace(/\n/g, '\n');
                    updateIPs(selectedRow);
                    selectedIP = null; // Clear the selected IP after removing it
                    saveIPs();
                    updateDatabase(selectedRow.getAttribute('data-subnet'), updatedIPs); // Update in the database
                } else {
                    alert('No IP selected or row not selected.');
                }
            }

            function deleteSelectedRow() {
                if (selectedRow) { // Ensure a row is selected
                    if (confirm("Are you sure you want to delete this subnet row?")) {
                        const subnet = selectedRow.getAttribute('data-subnet');

                        // Remove the row from the table
                        selectedRow.remove();

                        // Remove the entry from the database
                        deleteRowFromDatabase(subnet);

                        // Update local storage
                        const storedIPs = JSON.parse(localStorage.getItem('subnetIPs')) || {};
                        delete storedIPs[subnet];
                        localStorage.setItem('subnetIPs', JSON.stringify(storedIPs));

                        // Clear the selected row and the IPs textarea
                        selectedRow = null;
                        document.getElementById('staticIPs').value = '';
                    }
                } else {
                    alert("No row selected. Please select a row to delete.");
                }
            }

            function deleteRowFromDatabase(subnet) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_one.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        console.log(xhr.responseText);
                    }
                };
                xhr.send("subnet=" + subnet);
            }

            document.querySelector('button.save-button').addEventListener('click', addStaticIP);
            document.querySelector('button.delete-button').addEventListener('click', removeSelectedIP);
            document.getElementById('staticIPs').addEventListener('mouseup', selectIP);
        });
    </script>
</body>
</html>
