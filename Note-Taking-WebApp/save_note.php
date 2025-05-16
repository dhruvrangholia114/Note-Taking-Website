<?php
/**
 * AJAX endpoint to save a new note
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

// Check if we have the necessary data
if ($_SERVER["REQUEST_METHOD"] != "POST" || 
    !isset($_POST['topic_id']) || !is_numeric($_POST['topic_id']) || 
    !isset($_POST['note_content']) || empty($_POST['note_content'])) {
    echo json_encode(['error' => 'Invalid request data']);
    exit();
}

$topicId = intval($_POST['topic_id']);
$content = $_POST['note_content'];

// Check if the user has access to this topic
if (!hasTopicAccess($conn, $_SESSION['user_id'], $topicId)) {
    echo json_encode(['error' => 'Access denied']);
    exit();
}

// Validate content length
if (strlen($content) > 1500) {
    echo json_encode(['error' => 'Note content must be 1500 characters or fewer']);
    exit();
}

try {
    // Begin transaction
    $conn->beginTransaction();
    
    $timestamp = date('Y-m-d H:i:s');
    
    // Insert new note
    $stmt = $conn->prepare("INSERT INTO notes (topic_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$topicId, $_SESSION['user_id'], $content]);
    
    // Get new note ID
    $noteId = $conn->lastInsertId();
    
    // Get all notes created after our timestamp
    $query = "SELECT n.note_id, n.content, n.created_at, 
              u.screen_name, u.avatar
              FROM notes n
              JOIN user u ON n.user_id = u.user_id
              WHERE n.topic_id = ? AND n.created_at >= ?
              ORDER BY n.created_at ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([$topicId, $timestamp]);
    $newNotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format dates for the response
    foreach ($newNotes as &$note) {
        $note['created_at_formatted'] = formatDate($note['created_at']);
    }
    
    // Commit transaction
    $conn->commit();
    
    // Return success with the new notes
    echo json_encode([
        'success' => true,
        'notes' => $newNotes
    ]);
    
} catch(PDOException $e) {
    // Rollback transaction
    $conn->rollBack();
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