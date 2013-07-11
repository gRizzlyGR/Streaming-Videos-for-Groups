<?php
/**
 * View for video objects
 *
 * @package video
 */

$full = elgg_extract('full_view', $vars, FALSE);
$video = elgg_extract('entity', $vars, FALSE);

if (!$video) {
	return TRUE;
}

$owner = $video->getOwnerEntity();
$submit_video_GUID = $video->friend; 
$user_GUID= $_SESSION['user']->getGUID();
$owner_video_GUID = $owner->getGUID();
$container = $video->getContainerEntity();
$categories = elgg_view('output/categories', $vars);
$excerpt = $video->excerpt;
$friend = $video->friend; 

if (!$excerpt) {
	$excerpt = elgg_get_excerpt($video->description);
}

if ($user_GUID == $owner_video_GUID) {
	$fri = $_SESSION['user']->getFriends("", 1000);
	foreach ($fri as $f) {
		$name[$f->guid] = $f->name;
	}
	$submit_text = elgg_echo($name[$friend]);
}

if ($user_GUID == $submit_video_GUID) {
	$submit_text = elgg_echo($_SESSION['user']->name);
}

if (($user_GUID != $owner_video_GUID) && ($user_GUID != $submit_video_GUID)) {
	$user = get_entity($submit_video_GUID);
	$submit_text = elgg_echo($user->name);
}

$owner_icon = elgg_view_entity_icon($owner, 'tiny');
//$owner_link = elgg_view('output/url', array(
//	'href' => "video/owner/$owner->username",
//	'text' => $owner->name,
//	'is_trusted' => true,
//));
$author_text = elgg_echo('byline', array($owner->name));
$date = elgg_view_friendly_time($video->time_created);


if ($video->comments_on != 'Off') {
	$comments_count = $video->countComments();

	if ($comments_count != 0) {
		$text = elgg_echo("comments") . " ($comments_count)";
		$comments_link = elgg_view('output/url', array(
			'href' => $video->getURL() . '#video-comments',
			'text' => $text,
			'is_trusted' => true,
		));
	} else {
		$comments_link = '';
	}
} else {
	$comments_link = '';
}

$metadata = elgg_view_menu('entity', array(
	'entity' => $vars['entity'],
	'handler' => 'video',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

$subtitle = "$author_text "." To "."$submit_text"."<br>"."$date $comments_link $categories";

if (elgg_in_context('widgets')) {
	$metadata = '';
}

if ($full) {

	$body = elgg_view('output/longtext', array(
		'value' => $video->description,
		'class' => 'video-post',
	));

	$params = array(
		'entity' => $video,
		'title' => false,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
	);
	$params = $params + $vars;
	$summary = elgg_view('object/elements/summary', $params);

	echo elgg_view('object/elements/full', array(
		'summary' => $summary,
		'icon' => $owner_icon,
		'body' => $body,
	));

} else {


	$params = array(
		'entity' => $video,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'content' => $excerpt,
	);
	$params = $params + $vars;
	$list_body = elgg_view('object/elements/summary', $params);

	echo elgg_view_image_block($owner_icon, $list_body);
}

if ( $user_GUID == $submit_video_GUID) {
	$submit_text = elgg_echo($_SESSION['user']->name);
}
	