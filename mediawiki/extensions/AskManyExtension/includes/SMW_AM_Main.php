<?php

if ( !defined( 'MEDIAWIKI' ) ) {
  die( "This file is part of the AskMany extension. It is not a valid entry point.\n" );
}

$amgScriptPath = $wgScriptPath . '/extensions/AskManyExtension';

$amgIP = $IP . '/extensions/AskManyExtension';

$amgAskManyResponder = true;
$amgAskManyRequester = true;

function enableAskMany() {
	global $amgIP, $wgExtensionFunctions, $wgAutoloadClasses, $wgSpecialPages, $wgHooks;

	$wgExtensionFunctions[] = 'amfSetupExtension';
	#$wgHooks['LanguageGetMagic'][] = 'amfAddMagicWords';

	$wgAutoloadClasses['AMAskExternal']	= $amgIP . '/specials/SMW_SpecialAskExternal.php';
	$wgSpecialPages['AskExternal']		= array('AMAskExternal');
}

function amfSetupExtension() {
	global $amgIP, $amgScriptPath, $wgHooks, $wgParser, $wgExtensionCredits, $wgLanguageCode;

	#amfInitContentLanguage($wgLanguageCode);

	#require_once($amgIP . '/includes/SMW_AM_Hooks.php');
	
	if( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) ) {
	  $wgHooks['ParserFirstCallInit'][] = 'amfRegisterParserFunctions';
	} else {
	  if ( class_exists( 'StubObject' ) && !StubObject::isRealObject($wgParser) ) {
	    $wgParser->_unstub();
          }
	  $wgExtensionFunctions[] = 'amfRegisterParserFunctions';
        }

	$wgExtensionCredits['parserhook'][] = 
            array('name'=>'AskManyExtension',
	          'version'=>'0.1',
                  'author'=>'[http://www.evanpatton.com/ Evan Patton], [http://tw.rpi.edu/ Tetherless World]',
		  'url'=>'http://tw.rpi.edu/wiki/index.php/AskMany_Extension',
		  'description'=>'Adds the ability to query other wikis with the AskMany extension installed.');

        return true;
}

function amfRegisterParserFunctions() {
	global $wgParser;
	$wgParser->setHook( 'askmany', 'amfProcessAskManyInlineQuery' );
	#$wgParser->setFunctionHook( 'askmany', 'amfProcessAskManyQueryParserFunction' );
	return true;
}

/**
 * The <askmany> parser hook processing part.
 */
function amfProcessAskManyInlineQuery($querytext, $params, &$parser) {
	global $smwgQEnabled, $smwgIQRunningNumber, $wgOut;
	
	$theResult = '';
	$xmldom = new DOMDocument();
	$xmldom->loadXML('<?xml version="1.0"?><askmany>' . $querytext . '</askmany>');
	$sites = $xmldom->getElementsByTagName("site");
	$queryLocal = $params["querylocal"];
	if($queryLocal == null or $queryLocal != "true")
	  $queryLocal = false;
	else
	  $queryLocal = true;

	//$theResult .= "Will query sites:<br />";
	
	$len = $sites->length;
	$tempDoc = new DOMDocument();
	$theTable = $tempDoc->createElement("table");
	$theHeader = $tempDoc->createElement("tr");
	$tempDoc->appendChild($theTable);
	$theTable->appendChild($theHeader);
	$smwTableAttribute = $tempDoc->createAttribute("class");
	$smwTableAttribute->value = "smwtable";
	$theTable->appendChild($smwTableAttribute);
	$theCurRow = null;
	for($i=0;$i < $len;$i++) {
	  $node = $sites->item($i);
	  $textnode = $node->firstChild;
	  $baseuri = $textnode->nodeValue;
	  $remoteResults = new DOMDocument();
	  $remoteUri = $baseuri;
	  if(1==strlen(strrchr($baseuri, "/"))) {
	    $remoteUri .= "Special:AskExternal?q=";
	  }
	  else {
	    $remoteUri .= "?title=Special:AskExternal&q=";
	  }
	  $queryNodes = $xmldom->getElementsByTagName("query");
	  $queryNode = $queryNodes->item(0);
	  $textNode = $queryNode->firstChild;
	  $queryText = $textNode->nodeValue;
	  $remoteUri .= urlencode($queryText);
	  //$theResult .= $remoteUri . "<br />";
	  $remoteResults->loadHTMLFile($remoteUri);
	  $rows = $remoteResults->getElementsByTagName("tr");
	  for($j = 1;$j < $rows->length; $j++) {
	    $theCurRow = $rows->item($j);
	    if($theCurRow->childNodes->length-1 > $theHeader->childNodes->length) {
	      for($k = $theHeader->childNodes->length; $k < $theCurRow->childNodes->length-1; $k++) {
		$theHeader->appendChild($tempDoc->createElement("th"));
	      }
	    }
	    $theCurRow = $tempDoc->importNode($theCurRow, true);
	    $theTable->appendChild($theCurRow);
	  }
	}
	$theResult .= $tempDoc->saveHTML();
	//if($smwgQEnabled and $queryLocal)
	//  $theResult .= "Local data<br />";

	//$queryNodes = $xmldom->getElementsByTagName("query");
	//$theResult .= "Number of query nodes: " . $queryNodes->length . "<br />";
	//$queryNode = $queryNodes->item(0);
	//$textNode = $queryNode->firstChild;
	//$queryText = $textNode->nodeValue;
	//$theResult .= "Query: " . $queryText . "<br />";
	/*
	foreach($params as $key => $value) {
		$theResult .= '<br />' . $key . ' = ' . $value;
	}
	*/
	if($smwgQEnabled and $queryLocal) {
	} else {
	}
	return $theResult;
}

/**
 * The {{#askmany }} parser function processing part.
 */
function amfProcessAskManyQueryParserFunction(&$parser) {
	global $smwgQEnabled, $smwgIQRunningNumber;
	if($smwgQEnabled) {
	} else {
	}
}
