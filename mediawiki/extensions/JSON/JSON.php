<?php
/**
 * Create Semantic MediaWiki annotation using JSON format
 * Extension documentation: http://www.mediawiki.org/wiki/Extension:JSON
 *
 * @file JSON.php
 *
 * @licence GNU GPL v3 or later
 * @author Jie Bao < baojie@gmail.com >
 */
 
/*
Usage Example

{{#json:
{
  "name" : "John", 
  "address" : {
    "city" : "Boston",
  }
}
}}

*/ 
 
$wgExtensionCredits['parserhook'][] = array(
   'path' => __FILE__,
   'name' => "JSON",
   'description' => "Create Semantic MediaWiki annotation using JSON format",    
   'version' => 0.1, 
   'author' => "[http://www.mediawiki.org/wiki/User:Baojie Jie Bao]",
   'url' => "http://www.mediawiki.org/wiki/Extension:JSON"
 );
 
$wgHooks['ParserFirstCallInit'][] = 'efJSON_Initialize';
 
$wgHooks['LanguageGetMagic'][] = 'efJSON_Magic';

$efJSON_Count = 0;
 
function efJSON_Initialize(&$parser) {
   $parser->setFunctionHook('json', 'efJSON_Render');
   return true;
}
 
function efJSON_Magic(&$magicWords, $langCode) {
   $magicWords['json'] = array(0, 'json');
   return true; 
}
 
function efJSON_Render($parser, $json = '') { 
   global $wgArticle, $wgOut;
   $output = '';
   $data = json_decode($json,true);   
   if ($data == NULL){
		return "<font color='red'>JSON format error:</font> <pre>$json</pre>";		
   }   
   #$json_string = json_encode($data);
   $title = $wgArticle ->getTitle()->getFullText();
   $triples = efJSON_Parse($data, efJSON_makeID() , $title);
   
   #$wgOut->addWikiText($triples);
   $output = $parser->recursiveTagParse($triples);
   $output = '<div class="wonderful">' . $output . '</div>';
   
   #debug
   #$output = "<pre>$json</pre><pre>$triples</pre>";
   #return $output;
   #debug ends
   
   return $output;
}

function efJSON_makeID()
{
	global $efJSON_Count;
	$efJSON_Count++;
	return "json$efJSON_Count";
}

function efJSON_Parse($obj, $id, $title){			
	# create a new json object, increase the gobal counter of json ids
    $output = "{{#subobject:$id|Subobject of=$title";
	#$output = "{{#set_internal:$id|Subobject of=$title";
	
	# other subobjects
	$more_objects = '';
	
	# fill propert slots
	# create subojects if the slot value is another json object
	foreach ($obj as $property => $value){
	    # if the value is not an array, it is a value
		if (!is_array($value)){	
			if (is_bool($value))
				$value = $value ? "true" : "false";
			$output .= "|$property=$value";
		}
		#otherwise, the value is another json object
		else{
			$new_id = efJSON_makeID();
			$output .= "|$property=$title#$new_id";
			$more_objects .= efJSON_Parse($value, $new_id, $title);
		}		
	}
	$output .= "}}";#."\n";
	$output .= $more_objects;
	return $output;
}