<?php

require_once (dirname(__FILE__) . '/hypothesis.php');


// Webhook to receive POST request 
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
	$entityBody = file_get_contents('php://input');
	
	file_put_contents('tmp/log.txt', time() . ": " . $entityBody . "\n", FILE_APPEND);	
	
	// do something
	
	if (preg_match('/^https:/', $entityBody))
	{
		store_annotation($entityBody);
	}
}
else
{
	echo 'Error: Execting a POST request';
}

?>