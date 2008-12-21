<?php
/**

revision log

2008.04.28 Li  fixed a problem when we treat a html table as xml (this is not a good fix but it should work for now)
2008.06.30 Jie add area drawing function

*/

 
# Define a setup function
$wgExtensionFunctions[] = 'wf_GoogleMapArea_Setup';
# Add a hook to initialise the magic word
$wgHooks['LanguageGetMagic'][]= 'wf_GoogleMapMultiObject_Magic';
 

function wf_GoogleMapArea_Setup() {
        global $wgParser;
        # Set a function hook associating the "example" magic word with our function
        $wgParser->setFunctionHook( 'map_area', 'wf_GoogleMapArea_Render' );
}
 
function wf_GoogleMapMultiObject_Magic( &$magicWords, $langCode ) {
        # Add the magic word
        # The first array element is case sensitive, in this case it is not case sensitive
        # All remaining elements are synonyms for our parser function
        $magicWords['map_area'] = array( 0, 'map_area' );
        # unless we return true, other parser functions extensions won't get loaded.
        return true;
}

/*
*/
function wf_GoogleMapArea_Render( &$parser, $data, 
        $zoom = '16	', $type = 'G_HYBRID_MAP', $controls = 'GSmallMapControl', 
        $class = 'pmap', $width = '800', $height = '600', $style = '',$html='' ) {
        
              global $wgJsMimeType, $wgGoogleMapsKey,$wgLocalPath;
 
              if (!$wgGoogleMapsOnThisPage) {$wgGoogleMapsOnThisPage = 0;}
              $wgGoogleMapsOnThisPage++;

		/////////////////////////////////////
		// generate the map: common functions, variables, and content 
		
		$output .= '<script src="http://maps.google.com/maps?file=api&v=2&key='.
				   $wgGoogleMapsKey.'" type="'.$wgJsMimeType.'"></script>';	
              $output .= '<script src="'.$wgLocalPath.'/extensions/TetherlessMap/maparea.js"></script>';			
		  
              //make buttons              
		$output .= '[<a href="#" class="button" onclick="zoomToPoly();return false;">Zoom Fit</a>]&nbsp;';
		$output .= '[<a href="#" class="button" onclick="clearPoly();return false;">Clear</a>]&nbsp;';
		$output .= '[<a href="#" class="button" onclick="pasteData();return false;">Draw Points</a>]&nbsp;';
              $output .= '<br/>';
              $output .= '<table id="descr" border="0" width="100%">';
		$output .= '<tr><td width="20">Area:</td><td id="area"></td><tr>';
		$output .= '<tr><td width="20">Vertices</td><td><textarea rows="2" id="points" cols="18" style="color: #000000; border: 1px solid #fdddbb; padding-left: 4px; padding-right: 4px; padding-top: 1px; padding-bottom: 1px; background-color: #FFFFFF"></textarea></td><tr>';
              $output .= '</table>';
             
              //draw the map
              $output .= '<div id="map'.$wgGoogleMapsOnThisPage.'" class="'.$class.'" ';
		$output .= 'style="';
		if ($width) {$output .= 'width: '.$width.'px; '; }
		if ($height) {$output .= 'height: '.$height.'px; ';}
		$output .= $style.'" ></div>';

		$output .= "<script type=\"text/javascript\"> ".
					"var geocoder = new GClientGeocoder ();".
					"var polyShape;".
					"function makeMap{$wgGoogleMapsOnThisPage}() ".
					"{ ".
					" if (GBrowserIsCompatible()) {".
					" var map = new GMap2(document.getElementById(\"map{$wgGoogleMapsOnThisPage}\")); ".
					
					" map.setCenter(new GLatLng(42.72993,-73.67661), {$zoom}, {$type});".
		                     " map.addControl(new {$controls}()); ".
		       	       " map.addControl(new GMapTypeControl()); ".	
					" ".	
					" ".	
					" GoogleMapAreaSetup(map,'$data');".
					" ".	
					" ".	
			              "} ".
					"else { document.write('should show map'); } ".
					"} ".
					"addLoadEvent(makeMap{$wgGoogleMapsOnThisPage});zoomToPoly();".
					"</script>";
    return array( $output, noparse => true, isHTML => true );		
}
