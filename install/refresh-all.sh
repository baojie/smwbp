#!/bin/bash
###################################################
# usage
###################################################
if [ -z "$1" ]
then 
   echo "This script should not be called directly, please use setup scripts"
   exit
else
   # configure your installation path
   WIKI_DIR=$1
fi

echo $WIKI_DIR

###################################################
# Switch to extension directory
###################################################
cd $WIKI_DIR

SCRIPT_NAME ="maintenance/update.php"
if [ -d $SCRIPT_NAME ]
then
  echo "running script";
  php $SCRIPT_NAME
fi


SCRIPT_NAME ="extensions/SemanticMediaWiki/maintenance/SMW_setup.php"
if [ -d $SCRIPT_NAME ]
then
  echo "running script";
  php $SCRIPT_NAME
fi

SCRIPT_NAME ="extensions/SMWHalo/maintenance/SMW_setup.php"
if [ -d $SCRIPT_NAME ]
then
  echo "running script";
  php $SCRIPT_NAME
fi

 
SCRIPT_NAME ="maintenance/rebuildall.php"
if [ -d $SCRIPT_NAME ]
then
  echo "running script";
  php $SCRIPT_NAME
fi

SCRIPT_NAME ="extensions/SemanticMediaWiki/maintenance/SMW_refreshData.php"
if [ -d $SCRIPT_NAME ]
then
  echo "running script";
  php $SCRIPT_NAME
fi
