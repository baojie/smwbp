<?php
/**
 * Print query results in tables for use in Excel.
 * @author Kai (derived from SMWTableResultPrint of Markus Krötzsch)
 */

/**
 * New implementation of SMW's printer for result tables.
 *
 * @note AUTOLOADED
 */
class SMWExcelResultPrinter extends SMWResultPrinter {

	protected function getResultText($res, $outputmode) {
		global $smwgIQRunningNumber;
		SMWOutputs::requireHeadItem(SMW_HEADER_SORTTABLE);

		$cols = array(); //Names of columns

		// print header
		if ('broadtable' == $this->mFormat)
			$widthpara = ' width="100%"';
		else $widthpara = '';
		$result = "<table class=\"smwtable\"$widthpara id=\"querytable" . $smwgIQRunningNumber . "\">\n";
		if ($this->mShowHeaders) { // building headers
			$result .= "\t<tr>\n";
			foreach ($res->getPrintRequests() as $pr) {
				$title = $pr->getTitle();
				if($title instanceof Title)
					array_push($cols, $title);
				else
					array_push($cols, "");
				$result .= "\t\t<th>" . $pr->getText($outputmode, $this->mLinker) . "</th>\n";
			}
			$result .= "\t</tr>\n";
		} else {
			foreach ($res->getPrintRequests() as $pr) {
				$title = $pr->getTitle();
				if($title instanceof Title)
					array_push($cols, $title);
				else
					array_push($cols, "");
			}
		}

		// print all result rows
		while ( $row = $res->getNext() ) {
			$result .= "\t<tr>\n";
			$firstcol = true;
			$gIssues = null;
			$act_column = 0;
			foreach ($row as $field) {
				$result .= "\t\t<td>";
				$first = true;
				
				while ( ($object = $field->getNextObject()) !== false ) {
				
					if ($object->getTypeID() == '_wpg') { // use shorter "LongText" for wikipage
						$text = $object->getLongText($outputmode,$this->getLinker($firstcol));
					} else {
						$text = $object->getShortText($outputmode,$this->getLinker($firstcol));
					}
					if ($first) {
						
						$first = false;
					} else {
						$result .= '<br />';
					}
				
					$result .= $text;
				}
				
				$result .= "</td>\n";
				$firstcol = false;
				$act_column ++;
			}
			$result .= "\t</tr>\n";
		}

		// print further results footer
		if ( $this->linkFurtherResults($res) ) {
			$link = $res->getQueryLink();
			if ($this->getSearchLabel($outputmode)) {
				$link->setCaption($this->getSearchLabel($outputmode));
			}
			$result .= "\t<tr class=\"smwfooter\"><td class=\"sortbottom\" colspan=\"" . $res->getColumnCount() . '"> ' . $link->getText($outputmode,$this->mLinker) . "</td></tr>\n";
		}
		$result .= "</table>\n"; // print footer
		$this->isHTML = ($outputmode == SMW_OUTPUT_HTML); // yes, our code can be viewed as HTML if requested, no more parsing needed
		return $result;
	}

}
