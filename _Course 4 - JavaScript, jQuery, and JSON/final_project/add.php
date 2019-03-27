<?php
require_once "head.php";
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

// Check if all fields are set
if ( isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['email']) && isset($_POST['headline'])
     && isset($_POST['summary'])) {

      // validate that all fields are filled in and that email has "@" sign
      $msg = validateProfile(); //it might be true or a string (if error)
      if ( is_string($msg) ) {
          $_SESSION['error'] = $msg;
          header("Location: add.php");
          return;
      }

      // validate position entries if present
      $msg = validatePos();
      if ( is_string($msg) ) {
          $_SESSION['error'] = $msg;
          header("Location: add.php");
          return;
      }

      // Validate education entries if present
      $msg = validateEdu();
      if ( is_string($msg) ) {
          $_SESSION['error'] = $msg;
          header("Location: add.php");
          return;
      }

      // Data is valid - time to insert
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
      // get the primary key (profile_id) of the last inserted id
      $profile_id = $pdo->lastInsertId();

      // Insert the postiion entries
      insertPositions($pdo, $profile_id);

      // Insert the education entries
      insertEducations($pdo, $profile_id);

      $_SESSION["success"] = "Profile added";
      error_log("Record added sucessfully ".$_POST['user_id']);
      header( 'Location: index.php' ) ;
      return;

}

?>

<!DOCTYPE html>
<html>
<head>
<title>Kirill Bogoslovskii - Resume Registry</title>
</head>
<body>
<div class="container">
<h1>Adding Profile for <?php echo(htmlentities($_SESSION["name"])); ?></h1>
<?php flashMessages(); ?>
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
Education: <input type="submit" id="addEdu" value="+">
<div id="edu_fields">
</div>
<p>
Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
</div>
</p>
<p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
<script>
countEdu = 0;
countPos = 0;


$(document).ready(function(){
    window.console && console.log('Document ready called');

    $('#addEdu').click(function(event){
        //http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of 9 education entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Addng Education "+countEdu);

        // Grab some HTML with hot spots and insert into the DOM
        var source = $("#edu-template").html();
        $('#edu_fields').append(source.replace(/@COUNT@/g,countEdu));

        // Add the event handler to the new ones
        $('.school').autocomplete({
            source: "school.php"
        });
    });

    $('.school').autocomplete({
        source: "school.php"
    });

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

<!-- HTML with Substitution hot spots -->
<script id="edu-template" type="text">
    <div id="edu@COUNT@">
      <p>Year: <input type="text" name="edu_year@COUNT@" value="" />
      <input type="button" value="-" onclick="$('#edu@COUNT@').remove();return false;"><br>
      <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value="" />
      </p>
    </div>
</script>

</div>
</body>
</html>
