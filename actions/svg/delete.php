<?php

$video_guid = get_input('guid');
$video = get_entity($video_guid);

if (elgg_instanceof($video, 'object', 'video') && $video->canEdit()) {
	$container = get_entity($video->container_guid);
	if ($video->delete()) {
		system_message(elgg_echo('svg:delete_success'));
		if (elgg_instanceof($container, 'group')) {
			forward("videos/group/$container->guid/all");
		} //else {
//			forward("videos/owner/$container->username");
//		}
	} else {
		register_error(elgg_echo('svg:cannot_delete'));
	}
} else {
	register_error(elgg_echo('svg:not_found'));
}

forward(REFERER);