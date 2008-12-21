// Jie Bao

var GoogleMapToolsBase = "http://map.rpi.edu/extensions/TetherlessMap/";


function addLoadEvent2(func) 
{ 
  var oldonload = window.onload;  
  if (typeof oldonload == 'function') 
  { 
       window.onload = function() 
       { 
            oldonload();  func(); 
       }; 
  } 
  else 
  {  
       window.onload = func;  
  } 
}  

function addIcon(icon){ // Add icon attributes

 icon.iconSize = new GSize(11, 11);
 icon.dragCrossSize = new GSize(0, 0);
 icon.shadowSize = new GSize(11, 11);
 icon.iconAnchor = new GPoint(5, 5);
// icon.infoWindowAnchor = new GPoint(5, 1);
}

//////////// Class GoogleMapArea /////////////

var GoogleMapAreaMarker = null;
var GoogleMapAreaMap  = null;
var GoogleMapAreaPolyPoints = null;
var polyLineColor = "#3355ff";
var polyFillColor = "#335599";
var polyShape = null;
var areaReport = null;
var allpointsCtl = null;

// Square marker icons
var square = new GIcon();square.image = GoogleMapToolsBase+ "square.png"; addIcon(square);


function drawPoly2() {

 GoogleMapAreaPolyPoints = new Array();
 for(i = 0; i < GoogleMapAreaMarker.length; i++) {
  GoogleMapAreaPolyPoints.push(GoogleMapAreaMarker[i].getLatLng());
 }
 this.drawPoly();
}

function GoogleMapAreaSetup(map,data)
{
       // Global variables    
       GoogleMapAreaMarker = new Array();       
       GoogleMapAreaPolyPoints = new Array();
       GoogleMapAreaMap = map;
       GoogleMapAreaMap.disableDoubleClickZoom();
       GEvent.addListener(GoogleMapAreaMap, "click", leftClick);
       areaReport = document.getElementById("area");
       allpointsCtl = document.getElementById("points");
       
       // parse initial data
       if (data)
       { 
       	   //alert(data);
       	   allpointsCtl.value = data;
       	   pasteData();       	   
       }
}

function drawPoly() {

 if(polyShape) GoogleMapAreaMap.removeOverlay(polyShape);
 GoogleMapAreaPolyPoints.length = 0;
 var allPoints="";	

 for(i = 0; i < GoogleMapAreaMarker.length; i++) {
  GoogleMapAreaPolyPoints .push(GoogleMapAreaMarker [i].getLatLng());
  allPoints += GoogleMapAreaMarker[i].getLatLng()+";";
 }
 
 // Close the shape with the last line or not
 GoogleMapAreaPolyPoints.push(GoogleMapAreaMarker[0].getLatLng());
 
 polyShape = new GPolygon(GoogleMapAreaPolyPoints, polyLineColor, 3, .8, polyFillColor,.3);
 var unit = " km&sup2;";
 var area = polyShape.getArea()/(1000*1000);

 if(GoogleMapAreaMarker.length <= 2 ) {
  areaReport.innerHTML = "&nbsp;";
 }
 else if(GoogleMapAreaMarker.length > 2 ) { 
  areaReport.innerHTML = area.toFixed(3)+ unit;
 }
 
 allpointsCtl.value = allPoints;

 GoogleMapAreaMap.addOverlay(polyShape);
}

function leftClick(overlay, point){

 if(point) {

  // Make markers draggable
  var marker =new GMarker(point, {icon:square, draggable:true, bouncy:false, dragCrossMove:true});
  
  var xy = marker.getLatLng();
  
  // find the closest point
  var minDis = 9999999999999;
  var minIndex = -1;
  for(var n = 0; n < GoogleMapAreaMarker.length; n++) {
    var dis = xy.distanceFrom(GoogleMapAreaMarker[n].getLatLng()); 
    if(dis < minDis ) 
    {   minDis = dis; minIndex = n;  }
  }
  
  // insert it into the list of all markers
  if (minIndex == -1)
    GoogleMapAreaMarker.push(marker);
  else // insert
  {
      GoogleMapAreaMarker.push(marker);
      for (var n = GoogleMapAreaMarker.length -1; n>minIndex; n--)
      {
          GoogleMapAreaMarker[n]=GoogleMapAreaMarker[n-1];
      }
      GoogleMapAreaMarker[minIndex+1]=marker;
  }    

  addMarker(marker);

  drawPoly2();
 }
}

function addMarker(marker)
{

  GoogleMapAreaMap.addOverlay(marker);
  GEvent.addListener(marker, "drag", function() {
   drawPoly();
  });

  GEvent.addListener(marker, "mouseover", function() {
    marker.setImage(GoogleMapToolsBase+"m-over-square.png");
  });

  GEvent.addListener(marker, "mouseout", function() {
   marker.setImage(GoogleMapToolsBase+"square.png");
  });

  // Double click listener to remove the square
  GEvent.addListener(marker, "dblclick", function() {
  // Find out which square to remove
  for(var n = 0; n < GoogleMapAreaMarker.length; n++) {
   if(GoogleMapAreaMarker[n] == marker) {
    GoogleMapAreaMap.removeOverlay(GoogleMapAreaMarker[n]);
    break;
   }
  }
  GoogleMapAreaMarker.splice(n, 1);
  drawPoly();
  });
}

function zoomToPoly() {

 if(polyShape && GoogleMapAreaPolyPoints.length > 0) {
  var bounds = polyShape.getBounds();
  GoogleMapAreaMap.setCenter(bounds.getCenter());
  GoogleMapAreaMap.setZoom(GoogleMapAreaMap.getBoundsZoomLevel(bounds));
 }
}

function clearPoly() {

 // Remove polygon and reset arrays
 GoogleMapAreaMap.clearOverlays();
 GoogleMapAreaPolyPoints.length = 0;
 GoogleMapAreaMarker.length = 0;
 areaReport.innerHTML = "&nbsp;";
}

function pasteData() {

 // Remove polygon and reset arrays
 clearPoly();
 
 //get data
 var data = allpointsCtl.value;
 //alert(data);
 
 // parse data
 splitValues = data.split(";");
 for (var n = 0; n < splitValues.length; n++)
 {
     var value = splitValues[n];
     //alert(value);
     if (value.length<=1) continue;
     var p1 = value.indexOf("(");
     var p2 = value.indexOf(",");
     var p3 = value.lastIndexOf(")");
     var lat = value.substring(p1+1,p2);
     var lng = value.substring(p2+1,p3);
     //alert(lat + ' == ' + lng);
     var marker =new GMarker(new GLatLng(lat,lng), {icon:square, draggable:true, bouncy:false, dragCrossMove:true});
     GoogleMapAreaMarker.push(marker);
     addMarker(marker);
     //break;
 }
 
 drawPoly();

}

function save()
{
	alert("save");	
}

//////////// END Class GoogleMapArea /////////////