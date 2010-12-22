<?php
require_once ( getenv( 'MW_INSTALL_PATH' ) !== false
	? getenv( 'MW_INSTALL_PATH' ) . "/maintenance/commandLine.inc"
	: dirname( __FILE__ ) . '/../../../maintenance/commandLine.inc' );
require_once("/../QLPlus_Setting.php");
require_once("/../QLPlus_DLVDB.php");
require_once("/../QLPlus_SMW2LP.php");

function testCategoryMapping() {
	print "\n";

	$db= new DLVDB();
	print $db->getCategoryMapping('c');
}

function testRefreshPage(){
	print "\ntestRefreshPage\n";
	$title = Title::newFromText( "A" );
	SMW2LP::refreshPage($title);
}

testRefreshPage();

?>