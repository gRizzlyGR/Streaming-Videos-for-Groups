<?php
/*
 *
 *
 */
group_gatekeeper();

$owner=elgg_get_page_owner_entity();

$creator=$owner->getOwnerEntity();

$user=elgg_get_logged_in_user_entity();

$title=elgg_echo('svg:group_videos');

//$title=elgg_echo('roles:role:ADMIN_ROLE');

//$title.=" ".roles_get_role()->title;

//$role=roles_get_role()->title;
//
//$title=roles_get_role()->title;
//
//if ($owner->isMember() && strcasecmp(role, elgg_echo('svg:role:group_moderator'))==0) {
//	$title="sei membro e moderatore";
//} else {
//	$title="non sei membro o non sei moderatore";
//}

//Breadcrumbs
elgg_push_breadcrumb($owner->name, 'groups/profile/'.$owner->guid);
elgg_push_breadcrumb(elgg_echo('svg:group_videos'));

$user=elgg_get_logged_in_user_entity();

$options=array(
	'type'=>'object',
	'subtype'=>'video',
	'access_id' => $owner->group_acl,
	'full_view' => false,
	'list_type' => 'gallery',
	'container_guid'=>elgg_get_page_owner_guid(),
);

$content=elgg_list_entities($options);

if (empty($content)) {
	$content=elgg_echo('svg:empty');
}

$vars=array();

//Upload form button only for group owners
if ($creator->guid==$user->guid) {
	elgg_register_menu_item('title', array(
			'name' => 'upload',
			'href' => elgg_get_site_url().'videos/group/'.$owner->guid.'/upload',
			'text' => elgg_echo('svg:upload'),
			'link_class' => 'elgg-button elgg-button-action',
	));
}

$vars=array(
	'content'=>$content,
	'title'=>$title,
);

$body=elgg_view_layout('two_sidebar', $vars);

echo elgg_view_page($title, $body);
