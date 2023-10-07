<?php
session_start();
?>

<HTML>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      #map-canvas {
        height: 600px;
        margin: 0px;
        padding: 0px
      }
    </style>
<?php
include "dbconfig.php";
$con = mysqli_connect($host, $username, $password, $dbname)
	or die("<br>Cannot connect to DB:$dbname on $host\n");

error_reporting(E_ALL|E_STRICT); //turn on error display

//username from session
$user_login = $_SESSION["username"];

$sql = "SELECT * from CPS3740.Stores where Name is not Null and zipcode is not Null and state is not null and city is not Null and address is not Null and latitude is not Null and longitude is not Null";

$result =mysqli_query($con,$sql);

if (mysqli_num_rows($result) >0)
{
	echo "<TABLE border=1>";
	//print stores in database
	echo "<b>The following stores are in the database. </b><br/>";
	echo "<TR><TD>ID<TD>Name<TD>Address<TD>City<TD>State<TD>Zipcode<TD>Location(Latitude,Lognitude)";

	while($row = mysqli_fetch_array($result))
	{

		$id = $row["sid"];
		$name=$row["Name"];
		$zip=$row["Zipcode"];
		$state=$row["State"];
		$city=$row["city"];
		$address=$row["address"];
		$latitude=$row["latitude"];							
		$longitude=$row["longitude"];

		$loc[]=array('id'=>$id, 'name'=>$name,'lat'=>$latitude, 'lng'=>$longitude, 'add'=>$address, 'city'=>$city,'state'=>$state, 'zip'=>$zip);							
						

		echo "<br><TR><TD>$id<TD>$name<TD>$address<TD>$city<TD>$state<TD>$zip<TD>$latitude , $longitude";	
	}
	echo"</TABLE>";

?>
 <head>

    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3"></script>

     

    <script>


    var i = 0;

    function initialize() {
        var mapOptions = {
                zoom: 4,

                center: new google.maps.LatLng(41.052053,-79.181157666667),
                mapTypeId: google.maps.MapTypeId.ROADMAP
       };

       var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

       var infowindow = new google.maps.InfoWindow();

	var markerIcon = {
  		scaledSize: new google.maps.Size(80, 80),
		  origin: new google.maps.Point(0, 0),
		  anchor: new google.maps.Point(32,65),
		  labelOrigin: new google.maps.Point(40,33)
	};
        var location;
        var mySymbol;
        var marker, m;







	var MarkerLocations = [
		<?php 
			for($i=0;$i<sizeof($loc);$i++) 
			{
				$j=$i+1;?>
				[
					<?php echo "'" . $loc[$i]['name'] . "'";?>, 
					<?php echo $loc[$i]['lat'];?>, 
					<?php echo $loc[$i]['lng']; ?>,
					<?php echo "'" . $loc[$i]['add'] . "'";?>,
					<?php echo "'" . $loc[$i]['city'] . "'";?>,
					<?php echo "'" . $loc[$i]['state'] . "'";?>,
					<?php echo "'" . $loc[$i]['zip'] . "'";?>
				]
				<?php if($j!=sizeof($loc)) echo ","; 
			} ?>
		];








for (m = 0; m < MarkerLocations.length; m++) {

        location = new google.maps.LatLng(MarkerLocations[m][1], MarkerLocations[m][2]),
        marker = new google.maps.Marker({ 
	    map: map, 
	    position: location, 
	    icon: markerIcon,	
	    label: {
	   	text: MarkerLocations[m][0] ,
		color: "black",
    		fontSize: "12px",
    		fontWeight: "bold"
	    }
	});

      google.maps.event.addListener(marker, 'click', (function(marker, m) {
        return function() {
          infowindow.setContent("Store Name: " + MarkerLocations[m][0] + "<br>" + MarkerLocations[m][3] + ", " + MarkerLocations[m][4] + ", " + MarkerLocations[m][5] + " " + MarkerLocations[m][6]);
          infowindow.open(map, marker);
        }
      })(marker, m));
 }
}
  google.maps.event.addDomListener(window, 'load', initialize);;


	/*
	for (m = 0; m < MarkerLocations.length; m++) 
	{
		location = new google.maps.LatLng(MarkerLocations[m][1],
		MarkerLocations[m][2]);
		marker = new google.maps.Marker({
			map: map,
			position: location,
			icon: markerIcon,
			label: {
				text: MarkerLocations[m][0] ,
				color: "black",
				fontSize: "16px",
				fontWeight: "bold"
			}
		});
	}
	*/	


	</script>
	</head>
<?php

}


?>
</head>
<div id="map-canvas" style="height: 400px; width: 720px;"></div>

</HTML>