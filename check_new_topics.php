<?php
/**
 * AJAX endpoint to check for new topics
 * CS 215 - Web & Database Programming
 * Winter 2025
 */

// Start session
session_start();

// Database connection
require_once 'db.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

// Check if we have the last topic ID
if (!isset($_GET['last_topic_id']) || !is_numeric($_GET['last_topic_id'])) {
    echo json_encode(['error' => 'Invalid last topic ID']);
    exit();
}

$lastTopicId = intval($_GET['last_topic_id']);

try {
    // Query for new topics that the user has access to
    $query = "SELECT t.topic_id, t.title, t.created_at, 
              u.screen_name AS creator_name, u.avatar AS creator_avatar,
              (SELECT MAX(n.created_at) FROM notes n WHERE n.topic_id = t.topic_id) AS last_updated,
              (SELECT COUNT(*) FROM notes n WHERE n.topic_id = t.topic_id) AS notes_count
              FROM topic t
              JOIN access a ON t.topic_id = a.topic_id
              JOIN user u ON t.creator_id = u.user_id
              WHERE a.user_id = ? AND a.status = 1 AND t.topic_id > ?
              ORDER BY t.created_at DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id'], $lastTopicId]);
    $newTopics = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format dates for the response
    foreach ($newTopics as &$topic) {
        $topic['created_at_formatted'] = formatDate($topic['created_at']);
        $topic['last_updated_formatted'] = $topic['last_updated'] ? formatDate($topic['last_updated']) : 'Never';
    }
    
    // Return the new topics as JSON
    echo json_encode(['topics' => $newTopics]);
    
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

/**
 * Helper function to format dates
 */
function formatDate($dateStr) {
    $date = new DateTime($dateStr);
    return $date->format('j M, Y, H:i');
}
?>