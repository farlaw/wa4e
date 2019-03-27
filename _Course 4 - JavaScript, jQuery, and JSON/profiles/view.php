<?php
session_start();
require_once "pdo.php";
require_once "bootstrap.php";

$sql = "SELECT first_name, last_name, email,
headline, summary FROM profile WHERE profile_id = ".$_GET['profile_id'];
$stmt = $pdo->query($sql);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row == false ) {
    echo("profile == false");
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
<h1>Welcome to the Automobiles Database</h1>

<ul>
<p>
<li>First Name: <?php echo(htmlentities($row['first_name'])); ?></li>
<li>Last Name:  <?php echo(htmlentities($row['last_name'])); ?> </li>
<li>Email: <?php echo(htmlentities($row['email'])); ?> </li>
<li>Headline: <?php echo(htmlentities($row['headline'])); ?> </li>
<li>Summary: <?php echo(htmlentities($row['summary'])); ?> </li>
</p>
</ul>
<p>
<a href="index.php">Done</a>
</p>

</div>
</body>
</html>
