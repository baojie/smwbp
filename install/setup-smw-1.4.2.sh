#!/bin/bash
###################################################
# usage
###################################################
if [ -z "$1" ]
then 
   echo "Example Usage"
   echo "./setup-k.sh WIKI_DIR     - install/update halo extension swm+ at the sub-directory named WIKI_DIR"
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


###################################################
# Install MW Extensions
###################################################
# LdapAuth (LdapAuth)
echo "LdapAuth"

EXT_WEBPATH="http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions"
EXT_NAME="LdapAuth"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi

# CategoryTree (http://www.mediawiki.org/wiki/Extension:CategoryTree)
echo "http://www.mediawiki.org/wiki/Extension:CategoryTree"

EXT_WEBPATH="http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions"
EXT_NAME="CategoryTree"
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
# Semantic Forms  http://www.mediawiki.org/wiki/Extension:Semantic_Forms
echo "http://www.mediawiki.org/wiki/Extension:Semantic_Forms"

EXT_WEBPATH="http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions"
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

EXT_WEBPATH="http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions"
EXT_NAME="SemanticDrilldown"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi