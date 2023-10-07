<?php
$gender=$_GET['gender'];

if (empty($_POST["gender"])) {
 $genderErr = "<br>Gender is required.\n";
} else {
	$gender = $_POST["gender"];
}

include "dbconfig.php";

$con = mysqli_connect($host, $username, $password, $dbname)
	or die("<br>Cannot connect to DB:$dbname on $host\n");

$sql= "SELECT staffno as sno,fname,lname, salary FROM dreamhome.Staff where sex='$gender'";

//send your query to your database 
$result = mysqli_query($con, $sql);
//comment debug statement so user doesnt see
//echo "<br>query: $sql\n";

if($result)
{

	if (mysqli_num_rows($result) > 0)
	{
		echo "<br>The following customers are in the database.";
		echo "<TABLE border=1>\n";
		//<TH> table header
		echo "<TR><TH>Staffno<TH>First Name<TH>Last Name<TH>Salary\n";
		while($row = mysqli_fetch_array($result))
		{
			$sno = $row["sno"];
			$fname=$row["fname"];
			$lname=$row["lname"];
			$salary=$row["salary"];
		//if first name is emyty dont display record
		// <> return true if fname != ""
			if ($fname <>"") 	
				echo "<br><TR><TD>$sno<TD>".$row['fname']. "<TD>$lname<TD>$salary\n";
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