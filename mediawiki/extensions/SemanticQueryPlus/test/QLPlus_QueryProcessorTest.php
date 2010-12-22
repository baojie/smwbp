<?php

//require_once(realpath(dirname(__FILE__) . "/QLPlus_QueryProcessor.php"));
	
$optionsWithArgs = array( 'q' );

require_once ( getenv( 'MW_INSTALL_PATH' ) !== false
	? getenv( 'MW_INSTALL_PATH' ) . "/maintenance/commandLine.inc"
	: dirname( __FILE__ ) . '/../../../maintenance/commandLine.inc' );
require_once(dirname( __FILE__ )."/../QLPlus_Setting.php");


if ( !empty( $options['q'] ) ) {
	$query_file = $options['q'];
} 
else {
	$query_file = null;
}

function testProcessQuery($query_file)
{
	global $wgQLPlus_WikiDump, $wgQLPlus_WikiOntologyDump;
	
	print "SMW-QL+ query answering test\n";
	print "Wiki dump are $wgQLPlus_WikiDump and \n $wgQLPlus_WikiOntologyDump\n";
	print "Query file is \"".$query_file."\".\n";

	if ( $query_file ) {
	
		$query_text = '';
		if (file_exists($query_file)) {
			$handle = fopen($query_file, "r"); 
			$query_text = fread($handle, filesize($query_file));
			fclose($handle);
		}
		
		print("Query is\n---------\n".$query_text."\n=========\n");
		
		$time_start = microtime(true);	

		$result = QLPlus_QueryProcessor::processQuery(
				$wgQLPlus_WikiDump, $wgQLPlus_WikiOntologyDump, $query_text);		
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		print "\n\nprint the first 20 of ". count($result). " results\n";
		array_splice($result, 20);
		print_r($result);		
		
		print "\nFinish query answering in $time seconds\n";		
		
		$time_start = microtime(true);	
		$cache_file = QLPlus_QueryProcessor::query2cache($query_text);
		//print $cache_file ."\n";
		// if the the result cache exists
		if ( file_exists($cache_file) )
		{
			// read the cache
			$result2 = QLPlus_QueryProcessor::getResultFromFile($cache_file);
			
			$time_end = microtime(true);
			$time = $time_end - $time_start;
			//print_r($result2);
			print "\nFinish query answering from cache in $time seconds\n";
		}
		//println("Result is\n");
		//print_r($result);		
	}	
}

function testCallSolver()
{
	global $wgQLPlus_WikiDump, $wgQLPlus_WikiOntologyDump, $wgQLPlus_TempFilePath;
	$dump_file = $wgQLPlus_WikiDump;
	$ont_file = $wgQLPlus_WikiOntologyDump;
	$tmp_query = "test.ql.lp";
	$tmp_result = $wgQLPlus_TempFilePath . "/result.tmp";
	QLPlus_QueryProcessor::callSolver($dump_file, $ont_file, $tmp_query, $tmp_result);
	
	$result = file_get_contents($tmp_result);
	echo $result;
}

//testCallSolver();
testProcessQuery($query_file);
?>