<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $username = $_POST['username'];
    $time = $_POST['time'];  // Ensure this is in HH:MM:SS format

    // Database connection parameters
    $servername = "127.0.0.1";
    $usernameDB = "root";  // Change to your MySQL username
    $password = "";  // Change to your MySQL password
    $dbname = "Konkurss";

    // Create connection using MySQLi
    $conn = new mysqli($servername, $usernameDB, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO top5 (username, time) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $time);  // "ss" means both parameters are strings

    // Execute the prepared statement
    if ($stmt->execute()) {
        echo "New record created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
