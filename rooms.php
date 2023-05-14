<?php
require_once "db_connection.php";

// Get the sorting option and order from the query string
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

// Prepare the SQL query based on the sorting option and order
if ($sort === 'price') {
    $query = "SELECT * FROM apartments ORDER BY price $order";
} elseif ($sort === 'name') {
    $query = "SELECT * FROM apartments ORDER BY name $order";
} else {
    $query = "SELECT * FROM apartments";
}

// Retrieve apartments from the database
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rooms | Alifu Hotel</title>
  <link rel="stylesheet" href="css/rooms.css">
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
      </ul>
    </nav>
  </header>

  <main class="main-container">
    <h2>Our Rooms</h2>

    <div class="sort-form">
      <form action="rooms.php" method="GET">
        <label for="sort">Sort By:</label>
        <select name="sort" id="sort" onchange="this.form.submit()">
          <option value="">-- Select --</option>
          <option value="name" <?php if ($sort === 'name') echo 'selected'; ?>>Name</option>
          <option value="price" <?php if ($sort === 'price') echo 'selected'; ?>>Price</option>
        </select>
        <label for="order">Order:</label>
        <select name="order" id="order" onchange="this.form.submit()">
          <option value="asc" <?php if ($order === 'asc') echo 'selected'; ?>>Ascending</option>
          <option value="desc" <?php if ($order === 'desc') echo 'selected'; ?>>Descending</option>
        </select>
      </form>
    </div>

    <div class="rooms-container">
      <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <div class="room-card">
          <img src="<?php echo $row['image_url']; ?>" alt="<?php echo $row['name']; ?>">
          <h3><?php echo $row['name']; ?></h3>
          <p><?php echo $row['description']; ?></p>
          <p>Price: $<?php echo $row['price']; ?> per night</p>
          <a href="reservation.php?apartment_id=<?php echo $row['id']; ?>" class="btn">Reserve Now</a>
        </div>
      <?php } ?>
    </div>
  </main>

  <footer>
    <p>&copy; 2023 Alifu Hotel. All rights reserved.</p>
  </footer>
</body>