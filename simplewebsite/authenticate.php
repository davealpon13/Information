<?php
include('db.php');

$username = 'admin'; // Change this to the desired admin username
$password = password_hash('admin123', PASSWORD_DEFAULT); // Replace 'admin123' with a secure password

$sql = "INSERT INTO admin_users (username, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);

if ($stmt->execute()) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
