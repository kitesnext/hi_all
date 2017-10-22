<?php

	header("Access-Control-Allow-Origin: *");


	$cpuusage = 100 - shell_exec("vmstat | tail -1 | awk '{print $15}'");
	$clock = shell_exec('cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq');
	$clock = round($clock / 1000);
	
	$uptimedata = shell_exec('uptime');
	$uptime = explode(' up ', $uptimedata);
	$uptime = explode(',', $uptime[1]);
	$uptime = $uptime[0].', '.$uptime[1];
	
	$temp = shell_exec('cat /sys/class/thermal/thermal_zone*/temp');
	$temp = round($temp / 1000, 1);

	// answer array
$result = array(
	
	'temp' => $temp,
	'clock' => $clock,
	'voltage' => $voltage,
	'cpuusage' =>  $cpuusage,
	'uptime' => $uptime	
	);
	
	
	// JSON output


if($result)
	echo json_encode($result);

?>