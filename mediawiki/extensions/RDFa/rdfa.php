<?php
/*
 Defines an extension which enables the functionality of outputing semantic data in the RDFa format.
 
 verion: 0.0.1
 authors: Jin Guang Zheng (zhengj3@rpi.edu) and Jie Bao
 update: 09/09/2009
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

$wgExtensionFunctions[] = 'RDFa';

$wgExtensionCredits['parserhook'][] = array(
        'name' => 'RDFa',
        'url' => 'http://www.mediawiki.org/wiki/Extension:RDFa',
        'author' => array ('Jin Guang Zheng','Jie Bao'),
        'description' => 'Output semantic data in the RDFa format',
        'version' => '0.0.1',
);

/*
 *Hook RDFa function to OutputPageBeforeHTML
 */
function RDFa() 
{
    global $wgHooks;
	$wgHooks['OutputPageBeforeHTML'][] = 'printRDFa';
}

/*
 * This function gets the title of the current page and calls the generateRDFa function to output the semantic data of the page.
 * Input: Defined in 'OutputPageBeforeHTML'
 * 	    $out: the OutputPage (object) to which wikitext is added 
 *           $text: the HTML text (string) that is being added
 * 
 */
function printRDFa(&$out,&$text)
{
	global $wgTitle;
	
	genertateRDFa($wgTitle,$text);
	
	return true;
}

/*
 * This function gets the semantic data of the current page, and output the data in the RDFa format
 * Input:
 * 	    $title: the title of the current page
 *           $text: the HTML text (string) that is being added
 *
 */
function genertateRDFa($title,&$text)
{
	$rdfa_output="";
	
	//Get the semantic data of current page
	$page = SMWDataValueFactory::newTypeIDValue( '_wpg',  $title->getFullText());
    $semdata = smwfGetStore()->getSemanticData($page->getTitle() );			
	
	if ($semdata == null)
	{
		return;
	}
	
	
	$this_page = $title->getFullURL();
	$text.="<div id='RDFa' about='".$this_page."'";	
	$count=0;
	//render the triples (stored in $semdata) in RDFa , which will be added to the HTML output of the page
	foreach($semdata->getProperties() as $key => $property)
	{	
		//get the values of each property
		$propvalues = $semdata->getPropertyValues($property);
		$count++;
		// process triples grouped by predicates
		foreach ($propvalues as $propvalue) 
		{	
			//get the proerty id
			$property_id =  $property->getPropertyID();
			$special_property=false;
			$propvalue_page_string="";
			
			//process the triple if property is used to define an instance(category)	
			if($property_id == '_INST')
			{
				$propvalue_page   =  SMWDataValueFactory::newTypeIDValue( '_wpg',$propvalue->getWikiValue());
				$propvalue_output =  $propvalue_page->getTitle()->getFullURL();
				$text            .=  " typeof='".$propvalue_output."'";
			}
			//process the triple if property is used to define subclass relation
			else if ($property_id == '_SUBC') 
			{
				$property_output  =  'http://www.w3.org/2000/01/rdf-schema#subClassOf';
				$propvalue_page_string='Category:'.$propvalue->getWikiValue();
				$special_property = true;
			}
			//process the triple if property is used to define sub-property relation
			else if ($property_id == '_SUBP') 
			{
				$property_output = 'http://www.w3.org/2000/01/rdf-schema#subPropertyOf';
				$propvalue_page_string='Property:'.$propvalue->getWikiValue();
				$special_property = true;
			}
			//process the triple if property is used to define the current page is redirect from anther page
			else if ($property_id == '_REDI') 
			{
				$property_output = 'http://www.w3.org/2002/07/owl#sameAs';
				$special_property = true;
			}
			//process the triple if property is used to define the type of a property's value
			else if($property_id == '_TYPE')
			{
				$property_string = $property->getWikiValue();	
				$property_page   = SMWDataValueFactory::newTypeIDValue( '_wpg', "Property:".$property_string);
				$property_output = $property_page->getTitle()->getFullURL();
				
				if(strtolower(substr($propvalue->getWikiValue(),0,5))!="type:")
					$propvalue_page_string='Type:';
				$propvalue_page_string.=$propvalue->getWikiValue();
				
				$special_property = true;				
			}
			//process the triple if property is used as any case defined above
			else
			{
				//get the URL of property
				$property_string = $property->getWikiValue();	
				$property_page   = SMWDataValueFactory::newTypeIDValue( '_wpg', "Property:".$property_string);
				$property_output = $property_page->getTitle()->getFullURL();
				
				
				//if the value of property is not a page
				//print the semantic data using "<div>" markup 				 
				if(strtolower($property->getTypesValue()->getWikiValue()) != "page")
				{
					$propvalue_output = $propvalue->getWikiValue();
					$rdfa_output .= "<div property='".$property_output."' content='".$propvalue_output."'></div>\n";
				}
				//otherwise print the data using "<a>" markup
				else
				{
					$propvalue_page   = SMWDataValueFactory::newTypeIDValue( '_wpg',$propvalue->getWikiValue());
					$propvalue_output = $propvalue_page->getTitle()->getFullURL();
					$rdfa_output     .= "<a href='".$propvalue_output."' rel='".$property_output."'></a>\n";
				}
			}
			
			//output the relation in RDFa if the property is one of the special case: _subc, _subp, _type, _redi
			if($special_property)
			{
				$propvalue_page   =  SMWDataValueFactory::newTypeIDValue( '_wpg',$propvalue_page_string);
				$propvalue_output =  $propvalue_page->getTitle()->getFullURL();
				$rdfa_output     .=  "<a href='".$propvalue_output."' rel='".$property_output."'></a>\n";			
			}
		}
	}

	//add rdfa data to the final output
	$text.=">\n".$rdfa_output."</div>";
}

?>