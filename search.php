<?php
session_start();
?>

<HTML>
<?php
include "dbconfig.php";

error_reporting(E_ALL|E_STRICT); //turn on error display
$user_login = $_SESSION["username"];
//$bpassword = $_POST["password"];
$keywords = $_GET["keywords"];
$con = mysqli_connect($host, $username, $password, $dbname)
	or die("<br>Cannot connect to DB:$dbname on $host\n");



//login authentication
$sql= "SELECT login,name,id,DOB,password FROM CPS3740.Customers WHERE login='$user_login'" ;
$result = mysqli_query($con,$sql);



//sql patternmatch keyword
$sql2= "SELECT mid,code,type,amount,name AS Source,mydatetime,note FROM CPS3740_2020F.Money_solanedu,CPS3740.Sources WHERE id=sid AND note like '%{$keywords}%'AND cid IN(select id FROM CPS3740.Customers WHERE login='$user_login' ) " ;

$result2 = mysqli_query($con,$sql2);


//sql show all rows & columns
$sqlAll= "SELECT mid,code,type,amount,name AS Source,mydatetime,note FROM CPS3740_2020F.Money_solanedu,CPS3740.Sources WHERE id=sid AND cid IN(select id FROM CPS3740.Customers WHERE login='$user_login' )";
$resultAll= mysqli_query($con,$sqlAll);

$row = mysqli_fetch_array($result);


if ($keywords =="")
{
	die(" <br> No keyword entered \n");

}
//show all records
elseif($keywords =="*")
{
	$balance=0;


	echo "There are "."<b>". mysqli_num_rows($resultAll)."</b>" ." transactions for customer ". "<b>".$row['name']."</b>"." that matched keyword: "."<b>".$keywords."</b>" ;
	echo "<TABLE border=1>\n";
	//<TH> table header
	echo "<TR><TH>ID<TH>Code<TH>Type<TH>Amount<TH>Source<TH>Date Time<TH>Note\n";
	//loop, store and print rows
	while($row = mysqli_fetch_array($resultAll))
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
			echo "<br><TR><TD>$id<TD>$code<TD>$type<TD><font color=blue>$amount</font><TD>$source<TD>$date<TD>$note\n";		
		}
		else if($type=='W')
		{
			$type="Withdraw";
			//amount withdrawn store as negative
			$amountW=$amount * -1;
			//update total balance
			$balance= $balance+$amountW;
			echo "<br><TR><TD>$id<TD>$code<TD>$type<TD><font color=red>$amountW</font><TD>$source<TD>$date<TD>$note\n";		
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

	mysqli_free_result($result);

}
elseif($result2)
{
		//if customer has records set a balance count
	$balance=0;

	if (mysqli_num_rows($result2) > 0)
	{
		//print # transactions and customer name
		echo "There are "."<b>". mysqli_num_rows($result2)."</b>" ." transactions for customer ". "<b>".$row['name']."</b>"." that matched keyword: "."<b>".$keywords."</b>" ;
		echo "<TABLE border=1>\n";
		//<TH> table header
		echo "<TR><TH>ID<TH>Code<TH>Type<TH>Amount<TH>Source<TH>Date Time<TH>Note\n";
		//loop, store and print rows
		while($row = mysqli_fetch_array($result2))
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
				echo "<br><TR><TD>$id<TD>$code<TD>$type<TD><font color=blue>$amount</font><TD>$source<TD>$date<TD>$note\n";		
			}
			else if($type=='W')
			{
				$type="Withdraw";
				//amount withdrawn store as negative
				$amountW=$amount * -1;
				//update total balance
				$balance= $balance+$amountW;
				echo "<br><TR><TD>$id<TD>$code<TD>$type<TD><font color=red>$amountW</font><TD>$source<TD>$date<TD>$note\n";		
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

		mysqli_free_result($result);

	}else{
		echo "<br>No transactions found for customer ". "<b>".$row['name']."</b>"." with matching keyword: ". "<b>".$keywords."</b>";
	}

}else{
	echo "<br>Something is a wrong in the query. $sql\n";
}




mysqli_close($con);
?>
</HTML>

