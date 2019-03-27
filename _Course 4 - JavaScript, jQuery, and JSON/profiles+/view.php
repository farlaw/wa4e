<?php
session_start();
require_once "pdo.php";
require_once "util.php";
require_once "head.php";


// Guardian: Make sure that profile_id is present
if ( ! isset($_REQUEST['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare('SELECT first_name, last_name, email,
headline, summary FROM profile WHERE profile_id = :prof');
$stmt->execute(array( ':prof' => $_REQUEST['profile_id']));
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $profile == false ) {
    $_SESSION["error"] = "Could not load page";
    error_log("No profile found ");
    header( 'Location: index.php');
    return;
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Kirill Bogoslovskii - Resume Registry</title>
</head>
<body>
<div class="container">
<h1>Profile Information</h1>

<ul>
<p>
<li>First Name: <?php echo(htmlentities($profile['first_name'])); ?></li>
<li>Last Name:  <?php echo(htmlentities($profile['last_name'])); ?> </li>
<li>Email: <?php echo(htmlentities($profile['email'])); ?> </li>
<li>Headline: <?php echo(htmlentities($profile['headline'])); ?> </li>
<li>Summary: <?php echo(htmlentities($profile['summary'])); ?> </li>
<?php

// Show positions if there are any
$positions = loadPos($pdo, $_REQUEST['profile_id']);
if ($positions != false) {
    echo("<li>Position: <ul>");
    foreach ($positions as $position) {
      echo("<li>".htmlentities($position["year"]).": ");
      echo(htmlentities($position["description"])."</li>");
    }
    echo("</ul></li>");
}

?>
</p>
</ul>
<p>
<a href="index.php">Done</a>
</p>

</div>
</body>
</html>
