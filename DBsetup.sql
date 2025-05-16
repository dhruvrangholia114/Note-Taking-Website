-- Database setup for Collaborative Note-Taking App
-- CS 215 - Web & Database Programming
-- Winter 2025

-- Drop tables if they exist
DROP TABLE IF EXISTS access;
DROP TABLE IF EXISTS notes;
DROP TABLE IF EXISTS topic;
DROP TABLE IF EXISTS user;

-- Create User Table
CREATE TABLE user (
    user_id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    first_name VARCHAR(50),    
    last_name VARCHAR(50),    
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    screen_name VARCHAR(50) UNIQUE NOT NULL,
    avatar VARCHAR(255) NOT NULL,
    date_of_birth DATE,       
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create Topic Table
CREATE TABLE topic (
    topic_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(256) NOT NULL,
    creator_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES user(user_id)
);

-- Create Notes Table
CREATE TABLE notes (
    note_id INT AUTO_INCREMENT PRIMARY KEY,
    topic_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (topic_id) REFERENCES topic(topic_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

-- Create Access Table
CREATE TABLE access (
    access_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    topic_id INT NOT NULL,
    status TINYINT NOT NULL DEFAULT 1,
    granted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (topic_id) REFERENCES topic(topic_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    UNIQUE (user_id, topic_id)
);

-- Sample data
-- Sample users
INSERT INTO user (email, screen_name, avatar, date_of_birth, password)
VALUES 
    ('dhruv@gmail.com', 'Dhruv_114', 'uploads/avatars/avatar1.jpg', '2004-04-11', 'dhruv114'),
    ('vaidik@gmail.com', 'Vaidik_69', 'uploads/avatars/avatar2.jpg', '2005-11-08', 'vaidik140'),
    ('smit@gmail.com', 'Smit_908', 'uploads/avatars/avatar3.jpg', '2004-08-01', 'smit89');

-- Sample topic
INSERT INTO topic (title, creator_id)
VALUES ('Introduction to Web Development', 1);

-- Grant access to users
INSERT INTO access (user_id, topic_id, status)
VALUES 
    (1, 1, 1),
    (2, 1, 1);

-- Sample notes
INSERT INTO notes (topic_id, user_id, content)
VALUES 
    (1, 1, 'This is a note about Web Development fundamentals...'),
    (1, 2, 'CSS animations can enhance user experience...'),
    (1, 1, 'JavaScript is essential for interactive web apps...');