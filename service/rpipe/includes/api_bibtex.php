<?php

/*****************************************************
 *  the following code are  shamelessly obtained from the Web 
 *    http://evra.ibisc.univ-evry.fr/index.php/Mediawiki_extensions 
 */

// a single bibtex entry excluding string definitions
// beginning of classes developped by Kinh Tieu and modified for our custom needs
class BibEntry {
     var $type_specifier; // e.g., inproceedings
     var $key_identifier; // e.g., Tieu2001
     var $fields; // array of field names and field text, e.g., author={Kinh Tieu}
     var $where_published;
     var $publishing_details;
     var $content; // Unmodified content of bibtex entry for ACM-like popup

     function BibEntry($type_specifier,$key_identifier, $cnt) {
	  $this->content = $cnt;
	  $this->type_specifier=$type_specifier;
	  $this->key_identifier=$key_identifier;
	  $this->fields=array();
	  $where_published='';
	  $publishing_details='';
     }

     function get_tag($tagname) {
	  return $this->$tagname;
     }

     function get_where_published() {
	  return $this->where_published;
     }

     function get_publishing_details() {
	  return $this->publishing_details;
     }

     function get_field($field_name) {
	  if (array_key_exists($field_name,$this->fields))
	  	return $this->fields[$field_name];
	  else
		return '';
     }

     function set_field($field_name,$value) {
	  $this->fields[$field_name]=$value;
     }

     function get_content() {
	  return $this->content;
     }

     function get_abstract() {
	  return str_replace(array("\n","\"","'"),array('<br/>',"&quot;", "\'"),$this->get_field('abstract'));
     }

     // print bibtex format, for debugging
     function p() {
	  print "@".$this->type_specifier."{".$this->key_identifier.",<br>\n";
	  $field_names=array_keys($this->fields);
	  foreach ($field_names as $field_name) {
	       print "$field_name={".$this->fields[$field_name]."}<br>\n";
	  }
	  print "}<br>\n";
     }

     // need to make this handle different bibliography styles
     function parse_name($name) {
	  return preg_replace(array('/{/','/}/'),array('',''),$name);
     }

     // see parse_name()
     function parse_author() {
	  $author_text=$this->get_field('author');

	  if (!preg_match('/,/',$author_text)) { // not already in comma format
	       $names=preg_split('/\s+and\s+/',$author_text);
      
	       $names_flipped=array();

	       foreach ($names as $name) {
		    array_push($names_flipped,$this->parse_name($name));
	       }

	       $this->fields['author']=join(', ',$names_flipped);

	  } else {
	       $this->fields['author']=preg_replace('/\s+and\s+/',', ',$author_text);
	  }
     }

     // fill in generic fields
     function parse() {
	     global $wbibtechreport;
	     global $wbibmastersthesis;
	     global $wbibphdthesis;
	     global $wbibunpublished;
	  $this->parse_author();

	  switch (trim($this->type_specifier)) {
	  case 'inproceedings':
	       $this->where_published=$this->get_field('booktitle');
	       $this->publishing_details=$this->get_field('volume');
	       if (''!=$this->publishing_details) {
		    if (''!=$this->get_field('pages')) {
			 $this->publishing_details.=':'.$this->fields['pages'];
		    }
	       } else if (''!=$this->get_field('pages')) {
		    $this->publishing_details='pp. '.$this->fields['pages'];
	       }
	       break;
	  case 'article':
	       $this->where_published=$this->get_field('journal');
	       $this->publishing_details=$this->get_field('volume');
	       if (''!=$this->get_field('number')) {
		    $this->publishing_details.='('.$this->fields['number'].')';
	       }
	       if (''!=$this->publishing_details) {
		    if (''!=$this->get_field('pages')) {
			 $this->publishing_details.=':'.$this->fields['pages'];
		    }
	       } else if (''!=$this->get_field('pages')) {
		    $this->publishing_details='pp. '.$this->fields['pages'];
	       }
	       break;
	  case 'techreport':
	       $this->where_published=$wbibtechreport;
	       if ( '' != $this->get_field('type'))
	       		$this->where_published .= " - ".$this->fields['type'];
	       $this->where_published .=", ".$this->get_field('institution');//$this->fields['institution'];
	       if (''!=$this->get_field('number')) {
		    $this->publishing_details='('.$this->fields['number'].')';
	       }
	       break;
	  case 'mastersthesis':
               if ( '' != $this->get_field('type'))
                      $this->where_published = $this->fields['type'];
		else 
			$this->where_published=$wbibmastersthesis;
		$this->where_published .= ", ".$this->get_field('school');//$this->fields['school'];
	       break;
	  case 'phdthesis':
	       $this->where_published=$wbibphdthesis.", ".$this->get_field('school');//$this->fields['school'];
	       break;
	  case 'unpublished':
	       $this->where_published=$wbibunpublished;
	       break;
	  case 'book' :
		if ( '' != $this->get_field('volume') )
			$this->publishing_details .= "Vol. ".$this->fields['volume'].", ";
		if ( '' != $this->get_field('edition') )
			$this->publishing_details .= $this->fields['edition'].", ";
		$this->publishing_details .= $this->get_field('publisher');//$this->fields['publisher'];
		break;
	  }
	  
          if ('' != $this->get_field('address'))
	       $this->publishing_details .= ', '.$this->get_field('address');//$this->fields['address'];
			  
	  
          if ('' != $this->get_field('month'))
	       $this->publishing_details .= ', '.$this->get_field('month');//$this->fields['month'];
			  
	  if ('' != $this->get_field('year'))
	       $this->publishing_details .= ', '.$this->get_field('year');//$this->fields['year'];
		
     }
}


