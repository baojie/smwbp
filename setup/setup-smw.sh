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
# Install Semantic MediaWiki (SMW, 1.4)
###################################################
if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticMediaWiki/
fi

if [ $1 = "update" ]
then
  svn update SemanticMediaWiki
fi


###################################################
# Install MW Extensions
###################################################
# VariablesExtension (http://www.mediawiki.org/wiki/Extension:Variables)
if [ $1 = "install" ]
then
  svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/Variables/
fi

if [ $1 = "update" ]
then
  svn update Variables
fi

# StringFunctions (http://www.mediawiki.org/wiki/Extension:StringFunctions)
if [ $1 = "install" ]
then 
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/StringFunctions/
fi

if [ $1 = "update" ]
then
  svn update StringFunctions
fi

# ParserFunctions (http://www.mediawiki.org/wiki/Extension:ParserFunctions)
if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/ParserFunctions/
fi

if [ $1 = "update" ]
then
  svn update ParserFunctions
fi

# CategoryTree (http://www.mediawiki.org/wiki/Extension:CategoryTree)
if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/CategoryTree/
fi

if [ $1 = "update" ]
then
  svn update CategoryTree
fi


# LdapAuth (http://www.mediawiki.org/wiki/LdapAuth)
if [ $1 = "install" ]
then
  svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/LdapAuth/
fi

if [ $1 = "update" ]
then
  svn update LdapAuth
fi

###################################################
# Install SMW Extensions
###################################################
# Semantic Forms 
if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticForms/
fi

if [ $1 = "update" ]
then
  svn update SemanticForms
fi

# Semantic Result Format (only compatible with SMW 1.4)
if [ $1 = "install" ]
then
  svn checkout http://svn.wikimedia.org/svnroot/mediawiki/trunk/extensions/SemanticResultFormats/
fi

if [ $1 = "update" ]
then
  svn update SemanticResultFormats
fi


###################################################
# Install MW and SMW Configuration Settings
###################################################
if [ $1 = "install" ]
then
  svn checkout http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions/SMWBP/
fi

if [ $1 = "update" ]
then
  svn update SMWBP
fi