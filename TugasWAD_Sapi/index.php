<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Cow Database</title>
</head>
<body>

<?php
include 'db.php';

$maxIDQuery = "SELECT MAX(CAST(SUBSTRING_INDEX(CowID, '_', -1) AS UNSIGNED)) as maxID FROM cows";
$maxIDResult = $conn->query($maxIDQuery);
$maxIDRow = $maxIDResult->fetch_assoc();
$nextID = str_pad($maxIDRow['maxID'] + 1, 3, '0', STR_PAD_LEFT);

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $deleteID = $_GET['id'];
    $deleteQuery = "DELETE FROM cows WHERE CowID = '$deleteID'";
    $conn->query($deleteQuery);
    header("Location: index.php");
    exit();
}

$query = "SELECT * FROM cows";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $breed = $_POST['breed'];
    $dob = $_POST['dob'];
    $weight = $_POST['weight'];

    $picture = $_FILES['picture']['tmp_name'];
    if (is_uploaded_file($picture)) {
        $pictureData = addslashes(file_get_contents($picture));
    } else {
        $pictureData = isset($_POST['existingPicture']) ? $_POST['existingPicture'] : '';
    }

    if ($_POST['action'] === 'edit') {
        $editID = $_POST['editID'];
        $updateQuery = "UPDATE cows SET Name='$name', Breed='$breed', DateOfBirth='$dob', Weight='$weight', Picture='$pictureData' WHERE CowID='$editID'";
        $conn->query($updateQuery);
    } else {
        $cowID = time() . '_' . $nextID;
        $insertQuery = "INSERT INTO cows (CowID, Name, Breed, DateOfBirth, Weight, Picture) VALUES ('$cowID', '$name', '$breed', '$dob', '$weight', '$pictureData')";
        $conn->query($insertQuery);
    }

    header("Location: index.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editID = $_GET['id'];
    $editQuery = "SELECT * FROM cows WHERE CowID = '$editID'";
    $editResult = $conn->query($editQuery);
    $editRow = $editResult->fetch_assoc();
}
?>

<table>
    <tr>
        <th>CowID</th>
        <th>Name</th>
        <th>Breed</th>
        <th>Date of Birth</th>
        <th>Weight</th>
        <th>Picture</th>
        <th>Action</th>
    </tr>

    <?php
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['CowID']}</td>";
        echo "<td>{$row['Name']}</td>";
        echo "<td>{$row['Breed']}</td>";
        echo "<td>{$row['DateOfBirth']}</td>";
        echo "<td>{$row['Weight']}</td>";
        echo "<td><img src='data:image/jpeg;base64," . base64_encode($row['Picture']) . "' height='50' width='50'/></td>";
        echo "<td><a href='index.php?action=edit&id={$row['CowID']}'>Edit</a> | <a href='index.php?action=delete&id={$row['CowID']}'>Delete</a></td>";
        echo "</tr>";
    }
    ?>
</table>

<!-- Form for inserting new data or editing existing data -->
<form action="index.php" method="POST" enctype="multipart/form-data">
    <?php if (isset($editRow)): ?>
        <h2>Edit Cow</h2>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="editID" value="<?php echo $editRow['CowID']; ?>">
        <input type="hidden" name="existingPicture" value="<?php echo base64_encode($editRow['Picture']); ?>">
    <?php else: ?>
        <h2>Berjualan Sapi Yuk!</h2>
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="cowID" value="<?php echo time() . '_' . mt_rand(); ?>">
    <?php endif; ?>

    <label for="name">Name:</label>
    <input type="text" name="name" value="<?php echo isset($editRow) ? $editRow['Name'] : ''; ?>" required>

    <label for="breed">Breed:</label>
    <input type="text" name="breed" value="<?php echo isset($editRow) ? $editRow['Breed'] : ''; ?>" required>

    <label for="dob">Date of Birth:</label>
    <input type="date" name="dob" value="<?php echo isset($editRow) ? $editRow['DateOfBirth'] : ''; ?>" required>

    <label for="weight">Weight:</label>
    <input type="number" name="weight" value="<?php echo isset($editRow) ? $editRow['Weight'] : ''; ?>" required>

    <label for="picture">Picture:</label>
    <input type="file" name="picture">

    <input type="submit" value="<?php echo isset($editRow) ? 'Update' : 'Add'; ?>">
</form>

</body>
</html>