// Bibtex class written by Kinh Tieu, modified to parse only one entry
// from a string.
class BibTex {
     var $entry; // bibtex entry, e.g., @inproceedings,etc.

     // do it character by character
     function get_fields($text) {
	  $fields=array();
	  $i=0;
	  $len=strlen($text);

	  while ($i<$len) {
	       $field='';

	       // look for field name and text separator '='
	       while ($i<$len) {
		    $ch=substr($text,$i,1);
		    ++$i;
		    $field.=$ch;
		    if ('='==$ch) {
			 break;
		    }
	       }

	       // skip whitespace
	       while ($i<$len) {
		    $ch=substr($text,$i,1);
		    ++$i;
		    if (' '!=$ch && "\t"!=$ch && "\n"!=$ch) {
			 break;
		    }
	       }
	
	       switch ($ch) {
	       case '"': // look for ending '"'
		    while ($i<$len) {
			 $ch=substr($text,$i,1);
			 ++$i;
			 if ('"'==$ch) {
			      break;
			 } else {
			      $field.=$ch;
			 }
		    }
		    break;

	       case '{': // match up with '}'
		    $brace_count=1;
		    while ($i<$len && $brace_count>0) {
			 $ch=substr($text,$i,1);
			 switch ($ch) {
			 case '{':
			      ++$brace_count;
			      $field.='{';
			      break;
			 case '}':
			      --$brace_count;
			      if ($brace_count>0) {
				   $field.='}';
			      }
			      break;
			 default:
			      $field.=$ch;
			 }
			 ++$i;
		    }
		    break;

	       default: // numbers only or predefined string key
		    $field.='@';
		    $field.=$ch;
		    while ($i<$len && ','!=($ch=substr($text,$i,1))) {
			 if ('}'==$ch) { // hack to fix last entry
			      break;
			 }
			 $field.=$ch;
			 ++$i;
		    }
	       }
	       if (''!=$field) {
		    array_push($fields,$field);
	       }
	       ++$i; // skip comma
	  }
	  return $fields;
     }

     function BibTex($contents) {
	  $tmpcnt = $contents;
	  
	  $tmpcnt=preg_replace('/@/','',$tmpcnt);
	  list($type_specifier,$rest)=preg_split('/{/',$tmpcnt,2);
	  $type_specifier=strtolower($type_specifier);
	  list($key_identifier,$rest)=explode(',',$rest,2);
	  $key_identifier=strtolower($key_identifier);
	  $contents=str_replace(array("\n","\"","'"),array('<br/>',"&quot;", "\'"),$contents);
	  $this->entry=new BibEntry($type_specifier,$key_identifier, $contents);

	  $fields=$this->get_fields($rest);

	  foreach ($fields as $field) {
		  $gruick = preg_split('/\s*=\s*/',$field,2);
		  if( count($gruick) >= 2)
		  {
			  $field_name=$gruick[0];
			  $field_text=$gruick[1];
	       //list($field_name,$field_text)=preg_split('/\s*=\s*/',$field,2);
	       $field_name=trim(strtolower($field_name));
	       $field_text=trim($field_text);
	       $this->entry->set_field($field_name,$field_text);
		  }
	  }
	  $this->entry->parse();
	  
   	  //li ding. added to normalized field values, remove extra while space { } , change line, white space
	  foreach ($this->entry->fields as $field_name=>$field_value ){
	     $this->entry->fields[$field_name] = preg_replace ('/[\{|\}|\\|\s|\n|\r]+/',' ',$field_value);
	 }
	  
     }

