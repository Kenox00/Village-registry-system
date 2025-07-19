<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'village_register';

// Create connection using mysqli
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8");

// Function to escape strings for security
function escape_string($string) {
    global $conn;
    return $conn->real_escape_string($string);
}

// Function to prepare and execute queries safely
function execute_query($query, $types = '', $params = []) {
    global $conn;
    
    if (!empty($params)) {
        $stmt = $conn->prepare($query);
        if ($stmt) {
            if (!empty($types)) {
                $stmt->bind_param($types, ...$params);
            }
            $result = $stmt->execute();
            
            if ($stmt->insert_id) {
                return $stmt->insert_id;
            }
            
            $result_set = $stmt->get_result();
            return $result_set ? $result_set : $result;
        }
    } else {
        return $conn->query($query);
    }
    
    return false;
}
?>
