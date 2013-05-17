<?php
 
/**
* @file
* @ingroup Extensions
* @author Aran Dunkley [http://www.organicdesign.co.nz/nad User:Nad]
* @copyright © 2007 Aran Dunkley
* @licence GNU General Public Licence 2.0 or later
*/
 
if( !defined( 'MEDIAWIKI' ) ) die( "Not an entry point." );
 
define( 'MYVARIABLES_VERSION', "2.1, 2010-11-08" );
 
/** REGISTRATION */
$wgExtensionCredits['parserhook'][] = array(
        'path' => __FILE__,
        'name' => 'MyVariables',
        'url' => 'http://www.mediawiki.org/wiki/Extension:MyVariables',
        'author' => array( 'Aran Dunkley', '...' ),
        'description' => 'Allows the addition of new built in variables',
        'version' => MYVARIABLES_VERSION
);
 
/** EXTENSION */
$wgCustomVariables = array('CURRENTUSER','CURRENTUSERREALNAME','LOGO');
 
$wgHooks['MagicWordMagicWords'][]          = 'wfmvAddCustomVariable';
$wgHooks['MagicWordwgVariableIDs'][]       = 'wfmvAddCustomVariableID';
$wgHooks['LanguageGetMagic'][]             = 'wfmvAddCustomVariableLang';
$wgHooks['ParserGetVariableValueSwitch'][] = 'wfmvGetCustomVariable';
 
function wfmvAddCustomVariable(&$magicWords) {
        foreach($GLOBALS['wgCustomVariables'] as $var) $magicWords[] = "MAG_$var";
        return true;
        }
 
function wfmvAddCustomVariableID(&$variables) {
        foreach($GLOBALS['wgCustomVariables'] as $var) $variables[] = "MAG_$var";
        return true;
        }
 
function wfmvAddCustomVariableLang(&$langMagic, $langCode = 0) {
        foreach($GLOBALS['wgCustomVariables'] as $var) {
                $magic = "MAG_$var";
                $langMagic[defined($magic) ? constant($magic) : $magic] = array(0,$var);
                }
        return true;
        }
 
function wfmvGetCustomVariable(&$parser,&$cache,&$index,&$ret) {
        switch ($index) {
 
                case 'MAG_CURRENTUSER':
                        $parser->disableCache(); # Mark this content as uncacheable
                 $ret = $GLOBALS['wgUser']->mName;
                        break;
 
                case 'MAG_LOGO':
                        $ret = $GLOBALS['wgLogo'];
                        break;
 
                case 'MAG_CURRENTUSERREALNAME':
                        $parser->disableCache(); # Mark this content as uncacheable
                 $ret = $GLOBALS['wgUser']->mRealName;
                        break;
                }
        return true;
        }
