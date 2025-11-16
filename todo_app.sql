CREATE DATABASE IF NOT EXISTS todoapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE todoapp;

	CREATE TABLE IF NOT EXISTS users (
	  id INT AUTO_INCREMENT PRIMARY KEY,
	  username VARCHAR(100) NOT NULL UNIQUE,
	  password VARCHAR(255) NOT NULL,
	  email VARCHAR(255) UNIQUE,
	  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
	) ENGINE=InnoDB;

	CREATE TABLE IF NOT EXISTS tasks (
	  id INT AUTO_INCREMENT PRIMARY KEY,
	  user_id INT NOT NULL,
	  title VARCHAR(255) NOT NULL,
	  description TEXT,
	  due_date DATE NULL,
	  status ENUM('pending','in_progress','completed') DEFAULT 'pending',
	  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
	) ENGINE=InnoDB;