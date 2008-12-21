<?php
/**
Author: Jin Guang Zheng, Rui Huang, Jie Bao, Li Ding
Version: 0.1.1
*/

# Define a setup function
$wgExtensionFunctions[] = 'wf_GoogleMapMultiObject_Setup';
# Add a hook to initialise the magic word
$wgHooks['LanguageGetMagic'][]= 'wf_GoogleMapMultiObject_Magic';
 

function wf_GoogleMapMultiObject_Setup() {
        global $wgParser;
        # Set a function hook associating the "example" magic word with our function
        $wgParser->setFunctionHook( 'map_objects', 'wf_GoogleMapMultiObject_Render' );
}
 
function wf_GoogleMapMultiObject_Magic( &$magicWords, $langCode ) {
        # Add the magic word
        # The first array element is case sensitive, in this case it is not case sensitive
        # All remaining elements are synonyms for our parser function
        $magicWords['map_objects'] = array( 0, 'map_objects' );
        # unless we return true, other parser functions extensions won't get loaded.
        return true;
}

/*
*/
function wf_GoogleMapMultiObject_Render( &$parser, $xmldata = 'Empty,1,1',$kml="",//jin
        $zoom = '16	', $type = 'G_HYBRID_MAP', $controls = 'GSmallMapControl', 
        $class = 'pmap', $width = '800', $height = '600', $style = '',$html='' ) {

                global $wgJsMimeType, $wgGoogleMapsKey,$wgLocalPath;
 
                if (!$wgGoogleMapsOnThisPage) {$wgGoogleMapsOnThisPage = 0;}
                $wgGoogleMapsOnThisPage++;

        /*===============================================Import all needed files here====================================================*/
		

		$output .= '<script src="http://maps.google.com/maps?file=api&v=2&key='.$wgGoogleMapsKey.'" type="'.$wgJsMimeType.'"></script>'; //Map key
		
		$output .= "<script src='".$wgLocalPath."/extensions/TetherlessMap/showLocation.js'></script>"; //file need for Semantic Layer
		
		$output .= '<script src="'.$wgLocalPath.'/extensions/TetherlessMap/labeledmarker.js"></script>'; //labeled marker file
		
		$output .= '<script src="'.$wgLocalPath.'/extensions/TetherlessMap/common.js"></script>';  //addLoadEvent function
		
		$output .= '<script src="'.$wgLocalPath.'/extensions/TetherlessMap/GoogleMap.js"></script>'; //create Marker
		
		
		/*======================================================================================================================= */

	    
		/*initialize for GoogleMap.js (for create Marker function)*/
		$output .= "<script>initializeWgs('$wgLocalPath');</script>"; 

		/*
		 * function that create a map
		 */
		$output .= "<script type=\"text/javascript\"> ".
					"var geocoder = new GClientGeocoder ();".
					"function makeMap{$wgGoogleMapsOnThisPage}() ".
					"{ ".
					" if (GBrowserIsCompatible()) {".
					" var map = new GMap2(document.getElementById(\"map{$wgGoogleMapsOnThisPage}\"));inimap(map); ".
					" var point = null; ";
		
		$output .= "point = new GLatLng(42.72993,-73.67661);";
					
		$output .=  " map.setCenter(point, {$zoom}, {$type});".
					" map.addControl(new {$controls}()); ".
					" map.addControl(new GMapTypeControl()); ".
					" ";

		try{

			/* parse data */
			 $xmldata = preg_replace("/&/", "%26", $xmldata);
			 $xmldata = preg_replace("/<br \/>/",";",$xmldata);

			 $data = new SimpleXMLElement($xmldata); 
			
			/*variable needed later*/	
			$locProperty="";
			$locNames="";
			$buildingLinkName;
			$markernum=0;
			
			foreach ($data->tr as $entry)
			{
				
				if (sizeof($entry->td)<3) // we need at least three fields: name, coordinate, page url
					continue;
				
				if (empty($entry->td[0])) // page name
					continue;

				if (empty($entry->td[1])) //coordinate
					continue;

				if (empty($entry->td[2])) // url
					continue;
				
				$property="";				
				if (!empty($entry->td[4]))  //building's property -- generate semantic layer
				    $property=$entry->td[4];
				
				if($property!="")
				{
				$locProperty.=$property.";";
				}
				//now parse the content
				$fullname = $entry->td[0]; //page name
                                $fullname = preg_replace("/%26/","&amp;",$fullname); 

				$pt = $entry->td[1];	//coordinate
				if (strlen(trim($pt))==0 )
					continue;

				$full_url = $entry->td[2]; //url
	                           if (empty($entry->td[3])) $shortname = substr($localname,0,4);
                           		else $shortname = $entry->td[3];

				if (!empty($entry->td[5])){  //image 
					$photo = "<IMG src=\"".$wgLocalPath."/".$entry->td[5]."\" width=100 />";
				}else{
					$photo= "";
				}

				$output .= " point = new GLatLng($pt);";

				
				//generate service information to be display on infowindow of markers
				$parsedproperty=explode(";",$property);
				$html = "<html><a href=\"{$full_url}\">".urldecode($fullname)."</a><br/>".$photo."<br/>";
				for($k=0;$k<sizeof($parsedproperty);$k++)
				{
					if($k==0&&$parsedproperty[$k]!="")
					{
						$html.="<b>Service</b>:";
					}
					if($parsedproperty[$k]!="")
					{	$html.= $parsedproperty[$k].";";
					}
				}
				$html.="";
				
				//create markers, group markers
				$output .= "var marker=new createMarker(point,'$html','$shortname','$property','$fullname');addNewMarkers(marker,'$property');map.addOverlay(marker); ";
				$locNames.=($markernum+1).". <a href='javascript:centerAtMarker($markernum)'>".$fullname."</a><br />";
				
				$markernum++;
			}
		}catch(Exception $e){
			$output .= "Exception in makemap";
		}
		//print out the layer control form
		$output.="printForm('$locProperty','$kml');";
		
		//center at last parsed point
		$output .= " map.setCenter(point, {$zoom}, {$type});";
		

		$output .=  "} ".
					"else { document.write('should show map'); } ".
					"} ".
					"addLoadEvent(makeMap{$wgGoogleMapsOnThisPage});".
					"</script>";
	/*
	lay out of page
	*/
		$output.="<table id='maptable'><tr><td id='aboveMap'></td><td id='property' class=taboff onClick='tabon(1)'>Layers</td><td class=spacer>&nbsp;&nbsp;</td><td id='location' onClick='tabon(2);' class=taboff>Location List</td></tr><tr><td valign='top'>";
		$output .= '<div id="map'.$wgGoogleMapsOnThisPage.'" class="'.$class.'" ';
		$output .= 'style="';
		//if ($width) {$output .= 'width: '.$width.'px; '; }
		//if ($height) {$output .= 'height: '.$height.'px; ';}
		if ($height) {$output .= 'height:'.$height.'px;';}
		if ($width) {$output .= 'width:'.$width.'px;';}
		$output .= $style.'" ></div>';
		$output.="</td><td class='disp' valign='top' colspan=3>";
$output.="<div id='checkform' class=dispoff><div id='numInst'></div><div id='cform'></div></div><div id='locationList' class=dispoff><form>Find Location: <input type='text' id='search' OnKeyUp='queryList()'></form><div id='namelist'>".$locNames."</div></div>";
		$output.="</td></tr></table>";	
		$output.="<script>tabon(2);</script>";
		$k=sizeof($parsedproperty);
		

$output.="<div id='t'></div>";
    return array( $output, noparse => true, isHTML => true );
}
