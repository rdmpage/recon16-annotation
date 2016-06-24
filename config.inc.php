<?php

// $Id: //

/**
 * @file config.php
 *
 * Global configuration variables (may be added to by other modules).
 *
 */

global $config;

// Date timezone
date_default_timezone_set('UTC');


// CouchDB--------------------------------------------------------------------------------
// local
$config['couchdb_options'] = array(
		'database' => 'annotation',
		'host' => 'localhost',
		'port' => 5984,
		'prefix' => 'http://'
		);		


// Cloudant
$config['couchdb_options'] = array(
		'database' => 'annotation',
		'host' => 'recon16:recon16password@recon16.cloudant.com',
		'port' => 5984,
		'prefix' => 'http://'
		);	

$config['proxy_name'] = '';
$config['proxy_port'] = '';

		
// HTTP proxy
if ($config['proxy_name'] != '')
{
	if ($config['couchdb_options']['host'] != 'localhost')
	{
		$config['couchdb_options']['proxy'] = $config['proxy_name'] . ':' . $config['proxy_port'];
	}
}

$config['stale'] = true;


	
?>