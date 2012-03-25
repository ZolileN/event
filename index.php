<?php

$con = mysql_connect("localhost","root","eagles7");
if (!$con) {
  die('Could not connect: ' . mysql_error());
}

mysql_select_db("ad2orlando", $con);

if(isset($_GET['delete']) && isset($_GET['id'])) {
	$sql = "DELETE FROM events WHERE id = $_GET[id]";

	mysql_query($sql);
}

$sql = "SELECT * FROM events ORDER BY date DESC";

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

<p><a href="create.php">Create a New Event</a></p>

<?php

while($row = mysql_fetch_array($result)) {
echo "[ <a href=\"details.php?id=" . $row['id'] . "\">DETAILS</a> ]" . $row['name'] . " " . $row['date'] . "<br>";
}

?>

</body>
</html>
