<?php
session_start();
require 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: admin_login.html");
    exit();
}

$sql = "SELECT id, username, first_name, middle_name, last_name, address, birthday, gender, access FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <?php include('admin_navbar.php'); ?>

    <div class="content">
        <h3>Registered Users</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Address</th>
                    <th>Birthday</th>
                    <th>Gender</th>
                    <th>Access</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $checked = $row['access'] ? "checked" : "";
                        echo "<tr id='user-{$row['id']}'>
                                <td>{$row['id']}</td>
                                <td>" . htmlspecialchars($row['username']) . "</td>
                                <td>" . htmlspecialchars($row['first_name']) . "</td>
                                <td>" . htmlspecialchars($row['middle_name']) . "</td>
                                <td>" . htmlspecialchars($row['last_name']) . "</td>
                                <td>" . htmlspecialchars($row['address']) . "</td>
                                <td>" . htmlspecialchars($row['birthday']) . "</td>
                                <td>" . htmlspecialchars($row['gender']) . "</td>
                                <td>
                                    <label class='switch'>
                                        <input type='checkbox' class='access-toggle' data-userid='{$row['id']}' $checked>
                                        <span class='slider round'></span>
                                    </label>
                                </td>
                                <td>
                                    <button class='delete-btn' data-userid='{$row['id']}'>Delete</button>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No users found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $(".access-toggle").change(function() {
                let userId = $(this).data("userid");
                let accessStatus = $(this).prop("checked") ? 1 : 0;

                $.post("update_access.php", { id: userId, access: accessStatus }, function(response) {
                    alert(response);
                });
            });

            $(".delete-btn").click(function() {
                let userId = $(this).data("userid");
                if (confirm("Are you sure you want to delete this user?")) {
                    $.post("delete_user.php", { id: userId }, function(response) {
                        alert(response);
                        $("#user-" + userId).remove();
                    });
                }
            });
        });
    </script>

</body>
</html>
