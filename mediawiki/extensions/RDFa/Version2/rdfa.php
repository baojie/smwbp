<?php
/*
 Defines an extension which enables the functionality of outputing semantic data in the RDFa format.
 
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

require_once("new_mapping.php");
$rdfa_output_bool=true;
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
        'version' => '0.0.2',
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
	global $rdfa_map_page;
	global $search_monkey_map,$rdfa_output_bool;
	//if default page that maps semantic property of MediaWiki to other semantic property is given, use that page
	if($rdfa_map_page!=null&&!$search_monkey_map)
	{
		$parser=null;
		wf_RDFa_Mapping_Render($parser,$rdfa_map_page);
	}
	if($rdfa_output_bool)
		generateRDFa($wgTitle,$text);
	
	return true;
}
/*
 * This function gets the semantic data of the current page, and output the data in the RDFa format
 * Input:
 * 	    $title: the title of the current page
 *           $text: the HTML text (string) that is being added
 *
 */
function generateRDFa($title,&$text)
{
	global $wgServer,$wgScript;
	$host_address=$wgServer.$wgScript;
	
	$rdfa_output="";
	
	//Get the semantic data of current page
	$page = SMWDataValueFactory::newTypeIDValue( '_wpg',  $title->getFullText());
    $semdata = smwfGetStore()->getSemanticData($page->getTitle() );			
	
	if ($semdata == null)
	{
		return;
	}
	
	$id=1;
	$this_page = $title->getFullURL();	
	$text .= "<div id='RDFa' about='".$this_page."' xmlns:wiki_".$id."='".$host_address."/'".
			                                       "xmlns:wiki_".$id."_property='".$host_address."/Property:'".
												   "xmlns:wiki_".$id."_category='".$host_address."/Category:'";
												   
	//variable for tranlated rdfa
	$search_monkey_rdfa_output="";
	$search_monkey_rdfa_namespace="";
	
	//render the triples (stored in $semdata) in RDFa , which will be added to the HTML output of the page
	foreach($semdata->getProperties() as $key => $property)
	{	
		//get the values of each property
		$propvalues = $semdata->getPropertyValues($property);
		$rdf_schema='';
		$wiki_type='';
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
				
				$propvalue_output =  str_replace(':Category','',$propvalue->getWikiValue());
				$text            .=  " typeof='wiki_".$id."_category".$propvalue_output."'";
				
				//output translated rdfa
				$search_monkey_rdfa_namespace=search_monkey_property_mapping("Category".$propvalue_output,$title);
			}
			//process the triple if property is used to define subclass relation
			else if ($property_id == '_SUBC') 
			{
				if($rdf_schema=='')
				{
					$rdf_schema="http://www.w3.org/2000/01/rdf-schema#\n";
					$text .= "xmlns:rdfs='".$rdf_schema."'";
				}	
				$property_output="rdfs:subClassOf";
				$propvalue_output="wiki_".$id."_category:".str_replace(" ","_",$propvalue->getWikiValue());
				$special_property = true;
			}
			//process the triple if property is used to define sub-property relation
			else if ($property_id == '_SUBP') 
			{
				if($rdf_schema=='')
				{
					$rdf_schema="http://www.w3.org/2000/01/rdf-schema#\n";
					$text .= "xmlns:rdfs='".$rdf_schema."'";
				}	
				
				$property_output="rdfs:subPropertyOf";
				$propvalue_output="wiki_".$id.":".str_replace(" ","_",$propvalue->getWikiValue());
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
				
				if($wiki_type=='')
				{
					$wiki_type = "xmlns:wiki_".$id."_type='".$host_address."/Type:'\n";
					$text     .= $wiki_type;
				}
					
				$property_string = $property->getWikiValue();
				$property_string = str_replace( ' ', '_',$property_string);				
				$property_output = "wiki_".$id."_property:".$property_string;
				

				$propvalue_page_string=$propvalue->getWikiValue();
				$propvalue_output="wiki_".$id."_type:".str_replace(" ","_",$propvalue_page_string);
				$special_property = true;				
			}
			//process the triple if property id does not belong to above cases
			else
			{
				//get the URL of property
				$property_string = $property->getWikiValue();	
				$property_string=str_replace( ' ', '_',$property_string);
				$property_output = "wiki_".$id."_property:".$property_string;
								
				//if the value of property is not a page
				//print the semantic data using "<div>" markup 				 
				if(strtolower($property->getTypesValue()->getWikiValue()) != "page")
				{
					$propvalue_output = $propvalue->getWikiValue();
					$rdfa_output .= "<div property='".$property_output."' content='".$propvalue_output."'></div>\n";
					$search_monkey_rdfa_output.=search_monkey_rdfa($property_string,$propvalue_output,"string");
				}
				//otherwise print the data using "<a>" markup
				else
				{
					$propvalue_output = "wiki_".$id.":".str_replace(" ","_",$propvalue->getWikiValue());
					$rdfa_output     .= "<a href='".$propvalue_output."' rel='".$property_output."'></a>\n";
					$search_monkey_rdfa_output.=search_monkey_rdfa($property_string,$propvalue->getWikiValue(),"page");
				}
			}
			
			//output the relation in RDFa if the property is one of the special case: _subc, _subp, _type, _redi
			if($special_property)
			{
				$rdfa_output     .=  "<a href='".$propvalue_output."' rel='".$property_output."'></a>\n";			
			}
		}
	}

	
	if($search_monkey_rdfa_namespace=="")
		$search_monkey_rdfa_namespace="<div style='display:none' ".search_monkey_property_mapping("prefix",$title).">";
		
	$search_monkey_rdfa_output.="</div>";	
	//add rdfa data to the final output
	$text.=">\n".$rdfa_output."</div>".$search_monkey_rdfa_namespace.$search_monkey_rdfa_output;
}

