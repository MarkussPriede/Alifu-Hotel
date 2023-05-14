<?php
require_once('db_connection.php');

session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header('Location: login.php');
    exit();
}

// Retrieve apartments from the database
$query = "SELECT * FROM apartments";
$result = mysqli_query($conn, $query);

// Handle form submission for adding or editing apartment
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["add_apartment"])) {
        // Process adding apartment form data
        $name = $_POST["name"];
        $description = $_POST["description"];
        $price = $_POST["price"];
        $type = $_POST["type"];
        $image_url = $_POST["image_url"];
        
        // Add the apartment to the database
        $stmt = $conn->prepare("INSERT INTO apartments (name, description, price, type, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $name, $description, $price, $type, $image_url);
        $stmt->execute();
        $stmt->close();
        
        // Redirect to the apartments.php page after adding apartment
        header("Location: apartments.php");
        exit();
    } elseif (isset($_POST["edit_apartment"])) {
        // Process editing apartment form data
        $apartment_id = $_POST["apartment_id"];
        $name = $_POST["name"];
        $description = $_POST["description"];
        $price = $_POST["price"];
        $type = $_POST["type"];
        $image_url = $_POST["image_url"];
        
        // Update the apartment in the database
        $stmt = $conn->prepare("UPDATE apartments SET name=?, description=?, price=?, type=?, image_url=? WHERE apartment_id=?");
        $stmt->bind_param("ssdssi", $name, $description, $price, $type, $image_url, $apartment_id);
        $stmt->execute();
        $stmt->close();
        
        // Redirect to the apartments.php page after editing apartment
        header("Location: apartments.php");
        exit();
    } elseif (isset($_POST["delete_apartment"])) {
        // Process deleting apartment form data
        $apartment_id = $_POST["delete_apartment"];
        
        // Delete the apartment from the database
        $stmt = $conn->prepare("DELETE FROM apartments WHERE id=?");
        $stmt->bind_param("i", $apartment_id);
        $stmt->execute();
        $stmt->close();
        
        // Redirect to the apartments.php page after deleting apartment
        header("Location: apartments.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Apartments</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="admin.php">Home</a></li>
                <li><a href="apartments.php">Apartments</a></li>
                <li><a href="reservations.php">Reservations</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="backend\logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container">
        <h2>Apartments</h2>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Type</th>
                    <th>Image URL</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><?php echo $row['type']; ?></td>
                        <td><?php echo $row['image_url']; ?></td>
                        <td>
                            <a href="edit_apartment.php?id=<?php echo $row['id']; ?>" id="editbutton" >Edit</a>
                            <form action="apartments.php" method="POST" style="display: inline-block;">
                                <input type="hidden" name="delete_apartment" value="<?php echo $row['id']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <h2>Add Apartment</h2>
            <form action="apartments.php" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" required>
                <label for="type">Type:</label>
                <input type="text" id="type" name="type" required>
                <label for="image_url">Image URL:</label>
                <input type="text" id="image_url" name="image_url" required>
                <button type="submit" name="add_apartment">Add Apartment</button>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2023 Alifu Hotel. All rights reserved.</p>
    </footer>
</body>
</html>