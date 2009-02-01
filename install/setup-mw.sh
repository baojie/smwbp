#!/bin/bash
###################################################
# usage
###################################################
if [ -z "$1" ]
then 
   echo "Example Usage"
   echo "./setup-mw.sh WIKI_DIR - install/update mediawiki at the sub-directory named WIKI_DIR"
   exit
else
   # configure your installation path
   WIKI_DIR=$1
fi

###################################################
# Install Mediawiki (MW, 1.13)
###################################################
echo "Mediawiki 1.13.3"
if [ -d $WIKI_DIR ]
then
  echo "updating...";
  svn update $WIKI_DIR
else
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/branches/REL1_13/phase3  $WIKI_DIR
fi



###################################################
# Switch to extension directory
###################################################
cd $WIKI_DIR/extensions


###################################################
# Install MW Extensions
###################################################
# VariablesExtension (http://www.mediawiki.org/wiki/Extension:Variables)
echo "http://www.mediawiki.org/wiki/Extension:Variables"

EXT_WEBPATH="http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions"
EXT_NAME="Variables"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi


# StringFunctions (http://www.mediawiki.org/wiki/Extension:StringFunctions)
echo "http://www.mediawiki.org/wiki/Extension:StringFunctions"

EXT_WEBPATH="http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions"
EXT_NAME="StringFunctions"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi


# ParserFunctions (http://www.mediawiki.org/wiki/Extension:ParserFunctions)
echo "http://www.mediawiki.org/wiki/Extension:ParserFunctions"

EXT_WEBPATH="http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions"
EXT_NAME="ParserFunctions"
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


# Icon (http://www.mediawiki.org/wiki/Extension:Icon)
echo "http://www.mediawiki.org/wiki/Extension:Icon"

EXT_WEBPATH="http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions"
EXT_NAME="Icon"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi


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


# ArrayExtension (http://www.mediawiki.org/wiki/Extension:ArrayExtension)
echo "http://www.mediawiki.org/wiki/Extension:ArrayExtension"

EXT_WEBPATH="http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions"
EXT_NAME="ArrayExtension"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi

# LoopFunctions (http://www.mediawiki.org/wiki/Extension:LoopFunctions)
echo "http://www.mediawiki.org/wiki/Extension:LoopFunctions"

EXT_WEBPATH="http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions"
EXT_NAME="LoopFunctions"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi

###################################################
# Install SMWBP Configuration Settings
###################################################
echo "SMWBP"

EXT_WEBPATH="http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions"
EXT_NAME="SMWBP"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi



###################################################
## experimental install
###################################################


# EmbedVideo (http://www.mediawiki.org/wiki/Extension:EmbedVideo)  for embedding video 
echo "http://www.mediawiki.org/wiki/Extension:EmbedVideo"

EXT_WEBPATH="http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions"
EXT_NAME="EmbedVideo"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi

# WikiWidgets (http://www.mediawiki.org/wiki/Extension:WikiWidgets)  for embedding some widgets 
echo "http://www.mediawiki.org/wiki/Extension:WikiWidgets"

EXT_WEBPATH="http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions"
EXT_NAME="wx"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi