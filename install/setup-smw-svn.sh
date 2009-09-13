#!/bin/bash
###################################################
# usage
###################################################
if [ -z "$1" ]
then 
   echo "Example Usage"
   echo "./setup-smw-svn.sh WIKI_DIR WIKI_VERSION - install/update MW, SWM and SMW+ at the sub-directory named WIKI_DIR using the wiki version"
   exit
else
   # configure your installation path
   WIKI_DIR=$1
   
   if [ -z "$2" ]
   then
		MW_WEBPATH=http://svn.wikimedia.org/svnroot/mediawiki/trunk
   else
        MW_VERSION="REL1_"$2
		MW_WEBPATH=http://svn.wikimedia.org/svnroot/mediawiki/branches/$MW_VERSION
   fi
   MW_WEBPATH_MW=$MW_WEBPATH/phase3
   MW_WEBPATH_EXT=$MW_WEBPATH/extensions
fi

###################################################
# Install Mediawiki 
###################################################
./setup-mw.sh "$1" "$2"



###################################################
# Switch to extension directory
###################################################
cd $WIKI_DIR/extensions


###################################################
# Install Semantic MediaWiki (SMW)
###################################################
echo "Semantic MediaWiki"

EXT_WEBPATH=$MW_WEBPATH_EXT
EXT_NAME="SemanticMediaWiki"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi


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