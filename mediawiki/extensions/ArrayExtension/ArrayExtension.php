<?php
/*
 Defines a subset of parser functions that operate with arrays.
 verion: 1.1.1
 authors: Li Ding (lidingpku@gmail.com) and Jie Bao
 update: 05 Feburary 2009
 homepage: http://www.mediawiki.org/wiki/Extension:ArrayExtension
 
 changelog
 * Feb 8, 2009 version 1.1.1
    - update #arrayprint, now wiki links, parser functions and templates properly parsed. This enables foreach loop call.
 * Feb 5, 2009 version 1.1
    - update #arraydefine: replacing  'explode' by 'preg_split', 
           and we now allow delimitors to  be (i) a string; or (ii) a perl regular expressnion pattern, sourrounded by '/', e.g. '/..blah.../'
    - update #arrayprint, change parameters from "prefix","suffix" to a "template", 
           and users can replace a substring in the template with array value, similar to arraymap in semantic forms
    - update #arrayunique,  empty elements will be removed
    - update #arraysort: adding "random" option to make the array of values in random order
    - add #arrayreset to free all defined arrays for memory saving
    - add #arrayslice to return an array bounded by start_index and length.
    - add  #arraysearch. now we can return the index of the first occurence of an element, return -1 if not found
    - remove #arraymember,  obsoleted by #arraysearch
    - remove #arraypush, obsoleted by #arraydefine and #arraymerge
    - remove #arraypop, obsoleted by  #arrayslice    
    - add safty check code to avoid unset parameters
    
 * Feb 1, 2009 version 1.0.3 
    - fixed bug on arrayunique,   php array_unique only make values unique, but the array index was not updated.  (arraydefine is also affected)
 * Jan 28, 2009 version 1.0.2 
    - changed arraypop  (add one parameter to support multiple pop)
    - added arrayindex (return an array element at index)
 * Jan 27, 2009  version 1.0.1 
    - changed arraydefine (allow defining empty array)
 
 
 == Part1. constructor ==
 {{#arraydefine:key|values|delimiter}}

 Define an array by a list of 'values' deliminated by 'delimiter', 
 the delimiter should be perl regular expression pattern
 * http://us2.php.net/manual/en/book.pcre.php
 * see also: http://us2.php.net/manual/en/function.preg-split.php
 
 
 
 == Part2. print functions ==

 {{#arrayprint:key|delimiter|search|subject}}

 foreach value of the array, print 'subject' where  all occurrences of 'search' is replaced with the value, deliminated by 'delimiter'
  
 notes:
* the subject can embed parser functions; wiki links; and templates.
 
 examples:
 {{#arrayprint:b}}    -- simple
  {{#arrayprint:b|<br/>}}    -- add change line
  {{#arrayprint:b|<br/>|@@@|[[@@@]]}}    -- embed wiki links
  {{#arrayprint:b|<br/>|@@@|{{#set:prop=@@@}} }}   -- embed parser function
 {{#arrayprint:b|<br/>|@@@|{{f.tag{{f.print.vbar}}prop{{f.print.vbar}}@@@}} }}   -- embed template function
 {{#arrayprint:b|<br/>|@@@|[[name::@@@]]}}   -- make SMW links
 
   {{#arraysize:key}}

   Print the size (number of elements) in the specified array
  See: http://www.php.net/manual/en/function.count.php
   
   
   
   {{#arraysearch:key|value}}

   print "1" or "0" to show whether the value is a member of the array identified by key
   See: http://www.php.net/manual/en/function.in-array.php
   
 
   {{#arrayindex:key|index}}

   print the value of an array (identified by key)  by the index, invalid index result in nothing being printed. note the index is 0-based.
 

 == Part3. alter array ==
   
   
   {{#arraysort:key|order}}
   
   sort specified array in the following order:
    * none (default)  - no sort   
   *  desc - in descending order, large to small
   *  asce - in ascending order, small to large
   * random - shuffle the arrry in random order
   see: http://www.php.net/manual/en/function.sort.php
          http://www.php.net/manual/en/function.rsort.php
          http://www.php.net/manual/en/function.shuffle.php
   
   {{#arrayunique:key}}

   make the array identified by key a set (all elements are unique)
   see: http://www.php.net/manual/en/function.array-unique.php
          

    {{#arrayreset:}}

   reset all defined arrayes
       
   == Part4. create a new array ==
   
   {{#arraymerge:key|key1|key2}}
   
   merge values two arrayes identified by key1 and key2 into a new array identified by key.
   this merge differs from array_merge of php because it merges values.
   
     {{#arrayslice:key|key1|offset|length}}

    extract a slice from an  array
    see: http://www.php.net/manual/en/function.array-slice.php
  
    == Part 5.  create a new array, set operations ==
    
    {{#arrayintersect:key|key1|key2}}
    
    set operation,    {red} = {red, white} intersect {red,black}
   See: http://www.php.net/manual/en/function.array-intersect.php


   
    {{#arrayunion:key|key1|key2}}
    
    set operation,    {red, white} = {red, white} union {red}
    similar to arraymerge, this union works on values.

    
    
    {{#arraydiff:key|key1|key2}}
    
    set operation,    {white} = {red, white}  -  {red}
    see: http://www.php.net/manual/en/function.array-diff.php
   

 -------------------------------------------
 the following fuctions are obsoleted
    #arraypush  (replaced by arraymerge)
    #arraypop  (replaced by arrayslice)
    #arraymember (replaced by arraysearch)
 -------------------------------------------
    
    
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
 
$wgExtensionFunctions[] = 'wfSetupArrayExtension';
 
$wgExtensionCredits['parserhook'][] = array(
        'name' => 'ArrayExtension',
        'url' => 'http://www.mediawiki.org/wiki/Extension:ArrayExtension',
        'author' => array ('Li Ding','Jie Bao'),
        'description' => 'store and compute named arrays',
        'version' => '1.1',
);
 
$wgHooks['LanguageGetMagic'][]       = 'wfArrayExtensionLanguageGetMagic';
 
/**
   *  named arrays    - an array has a list of values, and could be set to a SET
 */ 
