<?php

#################################################################
# This file is generated for typical configuration on MW ans SWM extensions


## #######################################
## MW Extensions
## #######################################

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
   

## --- Ratings --- 
# ratings component for MW
#  http://www.mediawiki.org/wiki/Extension:Ratings
#   svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/Ratings/

$extension='extensions/Ratings/Ratings.php';
if (file_exists($extension)){
   require_once($extension);
}   



   
## #######################################
## SMW Extensions
## #######################################



## --- TetherlessMap--- 
# adding map with SMW
#  http://www.mediawiki.org/wiki/Extension:Tetherless_Map
#  svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/TetherlessMap/

# you need to configure the following two lines of code in LocalSettings.php
#$wgGoogleMapsKey = "......"; # enter your Google Maps API key here
#$wgLocalPath= "......";  #enter your host server address

$extension='extensions/TetherlessMap/Individual_Location.php';
if (file_exists($extension)){
   require_once ("$IP/extensions/TetherlessMap/Individual_Location.php");
   require_once ("$IP/extensions/TetherlessMap/GoogleMapClick.php");
   require_once ("$IP/extensions/TetherlessMap/GoogleMapMultiObjects.php");
}




## --- AskMany --- 
# distributed query on SMW
# http://tw.rpi.edu/wiki/Help:AskMany
#   svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/AskMany/

$extension='extensions/AskManyExtension/includes/SMW_AM_Main.php';
if (file_exists($extension)){
   require_once($extension);
   enableAskMany();
}



?>