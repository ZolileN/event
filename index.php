<?php 
include("../includes/header.php"); 
require_once dirname(__FILE__) . '/includes/Database.php';

$database = new Database();

if(isset($_GET['delete']) && isset($_GET['id'])) {
	$database->delete($_GET['id']);
}

$result = $database->select();

?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<link href="style.css" rel="stylesheet" type="text/css" />

<div class="ContentHeaderLeft">

	<div class="ContentHeaderPaddingLeft">COMMUNICATIONS</div>

</div>

<div class="MainPadding">

	<div id="container">
	<p>
	<span class="button"><a href="create.php">Create a New Event</a></span>
	</p>

	<?php
	echo "<ul id=\"event_list\">";
	while($row = mysql_fetch_array($result)) {
		if($row['banner'] === 1) {
			$active = true;
		}
		echo "<li>[ <a href=\"details.php?id=" . $row['id'] . "\">Details</a> ] " . $row['name'];
		if($row['banner_active'] == 1) {
			echo " [active]";
		}	
		echo "</li>";
	}
	echo "</ul>";

	?>
	</div>

</div>

<div class="MainContentClear"></div>

<?php include("../includes/footer.php") ?>
