<?php
/*
 Defines a subset of parser functions that operate with arrays.
 verion: 1.0
 authors: Li Ding (lidingpku@gmail.com) and Jie Bao
 update: 27 Janunary 2009
 homepage: http://www.mediawiki.org/wiki/Extension:ArrayExtension
 
 
 == Part1. constructor ==
 {{#arraydefine:key|values|delimiter}}

 Define an array by a list of 'values' deliminated by 'delimiter'
 
 
 
 == Part2. print functions ==

 {{#arrayprint:key|delimiter|prefix|suffix}}

 Print the values of an array in the following format:
 
 prefix <value_1> suffix  delimiter prefix <value_2> suffix ... prefix <value_n> suffix 
 
 notes:
  1. the following characters are escaped
  [   => \[
  {   => \{
  }   => \}
  \  => \\
 2. there should be at least a white space between  escaped  '\}'   and the end of template '}}'
 
 examples:
 {{#arrayprint:b}}    -- simple
  {{#arrayprint:b|<br/>}}    -- add change line
  {{#arrayprint:b|<br/>|\[\[|]]}}    -- make links
  {{#arrayprint:b|<br/>|\{\{#set:a=|\}\} }}   -- make templates
 {{#arrayprint:b|<br/>|\[\[name::|]]}}   -- make SMW links
 
 
 

 == Part3. basic array functions ==
   {{#arraysize:key}}

   Print the size (number of elements) in the specified array
  See: http://www.php.net/manual/en/function.count.php
   
   
   
   {{#arraymember:key|value}}

   check if value is a member of the array identified by key, returns "yes", "no"
   See: http://www.php.net/manual/en/function.in-array.php
   
   
   
   
   {{#arraymerge:key|key1|key2}}
   
   merge values two arrayes identified by key1 and key2 into a new array identified by key.
   this merge differs from array_merge of php because it merges values.
   
   
   
   {{#arraysort:key|order}}
   
   sort specified array in the following order:
    * none (default)  - no sort   
   *  desc - in descending order, large to small
   *  asce - in ascending order, small to large
   see: http://www.php.net/manual/en/function.sort.php
          http://www.php.net/manual/en/function.rsort.php
   
   
   {{#arrayunique:key}}

   make the array identified by key a set (all elements are unique)
   see: http://www.php.net/manual/en/function.array-unique.php
   
   
   
    == Part 4.  set operations ==
    
    {{#arrayintersect:key|key1|key2}}
    
    set operation,    {red} = {red, white} intersect {red,black}
   See: http://www.php.net/manual/en/function.array-intersect.php


   
    {{#arrayunion:key|key1|key2}}
    
    set operation,    {red, white} = {red, white} union {red}
    similar to arraymerge, this union works on values.

    
    
    {{#arraydiff:key|key1|key2}}
    
    set operation,    {white} = {red, white}  -  {red}
    see: http://www.php.net/manual/en/function.array-diff.php
    
    == Part 5.  stack operations ==
    {{#arraypush:key|values|delimiter}}

    push a set of values to the array identified by key
    see: http://www.php.net/manual/en/function.array-push.php
    
    {{#arraypop:key}}

    pop the last meber of the array identified by key
    see: http://www.php.net/manual/en/function.array-pop.php
    
    
    
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
);
 
$wgHooks['LanguageGetMagic'][]       = 'wfArrayExtensionLanguageGetMagic';
 
/**
   *  named arrays    - an array has a list of values, and could be set to a SET
 */ 
class ArrayExtension {
    var $mArrayExtension; 
 
    // define an array variable
    function arraydefine( &$parser, $key = '', $value = '' , $delimiter = ',', $option = 'list', $sort = 'none') {
        //normalize 
	$value = preg_replace('/\s*'.$delimiter.'\s*/', $delimiter, $value);
	if ($this->mArrayExtension[$key] = explode ($delimiter, $value)){
	      switch ($option){	
		case 'unique': $this->arrayunique($parser, $key); break;
	      };
	      $this->arraysort($parser, $key, $sort);
	    
	}else{
	      $this->mArrayExtension[$key] = array( $value );
	}
	
	return '';  
    }


    //////////////////////////////////////////////////
    // Display Options:  print array
    
    function arrayprint( &$parser, $key = '', $delimiter = ', ', $prefix='', $suffix='') {
        $option='values';
        if ($key =='')
	   $option='keys';

        switch ($option){
	case "values":
		if (isset($this->mArrayExtension)
                    && array_key_exists($key,$this->mArrayExtension) && is_array($this->mArrayExtension[$key])
                ){
			//unescape prefix and suffix
			
		        if (!empty($prefix)){
				$prefix = str_replace('\\[','[',$prefix);
				$prefix = str_replace('\\{','{',$prefix);
				$prefix = str_replace('\\}','}',$prefix);
				$prefix = str_replace('\\\\','\\',$prefix);
			}	
		        if (!empty($suffix)){
				$suffix = str_replace('\\[','[',$suffix);
				$suffix = str_replace('\\{','{',$suffix);
				$suffix = str_replace('\\}','}',$suffix);
				$suffix = str_replace('\\\\','\\',$suffix);
			}	
			
			// print the entire key
		        return "$prefix". implode( "$suffix$delimiter$prefix", $this->mArrayExtension[$key] ) ."$suffix";
		}else{
			return "undefined array: $key";
		}
		break;
	case "keys":
	        if (is_array($this->mArrayExtension)) {
		        return "$prefix". implode( "$suffix$delimiter$prefix", $this->mArrayExtension ) ."$suffix";
		}else{
		   return '';
		}
		break;
       }
    }
   

   
    //////////////////////////////////////////////////
    // ARRAY OPERATIONS:   
    
