<?php
/**
 * Signup Page
 * CS 215 - Web & Database Programming
 * Winter 2025
 */

//  Database connection
require_once 'db.php';

// Initialize variables
$error = "";
$success = false;

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate required fields
    if (
        empty($_POST['email']) || empty($_POST['screenname']) || empty($_POST['password']) ||
        empty($_POST['confirm-password']) || empty($_POST['dob']) || empty($_POST['selected_avatar'])
    ) {
        $error = "All fields are required.";
    }
    // Validate email format
    elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    }
    // Check if passwords match
    elseif ($_POST['password'] !== $_POST['confirm-password']) {
        $error = "Passwords do not match.";
    }
    // Check if email already exists
    elseif (emailExists($conn, $_POST['email'])) {
        $error = "Email already exists.";
    }
    // Check if screen name already exists
    elseif (screenNameExists($conn, $_POST['screenname'])) {
        $error = "Screen name already exists.";
    } else {
        // Get the selected avatar path
        $avatar_path = 'uploads/avatars/' . $_POST['selected_avatar'];

        try {
            // Insert user into database with the avatar path
            $stmt = $conn->prepare("INSERT INTO user (email, screen_name, avatar, date_of_birth, password) 
                                    VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $_POST['email'],
                $_POST['screenname'],
                $avatar_path,
                $_POST['dob'],
                $_POST['password']
            ]);

            if ($result) {
                $success = true;
                // Redirect to login page
                header("Location: login.php");
                exit();
            } else {
                $error = "Failed to create account. Please try again.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Define available avatars
$avatars = [
    'avatar1.jpg',
    'avatar2.jpg',
    'avatar3.jpg',
    'avatar4.jpg',
    'avatar5.jpg',
    'avatar6.jpg',
    'avatar7.jpg',
    'avatar8.jpg',
    'avatar9.jpg',
    'avatar10.jpg',
    'avatar11.jpg',
    'avatar12.jpg',
];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Account - Collaborative Note-Taking App</title>
    <link rel="stylesheet" href="css/style.css" />
</head>

<body>
    <div class="signup-container">
        <h2>Create Your Account</h2>

        <?php if (!empty($error)): ?>
            <div class="error-message" style="color: red; margin-bottom: 15px;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form id="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <!-- Email Input -->
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required="required"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
                <span class="error"></span>
            </div>

            <!-- Screenname Input -->
            <div class="form-group">
                <label for="screenname">Screenname</label>
                <input type="text" id="screenname" name="screenname" placeholder="Enter your screenname"
                    required="required"
                    value="<?php echo isset($_POST['screenname']) ? htmlspecialchars($_POST['screenname']) : ''; ?>" />
                <span class="error"></span>
            </div>

            <!-- Avatar Selection -->
            <div class="form-group">
                <label>Select Avatar</label>
                <div class="avatar-selection">
                    <?php foreach ($avatars as $index => $avatar): ?>
                        <div class="avatar-option">
                            <input type="radio" id="avatar<?php echo $index; ?>" name="selected_avatar"
                                value="<?php echo $avatar; ?>" <?php echo $index === 0 ? 'checked' : ''; ?> />
                            <label for="avatar<?php echo $index; ?>">
                                <img src="uploads/avatars/<?php echo $avatar; ?>" alt="Avatar <?php echo $index + 1; ?>" />
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <span class="error"></span>
            </div>

            <!-- Date of Birth Input -->
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" required="required"
                    value="<?php echo isset($_POST['dob']) ? htmlspecialchars($_POST['dob']) : ''; ?>" />
                <span class="error"></span>
            </div>

            <!-- Password Input -->
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password"
                    required="required" />
                <span class="error"></span>
            </div>

            <!-- Confirm Password Input -->
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password"
                    required="required" />
                <span class="error"></span>
            </div>

            <!-- Terms and Conditions Checkbox -->
            <div class="form-group">
                <label>
                    <input type="checkbox" name="terms" required="required" />
                    I agree to the <a class="click" href="#"><b>Terms and Conditions</b></a>
                </label>
            </div>

            <!-- Submit Button -->
            <button class="submit" type="submit">Sign Up</button>

            <!-- Footer Link -->
            <p class="footer">Already have an account? <a class="click" href="login.php"><b>Log In</b></a></p>
        </form>
    </div>
    <script src="js/signup.js"></script>
</body>

</html>