<?php
/*
 Defines a subset of parser functions that operate with arrays.
 verion: 1.1.5
 authors: Li Ding (lidingpku@gmail.com) and Jie Bao
 update: 17 March 2009
 homepage: http://www.mediawiki.org/wiki/Extension:ArrayExtension
 
 changelog
 * Mar 17, 2009 version 1.1.5
   - update #arraysort, add "reverse" option, http://us3.php.net/manual/en/function.array-reverse.php
   - update #arrayreset, add option to reset a selection of arrays
 * Feb 23, 2009 version 1.1.4
   - fixed #arraysearch, better recognize perl patterns identified by starting with "/", http://www.perl.com/doc/manual/html/pod/perlre.html
 * Feb 23, 2009 version 1.1.3
   - fixed #arraysearch, "Warning: Missing argument 4..."
 * Feb 9, 2009 version 1.1.2
    - update #arraysearch, now support offset and preg regular expression
 * Feb 8, 2009 version 1.1.1
    - update #arrayprint, now wiki links, parser functions and templates properly parsed. This enables foreach loop call.
    - update #arraysearch, now allows customized output upon found/non-found by specifying additional parameters
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
        'version' => '1.1.5',
);
 
$wgHooks['LanguageGetMagic'][]       = 'wfArrayExtensionLanguageGetMagic';
 
/**
   *  named arrays    - an array has a list of values, and could be set to a SET
 */ 
