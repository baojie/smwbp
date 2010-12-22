<?php 
// Jie Bao, 2009-02-18, 2010-12-06
// Translate SMW ask queries into logic program

// todo: rework using SMWQueryParser and SMWDescription (SMW 1.5.3 and after)
// http://semantic-mediawiki.org/doc/SMW__Description_8php-source.html
// http://semantic-mediawiki.org/doc/classSMWQueryParser.html

require_once(realpath(dirname(__FILE__) . "/QLPlus_AskQuery.php"));

class AskQueryParser {

	//$query is a tring
	static function parse($query)
	{
		$q = new AskQuery();
		$q->source_query = $query;
		
		//0- filter out newlines
		$query = trim( preg_replace( '/\s+/', ' ', $query ) ); 
		//print $query;
		
		// 1 - find query body
		$query_body = null;
		if ((preg_match('/{{#ask:(.*?)}}/is', $query , $matches)) ||
		   (preg_match('/{{#askplus:(.*?)}}/is', $query , $matches))) {
			$query_body = $matches[1];
			$q->source_query = $query;
			//println("Query Body is \n". $query_body );
		}

		if (!$query_body) {
			print("no query found\n");
			exit;
		}
		// 2 - get selectors and output columns
		$selectors = null;
		$output = null;
		// find the first single "|"
		$n = strlen($query_body);
		$bar_pos = $n;
		for($i = 0; $i < $n ; $i += 1) {
			if ($query_body[$i] == '|') {
				if ($query_body[$i - 1] != '|' && $query_body[$i + 1] != '|') {
					$bar_pos = $i; 
					//println($query_body[$i+1]);
					break;
				}
			}
		}

		$selectors = trim(substr($query_body, 0, $bar_pos));
		$output = trim(substr($query_body, $bar_pos, $n - $bar_pos));
		// println("Selectors are: \n". $selectors );
		// println("Output are: \n". $output  );

		if (!$selectors) {
			print("no selectors\n");
			exit;
		}
		
		// 3 - get selector
		$q->selectors = AskQueryParser::parseSelectors($selectors);

		return $q;
	}
	
	// input: allS - a string of all selectors
	static function parseSelectors($allS)
	{
		$allS = trim($allS);
		// println("parseSelectors: ".$allS);
		$s = new Seletors(null); // the result

		$char = null;
		$lastChar = null;
		$level = 0;
		$startAtom = - 1;
		$endAtom = - 1;
		$aSelector = array(); // hold atom selectors  temporarily in one disjunct.

		for ($pos = 0 ; $pos < strlen($allS) ; $pos += 1) {
			$lastChar = $char;
			$char = $allS[$pos];

			switch ($char) {
				case '[':
					if ($lastChar == '[') {
						$level += 1;
						if ($startAtom == - 1) $startAtom = $pos - 1;
					}
					break;
				case ']':
					if ($lastChar == ']') {
						$level -= 1;
						$endAtom = $pos;
						
						// identify the end of a selector
						if ($level == 0) {
							// println("startAtom=".$startAtom . " endAtom=".$endAtom);
							$atomic = substr($allS, $startAtom, $endAtom - $startAtom + 1);
							//println("parse Selectors: " . $atomic);
							
							$aSelector[] = AskQueryParser::parseAtomSelector($atomic);
							$startAtom = -1;
							if ($pos == strlen($allS) - 1) { // the last
								// println("parseSelectors: the last char");
								$s->disjunctions[] = $aSelector;
								$aSelector = array();
							}
						}
					}
					break;
				case 'R': case 'r': // OR , or
					if (strtolower($lastChar) == 'o') {
						if ($level == 0) {
							// a new disjunct starts
							//println("---- OR ------");
							$s->disjunctions[] = $aSelector;
							$aSelector = array();
						}
					}
				default:

			}
		}
		// println("");
		return $s;
	}
	
