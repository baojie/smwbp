<?php
/**
 *
 * @author Jie Bao
 * @file
 * @ingroup SMWMaintenance
 */

require_once("QLPlus_Setting.php");
require_once("QLPlus_Rule.php");
require_once("QLPlus_DLVDB.php");

class SMW2LP
{	
	private static $dlvdb = null;
	private static $list_of_property = array();
	private static $list_of_category = array();	

	private static $triple_count = 0;
	
	private static function addProperty($s)
	{
		SMW2LP::$list_of_property[$s] = 1;
	}
	
	private static function addCategory($c)
	{
		SMW2LP::$list_of_category[$c] = 1;
	}		
	
	// get triples associated with a page (by it title object)
	// The returned data is an array, each key is a property name, and its value a set of values of the property
	// For example, is a page has [[p::v1]], [p::v2]], [q::v3]], then the returned array of the page will be 
	//  { p->{v1,v2}, q->{v3}}  
	private static function getTriple($title)
	{
		$page = SMWDataValueFactory::newTypeIDValue( '_wpg',  $title->getFullText());
		$semdata = smwfGetStore()->getSemanticData($page->getTitle());  

		// build the semantic data
		if ($semdata == null)
		{
			return text;
		}
		
		// subject is the page's name
		$s = $title->getFullText();

		$arr = array();
				
		// get all triple of the page   
		foreach($semdata->getProperties() as $key => $property){
			
			// $property is a SMWPropertyValue
			$p =  $property->getShortText(false,NULL); // see SMWDataValue 
			$p2 =  $property->getPropertyID();
			if (!isset($arr[$p])) $arr[$p] = array();
			
			// some pre-defined properties
			// http://semantic-mediawiki.org/doc/SMW__SQLStore2_8php-source.html            
			if ($p2 == '_MDAT') continue; //time stamp
			else if ($p2 == '_INST') $p = '_INST'; //'rdf:type';
			else if ($p2 == '_SUBC') $p = '_SUBC'; //'rdfs:subClassOf';
			else if ($p2 == '_SUBP') 
			{
				$p = '_SUBP'; //'rdfs:subPropertyOf';
			}
			else if ($p2 == '_REDI') $p = '_REDI'; //'owl:sameAs';
			else if ($p2 == '_TYPE') continue; //$p = '_TYPE'; //'has_type';

			// Capitalize the first letter of the property name (as MW always does) 
			$p = ucfirst($p);
			
			$propvalues = $semdata->getPropertyValues($property);
			foreach ($propvalues as $propvalue) {
				$o=$propvalue->getShortText(false);     
				//print "-- $s $p2 $o \n";			
				//$text .= "$s $p $o;<br/>";
				
				if (Condition::isDatatypeProperty($property)) // cf. QLPlus_AskQuery.php
				{
					if (Condition::isNumberProperty($property))
						$arr[$p][] = "#". Rule::makeNumber($o);	
					else if ($p == '_REDI')
						$arr[$p][] = $o;	
					else
						$arr[$p][] = ">".Rule::makeLiteral($o);		
				}
				else
					$arr[$p][] = $o;	
			}
		}       
		return $arr;
	}
	
	static function getABoxTautology($title)
	{
		$title = str_replace(' ', '_' , $title);
		// everything is a thing
		if (SMW2LP::$dlvdb){
			$triple = array(SMW2LP::instanceToString($title), '_INST', OWLTerms::$THING );
			SMW2LP::$dlvdb->insertTripleArray($triple);
		}
		else
			$text = OWLTerms::$THING . "(". SMW2LP::instanceToString($title) . ").\n";
		
	}
	
	static function getTBoxTautology($title, $prefix)
	{
		//print $title."\n";
		$title = str_replace(' ', '_' , $title);

		if ($prefix == "Property:")
		{
			$text = OWLTerms::$THING . "(X) :- $title(X,Y).\n";
			$text .= OWLTerms::$THING . "(Y) :- $title(X,Y).\n";
		}
		else if ($prefix == "Category:")
		{
			$text = OWLTerms::$THING . "(X) :- $title(X).\n";
		}		
		return $text;
	}
	
	static function printPage($title, &$instances, &$ontologies) 
	{
		$s =  $title->getFullText();
		$instances = SMW2LP::getABoxTautology($s);
		$ontologies = '';
		
		$s = str_replace('Category:','',$s);
		$triples = SMW2LP::getTriple($title);
				
		foreach($triples as $p => $o)
		{
			foreach ($o as $ov){
				SMW2LP::printTriple($s,$p,$ov, $instances, $ontologies);  				
			}
		}
	}
	
