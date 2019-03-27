<?php

//return flash message "sucess" or "error"
function flashMessages() {
  if ( isset($_SESSION['success']) ) {
        echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
        unset($_SESSION['success']);
  }
  if ( isset($_SESSION['error']) ) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
        unset($_SESSION['error']);
  }
}

// validate that all fields are filled in and that email has "@" sign
function validateProfile() {
    if ( strlen($_POST['first_name']) == 0 || strlen($_POST['last_name']) == 0 ||
         strlen($_POST['email']) == 0 || strlen($_POST['headline']) == 0
         || strlen($_POST['summary']) == 0) {
        return "ValidateProfile: All fields are required";
    }

    if (strpos($_POST['email'],'@') === false) {
        return "Email address must contain at-sign (@)";
    }
    return true;
}

// Look through the POST data and return true or error message
function validatePos() {
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            return "All fields are required";
        }

        if ( ! is_numeric($year) ) {
            return "Position year must be numeric";
        }
    }
    return true;
}

/* NOTE: What fetchAll() dows:
    $profiles = array();
    whlie ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        $profiles[]=$row;
    }
*/
function loadPos($pdo, $profile_id) {
      $stmt = $pdo->prepare('SELECT * FROM Position
          WHERE profile_id = :prof ORDER BY rank');
      $stmt->execute(array( ':prof' => $profile_id)) ;
      $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $positions;
}
