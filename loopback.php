<?php
// loopback.php

$zip_code = $_POST['zip_code'];
$accept_zip_code = $_POST['accept_zip_code'];

header( 'Content-Type: text/xml' );
echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
?>
<response>
	<status><?php echo ( $zip_code == $accept_zip_code ) ? 'ACCEPTED' : 'REJECTED'; ?></status>
	<price>42.42</price>
	<redirect>https://justclickhereloans.com/</redirect>
</response>