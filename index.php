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
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="events_container">
<p>
<span class="button"><a href="create.php">Create a New Event</a></span>
</p>

<?php

while($row = mysql_fetch_array($result)) {
	if($row['banner'] === 1) {
		$active = true;
	}

	echo "[ <a href=\"details.php?id=" . $row['id'] . "\">Details</a> ] " . $row['name'];
	if($row['banner_active'] == 1) {
		echo " [active]";
	}	
	echo "<br>";
}

?>
</div>
</body>
</html>
