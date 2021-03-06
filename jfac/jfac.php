
<?php

return function($facId, $timesVisited, $clientFac) { ?>

<!doctype html>

<html lang="en" data-ng-app="myApp">

<head>
    <meta charset="UTF-8">
    <title>Julius3d.com app</title>
    <meta name="viewport" content="width=device-width, user-scalable=no">

    <!-- This will be getting required by PHP relative to roojfac -->
    <!--<link href='https://fonts.googleapis.com/css?family=Lato:400,100,700,900' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="jfac/css/font-awesome.min.css">
    <link rel="stylesheet" href="jfac/css/style.css">
    <link rel="stylesheet" href="jfac/css/julius.css">-->

</head>


<body>


<!-- horizontal navigation, relative to PHP root
<nav class="cf" ng-include="'jfac/views/nav.html'"></nav>
-->

<br><br>

<!-- app entry point -->
<main class="cf" ng-view></main>


<br><br><br><br><br>


<footer class="j-text-center">
    <p><small>For the best mailing and printing, </small></p>
    <a href="https://plamigo.co">redstonprintmail.com</a>
</footer>


<!-- Vendor JavaScript -->
<script src="jfac/js/lib/angular/angular.min.js"></script>
<script src="jfac/js/lib/angular/angular-route.min.js"></script>
<script src="jfac/js/lib/angular/angular-animate.min.js"></script>

<script>
    let facIdGlobal = "<?= $facId ?>";
    let facSelectGlobal = "<?= $clientFac ?>";
</script>

<!-- Custom JavaScript -->
<script src="jfac/js/app.js"></script>
<!-- data controllers -->
<script src="jfac/js/controllers/ctrl.cookie.js"></script>

</body>
</html>

<?php }; // close the function