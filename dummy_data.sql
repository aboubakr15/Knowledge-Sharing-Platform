-- Insert dummy data into Users table
INSERT INTO Users (username, email, password, photo, role, reputations) VALUES
('admin_user', 'admin@example.com', 'adminpass', 'admin.jpg', 'admin', 100),
('john_doe', 'john@example.com', 'johnpass', 'john.jpg', 'user', 50),
('jane_smith', 'jane@example.com', 'janepass', 'jane.jpg', 'user', 30);

-- Insert dummy data into Questions table
INSERT INTO Questions (user_id, title, body, reputations) VALUES
(1, 'How to install PHP on Windows?', 'I am trying to install PHP on my Windows system. Any tips or recommendations?', 10),
(2, 'What are the benefits of database normalization?', 'Can someone explain the advantages of database normalization and share best practices?', 20),
(3, 'Recommended Bootstrap tutorial for beginners?', 'Looking for a beginner-friendly Bootstrap tutorial. Any suggestions or resources?', 15);

-- Insert dummy data into Answers table
INSERT INTO Answers (user_id, question_id, body, reputations) VALUES
(2, 1, 'You can download the PHP installer from the official website and follow the installation instructions.', 5),
(3, 1, 'Consider using XAMPP or WAMP for an easier PHP installation process.', 3),
(1, 2, 'Database normalization helps in reducing redundancy and improving data integrity in the database.', 8),
(3, 2, 'Normalization involves organizing data into tables and defining relationships between them.', 4),
(1, 3, 'Bootstrap provides ready-to-use components and a responsive grid system for web development.', 6),
(2, 3, 'You can start with the official Bootstrap documentation and tutorials available on their website.', 2);

 

-- Insert dummy data into Tags table
INSERT INTO Tags (name) VALUES
('PHP'),
('Database Normalization'),
('Bootstrap');

-- Insert dummy data into Question_Tags table
INSERT INTO Question_Tags (question_id, tag_id) VALUES
(1, 1),
(2, 2),
(3, 3);

-- Insert dummy data into Badges table
INSERT INTO Badges (name, type) VALUES
('Bronze Badge', 'Beginner'),
('Silver Badge', 'Intermediate'),
('Gold Badge', 'Advanced');

-- Insert dummy data into User_Badges table
INSERT INTO User_Badges (user_id, badge_id) VALUES
(1, 3),
(2, 1),
(3, 2);