	//$title is a Title object
	// reload data to dlvdb - only the instance part. TBox won't be updated
	public static function refreshPage($title) 
	{	
		global $wgQLPlus_Solver;
		if ($wgQLPlus_Solver != 'dlvdb' ) return; // do nothing
		
		$s =  $title->getFullText();
		$escapedS = SMW2LP::instanceToString($s);
	
		if (!SMW2LP::$dlvdb){
			SMW2LP::$dlvdb = new DLVDB;
			SMW2LP::$dlvdb->open();
		}	
		SMW2LP::$dlvdb->clean($escapedS); // remove triples of this page
				
		$instances = '';
		$ontologies = '';
		
		$s = str_replace('Category:','',$s);
		$triples = SMW2LP::getTriple($title);
		
		//print_r($triples);
				
		foreach($triples as $p => $o)
		{
			foreach ($o as $ov){
				// triples are sent to the db in this call
				SMW2LP::printTriple($s,$p,$ov, $instances, $ontologies);  				
			}
		}
		
		if (SMW2LP::$dlvdb){
			SMW2LP::$dlvdb->close();
			SMW2LP::$dlvdb = null; 
		}
	}

	/* input maybe
		string	-
		number	- start with a #
		page		- 
	*/
	static function instanceToString($instance)
	{
		if ($instance[0] == ">") 
			return Rule::escape(substr($instance,1), false, "s");
		else if ($instance[0] == "#") 
			return substr($instance,1);
		else 
			return Rule::escape($instance, true, "p");
	}
	
	static function propToString($prop)
	{		
		$prop = ucfirst($prop);
		$prop = Rule::escape(str_replace('Property:','',$prop));		
		SMW2LP::addProperty($prop);
		return $prop;
	}

	static function catToString($cat)
	{
		$cat = ucfirst($cat);
		$cat = Rule::escape(str_replace('Category:','',$cat));
		SMW2LP::addCategory($cat);
		return $cat;
	}
	
	/**
	 * StartsWith
	 * Tests if a text starts with an given string.
	 *
	 * @param     string
	 * @param     string
	 * @return    bool
	 */
	static function startsWith($Haystack, $Needle){
		// Recommended version, using strpos
		return strpos($Haystack, $Needle) === 0;
	}
	
