<?php
session_start();

// Database connection settings
$host = '127.0.0.1';
$dbname = 'konkurss';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Handle the AJAX POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['time'])) {
    // Sanitize input data
    $username = htmlspecialchars($_POST['username']);
    $time = $_POST['time'];

    // Insert the new score into the database
    $stmt = $pdo->prepare("INSERT INTO top5 (username, time) VALUES (:username, :time)");
    $stmt->execute(['username' => $username, 'time' => $time]);

    // Fetch the updated leaderboard
    $leaderboard = getLeaderboard($pdo);

    // Return the updated leaderboard as a JSON response
    $response = [
        'success' => true,
        'message' => 'Your score has been recorded!',
        'entries' => $leaderboard
    ];

    echo json_encode($response);
    exit();
}

// Function to fetch the top 5 leaderboard entries
function getLeaderboard($pdo) {
    $stmt = $pdo->prepare("SELECT username, time FROM top5 ORDER BY time ASC LIMIT 5");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
