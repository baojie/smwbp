<?php 

require_once ( getenv( 'MW_INSTALL_PATH' ) !== false
	? getenv( 'MW_INSTALL_PATH' ) . "/maintenance/commandLine.inc"
	: dirname( __FILE__ ) . '/../../../maintenance/commandLine.inc' );
require_once(dirname( __FILE__ ) ."/../QLPlus_Setting.php");
require_once(dirname( __FILE__ ) ."/../QLPlus_AskQueryParser.php");

function test($query)
{
	$q = AskQueryParser::parse($query);
	//$q->toPrint();
	Rule::$DEBUG = true;
	$q->toLP(true);
	print("\nfinal rules: \n" . $q->rules->toString());
	Rule::$DEBUG = false;
	$q->toLP(true);
	print("\n\nfinal rules (escaped): \n" . $q->rules->toString());
}

function test_from_file($query_file)
{
	print("Translate SMW ask query into logic program\n");
	
	$query = '';
	if (file_exists($query_file)) {
		$handle = fopen($query_file, "r"); 
		$query = fread($handle, filesize($query_file));
    }
	print($query);
	test($query);
}

/*
{{#Ask:  
	[[Category:A]][[p3::category:B]] or 
	[[p.p1.p2::
	    <q>
			[[Category:D]] or 
			[[p1::<q>
					[[Some Page]]
				</q>
			]]
		</q>
		||!v
		||<q>[[Category:E]]</q>
	]]
|?has name|format=table
}}
*/

// $query =  "{{#Ask:  [[Category:A]][[Category:C]] or [[p.q::v1||v2]]|?has name\n}}";
// $query =  "{{#Ask:  [[Category:A]][[B]] or [[p3.p4::<q>[[Concept:D]] or [[E]]</q>]][[p1.p2::v2||<q>[[Category:F]]</q>]]\n}}";
// $query =  "{{#ASK:  [[Image:A]][[:C]] or [[p::c]]|?has name\n}}";
// $query =  "{{#ask:[[Category:C]][[p::<q>[[Category:C]]</q>]]}}";
/*$query = "{{#ask:
   [[Category:Faculty]]
   [[has affiliation::Tetherless World Constellation]]
   [[has affiliation::Rensselaer Polytechnic Institute]]
   |? foaf:depiction
   |? has role
   |? foaf:homepage

   |format=template
   |template=ResearcherBox2
   |link=none
   |sort=Has year join
   }}";
*/

test_from_file("ask.ql");

?>