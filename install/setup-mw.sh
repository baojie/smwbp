#!/bin/bash
###################################################
# usage
###################################################
if [ -z "$1" ]
then 
   echo "Example Usage"
   echo "setup-mw install WIKI_DIR - install mw at the subdirectory named WIKI_DIR"
   echo "setup-mw update WIKI_DIR - update mw at the subdirectory named WIKI_DIR"
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
echo "Mediawiki 1.13.3"
if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/branches/REL1_13/phase3  $WIKI_DIR
fi

if [ $1 = "update" ]
then
  svn update $WIKI_DIR
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

if [ $1 = "install" ]
then
  svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/Variables/
fi

if [ $1 = "update" ]
then
  svn update Variables
fi

# StringFunctions (http://www.mediawiki.org/wiki/Extension:StringFunctions)
echo "http://www.mediawiki.org/wiki/Extension:StringFunctions"

if [ $1 = "install" ]
then 
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/StringFunctions/
fi

if [ $1 = "update" ]
then
  svn update StringFunctions
fi

# ParserFunctions (http://www.mediawiki.org/wiki/Extension:ParserFunctions)
echo "http://www.mediawiki.org/wiki/Extension:ParserFunctions"

if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/ParserFunctions/
fi

if [ $1 = "update" ]
then
  svn update ParserFunctions
fi

# CategoryTree (http://www.mediawiki.org/wiki/Extension:CategoryTree)
echo "http://www.mediawiki.org/wiki/Extension:CategoryTree"

if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/CategoryTree/
fi

if [ $1 = "update" ]
then
  svn update CategoryTree
fi


# LdapAuth (LdapAuth)
echo "LdapAuth"

if [ $1 = "install" ]
then
  svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/LdapAuth/
fi

if [ $1 = "update" ]
then
  svn update LdapAuth
fi



# Icon (http://www.mediawiki.org/wiki/Extension:Icon)
echo "http://www.mediawiki.org/wiki/Extension:Icon"

if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/Icon/
fi

if [ $1 = "update" ]
then
  svn update Icon
fi


###################################################
# Install SMWBP Configuration Settings
###################################################
echo "SMWBP"

if [ $1 = "install" ]
then
  svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/SMWBP/
fi

if [ $1 = "update" ]
then
  svn update SMWBP
fi


###################################################
## experimental install
###################################################


# EmbedVideo (http://www.mediawiki.org/wiki/Extension:EmbedVideo)  for embedding video 
echo "http://www.mediawiki.org/wiki/Extension:EmbedVideo"

if [ $1 = "install" ]
then
   svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/EmbedVideo/
fi

if [ $1 = "update" ]
then
  svn update EmbedVideo
fi

# WikiWidgets (http://www.mediawiki.org/wiki/Extension:WikiWidgets)  for embedding some widgets 
echo "http://www.mediawiki.org/wiki/Extension:WikiWidgets"

if [ $1 = "install" ]
then
   svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/wx/
fi

if [ $1 = "update" ]
then
  svn update wx
fi
