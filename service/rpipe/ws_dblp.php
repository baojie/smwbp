<?php 
require_once ("includes/api_web.php");
require_once ("includes/api_dblp.php");
require_once ("includes/default_settings.php");

// Report all PHP errors
error_reporting(E_ALL);

// internal objects
$mydblp  = new api_dblp();
$myweb  = new api_web();
$m_bAuthorUrl = true;

// params
$m_url=$_GET["url"]; //first name
if (empty($m_url)){

	$m_fn=$_GET["fn"]; //first name
	if (empty($m_fn)){
	  echo "empty first name, use fn= to specify ";
	  exit();
	}
	$m_fn=trim($m_fn);

	$m_mi=$_GET["mi"]; //middle name
	if (empty($m_mi)){
	// do nothing
	}else{
	  // just keep the initial
	  $m_mi=strtoupper(substr($m_mi,0,1));
	}
	$m_mi=trim($m_fn);

	$m_ln=$_GET["ln"]; // last name
	if (empty($m_ln)){
	  echo "empty last name, use ln= to specify ";
	  exit();
	}
	$m_ln=trim($m_fn);

	// generate dblp url
	$m_url = $mydblp->getAuthorURL($m_fn, $m_ln, $m_mi);
}else{
  if (! stripos($m_url,".html"))
    $m_bAuthorUrl = false;
}

//remove whitespace before and after
$m_url=trim($m_url);
	
$m_output_option=$_GET["output_option"];  //output output_option
if (empty($m_output_option)){
  $m_output_option="html";	
}

$m_tag=$_GET["tag"];  //tag of paper
if (empty($m_tag)){
  $m_tag="";	
}

//load dblp url
$contents = $myweb->load($m_url);
if (empty($contents)){
  echo "cannot access page: ". $myweb->printHyperlink($m_url);
  exit();
}

$format = $m_output_option;
//begin of document
switch ($m_output_option){
  case "i.publication":
     if ($m_bAuthorUrl){
	$myweb->printBegin("xml","mediawiki"); 
	$format ="i.publication-wikidump";
	break;
     }else{
        $myweb->printBegin("text"); 
	$format ="i.publication-text";
	break;
     }
  case "bibtex":
     $myweb->printBegin("text"); break;
  case "xml":
     $myweb->printBegin("xml"); break;
}
date_default_timezone_set('UTC');
$timestamp= date('Y-m-d\TH:i:s\Z');


	
if ($m_bAuthorUrl){
	//parse dblp url into data
	if (preg_match("@/db/conf/@",$m_url)){
		$data = $mydblp->parseProceedingPage($contents);
	}else{
		$data = $mydblp->parseAuthorPage($contents);
	}
	
	
	if (!isset($data) || !is_array($data) || sizeof($data)==0){
	  echo "no paper listed on page: ". $myweb->printHyperlink($m_url);
	  exit();
	}
	
	//print dblp data
	foreach ($data as $url){
	   $contents_bib = $myweb->load($url);
	   
	   //parse dblp url into data
	   $data_bib = $mydblp->parseBibtexPage($contents_bib);
	   if (!isset($data_bib) || !is_array($data_bib) || sizeof($data_bib)==0) {
		  echo "no bibtex specified on page: ". $myweb->printHyperlink($url);
		  exit();
	   }
	   //print dblp data
	   $out = $mydblp->printBibtex($data_bib,$url,$format,$timestamp,$m_tag);
	   print ($out);
	}
}else{
        $url =$m_url;

	$contents_bib = $myweb->load($url);
	$data_bib = $mydblp->parseBibtexPage($contents_bib);
	if (!isset($data_bib) || !is_array($data_bib) || sizeof($data_bib)==0) {
	  echo "no bibtex specified on page: ". $myweb->printHyperlink($url);
	  exit();
	}
	//print dblp data
	$out = $mydblp->printBibtex($data_bib,$url,$format,$timestamp,$m_tag);
	
	print ($out);
}

//end of document
switch ($m_output_option){
  case "i.publication":
     if ($m_bAuthorUrl){
	$myweb->printEnd("xml","mediawiki"); break;
     }else{
        $myweb->printEnd("text"); break;
     }
  case "bibtex":
     $myweb->printEnd("text"); break;
  case "xml":
     $myweb->printEnd("xml"); break;
}

?>
