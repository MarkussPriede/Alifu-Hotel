<?php
require_once 'db_connection.php';
session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: index.php');
            exit();
        } else {
            $login_error = "Wrong email or password";
        }
    } else {
        $login_error = "Wrong email or password";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Login | Alifu Hotel</title>
    <link rel="stylesheet" href="css/loginregister.css">
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
        </ul>
      </nav>
    </header>
    <main>
      <section class="login">
        <h2>Login to Your Account</h2>
        <form action="login.php" method="post">
            <?php if (isset($login_error)): ?>
            <div class="error"><?php echo $login_error; ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
        <p class="message">Not registered? <a href="register.php">Create an account</a></p>
        <p class="message">Forgot password? <a href="password_reset.php">Reset password</a></p>
      </section>
    </main>
    <footer>
      <p>&copy; 2023 Alifu Hotel. All rights reserved.</p>
    </footer>
  </body>
</html>
