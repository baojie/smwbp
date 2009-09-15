<?php
/*

 Track changes in a Semantic MediaWiki and render them as semantic data.
 
 verion: 0.1
 authors:Jie Bao (baojie@gmail.com) and Li Ding (lidingpku@gmail.com) 
 update: 16 July 2009
 homepage: http://www.mediawiki.org/wiki/Extension:SemanticHistory
 
The  MIT License
 
 Copyright (c) 2009

 Permission is hereby granted, free of charge, to any person
 obtaining a copy of this software and associated documentation
 files (the "Software"), to deal in the Software without
 restriction, including without limitation the rights to use,
 copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the
 Software is furnished to do so, subject to the following
 conditions:

 The above copyright notice and this permission notice shall be
 included in all copies or substantial portions of the Software.

 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 OTHER DEALINGS IN THE SOFTWARE.
*/

/*
Jie Bao 2009-07-07
*/
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

// lode the setting file
require_once('SemanticHistory_Setting.php');

global $wgHooks;

$wgExtensionCredits['other'][] = array(
    'name' => 'Semantic Hisory',
    'version' => '0.1',
    'author' => '[http://www.cs.rpi.edu/~baojie Jie Bao], [http://www.cs.rpi.edu/~dingl Li Ding]',
    'url' => 'http://www.mediawiki.org/wiki/Extension:SemanticHistory',
    'description' => 'Render revision history of the Wiki in semantic forms',
);
			   
$wgExtensionFunctions[] = "wfSemanticHistoryExtension";

$wgExtraNamespaces[NS_HISTORY] = PREFIX_HISTORY;
$wgExtraNamespaces[NS_REVISION] = PREFIX_REVISION;

//allow semantics on the two domain spaces
$smwgNamespacesWithSemanticLinks += 
   array( NS_HISTORY => true, 
     NS_REVISION => true);
	 
// disallow public access to the history and triple namespaces
$wgNamespaceProtection[NS_HISTORY] = array('history');
$wgNamespaceProtection[NS_REVISION] = array('history');
$wgGroupPermissions['sysop']['history'] = true;
$wgHooks[ 'userCan' ][] = 'fnSH_AceessPolicy';

// make sure nobody can edit history (except sysop)
function fnSH_AceessPolicy(&$title, &$user, $action, &$result)
{
    global $wgSemanticHistoryAllowEdit;
	global $wgSemanticHistoryAllowEdit;
     
	// do not allow editing of protected namespaces	
	if 	(fnIfProtected1($title) && $action == 'edit') 
	    $result = $wgSemanticHistoryAllowEdit;	
		
	// do not allow reading of protected namespaces	
	if 	(fnIfProtected1($title) && $action == 'read') 
	    $result = $wgSemanticHistoryAllowRead;	
		
    return true;		
}

// main entry of the extension
function wfSemanticHistoryExtension() 
{
     global $wgParser;
     global $wgHooks;
     global $wgMessageCache;
     $wgHooks['ArticleSaveComplete'][] = 'fnSemanticHistorySave';
     $wgHooks['ArticleSave'][] = 'fnSemanticHistoryBeforeSave';
	 
	 // must to insert at the first, otherwise can't get the deleted triples.
	 $wgHooks['ArticleDelete'] =  array_merge ( 
		array('fnSemanticHistoryDelete') , $wgHooks['ArticleDelete']   ) ;

     $wgHooks['ArticleUndelete'][] = 'fnSemanticHistoryUndelete';
	 $wgHooks['TitleMoveComplete'][] = 'fnSemanticHistoryMove';
}

$mTripleBeforeSave = array();

// if the article is in a protected name space
function fnIfProtected($article)
{ 
    if ($article)
    	return fnIfProtected1($article->getTitle());
    else return false;
}

// if the title object is in a protected name space
function fnIfProtected1($title)
{ 
	$namespace = $title-> getNamespace();
	return ($namespace == NS_HISTORY || $namespace == NS_REVISION);
}

