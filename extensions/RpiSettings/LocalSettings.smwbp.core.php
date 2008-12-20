<?php

#################################################################
# This file is generated the basic configuration for Wiki and SMW 

############################
## MediaWiki (MW)
#   svn checkout http://svn.wikimedia.org/svnroot/mediawiki/branches/REL1_13/phase3
## #######################################

# disallow anonymous edit
$wgGroupPermissions['*']['edit'] = false;  # restrict anonymous edit



## #######################################
## Semantic MediaWiki (SMW) 
# http://semantic-mediawiki.org/wiki/Help:Installation
# svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticMediaWiki/
## #######################################

# include SMW
include_once('extensions/SemanticMediaWiki/includes/SMW_Settings.php');
enableSemantics('example.org');


# export triples which have class as object
$smwgOWLFullExport = TRUE;
$smwgShowFactbox = SMW_FACTBOX_NONEMPTY;   # show factbox
$smwgAllowRecursiveExport=true;



?>
