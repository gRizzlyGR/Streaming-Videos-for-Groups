<?php
$video=get_entity(get_input('guid'));

$owner=elgg_get_page_owner_entity();

$title=$video->title;

//Breadcrumbs
elgg_push_breadcrumb($owner->name, 'groups/profile/'.$owner->guid);
elgg_push_breadcrumb(elgg_echo('svg:group_videos'), 'videos/group/'.$owner->guid.'/all');
elgg_push_breadcrumb($title);


$vars=array(
	'guid'=>$video->guid,
	'mime_type'=>$video->getMimetype(),
);

$content = elgg_view_entity($video, array('full_view' => true));
$content .= elgg_view('svg/player', $vars);
$content .= elgg_view_comments($video);


$vars=array(
	'content'=>$content,
	'title'=>$title,
	'filter'=>'',
);

$body=elgg_view_layout('two_sidebar', $vars);

echo elgg_view_page($title, $body);