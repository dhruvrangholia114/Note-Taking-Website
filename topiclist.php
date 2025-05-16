<?php
/**
 * Topic List Page
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

// Get topics that the user has access to
$topics = getUserTopics($conn, $_SESSION['user_id']);

// Function to format date
function formatDate($dateStr) {
    $date = new DateTime($dateStr);
    return $date->format('j M, Y, H:i');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Listed Topics - Collaborative Note-Taking App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css" />
</head>

<body class="topiclistbody">
    <div class="container-topiclist">
        <!-- Header Section -->
        <header class="topiclisthead">
            <div class="search-container">
                <input type="text" placeholder="Search Topics..." />
                <i class="fas fa-search"></i>
            </div>
            <div class="user-controls">
                <div class="profile">
                    <i class="fas fa-user"></i><?php echo htmlspecialchars($_SESSION['screen_name']); ?> ▼
                </div>
                <button class="logout-btn" onclick="window.location.href='logout.php';">
                    <i class="fas fa-power-off"></i>Logout
                </button>
            </div>
        </header>

        <!-- Topics List Section -->
        <section class="topics">
            <h2>Topics</h2>

            <div id="topics-container">
                <?php if (empty($topics)): ?>
                    <p style="text-align: center;">No topics found. Create a new topic to get started!</p>
                <?php else: ?>
                    <?php foreach ($topics as $topic): ?>
                        <div class="topic-container" data-topic-id="<?php echo $topic['topic_id']; ?>">
                            <div class="topic-details">
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <img src="<?php echo htmlspecialchars($topic['creator_avatar']); ?>" alt="Avatar" />
                                    </div>
                                    <span><?php echo htmlspecialchars($topic['creator_name']); ?></span>
                                </div>
                                <h3><?php echo htmlspecialchars($topic['title']); ?></h3>
                                <p><strong>Posted:</strong> <?php echo formatDate($topic['created_at']); ?> | 
                                <strong>Last Updated:</strong> <?php echo $topic['last_updated'] ? formatDate($topic['last_updated']) : 'Never'; ?>
                                </p>
                                <p><strong>Notes:</strong> <?php echo $topic['notes_count']; ?></p>
                            </div>
                            <div class="topic-actions">
                                <button class="view-btn" onclick="window.location.href='view_notes.php?topic_id=<?php echo $topic['topic_id']; ?>';">View Notes</button>
                                <button class="access-btn" onclick="window.location.href='grant_access.php?topic_id=<?php echo $topic['topic_id']; ?>';">Access</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Create New Topic Button -->
        <div class="create-topic">
            <button onclick="window.location.href='new_topic.php';"><b>+ </b>Create New Topic</button>
        </div>
    </div>

    <script src="js/topiclist.js"></script>
</body>

</html>