<?php
$url = 'http://www.routingnumbers.info/api/data.json?rn=' . $_GET['rn'] . '&callback=' . $_GET['callback'];
$response = file_get_contents( $url );
echo $response;
?>