<?php

require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/lib.php');
require_once (dirname(__FILE__) . '/couchsimple.php');
require_once (dirname(__FILE__) . '/geocode.php');

//----------------------------------------------------------------------------------------
function get_annotation($url)
{
	$annotation = null;
	
	$url = str_replace('https://hypothes.is/a/', 'https://hypothes.is/api/annotations/', $url);

	$json = get($url);

	if ($json != '')
	{
		$annotation = json_decode($json);

		$annotation->_id = $annotation->id;
		
		print_r($annotation);
		
		// get stuff
		
		//echo "---\n";
		
		$text = $annotation->text;
		if ($text == '')
		{
			foreach ($annotation->target[0]->selector as $selector)
			{
				//print_r($selector);
				
				if ($selector->type == 'TextQuoteSelector')
				{
					$text = $selector->exact;
				}
			}
		}
		//echo "---\n";
		
		// post process
		foreach ($annotation->tags as $tag)
		{
			switch ($tag)
			{
				case 'geo':
					if ($text != '')
					{
						echo $text . "\n";
						$results = find_points($text);
						
						if (count($results) == 1)
						{
							$annotation->type = $results[0]->feature->type;
							$annotation->geometry = $results[0]->feature->geometry;
						}
					}					
					break;
					
				default:
					break;
			}
		}
		
		//exit();
		
	}

	return $annotation;
}

//----------------------------------------------------------------------------------------
function store_annotation($url)
{
	global $couch;
	
	$annotation = get_annotation($url);
	if ($annotation)
	{
		$couch->add_update_or_delete_document($annotation,  $annotation->_id);
	}
}


// test
if (0)
{
	$url = 'https://hypothes.is/a/Q9YPfjk3Eea2kau3b2M4DQ';
	
	$url = 'https://hypothes.is/a/Xs1tFjlDEeav988WRCbyxQ';

	store_annotation($url);
}



