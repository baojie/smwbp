<?php
/**

Property mapping for searchMonkey

*/
function search_monkey_property_mapping($property,$title)
{	 
	global $wgServer,$wgScript;
	$this_page="";
	if($title !=null)
		$this_page=$title->getFullURL();
	
	//for news
	$search_monkey_map['DC:date']='dc:issued';
	$search_monkey_map['FOAF:name']='dc:title';
	$search_monkey_map['Has_author']='dc:creator';
	$search_monkey_map['Has_where_published']='dc:publisher';
	$search_monkey_map['Category:News Article']='<div about="'.$this_page.'" id="news_div" typeof="dcmitype:Text sioc:Post"
  xmlns:dcmitype="http://purl.org/dc/terms/DCMIType/"
  xmlns:dc="http://purl.org/dc/terms/"
  xmlns:sioc="http://rdfs.org/sioc/ns#"
  xmlns:media="http://search.yahoo.com/searchmonkey/media/"
  xmlns:xsd="http://www.w3.org/2001/XMLSchema#"
  xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
  xmlns:vcard="http://www.w3.org/2006/vcard/ns#"
 xmlns:wiki="'.$wgServer.$wgScript.'/">';
 
	//for event
	$search_monkey_map['End_date']='vcal:dtend';
	$search_monkey_map['Location']='vcal:location';
	$search_monkey_map['Start_date']='vcal:dtstart';
	$search_monkey_map['Name']='dc:title';
	$search_monkey_map['Has_start_date']='vcal:dtstart';
	$search_monkey_map['Has_end_date']='vcal:dtend';
	$search_monkey_map['Has_location']='vcal:location';
	$search_monkey_map['Has_label']='vcard:fn rdfs:label';
	$search_monkey_map['Has_label2']='vcard:label rdfs:label';
	$search_monkey_map['Comment']='rdfs:comment';
	$search_monkey_map['Latitude']='vcard:latitude';
	$search_monkey_map['Longitude']='vcard:longitude';
	$search_monkey_map['Longitude']='vcard:longitude';
	$search_monkey_map['Category:Event']='<div about="'.$this_page.'" id="event_div" typeof="vcal:Vevent" 
	  xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
	  xmlns:vcal="http://www.w3.org/2002/12/cal/icaltzd#"
	  xmlns:dc="http://purl.org/dc/terms/"
	  xmlns:vcard="http://www.w3.org/2006/vcard/ns#"
	  xmlns:review="http://purl.org/stuff/rev#"
	  xmlns:xsd="http://www.w3.org/2001/XMLSchema#"
	  xmlns:commerce="http://search.yahoo.com/searchmonkey/commerce/"
	  xmlns:wiki_property="'.$wgServer.$wgScript.'/Property:">';
	  
	  
	  //return map result
	  if(isset($search_monkey_map[$property]))
		return $search_monkey_map[$property];
	  else if($this_page=="")
		return "wiki_property:".$property;
	  else
		return "";
}


?>