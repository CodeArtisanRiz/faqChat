<?php
include './res/scripts/dataFetch.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>faqBubble</title>

	<!-- for mobile screens -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- UI Style -->
  <link rel="stylesheet" href="./res/styles/style.css">
</head>
<body>

<!-- container element for chat window -->
    <div id="chat"></div>

<!-- import the JavaScript file -->
<script src="./res/scripts/Bubbles.js"></script>
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
      }, 1)
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
})


// Injecting JSON to convo
var convo = <?php echo $json; ?>;

// passing JSON to function and done!
chatWindow.talk(convo)
</script>
</body>
</html>