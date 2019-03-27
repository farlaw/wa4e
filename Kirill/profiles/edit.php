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


// If all the fields including autos_id are set
if ( isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['email']) && isset($_POST['headline'])
      && isset($_POST['summary']) && isset($_POST['profile_id'])) {

  // Data validation
  if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 ||
      strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1
      || strlen($_POST['summary']) < 1) {
      $_SESSION['error'] = 'All fields are required';
      error_log("At least one field is empty ".$_POST['first_name']." ".$_POST['last_name']);
      header("Location: edit.php?profile_id=".$_GET['profile_id']);
      return;
    }
    elseif (strpos($_POST['email'],'@') < 1) {
      $_SESSION["error"] = "Email must have an at-sign (@)";
      header("Location: edit.php?profile_id=".$_GET['profile_id']);
      return;
    }
    else {
      // Update database
      $sql = "UPDATE profile SET first_name = :fn, last_name = :ln,
              email = :em, headline = :he, summary = :su WHERE profile_id = :pid";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'],
        ':pid' => $_POST['profile_id']));
      $_SESSION['success'] = 'Profile updated';
      error_log("Profile updated sucessfully ".$_POST['make']);
      header( 'Location: index.php' ) ;
      return;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Kirill Bogoslovskii - Resume Registry</title>
</head>
<body>
<div class="container">
<h1>Editing Profile </h1>
<p><b>Note:</b> All fields are required.</p>

<?php
// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

// Select data from database to show on the screen
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :pid");
$stmt->execute(array(":pid" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for ID';
    header( 'Location: index.php' ) ;
    return;
}

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$he = htmlentities($row['headline']);
$su = htmlentities($row['summary']);
$pid = $row['profile_id'];
?>

<p>Edit Profile</p>
<form method="post">
<p>First Name:
<input type="text" name="first_name" value="<?= $fn ?>" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" value="<?= $ln ?>" size="60"/></p>
<p>Email:
<input type="text" name="email" value="<?= $em ?>" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" value="<?= $he ?>" size="80"/></p>
<p>Summary:<br/>
<textarea rows="8" cols="80" name="summary" ><?= $su ?></textarea></p>
<input type="hidden" name="profile_id" value="<?= $pid ?>">

<p><input type="submit" value="Save"/>
<input type="submit" name="cancel" value="Cancel"></p>
</form>
</div>
</body>
</html>
