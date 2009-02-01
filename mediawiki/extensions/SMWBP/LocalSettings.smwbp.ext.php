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

$extension='$IP/extensions/Icon/Icon.php';
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

   
## ---WikiWidgets ---
# Provides lots of new variables to use in wiki text.
# http://www.mediawiki.org/wiki/Extension:WikiWidgets
# http://hexten.net/wiki/index.php/Installed_Widgets
# svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/wx/
$extension='extensions/wx/widget.php';
if (file_exists($extension))
   require_once($extension);

## --- EmbedVideo ---
# Provides lots of new variables to use in wiki text.
# http://www.mediawiki.org/wiki/Extension:EmbedVideo
# svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/EmbedVideo/
$extension='extensions/EmbedVideo/EmbedVideo.php';
if (file_exists($extension))
   require_once($extension);

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

?>