<?php
/*
 Defines a set of useful functions for SMW content generation
 verion: 1.0
 authors: Li Ding (lidingpku@gmail.com) 
 update: 08 Feburary 2009
 
 changelog
 
    
    
The  MIT License
 
 Copyright (c) 2008 Li Ding and Jie Bao

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
 
if ( !defined( 'MEDIAWIKI' ) ) {
    die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}
 
$wgExtensionFunctions[] = 'wfSetupSemanticToolkit';
 
$wgExtensionCredits['parserhook'][] = array(
        'name' => 'SemanticToolkit',
        'url' => 'http://www.mediawiki.org/wiki/Extension:SemanticToolkit',
        'author' => array ('Li Ding'),
        'description' => 'convinient tools for using semantic mediawiki',
        'version' => '1.0',
);
 
$wgHooks['LanguageGetMagic'][]       = 'wfSemanticToolkitLanguageGetMagic';
 
/**
   *  named arrays    - an array has a list of values, and could be set to a SET
 */ 
class SemanticToolkit {

/**
* generate SMW annotation smartly
* 
* {{#smartset:property=value|options}}
* property
*     'type' is a reserved property name indicating that is a type declaration
* value
*     multiple values can be deliminated by a delimiter
* namespace options:   ns=...
*      allowed values:   a wiki namespace (not ended by colon ':')
* format options: 
*      predefined values (exclusive in the following preemption order):  
*            hide -- no print
*            ul(default)   -- unnumberd list
*            ol  -- numbered list
*            tr  -- table row
*            text  --  just value, just text
*            link --  just value,  SMW link
* parse options: 
*      predefined values exclusive): 
*           m -- value is a multiple value list  
*           list -- deprecaded alternative of 'm'
*           delimiter  -- the specified delimiter for the value
*           sep  -- the delimiter for separating values in print-out
* 
* example
* {{#smartset:name=Li,Jie|m}}
*/ 
    function smartset( &$parser, $propertyvalue , $options) {
	///////////////////////////////
        //preprocess
	
	//parse property and value
	if (!empty($propertyvalue)){
		$temp = explode('=',$propertyvalue,2);
		if (!empty($temp[0]) && !empty($temp[1])){
		  $my_property= trim($temp[0]);
		  $my_value= trim($temp[1]);
		}
	}
	if (empty($my_property)||empty($my_value))
	   return '';
	   

	//parse options
	$my_params = array();
	$my_options = array();
	if (!empty($options)){
		$options = trim($options);
		$tempary= explode('&',$options);
		foreach ($tempary as $v){
			if (strpos($v,'=')>0){
				$temp = explode('=',$v,2);
				if (!empty($temp[0]) && !empty($temp[1]) && !is_numeric($temp[0])){
					$my_params[trim(strtolower($temp[0]))]= trim($temp[1]);
				}
			}else{
				$my_options[] = strtolower($v);
			}
		}
	}

	$all_format_options = array('ul','ol','tr','text','link','hide');
	if (count(array_intersect($my_options,$all_format_options))===0){
		$my_options [] ='ul';
	}
	
	//namespace of value
	if (strcmp($my_property,'type')===0){
		$my_namespace_of_value='Category:';
	}else{
		$tempkey ='ns';
		$temparray =$my_params;
		if ( array_key_exists($tempkey, $temparray) && isset($temparray[$tempkey])){
			$my_namespace_of_value =$temparray[$tempkey].':';
		}else{
			$my_namespace_of_value='';
		}	
	}
	//remove redundant namespace 
	if (!empty($my_namespace_of_value)){
		$my_value= str_replace($my_namespace_of_value,'',$my_value);	
	}
	
	// delimiter for parse
	$tempkey ='delimiter';
	$temparray =$my_params;
	if ( array_key_exists($tempkey, $temparray) && isset($temparray[$tempkey])){
		$my_delimiter =$temparray[$tempkey].':';
	}else{
		if (FALSE!==strpos($my_value,';')){
			$my_delimiter  =';';
		}else if (FALSE!==strpos($my_value,',')){
			$my_delimiter  =',';
		}
	}	
	
	// separator for print
	$tempkey ='sep';
	$temparray =$my_params;
	if ( array_key_exists($tempkey, $temparray) && isset($temparray[$tempkey])){
		$my_sep =$temparray[$tempkey].':';
	}else{
		$my_sep  =',';
	}	
	if (in_array('hide',$my_options)){
		$my_sep  ='';
	}


	///////////////////////////////
        //parse
	
	// parse value
	$my_values [] = $my_value;
	if (in_array('m',$my_options)||in_array('list',$my_options)){
		//smart list
		if (!empty($my_delimiter)){
			$my_values = $this->array_consolidate(explode($my_delimiter,$my_value));
		}
	}
	
	///////////////////////////////
        //print
	
	//print results
	foreach ($my_values as $k => $v){
		if (strcmp($my_property,'type')===0){
			$my_values[$k] = "[[Category:$v]]";
			if (in_array('hide',$my_options)){
			}else if (in_array('text',$my_options)){
				$my_values[$k] .= "Category:$v";
			}else {
				$my_values[$k] .= "[[:Category:$v]]";
			}
		}else{
			if (in_array('hide',$my_options)){
				$my_values[$k] = "[[$my_property::$my_namespace_of_value$v|]]";
			}else if (in_array('text',$my_options)){
				$my_values[$k] = "[[$my_property::$my_namespace_of_value$v|]]$my_namespace_of_value$v";
			}else {
				$my_values[$k] = "[[$my_property::$my_namespace_of_value$v]]";
			}
		}
	}
	
	//print value
	if (count($my_values)==0){
	}else if (count($my_values)==1){
		$ret = $my_values[0];
	}else{
		$ret = implode( $my_sep, $my_values);
	}
	
	// post process on print
	if (!empty($ret)){
		if (in_array('hide',$my_options)){
		}else if (in_array('ul',$my_options)){
			$ret="* $my_property: ".$ret ; 
		}else if (in_array('ol',$my_options)){
			$ret="# $my_property: ".$ret ; 
		}else if (in_array('tr',$my_options)){
			$ret="\n|-\n| $my_property\n|".$ret ; 
		}
	}
	return array( $ret, 'noparse' => false, 'isHTML' => false);
    }
   
