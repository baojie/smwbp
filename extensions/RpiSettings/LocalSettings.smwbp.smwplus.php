<?php

#################################################################
# This file is generated for common configuration on Wiki and SMW extensions
# see http://tw.rpi.edu/wiki/LocalSettings.ext.php

## #######################################
## SMW+

$wgDefaultSkin='ontoskin2';

$wgUseAjax = true; //MUST
include_once('extensions/SMWHalo/includes/SMW_Initialize.php');
#enableSMWHalo();

enableSMWHalo('SMWHaloStore2','SMWTripleStore');
$phpInterpreter = "/usr/local/bin/php";


$smwgMessageBroker = "localhost";
$smwgWebserviceEndpoint = "localhost:61616"; 




