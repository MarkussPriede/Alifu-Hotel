<?php
include 'db_connection.php';
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Function to check date conflicts
function isRoomAvailable($apartmentId, $checkIn, $checkOut) {
    global $conn;
    $sql = "SELECT * FROM reservations 
            WHERE apartment_id = ? 
            AND status != 'Cancelled'
            AND (
                (check_in_date <= ? AND check_out_date >= ?) 
                OR (check_in_date <= ? AND check_out_date >= ?)
                OR (check_in_date >= ? AND check_out_date <= ?)
            )";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issssss', $apartmentId, $checkOut, $checkIn, $checkOut, $checkIn, $checkIn, $checkOut);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows == 0;
}

// Function to create a new reservation
function createReservation($userId, $apartmentId, $checkIn, $checkOut) {
    global $conn;
    if (isRoomAvailable($apartmentId, $checkIn, $checkOut)) {
        $sql = "INSERT INTO reservations (user_id, apartment_id, check_in_date, check_out_date, status) VALUES (?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iiss', $userId, $apartmentId, $checkIn, $checkOut);
        return $stmt->execute();
    } else {
        return false;
    }
}

// Function to get reservations for a user
function getUserReservations($userId) {
    global $conn;
    $sql = "SELECT reservations.id, apartments.name AS apartment_name, check_in_date, check_out_date, status 
            FROM reservations 
            JOIN apartments ON reservations.apartment_id = apartments.id 
            WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Function to cancel a reservation
function cancelReservation($reservationId) {
    global $conn;
    $sql = "UPDATE reservations SET status = 'Cancelled' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $reservationId);
    return $stmt->execute();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_reservation'])) {
        $userId = $_SESSION['user_id'];
        $apartmentId = $_POST['apartment_id'];
        $checkIn = $_POST['check_in'];
        $checkOut = $_POST['check_out'];
        if (createReservation($userId, $apartmentId, $checkIn, $checkOut)) {
            header('Location: reservations.php');
        } else {
            echo "<script>alert('The apartment is already booked for the selected dates. Please choose different dates.');</script>";
        }
        exit();
    } elseif (isset($_POST['cancel_reservation'])) {
        $reservationId = $_POST['reservation_id'];
        cancelReservation($reservationId);
        header('Location: reservations.php');
        exit();
    }
}

// Fetch user reservations
$userId = $_SESSION['user_id'];
$reservations = getUserReservations($userId);

// Get apartment ID from query parameter if available
$selectedApartmentId = isset($_GET['apartment_id']) ? intval($_GET['apartment_id']) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Reservations</title>
    <link rel="stylesheet" href="css/reserve.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:400,500&display=swap">
</head>
<body>
<header>
  <nav>
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="rooms.php">Rooms</a></li>
      <li><a href="#">Amenities</a></li>
      <li><a href="#reviews">Reviews</a></li>
      <li><a href="#">Contact</a></li>
      <li class="right-buttons">
        <?php if (!isset($_SESSION['user_id'])) { ?>
          <a href="login.php" class="loginregisterbutton">Login</a>
          <a href="register.php" class="loginregisterbutton">Register</a>
        <?php } else {
          $query = "SELECT * FROM users WHERE id = '" . $_SESSION['user_id'] . "'";
          $result = mysqli_query($conn, $query);
          $row = mysqli_fetch_assoc($result);
          if ($row['administrator'] == 1) { ?>
            <a href="admin.php" class="loginregisterbutton">Admin</a>
          <?php } else { ?>
            <a href="profile.php">My Profile</a>
          <?php } ?>
          <a href="backend/logout.php" class="loginregisterbutton">Logout</a>
        <?php } ?>
      </li>
    </ul>
  </nav>
</header>
    <main>
        <section>
            <h2>Make a New Reservation</h2>
            <form action="reservations.php" method="post">
                <input type="hidden" name="create_reservation" value="1">
                <label for="apartment">Apartment:</label>
                <select name="apartment_id" id="apartment" required>
                    <!-- Populate options dynamically from the database -->
                    <?php
                    $apartmentsResult = $conn->query("SELECT id, name FROM apartments");
                    while ($apartment = $apartmentsResult->fetch_assoc()) {
                        $selected = $selectedApartmentId === $apartment['id'] ? 'selected' : '';
                        echo "<option value=\"{$apartment['id']}\" $selected>{$apartment['name']}</option>";
                    }
                    ?>
                </select>
                <label for="check_in">Check-in Date:</label>
                <input type="date" name="check_in" id="check_in" required>
                <label for="check_out">Check-out Date:</label>
                <input type="date" name="check_out" id="check_out" required>
                <input type="submit" value="Reserve">
            </form>
        </section>
        <section>
            <h2>Your Current Reservations</h2>
            <?php if (!empty($reservations)) : ?>
                <table>
                    <thead>
                        <tr>
                            <th>Apartment</th>
                            <th>Check-in Date</th>
                            <th>Check-out Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reservation['apartment_name']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['check_in_date']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['check_out_date']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['status']); ?></td>
                                <td>
                                    <?php if ($reservation['status'] !== 'Cancelled') : ?>
                                        <form action="reservations.php" method="post" style="display:inline;">
                                            <input type="hidden" name="cancel_reservation" value="1">
                                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                            <input type="submit" value="Cancel">
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>You have no reservations.</p>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Alifu Hotel</p>
    </footer>
</body>
</html>