class ArrayExtension {
    var $mArrayExtension; 
 
    // define an array variable
    function arraydefine( &$parser, $key, $value='', $delimiter = '/\s*,\s*/', $option = 'all', $sort = 'none') {
        if (!isset($key))
	   return '';

        //normalize 
	$value = trim($value);
	$delimiter = trim($delimiter);
	if (empty ($value)){
	    $this->mArrayExtension[$key] = array();
	}else if (empty ($delimiter)){
	    $this->mArrayExtension[$key] = array( $value );
	}else{ 
	    if (0!==strpos($delimiter,'/') || (strlen($delimiter)-1)!==strrpos($delimiter,'/')){
		$delimiter='/\s*'.$delimiter.'\s*/';
	    }
	    
	    $this->mArrayExtension[$key] = preg_split ($delimiter, $value);
	    switch ($option){	
		case 'unique': $this->arrayunique($parser, $key); break;
	    };
	    $this->arraysort($parser, $key, $sort);
	}
	    	
	return '';
    }


    //////////////////////////////////////////////////
    // Display Options:  print array
    /**
     * print an array
     *      {{#arrayprint:key|delimiter|search|subject}}
     * example
     */
    function arrayprint( &$parser, $key , $delimiter = ', ', $search='@@@@', $subject='@@@@', $frame=null) {
        if (!isset($key))
	   return '';
	   
        if (!isset($this->mArrayExtension))
 	   return "undefined array: $key";
	
        if (!array_key_exists($key,$this->mArrayExtension) || !is_array($this->mArrayExtension[$key]))
 	   return "undefined array: $key";
	   
	$values=$this->mArrayExtension[$key];    
	$rendered_values= array();
	foreach($values as $v){
		$temp_result_value  = str_replace($search, $v, $subject);
		if (isset($frame)){
			$temp_result_value = $parser->preprocessToDom($temp_result_value, $frame->isTemplate() ? Parser::PTD_FOR_INCLUSION : 0);
			$temp_result_value = trim($frame->expand($temp_result_value));
                }                  
		$rendered_values[] = $temp_result_value ;
	}
	return array(implode( $delimiter, $rendered_values) , 'noparse' => false, 'isHTML' => false);
    }
   
    function arrayprintObj(  &$parser, $frame, $args ) {
		// Set variables
	$key = isset($args[0]) ? trim($frame->expand($args[0])) : '';
	$delimiter = isset($args[1]) ? trim($frame->expand($args[1])) : ', ';
	$search = isset($args[2]) ? trim($frame->expand($args[2], PPFrame::NO_ARGS | PPFrame::NO_TEMPLATES)) : '@@@@';
	$subject = isset($args[3]) ? trim($frame->expand($args[3], PPFrame::NO_ARGS | PPFrame::NO_TEMPLATES)) : '@@@@';

	return $this->arrayprint($parser, $key, $delimiter, $search, $subject, $frame);
    }

    function arrayindex( &$parser, $key , $index ) {
        if (!isset($key) || !isset($index))
	   return '';

	if (isset($this->mArrayExtension)
	    && array_key_exists($key,$this->mArrayExtension) && is_array($this->mArrayExtension[$key])
        ){
		if (is_numeric($index) && $index>=0 && $index<count($this->mArrayExtension[$key])){
		    return $this->mArrayExtension[$key][$index];
		}
       }
       return '';
    }
   
