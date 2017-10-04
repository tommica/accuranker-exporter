<?php

$accurankerAPIKey = '';
if(isset($argv[1]) && $argv[1] != '') {
	$accurankerAPIKey = $argv[1];
} else {
	die('Please pass Accuranker API key');
}

// Setup some basic variables
$baseUrl = 'https://app.accuranker.com/api/v3';
$opts = [
	'http' => [
		'method' => 'GET',
		'header' => 'Authorization: Token '.$accurankerAPIKey
	]
];
$context = stream_context_create($opts);

// Get the group for the account
$groupsJSON = json_decode(file_get_contents($baseUrl.'/groups/', false, $context), true);
$groups = [];
// Filter the result to contain only what is needed
// Not sure if there is a cleaner way of doing this
foreach($groupsJSON as $group) {
	$groups[$group['id']] = $group['name'];
}

// Get the domains for the account
$domainsJSON = json_decode(file_get_contents($baseUrl.'/domains/', false, $context), true);

// Results
$fp = fopen('result.csv', 'w');
foreach($domainsJSON as $domain) {
	$group = isset($groups[$domain['group']]) ? $groups[$domain['group']] : 'UNKNOWN';
	fputcsv($fp, [
		'Gruppe' => $group,
		'Domain' => $domain['name']
	], ';');
}

fclose($fp);

?>
