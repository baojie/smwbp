#!/bin/bash
###################################################
# usage
###################################################
if [ -z "$1" ]
then 
   echo "This script should not be called directly, please use setup scripts"
   exit
else
   # configure your installation path
   WIKI_DIR=$1
fi




###################################################
# Switch to extension directory
###################################################
cd $WIKI_DIR/extensions



###################################################
# Install SMW Extensions
###################################################
# Semantic Forms  http://www.mediawiki.org/wiki/Extension:Semantic_Forms
echo "http://www.mediawiki.org/wiki/Extension:Semantic_Forms"

EXT_WEBPATH="http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions"
EXT_NAME="SemanticForms"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi


###################################################
# Experimental install

# Semantic Drilldown (http://www.mediawiki.org/wiki/Extension:Semantic_Drilldown)
echo "http://www.mediawiki.org/wiki/Extension:Semantic_Drilldown"

EXT_WEBPATH="http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions"
EXT_NAME="SemanticDrilldown"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi



# AskManyExtension (http://tw.rpi.edu/wiki/Help:AskMany)
echo "AskMany"

EXT_WEBPATH="http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions"
EXT_NAME="AskManyExtension"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi



# TetherlessMap (http://www.mediawiki.org/wiki/Extension:Tetherless_Map)
echo "TetherlessMap"

EXT_WEBPATH="http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions"
EXT_NAME="TetherlessMap"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi


# Ratings (http://www.mediawiki.org/wiki/Extension:Ratings)
echo "Ratings"

EXT_WEBPATH="http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions"
EXT_NAME="Ratings"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi
