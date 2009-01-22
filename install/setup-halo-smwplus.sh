#!/bin/bash
###################################################
# usage
###################################################
if [ -z "$1" ]
then 
   echo "Example Usage"
   echo "setup-smwplus install WIKI_DIR - install smw+ at the subdirectory named WIKI_DIR"
   echo "setup-smwplus update WIKI_DIR - install smw+ at the subdirectory named WIKI_DIR"
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
./setup-mw.sh "$1" "$2"

###################################################
# Install SMW extensions
###################################################
./inc-ext-smw.sh "$1" "$2"


###################################################
# Switch to wiki directory
###################################################
cd $WIKI_DIR


###################################################
# Install SMW Plus (1.4)
###################################################
echo "SMW+ 1.4"

if [ $1 = "install" ]
then
  wget http://downloads.sourceforge.net/halo-extension/smwplus-1.4.zip
  unzip smwplus-1.4.zip
  rm smwplus-1.4.zip
fi


###################################################
# Switch to extension directory
###################################################
cd extensions

###################################################
# Install SMW 1.4.1
###################################################

if [ $1 = "install" ]
then
  wget http://downloads.sourceforge.net/semediawiki/semediawiki-1.4.1.tar.gz
  tar -zxf semediawiki-1.4.1.tar.gz
  rm semediawiki-1.4.1.tar.gz
fi


if [ $1 = "install" ]
then
  wget http://downloads.sourceforge.net/semediawiki/SemanticResultFormats.tar.gz
  tar -zxf SemanticResultFormats.tar.gz
  rm SemanticResultFormats.tar.gz
fi

