<?php 

require_once("QLPlus_Rule.php");

class AskQuery {
	public static $QUERY_HEAD = "tmp_result";
	
	private static $index = 0;
	private static $var_index = 0;
	
	public $source_query; // the source string of the query
	
	public $selectors = null; //instance of Seletors.  An array of array (of atom selector); - DNF
	
	public $output = null; // an array
	
	public $rules = null; // of LogicProgram object
	
	static function makeAtom($name = null, $var = "X", $var2 = null, $isNaf = false)
	{
		//println("call AskQuery::makeAtom(), AskQuery::\$index=" .AskQuery::$index);	
		if ($name == null) {
			$name = "tmp_" . AskQuery::$index;
			AskQuery::$index++;
		}
		
		if ($var2) $variables = array($var,$var2);
		else $variables = array($var);

		return new Atom($name, $variables, $isNaf);
	}
	
	//e,.g., AskQuery::makeCountAtom(null, $var, "<", 1);	
	static function makeCountAtom($name = null, $variables, $operator, $value, $countVar)
	{
		if ($name == null) {
			$name = "tmp_" . AskQuery::$index;
			AskQuery::$index++;
		}
		return new CountAtom($name, $variables, $operator, $value, false, $countVar);	
	}

	static function getTempVar()
	{
		$name = "X" . AskQuery::$var_index;
		AskQuery::$var_index++;
		return $name;
	}

	function toPrint()
	{
		print("Source query: \n    " . $this->source_query ."\n");
		if ($this->selectors){
			print("Selectors: \n");
			$this->selectors->toPrint();
		}
	}

	// translate the query into logic programs.
	// if it has been translated before, the old one will be replaced
	// result: $this->rules
	function toLP($ifSimplify=true)
	{	
		//if($this->rules) print $this->rules->toString();
		
		// reset rule set
		$lp = new LogicProgram(null); // of LogicProgram object		
		
		//reset temp counters
		AskQuery::$index = 0 ;
		AskQuery::$var_index = 0;
		
		$nameSelector = AskQuery::makeAtom();
		$top_rule = new Rule(
			new Atom(AskQuery::$QUERY_HEAD, array("X")),
			array($nameSelector));
		
		$lp->addRule($top_rule);
				
		// get  more rules from selectors
		if ($this->selectors){
			//println(gettype($this->selectors));
			$new_rules = $this->selectors->toLP($nameSelector, "X"); // call Seletors->toLP
			$lp->rules = array_merge($lp->rules, $new_rules);
			//print_r($new_rules);
		}
		
		if($ifSimplify)
			$lp->simplify(AskQuery::$QUERY_HEAD);
		
		// $lp .= "\nnumber of rules: " . sizeof($rules);
		$this->rules = $lp;
	}
	
	function toFile($file_name)
	{
		if ($this->rules == null) $this->toLP();
		$handle = fopen($file_name, "w");
		fwrite($handle, $this->rules->toString());
		fwrite($handle, AskQuery::$QUERY_HEAD."(X)?");
		fclose($handle);
	}
}

Class Seletors {
	// Disjunctive Normal Form
	// [[A]][[B]] or [[C]] or [[D]]   then the array is { { [[A]],[[B]]},  [[C]], [[D]] }
	
	public $disjunctions = array(); // each  element is an array of AtomSelector

	function Seletors($arr) // an array of array of AtomSelector
	{
		$this->disjunctions = $arr;
	}

	function toPrint()
	{
		foreach ($this->disjunctions as $d) {
			// $d is a conjunction of atom selectors
			foreach ($d as $atomic) { // an AtomSelector
				//print(" and ");
				$atomic->toPrint();
			}
			print("\n" . "or\n");
		}
	}

	// $head is an Atom
	// $var is a string , e.g., "X"
	// return an array of rules.
	function toLP($head, $var)
	{
		$rules = array();
		foreach ($this->disjunctions as $d) {
			//println("a disjunction");
			$nameSel = AskQuery::makeAtom(null, $var);
			
			// each disjunct leads to a new rule
			$rules[] = new Rule($head, array($nameSel));
			
			$lp_head = $nameSel;
			$lp_body = array();
			
			// $d is a conjunction of atom selectors
			foreach ($d as $atomic) { // an AtomSelector
				// print(" and ");$atomic->toPrint();
				$nameAtom = AskQuery::makeAtom(null, $var);

				$lp_body[] = $nameAtom;
				
				// more rules, call AtomSelector::toLP
				$new_rules = $atomic->toLP($nameAtom, $var);
				$rules = array_merge($rules, $new_rules);
			}

			$rules[] = new Rule($lp_head , $lp_body);
			// println("\nor");
		}
		return $rules;
	}
}

