<?php

require_once dirname(__FILE__) . '/includes/Event.php';
require_once dirname(__FILE__) . '/includes/Database.php';

$database = new Database();
$event = new Event();

if(isset($_POST['id'])) {
	if(empty($_POST['name'])) {
		$event->setStatus('An event name has not been entered.');
	} else {
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
	}

	$result = $database->select($_POST['id']);
} else {
	$result = $database->select($_GET['id']);
}
?>
<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="events_container">
<p><span class="button"><a href="index.php">Back to Main</a></span></p>

<?php
$message = $event->getStatus();
if(!empty($message)) {
	echo "<p>$message</p>";
}

while($row = mysql_fetch_array($result)) {
	$event->setImg('eblast', $row['eblast_image']);
	$event->setPath('eblast', $row['eblast_path']);
	$event->setLink('eblast', $row['eblast_link']);
	$event->setEblastHtml();
?>

	<h1><?php echo $row['name'] ?></h1>
<?php 
	if($row['banner_active']) {
		$active = 'Yes';
	} else {
		$active = 'No';
	}
?>
	<p>Active: <?php echo $active ?></p>

<?php
	if(!isset($_GET['delete'])) {
		echo "<p><span class=\"button\"><a href=\"details.php?id=$_GET[id]&delete=false\">Delete Event</a></span></p>";
	} elseif(isset($_GET['delete'])) {
		echo "<p>Are you sure you want to delete this event? <a href=\"index.php?id=$_GET[id]&&delete=true\">Yes</a> <a href=\"details.php?id=$_GET[id]\">No</a></p>";
	}
?>
<p><span class="button"><a href="#" class="edit">Edit Event</a></span></p>
<p class="error"><p>
<form name="event" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" class="details">
	<label>
		Event Name:
	</label>
	<input type="text" name="name" id="name" value="<?php echo $row['name']; ?>" />

	<label>
		Banner Active:
	</label>
	<input type="checkbox" name="banner_active" id="banner_active" <?php if($row['banner_active'] == 1) { echo "checked=\"checked\""; } ?> />

	<label>
		Banner File:
	</label>
	<input type="file" name="banner_image" id="banner_image" />

	<label>
		Banner Link: 
	</label>
	<input type="text" name="banner_link" id="banner_link" value="<?php if($row['banner_link']) { echo $row['banner_link']; } ?>" />

	<label>
		eBlast File: 
	</label>
	<input type="file" name="eblast_image" id="eblast_image" />

	<label>
		eBlast Link: 
	</label>
	<input type="text" name="eblast_link" value="<?php if($row['eblast_link']) { echo $row['eblast_link']; } ?>" />

	<input type="hidden" name="id" value="<?php echo $row['id']; ?>" />

	<button type="submit">Submit</button>
</form>

<?php

	if(!empty($row['banner_image'])) {
?>
		<div class="detail_blurb">
			<p>Banner Image: </p>
			<p><a href="http://localhost/ad2orlando.org/<?php echo $row['banner_path'] . "/" . $row['banner_image']; ?>" target="_blank"><img src="http://localhost/ad2orlando.org/<?php echo $row['banner_path'] . "/" . $row['banner_image']; ?>" width="125"></a></p>
			<p>Banner Link: <a href="<?php echo $row['banner_link']; ?>" target="_blank"><?php echo $row['banner_link'] ?></a></p>
		</div>
<?php
	}

	if(!empty($row['eblast_image'])) {
?>
		<div class="detail_blurb">
			<p>Eblast Image: </p>
			<p><a href="http://localhost/ad2orlando.org/<?php echo $row['eblast_path'] . "/" . $row['eblast_image']; ?>" target="_blank"><img src="http://localhost/ad2orlando.org/<?php echo $row['eblast_path'] . "/" . $row['eblast_image']; ?>" width="125"></a></p>
			<p>Eblast Link: <a href="<?php echo $row['eblast_link']; ?>" target="_blank"><?php echo $row['eblast_link']; ?></a></p>
		</div>

<div class="float_clear"></div>
<div class="detail_eblast_html">
		<p>Eblast File: <a href="http://localhost/ad2orlando.org/<?php echo $row['eblast_path']; ?>/eblast.htm" target="_blank">Click Here</a></p>
<?php
		echo "Copy this code and paste into MyEmma:<br><br>";
		echo htmlspecialchars($event->getEblastHtml());
	}
}
?>
</div>
<script>
	$('a.edit').on('click', function() {
		$('.details').toggle();
	});

	$('button').on('click', function() {
		$('.error').text('');

		if($('#name').val().length === 0) {
			$('.error').text('An event name has not been entered.');
			return false;
		}
	});
</script>
</div>
</body>
</html>
