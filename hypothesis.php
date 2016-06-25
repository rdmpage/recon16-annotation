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
		
		//print_r($annotation);
		
		// can we interpret the annotations?
		
		// get the relevant text, either the user has entered some text or selected some text
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
	$url = 'https://hypothes.is/a/rXY93DlkEeaLbeMLB88DJA';
	
	$ids = array(
'QQnznDraEea1qHts0xaL-g',
'TfClNDrZEea1pwcGiDwNPQ',
'V5mCpDrZEea1BFMNEQPuRQ',
'W3z0BDraEeaMdWNUeKOJDw',
'Y0UqGDrZEeaHyLdPMVENew',
'YR7rxDraEeaIxcNcV5xt2g',
'ZPYPhDraEea1BX-zkQC0UA',
'aSuwLDraEeaIxofsHrtUiQ',
'bPUllDraEea1Bk8N0M6WPA',
'cTssjjraEea1B1_NBTo2VQ',
'dNa5xjraEea7Ug8V_4sKzQ',
'eMFsrDraEeaIx-OCY8J3ng',
'fLrGljraEea1qQOTv8VHxg',
'fo_4cDrZEeabkR-GvWO2Sw',
'g6xERDrZEeaqAZPfSmPrzA',
'iRDO3DrZEeabkncRZe3Ixg',
'qj3faDraEea1CJdgXUXYcA',
'rtrDODraEeaMdsszKQ5TLg',
'uiTPBDraEeaqAneYGMKT7g'
	);
	
	$ids = array('dNa5xjraEea7Ug8V_4sKzQ');
	
	foreach ($ids as $id)
	{
		$url = 'https://hypothes.is/a/' . $id; 
		store_annotation($url);
	}
}



