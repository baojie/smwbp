<?php
 
# Define a setup function
$wgExtensionFunctions[] = 'wf_GoogleMapClick_Setup';

function wf_GoogleMapClick_Setup() {
        global $wgParser;
        # Set a function hook associating the "example" magic word with our function
        $wgParser->setFunctionHook( 'GoogleMapClick', 'wf_GoogleMapClick_Render' );
}


# Add a hook to initialise the magic word
$wgHooks['LanguageGetMagic'][]       = 'wf_GoogleMapClick_Magic';
 
function wf_GoogleMapClick_Magic( &$magicWords, $langCode ) {
        # Add the magic word
        # The first array element is case sensitive, in this case it is not case sensitive
        # All remaining elements are synonyms for our parser function
        $magicWords['GoogleMapClick'] = array( 0, 'GoogleMapClick' );
        # unless we return true, other parser functions extensions won't get loaded.
        return true;
}

# Set the actionable code with no input parameters
function wf_GoogleMapClick_Render( &$parser, $coordinates = '1,1', 
        $center='42.72,-73.68', $zoom = '16', $type = 'G_HYBRID_MAP', 
        $class = 'pmap', $width = '800', $height = '600', $style = '' ) {
        # The parser function itself
        # The input parameters are wikitext with templates expanded
        # The output is not parsed as wikitext	
		global $wgJsMimeType, $wgGoogleMapsKey;

		if (!$wgGoogleMapsOnThisPage) {$wgGoogleMapsOnThisPage = 0;}
            $wgGoogleMapsOnThisPage++;

			// add the standard load/unload functions and variables
			$output .= '<script src="http://maps.google.com/maps?file=api&v=2&key='.
					   $wgGoogleMapsKey.'" type="'.$wgJsMimeType.'"></script>';
			$output .= "<script type=\"text/javascript\"> ".
					   
					   " function addLoadEvent(func) { ".
					   "    var oldonload = window.onload;  ".
					   "    if (typeof oldonload == 'function') ".
					   "        {  window.onload = function() {  oldonload();  func();  };  } ".
					   "    else {  window.onload = func;  }  ".
					   " }  ".
					   
					   "var geocoder = new GClientGeocoder ();".
					   " window.unload = GUnload;</script>";

			//add standard canvas for layout the map
		    $output .= '<div id="map'.$wgGoogleMapsOnThisPage.'" class="'.$class.'" ';
			$output .= 'style="';
			if ($width) {$output .= 'width: '.$width.'px; '; }
			if ($height) {$output .= 'height: '.$height.'px; ';}
			$output .= $style.'" ></div>';
                
			// add the specific makeMap function
			$output .= "<script type=\"text/javascript\"> ".
						"function makeMap{$wgGoogleMapsOnThisPage}() ".
						"{ ".
						"   if (GBrowserIsCompatible())  ".
						"	{  var map = new GMap2(document.getElementById(\"map{$wgGoogleMapsOnThisPage}\")); ".
						"     map.setCenter(new GLatLng({$center}), {$zoom} ,{$type}); ".

						"     GEvent.addListener(map,\"click\", function(overlay,latlng) {      ".
						"       var myHtml = \"The latLong value is: \" + latlng.toUrlValue(5) + \" at zoom level \" + map.getZoom(); ".
						"       map.openInfoWindow(latlng, myHtml); ".
						"     }); ".
						"     map.addControl(new GSmallMapControl()); ".
						"     map.addControl(new GMapTypeControl()); ".
						"  } ".
						"  else ".
						"  { document.write('should show map'); } ".
						"} ".
						
						"addLoadEvent(makeMap{$wgGoogleMapsOnThisPage});".
						"</script>";

			return array( $output, noparse => true, isHTML => true );
}
