<?php 
	header("Content-Type: text/plain");

	$adminsnos=0;
	$failed=0;	
	$Groups = array();
	$Admins = "";	$failedlinenos = "";	$GroupPerms= "";
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
				if ((isset($AdminData['gsx$clan']['$t'])) && ($AdminData['gsx$clan']['$t'] != '')) {					$Admins .= 'Admin=' . $AdminData['gsx$steamid']['$t'] . ':' . $AdminData['gsx$group']['$t'] . ' // ' . $AdminData['gsx$clan']['$t'] . ' - ' . $AdminData['gsx$username']['$t'] . "\n";				} else {					$Admins .= 'Admin=' . $AdminData['gsx$steamid']['$t'] . ':' . $AdminData['gsx$group']['$t'] . ' // ' . $AdminData['gsx$username']['$t'] . "\n";				}
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
		print("http://remoteadminlist.com/remoteadmin.php?config=1_6RcFNbPNaZ2jViKAV3dMfc-Jx7BqPS-uaawq7JEb4A&id=1MUTPM3KIIVkHdkgbEwlTwxY7TMPidKZ66MsebjY-HSA\n");		print("Contains 2 GET parameters\n");		print("1. a Config parameter:\n");		print("1_6RcFNbPNaZ2jViKAV3dMfc-Jx7BqPS-uaawq7JEb4A\n");		print("2. and an ID parameter:\n");		print("1MUTPM3KIIVkHdkgbEwlTwxY7TMPidKZ66MsebjY-HSA\n\n");

		print("Therefore you will need 2 corresponding Google Sheets:\n\n");		print("1. The Config Sheet - This sheet holds the settings for the way the users defined in the ID Google Sheet will be mapped into roles in the RemoteAdminList.\n");
		print("https://docs.google.com/spreadsheets/d/1_6RcFNbPNaZ2jViKAV3dMfc-Jx7BqPS-uaawq7JEb4A/edit#gid=0\n");
		print("It contains 4 columns:\n");		print("-the reference of the ID spreadsheet these roles will be applied to\n");		print("-the role group name\n");		print("-the access permissions granted to the role group https://squad.gamepedia.com/Server_Administration\n");		print("-a free text field to remind you what the sheet is (usually the title of the ID sheet)\n\n");		print("2. The ID Sheet - This sheet contains a list of the users that will be mapped to the roles defined in the corresponding Config Google Sheet.\n");
		print("https://docs.google.com/spreadsheets/d/1MUTPM3KIIVkHdkgbEwlTwxY7TMPidKZ66MsebjY-HSA/edit#gid=0\n");
		print("It also contains 4 columns:\n");		print("-users clan\n");		print("-users name\n");		print("-users group (must be correspond to the group defined in the Config Google Sheet)\n");		print("-users steam id (starting 7656...)\n\n");
		print("IMPORTANT\n");		print("1. Once you have drafted these Google Sheets you need to publish them (Go to the 'File' menu and click on 'Publish to the Web').\n");
		print("If you do not then the permissions will restrict the php script being able to extract the information in your Google Sheets.\n");
		print("This does not affect edit rights which can be defined as per usual in Google Sheets. \n");		print("2. The reference of the ID sheet must be added to the Config Google Sheet next to the role groups created in relation to it.\n");
		print("You can have one master Config Google Sheet for multiple ID Google Sheets.\n\n");		print("This is a great way to get users to add permissions for themselves for one off events (such as one life events)\n");
		print("as you can control what permissions they are allowed to assign themselves to. It's also a great way to provide whitelists\n");
		print("and admin permissions to partner and stakeholder clans in your servers so that they can add new members and remove old members\n");
		print("without asking the server owner every time.\n\n");		print("It's free to use and there's a second version hosted at:\n");		print("http://internetthingy.co.uk/remoteadmin.php\n");		print("If you which to host a version yourself you can download it from GitHub.\n\n");		print("http://remoteadminlist.com/remoteadmin.php\n");		print("https://docs.google.com/spreadsheets/d/1_6RcFNbPNaZ2jViKAV3dMfc-Jx7BqPS-uaawq7JEb4A/edit#gid=0\n");		print("https://docs.google.com/spreadsheets/d/1MUTPM3KIIVkHdkgbEwlTwxY7TMPidKZ66MsebjY-HSA/edit#gid=0\n");		print("http://remoteadminlist.com/remoteadmin.php?config=1_6RcFNbPNaZ2jViKAV3dMfc-Jx7BqPS-uaawq7JEb4A&id=1MUTPM3KIIVkHdkgbEwlTwxY7TMPidKZ66MsebjY-HSA\n");		
	}
?>