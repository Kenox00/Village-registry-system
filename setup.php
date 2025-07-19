<?php
// Database setup script
// Run this file once to create the database and tables

$host = 'localhost';
$username = 'root';
$password = '';

// Create connection without database selection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS village_register";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db('village_register');

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'officer') DEFAULT 'officer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Users table created successfully<br>";
} else {
    echo "Error creating users table: " . $conn->error . "<br>";
}

// Create birth_records table
$sql = "CREATE TABLE IF NOT EXISTS birth_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    child_name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    mother_name VARCHAR(100) NOT NULL,
    father_name VARCHAR(100) NOT NULL,
    village VARCHAR(100) NOT NULL,
    sector VARCHAR(100) NOT NULL,
    registered_by INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (registered_by) REFERENCES users(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Birth records table created successfully<br>";
} else {
    echo "Error creating birth_records table: " . $conn->error . "<br>";
}

// Create death_records table
$sql = "CREATE TABLE IF NOT EXISTS death_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deceased_name VARCHAR(100) NOT NULL,
    dod DATE NOT NULL,
    cause_of_death TEXT NOT NULL,
    family_contact VARCHAR(100) NOT NULL,
    village VARCHAR(100) NOT NULL,
    sector VARCHAR(100) NOT NULL,
    registered_by INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (registered_by) REFERENCES users(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Death records table created successfully<br>";
} else {
    echo "Error creating death_records table: " . $conn->error . "<br>";
}

// Insert default admin user
$admin_password = password_hash('admin123', PASSWORD_DEFAULT);
$sql = "INSERT IGNORE INTO users (name, email, password, role) VALUES 
        ('Administrator', 'admin@village.com', '$admin_password', 'admin'),
        ('Officer One', 'officer@village.com', '" . password_hash('officer123', PASSWORD_DEFAULT) . "', 'officer')";

if ($conn->query($sql) === TRUE) {
    echo "Default users created successfully<br>";
    echo "<strong>Default Login Credentials:</strong><br>";
    echo "Admin: admin@village.com / admin123<br>";
    echo "Officer: officer@village.com / officer123<br>";
} else {
    echo "Error creating default users: " . $conn->error . "<br>";
}

$conn->close();

echo "<br><a href='index.php'>Go to System</a>";
?>
