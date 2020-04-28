<?php

	#Redirect to https
	if (! isset($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off' ) {
		$redirect_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		header("Location: $redirect_url");
		exit();
	}

	// Check if the config field is empty
	if((strlen($_POST['config']) == 94) && 
		(strpos($_POST['config'], '/edit#gid=0') !== false) && 
		(strpos($_POST['config'], 'https://docs.google.com/spreadsheets/d/') !== false))
	{
		
		$configlink = $_POST['config'];
		$config =  str_replace('/edit#gid=0','',str_replace('https://docs.google.com/spreadsheets/d/','',$_POST['config']));
		
	}

	// Check if the id field is empty
	if((strlen($_POST['id']) == 94) && 
		(strpos($_POST['id'], '/edit#gid=0') !== false) && 
		(strpos($_POST['id'], 'https://docs.google.com/spreadsheets/d/') !== false))
	{
		
		$idlink = $_POST['id'];
		$id = str_replace('/edit#gid=0','',str_replace('https://docs.google.com/spreadsheets/d/','',$_POST['id']));
		
	}
	if((!empty($config)) && (!(empty($id))))
	{
		$remoteadminlistlink = 'http://remoteadminlist.com/remoteadmin.php?config=' . $config . '&id=' . $id;
		$url = 'https://spreadsheets.google.com/feeds/list/' . urlencode($config) . '/od6/public/values?alt=json';
		$arrContextOptions=array(
			  "ssl"=>array(
					"verify_peer"=>false,
					"verify_peer_name"=>false,
				),
			);  
		$configresponse = file_get_contents($url, false, stream_context_create($arrContextOptions));
		if(!(empty($configresponse)))
		{
			$configerror = 'Config Google Sheet response is empty. Either the sheet url is wrong or are the permissions incorrectly set';
		}
		$url = 'https://spreadsheets.google.com/feeds/list/' . urlencode($id) . '/od6/public/values?alt=json';
		$arrContextOptions=array(
			  "ssl"=>array(
					"verify_peer"=>false,
					"verify_peer_name"=>false,
				),
			);  
		$idresponse = file_get_contents($url, false, stream_context_create($arrContextOptions));
		if(!(empty($idresponse)))
		{
			$iderror = 'ID Google Sheet response is empty. Either the sheet url is wrong or are the permissions incorrectly set';
		}
	}
	
?>
<!DOCTYPE html>
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
	<link rel="stylesheet" type="text/css" href="css/Squad-Style.css">
	
<head>
</head>

<body>
<?php if (((empty($config)) && (empty($id)))): ?><!-- || (!(empty($configerror))) && (!(empty($iderror)))-->
<form action="help.php" method="POST" >
	<br /><p class="">Link to the Config Google Sheet:
	<br /><input type="text" class='' name="config" value="https://docs.google.com/spreadsheets/d/1_6RcFNbPNaZ2jViKAV3dMfc-Jx7BqPS-uaawq7JEb4A/edit#gid=0" />
	<br /><?php echo $configerror; ?></p>
	<p class="">Link to the ID Google Sheet:
	<br /><input type="text" class='' name="id" value="https://docs.google.com/spreadsheets/d/1MUTPM3KIIVkHdkgbEwlTwxY7TMPidKZ66MsebjY-HSA/edit#gid=0" />
	<br /><?php echo $iderror; ?></p>
	<button type="submit" name="Request" value="Submit">Submit</button>
</form>
<?php else: ?>
<form action="remoteadmin.php" method="GET" >
	<br /><p class="">Link to the Config Google Sheet:	<br /><a href="<?php echo $configlink; ?>"><?php echo $config; ?></a>
	<input type="hidden" class='' name="config" value="<?php echo $config; ?>" /></p>
	<p class="">Link to the ID Google Sheet:
	<br /><a href="<?php echo $idlink; ?>"><?php echo $id ?></a>
	<input type="hidden" class='' name="id" value="<?php echo $id; ?>" /></p>
	<button type="submit" name="Request" value="Submit">Submit</button>
</form>
<p class="">Link to the remote admin list:
<br /><a href="<?php echo $remoteadminlistlink; ?>" target="_blank"><?php echo $remoteadminlistlink; ?></a></p>
<?php endif; ?>

</body>
</html>
