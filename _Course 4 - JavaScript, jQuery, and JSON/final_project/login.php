<?php // Do not put any HTML above this line
session_start();
require_once "head.php";
require_once "util.php";
require_once "pdo.php";

$salt = 'XyZzy12*_';
// hashes of passwords are stored in the database

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    // Logout current user
    unset($_SESSION["name"]);
    unset($_SESSION["user_id"]);
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION["error"] = "User name and password are required";
        header( 'Location: login.php' ) ;
        return;
    } elseif (strpos($_POST['email'],'@') < 1) {
        $_SESSION["error"] = "Email must have an at-sign (@)";
        header( 'Location: login.php' ) ;
        return;
    }
    // Compute hash of salt+pass and store in "$check" variable
    $check = hash('md5', $salt.$_POST['pass']);
    $stmt = $pdo->prepare('SELECT user_id, name FROM users
      WHERE email = :em AND password = :pw');
    $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
    // try to a row from mysql where email and (hashed) pw are what the user inserted
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // if the database found a row, the email+pass is a valid pair
    if ( $row !== false ) {
        $_SESSION["name"] = $row['name'];
        // $_SESSION['user_id'] is required to add/edit/delete data
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION["success"] = "Logged in.";
        error_log("Login success ".$_POST["email"]);
        header( 'Location: index.php' ) ;
        return;
    } else {
        $_SESSION["error"] = "Incorrect password";
        error_log("Login fail ".$_POST['email']." $check");
        header( 'Location: login.php' ) ;
        return;
        }
}
// Fall through into the View
?>

<!DOCTYPE html>
<html>
<head>
<title>Kirill Bogoslovskii - Resume Registry</title>
</head>
<body>

<div class="container">
<h1>Please Log In</h1>
<?php
    if ( isset($_SESSION["error"]) ) {
        echo('<p style="color:red">'.htmlentities($_SESSION["error"])."</p>\n");
        unset($_SESSION["error"]);
    }

?>
<form method="POST">
<label for="email">Email</label>
<input type="text" name="email" id="addr"><br/>
<label for="id_1723">Password</label>
<input type="password" name="pass" id="id_1723"><br/>
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is the programming language (all lower case)
 followed by 123. -->
</p>
</div>
</body>
</html>

<script>
  function doValidate() {
      console.log('Validating...');
      try {
          addr = document.getElementById('addr').value;
          pw = document.getElementById('id_1723').value;
          console.log("Validating addr="+addr+" pw="+pw);
          if (addr == null || addr == "" || pw == null || pw == "") {
              alert("Both fields must be filled out");
              return false;
          }
          if ( addr.indexOf('@') == -1) {
              alert("Invalid email address");
              return false;
          }
          return true;
      } catch(e) {
          return false;
      }
      return false;
  }
</script>
