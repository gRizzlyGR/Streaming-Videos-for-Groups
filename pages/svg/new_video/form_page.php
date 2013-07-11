<?php
group_gatekeeper();

$owner=elgg_get_page_owner_entity();

$title=elgg_echo('svg:upload_form');

//Breadcrumbs
elgg_push_breadcrumb($owner->name, 'groups/profile/'.$owner->guid);
elgg_push_breadcrumb(elgg_echo('svg:group_videos'), 'videos/group/'.$owner->guid.'/all');
elgg_push_breadcrumb($title);


$content.=elgg_view_form('svg/upload_form', array('enctype' => 'multipart/form-data'));

$vars=array(
	'content'=>$content,
	'title'=>$title,
);

$body=elgg_view_layout('two_sidebar', $vars);

echo elgg_view_page($title, $body);