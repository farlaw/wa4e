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
            return "ValidatePos: All fields are required";
        }

        if ( ! is_numeric($year) ) {
            return "Position year must be numeric";
        }
    }
    return true;
}

// Look through the POST data and return true or error message
function validateEdu() {
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['edu_year'.$i]) ) continue;
        if ( ! isset($_POST['edu_school'.$i]) ) continue;
        $year = $_POST['edu_year'.$i];
        $school = $_POST['edu_school'.$i];
        if ( strlen($year) == 0 || strlen($school) == 0 ) {
            return "All fields are required";
        }

        if ( ! is_numeric($year) ) {
            return "Education year must be numeric";
        }
    }
    return true;
}


/* NOTE: What fetchAll() does: $profiles = array();
    whlie ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        $profiles[]=$row;}
*/
function loadPos($pdo, $profile_id) {
      $stmt = $pdo->prepare('SELECT * FROM Position
          WHERE profile_id = :prof ORDER BY rank');
      $stmt->execute(array( ':prof' => $profile_id)) ;
      $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $positions;
}

function loadEdu($pdo, $profile_id) {
      $stmt = $pdo->prepare('SELECT year, name FROM Education
          JOIN Institution
          ON Education.institution_id = Institution.institution_id
          WHERE profile_id = :prof ORDER BY rank');
      $stmt->execute(array( ':prof' => $profile_id)) ;
      $educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $educations;
}

function insertPositions($pdo, $profile_id) {
      $rank = 1;
      for ($i=1; $i<=9; $i++) {
          if ( ! isset($_POST['year'.$i]) ) continue;
          if ( ! isset($_POST['desc'.$i]) ) continue;
          $year = $_POST['year'.$i];
          $desc = $_POST['desc'.$i];

          $stmt = $pdo->prepare('INSERT INTO Position
             (profile_id, rank, year, description)
             VALUES (:pid, :rank, :year, :desc)');
          $stmt->execute(array(
              ':pid' => $profile_id,
              ':rank' => $rank,
              ':year' => $year,
              ':desc' => $desc)
          );
          $rank++;
      }
}

function insertEducations($pdo, $profile_id) {
      $rank = 1;
      for ($i=1; $i<=9; $i++) {
          if ( ! isset($_POST['edu_year'.$i]) ) continue;
          if ( ! isset($_POST['edu_school'.$i]) ) continue;
          $year = $_POST['edu_year'.$i];
          $school = $_POST['edu_school'.$i];

          // First, lookup the school if it is in the database
          $institution_id = false;
          $stmt = $pdo->prepare('SELECT institution_id FROM
               Institution WHERE name = :name');
          $stmt->execute(array(':name' => $school));
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          if ( $row !== false ) $institution_id = $row['institution_id'];

          // Second, if there is no Institution, insert it
          if ( $institution_id === false) {
              $stmt = $pdo->prepare('INSERT INTO Institution
                 (name) VALUES (:name)');
              $stmt->execute(array(':name' => $school));
              $institution_id = $pdo->lastInsertId();
          }

          $stmt = $pdo->prepare('INSERT INTO Education
             (profile_id, rank, year, institution_id)
             VALUES (:pid, :rank, :year, :iid)');
          $stmt->execute(array(
              ':pid' => $profile_id,
              ':rank' => $rank,
              ':year' => $year,
              ':iid' => $institution_id)
          );
          $rank++;
      }
}
