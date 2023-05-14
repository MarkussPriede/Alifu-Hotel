<?php
require_once('db_connection.php');

session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header('Location: login.php');
    exit();
}

// Check if the user ID is provided in the query string
if (!isset($_GET['id'])) {
    header('Location: users.php');
    exit();
}

// Get the user ID from the query string
$user_id = $_GET['id'];

// Retrieve the user details from the database
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Check if the user exists
if (!$user) {
    header('Location: users.php');
    exit();
}

// Process the form submission for updating the user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_user'])) {
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $email = $_POST['email'];
        $personal_id = $_POST['personal_id'];
        $phone_number = $_POST['phone_number'];
        $administrator = isset($_POST['administrator']) ? 1 : 0;

        // Update the user in the database
        $query = "UPDATE users SET name = '$name', surname = '$surname', email = '$email', personal_id = '$personal_id', phone_number = '$phone_number', administrator = $administrator WHERE id = $user_id";
        mysqli_query($conn, $query);

        // Redirect back to the users page
        header('Location: users.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Edit User</title>
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
            <h2>Edit User</h2>
            <form action="edit_user.php?id=<?php echo $user_id; ?>" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                <label for="surname">Surname:</label>
                <input type="text" id="surname" name="surname" value="<?php echo $user['surname']; ?>" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                <label for="personal_id">Personal ID:</label>
                <input type="text" id="personal_id" name="personal_id" value="<?php echo $user['personal_id']; ?>" required>
                <label for="phone_number">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo $user['phone_number']; ?>" required>
                <label for="administrator">Administrator:</label>
                <input type="checkbox" id="administrator" name="administrator" <?php echo $user['administrator'] ? 'checked' : ''; ?>>
                <button type="submit" name="update_user">Update User</button>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2023 Alifu Hotel. All rights reserved.</p>
    </footer>
</body>
</html>

