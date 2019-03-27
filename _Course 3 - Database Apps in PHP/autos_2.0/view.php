<?php
session_start();
require_once "pdo.php";
require_once "bootstrap.php";

if ( ! isset($_SESSION['name']) ) {
    die('Not logged in');
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Kirill B - a13f8dce - Automobile Tracker </title>
</head>
<body>
<div class="container">
<h1>Tracking Autos for <?php echo htmlentities($_SESSION["name"])?></h1>

<?php
if ( isset($_SESSION['success']) ) {
    echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
    unset($_SESSION['success']);
}
?>
<h2>Automobiles</h2>
<ul>
<p> <?php
  $stmt = $pdo->query("SELECT make, year, mileage FROM autos");
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ( $rows as $row ) {
    ?> <li> <?php
    echo(htmlentities($row['make'])); ?> | <?php
    echo(htmlentities($row['year'])); ?> | <?php
    echo(htmlentities($row['mileage']));
    ?> </li> <?php
  }
?>
</ul>

<p>
<a href="add.php">Add New</a> |
<a href="logout.php">Logout</a>
</p>
</div>

</body>
</html>
