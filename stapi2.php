<?php

$status = 'OK';
$response = null;
$error = 0;
$company = null;
$requeststatus = 0;
$answer = "No Companies Passed from BaseBid check" ;
$winnercompany = null;
$winner = null;

$budget = 0;
$bid = 0;
$items = array(); //   Response JSON

  $servername = "localhost";
  $username = "cp62865_st";
  $password = "st0ck";
  $dbname= "cp62865_st";

// parcing URL countrycode=US&Category=Automobile&BaseBid=10

if(!isset($_GET['countrycode'])){ // если не получили параметр
     die("ERROR URL No countrycode");
}else{
    $country = $_GET['countrycode'];
}  

if(!isset($_GET['Category'])){ // если не получили параметр
    die("ERROR URL No Category");
}else{
    $category = $_GET['Category'];
} 

if(!isset($_GET['BaseBid'])){ // если не получили параметр

    die("ERROR URL No BaseBid");
}else{
    $basebid = $_GET['BaseBid'];
}

  // Create connection
  $connect = mysqli_connect($servername, $username, $password, $dbname);
  mysqli_set_charset ( $conn , "utf8");

  // Check connection
  if (!$connect) {
   die("Connection failed: " . mysqli_connect_error());
  }

//--------------- Check  Targeting”

$targeting = "SELECT companies.Company, country.Country, category.Category, companies.Bid, companies.Budget
 FROM companies
 LEFT JOIN country ON country.CompanyID = companies.CompanyID AND country.Country='".$country."'
 LEFT JOIN category ON category.CompanyID = country.CompanyID AND category.Category='".$category."'
 ORDER BY companies.Bid DESC";

 
$targeting_set = $connect->query($targeting);

while (($row1 = $targeting_set->fetch_assoc()) != false) {
 
   $requeststatus = 0;
   
  if ($row1[Category] != null && $row1[Country] != null) $targeting_pass = 'Target Passed';
  else $targeting_pass = 'Target Failed';
  
  if ($row1[Bid] >= $basebid) $bid_pass = 'Bid Passed';
  else $bid_pass = 'Bid Failed';
  
  if ($row1[Budget] >= $basebid) $budget_pass = 'Budget Passed';
  else $budget_pass = 'Budget Failed';
   
  //------- Choising of winner 
  if ($winnercompany == null && $row1[Category] != null && $row1[Country] != null && $row1[Bid] >= $basebid && $row1[Budget] >= $basebid) {
    
    $winnercompany = $row1[Company];
    $winnerbudget = $row1[Budget];
    $winnerbid = $row1[Bid];
    $requeststatus = 1; 
    $answer = "Winner is ".$winnercompany;
    $items = $row1; 
    
   }
  
  
//-----------------Insert all companies in Log  

   
$insertlog = "INSERT INTO logfile1 (ReqCountry, ReqCategory, ReqBid, ReqStatus, Company, Budget, Bid, TargCheck, BudgetCheck, BidCheck)
  VALUES ('".$country."', '".$category."', ".$basebid.", ".$requeststatus.", '".$row1[Company]."', ".$row1[Budget].", ".$row1[Bid].", '".$targeting_pass."', '".$budget_pass."', '".$bid_pass."')";
$insert_set = $connect->query($insertlog);
 
  if (!$insert_set) {
      $status = 'ERROR logfile';
      $error = 2;
  } 
  
}

    //-------- Changing New Budget DB

if  ($winnercompany != null) {      // if winner 

    $update="UPDATE companies SET Budget = Budget - ".$basebid." WHERE Company = '".$winnercompany."'";
    $update_set = $connect->query($update);  
    if (!$update_set) {
        $status = 'ERROR Update Budget';
        $error = 2;
    }      
}

// массив для ответа
$result = array(
    'status' => $status,
    
    'error' => $error,
);

/*
echo json_encode($company);
echo json_encode($country);
echo json_encode($category);
echo json_encode($basebid);
echo json_encode($bid);
echo json_encode($budget);
echo json_encode($requeststatus);
*/
//echo '<Br>---------------- WINNER VIEW------------- <br>';
echo json_encode($answer);
echo '<Br>---------------- JSON ANSWER------------- <br>';
echo json_encode($items);
//echo '<Br>---------------- ERROR VIEW------------- <br>';
//echo json_encode($result); // ответ в формате json
echo '<Br>---------------- LOG VIEW------------- <br>';

$result_log = $connect->query("SELECT * FROM logfile1 ORDER BY ActID DESC");
while (($row = $result_log->fetch_assoc()) != false) echo json_encode($row).'<br />';
mysql_close();

?>
