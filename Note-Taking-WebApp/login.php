<?php
/**
 * Login Page
 * CS 215 - Web & Database Programming
 * Winter 2025
 */

// Start session
session_start();

//  Database connection
require_once 'db.php';

// Initialize variables
$error = "";

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to topic list page
    header("Location: topiclist.php");
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email and password
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $error = "Email and password are required.";
    } else {
        // Authenticate user
        $user = getUserByCredentials($conn, $_POST['email'], $_POST['password']);

        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['screen_name'] = $user['screen_name'];
            $_SESSION['avatar'] = $user['avatar'];

            // Redirect to topic list page
            header("Location: topiclist.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Collaborative Note-Taking App</title>
    <link rel="stylesheet" href="css/style.css" />
</head>

<body>
    <div class="container">
        <h2>Login to Your Account</h2>

        <?php if (!empty($error)): ?>
            <div class="error-message" style="color: red; margin-bottom: 15px;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="login-form">
            <!-- Email Input -->
            <div class="form-group">
                <label for="login-email">Email Address:</label>
                <input type="email" id="login-email" name="email" placeholder="Enter your email" required="required" />
                <span class="error"></span> <!-- For displaying error messages -->
            </div>

            <!-- Password Input -->
            <div class="form-group">
                <label for="login-password">Password:</label>
                <input type="password" id="login-password" name="password" placeholder="Enter your password"
                    required="required" />
                <span class="error"></span> <!-- For displaying error messages -->
            </div>

            <!-- Submit Button -->
            <button class="submit" type="submit">Log In</button>
        </form>
        <p class="fp">Forgot Password?</p>
        <p class="footer">Don't have an account? <a class="click" href="signup.php"><b>Sign Up</b></a></p>
    </div>
    <script src="js/login.js"></script>
</body>

</html>