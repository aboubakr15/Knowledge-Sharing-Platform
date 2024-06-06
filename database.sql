CREATE TABLE Users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reputations INT,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user'
);


CREATE TABLE Questions (
    question_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    reputations INT,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE Answers (
    answer_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    question_id INT,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reputations INT,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES Questions(question_id) ON DELETE CASCADE
);

CREATE TABLE Votes (
    vote_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    question_id INT,
    answer_id INT,
    type ENUM('upvote', 'downvote') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES Questions(question_id) ON DELETE CASCADE,
    FOREIGN KEY (answer_id) REFERENCES Answers(answer_id) ON DELETE CASCADE
);

CREATE TABLE Tags (
    tag_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE Question_Tags (
    question_id INT,
    tag_id INT,
    PRIMARY KEY (question_id, tag_id),
    FOREIGN KEY (question_id) REFERENCES Questions(question_id) ON DELETE NO ACTION,
    FOREIGN KEY (tag_id) REFERENCES Tags(tag_id) ON DELETE NO ACTION
);

CREATE TABLE Badges (
    badge_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    type VARCHAR(50) NOT NULL
);

CREATE TABLE User_Badges (
    user_id INT,
    badge_id INT,
    PRIMARY KEY (user_id, badge_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES Badges(badge_id) ON DELETE NO ACTION
);

CREATE TABLE Notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    targeted_user_id INT,
    notification_type ENUM('vote_on_question', 'vote_on_answer', 'answer', 'badge_earned') NOT NULL,
    source_id INT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(targeted_user_id) ON DELETE CASCADE
);
