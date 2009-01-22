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
# Install Mediawiki (MW, 1.13)
###################################################
./setup-mw.sh "$1" "$2"


###################################################
# Switch to extension directory
###################################################
cd $WIKI_DIR/extensions


###################################################
# Install Semantic MediaWiki (SMW, 1.4)
###################################################
if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticMediaWiki/
fi

if [ $1 = "update" ]
then
  svn update SemanticMediaWiki
fi


###################################################
# Install SMW Extensions
###################################################
# Semantic Forms 
if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticForms/
fi

if [ $1 = "update" ]
then
  svn update SemanticForms
fi

# Semantic Result Format (only compatible with SMW 1.4)
if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticResultFormats/
fi

if [ $1 = "update" ]
then
  svn update SemanticResultFormats
fi

###################################################
# Experimental install

# Semantic Drilldown (http://www.mediawiki.org/wiki/Extension:Semantic_Drilldown)
if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticDrilldown/
fi

if [ $1 = "update" ]
then
  svn update SemanticDrilldown
fi


# AskManyExtension (http://tw.rpi.edu/wiki/Help:AskMany)
if [ $1 = "install" ]
then
  svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/AskManyExtension/
fi

if [ $1 = "update" ]
then
  svn update AskManyExtension
fi

# TetherlessMap (http://www.mediawiki.org/wiki/Extension:Tetherless_Map)
if [ $1 = "install" ]
then
  svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/TetherlessMap/
fi

if [ $1 = "update" ]
then
  svn update TetherlessMap
fi


###################################################
# Install MW and SMW Configuration Settings
###################################################
if [ $1 = "install" ]
then
  svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/SMWBP/
fi

if [ $1 = "update" ]
then
  svn update SMWBP
fi