// an atomic selector in [[           ]]
class AtomSelector {

	// Category pages or instance pages, e.g. Catgory:C, Page1||Page2
	public $isPage; // true - class or page,  false- like P::v
	public $page; // for isPage true - a string
	
	// property pages: e.g., P::v, P.Q::<q>...</q>
	public $property; // for isPage false - can be a property chain e.g. p1.p2.p3
	public $conditions; // for isPage false, -it is an array of atomic conditions - instance of Condition
	
	// can be used together with the two previous caes, e.g. <>Category:C, <>P::+ (only if the value is the wildcard +)
	public $isNaf = false; // if it has a negation as failure operator.
	
	// cardinality type selectors
	public $isCardinality = false;
	public $cardNumber = null;
	public $cardOperator = null;

	// $page_name is string - the name of the page, e.g. Category:C, SomePage
	function setPage($page_name, $isNaf = false)
	{
		$this->isPage = true;
		$this->page = $page_name;
		$this->isNaf = $isNaf;
	}
	
	// property is a tring
	// conditions is an array of Condition
	function setPropetyCondition($property,$conditions,$isNaf=false)
	{
		$this->isPage = false;
		$this->property = $property;
		$this->conditions = $conditions;
		$this->isNaf = $isNaf;
	}
	
	function toPrint()
	{
		if ($this->isNaf) {
			print("~");
		}
		if ($this->isPage) {
			print($this->page);
		}else {
			print($this->property . " :: ");
			foreach ($this->conditions as $c) {
				$c->toPrint();
				print(" || ");
			}
		}
	}

