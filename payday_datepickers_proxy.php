<?php
$url = 'http://www.kayaposoft.com/enrico/json/v1.0/index.php?action=' . $_GET['action'] . '&country=' . $_GET['country'] . '&year=' . $_GET['year'] . '&jsonp=' . $_GET['jsonp'];
$response = file_get_contents( $url );
echo $response;
?>