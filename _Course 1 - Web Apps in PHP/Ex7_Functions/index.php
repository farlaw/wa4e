<!DOCTYPE html>
<head><title>Kirill B - MD5 Cracker</title></head>
<body style="font-family: sans-serif">
<h1>Welcome to Kirill's MD5 Cracker</h1>
<p>This application takes an MD5 hash
of a four digit pin and check all 10,000
possible four digit PINs to determine the PIN.</p>
<pre>
<?php
$goodtext = "Not found";
// If there is no parameter, this code is all skipped
if ( isset($_GET['md5']) ) {
    $time_pre = microtime(true);
    $md5 = $_GET['md5'];
    $show = 15;
    print "Debug output:\n";
    print "\n";
    // loop go through from 1 to 1000

    for($i=0; $i<9999; $i++ ) {
        if ($i<10) {
          $try = "000".strval($i);}
          elseif ($i<100) {
            $try = "00".strval($i);}
          elseif ($i<1000) {
            $try = "0".strval($i);}
          else {
            $try = strval($i);}
        $check = hash('md5', $try);
        if ( $check == $md5 ) {
            $goodtext = $try;
            break;   // Exit the loop
        }
        // Debug output until $show hits 0
        if ( $show > 0 ) {
            print "$check $try\n";
            $show = $show - 1;
        }
    }
      // Compute elapsed time
    $time_post = microtime(true);
    print "\n";
    print "Elapsed time: ";
    print $time_post-$time_pre;
    print "\n";
}
?>
</pre>
<!-- Use the very short syntax and call htmlentities() -->
<p>PIN: <?= htmlentities($goodtext); ?></p>
<form>
<input type="text" name="md5" size="60" />
<input type="submit" value="Crack MD5"/>
</form>
<ul>
<li><a href="index.php">Reset the page</a></li>
</ul>
</body>
</html>
