<?php

session_start();
include('db.php'); // Include database connection

// Check if editing existing record
$id = isset($_GET['id']) ? $_GET['id'] : null;
$record = null;

if ($id) {
    $sql = "SELECT * FROM info WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $record = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $surname = trim($_POST['surname']);
    $firstname = trim($_POST['firstname']);
    $middlename = $_POST['middlename'];
    $suffix = $_POST['suffix'];
    $phone = $_POST['phone'];
    $birthday = $_POST['birthday']; // Capture birthday input
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $postalcode = $_POST['postalcode'];
    $permanent_address = $_POST['permanent_address'];
    $current_address_same = isset($_POST['current_address_same']) ? 1 : 0;
    $instrument = $_POST['instrument'];
    $profile_picture = $record ? $record['profile_picture'] : null;

    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['profile_picture']['tmp_name'];
        $imageName = basename($_FILES['profile_picture']['name']);
        $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageExtension, $allowedExtensions)) {
            $uploadDir = "uploads/profile_picture/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Create filename using surname_firstname
            $cleanSurname = preg_replace('/[^A-Za-z0-9]/', '', $surname);
            $cleanFirstname = preg_replace('/[^A-Za-z0-9]/', '', $firstname);
            $newImageName = strtolower($cleanSurname . "_" . $cleanFirstname . "." . $imageExtension);
            $uploadPath = $uploadDir . $newImageName;

            if (move_uploaded_file($imageTmpName, $uploadPath)) {
                $profile_picture = "profile_picture/" . $newImageName;
            } else {
                echo "<script>alert('Error uploading image!');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type! Only JPG, JPEG, PNG, and GIF allowed.');</script>";
        }
    }

    if ($id) {
        // Update existing record
        $update_sql = "UPDATE info SET surname=?, firstname=?, middlename=?, suffix=?, phone=?, birthday=?, barangay=?, city=?, province=?, postalcode=?, permanent_address=?, current_address_same=?, instrument=?, profile_picture=? WHERE id=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssssssssssssssi", $surname, $firstname, $middlename, $suffix, $phone, $birthday, $barangay, $city, $province, $postalcode, $permanent_address, $current_address_same, $instrument, $profile_picture, $id);
        $update_stmt->execute();
        $update_stmt->close();

        echo "<script>alert('Record updated successfully!'); window.location.href='edit_info.php?id=$id';</script>";
    } else {
        // Insert new record
        $insert_sql = "INSERT INTO info (surname, firstname, middlename, suffix, phone, birthday, barangay, city, province, postalcode, permanent_address, current_address_same, instrument, profile_picture) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ssssssssssssss", $surname, $firstname, $middlename, $suffix, $phone, $birthday, $barangay, $city, $province, $postalcode, $permanent_address, $current_address_same, $instrument, $profile_picture);
        $insert_stmt->execute();
        $insert_stmt->close();

        echo "<script>alert('Record added successfully!'); window.location.href='edit_info.php';</script>";
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Edit' : 'Add'; ?> Information</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            max-width: 90%;
            max-height: 90%;
            position: relative;
            transition: transform 0.3s ease-in-out;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 30px;
            color: white;
            cursor: pointer;
        }

        .zoom-buttons {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
        }

        .zoom-buttons button {
            background-color: white;
            border: none;
            padding: 8px 12px;
            font-size: 16px;
            cursor: pointer;
            margin: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container">
    <h2><?php echo $id ? 'Edit' : 'Add'; ?> Information</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <label>Surname: <input type="text" name="surname" value="<?php echo htmlspecialchars($record['surname'] ?? ''); ?>" required></label>
        <label>First Name: <input type="text" name="firstname" value="<?php echo htmlspecialchars($record['firstname'] ?? ''); ?>" required></label>
        <label>Middle Name: <input type="text" name="middlename" value="<?php echo htmlspecialchars($record['middlename'] ?? ''); ?>"></label>
        <label>Suffix: <input type="text" name="suffix" value="<?php echo htmlspecialchars($record['suffix'] ?? ''); ?>"></label>
        <label>Phone: <input type="text" name="phone" value="<?php echo htmlspecialchars($record['phone'] ?? ''); ?>" required></label>
        <label>Birthday: <input type="date" name="birthday" value="<?php echo htmlspecialchars($record['birthday'] ?? ''); ?>" required></label> 
        <label>Barangay: <input type="text" name="barangay" value="<?php echo htmlspecialchars($record['barangay'] ?? ''); ?>" required></label>
        <label>City: <input type="text" name="city" value="<?php echo htmlspecialchars($record['city'] ?? ''); ?>" required></label>
        <label>Province: <input type="text" name="province" value="<?php echo htmlspecialchars($record['province'] ?? ''); ?>" required></label>
        <label>Postal Code: <input type="text" name="postalcode" value="<?php echo htmlspecialchars($record['postalcode'] ?? ''); ?>" required></label>
        <label>Permanent Address: <input type="text" name="permanent_address" value="<?php echo htmlspecialchars($record['permanent_address'] ?? ''); ?>" required></label>
        <label><input type="checkbox" name="current_address_same" <?php echo ($record['current_address_same'] ?? false) ? 'checked' : ''; ?>> Current Address Same</label>
        
        <label>Instrument:</label>
        <?php
        $instruments = ["trombone", "flute", "saxophone", "french_horn", "baritone", "clarinet", "trumpet", "tuba", "percussion", "majorette"];
        foreach ($instruments as $inst) {
            echo '<label for="' . $inst . '">
                    <input type="radio" id="' . $inst . '" name="instrument" value="' . $inst . '" ' . (($record['instrument'] ?? '') === $inst ? 'checked' : '') . '> ' . ucfirst(str_replace("_", " ", $inst)) . '
                  </label>';
        }
        ?>

        <label>Upload Picture:</label>
        <input type="file" name="profile_picture" accept="image/*" onchange="previewImage(event)">

        <!-- Display uploaded image -->
        <?php if (!empty($record['profile_picture'])): ?>
            <div>
                <p>Current Profile Picture:</p>
                <img id="profilePreview" src="uploads/<?php echo htmlspecialchars($record['profile_picture']); ?>" alt="Profile Picture" width="150" height="150" onclick="openModal()">
            </div>
        <?php else: ?>
            <img id="profilePreview" src="#" alt="Profile Preview" style="display:none; width:150px; height:150px;" onclick="openModal()">
        <?php endif; ?>

        <button type="submit"><?php echo $id ? 'Update' : 'Submit'; ?></button>
    </form>
</div>

<!-- Modal for Image Zoom -->
<div id="imageModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="modalImage">
    <div class="zoom-buttons">
        <button onclick="zoomIn()">+</button>
        <button onclick="zoomOut()">-</button>
    </div>
</div>

<script>
    function previewImage(event) {
        let reader = new FileReader();
        reader.onload = function() {
            let preview = document.getElementById('profilePreview');
            preview.src = reader.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function openModal() {
        let modal = document.getElementById('imageModal');
        let modalImage = document.getElementById('modalImage');
        let profileImage = document.getElementById('profilePreview');

        if (profileImage.src !== "#") {
            modalImage.src = profileImage.src;
            modal.style.display = "flex";
        }
    }

    function closeModal() {
        document.getElementById('imageModal').style.display = "none";
    }

    let zoomLevel = 1;

    function zoomIn() {
        zoomLevel += 0.2;
        document.getElementById('modalImage').style.transform = `scale(${zoomLevel})`;
    }

    function zoomOut() {
        if (zoomLevel > 0.4) {
            zoomLevel -= 0.2;
            document.getElementById('modalImage').style.transform = `scale(${zoomLevel})`;
        }
    }
</script>

</body>
</html>
