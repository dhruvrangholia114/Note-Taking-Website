<?php
/**
 * Grant/Revoke Access Page
 * CS 215 - Web & Database Programming
 * Winter 2025
 */

// Start session
session_start();

//  Database connection
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Check if topic_id is provided
if (!isset($_GET['topic_id']) || !is_numeric($_GET['topic_id'])) {
    // Redirect to topic list
    header("Location: topiclist.php");
    exit();
}

$topicId = intval($_GET['topic_id']);

// Check if the user has access to this topic
if (!hasTopicAccess($conn, $_SESSION['user_id'], $topicId)) {
    // Redirect to topic list with an error
    header("Location: topiclist.php?error=access");
    exit();
}

// Initialize variables
$error = "";
$success = "";

// Process form submission for granting/revoking access
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user_id is provided
    if (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
        $userId = intval($_POST['user_id']);
        $grantAccess = isset($_POST['grant_access']) ? 1 : 0;
        
        try {
            // Check if access record already exists
            $stmt = $conn->prepare("SELECT access_id, status FROM access WHERE user_id = ? AND topic_id = ?");
            $stmt->execute([$userId, $topicId]);
            $existingAccess = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingAccess) {
                // Update existing access record
                $stmt = $conn->prepare("UPDATE access SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE access_id = ?");
                $stmt->execute([$grantAccess, $existingAccess['access_id']]);
            } else {
                // Insert new access record
                $stmt = $conn->prepare("INSERT INTO access (user_id, topic_id, status) VALUES (?, ?, ?)");
                $stmt->execute([$userId, $topicId, $grantAccess]);
            }
            
            // Success message
            $success = "Access updated successfully!";
            
            // Refresh the page to show the updated access
            header("Location: grant_access.php?topic_id=" . $topicId);
            exit();
        } catch(PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } elseif (isset($_POST['new_user'])) {
        // Handle adding a new user by screen name
        $screenName = trim($_POST['new_user']);
        
        if (empty($screenName)) {
            $error = "Screen name is required.";
        } else {
            try {
                // Find the user by screen name
                $stmt = $conn->prepare("SELECT user_id FROM user WHERE screen_name = ?");
                $stmt->execute([$screenName]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    // Grant access to the user
                    $stmt = $conn->prepare("INSERT INTO access (user_id, topic_id, status) 
                                           VALUES (?, ?, 1) 
                                           ON DUPLICATE KEY UPDATE status = 1, updated_at = CURRENT_TIMESTAMP");
                    $stmt->execute([$user['user_id'], $topicId]);
                    
                    // Success message
                    $success = "Access granted to " . htmlspecialchars($screenName) . " successfully!";
                    
                    // Refresh the page to show the updated access
                    header("Location: grant_access.php?topic_id=" . $topicId);
                    exit();
                } else {
                    $error = "User not found: " . htmlspecialchars($screenName);
                }
            } catch(PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}

// Get all users with their access status for this topic
$users = getUsersWithAccessStatus($conn, $topicId);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Access - Collaborative Note-Taking App</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body>

    <div class="main-container">

        <!-- Back Button -->
        <div class="back-container">
            <a href="topiclist.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Back</a>
        </div>

        <!-- Heading -->
        <div class="heading">Manage Access</div>
        
        <?php if (!empty($error)): ?>
            <div class="error-message" style="color: red; margin-bottom: 15px;"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success-message" style="color: green; margin-bottom: 15px;"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- User Access Cards -->
        <?php foreach ($users as $user): ?>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?topic_id=' . $topicId); ?>" method="post">
                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>" />
                <div class="card">
                    <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="User Avatar" class="avatar" />
                    <div class="user-info-grant">
                        <div class="username"><?php echo htmlspecialchars($user['screen_name']); ?></div>
                    </div>
                    <input type="checkbox" name="grant_access" class="checkbox-access" 
                           <?php echo $user['access_status'] ? 'checked' : ''; ?> 
                           onchange="this.form.submit()" />
                </div>
            </form>
        <?php endforeach; ?>

        <!-- Add New User -->
        <div class="add-user">
            <h3>Add New User</h3>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?topic_id=' . $topicId); ?>" method="post">
                <input type="text" name="new_user" class="input-field" placeholder="Enter username" /><br />
                <button type="submit" class="btn btn-success">Grant Access</button>
            </form>
        </div>

    </div>

</body>

</html>