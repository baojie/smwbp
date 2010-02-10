<?php


require_once("arc/ARC2.php");
//require_once("this_mapping.php");
if( !defined( 'MEDIAWIKI' ) ) {
    echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
    die( 1 );
}

/*
 *Define parser functions
 */
$wgExtensionFunctions[] = 'wf_rdfa_extract';
$wgHooks['LanguageGetMagic'][]= 'wf_rdfa_extract_Magic';
//$wgHooks['LanguageGetMagic'][]= 'wf_import_rdfa_Magic';

function wf_rdfa_extract() 
{
    global $wgParser;
    $wgParser->setFunctionHook( 'rdfa_extract', 'wf_rdfa_extract_Render' );
}
 
function wf_rdfa_extract_Magic( &$magicWords, $langCode ) 
{
    $magicWords['rdfa_extract'] = array( 0, 'rdfa_extract' );
    return true;
}

$wgExtensionCredits['parserhook'][] = array(
        'name' => 'rdfa_extract',
        'url' => 'http://www.mediawiki.org/wiki/Extension:RDFa',
        'author' => array ('Jin Guang Zheng','Jie Bao'),
        'description' => '',
        'version' => '0.0.3',
);


function wf_rdfa_extract_Render(&$parser, $page_link, $template) 
{

	global $wgTitle,$map;
	
	$output="";
	//initialize_mapping($mapping_page);
	//echo "1".$page_link;
	$page_link = preg_replace("/&/", "%26", $page_link);
	$page_link = preg_replace("/\s/","",$page_link);
	//echo "2".$page_link;
	$data = new SimpleXMLElement($page_link);
	foreach ($data->tr as $entry)
	{
		if(sizeof($entry->td)<1)
			continue;
		
		if(empty($entry->td[0]))
			continue;
		
		$link=$entry->td[0];
		$output.="processing: ".$link."<br>";
		outputRDFa($parser,$wgTitle,$output,$link,$template);
	}
	
	return array( $output, 'noparse' => false, 'isHTML' => false );
	
}


function outputRDFa(&$parser,$title,&$text,$test_url,$template)
{
	$test_url2='http://www.cs.rpi.edu/~zhengj3/searchmonkey2.html';
	$test_url3="http://tw.rpi.edu/portal/Event";
	$this_page = $title->getFullURL();
	$config = array('auto_extract' => 0);
	$ARC_parser = ARC2::getSemHTMLParser();
	//echo "1".preg_replace("/<br \/>/","",$test_url);
	$ARC_parser->parse($test_url);
	$ARC_parser->extractRDF('rdfa');
	$triples=$ARC_parser->getTriples();
	$indexes = $ARC_parser->getSimpleIndex();

	foreach ($indexes as $index=>$objects)
		foreach ($objects as $property=>$values)
			foreach ($values as $value)
			{

					$index=preg_replace('/^_:/',$test_url."#",$index);
					$value=preg_replace('/^_:/',$test_url."#",$value);
					$id=sha1($index.'|'.$property.'|'.$value);
					$title = Title::newFromText($id); 
					$newArticle=new Article($title, 0);

					$content="{{".$template."|".$index."|".$property."|".urlencode($value)."}}<br>";
					if ( ! $newArticle->exists() )
					{
						$newArticle->doEdit($content,"","",EDIT_UPDATE|EDIT_FORCE_BOT);
						$text.="exist:<nowiki> ".$content."</nowiki>";
					}
					else
					{						
						$newArticle->doEdit($content,"","",EDIT_NEW|EDIT_FORCE_BOT);
						$text.="new: ".$content;
					}
					$text.=$property."[[Has Triple::".$id."]]<br>";

			}

	SMWOutputs::commitToParser($parser);
			
}
function create_page($id)
{
	
}
?>