	static function printTriple($s,$p,$ov, &$instances, &$ontologies)
	{	
		//print "[$s $p $ov] \n";
		SMW2LP::$triple_count++;
		$text = SMW2LP::propToString($p) . '(' . SMW2LP::instanceToString($s) . ',' . 
				SMW2LP::instanceToString($ov) . ')';				
		$triple = array(SMW2LP::instanceToString($s), 
						SMW2LP::propToString($p), 
						SMW2LP::instanceToString($ov));		
				
		$is_instance = true;		
		if ($p == '_INST') {
			$text = SMW2LP::instanceToString($ov) . '(' . SMW2LP::instanceToString($s) . ')' ;
			$triple = array(SMW2LP::instanceToString($s), $p, SMW2LP::instanceToString($ov));	
		}
		else if ($p == '_REDI') {
			$text = OWLTerms::$SAMEAS. '(' . Rule::escape($s) . 
					', ' . Rule::escape($ov) . ")";
			$triple = array(SMW2LP::instanceToString($s), $p, SMW2LP::instanceToString($ov));			
		}				
		else if ($p == '_SUBC') 
		{
			$is_instance = false;	
			$text = Rule::escape($ov) . '(X) :- ' .	Rule::escape($s) . '(X)' ;
		}
		
		// if $s is a property
		if (SMW2LP::startsWith(ucfirst($s),"Property:"))
		{
			$p = str_replace(' ', '_' , $p);
			if ($p == '_SUBP') $text = Rule::escape($ov) . '(X,Y) :- ' .
					'' . SMW2LP::propToString($s) . '(X,Y)' ;
					else if ($p == '_TYPE') {
				$text = $p . '(' . SMW2LP::instanceToString($s) . ',' . SMW2LP::instanceToString($ov) . ')';
				$is_instance = false;	
			}
			else if ($p == OWLTerms::$DOMAIN) {			
				$text = SMW2LP::catToString($ov);
				$text =	$text . '(X) :- ' . SMW2LP::propToString($s) . '(X,Y)';		
				$is_instance = false;					
			}
			else if ($p == OWLTerms::$RANGE) {			
				$text = SMW2LP::catToString($ov);
				$text =	$text . '(Y) :- ' . SMW2LP::propToString($s) . '(X,Y)';
				$is_instance = false;	
			}
			else if ($p == OWLTerms::$TYPE && $ov == OWLTerms::$TRANSITIVE) {	
				$prop = SMW2LP::propToString($s);
				$text =	$prop . '(X,Z) :- ' . $prop . '(X,Y), ' . $prop . '(Y,Z)';		
				$is_instance = false;	
			}
			else if ($p == OWLTerms::$TYPE && $ov == OWLTerms::$SYMMETRIC) {	
				$prop = SMW2LP::propToString($s);
				$text =	$prop . '(X,Y) :- ' . $prop . '(Y,X)' ;		
				$is_instance = false;	
			}
			else if ($p == OWLTerms::$TYPE && $ov == OWLTerms::$FUNC) {	
				$prop = SMW2LP::propToString($s);
				$text =	SMW2LP::propToString(OWLTerms::$SAMEAS) . '(Y,Z) :- ' . 
						$prop . '(X,Y), ' . $prop . '(X,Z)';		
				$is_instance = false;
			}
			else if ($p == OWLTerms::$TYPE && $ov == OWLTerms::$INVFUNC) {	
				$prop = SMW2LP::propToString($s);
				$text =	SMW2LP::propToString(OWLTerms::$SAMEAS) . '(Y,Z) :- ' . 
						$prop . '(Y,X), ' . $prop . '(Z,X)';		
				$is_instance = false;
			}		
			else if ($p == OWLTerms::$INVERSEOF) {	
				$p1 = SMW2LP::propToString($s);
				$p2 = SMW2LP::propToString($ov);
				$text =		"$p1(X,Y) :- $p2(Y,X).\n";		
				$text .=	"$p2(X,Y) :- $p1(Y,X)";		
				$is_instance = false;
			}
		}
		if ($is_instance) {
			if (SMW2LP::$dlvdb){
				SMW2LP::$dlvdb->insertTripleArray($triple);
			}
			else
				$instances .= ($text.".\n");
		}else $ontologies  .= ($text.".\n");
	}

	static function printAll( $outfile, $ontologyfile) {
		global $smwgNamespacesWithSemanticLinks;

		// step 1: prepare database and file to write
		$db = & wfGetDB( DB_MASTER );

		//print $outfile;
		
		if ( $outfile != null ) {
			$file = fopen( $outfile, 'w' );

			if ( !$file ) {
				print "\nCannot open \"$outfile\" for writing.\n";
				return false;
			}
			else {
				print "\n\"$outfile\" is opened successfully.\n";
			}
		}

		if ( $ontologyfile != null ) {
			$file2 = fopen( $ontologyfile, 'w' );

			if ( !$file2 ) {
				print "\nCannot open \"$ontologyfile\" for writing.\n";
				return false;
			}
			else {
				print "\n\"$ontologyfile\" is opened successfully.\n";
			}
		}
		
		//step 2: write all pages
		
		SMW2LP::$list_of_property = array();
	    SMW2LP::$list_of_category = array();	
		$start = 1;
		$end = $db->selectField( 'page', 'max(page_id)', false, $outfile );

		$a_count = 0; 

		//for ( $id = $start; $id <= 100; $id++ ) {
		for ( $id = $start; $id <= $end; $id++ ) {
			if ($id%100 == 0) 
				print $a_count .' pages processed (' . sprintf("%01.2f", 100*$id/$end) . "%)\n";
			
			$title = Title::newFromID( $id );

			if ( ( $title === null ) || !smwfIsSemanticsProcessed( $title->getNamespace() ) ) continue;

			// return instances and ontologies by reference - both as strings
			$instance = ''; $ontologies = '';
			SMW2LP::printPage($title, $instances, $ontologies);
			
			$a_count++; // DEBUG			
			
			if ( $file !== false && !SMW2LP::$dlvdb) { 
				fwrite( $file, $instances );
				
			}
			if ( $file2 !== false ) { 
				fwrite( $file2, $ontologies );
			}
		}
		
		// step3: add tautologies

		//make sure 'thing' is a category
		SMW2LP::$list_of_category[OWLTerms::$THING]=1;

		if ( $file2 !== false ) {
			SMW2LP::printAxiomaticTriple($file2);
			
			//print_r(SMW2LP::$list_of_property);
			foreach(SMW2LP::$list_of_property as $p=>$v)
			{				
				fwrite( $file2, SMW2LP::getTBoxTautology($p, "Property:") );
			}
			
			//print_r(SMW2LP::$list_of_category);				
			foreach(SMW2LP::$list_of_category as $c=>$v)
			{
				fwrite( $file2, SMW2LP::getTBoxTautology($c, "Category:") );
			}		
			fclose( $file2 );
		}
		
		if ( $file !== false ) {
			// write map rules to the instance file
			if (SMW2LP::$dlvdb){	
				//print_r(SMW2LP::$list_of_property);
				foreach(SMW2LP::$list_of_property as $p=>$v)
				{
					$map = SMW2LP::$dlvdb->getPropertyMapping($p);
					fwrite( $file, $map );
				}
				
				//print_r(SMW2LP::$list_of_category);				
				foreach(SMW2LP::$list_of_category as $c=>$v)
				{
					$map = SMW2LP::$dlvdb->getCategoryMapping($c);
					fwrite( $file, $map );
				}		
			}
			fclose( $file );
		}

		print $a_count .' pages processed';
	}
	
