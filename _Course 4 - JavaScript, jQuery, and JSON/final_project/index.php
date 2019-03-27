<?php
require_once "pdo.php";
require_once "head.php";

session_start();

?>

<!DOCTYPE html>
<html>
<head>
  <title>Kirill Bogoslovskii - Resume Registry</title>
</head>
<body>
<div class="container">
<h1>Welcome to Kirill's Resume Registry</h1>

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
$sql = "SELECT user_id, profile_id, first_name, last_name, headline
        FROM profile";
$stmt = $pdo->query($sql);
$try =  $stmt->rowCount(PDO::FETCH_ASSOC);

//If no rows to show:
if ($try == false) {
    echo ("No rows found\n");
    echo ("<p></p>");
    }
    //If there are entries. draw a table:
    else {
      echo('<table border="1" style="width:50%">'."\n");
      echo ("<tr><th>Name</th>");
      echo ("<th>Headline</th>");
      // Additional column if we're logged in
      if ( isset($_SESSION['name']) ) {
        echo ("<th>Action</th>");
        }
      echo("</tr>\n");
      while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
          echo ("<tr><td>");
          echo('<a href="view.php?profile_id='.$row['profile_id'].'">');
          echo(htmlentities($row['first_name']).' '.htmlentities($row['last_name']).'</a>');
          echo("</td><td>");
          echo(htmlentities($row['headline']));
          echo("</td>");
          // Rows for additional column if we're logged in
          if ( isset($_SESSION['name']) ) {
            echo('<td><a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
            echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a></td>');
            }
          echo("</tr>\n");
      }
      echo("</table>");
    } //end of the table here

  // If're logged in, show "Add New Entry" and "Logout"
  if ( isset($_SESSION['user_id']) ) {
      echo ("<p></p>");
      echo('<a href="add.php">Add New Entry</a> | ');
      echo ('<a href="logout.php">Logout</a>');
    }

    // If're not logged in, show "Please log in"
    else {
      echo ("<p></p>");
      echo ('<a href="login.php">Please log in</a>');
    }
?>
</div>
</body>
</html>
