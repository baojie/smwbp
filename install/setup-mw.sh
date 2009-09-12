#!/bin/bash
###################################################
# usage
# dc:creator   http://www.cs.rpi.edu/~dingl
# dc:modified  2009/09/12
###################################################
if [ -z "$1" ]
then 
   echo "Example Usage"
   echo "./setup-mw.sh WIKI_DIR WIKI_VERSION - install/update mediawiki at the sub-directory named WIKI_DIR (e.g. test) with subversion WIKI_VERSION (e.g. 15 for 1.15)"
   exit
else
   # configure your installation path
   WIKI_DIR=$1
   if [ -z "$2" ]
   then
      MW_VERSION="REL1_15"
   else
      MW_VERSION="REL1_"$2
   fi
fi

MW_WEBPATH=http://svn.wikimedia.org/svnroot/mediawiki/branches/$MW_VERSION
MW_WEBPATH_MW=$MW_WEBPATH/phase3
MW_WEBPATH_EXT=$MW_WEBPATH/extensions

echo MW_WEBPATH_MW;
###################################################
echo " Install Mediawiki 1.xx"
echo " $MW_VERSION" 
###################################################
if [ -d $WIKI_DIR ]
then
  echo "updating...";
  svn update $WIKI_DIR
else
  svn checkout $MW_WEBPATH_MW  $WIKI_DIR
fi



###################################################
# Switch to extension directory
###################################################
cd $WIKI_DIR/extensions


###################################################
echo "Install MW Extensions (from MW branch)"
###################################################
# StringFunctions (http://www.mediawiki.org/wiki/Extension:StringFunctions)
echo "http://www.mediawiki.org/wiki/Extension:StringFunctions"

EXT_WEBPATH=$MW_WEBPATH_EXT
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

EXT_WEBPATH=$MW_WEBPATH_EXT
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

EXT_WEBPATH=$MW_WEBPATH_EXT
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

EXT_WEBPATH=$MW_WEBPATH_EXT
EXT_NAME="Icon"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi


# Lockdown (http://www.mediawiki.org/wiki/Extension:Lockdown)
echo "http://www.mediawiki.org/wiki/Extension:Lockdown"

EXT_WEBPATH=$MW_WEBPATH_EXT
EXT_NAME="Lockdown"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi

# Widget (http://www.mediawiki.org/wiki/Extension:Widget)
echo "http://www.mediawiki.org/wiki/Extension:Widget"

EXT_WEBPATH=$MW_WEBPATH_EXT
EXT_NAME="Widget"
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

# Loops (http://www.mediawiki.org/wiki/Extension:Loops)
echo "http://www.mediawiki.org/wiki/Extension:Loops"

EXT_WEBPATH="http://smwbp.googlecode.com/svn/trunk/mediawiki/extensions"
EXT_NAME="Loops"
if [ -d $EXT_NAME ]
then
  echo "updating...";
  svn update $EXT_NAME
else
  svn checkout $EXT_WEBPATH/$EXT_NAME/
fi

# FCKeditor extension (http://www.mediawiki.org/wiki/Extension:FCKeditor_(Official))   for user friendly UI
echo "http://www.mediawiki.org/wiki/Extension:FCKeditor_(Official)"

EXT_WEBPATH="http://smwbp.googlecode.com/svn/trunk/release/FCKeditor_nightly"
EXT_NAME="FCKeditor"
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