// get triples associated with a page (by its article object)
function getTriple($article)
{
	return getTriple1($article->getTitle());
}
	   
// get triples associated with a page (by it title object)
// The returned data is an array, each key is a property name, and its value a set of values of the property
// For example, is a page has [[p::v1]], [p::v2]], [q::v3]], then the returned array of the page will be 
//  { p->{v1,v2}, q->{v3}}  
function getTriple1($title)
{
	//$text = '';
		
	$page = SMWDataValueFactory::newTypeIDValue( '_wpg',  $title->getFullText());
    $semdata = smwfGetStore()->getSemanticData($page->getTitle() 	);	
		
	// build the semantic data
	if ($semdata == null)
	{
		//$text = "null"; 
		return text;
	}
	
	// subject is the page's name
	$s = $title->getFullText();
	
	$arr = array();
		
	// get all triple of the page	
	foreach($semdata->getProperties() as $key => $property){
		$p =  $property->getShortText(false,NULL);
		$p2 =  $property->getPropertyID();
		if (!$arr[$p]) $arr[$p] = array();
		
		// some pre-defined properties
		// http://semantic-mediawiki.org/doc/SMW__SQLStore2_8php-source.html		
		if ($p2 == '_MDAT') continue; //time stamp
		else if ($p2 == '_INST') $p = 'rdf:type';
		else if ($p2 == '_SUBC') $p = 'rdfs:subClassOf';
		else if ($p2 == '_SUBP') $p = 'rdfs:subPropertyOf';
		else if ($p2 == '_REDI') $p = 'owl:sameAs';
		else if ($p2 == '_TYPE') $p = 'has_type';
		else { $p = str_replace('Property:','',$p); }
        
		// Capitalize the first letter of the property name (as MW always does) 
		$p[0] = mb_strtoupper($p[0]);
		
		$propvalues = $semdata->getPropertyValues($property);
		foreach ($propvalues as $propvalue) {
			$o=$propvalue->getShortText(false);		   
			//$text .= "$s $p $o;<br/>";
			$arr[$p][] =$o;
		}
	}	
	return $arr;
}

// make a "unique" id of the triple
// SHA1 is a hashing function that generate a 40-char string for the input. 
// There might be a very small chance that two triples having the same id (a collision ), but very unlike
function getID($s,$p,$ov)
{
	return sha1($s.'|'.$p.'|'.$ov);
}

// a helper function to add new text to a page
function appendArticle($papertitle, $contentIfNew, $content,$summary='')
{ 
   if ( strlen(trim($papertitle)) == 0 || $papertitle == ':') return false;   
  
   $title = Title::newFromText($papertitle); 
   if ( isset($title) == false)  // page name illegal
	   return false;
   
   $editPage = new Article( $title, 0 );
   $oldcontent = $editPage->getContent();
   
   if ( $editPage->exists() )
		return $editPage->doEdit($oldcontent.$content,$summary,EDIT_UPDATE|EDIT_FORCE_BOT);
	else //new
		return $editPage->doEdit($contentIfNew.$content,$summary,EDIT_NEW|EDIT_FORCE_BOT);
} 

// filp the obsolete flag of a triple page
function flipFlag($papertitle, $flag=1, $summary='')
{ 
   $obsolete = HISTORY_OBSOLETE;
   if ( strlen(trim($papertitle)) == 0 || $papertitle == ':') return false;   
  
   $title = Title::newFromText($papertitle); 
   if ( isset($title) == false)  // page name illegal
	   return false;
   
   $editPage = new Article( $title, 0 );
   $oldcontent = $editPage->getContent();
   $neg = (string)(1-$flag);
   $flag  = (string)$flag;
   if(strpos($oldcontent,         "{{",$obsolete."|".$neg."}}") == true) 
		$oldcontent = str_replace("{{".$obsolete."|".$neg."}}", 
		                          "{{".$obsolete."|".$flag."}}", $oldcontent);
   else if (strpos($oldcontent,   "{{".$obsolete."|".$flag."}}") == true) 	{}
   else $oldcontent = $oldcontent."{{".$obsolete."|".$flag."}}\n";   
   
   if ( $editPage->exists() ){
		return $editPage->doEdit($oldcontent,$summary,EDIT_UPDATE|EDIT_FORCE_BOT);
	}
	else //new
		return $editPage->doEdit($oldcontent,$summary,EDIT_NEW|EDIT_FORCE_BOT);
} 

