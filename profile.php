<?php
session_start();

require_once('db_connection.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user_data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $password = $_POST['password'];
  $query = "SELECT * FROM users WHERE id = '$user_id'";
  $result = mysqli_query($conn, $query);
  $user = mysqli_fetch_assoc($result);
  if (password_verify($password, $user['password'])) {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $personal_id = $_POST['personal_id'];
    $phone_number = $_POST['phone_number'];
    $new_password = $_POST['new_password'];
    if (!empty($new_password)) {
      $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
      $query = "UPDATE users SET name='$name', surname='$surname', email='$email', personal_id='$personal_id', phone_number='$phone_number', password='$password_hash' WHERE id='$user_id'";
    } else {
      $query = "UPDATE users SET name='$name', surname='$surname', email='$email', personal_id='$personal_id', phone_number='$phone_number' WHERE id='$user_id'";
    }
    mysqli_query($conn, $query);
    header("Location: profile.php");
    exit();
  } else {
    $error_message = "Incorrect password";
  }
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Profile | Alifu</title>
    <link rel="stylesheet" href="css/profile.css">
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
          <li><a href="backend/logout.php">Logout</a></li>
        </ul>
      </nav>
    </header>
    <main>
      <section class="profile">
        <h2>Edit profile</h2>
        <?php
          // Fetch user data from the database
          $query = "SELECT * FROM users WHERE id = ".$_SESSION['user_id'];
          $result = mysqli_query($conn, $query);
          $row = mysqli_fetch_assoc($result);
        ?>
        <form action="profile.php" method="POST">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?php echo $row['name']; ?>" required>
          </div>
          <div class="form-group">
            <label for="surname">Surname</label>
            <input type="text" name="surname" id="surname" value="<?php echo $row['surname']; ?>" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo $row['email']; ?>" required>
          </div>
          <div class="form-group">
            <label for="personal_id">Personal ID</label>
            <input type="text" name="personal_id" id="personal_id" value="<?php echo $row['personal_id']; ?>" required>
          </div>
          <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" name="phone_number" id="phone_number" value="<?php echo $row['phone_number']; ?>" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
          </div>
          <button type="submit" name="update">Update</button>
          <button type="submit" name="delete" id="buttondelete">Delete Profile</button>
        </form>
      </section>
    </main>
    <footer>
      <p>&copy; 2023 Alifu Hotel. All rights reserved.</p>
    </footer>
  </body>
</html>








