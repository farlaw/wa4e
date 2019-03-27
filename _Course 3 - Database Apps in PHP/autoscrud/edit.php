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
if ( isset($_POST['make']) && isset($_POST['model'])
     && isset($_POST['year']) && isset($_POST['mileage'])
     && isset($_POST['autos_id'])) {

  // Data validation
  if ( strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 ||
      strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1) {
      $_SESSION['error'] = 'All fields are required';
      error_log("At least one field is empty ".$_POST['make']."/".$_POST['year']);
      header("Location: edit.php?autos_id=".$_REQUEST['autos_id']);
      return;
    }

    elseif ( ! is_numeric($_POST['year']) ) {
      $_SESSION["error"] = "Year must be numeric";
      error_log("Year is not integer ".$_POST['make']."/".$_POST['year']);
      header("Location: edit.php?autos_id=".$_REQUEST['autos_id']);
      return;
    }

    elseif ( ! is_numeric($_POST['mileage']) ) {
      $_SESSION["error"] = "Mileage must be numeric";
      error_log("Mileage is not integer ".$_POST['make']."/".$_POST['mileage']);
      header("Location: edit.php?autos_id=".$_REQUEST['autos_id']);
      return;
    }

    else {
    // Update database
    $sql = "UPDATE autos SET make = :mk,
            model = :md, year = :yr, mileage = :mi
            WHERE autos_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':mk' => $_POST['make'],
        ':md' => $_POST['model'],
        ':yr' => $_POST['year'],
        ':mi' => $_POST['mileage'],
        ':id' => $_POST['autos_id']));
    $_SESSION['success'] = 'Record edited';
    error_log("Record edited sucessfully ".$_POST['make']);
    header( 'Location: index.php' ) ;
    return;
    }
}

// Select data from database to show on the screen
$stmt = $pdo->prepare("SELECT * FROM autos where autos_id = :id");
$stmt->execute(array(":id" => $_GET['autos_id']));
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
<h1>Editing an entry </h1>
<p><b>Note:</b> All fields are required.</p>

<?php
// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$mk = htmlentities($row['make']);
$md = htmlentities($row['model']);
$yr = htmlentities($row['year']);
$mi = htmlentities($row['mileage']);
$id = $row['autos_id'];
?>

<p>Edit Automobile</p>
<form method="post">
<p>Make:
<input type="text" name="make" value="<?= $mk ?>"></p>
<p>Model:
<input type="text" name="model" value="<?= $md ?>"></p>
<p>Year:
<input type="text" name="year" value="<?= $yr ?>"></p>
<p>Mileage:
<input type="text" name="mileage" value="<?= $mi ?>"></p>
<input type="hidden" name="autos_id" value="<?= $id ?>">
<p><input type="submit" value="Save"/>
<input type="submit" name="cancel" value="Cancel"></p>
</form>
</div>
</body>
</html>
