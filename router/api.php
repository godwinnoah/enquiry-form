<?php

use Akaninyene\Upworkone\Enquiry;
use Bramus\Router\Router;

$router = new Router();


// Route for Saving Enquiries
$router->post('api/enquiries', function(){
    $data = file_get_contents('php://input');
    $enquiry = new Enquiry();

    header("Content-type: application/json");
    echo json_encode($enquiry->save(json_decode($data, true)));
});


// Route for Fetching Enquiries
$router->get('api/enquiries', function(){
    $enquiry = new Enquiry();

    header("Content-type: application/json");
    echo json_encode($enquiry->load());
});


// Load an Enquiry Detail
$router->get('api/enquiry/{slug}', function($slug){
    $enquiry = new Enquiry();

    header("Content-type: application/json");
    echo json_encode($enquiry->loadAnEnquiryBySlug($slug));
});


// Run the Router
$router->run();