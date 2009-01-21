#!/bin/bash
###################################################
# usage
###################################################
if [ -z "$1" ]
then 
   echo "Example Usage"
   echo "setup-smwrpi install WIKI_DIR - install RPI extensions at the subdirectory named WIKI_DIR"
   echo "setup-smwrpi update WIKI_DIR - install RPI extensions at the subdirectory named WIKI_DIR"
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
# call the other install
###################################################
./setup-smwplus.sh "$1" "$2"


###################################################
# Switch to extension directory
###################################################
cd $WIKI_DIR/extensions


###################################################
# MW Extensions
###################################################


###################################################
# SMW Extensions
###################################################


# TetherlessMap (http://www.mediawiki.org/wiki/Extension:Tetherless_Map)
if [ $1 = "install" ]
then
  svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/TetherlessMap/
fi

if [ $1 = "update" ]
then
  svn update TetherlessMap
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


# MWSearch (http://www.mediawiki.org/wiki/Extension:MWSearch)
if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/MWSearch/
fi

if [ $1 = "update" ]
then
  svn update MWSearch
fi


# Widgets (http://www.mediawiki.org/wiki/Extension:Widgets) 
# for embedding video and etc.
if [ $1 = "install" ]
then
  svn checkout http://mediawiki-widgets.googlecode.com/svn/trunk/ Widgets
fi

if [ $1 = "update" ]
then
  svn update Widgets
fi
