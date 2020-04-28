<?php 
	header("Content-Type: text/plain");

	$adminsnos=0;
	$failed=0;	
	$Groups = array();
	$Admins = "";
	if((!(empty($_GET['config']))) && (!(empty($_GET['id']))))
	{
		$url = 'https://spreadsheets.google.com/feeds/list/' . urlencode($_GET['config']) . '/od6/public/values?alt=json';
		$arrContextOptions=array(
			  "ssl"=>array(
					"verify_peer"=>false,
					"verify_peer_name"=>false,
				),
			);  
		$response = file_get_contents($url, false, stream_context_create($arrContextOptions));
		$rawJSON = json_decode($response, true);		
		foreach( $rawJSON['feed']['entry'] as $number => $GroupData ) {
			if($GroupData['gsx$spreadheetid']['$t'] == $_GET['id']) {
				$GroupPerms .= 'Group=' . $GroupData['gsx$groupname']['$t'] . ':' . $GroupData['gsx$permissions']['$t'] . "\n";
				$Groups[] = $GroupData['gsx$groupname']['$t'];
			}
		}
		$GroupPerms .= "\n";

		$url = 'https://spreadsheets.google.com/feeds/list/' . urlencode($_GET['id']) . '/od6/public/values?alt=json';
		$arrContextOptions=array(
			  "ssl"=>array(
					"verify_peer"=>false,
					"verify_peer_name"=>false,
				),
			);  
		$response = file_get_contents($url, false, stream_context_create($arrContextOptions));
		$rawJSON = json_decode($response, true);
		foreach( $rawJSON['feed']['entry'] as $number => $AdminData ) {
			if ((substr($AdminData['gsx$steamid']['$t'], 0, 4) == '7656') && (is_numeric($AdminData['gsx$steamid']['$t'])) && (in_array($AdminData['gsx$group']['$t'], $Groups))) {
				if ((isset($AdminData['gsx$clan']['$t'])) && ($AdminData['gsx$clan']['$t'] != '')) {
				$adminsnos++;
			} else {
				$failed++;
				// plus 2 because starting 0 and spreadsheet heading
				$failedlinenos .= $number+2 . ',';
			}
		}
		if($failed > 0)
		{
			print("// " . $adminsnos . " admins loaded. (" . $failed . " failed - failed line numbers (" . rtrim($failedlinenos,",") . "))." . "\n");
		} else {
			print("// " . $adminsnos . " admins loaded. (" . $failed . " failed)." . "\n");
		}
		print($GroupPerms);	
		print($Admins);
	} else {
		print("You're missing one or both of the parameters.....\n\n");

		print("The remoteadminlist.com is a free to use tool for Squad and Post Scriptum that translates your google sheets into remote admin lists.\n");
		print("These can then be entered into your servers RemoteAdminListHost.cfg files.\n\n");
		print("The php script takes 2 GET parameters (referenced in the URL), config and id, referring to 2 Google Sheets. e.g.\n");
		print("http://remoteadminlist.com/remoteadmin.php?config=1_6RcFNbPNaZ2jViKAV3dMfc-Jx7BqPS-uaawq7JEb4A&id=1MUTPM3KIIVkHdkgbEwlTwxY7TMPidKZ66MsebjY-HSA\n");

		print("Therefore you will need 2 corresponding Google Sheets:\n\n");
		print("https://docs.google.com/spreadsheets/d/1_6RcFNbPNaZ2jViKAV3dMfc-Jx7BqPS-uaawq7JEb4A/edit#gid=0\n");
		print("It contains 4 columns:\n");
		print("https://docs.google.com/spreadsheets/d/1MUTPM3KIIVkHdkgbEwlTwxY7TMPidKZ66MsebjY-HSA/edit#gid=0\n");
		print("It also contains 4 columns:\n");
		print("IMPORTANT\n");
		print("If you do not then the permissions will restrict the php script being able to extract the information in your Google Sheets.\n");
		print("This does not affect edit rights which can be defined as per usual in Google Sheets. \n");
		print("You can have one master Config Google Sheet for multiple ID Google Sheets.\n\n");
		print("as you can control what permissions they are allowed to assign themselves to. It's also a great way to provide whitelists\n");
		print("and admin permissions to partner and stakeholder clans in your servers so that they can add new members and remove old members\n");
		print("without asking the server owner every time.\n\n");
	}
?>