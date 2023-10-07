<?php
session_start();
?>

<HTML>
<?php
include "dbconfig.php";
$con = mysqli_connect($host, $username, $password, $dbname)
	or die("<br>Cannot connect to DB:$dbname on $host\n");

error_reporting(E_ALL|E_STRICT); //turn on error display
//gets balance from login2 form
$balance = $_POST["balance"];
//username from session
$user_login = $_SESSION["username"];



//customer name
$sql= "SELECT login,name,id,DOB,password FROM CPS3740.Customers WHERE login='$user_login'" ;
$result = mysqli_query($con,$sql);


$sql2= "SELECT id,name from CPS3740.Sources";
$result2= mysqli_query($con,$sql2);

$row = mysqli_fetch_array($result);


?>

<a href='logout.php'>User logout</a><br>
<br>
<font size=4><b>Add Transaction</b></font>
<br>
<?php
echo "<b>". $row['name']."</b>"." current balance is "."<b>".$balance."</b>";

?>



<form name="input" action="insert_transaction.php" method="post" required="required">
<input type='hidden' name="balance" value='<?php echo $balance ?>' >

Transaction code: <input type="text" name="code"required="required">

<br><input type='radio' name="trs" value='D'>Deposit
<input type='radio' name="trs" value='W'3>Withdraw

<br> Amount: <input type="text" name="amount" required="required"><input type='hidden' name='balanceee' value=795'>

<br>Select a Source: <SELECT name='source_id'>


<?php
//insert blank option first
echo "<option value='" .''. "' > "."</option>";
//gets data stores it into drop down list
while ($row2 = mysqli_fetch_array($result2)){
echo "<option value='". $row2['id'] ."'>" .$row2['name'] ."</option>" ;
}
echo "</select>";
?>

<!--

<option value=1>ATM</option>
<option value=2>Online</option>
<option value=3>Branch</option>
<option value=4>Wired</option>
<option value=5>New3</option>
</SELECT>
-->




<BR>Note: <input type='text' name='note'>
<br>
<input type='submit' value='Submit'>
</form>

</form>



</HTML>



