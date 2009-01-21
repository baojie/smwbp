<?php

#################################################################
# This file is generated the basic configuration for MediaWiki

############################
## MediaWiki (MW)
#   svn checkout http://svn.wikimedia.org/svnroot/mediawiki/branches/REL1_13/phase3
## #######################################

############################
#  permissions
# disallow anonymous edit
$wgGroupPermissions['*']['edit'] = false;  # restrict anonymous edit

# add user privillege
$wgGroupPermissions['user']['delete'] = true;  # allow user delete
$wgGroupPermissions['user']['move'] = true;  # allow user move
$wgGroupPermissions['user']['refresh'] = true;  # allow user refresh


############################
# upload
$wgEnableUploads       = true;  //enable upload

/** Warn if uploaded files are larger than this (in bytes), or false to disable*/
$wgUploadSizeWarning = false;

/** This is a flag to determine whether or not to check file extensions on upload. */
$wgCheckFileExtensions = false;

/**
 * If this is turned off, users may override the warning for files not covered
 * by $wgFileExtensions.
 */
$wgStrictFileExtensions = false;

/** File extensions. */
$wgFileExtensions = array( 'png', 'gif', 'jpg', 'jpeg' , 'ppt', 'pdf', 'ps', 'ep
s', 'doc','xls', 'owl', 'zip', 'gz', 'txt', 'tex', 'bib', 'cls', 'sty' );

/** verify mime type */
$wgVerifyMimeType= false;



?>