	static function parseAtomSelector($atom)
	{
		// remove the leading and tailiing "[[   ]]"
		$len = strlen($atom);
		$atomS = trim(substr($atom, 2, $len - 4));

		$as = new AtomSelector();
		
		// if it is negated	
		$negLen = strlen(QLPlusTerm::$NEG);	
		if (substr($atomS,0,$negLen) == QLPlusTerm::$NEG)
		{
			//print("negated!\n");
			$as->isNaf = true;
			$atomS= trim(substr($atomS, $negLen, $len - $negLen));
		}
		// if it is cardinality
		else 
		{
			$pattern = "/^\s*([<>]?=?)\s*([0-9]*)\s*" . QLPlusTerm::$CARD . "\s*(.*?::.*)$/";
			$find = preg_match($pattern, $atomS, $matches);
			if($find !== 0){
			    //print_r($matches);
				$as->isCardinality = true;
				$as->cardOperator = $matches[1];
				if (trim($as->cardOperator) == '') $as->cardOperator = '=';
				$as->cardNumber = $matches[2]; 
				$atomS = $matches[3];
			}
		}
		
		// parse the rest			
		if (preg_match('/(.*?)::(.*)/is', $atomS , $matches)) {
			$as->isPage = false;
			$as->property = trim($matches[1]); 
			//println("\t".$as->property);
			$as->conditions = AskQueryParser::parseConditions(trim($matches[2])); 
			//println("\t".trim($matches[2]));
		}else {
			$as->isPage = true;
			$as->page = $atomS; // example: Page, Category:C, :Category:C,  A||B
		}
		// print("parseAtomSelector: "); $as->toPrint(); println("");
		return $as;
	}	
	
	function parseConditions($conditions)
	{
		$conditions = trim($conditions);
		//println("\t----------\nparse Conditions: ". $conditions);
		$finalCon = array();
		$con = array();

		$char = null;
		$lastChar = null;
		$last2Char = null;
		$last3Char = null;
		$level = 0; // level of nested subqueries
		$startAtom = 0;				

		for ($pos = 0 ; $pos < strlen($conditions) ; $pos += 1) {
			$lastChar = $char;
			if ($pos >= 2) $last2Char = strtolower(substr($conditions, $pos - 2, 2));
			if ($pos >= 3) $last3Char = strtolower(substr($conditions, $pos - 3, 3));
			$char = $conditions[$pos];

			switch ($char) {
				case '>':
					if ($last2Char == '<q') { // start of a subquery
						$level += 1;
					}else if ($last3Char == '</q') { // end of a subquery
						$level -= 1;
					}
					break;
				case '|':
					if ($lastChar == '|') {
						if ($level == 0) { // a disjunction branch
							$endAtom = $pos - 2;
							$atomic = substr($conditions, $startAtom, $endAtom - $startAtom + 1);
							// println("1 startAtom=".$startAtom . " endAtom=".$endAtom ." ". $atomic);
							$con[] = $atomic;
							$startAtom = $pos + 1;
						}
					}
					break;
				default:
			}
			if ($pos == strlen($conditions) - 1) {
				$endAtom = $pos;
				$atomic = substr($conditions, $startAtom, $endAtom - $startAtom + 1);
				// println("2 startAtom=".$startAtom . " endAtom=".$endAtom ." ". $atomic);
				$con[] = $atomic;
			}
		}
		//print_r($con);
		// parse each condition

		foreach($con as $aCondition) {
			//println("parseConditions - parse each: " . $aCondition ."\n");
			// if it is a subquery?
			if (preg_match('/<q>(.*)<\/q>/is', $aCondition , $matches)) {
				// println("a subquery!");
				$query_body = $matches[1]; //println($query_body);
				$c = new Condition();
				$c->isSubquery = true;
				$c->seletors = AskQueryParser::parseSelectors($query_body);
				$finalCon[] = $c;
			}else {
				$c = new Condition();
				$c->isSubquery = false;

				$aCondition = trim($aCondition);
				$ch = $aCondition[0];
				if ($ch == '!' || $ch == '<' || $ch == '>') {
					$c->modifier = $ch;
					$c->atom = substr($aCondition, 1, strlen($aCondition) - 1);
				}else
					$c->atom = $aCondition;
				$finalCon[] = $c;
			}
		}
		return $finalCon;
	}
}

?>