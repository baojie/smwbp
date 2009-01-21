<?php

$doc = new DOMDocument();
$doc->loadHTMLFile("http://onto.rpi.edu/wiki/c3po/index.php/Special:AskExternal?q=[[Category%3APerson]]&format=table");
echo $doc->saveHTML();

?>
