<?php
require_once "db_connection.php";
session_start();

// if the administrator value of the user is set to 1, set the session variable to "admin"
if (isset($_SESSION['user_id'])) {
  $query = "SELECT * FROM users WHERE id = '" . $_SESSION['user_id'] . "'";
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($result);
  if ($row['administrator'] == 1) {
    $_SESSION['admin'] = "admin";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Alifu | Our Rooms</title>
  <link rel="stylesheet" href="css/style.css">
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

<main id="rooms">
  <section class="room-list">
    <h2>Our Apartments</h2>
    <div class="rooms-container">
      <?php
      $apartmentsResult = $conn->query("SELECT id, name, description, price, image_url FROM apartments");
      while ($apartment = $apartmentsResult->fetch_assoc()) {
        echo '
        <div class="room-card">
          <img src="' . htmlspecialchars($apartment['image_url']) . '" alt="' . htmlspecialchars($apartment['name']) . '">
          <h3>' . htmlspecialchars($apartment['name']) . '</h3>
          <p>' . htmlspecialchars($apartment['description']) . '</p>
          <p>Price: $' . htmlspecialchars($apartment['price']) . ' per night</p>
          <a href="reservations.php?apartment_id=' . $apartment['id'] . '" class="button">Book Now</a>
        </div>';
      }
      ?>
    </div>
  </section>
</main>

<footer>
  <p>&copy; 2024 Alifu Hotel</p>
</footer>

<script src="script.js"></script>
</body>
</html>
