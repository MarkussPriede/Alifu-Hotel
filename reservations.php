<?php
include 'db_connection.php';
require 'vendor/autoload.php'; // Ensure this path is correct and the file exists

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
                (check_in_date < ? AND check_out_date > ?)
                OR (check_in_date < ? AND check_out_date > ?)
                OR (check_in_date >= ? AND check_out_date <= ?)
            )";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issssss', $apartmentId, $checkOut, $checkIn, $checkOut, $checkIn, $checkIn, $checkOut);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows == 0;
}

// Function to send emails
function sendEmail($to, $subject, $message, $userName) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'priedins.markuss@gmail.com'; 
        $mail->Password = 'ulso yzoy cqhh ghzs'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('priedins.markuss@gmail.com', 'Alifu Hotel'); // Replace with your Gmail address
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = "Dear $userName,<br>$message";

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Function to create a new reservation
function createReservation($userId, $apartmentId, $checkIn, $checkOut) {
    global $conn;
    $userEmail = '';
    $userName = '';
    $apartmentName = '';

    if (isRoomAvailable($apartmentId, $checkIn, $checkOut)) {
        $sql = "INSERT INTO reservations (user_id, apartment_id, check_in_date, check_out_date, status) VALUES (?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iiss', $userId, $apartmentId, $checkIn, $checkOut);
        if ($stmt->execute()) {
            // Fetch user email and name
            $userResult = $conn->query("SELECT email, name FROM users WHERE id = $userId");
            if ($userResult->num_rows > 0) {
                $userRow = $userResult->fetch_assoc();
                $userEmail = $userRow['email'];
                $userName = $userRow['name'];
            }

            // Fetch apartment name
            $apartmentResult = $conn->query("SELECT name FROM apartments WHERE id = $apartmentId");
            if ($apartmentResult->num_rows > 0) {
                $apartmentName = $apartmentResult->fetch_assoc()['name'];
            }

            // Send confirmation email
            $subject = "Reservation Confirmation";
            $message = "Your reservation for the $apartmentName apartment has been created successfully. Check-in Date: $checkIn, Check-out Date: $checkOut.";
            sendEmail($userEmail, $subject, $message, $userName);
            return true;
        }
    }
    return false;
}

// Function to get reservations for a user
function getUserReservations($userId, $sort_by = 'check_in_date') {
    global $conn;
    $valid_sort_columns = ['check_in_date', 'check_out_date', 'status'];
    if (!in_array($sort_by, $valid_sort_columns)) {
        $sort_by = 'check_in_date';
    }
    $sql = "SELECT reservations.id, apartments.name AS apartment_name, check_in_date, check_out_date, status 
            FROM reservations 
            JOIN apartments ON reservations.apartment_id = apartments.id 
            WHERE user_id = ?
            ORDER BY $sort_by";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Function to cancel a reservation
function cancelReservation($reservationId) {
    global $conn;
    $userEmail = '';
    $userName = '';
    $apartmentName = '';
    $checkIn = '';
    $checkOut = '';

    $sql = "UPDATE reservations SET status = 'Cancelled' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $reservationId);
    if ($stmt->execute()) {
        // Fetch user email, name, and reservation details
        $reservationResult = $conn->query("SELECT users.email, users.name, reservations.check_in_date, reservations.check_out_date, apartments.name AS apartment_name 
                                           FROM reservations 
                                           JOIN users ON reservations.user_id = users.id 
                                           JOIN apartments ON reservations.apartment_id = apartments.id 
                                           WHERE reservations.id = $reservationId");
        if ($reservationResult->num_rows > 0) {
            $row = $reservationResult->fetch_assoc();
            $userEmail = $row['email'];
            $userName = $row['name'];
            $apartmentName = $row['apartment_name'];
            $checkIn = $row['check_in_date'];
            $checkOut = $row['check_out_date'];

            // Send cancellation email
            $subject = "Reservation Cancelled";
            $message = "Your reservation for the $apartmentName apartment from $checkIn to $checkOut has been cancelled.";
            sendEmail($userEmail, $subject, $message, $userName);
        }
        return true;
    }
    return false;
}

// Handle form submissions
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_reservation'])) {
        $userId = $_SESSION['user_id'];
        $apartmentId = $_POST['apartment_id'];
        $checkIn = $_POST['check_in'];
        $checkOut = $_POST['check_out'];

        // Validate date format and check-in/out logic
        $currentDate = strtotime(date('Y-m-d'));
        if (strtotime($checkIn) === false || strtotime($checkOut) === false) {
            $error_message = "Invalid date format.";
        } elseif (strtotime($checkIn) >= strtotime($checkOut)) {
            $error_message = "Check-out date must be after check-in date.";
        } elseif (strtotime($checkIn) < $currentDate) {
            $error_message = "Check-in date cannot be in the past.";
        } elseif (strtotime($checkOut) < $currentDate) {
            $error_message = "Check-out date cannot be in the past.";
        } else {
            if (createReservation($userId, $apartmentId, $checkIn, $checkOut)) {
                header('Location: reservations.php');
                exit();
            } else {
                $error_message = "The apartment is already booked for the selected dates. Please choose different dates.";
            }
        }
    } elseif (isset($_POST['cancel_reservation'])) {
        $reservationId = $_POST['reservation_id'];
        if (cancelReservation($reservationId)) {
            header('Location: reservations.php');
            exit();
        } else {
            $error_message = "Failed to cancel the reservation.";
        }
    }
}

// Fetch user reservations
$userId = $_SESSION['user_id'];
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'check_in_date';
$reservations = getUserReservations($userId, $sort_by);

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
      <li><a href="index.php#reviews">Reviews</a></li>
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
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
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
            <div>
                <label for="sort_by">Sort by:</label>
                <select id="sort_by" onchange="sortReservations()">
                    <option value="check_in_date" <?php echo ($sort_by == 'check_in_date') ? 'selected' : ''; ?>>Check-in Date</option>
                    <option value="check_out_date" <?php echo ($sort_by == 'check_out_date') ? 'selected' : ''; ?>>Check-out Date</option>
                    <option value="status" <?php echo ($sort_by == 'status') ? 'selected' : ''; ?>>Status</option>
                </select>
            </div>
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
                                            <input type="submit" class="button" value="Cancel">
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
    <script>
        function sortReservations() {
            const sortBy = document.getElementById('sort_by').value;
            window.location.href = 'reservations.php?sort_by=' + sortBy;
        }
    </script>
</body>
</html>
