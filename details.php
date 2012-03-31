<?php

require_once dirname(__FILE__) . '/includes/Event.php';
require_once dirname(__FILE__) . '/includes/Database.php';

$database = new Database();
$event = new Event();

if(isset($_POST['id'])) {
	$update = array();
	$result = $database->select($_POST['id']);
	$row = mysql_fetch_array($result);

	foreach($_POST as $key => $value) {
		if($value !== $row[$key]) {
			$update[$key] = $value;
		}
	}

	($_POST['banner_active']) ? $update['banner_active'] = 1 : $update['banner_active'] = 0;

	foreach($_FILES as $name => $values) {
		if(!empty($values['name'])) {
			$type = preg_replace('/_image$/', '', $name);
			$event->setFile($type, $_FILES[$name]);
			$event->setPath($type, $row[$type . '_path']);
			$update[$name] = $values['name'];
		}
	}

	$event->upload($_FILES[$name]);

	if(!empty($update)) {
		$database->update($update, $_POST['id']);
	}

	$result = $database->select($_POST['id']);
} else {
	$result = $database->select($_GET['id']);
}
?>
<!DOCTYPE html>
<html>
<head>

</head>
<body>

<?php
if(!isset($_GET['delete'])) {
	echo "<p><a href=\"details.php?id=$_GET[id]&delete=false\">Delete Event</a></p>";
} elseif(isset($_GET['delete'])) {
	echo "<p>Are you sure you want to delete this event? <a href=\"index.php?id=$_GET[id]&&delete=true\">Yes</a></p>";
}

while($row = mysql_fetch_array($result)) {
	$event->setImg('eblast', $row['eblast_image']);
	$event->setPath('eblast', $row['eblast_path']);
	$event->setLink('eblast', $row['eblast_link']);
	$event->setEblastHtml();
?>

<?php
	if(!empty($row['banner_image'])) {
?>
		<h1><?php echo $row['name'] ?></h1>
		<p>Banner Image: </p>
		<p><a href="http://localhost/ad2orlando.org/<?php echo $row['banner_path'] . "/" . $row['banner_image']; ?>" target="_blank"><img src="http://localhost/ad2orlando.org/<?php echo $row['banner_path'] . "/" . $row['banner_image']; ?>" width="125"></a></p>
		<p>Banner Link: <a href="<?php echo $row['banner_link']; ?>" target="_blank"><?php echo $row['banner_link'] ?></a></p>
<?php
	}

	if(!empty($row['eblast_image'])) {
?>
		<p>Eblast Image: </p>
		<p><a href="http://localhost/ad2orlando.org/<?php echo $row['eblast_path'] . "/" . $row['eblast_image']; ?>" target="_blank"><img src="http://localhost/ad2orlando.org/<?php echo $row['eblast_path'] . "/" . $row['eblast_image']; ?>" width="125"></a></p>
		<p>Eblast Link: <a href="<?php echo $row['eblast_link']; ?>" target="_blank"><?php echo $row['eblast_link']; ?></a></p>
		<p>Eblast File: <a href="http://localhost/ad2orlando.org/<?php echo $row['eblast_path']; ?>/eblast.htm" target="_blank">Click Here</a></p>

<?php

		echo "Copy this code and paste into MyEmma:<br><br>";
		echo htmlspecialchars($event->getEblastHtml());
	}

?>
<form name="event" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
	Event Name: <input type="text" name="name" value="<?php echo $row['name']; ?>" /><br>
	Banner Active: <input type="checkbox" name="banner_active" <?php if($row['banner_active'] == 1) { echo "checked=\"checked\""; } ?> /><br>
	Banner File: <input type="file" name="banner_image" /><br>
	Banner Link: <input type="text" name="banner_link" value="<?php if($row['banner_link']) { echo $row['banner_link']; } ?>" /><br>
	eBlast File: <input type="file" name="eblast_image" /><br>
	eBlast Link: <input type="text" name="eblast_link" value="<?php if($row['eblast_link']) { echo $row['eblast_link']; } ?>" /><br>
	<input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
	<input type="submit" value="Submit" />
</form>
<?php
}
?>

</body>
</html>
