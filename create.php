<?php

require_once dirname(__FILE__) . '/includes/Event.php';
require_once dirname(__FILE__) . '/includes/Database.php';

$update = array();

if(isset($_FILES["eblast_img"]) || isset($_FILES["banner_img"])) {

	//Instantiate Event class and set submitted values
	$event = new Event();

	if(isset($_FILES["eblast_img"]) && !empty($_FILES["eblast_img"]['name'])) {
		$update[] = 'eblast';
	}

	if(isset($_FILES["banner_img"]) && !empty($_FILES["banner_img"]['name'])) {
		$update[] = 'banner';
	}

	if(empty($update)) {
		$event->setStatus('No images have been submitted');
		$message = $event->getStatus();
	} else {
		//Set submitted values
		foreach($update as $key => $type) {
			$event->setFile($type, $_FILES[$type . "_img"]);
			$event->setLink($type, $_POST[$type . "_link"]);
		}

		$created = $event->createEvent($_POST[event_name]);
		$message = $event->getStatus();

		if($created !== false) {
			//Get values to be uploaded to the database
			$eblastInfo = $event->getInfo('eblast');
			$bannerInfo = $event->getInfo('banner');

			//Set values in an array that will be used to create sql statement
			$values = array('name'		   => $_POST['event_name'],
							'date'		   => date('Y-m-d H:i:s'), 
							'eblast_path'  => $eblastInfo['path'], 
							'eblast_file'  => $eblastInfo['file'], 
							'eblast_link'  => $eblastInfo['link'], 
							'eblast_image' => $eblastInfo['img'], 
							'banner_path'  => $bannerInfo['path'], 
							'banner_link'  => $bannerInfo['link'], 
							'banner_image' => $bannerInfo['img']);
	
			$database = new Database();
			$database->insert($values);
		}
	}
}
?>

<!DOCTYPE html>
<html>
<head>

</head>
<body>
<?php
if(isset($message) && !empty($message)) {
	echo "<p>$message</p>";
}
?>
<?php if(empty($update) || isset($created) && $created === false) { ?>
<form name="event" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
	Event Name: <input type="text" name="event_name" value="<?php if($_POST['event_name']) { echo $_POST['event_name']; } ?>" /><br>
	eBlast File: <input type="file" name="eblast_img" /><br>
	eBlast Link: <input type="text" name="eblast_link" value="<?php if($_POST['eblast_link']) { echo $_POST['eblast_link']; } ?>" /><br>
	Banner File: <input type="file" name="banner_img" /><br>
	Banner Link: <input type="text" name="banner_link" value="<?php if($_POST['banner_link']) { echo $_POST['banner_link']; } ?>" /><br>
	<input type="submit" value="Submit" />
</form>
<?php } ?>

<?php
if($created !== false) {
	if(in_array('banner', $update)) {
		echo "<p>Banner Image: </p>";
		echo "<p><a href=\"http://localhost/ad2orlando.org/" . $bannerInfo['path'] . "/" . $bannerInfo['img'] . "\" target=\"_blank\"><img src=\"http://localhost/ad2orlando.org/" . $bannerInfo['path'] . "/" . $bannerInfo['img'] . "\" width=\"125\"></a></p>";
		echo "<p>Banner Link: <a href=\"" . $bannerInfo['link'] . "\" target=\"_blank\">" . $bannerInfo['link'] . "</a></p>";
	}

	if(in_array('eblast', $update)) {
		echo "<p>Eblast Image: </p>";
		echo "<p><a href=\"http://localhost/ad2orlando.org/" . $eblastInfo['path'] . "/" . $eblastInfo['img'] . "\" target=\"_blank\"><img src=\"http://localhost/ad2orlando.org/" . $eblastInfo['path'] . "/" . $eblastInfo['img'] . "\" width=\"125\"></a></p>";
		echo "<p>Eblast Link: <a href=\"" . $eblastInfo['link'] . "\" target=\"_blank\">" . $eblastInfo['link'] . "</a></p>";
		echo "<p>Eblast File: <a href=\"http://localhost/ad2orlando.org/" . $eblastInfo['path'] . "/eblast.htm\" target=\"_blank\">Click Here</a></p>";

		echo "Copy this code and paste into MyEmma:<br><br>";
		echo htmlspecialchars($event->getEblastHtml());
	}
}
?>

</body>
</html>
