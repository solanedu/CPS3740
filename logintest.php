<?php
include "dbconfig.php";
$con=mysqli_connect($host,$username,$password,$dbname);

$login=mysqli_real_escape_string($con,$_POST['login']);
$bpassword=mysqli_real_escape_string($con,$_POST['password']);
$sql= "SELECT login FROM CPS370.Users WHERE login='$login'AND
password='$bpassword'";
echo "<br>$sql\n";
$result = mysqli_query($con, $sql);
$num=mysqli_num_rows($result);
if ($num>0) {
	echo "<br>Login successfully!\n";
} else {
	echo "<br>Login failed!\n";
}
mysqli_close($con);
?>