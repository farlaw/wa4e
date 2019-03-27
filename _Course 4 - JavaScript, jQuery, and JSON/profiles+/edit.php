<?php
require_once "pdo.php";
require_once "util.php";

session_start();

// Check if the user is logged in
if ( ! isset($_SESSION['name']) ) {
    die('ACCESS DENIED');
}

// If the user pressed Cancel go back to view.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

// Guardian: Make sure that profile_id is present
if ( ! isset($_REQUEST['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

// Check to see if the user_id owns the profile
$stmt = $pdo->prepare('SELECT * FROM profile
   WHERE profile_id = :prof AND user_id = :uid');
$stmt->execute(array( ':prof' => $_REQUEST['profile_id'],
    ':uid' => $_SESSION['user_id']));
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $profile === false) {
    $_SESSION["error"] = "You are not authorized to edit this profile";
    error_log("currently logged in user does not own profile: "
      .$profile["user_id"]." vs. ".$_SESSION['user_id']);
    header( 'Location: index.php');
    return;
  }


// Handle the incoming data
if ( isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['email']) && isset($_POST['headline'])
      && isset($_POST['summary']) ) {

    // Data validation
    $msg = validateProfile();
    if ( is_string ($msg) ) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    }

    // Validate position entries if present
    $msg = validatePos();
    if ( is_string($msg) ) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    }

    // Update database
    $stmt = $pdo->prepare('UPDATE profile SET
            first_name = :fn, last_name = :ln,
            email = :em, headline = :he,
            summary = :su
            WHERE profile_id = :pid AND user_id =:uid');
    $stmt->execute(array(
      ':fn' => $_POST['first_name'],
      ':ln' => $_POST['last_name'],
      ':em' => $_POST['email'],
      ':he' => $_POST['headline'],
      ':su' => $_POST['summary'],
      ':pid' => $_REQUEST['profile_id'],
      ':uid' => $_SESSION['user_id'])
    );

    // Clear out the old position entries
    $stmt = $pdo->prepare('DELETE FROM position
        WHERE profile_id =:pid');
    $stmt->execute(array(
      ':pid' => $_REQUEST['profile_id']));

    // Insert the position entries
    $rank = 1;
    for ($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        $stmt = $pdo->prepare('INSERT INTO position
           (profile_id, rank, year, description)
           VALUES (:pid, :rank, :year, :desc)');
        $stmt->execute(array(
            ':pid' => $_REQUEST['profile_id'],
            ':rank' => $rank,
            ':year' => $year,
            ':desc' => $desc)
        );
        $rank++;
    }

    $_SESSION['success'] = 'Profile updated';
    error_log("Profile updated sucessfully ".$_POST['make']);
    header( 'Location: index.php' ) ;
    return;
}

// Load up the position rows
$positions = loadPos($pdo, $_REQUEST['profile_id']);

?>

<!DOCTYPE html>
<html>
<head>
<title>Kirill Bogoslovskii - Resume Registry</title>
<?php require_once "head.php"; ?>
</head>
<body>
<div class="container">
<h1>Editing Profile for <?php echo(htmlentities($_SESSION["name"])); ?></h1>
<?php flashMessages(); ?>

<?php
$fn = htmlentities($profile['first_name']);
$ln = htmlentities($profile['last_name']);
$em = htmlentities($profile['email']);
$he = htmlentities($profile['headline']);
$su = htmlentities($profile['summary']);
$pid = htmlentities($_GET['profile_id']);
?>

<form method="post" action="edit.php">
<input type="hidden" name="profile_id" value="<?= $pid ?>">
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

<?php

$pos = 0;
echo('<p>Position: <input type="submit" id="addPos" value="+">'."\n");
echo('<div id="position_fields">'."\n");
foreach ($positions as $position) {
    $pos++;
    echo('<div id="position'.$pos.'">'."\n");
    echo('<p>Year: <input type="text" name="year'.$pos.'"');
    echo(' value="'.$position['year'].'" />'."\n");
    echo('<input type="button" value="-" ');
    echo('onclick="$(\'#position'.$pos.'\').remove();return false;">'."\n");
    echo("</p>\n");
    echo('<textarea name="desc'.$pos.'" rows="8" cols="80">'."\n");
    echo(htmlentities($position['description'])."\n");
    echo("\n</textarea>\n</div>\n");
}
echo("</div></p>\n");
?>

<p>
<input type="submit" value="Save"/>
<input type="submit" name="cancel" value="Cancel">
</p>
</form>

<script>
countPos = <?= $pos ?>;
// creating a year+position fields on click of "+" sign
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        //http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of 9 position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Addng position "+countPos);
        $('#position_fields').append(
          '<div id="position'+countPos+'"> \
          <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
          <input type="button" value="-" \
              onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
          <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
          </div>');
    });
});
</script>

</div>
</body>
</html>