    function array_consolidate($data){
	if (!is_array($data)){
		return false;
	}
	$ret = array(); //reset 
	foreach (array_unique($data) as $v){
	   if (!empty($v))
		$ret[]= trim($v);
	}
	return $ret;
    }
   
    function smartsetObj(  &$parser, $frame, $args ) {
	return $this->smartset($parser, 
		isset($args[0]) ? trim($frame->expand($args[0])) : '', 
		isset($args[1]) ? trim($frame->expand($args[1])) : '');
    }


    function smartprint( &$parser, $value , $search, $subject) {
	if (empty($value)){
		return '';
	}
	
	if (empty($search) || empty($subject)){
		return $value;
	}else{
		return str_replace($search, $value, $subject);
	}
    }
    
    function smartprintObj(  &$parser, $frame, $args ) {
	return $this->smartset($parser, 
		isset($args[0]) ? trim($frame->expand($args[0])) : '', 
		isset($args[1]) ? trim($frame->expand($args[1])) : '', 
		isset($args[2]) ? trim($frame->expand($args[2])) : '');
    }
}
 
function wfSetupSemanticToolkit() {
    global $wgParser, $wgMessageCache, $wgSemanticToolkit, $wgMessageCache, $wgHooks;
 
    $wgSemanticToolkit = new SemanticToolkit;

    if( defined( get_class( $wgParser) . '::SFH_OBJECT_ARGS' ) ) {
	$wgParser->setFunctionHook('smartset', array( &$wgSemanticToolkit, 'smartsetObj' ), SFH_OBJECT_ARGS);
    } else {
	$wgParser->setFunctionHook( 'smartset', array( &$wgSemanticToolkit, 'smartset' ) );
    }
    
    if( defined( get_class( $wgParser) . '::SFH_OBJECT_ARGS' ) ) {
	$wgParser->setFunctionHook('smartprint', array( &$wgSemanticToolkit, 'smartprint' ), SFH_OBJECT_ARGS);
    } else {
	$wgParser->setFunctionHook( 'smartprint', array( &$wgSemanticToolkit, 'smartprint' ) );
    }

    //$wgParser->setFunctionHook( 'smartshow', array( &$wgSemanticToolkit, 'smartshow' ) );  // f.show

}
 
function wfSemanticToolkitLanguageGetMagic( &$magicWords, $langCode ) {
        require_once( dirname( __FILE__ ) . '/SemanticToolkit.i18n.php' );
        foreach( efSemanticToolkitWords( $langCode ) as $word => $trans )
                $magicWords[$word] = $trans;
        return true;
}
 
?>