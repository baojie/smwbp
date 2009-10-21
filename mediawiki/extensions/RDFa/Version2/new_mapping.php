<?php
/*
 This file defines the functionality that maps MediaWiki property to other property
 
 verion: 0.0.2
 authors: Jin Guang Zheng (zhengj3@rpi.edu) and Jie Bao
 update: 10/20/2009
 homepage:http://www.mediawiki.org/wiki/Extension:RDFa
 
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


if( !defined( 'MEDIAWIKI' ) ) {
    echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
    die( 1 );
}

//a hash map variable holds the mapping between 2 proeprties
$search_monkey_map;

/*
 *Define parser functions
 */
$wgExtensionFunctions[] = 'wf_RDFa_Mapping';
$wgHooks['LanguageGetMagic'][]= 'wf_RDFa_Mapping_Magic';

function wf_RDFa_Mapping() 
{
    global $wgParser;
    $wgParser->setFunctionHook( 'rdfa_map', 'wf_RDFa_Mapping_Render' );
}
 
function wf_RDFa_Mapping_Magic( &$magicWords, $langCode ) 
{
    $magicWords['rdfa_map'] = array( 0, 'rdfa_map' );
    return true;
}

/*
 * This function gets the content of the page contains the mapping between 2 properties
 * input:
 * 	$parser: the parser function
 * 	$page_title: title of the page contains the mapping between 2 properties 
 */
function wf_RDFa_Mapping_Render( &$parser, $page_title)
{
	global $search_monkey_map;
	$search_monkey_map=null;
	
	//get the content
	$title = Title::newFromText($page_title);	
	if ( isset($title) == false)  // page name illegal
		return false;
	$editPage = new Article( $title, 0 );
	$map_content = $editPage->getContent();
	
	//parse the content and store the result to the hash table $serach_monkey_map 
	$search_monkey_map=parseContent($map_content);
	$output=""; 
	if($parser!=null)
	return array( $output, 'noparse' => true, 'isHTML' => true );
}
/*
 * This function parse the content of the mapping page
 * Input:
 *	$content: a string contains the content of the mapping page
 * Return:
 *	$search_monkey_mappoing: a hash table contains the parse result, key: either 'prefix' or property of MediaWiki, value: the preferred property 
 */
function parseContent($content)
{
	$search_monkey_mapping['prefix']="";
	$map_array=explode("<br>", $content);
	for($i=0;$i<count($map_array);$i++)
	{
		$str=$map_array[$i];
		if(preg_match('/@prefix/',$str))
		{
			$prefix=explode(" ",$str);
			$prefix_value="";
			
			for($j=1;$j<count($prefix);$j++)
			{
				
				if(isset($prefix[$j]))
				{
					$prefix_value.=$prefix[$j]." ";
				}	
			}
			$search_monkey_mapping['prefix'].=$prefix_value;
		}
		else
		{
			
			$str=str_replace("\n","",$str);
			$property=explode("=",$str);
			if(isset($property[0])&&isset($property[1]))
			{
				$property[0]=str_replace(" ","",$property[0]);
				preg_match('/^\s*(.*?)\s*$/',$property[1],$matches);
				$search_monkey_mapping[$property[0]]=$matches[1];
			}
		}
	}
	
	return $search_monkey_mapping;
}
/*
 * This function is called by rdfa.php, and returns the preferred (translated) property
 * Input:
 * 	$property: MediaWiki property would liked to be translated (the key to hash table $search_monkey_map)
 *	$title:  title of the current page(the page would like to be translated)
 * Return: preferred property (the value of hash table $serach_monkey_map)
 */
function search_monkey_property_mapping($property,$title)
{	 
	global $wgServer,$wgScript,$search_monkey_map;
	if(!isset($search_monkey_map)) return "";

	$this_page="";
	if($title !=null)
	{
		$this_page=$title->getFullURL();
	}

	if($property=="prefix")
		$search_monkey_map['prefix'].='xmlns:wiki_property="'.$wgServer.$wgScript.'/Property:"'.' about="'.$this_page.'"';  
		
	  //return map result
	if(isset($search_monkey_map[$property]))
		return $search_monkey_map[$property];
	else if($this_page=="")
		return "wiki_property:".$property;
	else
		return "";
}
