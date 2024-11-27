document.addEventListener('DOMContentLoaded', () => {
  // Accordion functionality
  const accordionButtons = document.querySelectorAll('.accordion-button');
  
  accordionButtons.forEach(button => {
    button.addEventListener('click', () => {
      const accordionItem = button.closest('.accordion-item');
      const content = accordionItem.querySelector('.accordion-content');
      const isVisible = content.style.display === 'block';
      content.style.display = isVisible ? 'none' : 'block';
      accordionItem.classList.toggle('active');
    });
  });

  // Puzzle and Timer logic
  const pieces = document.querySelectorAll('.piece');
  const slots = document.querySelectorAll('.slot');
  let startTime, timer, isGameRunning = false;
  let placedPieces = 0; // Track how many pieces have been correctly placed

  // Shuffle the pieces in random order when the page loads
  shufflePieces(pieces);

  // Allow pieces to be dragged
  pieces.forEach(piece => {
    piece.addEventListener('dragstart', (e) => {
        e.dataTransfer.setData('text', e.target.id);
    });
  });

  // Allow pieces to be dropped into slots
  slots.forEach(slot => {
    slot.addEventListener('dragover', (e) => {
        e.preventDefault(); // Allow drop
    });

    slot.addEventListener('drop', (e) => {
        const pieceId = e.dataTransfer.getData('text');
        const draggedPiece = document.getElementById(pieceId);
        const slotId = slot.id.replace('slot', '');

        if (pieceId === `piece${slotId}`) {
            slot.appendChild(draggedPiece);
            placedPieces++;
            checkWin();
            document.getElementById('game-status').textContent = 'Correct piece! Keep going!';
            document.getElementById('game-status').style.color = 'green';
        } else {
            document.getElementById('game-status').textContent = 'Wrong slot! Try again.';
            document.getElementById('game-status').style.color = 'red';
        }
    });
  });

  function shufflePieces(pieces) {
    const parent = document.querySelector('.left-column');
    const piecesArray = Array.from(pieces);

    // Shuffle pieces using Fisher-Yates algorithm
    for (let i = piecesArray.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [piecesArray[i], piecesArray[j]] = [piecesArray[j], piecesArray[i]];
    }

    // Append shuffled pieces to the parent container
    piecesArray.forEach(piece => parent.appendChild(piece));
  }

  function checkWin() {
    // Check if all pieces are placed correctly
    if (placedPieces === 15) {
        if (isGameRunning) {
            clearInterval(timer); // Stop the timer
            isGameRunning = false;
        }
        setTimeout(displayTime, 500); // Show final time after a short delay
    }
  }

  function startTimer() {
    // Start the timer when the game begins
    if (!isGameRunning) {
        startTime = Date.now();
        timer = setInterval(updateTimer, 1000);
        isGameRunning = true;
        document.getElementById('game-status').textContent = 'Game started! Good luck!';
        document.getElementById('game-status').style.color = 'black';
    }
  }

  function updateTimer() {
    const timeElapsed = Date.now() - startTime;
    const seconds = Math.floor(timeElapsed / 1000);
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    document.getElementById('timer').textContent = `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
  }

  function displayTime() {
    // Display time when puzzle is completed
    const timeElapsed = Date.now() - startTime;
    const seconds = Math.floor(timeElapsed / 1000);
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;

    const status = document.getElementById('game-status');
    if (status) {
        status.textContent = `You finished the puzzle! :D`;
        status.style.color = 'rgb(31, 80, 110)';
    }

    const leaderboardForm = document.getElementById('leaderboard-form');
    if (leaderboardForm && leaderboardForm.style.display !== 'block') {
        leaderboardForm.style.display = 'block'; // Show leaderboard form after game completion
        const timeInput = document.getElementById('time');
        if (timeInput) {
            timeInput.value = `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
        }
    }
  }

  document.getElementById('start-game').addEventListener('click', startTimer);

  // AJAX Submission for leaderboard
  document.getElementById('leaderboard-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    const username = document.getElementById('username').value;
    const time = document.getElementById('time').value;

    if (username && time) {
        const formData = new FormData();
        formData.append('username', username);
        formData.append('time', time);

        fetch('leaderboard.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Parse the JSON response
        .then(data => {
            if (data.success) {
                // Update the leaderboard with the new entries
                updateLeaderboard(data.entries);

                // Show success message
                document.getElementById('game-status').textContent = data.message;

                // Optionally hide the form after submission
                document.getElementById('leaderboard-form').style.display = 'none';
            } else {
                document.getElementById('game-status').textContent = 'There was an issue submitting your score. Please try again.';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('game-status').textContent = 'There was an error submitting your score.';
            document.getElementById('game-status').style.color = 'red';
        });
    } else {
        document.getElementById('game-status').textContent = 'Please fill in both fields.';
    }
  });

  function updateLeaderboard(entries) {
    const leaderboardTable = document.getElementById('leaderboard').querySelector('tbody');
    leaderboardTable.innerHTML = ''; // Clear previous leaderboard entries

    entries.forEach(entry => {
        const row = document.createElement('tr');
        const usernameCell = document.createElement('td');
        const timeCell = document.createElement('td');

        usernameCell.textContent = entry.username;
        timeCell.textContent = entry.time;

        row.appendChild(usernameCell);
        row.appendChild(timeCell);
        leaderboardTable.appendChild(row);
    });
}
});
