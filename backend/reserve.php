<?php

require_once 'db_connection.php';

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Retrieve the data submitted through the form
$name = $_POST['name'];
$email = $_POST['email'];
$checkin = $_POST['checkin'];
$checkout = $_POST['checkout'];
$room = $_POST['room'];

// Sanitize and validate the input data
$name = mysqli_real_escape_string($conn, $name);
$email = mysqli_real_escape_string($conn, $email);
$checkin = mysqli_real_escape_string($conn, $checkin);
$checkout = mysqli_real_escape_string($conn, $checkout);
$room = mysqli_real_escape_string($conn, $room);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  die("Invalid email format");
}

// Insert the data into the database
$sql = "INSERT INTO reservations (name, email, checkin, checkout, room)
        VALUES ('$name', '$email', '$checkin', '$checkout', '$room')";

if (mysqli_query($conn, $sql)) {
  echo "Reservation successful";
} else {
  echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);

?>
