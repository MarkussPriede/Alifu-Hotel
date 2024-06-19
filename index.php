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

// Handle review form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
  $userId = $_SESSION['user_id'];
  $rating = $_POST['rating'];
  $content = $_POST['content'];
  $sql = "INSERT INTO reviews (user_id, rating, content, approved) VALUES (?, ?, ?, 0)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('iis', $userId, $rating, $content);
  $stmt->execute();
  header('Location: index.php');
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Alifu | Book your stay today</title>
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

<div class="hero" id="hero">
  <h1>Discover Luxury at Alifu</h1>
  <p>Book your stay today and enjoy our world-class amenities</p>
  <a href="rooms.php" class="button">Explore Rooms</a>
</div>

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

  <section class="reviews-container" id="reviews">
    <div class="review-list">
      <h2>Guest Reviews</h2>
      <?php
      $reviewsResult = $conn->query("SELECT reviews.id, reviews.content, reviews.rating, users.name AS username 
                                     FROM reviews 
                                     JOIN users ON reviews.user_id = users.id 
                                     WHERE reviews.approved = 1");
      while ($review = $reviewsResult->fetch_assoc()) {
        echo '
        <div class="review-card">
          <h3>' . htmlspecialchars($review['username']) . '</h3>
          <p>Rating: ' . htmlspecialchars($review['rating']) . '/5</p>
          <p>' . htmlspecialchars($review['content']) . '</p>
        </div>';
      }
      ?>
    </div>
    <?php if (isset($_SESSION['user_id'])) { ?>
      <div class="review-form">
        <h3>Leave a Review</h3>
        <form action="index.php" method="post">
          <input type="hidden" name="submit_review" value="1">
          <label for="rating">Rating:</label>
          <select name="rating" id="rating" required>
            <option value="5">5</option>
            <option value="4">4</option>
            <option value="3">3</option>
            <option value="2">2</option>
            <option value="1">1</option>
          </select>
          <label for="content">Review:</label>
          <textarea name="content" id="content" required></textarea>
          <input type="submit" value="Submit" class="button">
        </form>
      </div>
    <?php } else { ?>
      <p class="login-prompt">Please <a href="login.php">login</a> to leave a review.</p>
    <?php } ?>
  </section>
</main>

<footer>
  <p>&copy; 2024 Alifu Hotel</p>
</footer>

<script src="script.js"></script>
</body>
</html>