// get triples of a page before a save action
function fnSemanticHistoryBeforeSave(&$article, &$user, &$text, &$summary,
 $minor, $watch, $sectionanchor, &$flags)
{
	global $mTripleBeforeSave;
	//skip the "his" name space
	if (fnIfProtected($article)) return true;
	
	//$rev_id = $article->getRevIdFetched();
	//createNewArticle("His:2","{{Edit}}{{Rev|$rev_id||}}");
	
	$mTripleBeforeSave = getTriple($article);
	//$t = '';
	//$s =  $article->getTitle()->getFullText();
	//foreach($mTripleBeforeSave as $p => $o)
	//{
	//	foreach ($o as $ov)
	//		$t .= "$s $p $ov;<br/>";
	//}
    //createNewArticle("His:3",$t,'',true);
	
	return true;
}

// get triples of a page after a save action
function fnSemanticHistorySave(&$article, &$user, $text, $summary,
 &$minoredit, $watchthis, $sectionanchor, &$flags, $revision, &$status, $baseRevId)
{
	global $mTripleBeforeSave,$wgSemanticHistoryAnalyzeTemplate;
	
	//skip the "his" name space
	if (fnIfProtected($article)) return true;
	
	$s =  $article->getTitle()->getFullText();
	
	// section 1: the revision page
	
	$user_name = "User:".$user->getName();
	$rev_id = ''; $rev_time = '';
	if ($revision)
	{
		$rev_id = $revision->getId();
		$rev_time = $revision->getTimestamp(); 	
	}
	$Rev = HISTORY_REV;
	appendArticle(PREFIX_REVISION.":$rev_id","{{".$Rev."|$rev_id|$rev_time|$user_name|$s}}","");
	
	if ($minoredit) appendArticle(PREFIX_REVISION.":$rev_id","","{{".HISTORY_MINOR."}}");
	
	// process the summary
	if ($summary) {
		// esacpe special characters
	    $sss = str_replace('[[','<nowiki/>[<nowiki/>[<nowiki/>',$summary);
	    $sss = str_replace(']]','<nowiki/>]<nowiki/>]<nowiki/>',$sss);
	    $sss = str_replace('{{','<nowiki/>{<nowiki/>{<nowiki/>',$sss);
	    $sss = str_replace('}}','<nowiki/>}<nowiki/>}<nowiki/>',$sss);
		$sss = str_replace('|','<nowiki>|</nowiki>',$sss);
		appendArticle(PREFIX_REVISION.":$rev_id","",
		  "{{".HISTORY_SUMMARY."|$sss}}");
	}
	
	// analyze templates used by the page, find predicates used on them.
	if ($wgSemanticHistoryAnalyzeTemplate)
	{
		//Find all templates it uses
		$templateUsed = $article->getUsedTemplates();
		foreach($templateUsed as $t)
		{
			// get the title's current revision 
			$template_id = $t->getLatestRevID();
			if ($template_id){ // if the page exists
				$template_name = $t->getFullText();
				appendArticle(PREFIX_REVISION .":$rev_id","",
				  "{{".HISTORY_TEMPLATE."|$template_name|$template_id}}\n");
			}
		}
		
		// find all known properties assertions that may assert by including this page
		//prase $text
		//removed comments
		$text =  eregi_replace("<!--[^>]*-->","",$text);
		$text =  eregi_replace("<nowiki>.*</nowiki>","",$text);
		//because we are only interested in tracking template pages, noinclude parts will be stripped
		$text =  eregi_replace("<noinclude>.*</noinclude>","",$text);
		//@todo: strip ask
		//$text =  preg_replace("/{{#ask:.*?[^}]}}[^}]/iU","",$text);
		//remove queries
		
		global $smwgLinksInValues;
		if ($smwgLinksInValues) { // more complex regexp -- lib PCRE may cause segfaults if text is long :-(
			$semanticLinkPattern = '/\[\[                 # Beginning of the link
									(?:([^:][^]]*):[=:])+ # Property name (or a list of those)
									(                     # After that:
									  (?:[^|\[\]]         #   either normal text (without |, [ or ])
									  |\[\[[^]]*\]\]      #   or a [[link]]
									  |\[[^]]*\]          #   or an [external link]
									)*)                   # all this zero or more times
									(?:\|([^]]*))?        # Display text (like "text" in [[link|text]]), optional
									\]\]                  # End of link
									/xu';
		} else { // simpler regexps -- no segfaults found for those, but no links in values
			$semanticLinkPattern = '/\[\[                 # Beginning of the link
									(?:([^:][^]]*):[=:])+ # Property name (or a list of those)
									([^\[\]]*)            # content: anything but [, |, ]
									\]\]                  # End of link
									/xu';
		}
		$categoryPattern = '/\[\[\s*([Cc]ategory:(.*?))\]\]/xu';
		preg_match_all($semanticLinkPattern, $text, $properties);
		preg_match_all($categoryPattern, $text, $categories);
		
		$p_template = "{{".HISTORY_PROPERTIES."|";
		$c_template = "{{".HISTORY_CATEGORIES."|";
		foreach($properties[1] as $p) { $p_template .= trim($p).";"; };		
		foreach($categories[2] as $c) { $c_template .= trim($c).";"; };
		$p_template = trim ($p_template,';');
		$c_template = trim ($c_template,';');
		$p_template .= "}}\n";  
		$c_template .= "}}\n";

		appendArticle(PREFIX_REVISION .":$rev_id","",  $p_template.$c_template); 		
		
	} // end if wgSemanticHistoryAnalyzeTemplate
	
	// section 2: the triple pages
	
	$TripleAfterSave = getTriple($article);
	// get deleted triples
	foreach($mTripleBeforeSave as $p => $o)
	{
		foreach ($o as $ov)
			if ($TripleAfterSave[$p] ==null || !in_array($ov,$TripleAfterSave[$p])){
				// this triple has been deleted
				$id =  getID($s,$p,$ov);				
				appendArticle( PREFIX_HISTORY . ":$id",
				    "{{".HISTORY_TRIPLE."|$s|$p|$ov}}\n",
				    "{{".HISTORY_DELETE."|$rev_id|$rev_time|$user_name}}\n");
				flipFlag(PREFIX_HISTORY . ":$id",1);
			}
	}
	// get new triples
	foreach($TripleAfterSave as $p => $o)
	{
		foreach ($o as $ov)
			if ($mTripleBeforeSave[$p] ==null || !in_array($ov,$mTripleBeforeSave[$p])){
				// this triple has been added
				$id =  getID($s,$p,$ov);				
				appendArticle(PREFIX_HISTORY .":$id",
				   "{{". HISTORY_TRIPLE ."|$s|$p|$ov}}\n",
				   "{{". HISTORY_ADD ."|$rev_id|$rev_time|$user_name}}\n");
				flipFlag(PREFIX_HISTORY .":$id",0);
			}
	}    
	
    return true;
}

