<?php

use Bramus\Router\Router;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$router = new Router();

$loader = new FilesystemLoader(__DIR__."/../templates");
$twig = new Environment($loader, [
    'cache' => __DIR__.'/../tmp',
    'debug' => true
]);

// To be poetic, surely would I have loaded content into these views
// But if I did, I would have missed an opportunity at flexing JavaScript.
// So, I will not. Each view, will require their own javascript

$router->get('submit-enquiry', function() use ($twig) {
    echo $twig->display('starter.html');
});

$router->get('enquiries-saved', function() use ($twig) {
    $twig->display("confirmation.html");
});

$router->get('enquiries', function() use ($twig) {
    $twig->display("enquiries.html");
});

$router->get('enquiry/{slug}', function($slug) use ($twig) {
    echo $twig->render("enquiry.html", ['slug' => $slug]);
});


// Run the Router
$router->run();