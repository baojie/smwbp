#!/bin/bash
###################################################
# usage
###################################################
if [ -z "$1" ]
then 
   echo "Example Usage"
   echo "setup-smw install WIKI_DIR - install smw at the subdirectory named WIKI_DIR"
   echo "setup-smw update WIKI_DIR - update smw at the subdirectory named WIKI_DIR"
   echo "by default, WIKI_DIR is mw13"
   exit
fi

###################################################
# configuration
###################################################
# configure your installation path
if [ -z "$2" ]
then 
   WIKI_DIR=mw13
else
   WIKI_DIR=$2
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

if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticForms/
fi

if [ $1 = "update" ]
then
  svn update SemanticForms
fi



###################################################
# Experimental install

# Semantic Drilldown (http://www.mediawiki.org/wiki/Extension:Semantic_Drilldown)
echo "http://www.mediawiki.org/wiki/Extension:Semantic_Drilldown"

if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticDrilldown/
fi

if [ $1 = "update" ]
then
  svn update SemanticDrilldown
fi


# AskManyExtension (http://tw.rpi.edu/wiki/Help:AskMany)
echo "AskMany"

if [ $1 = "install" ]
then
  svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/AskManyExtension/
fi

if [ $1 = "update" ]
then
  svn update AskManyExtension
fi

# TetherlessMap (http://www.mediawiki.org/wiki/Extension:Tetherless_Map)
echo "TetherlessMap"

if [ $1 = "install" ]
then
  svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/TetherlessMap/
fi

if [ $1 = "update" ]
then
  svn update TetherlessMap
fi