     function allowed($title)
     {
	  //global $wgUser;
	  //$groups = $wgUser->getGroups();

	  //if ($title->isRestricted())
	  //{
	//	if (in_array("viewrestrict",$groups) || in_array("restrict",$groups))
	//		return true;
	//	else
	//		return false;
	//  }
	  return true;
     }

     function html(){
	  // gory html output
	  global $wgScriptPath;
	  global $wgUploadPath;
	  global $bibtexArray;
	  global $wbib_allowdivpopup;
	  global $wbib_allowbibpopup;
	  global $wbib_usejavascript;
	  global $wbib_pdficon;
	  global $wbib_psicon;
	  $entry = $this->entry;
	  //$entry->parse(); // needed ?
	  $output='';

	  // for things only needed once
  	  if ( (count($bibtexArray) == 0) && $wbib_allowdivpopup)
	       $output .= '<link rel="stylesheet" type="text/css" href="'.$wgScriptPath.'/extensions/BibTex/bibtex.css" />'."\n";
	  
	  // Writing the beginning of the entry
	  if ( $entry->get_field('author') != '')
	 	 $output .= "<i>".$entry->get_field('author')."</i> - ";
	  else
	  	$output .= "<i>".$entry->get_field('editor')."</i> - ";
		
	  $output .= "<b>";
	  $output .= $entry->get_field('title')."</b><br/>";
	  $output .= '<dl><dd>'.$entry->get_where_published().' ';
	  $output .= $entry->get_publishing_details()."</dd><dd>";
	  // Checking if pdf file is there
	  if ( '' != $entry->get_field('pdf')) {
	       $im = Image::newfromName($entry->get_field('pdf'));	
	       $im2 = Image::newFromName($wbib_pdficon);
	       if ( $this->allowed($im->getTitle()))
	       {
	       		$output .= '<a href="'.$im->getUrl().'"><img src="'.$im2->getURL().'">Pdf</a> ';
	       		array_push($bibtexArray, $entry->get_field('pdf'));
		}
	  }
	  // Checking if ps file is there
          if ( '' != $entry->get_field('ps')) {
	       $im = Image::newfromName($entry->get_field('ps'));
	       $im2 = Image::newFromName($wbib_psicon);
	       if ($this->allowed($im->getTitle()))
	       {
	       		$output .= '<a href="'.$im->getUrl().'"><img src="'.$im2->getURL().'">Postscript</a> ';
	       		array_push($bibtexArray, $entry->get_field('ps'));
		}
	  }
	  // Checking for url
	  if ( '' != $entry->get_field('url')) 
	  {
	       $output .= '<a href="'.$entry->get_field('url').'">Url</a>';	
	  }

	  global $wbibauthor,$wbibtitle,$wbibin,$wbibaddress,$wbibdate;
          //$output .= "<a class=\"bibtex\">".$entry->get_shortName();

	  // This for a div popup demonstration
	  $shouldlink = $wbib_allowdivpopup || ($wbib_allowbibpopup && $wbib_usejavascript) ;
	  if ($shouldlink)
	  {
	       $output .= '<a class="bibtex" href="';
	       if ($wbib_allowbibpopup && $wbib_usejavascript)
	       {
		    $output .= "javascript:bibpopup('".$entry->get_content()."')\">Bibtex";
	       }
	       else
		    $output .= '#">Bibtex';

	       if ($wbib_allowdivpopup)
	       {
		    $output .= "<div>";
		    $output .= "<b>$wbibauthor : </b>".$entry->get_field('author')."<br/>";
		    $output .= "<b>$wbibtitle : </b>".$entry->get_field('title')."<br/>";
		    $output .= "<b>$wbibin : </b>".$entry->get_where_published()." - ".$entry->get_field('page')."<br/>";
		    $output .= "<b>$wbibaddress : </b>".$entry->get_field('address')."<br/>";
		    $output .= "<b>$wbibdate : </b>".$entry->get_field('month')." ".$entry->get_field('year')."<br/>";
		    $output .= "</div>";
	       }
	       $output .= "</a>";
	       if ( '' != $entry->get_field('abstract'))
	       {
		       $output .= '<a href="javascript:abstractpopup('."'".$entry->get_abstract()."')\"> Abstract</a>";
	       }
  	  }
	  $output .= "</dd></dl>";
	  return $output;
     }
}

?>