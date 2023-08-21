<?php
// Replace with your database connection details
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

// Fetch conversations (questions and answers)
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

// Convert to JSON
// $json = json_encode($conversation, JSON_PRETTY_PRINT);
// Convert to JSON without escaping keys
$json = json_encode($conversation, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


// Replace ":1," with ':"ice",'
// $json = str_replace(':1,', ':"ice",', $json);
// $json = str_replace(':1}', ':"ice"}', $json);
// $json = str_replace(':1\n', ':"ice",', $json);

// Output the JSON

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>faqBubble</title>

	<!-- for mobile screens -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="./resources/styles/style.css">
</head>
<body>

<!-- container element for chat window -->
<div id="chat"></div>

<!-- import the JavaScript file -->
<script src="./resources/scripts/Bubbles.js"></script>
<script>
var chatWindow = new Bubbles(document.getElementById("chat"), "chatWindow", {

  inputCallbackFn: function(o) {
    // add error conversation block & recall it if no answer matched
    var miss = function() {
      chatWindow.talk(
        {
          "i-dont-get-it": {
            says: [
              "Sorry, I don't get it 😕. Pls repeat? Or you can just click below 👇"
            ],
            reply: o.convo[o.standingAnswer].reply
          }
        },
        "i-dont-get-it"
      )
    }

    // do this if answer found
    var match = function(key) {
      setTimeout(function() {
        chatWindow.talk(convo, key) // restart current convo from point found in the answer
      }, 600)
    }

    // sanitize text for search function
    var strip = function(text) {
      return text.toLowerCase().replace(/[\s.,\/#!$%\^&\*;:{}=\-_'"`~()]/g, "")
    }

    // search function
    var found = false
    o.convo[o.standingAnswer].reply.forEach(function(e, i) {
      strip(e.question).includes(strip(o.input)) && o.input.length > 0
        ? (found = e.answer)
        : found ? null : (found = false)
    })
    found ? match(found) : miss()
  }
}) // done setting up chat-bubble

// conversation object defined separately, but just the same as in the
// "Basic chat-bubble Example" (1-basics.html)


// json Inject
// var convo =


var convo = <?php echo $json; ?>;

// pass JSON to your function and you're done!
chatWindow.talk(convo)
</script>
<!-- <?php

?> -->
</body>
