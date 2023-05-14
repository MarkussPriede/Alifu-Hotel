<?php
require_once('db_connection.php');

session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header('Location: login.php');
    exit();
}

// Check if the apartment ID is provided in the query string
if (!isset($_GET['id'])) {
    header('Location: apartments.php');
    exit();
}

// Get the apartment ID from the query string
$apartment_id = $_GET['id'];

// Retrieve the apartment details from the database
$query = "SELECT * FROM apartments WHERE id = $apartment_id";
$result = mysqli_query($conn, $query);
$apartment = mysqli_fetch_assoc($result);

// Check if the apartment exists
if (!$apartment) {
    header('Location: apartments.php');
    exit();
}

// Process the form submission for updating the apartment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_apartment'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $type = $_POST['type'];
        $image_url = $_POST['image_url'];

        // Update the apartment in the database
        $query = "UPDATE apartments SET name = '$name', description = '$description', price = '$price', type = '$type', image_url = '$image_url' WHERE id = $apartment_id";

        mysqli_query($conn, $query);

        // Redirect back to the apartments page
        header('Location: apartments.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Edit Apartment</title>
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
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container">
            <h2>Edit Apartment</h2>
            <form action="edit_apartment.php?id=<?php echo $apartment_id; ?>" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $apartment['name']; ?>" required>
                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?php echo $apartment['description']; ?></textarea>
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" value="<?php echo $apartment['price']; ?>" required>
                <label for="type">Type:</label>
                <input type="text" id="type" name="type" value="<?php echo $apartment['type']; ?>" required>
                <label for="image_url">Image URL:</label>
                <input type="text" id="image_url" name="image_url" value="<?php echo $apartment['image_url']; ?>" required>
                <button type="submit" name="update_apartment">Update Apartment</button>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2023 Alifu Hotel. All rights reserved.</p>
    </footer>
</body>
</html>
