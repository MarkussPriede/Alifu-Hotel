<?php
include 'db_connection.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || $_SESSION['admin'] !== 'admin') {
    header('Location: index.php'); // Redirect to homepage if not admin
    exit();
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

// Function to accept a reservation
function acceptReservation($reservationId) {
    global $conn;
    $userEmail = '';
    $userName = '';
    $apartmentName = '';
    $checkIn = '';
    $checkOut = '';

    $sql = "UPDATE reservations SET status = 'Accepted' WHERE id = ?";
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

            // Send acceptance email
            $subject = "Reservation Accepted";
            $message = "Your reservation for the $apartmentName apartment from $checkIn to $checkOut has been accepted.";
            sendEmail($userEmail, $subject, $message, $userName);
        }
        return true;
    }
    return false;
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

// Function to get all users with sorting
function getAllUsers($sort_by = 'id', $order = 'ASC') {
    global $conn;
    $valid_sort_columns = ['id', 'name', 'surname', 'email'];
    if (!in_array($sort_by, $valid_sort_columns)) {
        $sort_by = 'id';
    }
    $sql = "SELECT * FROM users ORDER BY $sort_by $order";
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

// Function to get all reservations with sorting
function getAllReservations($sort_by = 'id', $order = 'ASC') {
    global $conn;
    $valid_sort_columns = ['id', 'email', 'apartment_name', 'check_in_date', 'check_out_date'];
    if (!in_array($sort_by, $valid_sort_columns)) {
        $sort_by = 'id';
    }
    $sql = "SELECT reservations.id, users.email AS email, apartments.name AS apartment_name, check_in_date, check_out_date, status 
            FROM reservations 
            JOIN users ON reservations.user_id = users.id 
            JOIN apartments ON reservations.apartment_id = apartments.id 
            ORDER BY $sort_by $order";
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

// Function to get all apartments with sorting
function getAllApartments($sort_by = 'id', $order = 'ASC') {
    global $conn;
    $valid_sort_columns = ['id', 'name', 'price', 'type'];
    if (!in_array($sort_by, $valid_sort_columns)) {
        $sort_by = 'id';
    }
    $sql = "SELECT * FROM apartments ORDER BY $sort_by $order";
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

// Function to get all reviews with sorting
function getAllReviews($sort_by = 'id', $order = 'ASC') {
    global $conn;
    $valid_sort_columns = ['id', 'username', 'rating'];
    if (!in_array($sort_by, $valid_sort_columns)) {
        $sort_by = 'id';
    }
    $sql = "SELECT reviews.id, users.name AS username, rating, content, approved 
            FROM reviews 
            JOIN users ON reviews.user_id = users.id 
            ORDER BY $sort_by $order";
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

// Handle form submissions for various actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $redirect_section = '';

        switch ($action) {
            case 'approve_reservation':
                $reservationId = $_POST['reservation_id'];
                if (acceptReservation($reservationId)) {
                    $redirect_section = '#reservations';
                } else {
                    echo "Failed to accept the reservation.";
                }
                break;
            case 'cancel_reservation':
                $reservationId = $_POST['reservation_id'];
                if (cancelReservation($reservationId)) {
                    $redirect_section = '#reservations';
                } else {
                    echo "Failed to cancel the reservation.";
                }
                break;
            case 'delete_reservation':
                $reservationId = $_POST['reservation_id'];
                $sql = "DELETE FROM reservations WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $reservationId);
                $stmt->execute();
                $redirect_section = '#reservations';
                break;
            case 'edit_user':
                $userId = $_POST['user_id'];
                $name = $_POST['name'];
                $surname = $_POST['surname'];
                $email = $_POST['email'];
                $phone_number = $_POST['phone_number'];
                $administrator = isset($_POST['administrator']) ? 1 : 0;
                $sql = "UPDATE users SET name = ?, surname = ?, email = ?, phone_number = ?, administrator = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssssii', $name, $surname, $email, $phone_number, $administrator, $userId);
                $stmt->execute();
                $redirect_section = '#users';
                break;
            case 'delete_user':
                $userId = $_POST['user_id'];
                $sql = "DELETE FROM users WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $userId);
                $stmt->execute();
                $redirect_section = '#users';
                break;
            case 'edit_apartment':
                $apartmentId = $_POST['apartment_id'];
                $name = $_POST['name'];
                $description = $_POST['description'];
                $price = $_POST['price'];
                $type = $_POST['type'];
                $sql = "UPDATE apartments SET name = ?, description = ?, price = ?, type = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssdsi', $name, $description, $price, $type, $apartmentId);
                $stmt->execute();
                $redirect_section = '#apartments';
                break;
            case 'delete_apartment':
                $apartmentId = $_POST['apartment_id'];
                $sql = "DELETE FROM apartments WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $apartmentId);
                $stmt->execute();
                $redirect_section = '#apartments';
                break;
            case 'approve_review':
                $reviewId = $_POST['review_id'];
                $sql = "UPDATE reviews SET approved = 1 WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $reviewId);
                $stmt->execute();
                $redirect_section = '#reviews';
                break;
            case 'reject_review':
                $reviewId = $_POST['review_id'];
                $sql = "UPDATE reviews SET approved = 0 WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $reviewId);
                $stmt->execute();
                $redirect_section = '#reviews';
                break;
            case 'delete_review':
                $reviewId = $_POST['review_id'];
                $sql = "DELETE FROM reviews WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $reviewId);
                $stmt->execute();
                $redirect_section = '#reviews';
                break;
            case 'add_user':
                $name = $_POST['name'];
                $surname = $_POST['surname'];
                $email = $_POST['email'];
                $phone_number = $_POST['phone_number'];
                $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $administrator = isset($_POST['administrator']) ? 1 : 0;
                $sql = "INSERT INTO users (name, surname, email, phone_number, password, administrator) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('sssssi', $name, $surname, $email, $phone_number, $password, $administrator);
                $stmt->execute();
                $redirect_section = '#users';
                break;
            case 'add_apartment':
                $name = $_POST['name'];
                $description = $_POST['description'];
                $price = $_POST['price'];
                $type = $_POST['type'];
                $sql = "INSERT INTO apartments (name, description, price, type) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssds', $name, $description, $price, $type);
                $stmt->execute();
                $redirect_section = '#apartments';
                break;
        }
        header("Location: admin.php$redirect_section");
        exit();
    }
}

// Fetch all data with sorting
$sort_users_by = isset($_GET['sort_users_by']) ? $_GET['sort_users_by'] : 'id';
$order_users = isset($_GET['order_users']) ? $_GET['order_users'] : 'ASC';
$sort_reservations_by = isset($_GET['sort_reservations_by']) ? $_GET['sort_reservations_by'] : 'id';
$order_reservations = isset($_GET['order_reservations']) ? $_GET['order_reservations'] : 'ASC';
$sort_apartments_by = isset($_GET['sort_apartments_by']) ? $_GET['sort_apartments_by'] : 'id';
$order_apartments = isset($_GET['order_apartments']) ? $_GET['order_apartments'] : 'ASC';
$sort_reviews_by = isset($_GET['sort_reviews_by']) ? $_GET['sort_reviews_by'] : 'id';
$order_reviews = isset($_GET['order_reviews']) ? $_GET['order_reviews'] : 'ASC';

$users = getAllUsers($sort_users_by, $order_users);
$reservations = getAllReservations($sort_reservations_by, $order_reservations);
$apartments = getAllApartments($sort_apartments_by, $order_apartments);
$reviews = getAllReviews($sort_reviews_by, $order_reviews);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<header>
    <h1>Alifu Hotel Admin Panel</h1>
</header>
<nav>
    <a href="#users" data-section="users">Users</a>
    <a href="#reservations" data-section="reservations">Reservations</a>
    <a href="#apartments" data-section="apartments">Apartments</a>
    <a href="#reviews" data-section="reviews">Reviews</a>
    <a href="index.php">Homepage</a>
</nav>
<div class="container">
    <section id="users" class="active">
        <h2>Users</h2>
        <button onclick="document.getElementById('addUserForm').style.display='block'">Add User</button>
        <label for="sort_users_by">Sort by:</label>
        <select id="sort_users_by" onchange="sortUsers()">
            <option value="id" <?php echo ($sort_users_by == 'id') ? 'selected' : ''; ?>>ID</option>
            <option value="name" <?php echo ($sort_users_by == 'name') ? 'selected' : ''; ?>>Name</option>
            <option value="surname" <?php echo ($sort_users_by == 'surname') ? 'selected' : ''; ?>>Surname</option>
            <option value="email" <?php echo ($sort_users_by == 'email') ? 'selected' : ''; ?>>Email</option>
        </select>
        <select id="order_users" onchange="sortUsers()">
            <option value="ASC" <?php echo ($order_users == 'ASC') ? 'selected' : ''; ?>>Ascending</option>
            <option value="DESC" <?php echo ($order_users == 'DESC') ? 'selected' : ''; ?>>Descending</option>
        </select>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Administrator</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['surname']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                    <td><?php echo $user['administrator'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <button onclick="editUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['name']); ?>', '<?php echo htmlspecialchars($user['surname']); ?>', '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo htmlspecialchars($user['phone_number']); ?>', <?php echo $user['administrator']; ?>)">Edit</button>
                        <form action="admin.php" method="post" style="display:inline;">
                            <input type="hidden" name="action" value="delete_user">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" class="delete-button">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form id="editUserForm" action="admin.php" method="post" style="display:none;">
            <h3>Edit User</h3>
            <input type="hidden" name="action" value="edit_user">
            <input type="hidden" name="user_id" id="editUserId">
            <label for="name">Name:</label>
            <input type="text" name="name" id="editUserName" required>
            <label for="surname">Surname:</label>
            <input type="text" name="surname" id="editUserSurname" required>
            <label for="email">Email:</label>
            <input type="email" name="email" id="editUserEmail" required>
            <label for="phone_number">Phone Number:</label>
            <input type="text" name="phone_number" id="editUserPhoneNumber" required>
            <label for="administrator">Administrator:</label>
            <input type="checkbox" name="administrator" id="editUserAdmin">
            <button type="submit">Save</button>
            <button type="button" onclick="document.getElementById('editUserForm').style.display='none'">Cancel</button>
        </form>
        <form id="addUserForm" action="admin.php" method="post" style="display:none;">
            <h3>Add User</h3>
            <input type="hidden" name="action" value="add_user">
            <label for="name">Name:</label>
            <input type="text" name="name" required>
            <label for="surname">Surname:</label>
            <input type="text" name="surname" required>
            <label for="email">Email:</label>
            <input type="email" name="email" required>
            <label for="phone_number">Phone Number:</label>
            <input type="text" name="phone_number" required>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <label for="administrator">Administrator:</label>
            <input type="checkbox" name="administrator">
            <button type="submit">Add</button>
            <button type="button" onclick="document.getElementById('addUserForm').style.display='none'">Cancel</button>
        </form>
    </section>
    <section id="reservations">
        <h2>Reservations</h2>
        <label for="sort_reservations_by">Sort by:</label>
        <select id="sort_reservations_by" onchange="sortReservations()">
            <option value="id" <?php echo ($sort_reservations_by == 'id') ? 'selected' : ''; ?>>ID</option>
            <option value="email" <?php echo ($sort_reservations_by == 'email') ? 'selected' : ''; ?>>Email</option>
            <option value="apartment_name" <?php echo ($sort_reservations_by == 'apartment_name') ? 'selected' : ''; ?>>Apartment</option>
            <option value="check_in_date" <?php echo ($sort_reservations_by == 'check_in_date') ? 'selected' : ''; ?>>Check-in Date</option>
            <option value="check_out_date" <?php echo ($sort_reservations_by == 'check_out_date') ? 'selected' : ''; ?>>Check-out Date</option>
        </select>
        <select id="order_reservations" onchange="sortReservations()">
            <option value="ASC" <?php echo ($order_reservations == 'ASC') ? 'selected' : ''; ?>>Ascending</option>
            <option value="DESC" <?php echo ($order_reservations == 'DESC') ? 'selected' : ''; ?>>Descending</option>
        </select>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Apartment</th>
                    <th>Check-in Date</th>
                    <th>Check-out Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($reservation['id']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['email']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['apartment_name']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['check_in_date']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['check_out_date']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['status']); ?></td>
                    <td>
                        <form action="admin.php" method="post" style="display:inline;">
                            <input type="hidden" name="action" value="approve_reservation">
                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                            <button type="submit">Approve</button>
                        </form>
                        <form action="admin.php" method="post" style="display:inline;">
                            <input type="hidden" name="action" value="cancel_reservation">
                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                            <button type="submit">Cancel</button>
                        </form>
                        <form action="admin.php" method="post" style="display:inline;">
                            <input type="hidden" name="action" value="delete_reservation">
                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                            <button type="submit" class="delete-button">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <section id="apartments">
        <h2>Apartments</h2>
        <button onclick="document.getElementById('addApartmentForm').style.display='block'">Add Apartment</button>
        <label for="sort_apartments_by">Sort by:</label>
        <select id="sort_apartments_by" onchange="sortApartments()">
            <option value="id" <?php echo ($sort_apartments_by == 'id') ? 'selected' : ''; ?>>ID</option>
            <option value="name" <?php echo ($sort_apartments_by == 'name') ? 'selected' : ''; ?>>Name</option>
            <option value="price" <?php echo ($sort_apartments_by == 'price') ? 'selected' : ''; ?>>Price</option>
            <option value="type" <?php echo ($sort_apartments_by == 'type') ? 'selected' : ''; ?>>Type</option>
        </select>
        <select id="order_apartments" onchange="sortApartments()">
            <option value="ASC" <?php echo ($order_apartments == 'ASC') ? 'selected' : ''; ?>>Ascending</option>
            <option value="DESC" <?php echo ($order_apartments == 'DESC') ? 'selected' : ''; ?>>Descending</option>
        </select>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($apartments as $apartment) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($apartment['id']); ?></td>
                    <td><?php echo htmlspecialchars($apartment['name']); ?></td>
                    <td><?php echo htmlspecialchars($apartment['description']); ?></td>
                    <td><?php echo htmlspecialchars($apartment['price']); ?></td>
                    <td><?php echo htmlspecialchars($apartment['type']); ?></td>
                    <td>
                        <button onclick="editApartment(<?php echo $apartment['id']; ?>, '<?php echo htmlspecialchars($apartment['name']); ?>', '<?php echo htmlspecialchars($apartment['description']); ?>', <?php echo $apartment['price']; ?>, '<?php echo htmlspecialchars($apartment['type']); ?>')">Edit</button>
                        <form action="admin.php" method="post" style="display:inline;">
                            <input type="hidden" name="action" value="delete_apartment">
                            <input type="hidden" name="apartment_id" value="<?php echo $apartment['id']; ?>">
                            <button type="submit" class="delete-button">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form id="editApartmentForm" action="admin.php" method="post" style="display:none;">
            <h3>Edit Apartment</h3>
            <input type="hidden" name="action" value="edit_apartment">
            <input type="hidden" name="apartment_id" id="editApartmentId">
            <label for="name">Name:</label>
            <input type="text" name="name" id="editApartmentName" required>
            <label for="description">Description:</label>
            <textarea name="description" id="editApartmentDescription" required></textarea>
            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" id="editApartmentPrice" required>
            <label for="type">Type:</label>
            <input type="text" name="type" id="editApartmentType" required>
            <button type="submit">Save</button>
            <button type="button" onclick="document.getElementById('editApartmentForm').style.display='none'">Cancel</button>
        </form>
        <form id="addApartmentForm" action="admin.php" method="post" style="display:none;">
            <h3>Add Apartment</h3>
            <input type="hidden" name="action" value="add_apartment">
            <label for="name">Name:</label>
            <input type="text" name="name" required>
            <label for="description">Description:</label>
            <textarea name="description" required></textarea>
            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" required>
            <label for="type">Type:</label>
            <input type="text" name="type" required>
            <button type="submit">Add</button>
            <button type="button" onclick="document.getElementById('addApartmentForm').style.display='none'">Cancel</button>
        </form>
    </section>
    <section id="reviews">
        <h2>Reviews</h2>
        <label for="sort_reviews_by">Sort by:</label>
        <select id="sort_reviews_by" onchange="sortReviews()">
            <option value="id" <?php echo ($sort_reviews_by == 'id') ? 'selected' : ''; ?>>ID</option>
            <option value="username" <?php echo ($sort_reviews_by == 'username') ? 'selected' : ''; ?>>Username</option>
            <option value="rating" <?php echo ($sort_reviews_by == 'rating') ? 'selected' : ''; ?>>Rating</option>
        </select>
        <select id="order_reviews" onchange="sortReviews()">
            <option value="ASC" <?php echo ($order_reviews == 'ASC') ? 'selected' : ''; ?>>Ascending</option>
            <option value="DESC" <?php echo ($order_reviews == 'DESC') ? 'selected' : ''; ?>>Descending</option>
        </select>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Rating</th>
                    <th>Content</th>
                    <th>Approved</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $review) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($review['id']); ?></td>
                    <td><?php echo htmlspecialchars($review['username']); ?></td>
                    <td><?php echo htmlspecialchars($review['rating']); ?></td>
                    <td><?php echo htmlspecialchars($review['content']); ?></td>
                    <td><?php echo $review['approved'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <form action="admin.php" method="post" style="display:inline;">
                            <input type="hidden" name="action" value="approve_review">
                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                            <button type="submit">Approve</button>
                        </form>
                        <form action="admin.php" method="post" style="display:inline;">
                            <input type="hidden" name="action" value="reject_review">
                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                            <button type="submit">Reject</button>
                        </form>
                        <form action="admin.php" method="post" style="display:inline;">
                            <input type="hidden" name="action" value="delete_review">
                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                            <button type="submit" class="delete-button">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>
<script src="admin.js"></script>
<script>
    function sortUsers() {
        const sortBy = document.getElementById('sort_users_by').value;
        const order = document.getElementById('order_users').value;
        window.location.href = 'admin.php?sort_users_by=' + sortBy + '&order_users=' + order;
    }

    function sortReservations() {
        const sortBy = document.getElementById('sort_reservations_by').value;
        const order = document.getElementById('order_reservations').value;
        window.location.href = 'admin.php?sort_reservations_by=' + sortBy + '&order_reservations=' + order;
    }

    function sortApartments() {
        const sortBy = document.getElementById('sort_apartments_by').value;
        const order = document.getElementById('order_apartments').value;
        window.location.href = 'admin.php?sort_apartments_by=' + sortBy + '&order_apartments=' + order;
    }

    function sortReviews() {
        const sortBy = document.getElementById('sort_reviews_by').value;
        const order = document.getElementById('order_reviews').value;
        window.location.href = 'admin.php?sort_reviews_by=' + sortBy + '&order_reviews=' + order;
    }

    function editUser(id, name, surname, email, phoneNumber, isAdmin) {
        document.getElementById('editUserId').value = id;
        document.getElementById('editUserName').value = name;
        document.getElementById('editUserSurname').value = surname;
        document.getElementById('editUserEmail').value = email;
        document.getElementById('editUserPhoneNumber').value = phoneNumber;
        document.getElementById('editUserAdmin').checked = isAdmin;
        document.getElementById('editUserForm').style.display = 'block';
    }

    function editApartment(id, name, description, price, type) {
        document.getElementById('editApartmentId').value = id;
        document.getElementById('editApartmentName').value = name;
        document.getElementById('editApartmentDescription').value = description;
        document.getElementById('editApartmentPrice').value = price;
        document.getElementById('editApartmentType').value = type;
        document.getElementById('editApartmentForm').style.display = 'block';
    }
</script>
</body>
</html>
