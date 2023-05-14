<?php
require_once('db_connection.php');

session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header('Location: login.php');
    exit();
}

// Retrieve reservations from the database
$query = "SELECT * FROM reservations";
$result = mysqli_query($conn, $query);

// Handle form submission for adding or deleting reservation
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["add_reservation"])) {
        // Process adding reservation form data
        $user_id = $_POST["user_id"];
        $apartment_id = $_POST["apartment_id"];
        $check_in_date = $_POST["check_in_date"];
        $check_out_date = $_POST["check_out_date"];
        $price = $_POST["price"];
        $status = $_POST["status"];
        
        // Add the reservation to the database
        $stmt = $conn->prepare("INSERT INTO reservations (user_id, apartment_id, check_in_date, check_out_date, price, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissds", $user_id, $apartment_id, $check_in_date, $check_out_date, $price, $status);
        $stmt->execute();
        $stmt->close();
        
        // Redirect to the reservations.php page after adding reservation
        header("Location: reservations.php");
        exit();
    } elseif (isset($_POST["delete_reservation"])) {
        // Process deleting reservation form data
        $reservation_id = $_POST["delete_reservation"];
        
        // Delete the reservation from the database
        $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        $stmt->close();
        
        // Redirect to the reservations.php page after deleting reservation
        header("Location: reservations.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Reservations</title>
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
            <h2>Reservations</h2>
            <table>
                <tr>
                    <th>User ID</th>
                    <th>Apartment ID</th>
                    <th>Check-in Date</th>
                    <th>Check-out Date</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo $row['apartment_id']; ?></td>
                        <td><?php echo $row['check_in_date']; ?></td>
                        <td><?php echo $row['check_out_date']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <a href="edit_reservation.php?id=<?php echo $row['id']; ?>">Edit</a>
                            <form action="reservations.php" method="POST" style="display: inline-block;">
                                <input type="hidden" name="delete_reservation" value="<?php echo $row['id']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <h2>Add Reservation</h2>
            <form action="reservations.php" method="POST">
                <label for="user_id">User ID:</label>
                <input type="number" id="user_id" name="user_id" required>
                <label for="apartment_id">Apartment ID:</label>
                <input type="number" id="apartment_id" name="apartment_id" required>
                <label for="check_in_date">Check-in Date:</label>
                <input type="date" id="check_in_date" name="check_in_date" required>
                <label for="check_out_date">Check-out Date:</label>
                <input type="date" id="check_out_date" name="check_out_date" required>
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" required>
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="Pending">Pending</option>
                    <option value="Confirmed">Confirmed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                <button type="submit" name="add_reservation">Add Reservation</button>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2023 Alifu Hotel. All rights reserved.</p>
    </footer>
</body>
</html>
