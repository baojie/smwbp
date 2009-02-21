<?php

#################################################################
# This file is generated the basic configuration for Wiki and SMW 

## #######################################
## Semantic MediaWiki (SMW) 
# http://semantic-mediawiki.org/wiki/Help:Installation
# svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticMediaWiki/
## #######################################

# MW 1.14 did set this value to 20M, but that is not enough
ini_set( 'memory_limit', '128M' );

# include SMW
include_once('extensions/SemanticMediaWiki/includes/SMW_Settings.php');
#enableSemantics('example.org');



# SMW configurations
# see also http://semantic-mediawiki.org/wiki/Help:Configuration
$smwgOWLFullExport = TRUE;   # export triples which have class as object
$smwgShowFactbox = SMW_FACTBOX_NONEMPTY;   # show factbox
$smwgAllowRecursiveExport=true;  # for better RDF dump
$smwgToolboxBrowseLink=false;  # it is already included in the navigation penal of side bar

# enables a status bar in  SMW Admin page (Data repair and upgrade section )
$phpInterpreter = "/usr/bin/php";

?>