// track triple changes in a deletion action
// since a deletion action has no database id, a new id start with "D", followed by the action timestample, will be used 
// as its id
function fnSemanticHistoryDelete(&$article, &$user, $reason, $id) 
{
	//skip the "his" name space
	if (fnIfProtected($article)) return true;
	
	$mTripleBeforeDelete = getTriple($article);
	
	$s =  $article->getTitle()->getFullText();
	
	$user_name = "User:".$user->getName();
	$rev_time = gmdate('YmdHis'); 	
	// invent a revision id, because deletion has no revision
	$rev_id = 'D'.$rev_time;	
	appendArticle(PREFIX_REVISION.":$rev_id",
	   "{{". HISTORY_REV ."|$rev_id|$rev_time|$user_name|$s|Deletion}}",""); 
	
	// get deleted triples
	foreach($mTripleBeforeDelete as $p => $o)
	{
		foreach ($o as $ov){
			$id =  getID($s,$p,$ov);				
			appendArticle(PREFIX_HISTORY .":$id",
			    "{{". HISTORY_TRIPLE ."|$s|$p|$ov}}\n",
			    "{{". HISTORY_DELETE ."|$rev_id|$rev_time|$user_name}}\n");
			flipFlag(PREFIX_HISTORY .":$id",1);
		}
	}
	return true;
}

