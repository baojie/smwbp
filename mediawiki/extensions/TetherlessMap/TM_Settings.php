<?php

if ( !defined( 'MEDIAWIKI' ) ) die();

define('TM_VERSION','0.1.1');

$wgExtensionCredits['specialpage'][]= array(
	'name' => 'Tetherless Map',
	'version' => TM_VERSION,
	'author' => '	Jin Guang Zheng and Rui Huang and Jie Bao and Li Ding',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Tetherless_Map',
	'description' => 'Allows users to generate maps based on query results from Semantic Mediawiki.',
);

require_once('Individual_Location.php');
require_once ('GoogleMapClick.php');
require_once ('GoogleMapMultiObjects.php');

?>
