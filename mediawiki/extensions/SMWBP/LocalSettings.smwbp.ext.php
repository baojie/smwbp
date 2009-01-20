<?php

#################################################################
# This file is generated for typical configuration on MW ans SWM extensions


## #######################################
## MW Extensions
## #######################################

## --- VariablesExtension ---
# Provides lots of new variables to use in wiki text.
# http://www.mediawiki.org/wiki/Extension:Variables
# svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/Variables/

include_once('extensions/Variables/Variables.php');



## --- StringFunctions ---
# Provides several string functions to modify for example query results.
# http://www.mediawiki.org/wiki/Extension:StringFunctions
# svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/StringFunctions/

include_once('extensions/StringFunctions/StringFunctions.php');



## --- ParserFunctions ---
# New Parser functions. For example switch, if, ifexist, ...
# http://www.mediawiki.org/wiki/Extension:ParserFunctions
# svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/ParserFunctions/

include_once('extensions/ParserFunctions/ParserFunctions.php');


## --- CategoryTree --- 
# browse ontological hierarchy of categories
# http://www.mediawiki.org/wiki/Extension:CategoryTree
# svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/CategoryTree/

$wgUseAjax = true;
require_once( 'extensions/CategoryTree/CategoryTree.php' );
$wgCategoryTreeMaxDepth = array(CT_MODE_PAGES => 5, CT_MODE_ALL => 5, CT_MODE_CATEGORIES => 10) ;

## --- Icon --- 
# display url with icon
# http://www.mediawiki.org/wiki/Extension:Icon
# svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/Icon/

require_once( "$IP/extensions/Icon/Icon.php" );




## #######################################
## SMW Extensions
## #######################################

## --- SemanticForms ---
# Enable editing semantic data by forms. This allows non-experienced people to use semantics.
# http://www.mediawiki.org/wiki/Extension:Semantic_Forms
# svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticForms/

$sfgNamespaceIndex = 150;
include_once('extensions/SemanticForms/includes/SF_Settings.php');


## --- Semantic Result Formats --- 
# render query result of SMW
# http://semantic-mediawiki.org/wiki/Semantic_Result_Formats
# svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticResultFormats/

require_once( 'extensions/SemanticResultFormats/SRF_Settings.php' );

?>