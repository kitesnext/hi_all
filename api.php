<?php
$status = 'OK';
$response = null;
$error = 0;
$company = null;
$requeststatus = 0;
$answer = "No Companies Passed from BaseBid check" ;

$budget = 0;
$bid = 0;

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

$sql=   "SELECT companies.Company, country.Country, category.Category, companies.Budget, companies.Bid
 FROM companies JOIN country ON companies.CompanyID=country.CompanyID 
 JOIN category ON country.CompanyID=category.CompanyID
 WHERE companies.Budget>=".$basebid." AND companies.Bid>=".$basebid." AND country.Country='".$country."' AND category.Category='".$category."'
 ORDER BY companies.Bid DESC"; 
$result_set = $connect->query($sql);    

if (!$result_set) {
    echo "select_error";
    exit;
}    
     $items = array(); // Массив  Response
   //  while (($row = $result_set->fetch_assoc()) != false) $items[] = $row; // 
     $items = $result_set->fetch_assoc();
 

if ($items[Company] != null) { 
$company = $items[Company];
$budget = $items[Budget];
$bid = $items[Bid];
$requeststatus = 1; 
$answer = "Winner is ".$company;  
}

 //--------2-- Inset Log DB   
$insert = "INSERT INTO log (Country, Category, BaseBid, StatusRequest, Company, Budget, Bid)
 VALUES ('".$country."', '".$category."', ".$basebid.", ".$requeststatus.", '".$company."', ".$budget.", ".$bid.")";
$insert_set = $connect->query($insert);
//$insert_set = $connect->query( "INSERT INTO log (Country) VALUES ('US')");  
if (!$insert_set) {

    $status = 'ERROR log';
    $error = 2;
}   

if  ($requeststatus = 1) {      // if OK
    //------1-- Changing DB
    $update="UPDATE companies SET Budget = Budget - ".$basebid." WHERE Company = '".$company."'";
    $update_set = $connect->query($update);  
    if (!$update_set) {
        $status = 'ERROR Update Budget';
        $error = 2;
    }      
}


//------------
/*
if(!isset($_GET['email'])){ // если не получили параметр
    $status = 'ERROR';
    $error = 1;
}else{
    $email = $_GET['email'];
    if(preg_match("|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i", $email)){ // проверяем корректрость e-mail
        $response = true;
    }else{
        $response = false;
    }
}
*/
// массив для ответа
$result = array(
    'status' => $status,
    
    'error' => $error,
);
//$company = $items[Company];
echo json_encode($company);
echo json_encode($country);
echo json_encode($category);
echo json_encode($basebid);
echo json_encode($bid);
echo json_encode($budget);
echo json_encode($requeststatus);
echo json_encode('<Br>'.$answer);
echo json_encode('<Br>'.$items);
echo json_encode($result); // ответ в формате json
?>
