<?php
session_start();

include "dbconfig.php";

$con = mysqli_connect($host, $username, $password, $dbname)
	or die("<br>Cannot connect to DB:$dbname on $host\n");

error_reporting(E_ALL|E_STRICT); //turn on error display
$user_login = $_SESSION["username"];

//money table
$sqlTransact = "SELECT mid,code,type,amount,name AS Source,mydatetime,note FROM CPS3740_2020F.Money_solanedu,CPS3740.Sources WHERE id=sid AND cid IN(select id FROM CPS3740.Customers WHERE login='$user_login' ) order by mid";
$result_Transact= mysqli_query($con,$sqlTransact);


?>
<HTML>

	<a href='logout.php'>User logout</a><br>
	You can only udpdate <b>Note</b> column.<br>

	<form action='update_transaction.php' method='post'>
	<TABLE border=1>

	<?php
	$balance=0;
	echo "<TR><TH>ID<TH>Code<TH>Type<TH>Amount<TH>Source<TH>Date Time<TH>Note<TH>Delete";
		while($row = mysqli_fetch_array($result_Transact))
		{
			$id = $row["mid"];
			$code=$row["code"];
			$type=$row["type"];
			$amount=$row["amount"];
			$source=$row["Source"];
			$date=$row["mydatetime"];
			$note=$row["note"];							

			if($type=='D')
			{
				$type="Deposit";
				//calc balance
				$balance = $amount + $balance;
				echo "<br><TR><TD><input type='text' name='id' value='$id' readonly><TD>$code<TD>$type<TD><font color=blue>$amount</font><TD>$source<TD>$date<td bgcolor='yellow'><input type='text' name='note[]' value='$note' style='background-color:yellow;'><TD><input type='checkbox' name='checkbox[]' value='$id'><TD> ";		
			}
			else if($type=='W')
			{
				$type="Withdraw";
				//amount withdrawn store as negative
				$amountW=$amount * -1;
				//update total balance
				$balance= $balance+$amountW;
				echo "<br><TR><TD><input type='text' name='id' value='$id' readonly><TD>$code<TD>$type<TD><font color=red>$amountW</font><TD>$source<TD>$date<td bgcolor='yellow'><input type='text' name='note[]' value='$note' style='background-color:yellow;'><TD><input type='checkbox' name='checkbox[]' value='$id'><TD> ";
			}

		

		}
		
		echo "</TABLE>\n";

		//format money 
		setlocale(LC_MONETARY, 'en_US.UTF-8');
					
		//if balance is negative red else blue
		if($balance < 0)
		{
			echo "Total balance: "."<font color=red>". money_format('%.2n', $balance) . "</font>";	
			}else{
				echo "Total balance: "."<font color=blue>". money_format('%.2n', $balance) . "</font>";	
			}			

			mysqli_free_result($result_Transact);
	?>
	<br><input type='submit' name='delete' id="delete" value='Update transaction'>
	
	</form>
<?php
mysqli_close($con);
?>

</HTML>
