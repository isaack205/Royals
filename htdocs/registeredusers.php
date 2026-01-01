<?php
// Include database connection
include 'db.php';

// Fetch data from the customers table
$sql = "SELECT * FROM customers";
$result = $connection->query($sql);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . $connection->error);
}

// Start output buffering to capture any errors
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Registered Users</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #0d1117;
            color: #c9d1d9;
            font-family: Arial, sans-serif;
        }

        .orders-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #4ea8de;
            border-radius: 6px;
            background-color: #0d1117;
            color: #c9d1d9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #4ea8de;
            color: white;
        }

        td {
            border: 1px solid #4ea8de;
            border-radius: 6px;
            background-color: #0d1117;
            color: #c9d1d9;
        }

        .action-btn {
            padding: 8px 16px;
            background-color: none;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: 1px solid rgb(179, 0, 0);
        }

        .action-btn:hover {
            background-color: rgb(179, 0, 0);
            color: black;
        }
    </style>

    <script>
        // Function to confirm deletion
        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this user?")) {
                window.location.href = "delete.php?id=" + id;
            }
        }
    </script>
</head>

<body>
    <div class="orders-container">
        <h1>Registered Users</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if there are rows returned
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['email'] . "</td>";
                        
                        // Action buttons: only "Delete" with confirmation
                        echo "<td>
                            <a href='javascript:void(0);' class='action-btn' onclick='confirmDelete(" . $row['id'] . ")'>Delete</a>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No users found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>

<?php
// Close the database connection
$connection->close();
?>