	// $nameAtom is an Atom instance - see QLPlus_Rule.php
	// $var is a string e.g., "X"
	function toLP($nameAtom, $var)
	{
		$rules = array();
		
		$lp_head = $nameAtom;
		$lp_body = array();

		if ($this->isPage) {
			// e,g, Category:C, Concept:C, SomePage
			// get its namespace
			$things = explode(":", $this->page);
			if (sizeof($things) == 2) {
				$namespace = strtolower($things[0]);
				if ($namespace == "category" || $namespace == "concept") {
					$lp_body[] = AskQuery::makeAtom(
						Rule::escape(ucfirst($things[1])), $var, null, $this->isNaf );
				}else if ($namespace == "") {				    
					$lp_body[] = AskQuery::makeAtom(
						"=",$var, Rule::makeLiteral($things[1]), $this->isNaf);
				}else {					
					$lp_body[] = AskQuery::makeAtom(
						"=",$var, Rule::makeLiteral($this->page), $this->isNaf);
				}
			}else {
				$pageAtoms = AskQuery::makeAtom(null,$var, $this->isNaf);
				$lp_body[] = $pageAtoms;
				// a single page or a list of pages
				$pages = explode("||", $this->page);
				foreach ($pages as $name){
					$name = trim($name);
					if ($name[0]==':') $name = ucfirst(substr($name,1));
					$onePage = AskQuery::makeAtom("=",$var, Rule::escape($name));
					$rules[] = new Rule($pageAtoms, array($onePage));
				}
			}
			if ($this->isNaf) // make the rule safe
				$lp_body[] = AskQuery::makeAtom(OWLTerms::$THING, $var);
		}
		else {//e.g., p=v||v2, p1.p2=v, p=<q>[[Category:C]]</q>
			
			$newVar = AskQuery::getTempVar();
			
			// if it is <>p::+, then translate into #count{Y, p(x,Y)}<1  [instead of "not p(X,Y)"]
			$atomFromProperty = AskQuery::makeAtom(null, $var, $newVar, false); 
			
			// <>p::+
			if ($this->isNaf == true && $this->conditions[0]->isWildCard())			
			{
				//print "$this->property: count!!\n";
				// create a temporary predicate
				$countVar = AskQuery::getTempVar();
				$atomCount = AskQuery::makeCountAtom(
							null, array($var,$countVar), "<", 1,$countVar);
				$atomFromProperty = AskQuery::makeAtom(
							$atomCount->predicate, $var, $newVar, false); 
				$lp_body[] = AskQuery::makeAtom(OWLTerms::$THING, $var);//safety guard: thing(X)
				$lp_body[] = $atomCount;
			}
			//  cardinality e.g., <3#P::+, <3#P::<q>[[...]]</q>
			else if ($this->isCardinality == true)
			{
				// e.g. #count{X:t1(X)}<5
				$countVar = AskQuery::getTempVar();				
				$atomCount = AskQuery::makeCountAtom(null, array($var,$countVar), 
								$this->cardOperator, $this->cardNumber,$countVar);
				$lp_body[] = AskQuery::makeAtom(OWLTerms::$THING, $var);//safety guard: thing(X)
				$lp_body[] = $atomCount;	
		
				$atomFromProperty = AskQuery::makeAtom(
							null, $var, $newVar, false); 

				// t1(X) :- p(X,Y),t2(Y)
				$atomCountHead	= AskQuery::makeAtom(
							$atomCount->predicate, $var, $newVar, false); 
				$atomCountBody[] = $atomFromProperty;
			}
			else {	
				$lp_body[] = $atomFromProperty;	
			}	

			$rules[] = $this->propertyChainToLP($atomFromProperty, $this->property, $var, $newVar);
			
			$atomFromCondtition = AskQuery::makeAtom(null, $newVar);
			if (!$this->conditions[0]->isWildCard()){
				if ($this->isCardinality){
					$atomCountBody[] = $atomFromCondtition;
				}	
				else{
					$lp_body[] = $atomFromCondtition;
				}
			}
			foreach ($this->conditions as $c) {
				// get more rules from the conditions
				if (!$c->isWildCard())
				{
					//print_r($c);
					$newP1 = AskQuery::makeAtom(null, $newVar);
					$rules[] = new Rule($atomFromCondtition, array($newP1));						
					
					// more rules from the conditions
					$new_rules = $c->toLP($newP1, $newVar,$this->property);
					$rules = array_merge($rules, $new_rules);
									}
			} 
			if (isset($atomCountHead) && isset($atomCountBody))
				$rules[] = new Rule($atomCountHead, $atomCountBody);
		}
		$rules[] = new Rule($lp_head, $lp_body);
		return $rules;
	}
	
	//$name maybe P, -P
	// inverse property is processd here
	// return: an atom for the property (to be added to a rule body)
	function propertyToAtom($name, $var1, $var2)
	{
		$name = trim($name);
		$v1 = $var1; $v2 = $var2;
		if ($name[0] == "-")
		{
			$v1 = $var2; $v2 = $var1;
			$name = substr($name,1);
		}
		$name = preg_replace('/\s/', '_', ucfirst($name));	
		return AskQuery::makeAtom(Rule::escape($name), $v1, $v2);
	}
	
