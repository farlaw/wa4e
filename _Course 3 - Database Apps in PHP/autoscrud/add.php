<?php
session_start();
require_once "bootstrap.php";
require_once "pdo.php";

// Check if the user is logged in
if ( ! isset($_SESSION['name']) ) {
    die('ACCESS DENIED');
}

// If the user pressed Cancel go back to view.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

if ( isset($_POST['make']) && isset($_POST['model'])
     && isset($_POST['year']) && isset($_POST['mileage'])) {

  // Data validation
  if ( strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 ||
      strlen($_POST['year']) < 1 || strlen($_POST['mileage']) < 1) {
      $_SESSION['error'] = 'All fields are required';
      error_log("At least one field is empty ".$_POST['make']."/".$_POST['year']);
      header("Location: add.php");
      return;
    }

      elseif ( ! is_numeric($_POST['year']) ) {
        $_SESSION["error"] = "Year must be numeric";
        error_log("Year is not integer ".$_POST['make']."/".$_POST['year']);
        header( 'Location: add.php' ) ;
        return;
      }

      elseif ( ! is_numeric($_POST['mileage']) ) {
        $_SESSION["error"] = "Mileage must be numeric";
        error_log("Mileage is not integer ".$_POST['make']."/".$_POST['mileage']);
        header( 'Location: add.php' ) ;
        return;
      }

      else {
        $sql = "INSERT INTO autos (make, model, year, mileage)
                  VALUES (:mk, :md, :yr, :mi)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':mk' => $_POST['make'],
            ':md' => $_POST['model'],
            ':yr' => $_POST['year'],
            ':mi' => $_POST['mileage']));
        $_SESSION["success"] = "Record added";
        error_log("Record added sucessfully ".$_POST['make']);
        header( 'Location: index.php' ) ;
        return;
      }
  }
?>

<!DOCTYPE html>
<html>
<head>
<title>Kirill B - 1d18c59d - Automobile Tracker </title>
</head>
<body>
<div class="container">
<h1>Adding Automobile </h1>
<p><b>Note:</b> All fields are required.</p>

<?php
// Flash pattern
if ( isset($_SESSION["error"]) ) {
    echo('<p style="color:red">'.htmlentities($_SESSION["error"])."</p>\n");
    unset($_SESSION["error"]);
}
?>

<p>Add a new entry</p>
<form method="post">
<label for="inp01">Make</label>
<input type="text" name="make" id="inp01" size="50"><br/>
<label for="inp02">Model</label>
<input type="text" name="model" id="inp01" size="50"><br/>
<label for="inp03">Year</label>
<input type="text" name="year" id="inp02" size="20"><br/>
<label for="inp04">Mileage</label>
<input type="text" name="mileage" id="inp03" size="20" ><br/>
<input type="submit" name="add" value="Add">
<input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>
</html>
