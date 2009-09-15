<?php
/*
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

/*
Jie Bao 2009-07-07

This is the setting file for the Semantic History extension. It defines a set of constants that
may be customized.

*/

date_default_timezone_set('UTC');

// name space numbers
define('NS_HISTORY' , 200);
define('NS_REVISION', 202);

// name space prefix
define('PREFIX_HISTORY'  , 'his');
define('PREFIX_REVISION' , 'rev');

// Template names (shoulde be defined in the wiki)
define('HISTORY_OBSOLETE'  , 'SH_Obsolete');
define('HISTORY_REV'       , 'SH_Rev');
define('HISTORY_TRIPLE'    , 'SH_Triple');
define('HISTORY_ADD'       , 'SH_Add');
define('HISTORY_DELETE'    , 'SH_Delete');
define('HISTORY_MINOR'     , 'SH_Minor');
define('HISTORY_SUMMARY'   , 'SH_Summary');
define('HISTORY_TEMPLATE'   , 'SH_UseTemplate');
define('HISTORY_PROPERTIES'   , 'SH_Properties');
define('HISTORY_CATEGORIES'   , 'SH_Categories');

// if common user can r/w history and revision data
$wgSemanticHistoryAllowEdit = false;
$wgSemanticHistoryAllowRead = false;

// if  to analyze the triple-template dependency (experimental)
$wgSemanticHistoryAnalyzeTemplate = true;

?>