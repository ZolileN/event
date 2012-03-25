<?php

require_once dirname(__FILE__) . '/includes/Event.php';

$con = mysql_connect("localhost","root","test1234");
if (!$con) {
  die('Could not connect: ' . mysql_error());
}

mysql_select_db("ad2orlando", $con);

$sql = "SELECT * FROM events WHERE id = $_GET[id]";

//Submit INSERT query
$result = mysql_query($sql);

//Close database connection
mysql_close($con);

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

		echo "Copy this code and paste into MyEmma:<br><br>";
		echo htmlspecialchars($event->getEblastHtml());
	}
}

?>

</body>
</html>
