<?php

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/api_utils.php');
require_once (dirname(dirname(__FILE__)) . '/couchsimple.php');

//--------------------------------------------------------------------------------------------------
function default_display()
{
	echo "hi";
}



//--------------------------------------------------------------------------------------------------
// One record
function display_one ($id, $format= '', $callback = '')
{
	global $config;
	global $couch;

	$obj = null;
	
	// grab JSON from CouchDB
	$couch_id = $id;

	// fetch from CouchDB
	$obj = new stdclass;

	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . urlencode($couch_id));

	$reference = json_decode($resp);
	if (isset($reference->error))
	{
		$obj->status = 404;
	}
	else
	{
		$obj = $reference;
		$obj->status = 200;
	}

		
	api_output($obj, $callback);
}

//--------------------------------------------------------------------------------------------------
function display_document_geojson($id, $callback = '')
{
	global $config;
	global $couch;

	// fetch
	$obj = new stdclass;

	$url = '_design/representation/_view/identifier_coordinates';	
	
	$url .= '?key=' . urlencode('"' . $id . '"');

	/*
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	
	*/	

	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	$response_obj = json_decode($resp);

	$obj = new stdclass;
	$obj->status = 404;
	$obj->url = $url;

	if (isset($response_obj->error))
	{
		$obj->error = $response_obj->error;
	}
	else
	{
		if (count($response_obj->rows) == 0)
		{
			$obj->error = 'Not found';
		}
		else
		{	
			$obj->status = 200;
		
			$obj->type = 'MultiPoint';
			$obj->coordinates = array();

			foreach ($response_obj->rows as $row)
			{
				$obj->coordinates[] = $row->value;
			}
		}
	}
	
	api_output($obj, $callback);
}

//--------------------------------------------------------------------------------------------------
function display_document_cites($id, $callback = '')
{
	global $config;
	global $couch;

	// fetch
	$obj = new stdclass;


	// First pass, query article metadata that hypothes.is has extracted
	$url = '_design/highwire/_view/reference_doi';	
	
	$url .= '?key=' . urlencode('"' . $id . '"');

	/*
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	
	*/	

	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	$response_obj = json_decode($resp);

	$obj = new stdclass;
	$obj->status = 404;
	$obj->url = $url;

	if (isset($response_obj->error))
	{
		$obj->error = $response_obj->error;
	}
	else
	{
		if (count($response_obj->rows) == 0)
		{
			$obj->error = 'Not found';
		}
		else
		{	
			$obj->status = 200;
			
			$obj->cites = array();

			foreach ($response_obj->rows as $row)
			{
				$obj->cites[] = $row->value;
			}
			$obj->cites = array_unique($obj->cites);
			
		}
	}
	
	// Second pass, query the annotations
	$url = '_design/representation/_view/identifier_annotation_by_tag';	
	
	$url .= '?key=' . urlencode(json_encode(array($id, "cites")));

	/*
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	
	*/	

	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	$response_obj = json_decode($resp);

	if (count($response_obj->rows) > 0)
	{
		$obj->status = 200;
		foreach ($response_obj->rows as $row)
		{
			$obj->cites[] = $row->value;
		}
		$obj->cites = array_unique($obj->cites);
	}	
	
	api_output($obj, $callback);
}


//--------------------------------------------------------------------------------------------------
function display_document_annotations($id, $callback = '')
{
	global $config;
	global $couch;

	// fetch
	$obj = new stdclass;

	$url = '_design/representation/_view/identifier_annotation_detail';	
	
	$url .= '?key=' . urlencode('"' . $id . '"');

	/*
	if ($config['stale'])
	{
		$url .= '&stale=ok';
	}	
	*/	

	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	$response_obj = json_decode($resp);

	$obj = new stdclass;
	$obj->status = 404;
	$obj->url = $url;

	if (isset($response_obj->error))
	{
		$obj->error = $response_obj->error;
	}
	else
	{
		if (count($response_obj->rows) == 0)
		{
			$obj->error = 'Not found';
		}
		else
		{	
			$obj->status = 200;
			
			$obj->annotations = array();

			foreach ($response_obj->rows as $row)
			{
				$obj->annotations[] = $row->value;
			}
		}
	}
	
	api_output($obj, $callback);
}


//--------------------------------------------------------------------------------------------------
function main()
{
	$callback = '';
	$handled = false;
	
	//print_r($_GET);
	
	// If no query parameters 
	if (count($_GET) == 0)
	{
		default_display();
		exit(0);
	}
	
	if (isset($_GET['callback']))
	{	
		$callback = $_GET['callback'];
	}
	
	// Submit job
	if (!$handled)
	{
		if (isset($_GET['id']))
		{	
			$id = $_GET['id'];
			
			$format = '';
			
			if (isset($_GET['geojson']))
			{
				//display_geojson($id, $style);
				$handled = true;
			}
			
			if (!$handled)
			{
				// display an annotation with "id"
				display_one($id, $format, $callback);
				$handled = true;
			}
			
		}
	}

	// 
	if (!$handled)
	{
		if (isset($_GET['document']))
		{	
			$document = $_GET['document'];
			
			if (!$handled)
			{
				if (isset($_GET['geojson']))
				{			
					// list annotations for document
					display_document_geojson($document, $callback);
					$handled = true;
				}
			}
			if (!$handled)
			{
				if (isset($_GET['cites']))
				{			
					// list annotations for document
					display_document_cites($document, $callback);
					$handled = true;
				}
			}
			if (!$handled)
			{
				if (isset($_GET['annotations']))
				{			
					// list annotations for document
					display_document_annotations($document, $callback);
					$handled = true;
				}
			}
			
			
			
		}
	}
	

	
	
	if (!$handled)
	{
		default_display();
	}
	
		

}


main();

?>