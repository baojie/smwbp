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
# Install SMW extensions
###################################################
./inc-ext-smw.sh "$1" "$2"


###################################################
# Switch to extension directory
###################################################
cd $WIKI_DIR/extensions


###################################################
# Install Semantic MediaWiki (SMW)
###################################################
echo "Semantic MediaWiki"

if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticMediaWiki/
fi

if [ $1 = "update" ]
then
  svn update SemanticMediaWiki
fi

# Semantic Result Format (only compatible with SMW 1.4, http://www.mediawiki.org/wiki/Extension:Semantic_Result_Formats)
echo "http://www.mediawiki.org/wiki/Extension:Semantic_Result_Formats"

if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticResultFormats/
fi

if [ $1 = "update" ]
then
  svn update SemanticResultFormats
fi