    // return size of array
    function arraysize( &$parser, $key) {
        if (!isset($key) )
	   return '';

       if (isset($this->mArrayExtension)    
            && array_key_exists($key,$this->mArrayExtension) && is_array($this->mArrayExtension[$key])) 
	{
          return count ($this->mArrayExtension[$key]);
	}
       return '';
    }    
    
    // locate the index of the first occurence of an element, return -1 if not found
    function arraysearch( &$parser, $key, $needle) {
        if (!isset($key) || !isset($needle) || strlen($needle)===0)
	   return '';

        if (isset($this->mArrayExtension)    
	    && array_key_exists($key,$this->mArrayExtension) && is_array($this->mArrayExtension[$key]))
	{
	    if (false !== ($ret = array_search($needle, $this->mArrayExtension[$key], true)))
	       return $ret;	       
        }
	return '-1';
    }        
   

   
   //////////////////////////////////////////////////
    // alter an array   
    
    // reset memory
    function arrayreset( &$parser) {
	$this->mArrayExtension = array();
	return '';
    }    
    
    // convert an array to set
    function arrayunique( &$parser, $key ) {
        if (!isset($key))
	   return '';

        if (isset($this->mArrayExtension)   
              && array_key_exists($key,$this->mArrayExtension) && is_array($this->mArrayExtension[$key]))
	{
	    $this->mArrayExtension[$key]= array_unique ($this->mArrayExtension[$key]);
	    $values= array();
	    foreach ($this->mArrayExtension[$key] as $v){
		if (!empty($v))
		   $values[]=$v;
	    }
	    $this->mArrayExtension[$key] = $values;
        }
	return '';
    }    

    // sort an array 
    function arraysort( &$parser, $key , $sort = 'none') {
        if (!isset($key))
	   return '';
    
        if (isset($this->mArrayExtension)    
	       && array_key_exists($key,$this->mArrayExtension) && is_array($this->mArrayExtension[$key]))
	{
	    switch ($sort){	
		case 'asce': sort($this->mArrayExtension[$key]); break;
		case 'ascending': sort($this->mArrayExtension[$key]); break;
		
		case 'desc': rsort($this->mArrayExtension[$key]); break;
		case 'descending': rsort($this->mArrayExtension[$key]); break;
		case 'random': shuffle($this->mArrayExtension[$key]); break;
	    };
        }
	return '';
    }    
    

    //////////////////////////////////////////////////
    // create  an array   
    
    // merge two arrays,  keep duplicated values 
    function arraymerge( &$parser, $key, $key1, $key2='' ) {
        if (!isset($key) ||!isset($key1) )
	   return '';
	   
	$this->mArrayExtension[$key] = array();
        if (isset($this->mArrayExtension)    
	     && array_key_exists($key1,$this->mArrayExtension) && is_array($this->mArrayExtension[$key1]) 
	){
	    foreach ($this->mArrayExtension[$key1] as $entry){
		   array_push ($this->mArrayExtension[$key], $entry);
	    }

	    if ( strlen($key2)>0 && array_key_exists($key2,$this->mArrayExtension) && is_array($this->mArrayExtension[$key2])){
		foreach ($this->mArrayExtension[$key2] as $entry){
		   array_push ($this->mArrayExtension[$key], $entry);
		}
	    }
        }
	return '';
    }    

    // extract a slice from an array
    // http://us3.php.net/manual/en/function.array-slice.php
    function arrayslice( &$parser, $key , $key1 , $offset, $length='') {
        if (!isset($key) || !isset($key1) || !isset($offset))
	   return '';
	   
        $this->mArrayExtension[$key] = array();
        if (isset($this->mArrayExtension)    
	     && array_key_exists($key1,$this->mArrayExtension) && is_array($this->mArrayExtension[$key1]) 
	     && !empty($offset) && is_numeric($offset)
	){
		if (!empty($length) && is_numeric($length)){
			$temp = array_slice($this->mArrayExtension[$key1], $offset, $length);
		}else{
			$temp = array_slice($this->mArrayExtension[$key1], $offset);		
		}
		if (!empty($temp) && is_array($temp))
		    $this->mArrayExtension[$key] = array_values($temp);
        }
	return '';
    }    

    //////////////////////////////////////////////////
    // SET OPERATIONS:    a set does not have duplicated element
    
