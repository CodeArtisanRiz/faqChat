<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "faqChat";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data
$query = "SELECT din, dout, dopt
          FROM conversations";

$result = $conn->query($query);

// Prepare JSON structure
$conversation = [];
while ($row = $result->fetch_assoc()) {
  $questionID = $row['din'];
  $answers = explode(',', $row['dopt']);

  if (!isset($conversation[$questionID])) {
      $conversation[$questionID] = [
          'says' => [$row['dout']],
          'reply' => [],
      ];
  }

  foreach ($answers as $answer) {
      if ($answer === 'Restart') {
          $conversation[$questionID]['reply'][] = [
              'question' => $answer,
              'answer' => 'ice',
          ];
      } else {
          $conversation[$questionID]['reply'][] = [
              'question' => $answer,
              'answer' => $answer,
          ];
      }
  }
}

// Close the database connection
$conn->close();

// Convert to JSON without escaping keys
$json = json_encode($conversation, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>