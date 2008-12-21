<?php
 
if ( !defined( 'MEDIAWIKI' ) ) {
    die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}
 
$wgExtensionFunctions[] = 'wfSetupVariables';
 
$wgExtensionCredits['parserhook'][] = array(
        'name' => 'Variables',
        'version' => '1.1',
        'url' => 'http://www.mediawiki.org/wiki/Extension:VariablesExtension',
        'author' => 'Rob Adams',
        'description' => 'Define page-scoped variables'
);
 
$wgHooks['LanguageGetMagic'][]       = 'wfVariablesLanguageGetMagic';
 
class ExtVariables {
    var $mVariables;
 
    function vardefine( &$parser, $expr = '', $value = '' ) {
        $this->mVariables[$expr] = $value;
        return '';
    }
 
    function vardefineecho( &$parser, $expr = '', $value = '' ) {
        $this->mVariables[$expr] = $value;
        return $value ;
    }
 
    function varf( &$parser, $expr = '' ) {
        return $this->mVariables[$expr];
    }
}
 
function wfSetupVariables() {
    global $wgParser, $wgMessageCache, $wgExtVariables, $wgMessageCache, $wgHooks;
 
    $wgExtVariables = new ExtVariables;
 
    $wgParser->setFunctionHook( 'vardefine', array( &$wgExtVariables, 'vardefine' ) );
    $wgParser->setFunctionHook( 'vardefineecho', array( &$wgExtVariables, 'vardefineecho' ) );
    $wgParser->setFunctionHook( 'var', array( &$wgExtVariables, 'varf' ) );
}
 
function wfVariablesLanguageGetMagic( &$magicWords, $langCode = 0 ) {
        require_once( dirname( __FILE__ ) . '/Variables.i18n.php' );
        foreach( efVariablesWords( $langCode ) as $word => $trans )
                $magicWords[$word] = $trans;
        return true;
}