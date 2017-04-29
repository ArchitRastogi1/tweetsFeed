<?php
require 'gets/TweetsAPI.php';
$app->get('/', function() use ($app) {
        $app->render('twitterFeed.html.twig');
});
