<?php
// Firsr-order Logic program rules
// e.g. man(X) :- male(X), human(X)

require_once(realpath(dirname(__FILE__) . "/QLPlus_Setting.php"));

class Atom
{
	// must start with [a-z]
	public $predicate = null;	// e.g. human
	public $is_naf = false; // if has a negation as failure operatore, e.g. not p1(X)
	
	// An array of strings. Must start with [A-Z]
	public $variables = null;	// e.g., X,Y
	
	function Atom($p, $v, $naf=false)
	{
		$this->predicate = $p;
		$this->variables = $v;
		$this->is_naf = $naf;
	}
	
	function getArity()
	{
		if ($this->variables == null) return 0;
		else return count($this->variables);
	}
	
	function copy()
	{		
		$atom_new = @new Atom($this->predicate,
				$this->variables,
				$this->is_naf);
		return $atom_new;
	}
	
	function isOf($name)
	{
		if ($name == null)
			return false;
		else if ($this->predicate == null)
			return false;
		else
			return ($this->predicate == $name);		
	}
	
	function toString($unescape=false)
	{
		$name = $unescape? Rule::unesacpe($this->predicate) : $this->predicate ;
		// it it is of the form <, <=, >, >=, =
		if (in_array($name, QLPlusTerm::$OP) && count($this->variables) == 2){
			$r = $this->variables[0] . ' ' . $name . ' ' . $this->variables[1];
		}
		else{			
			$r = $name . "(" . implode("," , $this->variables) .	")";
		}
		if ($this->is_naf) $r = 'not ' . $r;
		return $r;
	}
}

class CountAtom extends Atom
{
	public $comparator;
	public $number;
	public $countVar;
	
	// so far, only one variable is accpeted
	function CountAtom($p, $v, $comparator, $number, $naf=false, $countVar)
	{
		$this->predicate = $p;
		$this->variables = $v;
		$this->comparator = $comparator;
		$this->number = $number;
		$this->is_naf = $naf;
		$this->countVar = $countVar;
	}
	
	function copy()
	{		
		$atom_new = @new CountAtom(
			$this->predicate,
			$this->variables,
			$this->comparator,
			$this->number,
			$this->is_naf,
			$this->countVar);
		return $atom_new;
	}	
	
	// e.g. #count{Y:p(X,Y)}<1 .
	function toString($unesacpe=false)
	{
		//println($this->countVar);
		$name = $unesacpe? Rule::unesacpe($this->predicate) : $this->predicate ;
		$vars = implode(",",$this->variables);
		return "#count{" . $this->countVar . ":" . $name . 
			"($vars)}$this->comparator$this->number";
	}
}

class Rule {	
	public $head = null; // an Atom
	public $body = null; // an array of Atom
	public static $DEBUG = false;

	function Rule($h, $b)
	{
		$this->head = $h;
		$this->body = $b;
	}
	
	static function escape($str, $replaceblank = true, $prefix = 'p')
	{
		//replace  non-alphanumeric characters with '_', since DLV only accept [A-Za-z0-9_]
		//return preg_replace('/\W/', '_', $str);
		if ($replaceblank){
			$str = preg_replace('/\s/', '_', $str);	
		}
		if (Rule::$DEBUG)
			return $str;
		else
			return $prefix . bin2hex($str);	
	}

	static function unesacpe($hex_str)
	{
		// if it is string - start with 's'
		$hex_str = trim($hex_str);
		
		// if it is dlvdb mode, the result is quoted, remove it
		$len = strlen($hex_str);
		if ($len >=2 && $hex_str[0] == "\"" || $hex_str[$len-1] == "\"")
			$hex_str = substr($hex_str,1,$len-2);
		
		// is it a number?
		if (is_numeric($hex_str[0])) return $hex_str;	
			
		// else,  an escaped string if starts with "p" or "s"
		//omit the first character
		if ($hex_str[0] == 'p' || $hex_str[0] == 's'){
			$hex_str = substr($hex_str,1);
			return Rule::hex2bin($hex_str);
		}
		return $hex_str;
	}
	
	static function hex2bin($hex_str)
	{
		if (!is_string($hex_str)) return null;
		$r='';
		for ($a=0; $a<strlen($hex_str); $a+=2) 
		{ 
			$r.=chr(hexdec($hex_str{$a}.$hex_str{($a+1)})); 
		}
		return $r;
	}
	
	static function makeLiteral($str)
	{
		return Rule::escape($str,false,"s");
	}
	
	static function makeNumber($str)
	{
		return preg_replace('/,/', '', $str);
	}

