<?php

require_once dirname(__FILE__) . '/includes/Database.php';

$database = new Database();

if(isset($_GET['delete']) && isset($_GET['id'])) {
	$database->delete($_GET['id']);
}

$result = $database->select();

?>
<!DOCTYPE html>
<html>
<head>

</head>
<body>

<p><a href="create.php">Create a New Event</a></p>

<?php

while($row = mysql_fetch_array($result)) {
echo "[ <a href=\"details.php?id=" . $row['id'] . "\">DETAILS</a> ] " . $row['name'] . " " . $row['date'] . "<br>";
}

?>

</body>
</html>
