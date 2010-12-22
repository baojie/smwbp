<?php

require_once(realpath(dirname(__FILE__) . "/QLPlus_SMW2LP.php"));
require_once(realpath(dirname(__FILE__) . "/QLPlus_Setting.php"));

require_once ( getenv( 'MW_INSTALL_PATH' ) !== false
	? getenv( 'MW_INSTALL_PATH' ) . "/maintenance/commandLine.inc"
	: dirname( __FILE__ ) . '/../../maintenance/commandLine.inc' );

error_reporting(E_ALL & ~E_NOTICE);
	
global $wgQLPlus_WikiDump,$wgQLPlus_WikiOntologyDump;
SMW2LP::run($wgQLPlus_WikiDump,$wgQLPlus_WikiOntologyDump);

?>