	// generate string representation of the rule
	function toString($unescape=false)
	{
		$s = "";
		
		// some rule may have no head, e.g., integrity constraint
		if ($this->head)
			$s .= $this->head->toString($unescape);			
			
		if ($this->body){			
			$s .= " :- " ;
			$body = "";
			foreach($this->body as $b)
				$body .= $b->toString($unescape) .", ";
			//remove the last  ", "	
			if (strlen($body)> 0 )
				$s .= substr ($body, 0,strlen($body)-2);			
		}
		
		if ($this->head || $this->body)
			$s .= " .";
		
		return $s;
	}
}

class LogicProgram
{
	public $rules = null ; // array of Rule
	
	public function LogicProgram($arr)
	{
		$this->rules = $arr;
	}
	
	public function addRule($rule)
	{
		if ($this->rules==null) 
			$this->rules = array($rule);
		else
			$this->rules[] = $rule;
	}
	
	public function addRule2($head, $body)
	{
		$this->addRule(new Rule($head,$body));
	}
	
	public function toString($unescape=false)
	{
		$s = "";
		foreach($this->rules as $r) {
			$s .= ($r->toString($unescape) . "\n");
		}
		return $s;
	}
	
	// a rule head will be replaced by its body unless it appears as heads of >=2 rules or the body is n-ary, n>=2
	// $exception is  a string, containing a predicate that shall not be replaced.
	public function simplify($exception=null)
	{
		// step 1: generate the replace map
		$replaceMap = array(); // of the form  p1 => p2
				
		$count = array();
		foreach($this->rules as $r) {
		
			if ( $r->head == null ||
				 $r->body == null ||
				 $r->head->isOf($exception)) 
				continue;
			
			if ( $r->head != null )
			{
				if (count($r->body) != 1) 
					continue; // only generate map from rules with a single body literal
				else {
					if ($r->body[0]->is_naf == true) 
						continue;  // if the body negated, do not replace.
					if ($r->body[0]->getArity() != $r->head->getArity()) 
						continue;  // head and body are of different arities.
					if 	($r->body[0]->variables !== $r->head->variables)
						continue; // if variable lists are different
				}
				
				$replaceMap[$r->head->predicate] = $r->body[0]->predicate;
				
				if (!array_key_exists($r->head->predicate, $count)) 
					$count[$r->head->predicate] = 1;
				else $count[$r->head->predicate]++;								
			}			
		}
		// print_r($count);
		//print_r($replaceMap);
		
		// step 2: remove from replace map if the head appears in more than one rules
		//or if the replacee is of the form >, <, =, !=
		foreach($count as $k => $v) {
			//print $v . " " . $replaceMap[$k][0] .  "\n";
			if ($v > 1 || !ctype_alnum($replaceMap[$k][0])) 
				unset($replaceMap[$k]);
		}
		
		//print_r($replaceMap);
		
		// step 3: replace exhustively
		foreach($replaceMap as $k => $v) {
			$final = $v; 
			while(isset($replaceMap[$final]))
			{
				$final = $replaceMap[$final];
				
				//@todo: cycle detection
				// for SMW-QL_Plus query translation, we won't have cycles in queries.
			}
			$replaceMap[$k] = $final;
		}
		
		// print_r($rules);
		// print_r($replaceMap);
		
		// step 4: do the replacement
		$new_rules = array();
		foreach($this->rules as $r) {
			//print($r->toString() . "\n");								
			//print $r->head->predicate ."\n";	
			
			// add to new rule list if it is a fact or the head is not in the replace map
			if ($r->body == null || !array_key_exists($r->head->predicate, $replaceMap))			
			{
				// replace head
				$new_head = new Atom($r->head->predicate, $r->head->variables);
				if (isset($replaceMap[$r->head->predicate]))
				{
					$new_head->predicate = $replaceMap[$r->head->predicate];
				}

				// if the body should be replaced, replaced it	
				$new_body = array();
				if($r->body){					
					foreach($r->body as $b) {
						$b_new = $b->copy();					
						
						if (array_key_exists($b->predicate, $replaceMap)) {
							$b_new->predicate = $replaceMap[$b->predicate];
						}
						$new_body[] = $b_new;
						//print $b_new->toString()."\n";
					}					
				}				
				$new_rule = new Rule($new_head, $new_body);
				//print "--- " .$new_rule->toString() ."\n";
				$new_rules[] = $new_rule;	
			}					
		}
		$this->rules = $new_rules;
		//print $this->toString();	
	}	
}
?>