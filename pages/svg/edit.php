<?php
group_gatekeeper();

$owner=elgg_get_page_owner_entity();
$video=get_entity(get_input('guid'));

$title=elgg_echo('svg:edit_form');

//Breadcrumbs
elgg_push_breadcrumb($owner->name, 'groups/profile/'.$owner->guid);
elgg_push_breadcrumb(elgg_echo('svg:group_videos'), 'videos/group/'.$owner->guid.'/all');
elgg_push_breadcrumb($title);

$vars=array(
	'guid'=>$video->guid,
	'title'=>$video->title,
	'description'=>$video->description,
	'tags'=>$video->tags,
	'access_id'=>$video->access_id,
);

$content.=elgg_view_form('svg/edit_form', null, $vars);

$vars=array(
	'content'=>$content,
	'title'=>$title,
);

$body=elgg_view_layout('two_sidebar', $vars);

echo elgg_view_page($title, $body);