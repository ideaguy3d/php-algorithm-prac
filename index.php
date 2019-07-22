<?php

require __DIR__ . '\vendor\autoload.php';

$homeCtrl = new \Latchel\HomeController();
/**
 * @var array
 */
$index = $homeCtrl->index();

$app = 'app';
$posts = [];

$render = require 'templates/template.blade.php';
$render($app, $posts);