// track triple changes in an undeletion action
// since an undeletion action has no database id, a new id start with "U", followed by the action timestample, 
//will be used as its id
function fnSemanticHistoryUndelete($title, $create) 
{
	global $wgUser;
	//skip the "his" name space
	if (fnIfProtected($article)) return true;
	
	$mTripleAfterUndelete = getTriple1($title);
	
	$s =  $title->getFullText();	
	
	$user_name = "User:".$wgUser->getName();
	$rev_time = gmdate('YmdHis'); 	
	// invent a revision id, because undeletion has no revision
	$rev_id = 'U'.$rev_time;	
	appendArticle(PREFIX_REVISION.":$rev_id",
	    "{{".HISTORY_REV."|$rev_id|$rev_time|$user_name|$s|Undeletion}}",""); 
	
	// get restored triples
	foreach($mTripleAfterUndelete as $p => $o)
	{
		foreach ($o as $ov){
			$id =  getID($s,$p,$ov);				
			appendArticle(PREFIX_HISTORY .":$id",
			     "{{". HISTORY_TRIPLE ."|$s|$p|$ov}}\n",
			    "{{" . HISTORY_ADD . "|$rev_id|$rev_time|$user_name}}\n");
			flipFlag(PREFIX_HISTORY .":$id",0);
		}
	}
	
    return true;
}

// track triple changes in a move action
// since a move action has no database id, a new id start with "M", followed by the action timestample, 
//will be used as its id
function fnSemanticHistoryMove(&$title, &$newtitle, &$user, $oldid, $newid) 
{
	global $wgUser;
	//skip the "his" name space
	if (fnIfProtected($article)) return true;
	
	$mTripleAfterMove = getTriple1($title);
	
	$s =  $title->getFullText();	
	$s1 =  $newtitle->getFullText();	
	
	$user_name = "User:".$user->getName();
	$rev_time = gmdate('YmdHis'); 	
	// invent a revision id, because undeletion has no revision
	$rev_id = 'M'.$rev_time;	
	appendArticle(PREFIX_REVISION .":$rev_id",
	    "{{". HISTORY_REV ."|$rev_id|$rev_time|$user_name|$s1|Move}}",""); 
	
	// get new triples
	foreach($mTripleAfterMove as $p => $o)
	{
		foreach ($o as $ov){
			$id =  getID($s,$p,$ov);	//old triple			
			$id1 =  getID($s1,$p,$ov);	// new triple			
			appendArticle(PREFIX_HISTORY .":$id",
			   "{{".HISTORY_TRIPLE."|$s|$p|$ov}}\n",
			   "{{".HISTORY_DELETE."|$rev_id|$rev_time|$user_name}}\n");
			flipFlag(PREFIX_HISTORY .":$id",1);
			appendArticle(PREFIX_HISTORY .":$id1",
			   "{{".HISTORY_TRIPLE."|$s1|$p|$ov}}\n",
			   "{{".HISTORY_ADD."|$rev_id|$rev_time|$user_name}}\n");
			flipFlag(PREFIX_HISTORY.":$id1",0);
		}
	}	
    return true;
}
?>