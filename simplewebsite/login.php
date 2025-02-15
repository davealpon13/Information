<?php
// Include the database connection
include('db.php');

// Start the session
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get username and password from form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepare SQL to check if the username exists and fetch user data
    $sql = "SELECT id, username, password, access FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the user data
        $user = $result->fetch_assoc();

        // Check if access is enabled
        if ($user['access'] == 0) {
            echo "<script>alert('Your account has been disabled. Please contact the administrator.'); window.location.href='login.html';</script>";
            exit();
        } elseif (password_verify($password, $user['password'])) {
            // Password is correct, create session
            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
        
            // Redirect to dashboard or home page after login
            echo "<script>alert('Login successful!'); window.location.href='dashboard.php';</script>";
            exit();
        }
        

            header("Location: dashboard.php"); // Redirect to the dashboard
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Invalid username.";
    }

    $stmt->close();


$conn->close();
?>
