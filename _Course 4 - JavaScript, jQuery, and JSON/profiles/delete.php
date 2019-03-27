<?php
require_once "pdo.php";
require_once "bootstrap.php";
session_start();

// Check if the user is logged in
if ( ! isset($_SESSION['name']) ) {
    die('Not logged in');
}

// If the user pressed Cancel go back to view.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

// Check to see if the user_id owns the profile
$sql = "SELECT user_id FROM profile WHERE profile_id = ".$_GET['profile_id'];
$stmt = $pdo->query($sql);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row["user_id"] !== $_SESSION["user_id"]) {
    $_SESSION["error"] = "You are not authorized to edit this profile";
    error_log("currently logged in user does not own profile: "
      .$row["user_id"]." vs. ".$_SESSION['user_id']);
    header( 'Location: index.php');
    return;
  }

// If all the fields including profile_id are set
if ( isset($_POST['delete']) && isset($_POST['profile_id'])) {
    // Delete from database
    $sql = "DELETE FROM profile WHERE profile_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(":id" => $_POST['profile_id']));
    $_SESSION['success'] = 'Record deleted';
    error_log("Record deleted sucessfully ".$_POST['profile_id']);
    header( 'Location: index.php' ) ;
    return;
}

// Select data from database to show on the screen
$stmt = $pdo->prepare("SELECT first_name, last_name, profile_id FROM profile where profile_id = :id");
$stmt->execute(array(':id' => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for ID';
    header( 'Location: index.php' ) ;
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
<h1>Deleting Profile </h1>
<p>Confirm: Deleting <?= htmlentities($row['first_name']) ?></p>
<form method="post">
<ul>
<li>First Name: <?php echo(htmlentities($row['first_name'])); ?></li>
<li>Last Name:  <?php echo(htmlentities($row['last_name'])); ?> </li>
</ul>
<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
<p><input type="submit" name = "delete" value="Delete">
<input type="submit" name="cancel" value="Cancel"></p>
</form>
</div>
</body>
</html>
