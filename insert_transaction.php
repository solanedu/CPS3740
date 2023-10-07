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
//gets balance from login2 form
$balance = $_POST["balance"];
//username from session
$user_login = $_SESSION["username"];


//sql get cid
$sqlCid = "SELECT id from CPS3740.Customers where login='$user_login' ";
$resultCid = mysqli_query($con,$sqlCid);

$row = mysqli_fetch_array($resultCid);

//store users cid
if($resultCid)
{
	$cid = $row["id"];
}


//store amount from add_transactions page
$amount = $_POST["amount"];

//is D or W checked
if (!isset($_POST['trs']) )
{
    die("Please select Deposit or Withdraw.");
}
else
{
	//store deposit or withdraw
	$transaction = $_POST["trs"];

	//is source set
	if (!isset($_POST['source_id']) )
	{
   		die("Please select a Source.");
	}else
		//stores source id value
		$source = $_POST["source_id"]; 
		//is source empty
		if ($source =="")
		{
			die("Please select a Source.");
		}
		else
		{

		//check if amount input is a number
		//if not then sets it to 0
		if (is_numeric($amount))
		{
			
			$amountNum=$amount + 0;

			//check if num is negative
			if($amountNum <=0)
			{
				die("Please enter a valid and positive number for amount.");
			}
			else
			{
				//check if transaction code is set
				if (!isset($_POST['code']) )
				{
    				die("Please enter a transaction code");
				}
				else
				{
					$code = $_POST['code'];


 					//sql patternmatch code
					$sqlCode= "SELECT mid,code,type,amount,name AS Source,mydatetime,note FROM CPS3740_2020F.Money_solanedu,CPS3740.Sources WHERE id=sid AND code like '%{$code}%'AND cid IN(select id FROM CPS3740.Customers WHERE login='$user_login' ) " ;

					$resultCode = mysqli_query($con,$sqlCode);

					if ($code == '')
					{
						die("Transaction code can not be empty.");
					}
					else
					{
						//if sqlCode returns more than 0 rows than code is duplicate
						if (mysqli_num_rows($resultCode) > 0)
						{
							die("Error! Transaction is in the database already.");
						}
						else
						{
							//is note set, its not required
							if (!isset($_POST['note']) )
							{
								//if not set -> set it to empty
    							$note="";
							}
							else
							{
								$note= $_POST['note'];
							}

							//set default timezone to NY est
							date_default_timezone_set('America/New_York');
							$date = date('Y-m-d H:i:s');


							// insert into table
							$sqlInsert="INSERT into CPS3740_2020F.Money_solanedu (code,cid,sid,type,amount,mydatetime,note) 
							VALUES ('$code','$cid','$source','$transaction','$amountNum','$date','$note' )";

							if(mysqli_query($con, $sqlInsert))
							{

   								echo "Transaction inserted successfully. <br/>";
   								//already defined
   								//use $transaction= D or W 
   								//use $amountNum -> how much customer deposited or withdre
	
								if($transaction=='D')
								{
									$type="Deposit";
									//calc balance
									$balance = $amountNum + $balance;

								}
								else if($transaction=='W')
								{
									$type="Withdraw";
									//amount withdrawn store as negative
									$amountW=$amountNum * -1;
									//update total balance
									$balance= $balance+$amountW;
								}		
									
								//format money 
								setlocale(LC_MONETARY, 'en_US.UTF-8');
					
								//if balance is negative red else blue
								if($balance < 0)
								{
									echo "New balance: "."<font color=red>". money_format('%.2n', $balance) . "</font>";	
								}else{
									echo "New balance: "."<font color=blue>". money_format('%.2n', $balance) . "</font>";	
								}
							}else{
    							echo "ERROR: Could not able to execute $sqlInsert. " . mysqli_error($con);
							}
						}
					}
				}
			}
		}
		else
		{
			$amountNum= 0;
			
			//amount is empty, negative, or a string
			if($amountNum <= 0)
				die("Please enter a valid and positive number for amount.");
		}	
	}
}

?>
