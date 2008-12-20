<?php

#################################################################
# This file is generated for installing SMW+ (halo extension)
# see http://tw.rpi.edu/wiki/LocalSettings.ext.php

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
#enableSemantics('example.org');
enableSemantics('http://onto.rpi.edu', true); 

# SMW options
$smwgOWLFullExport = TRUE;   # export triples which have class as object
$smwgShowFactbox = SMW_FACTBOX_NONEMPTY;   # show factbox
$smwgAllowRecursiveExport=true;


## #######################################
## SMW+
## #######################################

$wgDefaultSkin='ontoskin';

$wgUseAjax = true; //MUST
include_once('extensions/SMWHalo/includes/SMW_Initialize.php');
enableSMWHalo();

$phpInterpreter = "/usr/local/bin/php";

#enableSMWHalo('SMWHaloStore2','SMWTripleStore');
#$smwgMessageBroker = "localhost";
#$smwgWebserviceEndpoint = "localhost:9876"; 




