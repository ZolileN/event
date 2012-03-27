<?php

require_once dirname(__FILE__) . '/includes/Event.php';
require_once dirname(__FILE__) . '/includes/Database.php';

$database = new Database();
$result = $database->select($_GET['id']);
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
	$event = new Event();
	$event->setImg('eblast', $row['eblast_image']);
	$event->setPath('eblast', $row['eblast_path']);
	$event->setLink('eblast', $row['eblast_link']);
	$event->setEblastHtml();;

	if(!empty($row['banner_image'])) {
		echo $row['name'] . "<br>";
		echo "<p>Banner Image: </p>";
		echo "<p><a href=\"http://localhost/ad2orlando.org/" . $row['banner_path'] . "/" . $row['banner_image'] . "\" target=\"_blank\"><img src=\"http://localhost/ad2orlando.org/" . $row['banner_path'] . "/" . $row['banner_image'] . "\" width=\"125\"></a></p>";
		echo "<p>Banner Link: <a href=\"" . $row['banner_link'] . "\" target=\"_blank\">" . $row['banner_link'] . "</a></p>";
	}

	if(!empty($row['eblast_image'])) {
		echo "<p>Eblast Image: </p>";
		echo "<p><a href=\"http://localhost/ad2orlando.org/" . $row['eblast_path'] . "/" . $row['eblast_image'] . "\" target=\"_blank\"><img src=\"http://localhost/ad2orlando.org/" . $row['eblast_path'] . "/" . $row['eblast_image'] . "\" width=\"125\"></a></p>";
		echo "<p>Eblast Link: <a href=\"http://localhost/ad2orlando.org" . $row['eblast_link'] . "\" target=\"_blank\">" . $row['eblast_link'] . "</a></p>";
		echo "<p>Eblast File: <a href=\"http://localhost/ad2orlando.org/" . $row['eblast_path'] . "/eblast.htm\" target=\"_blank\">Click Here</a></p>";

		echo "Copy this code and paste into MyEmma:<br><br>";
		echo htmlspecialchars($event->getEblastHtml());
	}

?>
<form name="event" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
	Event Name: <input type="text" name="event_name" value="<?php echo $row['name']; ?>" /><br>
	eBlast File: <input type="file" name="eblast_img" /><br>
	eBlast Link: <input type="text" name="eblast_link" value="<?php if($row['banner_link']) { echo $row['banner_link']; } ?>" /><br>
	Banner File: <input type="file" name="banner_img" /><br>
	Banner Link: <input type="text" name="banner_link" value="<?php if($row['eblast_link']) { echo $row['eblast_link']; } ?>" /><br>
	<input type="submit" value="Submit" />
</form>
<?php
}
?>

</body>
</html>
