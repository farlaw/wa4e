<?php

//Submitted by some random guy on Coursera - for my notes only. Kirill

  if (! isset($_GET['name']) || strlen($_GET['name']) < 1) {
    die('Name parameter missing');
  }

  if (isset($_POST['logout'])) {
    header("Location: index.php");
    return;
  }

  require_once ('pdo.php');
  $message = false;
  $rows = false;
  $err_msg = false;

  if (isset($_POST['submit'])) {
    $mk = htmlentities($_POST['make']);
    $yr = htmlentities($_POST['year']);
    $mi = htmlentities($_POST['mileage']);
    if (strlen($mk)<1) {
      $err_msg = "Make is required";
    } elseif ((! is_numeric($yr)) || (! is_numeric($mi))) {
      $err_msg = "Mileage and year must be numeric";
    } else {
      try {
        $stmt = $pdo->prepare('INSERT INTO autos (make, year, mileage) VALUES ( :mk, :yr, :mi)');
        $stmt->execute(array(
          ':mk' => $_POST['make'],
          ':yr' => $_POST['year'],
          ':mi' => $_POST['mileage']));
        $message = "<p>Record inserted</p>";
      } catch (Exception $ex) {
        $message = "<p>Something went wrong</p>";
        error_log("Database Add fail: ".$ex);
      }
    }
  }
  $rows = $pdo->query('SELECT * FROM autos ORDER BY make;');

?>

 <head>
   <title>Sherry Freitas Autos Form </title>
   <style>
    table, tr, td, thead {border:1px solid black; border-collapse:collapse;}
    thead {font-weight:bold;text-align:center;}
    td {padding:0 2em;}
    </style>
 </head>
 <body>
   <h1>Automobile Database</h1>
   <p>Enter automobile information</p>
   <?php if (strlen($err_msg)> 0) {
     echo "<p>".$err_msg."</p>";
   }
   ?>
   <form method="post">
     <label for="make">Make: </label>
     <input type="text" name="make"/>
     <label for="year">Year: </label>
     <input type="text" name="year" maxlength="4" size="4"/>
     <label for="mileage">Mileage: </label>
     <input type="text" name="mileage"/>
     <input type="submit" value="Add" name="submit" />
     <input type="submit" value="Logout" name="logout" />
   </form>
   <?php
     if ($message !== false) {
       echo $message;
     }
     if ($rows !== false) {
       echo "<table><thead><tr><td>Make</td><td>Year</td><td>Mileage</td></tr></thead>\n";
       foreach ($rows->fetchAll(PDO::FETCH_ASSOC) as $row) {
         echo "<tr><td>".htmlentities($row['make'])."</td><td>".$row['year']."</td><td>".$row['mileage']."</td></tr>\n";
       }
       echo "</table>\n";
     } else {
       echo "<p>Database is currently empty</p>";
     }
   ?>


 </body>
