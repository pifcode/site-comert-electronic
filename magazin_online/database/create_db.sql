-- Crearea bazei de date
CREATE DATABASE IF NOT EXISTS pescarul_expert;
USE pescarul_expert;

-- Crearea tabelului pentru utilizatori
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(15),
    address TEXT
);
