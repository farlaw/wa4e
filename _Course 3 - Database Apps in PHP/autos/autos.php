<?php
require_once "pdo.php";

// Demand a GET parameter
if ( ! isset($_GET['name']) || strlen($_GET['name']) < 1  ) {
    die('Name parameter missing');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: index.php');
    return;
}

$failure = false;  // If we have no POST data
$added = false;

if ( isset($_POST['add']) ) {
    if ( ! is_numeric($_POST['mileage']) || ! is_numeric($_POST['year']) ) {
      $failure = "Mileage and year must be numeric";
      error_log("Mileage or year field is not integer ".$_POST['make']."/".$_POST['year']);
    }
    elseif ( strlen($_POST['make']) < 1  ) {
      $failure = "Make is required";
      error_log("Make field is empty ".$_POST['make']);
    }
    else {
      $sql = "INSERT INTO autos (make, year, mileage)
                VALUES (:mk, :yr, :mi)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
          ':mk' => $_POST['make'],
          ':yr' => $_POST['year'],
          ':mi' => $_POST['mileage']));
      $added = "Record inserted";
      error_log("Data added sucessfully ".$_POST['make']);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Kirill B - a13f8dce - Automobile Tracker </title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Tracking Autos </h1>
<?php
// Note triple not equals and think how badly double
// not equals would work here...
if ( $failure !== false ) {
    // Look closely at the use of single and double quotes
    echo('<p style="color: red;">'.htmlentities($failure)."</p>\n");
}
if ( $added !== false ) {
    echo('<p style="color: green;">'.htmlentities($added)."</p>\n");
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
<input type="submit" name="logout" value="Logout">
</form>
</div>

<table border="1">
<?php
if ( $added !== false ) {
  $stmt = $pdo->query("SELECT make, year, mileage FROM autos");
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ( $rows as $row ) {
    echo "<tr><td>";
    echo(htmlentities($row['make']));
    echo("</td><td>");
    echo(htmlentities($row['year']));
    echo("</td><td>");
    echo(htmlentities($row['mileage']));
    echo("</td></tr>\n");
  }
}
?>
</table>

</body>
</html>
