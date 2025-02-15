<?php
include('db.php'); 
include('navbar.php');

// Fetch records from the database
$searchValue = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM info WHERE (surname LIKE ? OR firstname LIKE ? OR phone LIKE ?) AND archived = 0";
$stmt = $conn->prepare($sql);
$searchTerm = "%{$searchValue}%";
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Information List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Members Recorded Information</h2>
    <input type="text" id="search" placeholder="Enter search term" value="<?php echo htmlspecialchars($searchValue); ?>">
    <button onclick="searchRecords()">Search</button>

    <script>
        function searchRecords() {
            let searchValue = document.getElementById("search").value;
            window.location.href = "?search=" + encodeURIComponent(searchValue);
        }
    </script>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Surname</th>
                    <th>First Name</th>
                    <th>Phone</th>
                    <th>Instrument</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['surname']); ?></td>
                        <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['instrument']); ?></td>
                        <td>
                            <a href="edit_info.php?id=<?php echo $row['id']; ?>">Edit</a> | 
                            <a href="archive_info.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to archive this record?');">Archive</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$stmt->close();
$conn->close();
?>
</body>
</html>
