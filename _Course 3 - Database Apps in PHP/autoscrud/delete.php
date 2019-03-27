<?php
require_once "pdo.php";
require_once "bootstrap.php";
session_start();

// Check if the user is logged in
if ( ! isset($_SESSION['name']) ) {
    die('ACCESS DENIED');
}

// If the user pressed Cancel go back to view.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

// Guardian: Make sure that autos_id is present
if ( ! isset($_GET['autos_id']) ) {
  $_SESSION['error'] = "Missing autos_id";
  header('Location: index.php');
  return;
}

// If all the fields including autos_id are set
if ( isset($_POST['delete']) && isset($_POST['autos_id'])) {
    // Delete from database
    $sql = "DELETE FROM autos WHERE autos_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(":id" => $_POST['autos_id']));
    $_SESSION['success'] = 'Record deleted';
    error_log("Record deleted sucessfully ".$_POST['autos_id']);
    header( 'Location: index.php' ) ;
    return;
}

// Select data from database to show on the screen
$stmt = $pdo->prepare("SELECT make, autos_id FROM autos where autos_id = :id");
$stmt->execute(array(':id' => $_GET['autos_id']));
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
<title>Kirill B - 1d18c59d - Automobile Tracker </title>
</head>
<body>
<div class="container">
<h1>Deleting an entry </h1>

<p>Confirm: Deleting <?= htmlentities($row['make']) ?></p>

<form method="post">
<input type="hidden" name="autos_id" value="<?= $row['autos_id'] ?>">
<p><input type="submit" name = "delete" value="Delete">
<input type="submit" name="cancel" value="Cancel"></p>
</form>
</div>
</body>
</html>
