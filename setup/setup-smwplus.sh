#!/bin/bash
###################################################
# usage
###################################################
if [ -z "$1" ]
then 
   echo "Example Usage"
   echo "setup-smwplus install WIKI_DIR - install smw+ at the subdirectory named WIKI_DIR"
   echo "setup-smwplus update WIKI_DIR - install smw+ at the subdirectory named WIKI_DIR"
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
./setup-smw.sh "$1" "$2"


###################################################
# Switch to wiki directory
###################################################
cd $WIKI_DIR


###################################################
# Install SMW Plus (1.4)
###################################################
if [ $1 = "install" ]
then
  wget http://downloads.sourceforge.net/halo-extension/smwplus-1.4.zip
  unzip smwplus-1.4.zip
  rm smwplus-1.4.zip
fi


###################################################
# Switch to extension directory
###################################################
cd extensions

###################################################
# Install SMW extensions
###################################################

# Semantic Drilldown (http://www.mediawiki.org/wiki/Extension:Semantic_Drilldown)
if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticDrilldown/
fi

if [ $1 = "update" ]
then
  svn update SemanticDrilldown
fi
