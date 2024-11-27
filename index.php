<?php
// Start session to track the form submission status
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

// Prevent resubmission after form submit (POST/Redirect/GET pattern)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['time'])) {
    // Prevent form resubmission
    if (!isset($_SESSION['form_submitted']) || $_SESSION['form_submitted'] !== true) {
        // Mark as submitted
        $_SESSION['form_submitted'] = true;

        // Sanitize and validate input
        $username = htmlspecialchars($_POST['username']);
        $time = $_POST['time'];

        // Insert into the database
        $stmt = $pdo->prepare("INSERT INTO top5 (username, time) VALUES (:username, :time)");
        $stmt->execute(['username' => $username, 'time' => $time]);

        // Redirect to avoid resubmission (POST/Redirect/GET pattern)
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Fetch leaderboard data
$stmt = $pdo->prepare("SELECT username, time FROM top5 ORDER BY time ASC LIMIT 5");
$stmt->execute();
$leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Unset form submission status after page load
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    unset($_SESSION['form_submitted']);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reasons to Care for Pets</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="main-header">
  <h1><span class="top-text">Abandonment hurts.</span> <span class="bottom-text">Love saves.</span></h1>
</header>

<!-- Reasons Section -->
<div class="reasons-container">
  <div class="reason">
    <img src="icons/connection.png" alt="Connection and Trust Icon" class="icon">
    <h3>Connection and Trust</h3>
    <p>Animals provide unconditional love and trust, and it is our responsibility not to break that trust.</p>
  </div>
  <div class="reason">
    <img src="icons/responsibility.png" alt="Responsibility Icon" class="icon">
    <h3>Responsibility</h3>
    <p>A pet is a family member that is completely dependent on its owner.</p>
  </div>
  <div class="reason">
    <img src="icons/health.png" alt="Health and Safety Icon" class="icon">
    <h3>Health and Safety</h3>
    <p>Abandoned animals are exposed to starvation, disease, and attacks by predators that threaten their lives.</p>
  </div>
  <div class="reason">
    <img src="icons/society.png" alt="Impact on Society Icon" class="icon">
    <h3>Impact on Society</h3>
    <p>Abandoned pets often become strays, creating public safety risks.</p>
  </div>
  <div class="reason">
    <img src="icons/paw.png" alt="Moral Values Icon" class="icon">
    <h3>Moral Values</h3>
    <p>Caring for a pet reflects our humanity and empathy towards a living creature.</p>
  </div>
  <div class="reason">
    <img src="icons/law.png" alt="The Law Icon" class="icon">
    <h3>The Law</h3>
    <p>It is against the law to neglect animals.</p>
  </div>
</div>

<!-- Accordion Section -->
<div class="accordion">
  <div class="accordion-item">
    <button class="accordion-button">What to consider before buying a pet?</button>
    <div class="accordion-content">
      <ul>
        <li>Caring for an animal is long-term. For example, dogs and cats live 10 - 20 years.</li>
        <li>A pet needs daily attention. What will you do if you go on a trip?</li>
        <li>Finances are needed for feeding. In addition, pets also tend to get sick and do not have free healthcare.</li>
      </ul>
    </div>
  </div>

  <div class="accordion-item">
    <button class="accordion-button">What to do if you can't take care of yourself anymore?</button>
    <div class="accordion-content">
      <ul>
        <li>Remember that an animal is not a thing, but a living creature!</li>
        <li>It is preferable to try to find new, loving homes with people you know.</li>
        <li>Consult animal shelters.</li>
        <li>Inform the future owners or the shelter about the true state of the animal's health.</li>
      </ul>
    </div>
  </div>

  <div class="accordion-item">
    <button class="accordion-button">What should you do if you see an abandoned pet?</button>
    <div class="accordion-content">
      <ul>
        <li>Make sure of the situation - sometimes the owner is further away than he should be.</li>
        <li>With your safety as a priority, try to call the animal and see if there is any indication of the owner (such as a tag on the collar).</li>
        <li>If the animal does not come to you willingly, do not try to catch it and force it!</li>
        <li>Contact the nearest shelter or call the police on 112.</li>
      </ul>
    </div>
  </div>
</div>

<!-- Start Game Button and Timer -->
<button id="start-game">Start Game</button>
<div id="timer">0:00</div>
<div id="game-status"></div> <!-- This will display "Wrong slot!" or "Game finished!" -->

<!-- Jigsaw Puzzle Section -->
<div class="game-container">
  <!-- Left column with puzzle pieces -->
  <div class="left-column">
      <img src="puzzle/1.png" id="piece1" draggable="true" data-id="1" class="piece" />
      <img src="puzzle/2.png" id="piece2" draggable="true" data-id="2" class="piece" />
      <img src="puzzle/3.png" id="piece3" draggable="true" data-id="3" class="piece" />
      <img src="puzzle/4.png" id="piece4" draggable="true" data-id="4" class="piece" />
      <img src="puzzle/5.png" id="piece5" draggable="true" data-id="5" class="piece" />
      <img src="puzzle/6.png" id="piece6" draggable="true" data-id="6" class="piece" />
      <img src="puzzle/7.png" id="piece7" draggable="true" data-id="7" class="piece" />
      <img src="puzzle/8.png" id="piece8" draggable="true" data-id="8" class="piece" />
      <img src="puzzle/9.png" id="piece9" draggable="true" data-id="9" class="piece" />
      <img src="puzzle/10.png" id="piece10" draggable="true" data-id="10" class="piece" />
      <img src="puzzle/11.png" id="piece11" draggable="true" data-id="11" class="piece" />
      <img src="puzzle/12.png" id="piece12" draggable="true" data-id="12" class="piece" />
      <img src="puzzle/13.png" id="piece13" draggable="true" data-id="13" class="piece" />
      <img src="puzzle/14.png" id="piece14" draggable="true" data-id="14" class="piece" />
      <img src="puzzle/15.png" id="piece15" draggable="true" data-id="15" class="piece" />
  </div>

  <!-- Right column with empty slots -->
  <div class="right-column">
      <div class="slot" id="slot1"></div>
      <div class="slot" id="slot2"></div>
      <div class="slot" id="slot3"></div>
      <div class="slot" id="slot4"></div>
      <div class="slot" id="slot5"></div>
      <div class="slot" id="slot6"></div>
      <div class="slot" id="slot7"></div>
      <div class="slot" id="slot8"></div>
      <div class="slot" id="slot9"></div>
      <div class="slot" id="slot10"></div>
      <div class="slot" id="slot11"></div>
      <div class="slot" id="slot12"></div>
      <div class="slot" id="slot13"></div>
      <div class="slot" id="slot14"></div>
      <div class="slot" id="slot15"></div>
  </div>
</div>

<!-- Leaderboard Section -->
<h3>Leaderboard</h3>
<p>Save your time and check out the top 5 players!</p>

<form id="leaderboard-form" method="POST" autocomplete="off">
    <input type="text" id="username" name="username" placeholder="Enter your username" required autocomplete="off">
    <button type="submit">Add</button>
</form>

<table id="leaderboard">
  <thead>
    <tr>
      <th>Username</th>
      <th>Time</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($leaderboard as $entry): ?>
      <tr>
        <td><?php echo htmlspecialchars($entry['username']); ?></td>
        <td><?php echo htmlspecialchars($entry['time']); ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<script src="script.js"></script>

</body>
</html>