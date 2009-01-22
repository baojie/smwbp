<?php

#################################################################
# This file is generated for typical configuration on MW ans SWM extensions


## #######################################
## MW Extensions
## #######################################


## #######################################
## SMW Extensions
## #######################################

## --- Semantic Drill Down --- 
# a page for drilling down into the category-based and semantic data of a site, using easily-created filters.
# http://www.mediawiki.org/wiki/Extension:Semantic_Drilldown
# svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticDrilldown/

require_once( 'extensions/SemanticDrilldown/includes/SD_Settings.php' );

## --- TetherlessMap--- 
# adding map with SMW
#  http://www.mediawiki.org/wiki/Extension:Tetherless_Map
#  svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/TetherlessMap/

# you need to configure the following two lines of code in LocalSettings.php
#$wgGoogleMapsKey = "......"; # enter your Google Maps API key here
#$wgLocalPath= "......";  #enter your host server address

require_once ("$IP/extensions/TetherlessMap/Individual_Location.php");
require_once ("$IP/extensions/TetherlessMap/GoogleMapClick.php");
require_once ("$IP/extensions/TetherlessMap/GoogleMapMultiObjects.php");



## --- AskMany --- 
# distributed query on SMW
# http://tw.rpi.edu/wiki/Help:AskMany
#   svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/AskMany/

require_once('extensions/AskManyExtension/includes/SMW_AM_Main.php');
enableAskMany();

?>