<?php
session_start();
require_once "pdo.php";
require_once "bootstrap.php";

?>

<!DOCTYPE html>
<html>
<head>
  <title>Kirill B - 1d18c59d - Automobile Tracker </title>
</head>
<body>
<div class="container">
<h1>Welcome to the Automobiles Database</h1>

<?php

if ( isset($_SESSION['success']) ) {
    echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
    unset($_SESSION['success']);
}

if ( isset($_SESSION['error']) ) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}

// Show table if we are logged in:
if ( isset($_SESSION['name']) ) {
  $stmt = $pdo->query("SELECT make, model, year, mileage, autos_id FROM autos");
  $try =  $stmt->rowCount(PDO::FETCH_ASSOC);
  if ($try !== 0) {
    echo('<table border="1" style="width:50%">'."\n");
    echo ("<tr><th>Make</th>");
    echo ("<th>Model</th>");
    echo ("<th>Year</th>");
    echo ("<th>Mileage</th>");
    echo ("<th>Action</th></tr>\n");
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        echo "<tr><td>";
        echo(htmlentities($row['make']));
        echo("</td><td>");
        echo(htmlentities($row['model']));
        echo("</td><td>");
        echo(htmlentities($row['year']));
        echo("</td><td>");
        echo(htmlentities($row['mileage']));
        echo("</td><td>");
        echo('<a href="edit.php?autos_id='.$row['autos_id'].'">Edit</a> / ');
        echo('<a href="delete.php?autos_id='.$row['autos_id'].'">Delete</a>');
        echo("</td></tr>\n");
    }
    echo("</table>");
  }
  else {
    echo ("No rows found\n");
    echo ("<p></p>");
  }
  echo ("<p></p>");
  echo('<a href="add.php">Add New Entry</a> | ');
  echo ('<a href="logout.php">Logout</a>');
}

else {
  echo ('<a href="login.php">Please log in</a>');
}
?>
</div>
</body>
</html>
