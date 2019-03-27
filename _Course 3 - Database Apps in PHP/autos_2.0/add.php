<?php
session_start();
require_once "bootstrap.php";
require_once "pdo.php";

// Check if the user is logged in
if ( ! isset($_SESSION['name']) ) {
    die('Not logged in');
}

// If the user pressed Cancel go back to view.php
if ( isset($_POST['cancel']) ) {
    header('Location: view.php');
    return;
}

if ( isset($_POST['add']) ) {
    if ( ! is_numeric($_POST['mileage']) || ! is_numeric($_POST['year']) ) {
      $_SESSION["error"] = "Mileage and year must be numeric";
      error_log("Mileage or year field is not integer ".$_POST['make']."/".$_POST['year']);
      header( 'Location: add.php' ) ;
      return;
    }
    elseif ( strlen($_POST['make']) < 1  ) {
      $_SESSION["error"] = "Make is required";
      error_log("Make field is empty ".$_POST['make']);
      header( 'Location: add.php' ) ;
      return;
    }
    else {
      $sql = "INSERT INTO autos (make, year, mileage)
                VALUES (:mk, :yr, :mi)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
          ':mk' => $_POST['make'],
          ':yr' => $_POST['year'],
          ':mi' => $_POST['mileage']));
      $_SESSION["success"] = "Record inserted";
      error_log("Data added sucessfully ".$_POST['make']);
      header( 'Location: view.php' ) ;
      return;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Kirill B - 4d235633 - Automobile Tracker </title>
</head>
<body>
<div class="container">
<h1>Tracking Autos </h1>
<?php
if ( isset($_SESSION["error"]) ) {
    echo('<p style="color:red">'.htmlentities($_SESSION["error"])."</p>\n");
    unset($_SESSION["error"]);
}
?>

<form method="post">
<label for="inp01">Make</label>
<input type="text" name="make" id="inp01" size="50"><br/>
<label for="inp02">Year</label>
<input type="text" name="year" id="inp02" size="20"><br/>
<label for="inp03">Mileage</label>
<input type="text" name="mileage" id="inp03" size="20" ><br/>
<input type="submit" name="add" value="Add">
<input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>
</html>