/*
 * This function returns the data in preferred(translated) RDFa format
 * Input:
 * 	    $property: the property would like to be mapped to another property
 *          $propvalue: the value of the property
 *	    $propvalue_type: this variable is used to check if the type of propvalue is a Page or not.  The value of this variable is either "page" or "string"	
 */
function search_monkey_rdfa($property,$propvalue,$propvalue_type)
{
	$rdfa_property=search_monkey_property_mapping($property,null);
	$rdfa_output="";
	
	if($rdfa_property=="")
		return "";
	
	

	if($propvalue_type=="page"&&$rdfa_property=="wiki:".$property)
	{
		$propvalue = str_replace(" ","_",$propvalue);
		$rdfa_output="<a rel='".$rdfa_property."' resource='wiki:".$propvalue."'></a>\n";
	}
	else
	{
		if($rdfa_property=="dc:issued"||$rdfa_property=="vcal:dtstart"||$rdfa_property=="vcal:dtend"){
		$rdfa_output="<span property='".$rdfa_property."' datatype='xsd:dateTime' content='".$propvalue."'></span>\n";
		}
		else if($rdfa_property=="dc:publisher")
		{
			$rdfa_output='<div rel="dc:publisher">'.
						 '	<div typeof="vcard:Organization">'.
						 '     <span property="rdfs:label vcard:organization-name">'.$propvalue.'</span>'.
						 '	</div>'.
						'</div>';
		}
		else if($rdfa_property=="dc:creator")
		{
			$rdfa_output='<div rel="dc:creator">'.
							'<div typeof="vcard:VCard">'.
								'<span property="rdfs:label vcard:fn">'.$propvalue.'</span>'.
							'</div>'.
						'</div>';
		}
		else if($rdfa_property=="rdfs:seeAlso media:image")
		{
			$rdfa_output='<span rel="rdfs:seeAlso media:image">'.
								'<img src="'.$propvalue.'"/>'.
						  '</span>';
		}
		else if($rdfa_property=="vcal:location")
		{
			$rdfa_output='<div rel="vcal:location">'.
							'<div typeof="vcard:VCard commerce:Business">'.
								'<div rel="vcard:adr">'.
									'<div typeof="vcard:Address">'.
										'<span property="rdfs:label">'.$propvalue.'</span>'.
									'</div>'.
								'</div>'.
							'</div>'.
						  '</div>';
		}
		else if($rdfa_property=="vcard:latitude")
		{
			$rdfa_output='<div rel="vcard:geo"><span property="vcard:latitude" datatype="xsd:float" content="'.$propvalue.'"></span></div>';
		}
		else if($rdfa_property=="vcard:longitude")
		{
			$rdfa_output='<div rel="vcard:geo"><span property="vcard:longitude" datatype="xsd:float" content="'.$propvalue.'"></span></div>';
		}
		else if($rdfa_property=="vcal:url rdfs:seeAlso")
		{
			$rdfa_output="<span property='vcal:url rdfs:seeAlso' resource='".$propvalue."'></span>\n";
		}
		else
		{
			$rdfa_output="<span property='".$rdfa_property."'>".$propvalue."</span>\n";
		}
	}
	
	return $rdfa_output;
}
?>