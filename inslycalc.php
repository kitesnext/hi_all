<?php
ob_start();
header("Access-Control-Allow-Origin: *");
/*
* Common functions
*/
function input($str)
	{
	return stripslashes(trim($str));
	}
/*
* If data is posted.
*/
if ($_POST)
	{
	$value = input($_POST['value']) * 1;
	$tax = input($_POST['tax']) * 1;
	$ninsta = input($_POST['ninsta']) * 1;
	$day = input($_POST['day']) * 1;
	$hours = input($_POST['hours']) * 1;
	if ($day == 5 && $hours >= 17 && $hours <= 20)
		{
		$policy = 13;
		}
	  else $policy = 11;
	$base_price = round($value * $policy / 100, 2);
	$comission = round($base_price * 0.17, 2);
	$tax_pay = round($base_price * $tax / 100, 2);
	$total_cost = $base_price + $comission + $tax_pay;

	// answer array

	$result[0]['base_price'] = "Base premium (${policy}%)" . PHP_EOL;
	$result[0]['comission'] = 'Comission (17%)';
	$result[0]['tax_pay'] = "Tax (${tax}%)" . PHP_EOL;
	$result[0]['total_cost'] = 'Total cost';
	$result[1]['base_price'] = $base_price;
	$result[1]['comission'] = $comission;
	$result[1]['tax_pay'] = $tax_pay;
	$result[1]['total_cost'] = $total_cost;

	for ($i = 2; $i <= $ninsta + 1; $i++)
		{
		$result[$i]['base_price'] = round($base_price / $ninsta, 2);
		$result[$i]['comission'] = round($comission / $ninsta, 2);
		$result[$i]['tax_pay'] = round($tax_pay / $ninsta, 2);
		$result[$i]['total_cost'] = round($total_cost / $ninsta, 2);
		}

	// JSON output

	if ($result) echo json_encode($result);
	}
?>