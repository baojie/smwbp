<?php

/*
Class to define hooks and parser functions

refer SMW_Setup.php, SMW_Ask.php, SMW_QP_Table.php, SMW_QueryResult.php

*/

require_once("QLPlus_Setting.php");
require_once("QLPlus_QueryProcessor.php");
require_once("QLPlus_SMW2LP.php");

class SemanticQueryPlus
{
	//Define a hook for #askplus
	static function render( $parser ) {	
		
		$time_start = microtime(true);
		
		$result = "";

		// step 1: get the input query

		global 	$wgQLPlus_WikiDump, $wgQLPlus_WikiOntologyDump,
				$wgQLPlus_TempFilePath, $wgQLPlus_UseCache;
		$output ="<pre>";
			
		$rawparams = func_get_args();
		array_shift( $rawparams ); // We already know the $parser ...
		//$output .=   print_r($rawparams, true) ;
		
		// step 2: process the query and get result 
		$rawquery = "{{#ask: " . $rawparams[0] ."}}";
		//$output .=  $rawquery;
		//if we can get the answer from cache
		$rawresult = null;
		if ($wgQLPlus_UseCache){
			$cache_file = QLPlus_QueryProcessor::query2cache($rawquery);
			$output .= $cache_file ."<br/>";
			
			// if it is a purge action, remove the file  if it is exist, skip the rest - will rebuld the cache
			global $action;
 			if ($action == 'purge')
			{
				if ( file_exists($cache_file) ) unlink($cache_file);
			}			
			// if the the result cache exists
			 
			if ( file_exists($cache_file) )
			{
				// read the cache
				$rawresult = QLPlus_QueryProcessor::getResultFromFile($cache_file);
				$output .= "From cache <br/>";
				$output .= print_r($rawresult,true);
			}
			else{
				$output .= "Cache does not exist!<br/>";
			}
			
		}		
		
		//otherwise, get the result from the lp solver
		if ($rawresult == null){
			$rawresult = QLPlus_QueryProcessor::processQuery(
					$wgQLPlus_WikiDump, $wgQLPlus_WikiOntologyDump,$rawquery,$error_info);
			$output .= "Build a new result by querying <br/>";	
			$output .= $error_info;		
		}
		$output .= print_r($rawresult,true);
				
		$time_end1 = microtime(true);
		$time1 = $time_end1 - $time_start;
		// step3: build a SMWQueryResult instance 
		$result .= SemanticQueryPlus::printResult($rawparams, $rawresult);

		SMWOutputs::commitToParser( $parser );
		
		$output .= "</pre>";
		$time_end2 = microtime(true);
		$time2 = $time_end2 - $time_end1;
		
		global $wgQLPlus_PrintTimeUsed;
		if ($wgQLPlus_PrintTimeUsed)
			$result .= "<p>Loading in $time1 seconds, printing in $time2 seconds.</p>";	

		return $result;		
		//return $output;// for debug
	}
	
	private static function printResult($rawparams, $rawresult)
	{
		// replace the real query body with a fake one to cheat the SMW query parser - since we extended the query syntax
		$rawparams[0] = "[[Category:Foo]]";
		$outputmode = SMW_OUTPUT_WIKI;
		$showmode = false;
		$context = SMWQueryProcessor::INLINE_QUERY;
		// to parse $params 
		SMWQueryProcessor::processFunctionParams( $rawparams, $querystring, $params, $printouts, $showmode );
		// out put: $params, $printouts
		//$output .=  print_r($params, true) ;

		$format = QLPlus_QueryProcessor::getResultFormat( $params );
		if ( array_key_exists( 'limit', $params ) ) 
			$limit = strtolower( trim( $params['limit'] )) ;
		
		$query  = SMWQueryProcessor::createQuery( $rawparams[0], $params, $context, $format, $printouts );
		
		//array of SMWWikiPageValue $results	
		$qr = array(); $counter = 0;
		foreach ($rawresult as $v)
		{	
			$title = Title::newFromDBkey(trim($v));
			//$output .= ($v . " " .strlen($v). "\n");
			
			if ($title)	{					
				$qr[] = SMWWikiPageValue::makePageFromTitle($title);
			}
			$counter++;
			if (isset($limit) && $counter > $limit) break;
		}
		
		$res = new SMWQueryResult($query->getDescription()->getPrintrequests(), 
				$query, $qr, smwfGetStore(), false);
		
		$printer = SMWQueryProcessor::getResultPrinter( $format, $context, $res );
		$result = $printer->getResult( $res, $params, $outputmode );
		
		return $result;
	}
	
	// hook function for action ArticleSaveComplete
	static function updatePageSave(&$article, &$user, $text, $summary,
							$minoredit, $watchthis, $sectionanchor, 
							&$flags, $revision, &$status, $baseRevId)
	{
		SMW2LP::refreshPage($article->getTitle());
		return true;
	}
	
	// hook function for action ArticleDelete
	static function updatePageDelte(&$article, &$user, $reason, $id) 
	{
		SMW2LP::refreshPage($article->getTitle());
		return true;
	}
	
	// hook function for action ArticlePurge
	static function updatePageRefresh(&$article) 
	{
		SMW2LP::refreshPage($article->getTitle());		
		return true;
	}
	
	// hook function for action ArticleUndelete
	static function updatePageUndelete($title, $create) 
	{
		SMW2LP::refreshPage($title);	
		return true;
	}
	
	// hook function for action TitleMoveComplete
	static function fnSemanticHistoryMove(&$title, &$newtitle, &$user, $oldid, $newid) 
	{
		SMW2LP::refreshPage($title);	
		SMW2LP::refreshPage($newtitle);	
		return true;
	}
}

?>