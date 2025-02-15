<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['access'])) {
    $id = intval($_POST['id']);
    $access = intval($_POST['access']);

    $stmt = $conn->prepare("UPDATE users SET access = ? WHERE id = ?");
    $stmt->bind_param("ii", $access, $id);

    if ($stmt->execute()) {
        echo "User access updated successfully!";
    } else {
        echo "Error updating access.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
