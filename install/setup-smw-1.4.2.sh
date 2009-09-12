#!/bin/bash
###################################################
# usage
###################################################
if [ -z "$1" ]
then 
   echo "Example Usage"
   echo "./setup-smw-1.4.2.sh WIKI_DIR     - install/update halo extension swm+ at the sub-directory named WIKI_DIR"
   exit
else
   # configure your installation path
   WIKI_DIR=$1
fi

###################################################
# Install Mediawiki (MW)
###################################################
./setup-mw.sh "$1" "14"


###################################################
# Switch to wiki directory
###################################################
cd $WIKI_DIR


###################################################
# Install SMW Plus (1.4.2)
###################################################
echo "SMW+ 1.4.2"

if [ -d "extensions/SMWHalo" ]
then
  echo "smwplus 1.4 already installed" 
else
  wget http://downloads.sourceforge.net/halo-extension/smwhalo-1.4.2.zip
  unzip smwhalo-1.4.2.zip
  rm smwhalo-1.4.2.zip
fi


###################################################
# Switch to extension directory
###################################################
cd extensions

###################################################
# Install SMW 1.4.2
###################################################

echo "Semantic MediaWiki"

#if [ -d "SemanticMediaWiki" ]
#then
#  echo "SemanticMediaWiki 1.4.2 already installed" 
#else
#  wget http://downloads.sourceforge.net/semediawiki/semediawiki-1.4.2.tar.gz
#  tar -zxf semediawiki-1.4.2.tar.gz
#  rm semediawiki-1.4.2.tar.gz
#fi

EXT_WEBPATH="http://smwbp.googlecode.com/svn/trunk/release/semediawiki-1.4.2"
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

#if [ -d "SemanticResultFormats" ]
#then
#  echo "SemanticResultFormats 1.4.2 already installed" 
#else
#  wget http://downloads.sourceforge.net/semediawiki/SemanticResultFormats-1.4.2.tar.gz
#  tar -zxf SemanticResultFormats-1.4.2.tar.gz
#  rm SemanticResultFormats-1.4.2.tar.gz
#fi


EXT_WEBPATH="http://smwbp.googlecode.com/svn/trunk/release/semediawiki-1.4.2"
EXT_NAME="SemanticResultFormats"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi


MW_VERSION=REL1_14
MW_WEBPATH=http://svn.wikimedia.org/svnroot/mediawiki/branches/$MW_VERSION
MW_WEBPATH_MW=$MW_WEBPATH/phase3
MW_WEBPATH_EXT=$MW_WEBPATH/extensions


###################################################
# Install SMW Extensions
###################################################
# Semantic Forms  http://www.mediawiki.org/wiki/Extension:Semantic_Forms
echo "http://www.mediawiki.org/wiki/Extension:Semantic_Forms"

EXT_WEBPATH=$MW_WEBPATH_EXT
EXT_NAME="SemanticForms"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi


# Semantic Drilldown (http://www.mediawiki.org/wiki/Extension:Semantic_Drilldown)
echo "http://www.mediawiki.org/wiki/Extension:Semantic_Drilldown"

EXT_WEBPATH=$MW_WEBPATH_EXT
EXT_NAME="SemanticDrilldown"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi