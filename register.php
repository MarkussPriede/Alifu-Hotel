<?php
require_once('db_connection.php');

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $personal_id = $_POST['personal_id'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Validate personal ID
    if (!preg_match('/^\d{6}-\d{5}$/', $personal_id)) {
      echo "Error: Personal ID should be in the format '123456-78910'.";
  }
    // Validate password length
    elseif (strlen($password) < 8) {
        $error_message = "Password should be at least 8 characters long.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, surname, email, personal_id, phone_number, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $surname, $email, $personal_id, $phone_number, $hashed_password);

        if ($stmt->execute()) {
            header('Location: login.php');
            exit();
        } else {
            $error_message = "Error: " . $stmt->error;
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Alifu</title>
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
    <section class="register">
        <h2>Create a New Account</h2>
        <?php if (isset($error_message)) { ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php } ?>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Surname</label>
                <input type="text" name="surname" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Personal ID</label>
                <input type="text" name="personal_id" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_number" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="register">Register</button>
</form>
<p class="message">Already have an account? <a href="login.php">Login here</a></p>
</section>
</main>
<footer>
    <p>&copy; 2023 Alifu Hotel. All rights reserved.</p>
</footer>
</body>
</html>