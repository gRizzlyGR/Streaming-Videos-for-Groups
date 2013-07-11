<?php
// Get input data
$title = htmlspecialchars(get_input('title', '', false), ENT_QUOTES, 'UTF-8');
$description = get_input("description");
$access_id = (int) get_input("access_id");
$container_guid = (int) get_input('container_guid', 0);
$tags = get_input("tags");

if ($container_guid == 0) {
	$container_guid = elgg_get_page_owner_guid();
}

elgg_make_sticky_form('video');

// check if upload failed
if (!empty($_FILES['upload']['name']) && $_FILES['upload']['error'] != 0) {
	register_error(elgg_echo('svg:cannotload'));
	forward(REFERER);
}

// check whether this is a new file or an edit
$new_video = true;
if ($guid > 0) {
	$new_video = false;
}

if ($new_video) {
	// must have a file if a new file upload
	
	if (empty($_FILES['upload']['name'])) {
		$error = elgg_echo('svg:nofile');
		register_error($error);
		forward(REFERER);
	}

	$video=new GroupVideo();
	$video->subtype = "video";
	

	// if no title on new upload, grab filename
	if (empty($title)) {
		$title = htmlspecialchars($_FILES['upload']['name'], ENT_QUOTES, 'UTF-8');
	}

} else {
	// load original file object
	$video=new GroupVideo($guid);
	if (!$video) {
		register_error(elgg_echo('svg:cannotload'));
		forward(REFERER);
	}

	// user must be able to edit file
	if (!$video->canEdit()) {
		register_error(elgg_echo('svg:noaccess'));
		forward(REFERER);
	}

	if (!$title) {
		// user blanked title, but we need one
		$title = $video->title;
	}
}

$video->container_guid = $container_guid;
$video->access_id = $access_id;
$video->title = $title;
$video->description = $description;

$tags = explode(",", $tags);
$video->tags = $tags;


if (isset($_FILES['upload']['name']) && !empty($_FILES['upload']['name'])) {

	$prefix = "video/";

	// if previous file, delete it
	if ($new_video == false) {
		$filename = $video->getFilenameOnFilestore();
		if (file_exists($filename)) {
			unlink($filename);
		}

		// use same filename on the disk - ensures thumbnails are overwritten
		$filestorename = $video->getFilename();
		$filestorename = elgg_substr($filestorename, elgg_strlen($prefix));
	} else {
		$filestorename = elgg_strtolower(time().$_FILES['upload']['name']);
	}

	$video->setFilename($prefix.$filestorename);
	$mime_type=$_FILES['upload']['type'];
	
	$video->setMimeType($mime_type);
	$video->originalfilename = $_FILES['upload']['name'];
	$video->simpletype = file_get_simple_type($mime_type);

	// Open the file to guarantee the directory exists
	$video->open("write");
	$video->close();
	move_uploaded_file($_FILES['upload']['tmp_name'], $video->getFilenameOnFilestore());
}

if ($video->save()) {
	elgg_clear_sticky_form('video');
	system_message(elgg_echo('svg:success'));
	add_to_river('river/object/video/create', 'create', elgg_get_logged_in_user_guid(), $video->guid);
	forward($video->getURL());
	
} else {
	register_error(elgg_echo('svg:fail'));;
}

