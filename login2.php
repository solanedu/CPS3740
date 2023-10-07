<?php
session_start();
?>

<HTML>
<?php
include "dbconfig.php";

error_reporting(E_ALL|E_STRICT); //turn on error display
$user_login = $_POST["username"];
$bpassword = $_POST["password"];

$con = mysqli_connect($host, $username, $password, $dbname)
	or die("<br>Cannot connect to DB:$dbname on $host\n");

//login authentication
$sql= "SELECT login,name,id,DOB,password FROM CPS3740.Customers WHERE login='$user_login'" ;
$result = mysqli_query($con,$sql);

//sql age and address
$sql_info="SELECT login,CONCAT(street,', ',city,', ',state,', ',zipcode) AS address, 
		 truncate( DATEDIFF(CURDATE(),DOB)/365,0) AS age FROM CPS3740.Customers WHERE login='$user_login' ";
$result_info = mysqli_query($con,$sql_info);

//sql for money table
$sqlTransact = "SELECT mid,code,type,amount,name AS Source,mydatetime,note FROM CPS3740_2020F.Money_solanedu,CPS3740.Sources WHERE id=sid AND cid IN(select id FROM CPS3740.Customers WHERE login='$user_login' ) order by mid";
$result_Transact= mysqli_query($con,$sqlTransact);



//sql numb of trnasactions
$sql_count = "SELECT count(*) as count from CPS3740_2020F.Money_solanedu where login='$user_login'";
$result_count= mysqli_query($con,$sql_count);





//login authentication
//login empty
if ($user_login =="" ) 
{
	die(" <br> Please enter username. \n");
}
//password empty
if ($bpassword=="")
{
	die(" <br> Please enter password. \n");
}
//if login and password not empty
elseif ($user_login && $bpassword)
{
	//logout link
	?><a href='logout.php'>User logout</a><br><?php

	//ip address
	if (!empty($_SERVER['HTTP_CLIENT_IP']))
	{
		$ip = $_SERVER['HTTP_CLIENT_IP']; 
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	{ 
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else
	{ 
		$ip = $_SERVER['REMOTE_ADDR']; 
	}

	$IPv4= explode(".",$ip);
	//print ip
	echo "<br>Your IP: $ip<br>";

	//print OS/Browser info
	$browserAgent = $_SERVER['HTTP_USER_AGENT'];
	echo "Your browser and OS: $browserAgent";

	//login from kean domain
	if ( $IPv4[0]=="10" || ($IPv4[0]=="131" && $IPv4[1]=="125"))
	{ 
		echo "<br>You are from Kean Unversity.\n"; 
	}
	else 
	{ 
		echo "<br>You are NOT from Kean Unversity.\n"; 
	}

	//store number of rows
	$num=mysqli_num_rows($result);

	if ($num==0)
	{
		echo " <br>Login $user_login doesn't exist in the database \n";
		echo "<br><a href='index.html'>project home page</a>";
	}
	else if ($num>1)
	{
		echo "More than one user login $user_login" ;
	}
	else
	{
			
		$row = mysqli_fetch_array($result);
		$rowInfo = mysqli_fetch_array($result_info);
		//authenticate password
		if ($row['password']==$bpassword)
		{
			//set cookie
			$cookie_name= "Customer_name";
			$cookie_value= $row["name"];
			setcookie($cookie_name, $cookie_value,time()+ 3600, "/");

			$cookie_name2= "Customer_id";
			$cookie_value2= $row["id"];
			setcookie($cookie_name2,$cookie_value2,time()+ 3600, "/");

			//print Customer name, age, address
			echo "<br>Welcome Customer: ". "<b>".$row['name']."</b>";
			echo "<br>Age: ". $rowInfo['age'];
			echo "<br>Address: ".$rowInfo['address']."<br>" ;

			//once password is true, then store login in session to pass to search page
			$_SESSION["username"] = $user_login;




			//Retrieve logged customers image
			$sqlimg = "SELECT img FROM CPS3740.Customers where login = '$user_login'";

			$imgresult = mysqli_query($con, $sqlimg);
			$imgrow = mysqli_fetch_array($imgresult);

			$img= $imgrow['img'] ;
	
			echo '<img src="data:image/jpeg;base64,'.base64_encode($imgrow['img']).'"/>'; 

			echo "<br><hr>";



			if($result_Transact)
			{
				//if customer has records set a balance count
				$balance=0;

				if (mysqli_num_rows($result_Transact) > 0)
				{
					//print # transactions and customer name
					echo "There are "."<b>". mysqli_num_rows($result_Transact)."</b>" ." transactions for customer: ". "<b>".$row['name']."</b>";
					echo "<TABLE border=1>\n";
					//<TH> table header
					echo "<TR><TH>ID<TH>Code<TH>Type<TH>Amount<TH>Source<TH>Date Time<TH>Note\n";
					//loop, store and print rows
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
					echo "<br>No record found for customer: ". "<b>".$row['name']."</b>";
				}

			}
			else
			{
				die("<br>Something wrong in the query. <br/> ");
			}

				//4 additional functions PJ2
				?>
     				<br><br><TABLE border=0>
     				<TR><TD><form action="add_transaction.php" method="post">
     				<input type='hidden' name="balance" value='<?php echo $balance ?>' >
     				<input type='submit' value='Add transaction'></form>
     				<TD><a href='display_transaction.php'>Display and update transaction</a>
    				&nbsp;&nbsp;  <a href="display_stores.php" targt=_blank>Display stores</a>
     				<TR><TD colspan=2><form action="search.php" method="get">
     				Keyword: <input type="text" name="keywords"  required="required" >
     				<input type="submit" value="Search transaction"></form>

     				</TABLE>

     			<?php

		}
		else 
		{
			echo "<br>Login $user_login exists, but password does not match.";
			echo "<br><a href='index.html'>project home page</a>";
			die();
 
		}
	}	
}

mysqli_close($con);
?>
</HTML>