class ArrayExtension {
    var $mArrayExtension; 

///////////////////////////////////////////////////////////
// PART 1. constructor
///////////////////////////////////////////////////////////
 
/**
* Define an array by a list of 'values' deliminated by 'delimiter', 
* the delimiter should be perl regular expression pattern
*      {{#arraydefine:key|values|delimiter}}
*
* http://us2.php.net/manual/en/book.pcre.php
* see also: http://us2.php.net/manual/en/function.preg-split.php
*/
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

///////////////////////////////////////////////////////////
// PART 2. print
///////////////////////////////////////////////////////////

/**
* print an array.
* foreach element of the array, print 'subject' where  all occurrences of 'search' is replaced with the element, 
* and each element print-out is deliminated by 'delimiter'
* The subject can embed parser functions; wiki links; and templates.
* usage
*      {{#arrayprint:key|delimiter|search|subject}}
* examples:
*    {{#arrayprint:b}}    -- simple
*    {{#arrayprint:b|<br/>}}    -- add change line
*    {{#arrayprint:b|<br/>|@@@|[[@@@]]}}    -- embed wiki links
*    {{#arrayprint:b|<br/>|@@@|{{#set:prop=@@@}} }}   -- embed parser function
*    {{#arrayprint:b|<br/>|@@@|{{f.tag{{f.print.vbar}}prop{{f.print.vbar}}@@@}} }}   -- embed template function
*    {{#arrayprint:b|<br/>|@@@|[[name::@@@]]}}   -- make SMW links
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

/**
* print the value of an array (identified by key)  by the index, invalid index result in nothing being printed. note the index is 0-based.
* usage
*   {{#arrayindex:key|index}}
*/
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
   
/**
* return size of array.
* Print the size (number of elements) in the specified array
* usage
*   {{#arraysize:key}}
*
*  See: http://www.php.net/manual/en/function.count.php
*/
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

/**
* locate the index of the first occurence of an element starting from the 'offset'
*   - print "-1" or index to show the index of the first occurence of 'value' in the array identified by key
*    - if 'yes' and 'no' are set, print value of them when found or not-found
* usage
*   {{#arraysearch:key|value|offset|yes|no}}
*
*   See: http://www.php.net/manual/en/function.array-search.php
*   note it is extended to support regular expression match and offset
*/   
    function arraysearch( &$parser, $key, $needle, $offset=null, $yes=null, $no=null) {
        if (!isset($key) || !isset($needle) || strlen($needle)===0 )
	   return '';
        if (isset($offset)){
		if (!is_numeric($offset) || $offset<0 || $offset>=count($this->mArrayExtension[$key]))
			return ''; //bad offset
	}else{
		$offset=0;
	}

        if (isset($this->mArrayExtension)    
	    && array_key_exists($key,$this->mArrayExtension) && is_array($this->mArrayExtension[$key]))
	{
	//TODO we need a better way to decide perl expresion.
	    $bIsPreg= (0===strpos($needle,'/') );
	    $ret = false;
	    for ($i=$offset; $i< count($this->mArrayExtension[$key]) ;$i++){
	        $value = $this->mArrayExtension[$key][$i];
	        if ($bIsPreg){
			// check if the needle is preg regular expression (require '/.../')
			if (preg_match($needle, $value)){
			   $ret = $i;
			   break;
			}   
		}else{
			if (strcmp($needle, $value)===0){
			   $ret = $i;
			   break;
			}   
		}	
	    }
	    
	    if (false !== $ret ){
	       if (isset($yes))
		  $ret=$yes;
	       return $ret;	       
	    }
	    
        }
	$ret = -1;
        if (isset($no))
	  $ret=$no;
	return $ret;
    }        
   

   
///////////////////////////////////////////////////////////
// PART 3. alter an array   
///////////////////////////////////////////////////////////
    
/**
* reset some or all defined arrayes
* usage
*    {{#arrayreset:}}
*    {{#arrayreset:key1,key2,...keyn}}
*/
   function arrayreset( &$parser, $keys) {
        if (empty($keys)){
	    //reset all
	    $this->mArrayExtension = array();
	}else{
	    $arykeys = explode(',', $keys);
	    foreach ($arykeys as $key){
		$key = trim($key);
		if ( array_key_exists($key,$this->mArrayExtension) && is_array($this->mArrayExtension[$key]) ){
		    unset($this->mArrayExtension[$key]);
		}
	    }
	}
	return '';
    }    
  
    
/**
* convert an array to set
* convert the array identified by key into a set (all elements are unique)
* usage
*   {{#arrayunique:key}}
*
*   see: http://www.php.net/manual/en/function.array-unique.php
*/
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

/**
* sort an array 
*   sort specified array in the following order:
*   * none (default)  - no sort   
*   *  desc - in descending order, large to small
*   *  asce - in ascending order, small to large
*   * random - shuffle the arrry in random order
*   * reverse - Return an array with elements in reverse order
* usage
*   {{#arraysort:key|order}}
*   
*   see: http://www.php.net/manual/en/function.sort.php
*          http://www.php.net/manual/en/function.rsort.php
*          http://www.php.net/manual/en/function.shuffle.php
*          http://us3.php.net/manual/en/function.array-reverse.php
*/ 
    function arraysort( &$parser, $key , $sort = 'none') {
        if (!isset($key))
	   return '';
        if (isset($this->mArrayExtension)    
	       && array_key_exists($key,$this->mArrayExtension) && is_array($this->mArrayExtension[$key]))
	{
	    switch ($sort){	
		case 'asce': 
		case 'ascending': sort($this->mArrayExtension[$key]); break;

		case 'desc': 
		case 'descending': rsort($this->mArrayExtension[$key]); break;
		
		case 'random': shuffle($this->mArrayExtension[$key]); break;

		case 'reverse': $this->mArrayExtension[$key]= array_reverse($this->mArrayExtension[$key]); break;		
	    };
        }
	return '';
    }    
    
///////////////////////////////////////////////////////////
// PART 4. create an array   
///////////////////////////////////////////////////////////
/**
* merge two arrays,  keep duplicated values 
* usage
*   {{#arraymerge:key|key1|key2}}
*   
*  merge values two arrayes identified by key1 and key2 into a new array identified by key.
*  this merge differs from array_merge of php because it merges values.
*/   
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

/**
* extract a slice from an array
* usage
*     {{#arrayslice:key|key1|offset|length}}
*
*    extract a slice from an  array
*    see: http://www.php.net/manual/en/function.array-slice.php
*/   
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
    
/**
*  set operation,    {red} = {red, white} intersect {red,black}
* usage
*    {{#arrayintersect:key|key1|key2}}
*   See: http://www.php.net/manual/en/function.array-intersect.php
*/ 
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
    

/**
*    set operation,    {red, white} = {red, white} union {red}
* usage
*    {{#arrayunion:key|key1|key2}}
    
*    similar to arraymerge, this union works on values.
*/ 
    
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
/**
*
* usage
*    {{#arraydiff:key|key1|key2}}
    
*    set operation,    {white} = {red, white}  -  {red}
*    see: http://www.php.net/manual/en/function.array-diff.php
*/        

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
	$wgParser->setFunctionHook('arrayprint', array( &$wgArrayExtension, 'arrayprintObj' ), SFH_OBJECT_ARGS);
    } else {
	$wgParser->setFunctionHook( 'arrayprint', array( &$wgArrayExtension, 'arrayprint' ) );
    }

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