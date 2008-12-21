<?php
/*
This file is used to display individual location on google map
wiki function hook "insert_map" defined here
*/ 

# Define a setup function
$wgExtensionFunctions[] = 'wfInsert_MapFunction_Setup';
# Add a hook to initialise the magic word
$wgHooks['LanguageGetMagic'][]       = 'wfInsert_MapFunction_Magic';


function wfInsert_MapFunction_Setup() {
        global $wgParser;
        # Set a function hook associating the "example" magic word with our function
        $wgParser->setFunctionHook( 'insert_map', 'wfInsert_MapFunction_Render' );
}
 
function wfInsert_MapFunction_Magic( &$magicWords, $langCode ) {
        # Add the magic word
        # The first array element is case sensitive, in this case it is not case sensitive
        # All remaining elements are synonyms for our parser function
        $magicWords['insert_map'] = array( 0, 'insert_map' );
        # unless we return true, other parser functions extensions won't get loaded.
        return true;
}
 
function wfInsert_MapFunction_Render( &$parser, $coordinates = '1,1', 
        $zoom = '2', $type = 'G_HYBRID_MAP', $controls = 'GSmallMapControl', 
        $class = 'pmap', $width = '800', $height = '600', $style = '',$html='',$name='' ) {

        global $wgJsMimeType, $wgGoogleMapsKey,$wgLocalPath;
 
        if (!$wgGoogleMapsOnThisPage) {$wgGoogleMapsOnThisPage = 0;}
            $wgGoogleMapsOnThisPage++;

        $coordinates = preg_replace('/\[\[.*\]\]/U', '', $coordinates);
                        
		
		$points = explode(";", $coordinates);  
        
		/*=======================================File and Keys import here=================================================================*/
        $output .= '<script src="http://maps.google.com/maps?file=api&v=2&key='.$wgGoogleMapsKey.'" type="'.$wgJsMimeType.'"></script>';
        
		$output .= '<script src="'.$wgLocalPath.'/extensions/TetherlessMap/labeledmarker.js"></script>';
		
		/*===============================================================================================================================*/
		
		//function to create a labeled marker
		$output .= "<script type=\"text/javascript\"> ".

				   "function createMarker(point, label,name) { ".

				   "var icon = new GIcon();".
				   "icon.image = '".$wgLocalPath."/extensions/TetherlessMap/MarkerIcon/greencirclemarker.png';".
				   "icon.iconSize = new GSize(16, 16);".
				   "icon.iconAnchor = new GPoint(8, 8);".
				   "icon.infoWindowAnchor = new GPoint(12, 7);".
				   "opts = { \"icon\": icon,".  
                                         "\"clickable\": true,".
					 "  \"labelText\": name,".
					 "  \"labelOffset\": new GSize(0, 0)};".

				   " var marker = new LabeledMarker(point,opts);  ".
				   " GEvent.addListener(marker, 'click', function()".
                                   "  { marker.openInfoWindowHtml(label); }); ".
				   "  return marker;  } ". 
				   
				   " function addLoadEvent(func) { ".
					  " var oldonload = window.onload;  ".
					  "if (typeof oldonload == 'function') ".
					  " {  window.onload = function() {  oldonload();  func();  };  } ".
					  "else {  window.onload = func;  }  }  ".
				   "   window.unload = GUnload;" .
				   " </script>";
            
		        //create map and initialize map
                $output .= '<div id="map'.$wgGoogleMapsOnThisPage.'" class="'.$class.'" ';
                $output .= 'style="';
                if ($width) {$output .= 'width: '.$width.'px; '; }
                if ($height) {$output .= 'height: '.$height.'px; ';}
                $output .= $style.'" ></div>';
                $output .= "<script type=\"text/javascript\"> ".
                            "var geocoder = new GClientGeocoder ();".
                            "function makeMap{$wgGoogleMapsOnThisPage}() ".
                            "{ ".
                            " if (GBrowserIsCompatible()) {".
                            " var map = new GMap2(document.getElementById(\"map{$wgGoogleMapsOnThisPage}\")); ".
                            " var point = null; ";
                
                $output .= "point = new GLatLng($points[0]);";
                            
                $output .=  " map.setCenter(point, {$zoom}, {$type});".
                            " map.addControl(new {$controls}()); ";
                            " map.addControl(new GMapTypeControl()); ".
                            " ";
				//add marker to the map
                foreach ($points as $pt)
                {
					if (strlen(trim($pt))>0 )
					{ $output .= " point = new GLatLng($pt);";

	                  $temp = html_entity_decode($html);	

						$output .= " map.addOverlay(new createMarker(point,'$temp','$name')); ";
                    }

              	
				}  
                $output .=  "} ".
                            "else { document.write('should show map'); } ".
                            "} ".
                            "addLoadEvent(makeMap{$wgGoogleMapsOnThisPage});".
                            "</script>";

                return array( $output, noparse => true, isHTML => true );
 
}
