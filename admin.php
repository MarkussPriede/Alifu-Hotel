<?php
session_start();
require_once('db_connection.php');

// check if user is logged in and is an admin
if(!isset($_SESSION['user_id']) || $_SESSION['admin'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// handle form submission for adding a new user
if(isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $personal_id = $_POST['personal_id'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];
    
    // Hash the password using Bcrypt algorithm
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (name, surname, email, personal_id, phone_number, password, user_type)
    VALUES ('$name', '$surname', '$email', '$personal_id', '$phone_number', '$hashed_password', '$user_type')";

    if($conn->query($sql) === TRUE) {
        $_SESSION['success_msg'] = 'User added successfully';
        header('Location: admin.php');
        exit();
    } else {
        $_SESSION['error_msg'] = 'Error adding user: ' . $conn->error;
    }
}

// handle form submission for updating a user
if(isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $personal_id = $_POST['personal_id'];
    $phone_number = $_POST['phone_number'];
    $user_type = $_POST['user_type'];
    
    $sql = "UPDATE users SET name='$name', surname='$surname', email='$email', personal_id='$personal_id', 
    phone_number='$phone_number', user_type='$user_type' WHERE id='$id'";

    if($conn->query($sql) === TRUE) {
        $_SESSION['success_msg'] = 'User updated successfully';
        header('Location: admin.php');
        exit();
    } else {
        $_SESSION['error_msg'] = 'Error updating user: ' . $conn->error;
    }
}

// handle form submission for deleting a user
if(isset($_POST['delete_user'])) {
    $id = $_POST['id'];
    
    $sql = "DELETE FROM users WHERE id='$id'";

    if($conn->query($sql) === TRUE) {
        $_SESSION['success_msg'] = 'User deleted successfully';
        header('Location: admin.php');
        exit();
    } else {
        $_SESSION['error_msg'] = 'Error deleting user: ' . $conn->error;
    }
}

// get all users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Alifu Hotel</title>
    <link rel="stylesheet" href="css/admin.css">
  </head>
  <body>
    <header>
      <div class="container">
        <h1>Alifu Hotel Admin Panel</h1>
        <nav>
          <ul>
            <li><a href="admin.php">Dashboard</a></li>
            <li><a href="rooms.php">Rooms</a></li>
            <li><a href="bookings.php">Bookings</a></li>
            <li><a href="users.php">Users</a></li>
            <li><a href="backend/logout.php">Logout</a></li>
          </ul>
        </nav>
      </div>
    </header>
    <main>
      <section class="dashboard">
        <div class="container">
          <h2>Dashboard</h2>
          <div class="grid">
            <div class="card">
              <h3>Rooms</h3>
              <?php 
              // get number of rooms
              require_once('db_connection.php');
              $conn = new mysqli($servername, $username, $password, $dbname);

              $sql = "SELECT COUNT(*) AS num_rooms FROM apartments";
              $result = $conn->query($sql);
              $row = $result->fetch_assoc();
              $num_rooms = $row['num_rooms'];
              echo '<p><strong>Number of Rooms: </strong>' .  $num_rooms . '</p>'; ?>
              <p><strong>Occupancy Rate:</strong> 80%</p>
              <a href="rooms.php" class="button">View Rooms</a>
            </div>
            <div class="card">
              <h3>Bookings</h3>
              <p><strong>Number of Bookings:</strong> 50</p>
              <p><strong>Average Booking Price:</strong> $150</p>
              <a href="bookings.php" class="button">View Bookings</a>
            </div>
            <div class="card">
              <h3>Users</h3>
              <?php
              // get number of users
              require_once('db_connection.php');
              $conn = new mysqli($servername, $username, $password, $dbname);
              
              $sql = "SELECT COUNT(*) AS num_users FROM users";
              $result = $conn->query($sql);
              $row = $result->fetch_assoc();
              $num_users = $row['num_users'];
              echo '<p><strong>Number of Users: </strong>' .  $num_users . '</p>'; ?>
              <p><strong>New Users Today:</strong> - </p>
              <a href="users.php" class="button">View Users</a>
            </div>
          </div>
        </div>
      </section>
    </main>
    <footer>
      <div class="container">
        <p>&copy; 2023 Alifu Hotel. All rights reserved.</p>
      </div>
    </footer>
  </body>
</html>
