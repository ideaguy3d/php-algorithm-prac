<?php
/**
 * Created by PhpStorm.
 * User: Julius Alvarado
 * Date: 8/17/2019
 * Time: 3:35 PM
 */

$scriptStruct = new stdclass;
$scriptStruct->output = '';

$rawData = 'Data with bad <img src="http://localhost/php-security/verybadwebsite/bad.php" /> '
    . 'tags <script>alert("YOU HAVE BEEN HACKED")</script>';

//e($rawData);

e(strip_tags($rawData));

// validate
// test array of city names, simulates user input
$cityTest = [
    'This is a very long city name which does not follow the rule where the length of the city name must be less than one hundred twenty eight characters',
    'London', '', 'San Francisco',
];

recurseCities($cityTest);

$rawData = '<script>alert("XSS Attack");</script>';
$output = '';

if($_POST['raw'] ?? null) {
    $scriptStruct->output = '<br> raw data = ' . $rawData;
}
else if($_POST['escaped'] ?? null) {
    $scriptStruct->output = '<br> escaped data = ' . htmlentities($rawData);
}

function e(string $s): void {
    echo "\n<br>\n";
    echo $s;
    echo "\n<br>\n";
}

function recurseCities(array $cities) {
    static $minLenCityName = 1;
    static $maxLenCityName = 128;
    $city = array_splice($cities, 0, 1)[0];
    
    if(strlen($city) > $maxLenCityName) {
        echo '<li><b style="color: red;">INVALID</b>: City Name Too Long! [' . substr($city, 0, 20) . '...]' . PHP_EOL;
        echo '<br /><i>Must be no more than ' . $maxLenCityName . ' letters in length!</i></li>' . PHP_EOL;
    }
    else if(strlen($city) < $minLenCityName) {
        echo '<li><b style="color: #ff0000;">INVALID</b>: City Name Too Short! [' . substr($city, 0, 20) . ']' . PHP_EOL;
        echo '<br /><i>Must be at at least ' . $minLenCityName . ' letter(s) in length!</i></li>' . PHP_EOL;
    }
    else {
        echo '<li><b style="color: green;">VALID</b> [' . $city . ']</li>' . PHP_EOL;
    }
    
    if(!empty($cities)) recurseCities($cities);
}


$testData = [
    12345,
    12345.678,
    'Non Numeric',
    '<script>alert("XSS Attack");</script>',
    'Test string with no tags',
    'Test <b>string</b> with <i>harmless</i> tags',
    'Test <b>string</b> with bogus image <img src="http://verybadwebsite/badcode.php" />',
    'Test string with javascript <script>alert("XSS Attack");</script>',
];

$output = '<table border=1><tr>';
$output .= '<th>Escaped Original</th>'
    . '<th>(int)$data</th>'
    . '<th>(float)$data</th>'
    . '<th>strip_tags($data)</th>'
    . '<th>strip_tags($data, "&lt;b&gt;&lt;i&gt;")</th>'
    . '</tr>' . PHP_EOL;

testDataRecursion($testData);

function testDataRecursion(array $testData): void {
    global $output;
    $item = array_shift($testData);
    
    $notWhitelisted = strip_tags($item);
    $whitelisted = strip_tags($item, '<b><i>');
    
    $output .= '<tr>'
        . '<td>' . htmlspecialchars($item) . '</td>'
        // security via type conversion
        . '<td>' . (int)$item . '</td>'
        . '<td>' . (float)$item . '</td>'
        // security via filtering
        . "<td>$notWhitelisted</td>"
        . "<td>$whitelisted</td>"
        . '</tr>' . PHP_EOL;
    
    if(!empty($testData)) testDataRecursion($testData);
}

echo "<br><hr>$output";
echo '</table><hr><br>' . PHP_EOL;

// end of PHP embed code
?>

<!DOCTYPE HTML>
<head>
    <title>Output Escape Test</title>
    <meta charset="utf-8">
</head>
<body>
<?= $scriptStruct->output ?>

<!-- by default will submit to itself -->
<form method="post">
    <input type="submit" name="raw" value="Raw"/>
    <input type="submit" name="escaped" value="Escaped"/>
</form>
</body>


