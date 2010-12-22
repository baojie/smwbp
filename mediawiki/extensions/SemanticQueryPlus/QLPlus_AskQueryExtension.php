<?php

/*
 verion: 0.1
 authors:Jie Bao (baojie@gmail.com) 
 update: 121Dec 2010
 homepage: http://www.mediawiki.org/wiki/Extension:SemanticQueryPlus
*/

require_once("QLPlus_SemanticQueryPlus.php");

global $wgHooks;

$wgExtensionCredits['parserhook'][] = array(
    'name' => 'Semantic Query Plus',
    'version' => '0.1.20101221',
    'author' => '[http://www.cs.rpi.edu/~baojie Jie Bao]',
    'url' => 'http://www.mediawiki.org/wiki/Extension:SemanticQueryPlus',
    'description' => 'Extend Semantic MediaWiki the query language for additional expressivity',
);

$wgHooks['ParserFirstCallInit'][] 		= 'gfQLPlusParserFunction_Setup';
$wgHooks['LanguageGetMagic'][]     		= 'gfQLPlusParserFunction_Magic';
$wgExtensionFunctions[] 				= "gfQLPlusHooks";
 
function gfQLPlusParserFunction_Setup( &$parser ) {
	$parser->setFunctionHook( 'askplus', 'SemanticQueryPlus::render' );
	return true;
}
 
function gfQLPlusParserFunction_Magic( &$magicWords, $langCode ) {
	$magicWords['askplus'] = array( 0, 'askplus' );
	return true;
}

// hooks
function gfQLPlusHooks() 
{
     global $wgParser;
     global $wgHooks;
     global $wgMessageCache;
	 global $wgQLPlus_DlvODBCRealTime;
	 global $wgQLPlus_Solver;
	 
	if ($wgQLPlus_DlvODBCRealTime != true || $wgQLPlus_Solver != "dlvdb")
		return;
	 
     $wgHooks['ArticleSaveComplete'][] = 'SemanticQueryPlus::updatePageSave';
	 
	 // must to insert at the first, otherwise can't get the deleted triples.
	 $wgHooks['ArticleDelete'] =  array_merge ( 
		array('SemanticQueryPlus::updatePageDelete') , $wgHooks['ArticleDelete']   ) ;

     $wgHooks['ArticleUndelete'][] = 'SemanticQueryPlus::updatePageUndelete';
	 $wgHooks['TitleMoveComplete'][] = 'SemanticQueryPlus::updatePageMove';
     $wgHooks['ArticlePurge'][] = 'SemanticQueryPlus::updatePageRefresh';
}

?>