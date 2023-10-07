<?php
include "dbconfig.php";

$con = mysqli_connect($host, $username, $password, $dbname)
	or die("<br>Cannot connect to DB:$dbname on $host\n");

$sql= "SELECT id,login,password,name,gender,DOB,street,city,state,zipcode FROM CPS3740.Customers";

//send your query to your database 
$result = mysqli_query($con, $sql);

//comment debug statement so user doesnt see
//echo "<br>query: $sql\n";

if($result) 
{

	if (mysqli_num_rows($result) > 0)
	{
		echo "<br>The following customers are in the bank system:";
		echo "<TABLE border=1>\n";
		//<TH> table header
		echo "<TR><TH>ID<TH>login<TH>password<TH>Name<TH>Gender<TH>DOB<TH>Street<TH>City<TH>State<TH>Zipcode\n";
		while($row = mysqli_fetch_array($result))
		{
			$id=$row["id"];
			$login=$row["login"];
			$password=$row["password"];
			$name=$row["name"];
			$gender=$row["gender"];
			$DOB=$row["DOB"];
			$street=$row["street"];
			$city=$row["city"];
			$state=$row["state"];
			$zipcode=$row["zipcode"];

			echo "<br><TR><TD>$id<TD>$login<TD>$password<TD>$name<TD>$gender<TD>$DOB
				  <TD>$street<TD>$city<TD>$state<TD>$zipcode\n";

		}
		echo "</TABLE>\n";
		mysqli_free_result($result);
	}else{
		echo "<br>No record found.\n";
	}
}else{
	echo "<br>Something wrong in the query. $sql\n";
}

mysqli_close($con);
?>