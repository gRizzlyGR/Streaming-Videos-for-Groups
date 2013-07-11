<?php
// Get input data
$title = htmlspecialchars(get_input('title', '', false), ENT_QUOTES, 'UTF-8');
$description = get_input("description");
$access_id = (int) get_input("access_id");
$container_guid = (int) get_input('container_guid', 0);
$tags = get_input("tags");
$guid=get_input("guid");

if ($container_guid == 0) {
	$container_guid = elgg_get_page_owner_guid();
}

elgg_make_sticky_form('video');


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

$video->container_guid = $container_guid;
$video->access_id = $access_id;
$video->title = $title;
$video->description = $description;

$tags = explode(",", $tags);
$video->tags = $tags;


if ($video->save()) {
	elgg_clear_sticky_form('video');
	system_message(elgg_echo('svg:success'));
	add_to_river('river/object/video/create', 'create', elgg_get_logged_in_user_guid(), $video->guid);
	forward($video->getURL());
	
} else {
	register_error(elgg_echo('svg:fail'));;
}

