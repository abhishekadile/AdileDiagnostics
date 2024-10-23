<?php
// Database configuration
$servername = "localhost";  // Usually 'localhost'
$username = "YOUR_DB_USERNAME";  // Replace with your MySQL username
$password = "YOUR_DB_PASSWORD";  // Replace with your MySQL password
$dbname = "contact_form_db";     // Replace with the name of your database

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data
$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];

// Prepare and bind the SQL query to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO contact_form (name, email, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $message);

// Execute the query and check for success
if ($stmt->execute()) {
    echo "<script>alert('Thank you! Your message has been sent.'); window.location.href='contact.html';</script>";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