	static function printAxiomaticTriple(&$file)
	{
		// sameas
		//SameAs is reflexive SameAs(X,X):- thing(X) .
		$text = SMW2LP::propToString(OWLTerms::$SAMEAS) . "(X,X) :- " . OWLTerms::$THING . "(X).\n" ;
		//SameAs is transtive: SameAs(X,Z) :- SameAs(X,Y), SameAs(Y,Z) .
		$text .= SMW2LP::propToString(OWLTerms::$SAMEAS) . "(X,Z) :- " . 
				SMW2LP::propToString(OWLTerms::$SAMEAS) . "(X,Y), " .
				SMW2LP::propToString(OWLTerms::$SAMEAS) . "(Y,Z) " .
				".\n" ;
		//SameAs is symmetric SameAs(X,Y) :- SameAs(Y,X)		
		$text .= SMW2LP::propToString(OWLTerms::$SAMEAS) . "(X,Y) :- " . 
				SMW2LP::propToString(OWLTerms::$SAMEAS) . "(Y,X).\n" ;
		//print $text;		
		fwrite( $file, $text );
	}
	
	static function cleanCache()
	{
		global $wgQLPlus_TempFilePath;
		$files = glob($wgQLPlus_TempFilePath . "/*"); 
		foreach($files as $file) unlink($file); 
	}
	
	static function run($outfile,$ontologyfile)
	{
		global $wgQLPlus_UseClosure, $wgQLPlus_Solver, $wgQLPlus_DlvODBC;
		if ($wgQLPlus_Solver =='dlvdb')
			print "\nWriting instances to ODBC link \"$wgQLPlus_DlvODBC\""; 
		else
			print "\nWriting instances to file \"".$outfile."\""; 
			
		print "and ontology axioms to \"$ontologyfile\"...\n";
		
		$time_start = microtime(true);
		
		if ($wgQLPlus_Solver =='dlvdb'){
			SMW2LP::$dlvdb = new DLVDB;
			SMW2LP::$dlvdb->open();
			SMW2LP::$dlvdb->clean(); // remove triples
		}
		
		SMW2LP::$triple_count = 0;
		SMW2LP::printAll($outfile,$ontologyfile);
		
		if (SMW2LP::$dlvdb){
			SMW2LP::$dlvdb->close();
			SMW2LP::$dlvdb = null; 
		}	
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		
		echo "\nFinish dumping in $time seconds, ". SMW2LP::$triple_count . " triples\n";
		
		// delete prvious caches
		SMW2LP::cleanCache();
		
		if ($wgQLPlus_UseClosure && $wgQLPlus_Solver =='dlv'){
			$time_start = microtime(true);
			QLPlus_QueryProcessor::computeClosure($outfile,$ontologyfile);
			$time_end = microtime(true);
			$time = $time_end - $time_start;
			echo "\nFinish closure computing in $time seconds\n";
		}	
		
		// debug mode: print a readable version
		global $wgQLPlus_Debug;
		if ($wgQLPlus_Debug)
		{
			$solver = $wgQLPlus_Solver;
			$wgQLPlus_Solver = 'dlv';
			Rule::$DEBUG = true;
			SMW2LP::printAll($outfile.".debug", $ontologyfile.".debug");
			Rule::$DEBUG = false;
			$wgQLPlus_Solver = $solver;
		}		
	}
}

