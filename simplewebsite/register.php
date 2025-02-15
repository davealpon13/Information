<?php
// Include the database connection
include('db.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $address = trim($_POST['address']);
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];

    // Default access level (0 means waiting for admin approval)
    $default_access = 0;

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.location.href='register.html';</script>";
        exit();
    }

    // Check if the username already exists
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username already exists. Please choose a different one.'); window.location.href='register.html';</script>";
        exit();
    }

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database with access set to 0 (waiting for approval)
    $sql = "INSERT INTO users (username, password, first_name, middle_name, last_name, address, birthday, gender, access) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $username, $hashed_password, $first_name, $middle_name, $last_name, $address, $birthday, $gender, $default_access);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful. Please wait for admin approval.'); window.location.href='login.html';</script>";
        exit();
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='register.html';</script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
