<?php

echo 'hi';

$pdo = new PDO('mysql:host=localhost;dbname=lara1', 'root', '');
$debug = 1;

?>

<html lang="en">

<body>

<form action="./xss1-xdb.php" method="post">
    <label for="description">Product Description</label>
    <input type="text" id="description" name="description">
    <button type="submit">submit</button>
</form>

</body>

</html>
