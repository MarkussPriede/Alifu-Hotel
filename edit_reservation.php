<?php
require_once('db_connection.php');

session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header('Location: login.php');
    exit();
}

// Check if the reservation ID is provided in the query string
if (!isset($_GET['id'])) {
    header('Location: reservations.php');
    exit();
}

// Get the reservation ID from the query string
$reservation_id = $_GET['id'];

// Retrieve the reservation details from the database
$query = "SELECT * FROM reservations WHERE id = $reservation_id";
$result = mysqli_query($conn, $query);
$reservation = mysqli_fetch_assoc($result);

// Check if the reservation exists
if (!$reservation) {
    header('Location: reservations.php');
    exit();
}

// Process the form submission for updating the reservation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_reservation'])) {
        $user_id = $_POST['user_id'];
        $apartment_id = $_POST['apartment_id'];
        $check_in_date = $_POST['check_in_date'];
        $check_out_date = $_POST['check_out_date'];
        $price = $_POST['price'];
        $status = $_POST['status'];

        // Update the reservation in the database
        $query = "UPDATE reservations SET user_id = $user_id, apartment_id = $apartment_id, check_in_date = '$check_in_date', check_out_date = '$check_out_date', price = $price, status = '$status' WHERE id = $reservation_id";
        mysqli_query($conn, $query);

        // Redirect back to the reservations page
        header('Location: reservations.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Edit Reservation</title>
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
            <h2>Edit Reservation</h2>
            <form action="edit_reservation.php?id=<?php echo $reservation_id; ?>" method="POST">
                <label for="user_id">User ID:</label>
                <input type="number" id="user_id" name="user_id" value="<?php echo $reservation['user_id']; ?>" required>
                <label for="apartment_id">Apartment ID:</label>
                <input type="number" id="apartment_id" name="apartment_id" value="<?php echo $reservation['apartment_id']; ?>" required>
                <label for="check_in_date">Check-in Date:</label>
                <input type="date" id="check_in_date" name="check_in_date" value="<?php echo $reservation['check_in_date']; ?>" required>
                <label for="check_out_date">Check-out Date:</label>
                <input type="date" id="check_out_date" name="check_out_date" value="<?php echo $reservation['check_out_date']; ?>" required>
<label for="price">Price:</label>
<input type="number" id="price" name="price" value="<?php echo $reservation['price']; ?>" required>
<label for="status">Status:</label>
<select id="status" name="status" required>
<option value="Pending" <?php echo $reservation['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
<option value="Confirmed" <?php echo $reservation['status'] === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
<option value="Cancelled" <?php echo $reservation['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
</select>
<button type="submit" name="update_reservation">Update Reservation</button>
</form>
</div>
</main>
<footer>
<p>© 2023 Alifu Hotel. All rights reserved.</p>
</footer>
</body>
</html>