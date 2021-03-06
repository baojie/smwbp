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
$extension='extensions/Variables/Variables.php';
if (file_exists($extension))
   require_once($extension);



## --- StringFunctions ---
# Provides several string functions to modify for example query results.
# http://www.mediawiki.org/wiki/Extension:StringFunctions
# svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/StringFunctions/
$extension='extensions/StringFunctions/StringFunctions.php';
if (file_exists($extension))
   require_once($extension);



## --- ParserFunctions ---
# New Parser functions. For example switch, if, ifexist, ...
# http://www.mediawiki.org/wiki/Extension:ParserFunctions
# svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/ParserFunctions/

$extension='extensions/ParserFunctions/ParserFunctions.php';
if (file_exists($extension))
   require_once($extension);


## --- CategoryTree --- 
# browse ontological hierarchy of categories
# http://www.mediawiki.org/wiki/Extension:CategoryTree
# svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/CategoryTree/

$extension='extensions/CategoryTree/CategoryTree.php';
if (file_exists($extension)){
   $wgUseAjax = true;
   require_once($extension);
   $wgCategoryTreeMaxDepth = array(CT_MODE_PAGES => 5, CT_MODE_ALL => 5, CT_MODE_CATEGORIES => 10) ;
}

## --- Icon --- 
# display url with icon
# http://www.mediawiki.org/wiki/Extension:Icon
# svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/Icon/

$extension='extensions/Icon/Icon.php';
if (file_exists($extension))
   require_once($extension);

## --- ArrayExtension ---
# Provides array variables in wiki
# http://www.mediawiki.org/wiki/Extension:ArrayExtension
# svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/ArrayExtension/

$extension='extensions/ArrayExtension/ArrayExtension.php';
if (file_exists($extension))
   require_once($extension);

## --- LoopFunctions ---
# Provides loop function
# http://www.mediawiki.org/wiki/Extension:Loops
# svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/Loops/

$extension='extensions/Loops/Loops.php';
if (file_exists($extension))
   require_once($extension);

   
## --- Widgets --- 
# Widget extension for MW
#  http://www.mediawiki.org/wiki/Extension:Widgets
#   svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/Widgets/

$extension='extensions/Widgets/Widgets.php';
if (file_exists($extension)){
   require_once($extension);
   $wgGroupPermissions['sysop']['editwidgets'] = true;
}

## --- FCKeditor --- 
# Widget extension for MW
#  http://www.mediawiki.org/wiki/Extension:FCKeditor_(Official)
#   svn checkout http://smwbp.googlecode.com/svn/trunk/release/FCKeditor_nightly/FCKeditor

$extension='extensions/FCKeditor/FCKeditor.php';
if (file_exists($extension)){
   require_once($extension);
}

## #######################################
## SMW Extensions
## #######################################

## --- SemanticForms ---
# Enable editing semantic data by forms. This allows non-experienced people to use semantics.
# http://www.mediawiki.org/wiki/Extension:Semantic_Forms
# svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticForms/

$extension='extensions/SemanticForms/includes/SF_Settings.php';
if (file_exists($extension)){
   $sfgNamespaceIndex = 150;
   require_once($extension);
}

## --- Semantic Result Formats --- 
# render query result of SMW
# http://semantic-mediawiki.org/wiki/Semantic_Result_Formats
# svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticResultFormats/

$extension='extensions/SemanticResultFormats/SRF_Settings.php';
if (file_exists($extension))
   require_once($extension);

## --- Semantic Drill Down --- 
# a page for drilling down into the category-based and semantic data of a site, using easily-created filters.
# http://www.mediawiki.org/wiki/Extension:Semantic_Drilldown
# svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticDrilldown/
$extension='extensions/SemanticDrilldown/includes/SD_Settings.php';
if (file_exists($extension))
   require_once($extension);

## --- External Data --- 
# a page for drilling down into the category-based and semantic data of a site, using easily-created filters.
# http://www.mediawiki.org/wiki/Extension:External_Data
# svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/ExternalData/
$extension='extensions/ExternalData/ED_Settings.php';
if (file_exists($extension))
   require_once($extension);
   
   
## --- Semantic Toolkit--- 
# tools for enhancing user experiences with Semantic Wiki
# svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/SemanticToolkit/

$extension='extensions/SemanticToolkit/SemanticToolkit.php';
if (file_exists($extension))
   require_once($extension);
   
   
?>