<?php

require_once("QLPlus_Setting.php");
require_once("QLPlus_AskQueryParser.php");

class QLPlus_QueryProcessor{

	static function computeClosure($dump_file, $ont_file)
	{
		global $wgQLPlus_DlvPath, $wgQLPlus_TempFilePath, $wgQLPlus_WikiClosure;
		
		$command = "$wgQLPlus_DlvPath -silent $dump_file $ont_file > $wgQLPlus_WikiClosure";
		exec($command);
		$file = @file_get_contents($wgQLPlus_WikiClosure);
		if($file) {
			$file = str_replace("), ", '). ', $file);
			$file[0]=' ';
			for ($i = strlen($file)-1; $i>0 ; $i--)
			{
				if ($file[$i] == '}'){
					$file[$i]='.'; break;
				}
			}
			$fp = fopen($wgQLPlus_WikiClosure, 'w');
			fwrite($fp, $file);
			fclose($fp);
		}
	}

	// return $output - log and error information
	// $tmp_result is the file containing resul result, on per line
	static function callSolver($dump_file, $ont_file, $tmp_query, $tmp_result)
	{
		global $wgQLPlus_DlvPath, $wgQLPlus_Solver, $wgQLPlus_DoInference, 
			   $wgQLPlus_UseClosure, $wgQLPlus_WikiClosure;
		$command = null;
		
		$output ='';
		if ($wgQLPlus_DoInference == false)
			$ont_file = '';
		if ($wgQLPlus_Solver == 'dlv' || $wgQLPlus_Solver == 'dlvdb')
		{
			if ($wgQLPlus_UseClosure){
				$command = "$wgQLPlus_DlvPath -brave -silent" .
						   " $wgQLPlus_WikiClosure $tmp_query > $tmp_result";
			}
			else
				$command = "$wgQLPlus_DlvPath -brave -silent" .
						   " $dump_file $ont_file $tmp_query > $tmp_result";
				//$command = "$wgQLPlus_DlvPath > $tmp_result";
		}
		if ($command){
			$output .= "$command\n";
			exec($command, $info);
			//$r = system($command);
			//$output .= print_r($info,true);			
		}
		return $output;
	}

	static function processQuery($dump_file, $ont_file, $query_text, &$error_info="")
	{
		global $wgQLPlus_TempFilePath, $wgQLPlus_UseCache;
		
		$t1 = microtime(true);	
		$q = AskQueryParser::parse($query_text);
		$q->toLP(true); // translate to LP with simplification
		
		$error_info .= "\nTranslated query\n";
		$error_info .= $q->rules->toString(false);
		//$error_info .= "\nTranslated unescaped query\n";
		//$error_info .= $q->rules->toString(true); // unescape
		$error_info .= "\n";
		
		$t2 = microtime(true);	
		$error_info .= ($t2-$t1 . "s in parsing\n"); 
		$t1 = microtime(true);
		
		// write the translated rules into a temp file
		$tmp_query = tempnam($wgQLPlus_TempFilePath, "QUERY");
		
		$error_info .= ("write query to ". $tmp_query."\n");
		$q->toFile($tmp_query);
		
		$tmp_result = QLPlus_QueryProcessor::query2cache($query_text);//tempnam($wgQLPlus_TempFilePath, "RESULT");
		$error_info .= ("write result to ". $tmp_result."\n");
		
		//passthru("dlv -brave -silent dump.lp $tmp_query");
		// result is in a file $tmp_result, one result per line
		$output = QLPlus_QueryProcessor::callSolver($dump_file, $ont_file, $tmp_query, $tmp_result);
		$error_info .= "\n".$output;

		$t2 = microtime(true);	
		$error_info .= ($t2-$t1 . "s in reasoning\n");
		$t1 = microtime(true);
		
		$result = array();
		if (file_exists($tmp_result)){
			$result = QLPlus_QueryProcessor::getResultFromFile($tmp_result);	
		}	

		$t2 = microtime(true);	
		$error_info .= ($t2-$t1 . "s in fetching results\n"); 
		$t1 = microtime(true);
			
		@unlink($tmp_query);// remove the temp translated query file
		if ($wgQLPlus_UseCache == false){
			@unlink($tmp_result);// this removes the file
		}
		return $result;
	}
	
	static function query2cache($query_text)
	{
		global $wgQLPlus_TempFilePath;
		//println($wgQLPlus_TempFilePath . "/" . hash('ripemd160',$query_text));
		//println(realpath($wgQLPlus_TempFilePath . "\\" . hash('ripemd160',$query_text)));
		return $wgQLPlus_TempFilePath . "/" . hash('ripemd160',$query_text);
	}
	
	// result file: one page name per line
	static function getResultFromFile($result_file)
	{
		$result = array();
		$handle2 = fopen($result_file, "r");

		if ($handle2) {
			while (($buffer = fgets($handle2, 4096)) !== false) {
				// the last character is '\n';
				//echo $buffer ."\n";
				$one_result = Rule::unesacpe($buffer);
				if (strlen(trim($one_result))>0)
					$result[]= $one_result;
				//echo $one_result . "\n";
			}
			if (!feof($handle2)) {
				echo "Error: unexpected fgets() fail\n";
			}
			fclose($handle2);
		}
		return $result;
	}

	// copied from  SMWQueryProcessor::getResultFormat
	static function getResultFormat( array $params ) {
		$format = 'auto';

		if ( array_key_exists( 'format', $params ) ) {
			global $smwgResultFormats;

			$format = strtolower( trim( $params['format'] ) );

			if ( !array_key_exists( $format, $smwgResultFormats ) ) {
				$isAlias = self::resolveFormatAliases( $format );
				if ( !$isAlias ) {
					$format = 'auto';  // If it is an unknown format, defaults to list/table again
				}
			}
		}

		return $format;
	}
}
?>