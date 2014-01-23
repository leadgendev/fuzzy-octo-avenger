<?php
//Year, Make, Model Lookup
define('AFFILIATE_ID', 'CD1466');
define('PASSWORD','trapa6');

$year = $_POST['vehicleYear'];
$make = $_POST['vehicleMake'];

#### GET VEHICLE MODEL: ####
$service = "VEHICLEMODELSLOOKUP";
$post_to_url = "https://services.mossexchange.com/DataAPI/MXAPIData.aspx";
$post_data = array("affiliate_id" => AFFILIATE_ID, "password" => PASSWORD, "year" => "$year", "make" => "$make", "service" => "$service");

$post = http_build_query($post_data);

$context = stream_context_create(array("http" => array(
    "method" => "POST",
    "header" => "Content-Type: application/x-www-form-urlencoded\r\n" .
        "Content-Length: " . strlen($post) . "\r\n",
    "content" => $post,
)));

$page = file_get_contents($post_to_url, false, $context);
//$xml = simplexml_load_file($page); // $page must be an URL here!

echo $page;