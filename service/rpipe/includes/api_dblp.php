<?php 

require_once ("default_settings.php");
require_once ("api_web.php");
require_once ("api_bibtex.php");

class api_dblp{	
	/** return the DBLP URL for an author
	 */
	function getAuthorURL($firstName,$lastName,$middle=""){
		global $wgDBLP_Base;
		
		$firstName = str_replace("-","=",$firstName);
		$lastName  = str_replace("-","=",$lastName);

		$initial = strtolower($lastName[0]);

		$url = $wgDBLP_Base.$initial."/".$lastName.":".$firstName;
		if ($middle) $url = $url. "_".$middle."=";
		$url = $url.".html";
		return $url;
	}

	/** return an array of paper urls 
	 */
	function parseAuthorPage($contents){
		$wgDBLP_Pattern_Paper = "/<a class=\"rec\" href=\"(.*)\" name=/";

		// set array of data
		$matches = array();
		$ret = preg_match_all($wgDBLP_Pattern_Paper, $contents, $matches);
		return $matches[1];
	}

	/** return an array of paper urls 
	 */
	function parseProceedingPage($contents){
		$wgDBLP_Pattern_Paper ="/href=\"([^\"]*)\">BibTeX/";

		// set array of data
		$matches = array();
		$ret = preg_match_all($wgDBLP_Pattern_Paper, $contents, $matches);
		return $matches[1];
	}
	
	
	/** return an array of bibtex 
	 * $data[0]  is the paper's bibtex
	 * $data[1]  is the crossreference bibtex
	 */
	function parseBibtexPage($contents){
		$wgDBLP_Pattern_Bibtex = "/<pre>(.*?)<\/pre>/ism";

		$contents = preg_replace('/<a href="http:\/\/dblp.uni-trier.de\/db\/about\/bibtex.html">DBLP<\/a>/','DBLP',$contents);
		// set array of data
		$matches = array();
		$ret = preg_match_all($wgDBLP_Pattern_Bibtex, $contents, $matches);
		return $matches[1];
	}
	

	/** print paper list
	 */
	function printPaperList($data, $source, $format='html'){
		global $wgServiceDBLP_BibtexParser;
		
		$myweb = new api_web();
		
		$ret = "";
		switch ($format){
		case "xml":
			$ret .= "<source>".$source."</source>\n";
			foreach ($data as $entry){
				$ret .= "<Paper>\n";
				$ret .= "  <url>".$entry."</url>\n";
				$ret .= "  <bibtex_url>".$wgServiceDBLP_BibtexParser.urlencode($entry)."</bibtex_url>\n";
				$ret .= "</Paper>\n";
			}
			break;
		case "html":
			$ret .= "<p>source:" . $myweb->printHyperlink($source)."\n";
			$ret .= "\n";
			$ret .= "<ol>\n";
			foreach ($data as $entry){
				$ret .= "<li>Paper:<br/>\n";
				$ret .= "&nbsp;* url: ".$myweb->printHyperlink($entry)."<br/>\n";
				$ret .= "&nbsp;* bibtex_url: ".$myweb->printHyperlink($wgServiceDBLP_BibtexParser.urlencode($entry), "bibtex")."\n";
				$ret .= "</li>\n";
			}
			$ret .= "</ol>\n";
			$ret .= "</p>\n";
			break;
		}

		return $ret;		
	}	
	
	/** print bibtex list
	 */
	function printBibtex($data, $source, $format='bibtex', $timestamp=''){
		$ret = "";
		switch ($format){
		case "xml":
			$ret .= "<Paper>\n";
			foreach ($data as $entry){
				$ret .= "  <source>".$source."</source>\n";
				$ret .= "  <bibtex><![CDATA[".$entry."]]></bibtex>\n";
			}
			$ret .= "</Paper>\n";
			break;
		case "bibtex":
			$ret = "% source:".$source."\n";
			$ret .= $data[0];
			break;
		
		case "i.publication-wikidump":
			$bib = new BibTex ($data[0]);
			$bib->entry->fields["source"] =$source;
			
			$ret ='<page>
    <title><![CDATA['.strtolower($bib->entry->get_field('title')).']]></title>
    <revision>   
      <timestamp>'.$timestamp.'</timestamp>
      <text xml:space="preserve"><![CDATA['.$this->print_i_publication($bib).']]></text>
    </revision>
  </page>';
			break;
		case "i.publication-text":
			$bib = new BibTex ($data[0]);
			$bib->entry->fields["source"] =$source;

			$ret = $this->print_i_publication($bib);
			break;
		}
		return $ret;		
	}
	
	function print_i_publication($bib){
		$bibtype = $bib->entry->type_specifier;
		$bibkey = $bib->entry->key_identifier;
		$bibfields= $bib->entry->fields;		

		$templ = "{{i.publication.".$bibtype."\n" ;
		$templ .= "  |bibtype=".$bibtype."\n";
		$templ .= "  |key=".$bibkey."\n";
		foreach ($bibfields as $field_name=>$field_value ){
		   $templ .= "  |".$field_name." = ".$field_value."\n";
		}
		$templ .= "}}";	
		
		return $templ;
	}
	
}

?>