	// propertyName may be P, -P. P.Q, P.-Q
	function propertyChainToLP($head, $propertyName, $var1, $var2)
	{
		$lp_head = $head;
		$lp_body = array();

		//println($propertyName);
		$things = explode(".", $propertyName);
		//print_r($things);
		$n = sizeof($things);

		if ($n == 1) {			
			$lp_body[] = $this->propertyToAtom($things[0], $var1, $var2);
		}
		else {
			$z = array();
			for ($i = 1; $i < $n; $i += 1) {
				$z[$i] = AskQuery::getTempVar();
			}

			$lp_body[] = $this->propertyToAtom($things[0], $var1, $z[1]);
			
			for ($i = 1; $i < $n - 1; $i += 1) {
				$j = $i + 1;
				$lp_body[] = $this->propertyToAtom($things[$i], $z[$i], $z[$j]);
			}
			$j = $n - 1;
			$lp_body[] = $this->propertyToAtom($things[$j], $z[$j], $var2);
		}
		$newRule = new Rule($lp_head, $lp_body);
		//print $newRule->toString();
		return $newRule;
	}
}

// a condition is of the form v, !v, <v, >v, ~v, <q>ANOTHER_QUERY</q>
class Condition {
	public $isSubquery = false;
	
	// if $isSubquery = true
	public $seletors; // a Seletors object

	// if $isSubquery = false
	public $modifier = ""; // ! or < or > or ~ ; optional
	public $atom; // the value
	
	function isWildCard()
	{
		return (($this->isSubquery == false) && 
			(trim($this->atom) == QLPlusTerm::$PLUS));
	}

	function toPrint()
	{
		if ($this->isSubquery) {
			$this->seletors->toPrint();
		}else {
			print($this->modifier . $this->atom);
		}
	}
	
	// $all_types = SMWDataValueFactory::getKnownTypeLabels() ;
	// print_r($all_types);
	// print "-----------------------\n";
	protected static $page_types = array ("Page","  sin", "  suc", "  sup");
	
	// $property is a SMWPropertyValue
	static function isDatatypeProperty($property)
	{
		$type = $property->getTypesValue() ;    //SMWTypesValue
		$type_id = end($type->getTypeLabels()) ;
		
		return !in_array((string)$type_id, Condition::$page_types);
	}
	
	static function isNumberProperty($property)
	{
		$type = $property->getTypesValue() ;    //SMWTypesValue
		$type_id = end($type->getTypeLabels()) ;		
		return ((string)$type_id === "Number");
	}
	//$property is passed to determined whether it is a datatype property or object property
	// if it is a property chain, then by default it is an object property
	function toLP($head, $var, $property)
	{	
		//dlvdb requires instances to be quoted	
		global $wgQLPlus_Solver;
		$quote = ($wgQLPlus_Solver == 'dlvdb')? "\"" : "";
		
		$property = preg_replace('/\s/', '_', ucfirst($property));	
		$rules = array();
		if ($this->isSubquery) {
			$pp = AskQuery::makeAtom(null, $var);			
			$rules[] = new Rule($head, array($pp));
			
			// if it is a subquery, get more rules from it
			$new_rules = $this->seletors->toLP($pp, $var);
			$rules = array_merge($rules, $new_rules);
			
		}else {			
			//print $property;
			$ppt = SMWPropertyValue::makeProperty($property);
			if (Condition::isDatatypeProperty($ppt)){
				if (Condition::isNumberProperty($ppt))
					$third_var = $quote . Rule::makeNumber($this->atom) . $quote;
				else 
					$third_var = $quote . Rule::makeLiteral($this->atom) . $quote ;	
				//print " is datatype property\n";
			}
			else{
				$third_var = $quote . Rule::escape($this->atom) . $quote;
				//print " is not datatype property\n";
			}
			$body = array(AskQuery::makeAtom($this->modifier."=", $var, $third_var));
			// to make the rule safe	
			if ($this->modifier != ''){
				$tautology = AskQuery::makeAtom(OWLTerms::$THING, $var);	
				$body[] = $tautology ;
			}	
			$rules[] = new Rule($head, $body);
		}
		return $rules;
	}
}
?>