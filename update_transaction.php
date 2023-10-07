<?php
session_start();
?>
<HTML>
<a href='logout.php'>User logout</a><br>
<?php

include "dbconfig.php";

$con = mysqli_connect($host, $username, $password, $dbname)
	or die("<br>Cannot connect to DB:$dbname on $host\n");

error_reporting(E_ALL|E_STRICT); //turn on error display
$user_login = $_SESSION["username"];

if(isset($_POST['checkbox']))
{
	$chk= $_POST['checkbox'];
	foreach($chk as $id)
	{

		mysqli_query($con,"DELETE from CPS3740_2020F.Money_solanedu where mid=".$id);
	}
	//header("location:display_transaction.php");
	echo "Succesfully deleted record. <br/>";
}elseif(isset($_POST['note']))
{
	//$chk= $_POST['note'];
	
	//	mysqli_query($con,"UPDATE CPS3740_2020F.Money_solanedu set note='$chk' where note !='$chk' and mid='$_POST[mid]'");
	
	//header("location:display_transaction.php");
	echo "Update Note function under construction.";
}


mysqli_close($con);

?>
</HTML>