<?php
/**
 * Database Connection File
 * CS 215 - Web & Database Programming
 * Winter 2025
 */

// Database configuration
$db_host = 'localhost';
$db_name = 'drr433'; // database name
$db_user = 'drr433'; // username
$db_pass = 'Dhruv@9016443455'; // database password

// Database Connection
$conn = null;

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

/**
 * Helper function to get user by email and password
 * @param PDO $conn Database connection
 * @param string $email User email
 * @param string $password User password
 * @return array|null User data or null if not found
 */
function getUserByCredentials($conn, $email, $password) {
    try {
        $stmt = $conn->prepare("SELECT user_id, screen_name, avatar FROM user WHERE email = ? AND password = ?");
        $stmt->execute([$email, $password]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return null;
    }
}

/**
 * Helper function to check if an email already exists
 * @param PDO $conn Database connection
 * @param string $email Email to check
 * @return bool True if email exists, false otherwise
 */
function emailExists($conn, $email) {
    try {
        $stmt = $conn->prepare("SELECT user_id FROM user WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    } catch(PDOException $e) {
        return false;
    }
}

/**
 * Helper function to check if a screen name already exists
 * @param PDO $conn Database connection
 * @param string $screenName Screen name to check
 * @return bool True if screen name exists, false otherwise
 */
function screenNameExists($conn, $screenName) {
    try {
        $stmt = $conn->prepare("SELECT user_id FROM user WHERE screen_name = ?");
        $stmt->execute([$screenName]);
        return $stmt->rowCount() > 0;
    } catch(PDOException $e) {
        return false;
    }
}

/**
 * Helper function to check if a user has access to a topic
 * @param PDO $conn Database connection
 * @param int $userId User ID
 * @param int $topicId Topic ID
 * @return bool True if user has access, false otherwise
 */
function hasTopicAccess($conn, $userId, $topicId) {
    try {
        $stmt = $conn->prepare("SELECT access_id FROM access WHERE user_id = ? AND topic_id = ? AND status = 1");
        $stmt->execute([$userId, $topicId]);
        return $stmt->rowCount() > 0;
    } catch(PDOException $e) {
        return false;
    }
}

/**
 * Helper function to get topics that a user has access to
 * @param PDO $conn Database connection
 * @param int $userId User ID
 * @return array Array of topics
 */
function getUserTopics($conn, $userId) {
    try {
        $query = "SELECT t.topic_id, t.title, t.created_at, 
                u.screen_name AS creator_name, u.avatar AS creator_avatar,
                (SELECT MAX(n.created_at) FROM notes n WHERE n.topic_id = t.topic_id) AS last_updated,
                (SELECT COUNT(*) FROM notes n WHERE n.topic_id = t.topic_id) AS notes_count
                FROM topic t
                JOIN access a ON t.topic_id = a.topic_id
                JOIN user u ON t.creator_id = u.user_id
                WHERE a.user_id = ? AND a.status = 1
                ORDER BY t.created_at DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return [];
    }
}

/**
 * Helper function to get topic details
 * @param PDO $conn Database connection
 * @param int $topicId Topic ID
 * @return array|null Topic data or null if not found
 */
function getTopicDetails($conn, $topicId) {
    try {
        $query = "SELECT t.topic_id, t.title, t.created_at,
                (SELECT MAX(n.created_at) FROM notes n WHERE n.topic_id = t.topic_id) AS last_updated
                FROM topic t
                WHERE t.topic_id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$topicId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return null;
    }
}

/**
 * Helper function to get notes for a topic
 * @param PDO $conn Database connection
 * @param int $topicId Topic ID
 * @return array Array of notes
 */
function getTopicNotes($conn, $topicId) {
    try {
        $query = "SELECT n.note_id, n.content, n.created_at, 
                u.screen_name, u.avatar
                FROM notes n
                JOIN user u ON n.user_id = u.user_id
                WHERE n.topic_id = ?
                ORDER BY n.created_at ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$topicId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return [];
    }
}

/**
 * Helper function to get all users with their access status for a topic
 * @param PDO $conn Database connection
 * @param int $topicId Topic ID
 * @return array Array of users with access status
 */
function getUsersWithAccessStatus($conn, $topicId) {
    try {
        $query = "SELECT u.user_id, u.screen_name, u.avatar,
                IFNULL(a.status, 0) AS access_status
                FROM user u
                LEFT JOIN access a ON u.user_id = a.user_id AND a.topic_id = ?
                ORDER BY u.screen_name";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$topicId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return [];
    }
}