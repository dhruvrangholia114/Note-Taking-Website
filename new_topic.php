<?php
/**
 * Create New Topic Page
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

// Initialize variables
$error = "";
$success = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate topic name
    if (empty($_POST['topicName'])) {
        $error = "Topic name is required.";
    } elseif (strlen($_POST['topicName']) > 256) {
        $error = "Topic name must be 256 characters or fewer.";
    } else {
        try {
            // Begin transaction
            $conn->beginTransaction();

            // Insert new topic
            $stmt = $conn->prepare("INSERT INTO topic (title, creator_id) VALUES (?, ?)");
            $stmt->execute([$_POST['topicName'], $_SESSION['user_id']]);

            // Get the new topic ID
            $topicId = $conn->lastInsertId();

            // Grant access to the creator
            $stmt = $conn->prepare("INSERT INTO access (user_id, topic_id, status) VALUES (?, ?, 1)");
            $stmt->execute([$_SESSION['user_id'], $topicId]);

            // Commit transaction
            $conn->commit();

            // Success message
            $success = "Topic created successfully!";

            // Redirect to topic list
            header("Location: topiclist.php");
            exit();
        } catch (PDOException $e) {
            // Rollback transaction
            $conn->rollBack();
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>New Topic - Collaborative Note-Taking App</title>

    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body>

    <div class="wrapper">
        <div class="card-newtopic" style="padding-bottom: 25px; height: auto; width: 450px;">

            <!-- Back Button -->
            <div class="back-container-topic">
                <a href="topiclist.php" class="btn2 btn-primary"><i class="fas fa-arrow-left"></i> Back</a>
            </div>

            <!-- Title -->
            <h2 class="title">Create a New Topic</h2>

            <?php if (!empty($error)): ?>
                <div class="error-message" style="color: red; margin-bottom: 15px;"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="success-message" style="color: green; margin-bottom: 15px;"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <!-- Topic Name Input -->
                <label for="topicName" class="label2">Topic Name</label>
                <input type="text" id="topicName" name="topicName" class="input-field-new"
                    placeholder="Type your topic..." />

                <p class="info-text">The max number of characters input is <b>256</b>.</p>

                <!-- Button Container -->
                <div class="button-container">
                    <button type="button" class="cancel-btn"
                        onclick="window.location.href='topiclist.php'">Cancel</button>
                    <button type="submit" class="create-btn">Create</button>
                </div>
            </form>

        </div>
    </div>
    <script src="js/new_topic.js"></script>
</body>

</html>