    // return size of array
    function arraysize( &$parser, $key = '') {
       if (isset($this->mArrayExtension)    
            && array_key_exists($key,$this->mArrayExtension) && is_array($this->mArrayExtension[$key])) 
	{
          return count ($this->mArrayExtension[$key]);
	}
       return '';
    }    

    // convert an array to set
    function arrayunique( &$parser, $key = '') {
        if (isset($this->mArrayExtension)   
              && array_key_exists($key,$this->mArrayExtension) && is_array($this->mArrayExtension[$key]))
	{
     	    $this->mArrayExtension[$key] = array_unique ($this->mArrayExtension[$key]);
        }
	return '';
    }    

    // sort an array 
    function arraysort( &$parser, $key = '', $sort = 'none') {
        if (isset($this->mArrayExtension)    
	       && array_key_exists($key,$this->mArrayExtension) && is_array($this->mArrayExtension[$key]))
	{
	    switch ($sort){	
		case 'asce': sort($this->mArrayExtension[$key]); break;
		case 'ascending': sort($this->mArrayExtension[$key]); break;
		
		case 'desc': rsort($this->mArrayExtension[$key]); break;
		case 'descending': rsort($this->mArrayExtension[$key]); break;
	    };
        }
	return '';
    }    
    
    
    // merge two arrays,  keep duplicated values 
    function arraymerge( &$parser, $key = '', $key1 = '', $key2 = '') {
        if (isset($this->mArrayExtension)    
	     && array_key_exists($key1,$this->mArrayExtension) && is_array($this->mArrayExtension[$key1]) 
	){
	    $this->mArrayExtension[$key] = array();
	    foreach ($this->mArrayExtension[$key1] as $entry){
		   array_push ($this->mArrayExtension[$key], $entry);
	    }

	    if ( array_key_exists($key2,$this->mArrayExtension) && is_array($this->mArrayExtension[$key2])){
		foreach ($this->mArrayExtension[$key2] as $entry){
		   array_push ($this->mArrayExtension[$key], $entry);
		}
	    }
        }
	return '';
    }    
    // membership test, check if a value is member of a set
    function arraymember( &$parser, $key = '', $needle = '') {
        if (isset($this->mArrayExtension) &&  !empty($needle)    
	    && array_key_exists($key,$this->mArrayExtension) && is_array($this->mArrayExtension[$key]))
	{
	    if ($ret = in_array ($needle, $this->mArrayExtension[$key]))
	       return 'yes';
	       
        }
	return 'no';
    }    

    
    
    // append element(s)  to the end of an array
    function arraypush( &$parser, $key = '', $value = '', $delimiter = ',') {
        if (isset($this->mArrayExtension) ){
	   if (array_key_exists($key,$this->mArrayExtension) && is_array($this->mArrayExtension[$key])){
	        $temp = explode ($delimiter, $value);
		if (isset($temp) && is_array($temp)){
		    $this->mArrayExtension[$key] = array_merge($this->mArrayExtension[$key], $temp);
		}else{
		    array_push($this->mArrayExtension[$key],$value);
		}
	   }else{
		$this->arraydefine( $parser, $key , $value, $delimiter );
	   }
        }
	return '';
    }    
    
    // remove an element from the end of an array
    function arraypop( &$parser, $key = '' ) {
        if (isset($this->mArrayExtension)    
	    && array_key_exists($key,$this->mArrayExtension) && is_array($this->mArrayExtension[$key]))
	{
	    array_pop ($this->mArrayExtension[$key]);
        }
	return '';
    }    


    //////////////////////////////////////////////////
    // SET OPERATIONS:    a set does not have duplicated element
    
    // merge two sets
    function arrayunion( &$parser, $key = '', $key1 = '', $key2 = '') {
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
    function arrayintersect( &$parser, $key = '', $key1 = '', $key2 = '') {
        if (isset($this->mArrayExtension)    
	     && array_key_exists($key1,$this->mArrayExtension) && is_array($this->mArrayExtension[$key1])
	     && array_key_exists($key2,$this->mArrayExtension) && is_array($this->mArrayExtension[$key2]) 
	){
     	    $this->mArrayExtension[$key] = array_intersect( array_unique($this->mArrayExtension[$key1]), array_unique($this->mArrayExtension[$key2]) );
        }
	return '';
    }    
    
    // diff  two sets, subset test
    function arraydiff( &$parser, $key = '', $key1 = '', $key2 = '') {
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

    $wgParser->setFunctionHook( 'arraysize', array( &$wgArrayExtension, 'arraysize' ) );
    $wgParser->setFunctionHook( 'arrayprint', array( &$wgArrayExtension, 'arrayprint' ) );
    $wgParser->setFunctionHook( 'arraymember', array( &$wgArrayExtension, 'arraymember' ) );

    $wgParser->setFunctionHook( 'arrayunique', array( &$wgArrayExtension, 'arrayunique' ) );
    $wgParser->setFunctionHook( 'arraysort', array( &$wgArrayExtension, 'arraysort' ) );
    $wgParser->setFunctionHook( 'arraymerge', array( &$wgArrayExtension, 'arraymerge' ) );

    $wgParser->setFunctionHook( 'arraypush', array( &$wgArrayExtension, 'arraypush' ) );
    $wgParser->setFunctionHook( 'arraypop', array( &$wgArrayExtension, 'arraypop' ) );

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