<!DOCTYPE html>
<html>
<head>
<title>Kirill B - PHP Arrays</title>
</head>
<body>
<h1>Arrays Test PHP</h1>
<p>
<?php
  $stuff = array('course ' => 'PHP-Intro', 'topic' => 'Arrays');
  print_r ($stuff['topic']);
?>
</p>
<p>
<?php
  $stuff = array('course' => 'PHP-Intro', 'topic' => 'Arrays');
  echo isset($stuff['course']);
?>
</p>
<p>
<?php
  $inp = "This is a sentence with seven words";
  $temp = explode(' ', $inp);
  print_r($temp);
  echo($temp[0]);
?>
</p>
