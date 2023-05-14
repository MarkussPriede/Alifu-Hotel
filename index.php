<?php
require_once "db_connection.php";
session_start();

// if the administrator value of the user is set to 1, set the session variable to "admin"
if(isset($_SESSION['user_id'])) {
  $query = "SELECT * FROM users WHERE id = '".$_SESSION['user_id']."'";
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($result);
  if($row['administrator'] == 1) {
    $_SESSION['admin'] = "admin";
  }
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
          <li><a href="#">Reviews</a></li>
          <li><a href="#">Contact</a></li>
          <li class="right-buttons">
            <?php if(!isset($_SESSION['user_id'])) { ?>
              <a href="login.php" class="loginregisterbutton">Login</a>
              <a href="register.php" class="loginregisterbutton">Register</a>
            <?php } else { 
                $query = "SELECT * FROM users WHERE id = '".$_SESSION['user_id']."'";
                $result = mysqli_query($conn, $query);
                $row = mysqli_fetch_assoc($result);
                if($row['administrator'] == 1) { ?>
                  <a href="admin.php" class="loginregisterbutton" >Admin</a>
                <?php } else { ?>
                  <a href="profile.php">My Profile</a>
                <?php } ?>
              <a href="backend/logout.php" class="loginregisterbutton">Logout</a>
            <?php } ?>
          </li>
        </ul>
      </nav>
      <div class="hero" id="hero">
        <h1>Discover Luxury at Alifu</h1>
        <p>Book your stay today and enjoy our world-class amenities</p>
        <a href="#" class="button">Book Now</a>
      </div>
    </header>
    <main>
      <section class="rooms">
        <h2>Our Rooms</h2>
        <div class="room-list">
          <div class="room">
            <img src="img/deluxe.jpg" alt="Room 1">
            <h3>Deluxe Room</h3>
            <p>Starting from $200/night</p>
            <a href="#" class="button">Book Now</a>
          </div>
          <div class="room">
            <img src="img/premium.jpg" alt="Room 2">
            <h3>Premium Room</h3>
            <p>Starting from $300/night</p>
            <a href="#" class="button">Book Now</a>
          </div>
          <div class="room">
            <img src="img/suite.jpg" alt="Room 3">
            <h3>Suite Room</h3>
            <p>Starting from $500/night</p>
            <a href="#" class="button">Book Now</a>
          </div>
        </div>
      </section>
      <section class="amenities">
        <h2>Our Amenities</h2>
        <div class="amenity-list">
          <div class="amenity">
            <img src="img/pool.png" alt="Pool">
            <h3>Swimming Pool</h3>
            <p>Relax and cool off in our Olympic-sized pool</p>
          </div>
          <div class="amenity">
            <img src="img/gym.png" alt="Gym">
            <h3>Fitness Center</h3>
            <p>Stay fit and healthy with our state-of-the-art gym</p>
          </div>
          <div class="amenity">
            <img src="img/spa.jpg" alt="Spa">
            <h3>Spa &amp; Wellness</h3>
            <p>Indulge in a luxurious spa treatment for ultimate relaxation</p>
          </div>
        </div>
      </section>
      <section class="reviews">
        <h2>Our Reviews</h2>
        <div class="review-list">
          <div class="review">
            <img src="img/user1.png" alt="User 1">
            <h3>Scarlett Johansson</h3>
            <p>"The hotel was amazing. The staff was very friendly and helpful. The room was spacious and clean."</p>
          </div>
          <div class="review">
            <img src="img/user2.png" alt="User 2"> 
            <h3>Will Smith</h3>
            <p>"I had a wonderful stay at this hotel. The amenities were top-notch and the room was very comfortable."</p>
          </div>
          <div class="review">
            <img src="img/user3.png" alt="User 3">
            <h3>Michael Johnson</h3>
            <p>"I highly recommend this hotel. The staff was very attentive and the facilities were excellent."</p>
          </div>
        </div>
      </section>
    </main>
    <footer>
      <p>&copy; 2023 Alifu Hotel. All rights reserved.</p>
    </footer>
    <script src="script.js"></script>
  </body>
</html>