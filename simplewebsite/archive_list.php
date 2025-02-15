<?php
include('db.php'); 
include('navbar.php');

// Fetch archived records
$sql = "SELECT * FROM info WHERE archived = 1";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Members</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Archived Members</h2>
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
                            <a href="restore_info.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Restore this record?');">Restore</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$conn->close();
?>
</body>
</html>
