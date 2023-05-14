<?php
require_once('db_connection.php');

session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header('Location: login.php');
    exit();
}

// Retrieve users from the database
$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);

// Handle form submission for adding or editing user
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["add_user"])) {
        // Process adding user form data
        $name = $_POST["name"];
        $surname = $_POST["surname"];
        $email = $_POST["email"];
        $personal_id = $_POST["personal_id"];
        $phone_number = $_POST["phone_number"];
        $administrator = isset($_POST["administrator"]) ? 1 : 0;
        
        // Add the user to the database
        $stmt = $conn->prepare("INSERT INTO users (name, surname, email, personal_id, phone_number, administrator) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $name, $surname, $email, $personal_id, $phone_number, $administrator);
        $stmt->execute();
        $stmt->close();
        
        // Redirect to the users.php page after adding user
        header("Location: users.php");
        exit();
    } elseif (isset($_POST["edit_user"])) {
        // Process editing user form data
        $user_id = $_POST["user_id"];
        $name = $_POST["name"];
        $surname = $_POST["surname"];
        $email = $_POST["email"];
        $personal_id = $_POST["personal_id"];
        $phone_number = $_POST["phone_number"];
        $administrator = isset($_POST["administrator"]) ? 1 : 0;
        
        // Update the user in the database
        $stmt = $conn->prepare("UPDATE users SET name=?, surname=?, email=?, personal_id=?, phone_number=?, administrator=? WHERE user_id=?");
        $stmt->bind_param("sssssii", $name, $surname, $email, $personal_id, $phone_number, $administrator, $user_id);
        $stmt->execute();
        $stmt->close();
        
        // Redirect to the users.php page after editing user
        header("Location: users.php");
        exit();
    }  elseif (isset($_POST["delete_user"])) {
        // Process deleting user
        $user_id = $_POST["delete_user"];

        // Delete the user from the database
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Redirect to the users.php page after deleting user
        header("Location: users.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
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
            <h2>Users</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Email</th>
                    <th>Personal ID</th>
                    <th>Phone Number</th>
                    <th>Total Reservations</th>
                    <th>Admin</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['surname']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['personal_id']; ?></td>
                        <td><?php echo $row['phone_number']; ?></td>
                        <td><?php echo $row['total_reservations']; ?></td>
                        <td><?php echo $row['administrator'] ? 'Yes' : 'No'; ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a>
                            <form action="users.php" method ="POST">
                                <input type="hidden" name="delete_user" value="<?php echo $row['id']; ?>">
                                <input type="submit" value="Delete">
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <h2>Add User</h2>
            <form action="users.php" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                <label for="surname">Surname:</label>
                <input type="text" id="surname" name="surname" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <label for="personal_id">Personal ID:</label>
                <input type="text" id="personal_id" name="personal_id" required>
                <label for="phone_number">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" required>
                <label for="administrator">Administrator:</label>
                <input type="checkbox" id="administrator" name="administrator">
                <button type="submit" name="add_user">Add User</button>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2023 Alifu Hotel. All rights reserved.</p>
    </footer>
</body>
</html>