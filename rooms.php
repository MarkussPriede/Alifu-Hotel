<?php
require_once 'db_connection.php';
session_start();

if(isset($_POST['reserve'])) {
    $user_id = $_SESSION['user_id'];
    $apartment_id = $_POST['apartment_id'];
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];
    $price = $_POST['price'];
    $status = 'pending';

    $sql = "SELECT * FROM reservations WHERE apartment_id='$apartment_id' AND ((check_in_date <= '$check_in_date' AND check_out_date >= '$check_in_date') OR (check_in_date <= '$check_out_date' AND check_out_date >= '$check_out_date'))";
    $result = $conn->query($sql);

    if($result->num_rows == 0) {
        $sql = "INSERT INTO reservations (user_id, apartment_id, check_in_date, check_out_date, price, status) VALUES ('$user_id', '$apartment_id', '$check_in_date', '$check_out_date', '$price', '$status')";
        if($conn->query($sql) === TRUE) {
            $reservation_success = "Reservation successful!";
        } else {
            $reservation_error = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $reservation_error = "The room is already reserved for the selected dates!";
    }
}

$sql = "SELECT * FROM apartments";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Rooms | Alifu Hotel</title>
    <link rel="stylesheet" href="css/rooms.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:400,500&display=swap">
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
        </ul>
      </nav>
    </header>
    <main>
      <section class="room-types">
        <h2>Choose a Room Type</h2>
        <div class="rooms-container">
          <?php
          require_once 'db_connection.php';
          $sql = "SELECT * FROM apartments";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  $image_url = $row["image_url"];
                  $name = $row["name"];
                  $type = $row["type"];
                  $price = $row["price"];
          ?>
          <div class="room-card">
            <div class="room-image" style="background-image: url(<?php echo $image_url; ?>)"></div>
            <h3 class="room-name"><?php echo $name; ?></h3>
            <p class="room-type"><?php echo $type; ?></p>
            <p class="room-price">$<?php echo $price; ?> / night</p>
            <form method="post" action="reservation.php">
              <input type="hidden" name="apartment_id" value="<?php echo $row["id"]; ?>">
              <label for="check-in-date">Check-in date:</label>
              <input type="date" id="check-in-date" name="check_in_date" required>
              <label for="check-out-date">Check-out date:</label>
              <input type="date" id="check-out-date" name="check_out_date" required>
              <button type="submit">Book Now</button>
            </form>
          </div>
          <?php
              }
          } else {
              echo "No rooms found.";
          }
          $conn->close();
          ?>
        </div>
      </section>
    </main>
    <footer>
      <p>&copy; 2023 Alifu Hotel. All rights reserved.</p>
    </footer>
  </body>
</html>


                   
