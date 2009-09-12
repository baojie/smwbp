#!/bin/bash
###################################################
# usage
###################################################
if [ -z "$1" ]
then 
   echo "Example Usage"
   echo "./setup-smw-1.4.3.sh WIKI_DIR     - install/update smw and swm+ at the sub-directory named WIKI_DIR"
   exit
else
   # configure your installation path
   WIKI_DIR=$1
fi

###################################################
# Install Mediawiki (MW)
###################################################
./setup-mw.sh "$1" "15"


###################################################
# Switch to wiki directory
###################################################
cd $WIKI_DIR


###################################################
# Install SMW Plus 
###################################################
SMWPLUS_VERSION=1.4.4
echo "SMW+ "$SMWPLUS_VERSION

if [ -d "extensions/SMWHalo" ]
then
  echo "smwplus already installed" 
else
  wget http://downloads.sourceforge.net/halo-extension/smwhalo-$SMWPLUS_VERSION.zip
  unzip smwhalo-$SMWPLUS_VERSION.zip
  rm smwhalo-$SMWPLUS_VERSION.zip
fi


###################################################
# Switch to extension directory
###################################################
cd extensions

###################################################
# Install SMW 1.4.3
###################################################
$SMW_VERSION

echo "Semantic MediaWiki " $SMW_VERSION

if [ -d "SemanticMediaWiki" ]
then
  echo "SemanticMediaWiki $SMW_VERSION already installed" 
else
  wget http://downloads.sourceforge.net/semediawiki/semediawiki-$SMW_VERSION.tar.gz
  tar -zxf semediawiki-$SMW_VERSION.tar.gz
  rm semediawiki-$SMW_VERSION.tar.gz
fi

#EXT_WEBPATH="http://smwbp.googlecode.com/svn/trunk/release/semediawiki-$SMW_VERSION"
#EXT_NAME="SemanticMediaWiki"
#if [ -d $EXT_NAME ]
#then
#  echo "updating...";
#  svn update $EXT_NAME
#else
#  svn checkout $EXT_WEBPATH/$EXT_NAME/
#fi


MW_VERSION=REL1_15
MW_WEBPATH=http://svn.wikimedia.org/svnroot/mediawiki/branches/$MW_VERSION
MW_WEBPATH_MW=$MW_WEBPATH/phase3
MW_WEBPATH_EXT=$MW_WEBPATH/extensions

###################################################
# Install SMW Extensions
###################################################

# Semantic Result Format (http://www.mediawiki.org/wiki/Extension:Semantic_Result_Formats)
echo "http://www.mediawiki.org/wiki/Extension:Semantic_Result_Formats"

EXT_WEBPATH=$MW_WEBPATH_EXT
EXT_NAME="Extension:Semantic_Result_Formats"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi



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