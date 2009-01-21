<?php

#################################################################
# This file is generated the basic configuration for MediaWiki

############################
## MediaWiki (MW)
#   svn checkout http://svn.wikimedia.org/svnroot/mediawiki/branches/REL1_13/phase3
## #######################################

# disallow anonymous edit
$wgGroupPermissions['*']['edit'] = false;  # restrict anonymous edit

# add user privillege
$wgGroupPermissions['user']['delete'] = true;  # allow user delete
$wgGroupPermissions['user']['move'] = true;  # allow user move
$wgGroupPermissions['user']['refresh'] = true;  # allow user refresh


?>