    // merge two sets
    function arrayunion( &$parser, $key , $key1 , $key2 ) {
        if (!isset($key) ||!isset($key1) || !isset($key2))
	   return '';

        if (isset($this->mArrayExtension)    
	     && array_key_exists($key1,$this->mArrayExtension) && is_array($this->mArrayExtension[$key1])
	     && array_key_exists($key2,$this->mArrayExtension) && is_array($this->mArrayExtension[$key2]) 
	){
     	    $this->arraymerge($parser, $key, $key1, $key2);
	    $this->mArrayExtension[$key] = array_unique ($this->mArrayExtension[$key]);
        }
	return '';
    }    

    // intersect two sets
    function arrayintersect( &$parser, $key , $key1 , $key2 ) {
        if (!isset($key) ||!isset($key1) ||!isset($key2))
	   return '';

        if (isset($this->mArrayExtension)    
	     && array_key_exists($key1,$this->mArrayExtension) && is_array($this->mArrayExtension[$key1])
	     && array_key_exists($key2,$this->mArrayExtension) && is_array($this->mArrayExtension[$key2]) 
	){
     	    $this->mArrayExtension[$key] = array_intersect( array_unique($this->mArrayExtension[$key1]), array_unique($this->mArrayExtension[$key2]) );
        }
	return '';
    }    
    
    // diff  two sets, subset test
    function arraydiff( &$parser, $key , $key1 , $key2 ) {
        if (!isset($key) ||!isset($key1) ||!isset($key2))
	   return '';

        if (isset($this->mArrayExtension)    
	     && array_key_exists($key1,$this->mArrayExtension) && is_array($this->mArrayExtension[$key1])
	     && array_key_exists($key2,$this->mArrayExtension) && is_array($this->mArrayExtension[$key2]) 
	){
     	    $this->mArrayExtension[$key] = array_diff( array_unique($this->mArrayExtension[$key1]),array_unique($this->mArrayExtension[$key2]));
        }
	return '';
    }    



    
}
 
function wfSetupArrayExtension() {
    global $wgParser, $wgMessageCache, $wgArrayExtension, $wgMessageCache, $wgHooks;
 
    $wgArrayExtension = new ArrayExtension;
 
    $wgParser->setFunctionHook( 'arraydefine', array( &$wgArrayExtension, 'arraydefine' ) );

		if( defined( get_class( $wgParser) . '::SFH_OBJECT_ARGS' ) ) {
			//$parser->setFunctionHook('arraymap', array('SFParserFunctions', 'renderArrayMapObj'), SFH_OBJECT_ARGS);
			//$parser->setFunctionHook('arraymaptemplate', array('SFParserFunctions', 'renderArrayMapTemplateObj'), SFH_OBJECT_ARGS);
			$wgParser->setFunctionHook('arrayprint', array( &$wgArrayExtension, 'arrayprintObj' ), SFH_OBJECT_ARGS);
		} else {
			//$parser->setFunctionHook('arraymap', array('SFParserFunctions', 'renderArrayMap'));
	    $wgParser->setFunctionHook( 'arrayprint', array( &$wgArrayExtension, 'arrayprint' ) );
		}

//    $wgParser->setFunctionHook( 'arrayprint', array( &$wgArrayExtension, 'arrayprint' ) );
    $wgParser->setFunctionHook( 'arraysize', array( &$wgArrayExtension, 'arraysize' ) );
    $wgParser->setFunctionHook( 'arrayindex', array( &$wgArrayExtension, 'arrayindex' ) );
    $wgParser->setFunctionHook( 'arraysearch', array( &$wgArrayExtension, 'arraysearch' ) );

    $wgParser->setFunctionHook( 'arraysort', array( &$wgArrayExtension, 'arraysort' ) );
    $wgParser->setFunctionHook( 'arrayunique', array( &$wgArrayExtension, 'arrayunique' ) );
    $wgParser->setFunctionHook( 'arrayreset', array( &$wgArrayExtension, 'arrayreset' ) );

    $wgParser->setFunctionHook( 'arraymerge', array( &$wgArrayExtension, 'arraymerge' ) );
    $wgParser->setFunctionHook( 'arrayslice', array( &$wgArrayExtension, 'arrayslice' ) );

    $wgParser->setFunctionHook( 'arrayunion', array( &$wgArrayExtension, 'arrayunion' ) );
    $wgParser->setFunctionHook( 'arrayintersect', array( &$wgArrayExtension, 'arrayintersect' ) );
    $wgParser->setFunctionHook( 'arraydiff', array( &$wgArrayExtension, 'arraydiff' ) );
}
 
function wfArrayExtensionLanguageGetMagic( &$magicWords, $langCode ) {
        require_once( dirname( __FILE__ ) . '/ArrayExtension.i18n.php' );
        foreach( efArrayExtensionWords( $langCode ) as $word => $trans )
                $magicWords[$word] = $trans;
        return true;
}
 
?>