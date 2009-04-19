#!/bin/bash
###################################################
# usage
###################################################
if [ -z "$1" ]
then 
   echo "Example Usage"
   echo "./setup-smw.sh WIKI_DIR - install/update semantic mediawiki at the sub-directory named WIKI_DIR"
   exit
else
   # configure your installation path
   WIKI_DIR=$1
fi

###################################################
# Install Mediawiki (MW, 1.14)
###################################################
echo "Mediawiki 1.14"
if [ -d $WIKI_DIR ]
then
  echo "updating...";
  svn update $WIKI_DIR
else
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/branches/REL1_14/phase3  $WIKI_DIR
fi



###################################################
# Switch to extension directory
###################################################
cd $WIKI_DIR/extensions


###################################################
# Install Semantic MediaWiki (SMW)
###################################################
echo "Semantic MediaWiki"

EXT_WEBPATH="http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions"
EXT_NAME="SemanticMediaWiki"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi

# Semantic Result Format (only compatible with SMW 1.4, http://www.mediawiki.org/wiki/Extension:Semantic_Result_Formats)
echo "http://www.mediawiki.org/wiki/Extension:Semantic_Result_Formats"

EXT_WEBPATH="http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions"
EXT_NAME="SemanticResultFormats"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi


