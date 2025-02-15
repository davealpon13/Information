<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit();
}

include('db.php'); // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $surname = trim($_POST['surname']);
    $firstname = trim($_POST['firstname']);
    $middlename = $_POST['middlename'];
    $suffix = $_POST['suffix'];
    $phone = $_POST['phone'];
    $birthday = $_POST['birthday']; // Retrieve birthday
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $postalcode = $_POST['postalcode'];
    $permanent_address = $_POST['permanent_address'];
    $current_address_same = isset($_POST['current_address_same']) ? 1 : 0;
    $instrument = isset($_POST['instrument']) ? $_POST['instrument'] : null;

    // Handle image upload
    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['profile_picture']['tmp_name'];
        $imageName = basename($_FILES['profile_picture']['name']);
        $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageExtension, $allowedExtensions)) {
            $uploadDir = "uploads/profile_picture/";

            // Ensure the directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Format filename: surname_firstname.extension
            $cleanSurname = preg_replace('/[^A-Za-z0-9]/', '', strtolower($surname));
            $cleanFirstname = preg_replace('/[^A-Za-z0-9]/', '', strtolower($firstname));
            $newImageName = "{$cleanSurname}_{$cleanFirstname}.{$imageExtension}";

            $uploadPath = $uploadDir . $newImageName;

            if (move_uploaded_file($imageTmpName, $uploadPath)) {
                $profile_picture = "profile_picture/" . $newImageName; // Save relative path
            } else {
                echo "<script>alert('Error uploading the image!');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.');</script>";
        }
    }

    $sql = "INSERT INTO info (surname, firstname, middlename, suffix, phone, birthday, barangay, city, province, postalcode, permanent_address, current_address_same, instrument, profile_picture) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssssssss", $surname, $firstname, $middlename, $suffix, $phone, $birthday, $barangay, $city, $province, $postalcode, $permanent_address, $current_address_same, $instrument, $profile_picture);


    if ($stmt->execute()) {
        echo "<script>alert('Form submitted successfully!'); window.location.href='information.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Information System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h2>Members Information Form</h2>
        <form method="POST" action="information.php" enctype="multipart/form-data">
            <!-- Personal Information -->
            <div class="form-group">
                <label for="surname">Surname:</label>
                <input type="text" id="surname" name="surname" required>
            </div>
            <div class="form-group">
                <label for="firstname">First Name:</label>
                <input type="text" id="firstname" name="firstname" required>
            </div>
            <div class="form-group">
                <label for="middlename">Middle Name:</label>
                <input type="text" id="middlename" name="middlename">
            </div>
            <div class="form-group">
                <label for="suffix">Suffix (if any):</label>
                <input type="text" id="suffix" name="suffix">
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            <div class="form-group">
    <label for="birthday">Birthday:</label>
    <input type="date" id="birthday" name="birthday" required>
</div>

            <!-- Address Information -->
            <div class="form-group">
                <label for="barangay">Barangay:</label>
                <input type="text" id="barangay" name="barangay" required>
            </div>
            <div class="form-group">
                <label for="city">City:</label>
                <input type="text" id="city" name="city" required>
            </div>
            <div class="form-group">
                <label for="province">Province:</label>
                <input type="text" id="province" name="province" required>
            </div>
            <div class="form-group">
                <label for="postalcode">Postal Code:</label>
                <input type="text" id="postalcode" name="postalcode" required>
            </div>

            <!-- Permanent Address -->
            <div class="form-group">
                <label for="permanent_address">Permanent Address:</label>
                <textarea id="permanent_address" name="permanent_address" required></textarea>
            </div>

            <div class="form-group">
                <label for="current_address_same">
                    <input type="checkbox" id="current_address_same" name="current_address_same">
                    Current Address is the same as Permanent Address
                </label>
            </div>

            <!-- Instrument Selection -->
            <div class="form-group instruments">
                <label>Instrument:</label>
                <label><input type="radio" name="instrument" value="trombone"> Trombone</label>
                <label><input type="radio" name="instrument" value="flute"> Flute</label>
                <label><input type="radio" name="instrument" value="saxophone"> Saxophone</label>
                <label><input type="radio" name="instrument" value="french_horn"> French Horn</label>
                <label><input type="radio" name="instrument" value="baritone"> Baritone</label>
                <label><input type="radio" name="instrument" value="clarinet"> Clarinet</label>
                <label><input type="radio" name="instrument" value="trumpet"> Trumpet</label>
                <label><input type="radio" name="instrument" value="tuba"> Tuba</label>
                <label><input type="radio" name="instrument" value="percussion"> Percussion</label>
                <label><input type="radio" name="instrument" value="majorette"> Majorette</label>
            </div>

            <!-- Upload Picture -->
            <div class="form-group">
                <label for="profile_picture">Upload Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required>
            </div>

            <div class="form-group">
                <button type="submit" class="submit-btn">Submit</button>
            </div>
        </form>
    </div>
</body>
</html>
