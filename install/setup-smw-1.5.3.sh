#!/bin/bash
###################################################
# configure
###################################################
MW_VERSION=16
SMW_VERSION=1.5.3

###################################################
# usage
###################################################
if [ -z "$1" ]
then 
   echo "Example Usage"
   echo "./setup-smw-$SMW_VERSION.sh WIKI_DIR     - install/update smw and swm+ at the sub-directory named WIKI_DIR"
   exit
else
   # configure your installation path
   WIKI_DIR=$1
fi

###################################################
# Install Mediawiki (MW)
###################################################
./setup-mw.sh "$1" "$MW_VERSION"


###################################################
# Switch to wiki directory
###################################################
cd $WIKI_DIR



###################################################
# Switch to extension directory
###################################################
cd extensions

###################################################
# Install SMW 
###################################################

echo "Semantic MediaWiki " $SMW_VERSION

if [ -d "SemanticMediaWiki" ]
then
  echo "SemanticMediaWiki $SMW_VERSION already installed" 
else
  wget http://downloads.sourceforge.net/semediawiki/SemanticMediaWiki$SMW_VERSION.tgz
  tar -zxf SemanticMediaWiki$SMW_VERSION.tgz
  rm SemanticMediaWiki$SMW_VERSION.tgz
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


MW_VERSION=REL1_$MW_VERSION
MW_WEBPATH=http://svn.wikimedia.org/svnroot/mediawiki/branches/$MW_VERSION
MW_WEBPATH_MW=$MW_WEBPATH/phase3
MW_WEBPATH_EXT=$MW_WEBPATH/extensions

###################################################
# Install SMW Extensions
###################################################

# Semantic Result Format (http://www.mediawiki.org/wiki/Extension:Semantic_Result_Formats)
echo "http://www.mediawiki.org/wiki/Extension:Semantic_Result_Formats"

EXT_WEBPATH=$MW_WEBPATH_EXT
EXT_NAME="SemanticResultFormats"
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


###################################################
echo "Install MW Extensions (from SMWBP)"
###################################################

# Semantic History (http://www.mediawiki.org/wiki/Extension:Semantic_History)
echo "http://www.mediawiki.org/wiki/Extension:Semantic_History"

EXT_WEBPATH="http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions"
EXT_NAME="SemanticHistory"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi