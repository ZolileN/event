<?php
include("../includes/header.php"); 
require_once dirname(__FILE__) . '/includes/Event.php';
require_once dirname(__FILE__) . '/includes/Database.php';

$update = array();

if(isset($_FILES["eblast_img"]) || isset($_FILES["banner_img"])) {

	//Instantiate Event
	$event = new Event();

	if(empty($_POST["event_name"])) {
		$event->setStatus('An event name has not been entered.');
		$message = $event->getStatus();
	} else {

		//Set submitted values
		if(isset($_FILES["eblast_img"]) && !empty($_FILES["eblast_img"]['name'])) {
			$update[] = 'eblast';
		}

		if(isset($_FILES["banner_img"]) && !empty($_FILES["banner_img"]['name'])) {
			$update[] = 'banner';
		}

		if(empty($update)) {
			$event->setStatus('No images have been submitted.');
			$message = $event->getStatus();
		} else {
			//Set submitted values
			foreach($update as $key => $type) {
				$event->setFile($type, $_FILES[$type . "_img"]);
				$event->setLink($type, $_POST[$type . "_link"]);
			}

			$created = $event->upload($_POST['event_name']);
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
}
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<link href="style.css" rel="stylesheet" type="text/css" />

<div class="ContentHeaderLeft">

	<div class="ContentHeaderPaddingLeft">COMMUNICATIONS</div>

</div>

<div class="MainPadding">

	<div id="container">
	<p><span class="button"><a href="index.php">Back to Main</a></span></p>

	<?php
	if(isset($message) && !empty($message)) {
		echo "<p>$message</p>";
	}

	if(empty($update) || isset($created) && $created === false) { ?>
	<p class="error"><p>
	<form name="event" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" class="create">
		<label>
			Event Name:
		</label>
		<input type="text" name="event_name" id="event_name" value="<?php if($_POST['event_name']) { echo $_POST['event_name']; } ?>" />

		<label>
			Banner File:
		</label>
		<input type="file" name="banner_img" id="banner_img" />

		<label>
			Banner Link: 
		</label>
		<input type="text" name="banner_link" id="banner_link" value="<?php if($_POST['banner_link']) { echo $_POST['banner_link']; } ?>" />

		<label>
			eBlast File: 
		</label>
		<input type="file" name="eblast_img" id="eblast_img" />

		<label>
			eBlast Link: 
		</label>
		<input type="text" name="eblast_link" id="eblast_link" value="<?php if($_POST['eblast_link']) { echo $_POST['eblast_link']; } ?>" />

		<button type="submit">Submit</button>
	</form>
	<?php } ?>

	<?php
	if($created !== false) {
		if(in_array('banner', $update)) {
	?>
			<p>Banner Image: </p>
			<p><a href="http://ad2orlando.org/<?php echo $bannerInfo['path'] . "/" . $bannerInfo['img']; ?>" target="_blank"><img src="http://ad2orlando.org/<?php echo $bannerInfo['path'] . "/" . $bannerInfo['img']; ?>" width="125"></a></p>
			<p>Banner Link: <a href="<?php echo $bannerInfo['link']; ?>" target="_blank"><?php echo $bannerInfo['link']; ?></a></p>
	<?php
		}

		if(in_array('eblast', $update)) {
	?>
			<p>Eblast Image: </p>
			<p><a href="http://ad2orlando.org/<?php echo $eblastInfo['path'] . "/" . $eblastInfo['img']; ?>" target="_blank"><img src="http://ad2orlando.org/<?php echo $eblastInfo['path'] . "/" . $eblastInfo['img']; ?>" width="125"></a></p>
			<p>Eblast Link: <a href="<?php echo $eblastInfo['link']; ?>" target="_blank"><?php echo $eblastInfo['link']; ?></a></p>
			<p>Eblast File: <a href="http://ad2orlando.org/<?php echo $eblastInfo['path']; ?>/eblast.htm" target="_blank">Click Here</a></p>

	<?php
			echo "Copy this code and paste into MyEmma:<br><br>";
			echo htmlspecialchars($event->getEblastHtml());
		}
	}
	?>
	</div>

</div>

<div class="MainContentClear"></div>

<script>
	$('button').on('click', function() {
		$('.error').text('');

		if($('#event_name').val().length === 0) {
			$('.error').text('An event name has not been entered.');
			return false;
		}

		if($('#banner_img').val().length === 0 && $('#eblast_img').val().length === 0) {
			$('.error').text('No images have been submitted.');
			return false;
		}
	});
</script>
<?php include("../includes/footer.php") ?>
