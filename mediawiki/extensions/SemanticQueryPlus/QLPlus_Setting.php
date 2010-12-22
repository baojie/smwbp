<?php


$wgQLPlus_WikiDump = dirname( __FILE__ ). "/dump/instance.lp";
$wgQLPlus_WikiOntologyDump = dirname( __FILE__ ). "/dump/ontology.lp";
$wgQLPlus_TempFilePath = dirname( __FILE__ ) . "/tmp";

// make sure set its permissions to 755
$wgQLPlus_DlvPath = dirname( __FILE__ ) . "/bin/dlv.bin"; // linux
//$wgQLPlus_DlvPath = dirname( __FILE__ ) . "/bin/dlvdb.exe"; //windows

/*
$wgQLPlus_WikiDump = "d:/instance.lp";
$wgQLPlus_WikiOntologyDump = "d:/ontology.lp";
$wgQLPlus_TempFilePath = "d:/tmp/";
$wgQLPlus_DlvPath = "d:/dlvdb.exe";
*/

$wgQLPlus_Solver = "dlv"; 
//$wgQLPlus_Solver = "dlvdb"; 

//from http://www.dbai.tuwien.ac.at/proj/dlv/dlv.mingw.odbc.exe

$wgQLPlus_DlvODBC = "dlvodbc";
//$wgQLPlus_DlvODBCServer = "localhost";
//$wgQLPlus_DlvODBCPort = "3306";
$wgQLPlus_DlvODBCUser = "dlvodbc";
$wgQLPlus_DlvODBCPass = "dlvodbc";
$wgQLPlus_DlvODBCDB = "wiki_dlv";

$wgQLPlus_DlvODBCRealTime = true;

$wgQLPlus_DoInference = true;

// if use deductive closure - must rerun dumping when changed from false to true
// only works when wgQLPlus_Solver = dlv
$wgQLPlus_UseClosure = false;
$wgQLPlus_WikiClosure = dirname(__FILE__) ."/dump/closure.lp";

$wgQLPlus_UseCache = true;
$wgQLPlus_PrintTimeUsed = true;

$wgQLPlus_Debug = false;

class QLPlusTerm
{
	static public $PLUS 	= "+";
	static public $CARD 	= "#";
	static public $NEG 		= "<>";
	static public $OP = array ('<','<=','>','>=','=','!=');
}

class OWLTerms{
	static public $THING		= "thing";
	static public $DOMAIN 		= "Domain";
	static public $RANGE 		= "Range";
	static public $TYPE 		= "Type";
	static public $TRANSITIVE	= "Transitive";
	static public $SYMMETRIC	= "Symmetric"; 
	static public $SAMEAS		= "SameAs";
	static public $FUNC			= "Functional";
	static public $INVFUNC		= "InverseFunctional";
	static public $INVERSEOF	= "Inverse_of";
}	
?>