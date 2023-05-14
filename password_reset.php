<?php
session_start();

// Include the database connection file
require_once "db_connection.php";

// Define variables and initialize with empty values
$email = "";
$email_err = "";
$password = "";
$password_err = "";
$confirm_password = "";
$confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a new password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the new password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password != $confirm_password) {
            $confirm_password_err = "The new passwords do not match.";
        }
    }

    // Check input errors before updating the database
    if (empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        
        // Prepare an update statement
        $sql = "UPDATE users SET password = ? WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ss", $hashed_password, $email);
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Password updated successfully. Destroy the session and redirect to login page
                session_destroy();
                header("location: login.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            
            $stmt->close();
        }
    }
    
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Your Password | Alifu</title>
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
            <h2>Reset Your Password</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
                    <span class="help-block"><?php echo $email_err; ?></span>
                  </div>
                  <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                  <label>New Password</label>
                  <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                  <span class="help-block"><?php echo $password_err; ?></span>
                </div>
                  <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                  <label>Confirm New Password</label>
                  <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                  <span class="help-block"><?php echo $confirm_password_err; ?></span>
                </div>
                  <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Reset Password">
                  <a class="btn btn-link" href="index.php">Cancel</a>
                </div>
                </form>
                </section>
                </main>
                  <footer>
                    <p>© 2023 Alifu Hotel. All rights reserved.</p>
                </footer>
                </body>
                  </html>