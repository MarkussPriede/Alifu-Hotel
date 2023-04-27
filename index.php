<?php
require_once "db_connection.php";
session_start();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Alifu | Book your stay today</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:400,500&display=swap">
	<script src="script.js"></script>
  </head>
  <body>
    <header>
    <nav>
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="#">Rooms</a></li>
      <li><a href="#">Amenities</a></li>
      <li><a href="#">Reviews</a></li>
      <li><a href="#">Contact</a></li>
      <?php if(!isset($_SESSION['user_id'])) { ?>
      <li><a href="login.php" class="loginregisterbutton">Login</a></li>
      <li><a href="register.php" class="loginregisterbutton">Register</a></li>
      <?php } else { 
          $query = "SELECT * FROM users WHERE id = '".$_SESSION['user_id']."'";
          $result = mysqli_query($conn, $query);
          $row = mysqli_fetch_assoc($result);
          if($row['administrator'] == 1) { ?>
            <li><a href="admin.php" class="loginregisterbutton" >Admin</a></li>
          <?php } else { ?>
            <li><a href="profile.php">My Profile</a></li>
          <?php } ?>
      <li><a href="backend/logout.php" class="loginregisterbutton">Logout</a></li>
      <?php } ?>
    </ul>
      </nav>
      <div class="hero">
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
            <img src="room1.jpg" alt="Room 1">
            <h3>Deluxe Room</h3>
            <p>Starting from $200/night</p>
            <a href="#" class="button">Book Now</a>
          </div>
          <div class="room">
            <img src="room2.jpg" alt="Room 2">
            <h3>Premium Room</h3>
            <p>Starting from $300/night</p>
            <a href="#" class="button">Book Now</a>
          </div>
          <div class="room">
            <img src="room3.jpg" alt="Room 3">
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
            <img src="pool.jpg" alt="Pool">
            <h3>Swimming Pool</h3>
            <p>Relax and cool off in our Olympic-sized pool</p>
          </div>
          <div class="amenity">
            <img src="gym.jpg" alt="Gym">
            <h3>Fitness Center</h3>
            <p>Stay fit and healthy with our state-of-the-art gym</p>
          </div>
          <div class="amenity">
            <img src="spa.jpg" alt="Spa">
            <h3>Spa &amp; Wellness</h3>
            <p>Indulge in a luxurious spa treatment for ultimate relaxation</p>
          </div>
        </div>
      </section>
      <section class="reviews">
        <h2>Our Reviews</h2>
        <div class="review-list">
          <div class="review">
            <img src="user1.jpg" alt="User 1">
            <h3>John Doe</h3>
            <p>"The hotel was amazing. The staff was very friendly and helpful. The room was spacious and clean."</p>
          </div>
          <div class="review">
            <img src="user2.jpg" alt="User 2"> 
            <h3>Jane Smith</h3>
            <p>"I had a wonderful stay at this hotel. The amenities were top-notch and the room was very comfortable."</p>
          </div>
          <div class="review">
            <img src="user3.jpg" alt="User 3">
            <h3>Michael Johnson</h3>
            <p>"I highly recommend this hotel. The staff was very attentive and the facilities were excellent."</p>
          </div>
        </div>
      </section>
    </main>
    <footer>
      <p>&copy; 2023 Alifu. All rights reserved.</p>
    </footer>
  </body>
</html>