<?php
require_once "pdo.php";
echo "<pre>\n";
$stmt = $pdo->query("SELECT * FROM users");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    print_r($row);
}
echo "</pre>\n";
?>
