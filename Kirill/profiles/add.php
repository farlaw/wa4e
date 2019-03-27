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
    header('Location: index.php');
    return;
}

// Check if all fields are set
if ( isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['email']) && isset($_POST['headline'])
     && isset($_POST['summary'])) {

  // Data validation
  if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 ||
      strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1
      || strlen($_POST['summary']) < 1)
       {
      $_SESSION['error'] = 'All fields are required';
      error_log("At least one field is empty ".$_POST['first_name']." ".$_POST['last_name']);
      header("Location: add.php");
      return;
    }
  elseif (strpos($_POST['email'],'@') < 1) {
        $_SESSION["error"] = "Email must have an at-sign (@)";
        error_log("Email without @ sign: ".$_POST['email']);
        header( 'Location: add.php' ) ;
        return;
    }
  else {
      $sql = "INSERT INTO profile (user_id, first_name, last_name, email, headline, summary)
                VALUES (:uid, :fn, :ln, :em, :he, :su)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
          /* Make sure to mark the entry with the foreign key user_id
          of the currently logged in user */
          ':uid' => $_SESSION['user_id'],
          ':fn' => $_POST['first_name'],
          ':ln' => $_POST['last_name'],
          ':em' => $_POST['email'],
          ':he' => $_POST['headline'],
          ':su' => $_POST['summary'])
      );
      $_SESSION["success"] = "Record added";
      error_log("Record added sucessfully ".$_POST['user_id']);
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
<h1>Adding Profile for <? echo(htmlentities($_SESSION["name"])) ?></h1>
<p><b>Note:</b> All fields are required.</p>

<?php
// Flash pattern
if ( isset($_SESSION["error"]) ) {
    echo('<p style="color:red">'.htmlentities($_SESSION["error"])."</p>\n");
    unset($_SESSION["error"]);
}
?>

<form method="post">
<p>First Name:
<input type="text" name="first_name" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" size="60"/></p>
<p>Email:
<input type="text" name="email" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"></textarea>
<p>
<input type="submit" name="add" value="Add">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
</div>
</body>
</html>
