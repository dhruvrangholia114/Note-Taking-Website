<?php
/**
 * View Notes Page
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

// Get topic details
$topic = getTopicDetails($conn, $topicId);

if (!$topic) {
    // Redirect to topic list if topic not found
    header("Location: topiclist.php?error=notfound");
    exit();
}

// Get notes for this topic
$notes = getTopicNotes($conn, $topicId);

// Function to format date
function formatDate($dateStr) {
    $date = new DateTime($dateStr);
    return $date->format('j M, Y, H:i');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>View Notes - Collaborative Note-Taking App</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body class="notes-body">
    <div class="container-notes">
        <!-- Header Section (Search, Profile, Logout) -->
        <header class="notes-header">
            <div class="search-container">
                <input type="text" placeholder="Search Notes..." />
                <i class="fas fa-search"></i>
            </div>
            <div class="user-controls">
                <div class="profile">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['screen_name']); ?> ▼
                </div>
                <button class="logout-btn" onclick="window.location.href='logout.php';">
                    <i class="fas fa-power-off"></i> Logout
                </button>
            </div>
        </header>

        <!-- Topic Title with Back Button -->
        <div class="topic-title">
            <button class="back-btn" onclick="window.location.href='topiclist.php'">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <h2><?php echo htmlspecialchars($topic['title']); ?></h2>
            <p><strong>Posted:</strong> <?php echo formatDate($topic['created_at']); ?> | 
               <strong>Last Updated:</strong> <?php echo $topic['last_updated'] ? formatDate($topic['last_updated']) : 'Never'; ?>
            </p>
        </div>

        <!-- Notes Section -->
        <section class="notes-section" id="notes-container">
            <?php if (empty($notes)): ?>
                <p style="text-align: center;" id="no-notes-message">No notes found. Be the first to add a note!</p>
            <?php else: ?>
                <?php foreach ($notes as $note): ?>
                    <div class="note">
                        <div class="note-header">
                            <div class="user-info">
                                <div class="user-avatar">
                                    <img src="<?php echo htmlspecialchars($note['avatar']); ?>" alt="User Avatar" />
                                </div>
                                <span><?php echo htmlspecialchars($note['screen_name']); ?></span>
                            </div>
                            <p><strong>Posted:</strong> <?php echo formatDate($note['created_at']); ?></p>
                        </div>
                        <p class="note-text"><?php echo nl2br(htmlspecialchars($note['content'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <!-- Add New Note Section -->
        <div class="add-note">
            <div id="error-message" class="error-message" style="color: red; margin-bottom: 15px; display: none;"></div>
            <div id="success-message" class="success-message" style="color: green; margin-bottom: 15px; display: none;"></div>
            
            <form id="add-note-form">
                <textarea id="note-content" name="note_content" placeholder="Write a new note..."></textarea>
                <p class="info-text">The max number of characters input is <b>1500</b>.</p>
                <button type="button" id="add-note-btn" class="add-note-btn">Add Note</button>
            </form>
        </div>

    </div>

    <script src="js/view_notes_ajax.js"></script>
</body>

</html>