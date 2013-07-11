<?php
elgg_register_event_handler('init', 'system', 'svg_init');

function svg_init() {
	//elgg_extend_view('group/profile/module', 'svg/url_video');

	$base=elgg_get_plugins_path().'streaming_videos_groups/actions/svg';
	elgg_register_action('svg/upload_form', "$base/save.php");
	elgg_register_action('video/delete', "$base/delete.php");
	elgg_register_action('svg/edit_form', "$base/edit.php");
	
	
	//Handlers
	elgg_register_page_handler('videos', 'streaming_group_videos');
	elgg_register_page_handler('video', 'streaming_group_videos');
	
	elgg_register_entity_url_handler('object', 'video', 'video_url_override');
	
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'svg_owner_block_menu');
	elgg_register_plugin_hook_handler('entity:icon:url', 'object', 'video_icon_url_override');
	elgg_register_plugin_hook_handler('roles:config', 'role', 'roles_config', 700);
		
	//Roles handler
	elgg_register_event_handler('create', 'group', 'group_owner_role_init');
	elgg_register_event_handler('delete', 'group', 'group_owner_role_disable');
	
	//Group videos enabler
	add_group_tool_option('video', elgg_echo('svg:enable'), true);
	
}

//Callback to get the pages
function streaming_group_videos($page, $identifier) {
	$plugin_path=elgg_get_plugins_path();
	$base_path=$plugin_path.'streaming_videos_groups/pages/svg';
	
	switch ($page[0]) {
		case "view":
			set_input('guid', $page[1]);
			require "$base_path/view.php";
			break;
		case "edit":
			set_input('guid', $page[1]);
			require "$base_path/edit.php";
			break;	
	}
	
	switch ($page[2]) {
		case "upload":
			require "$base_path/new_video/form_page.php";
			break;
		case "all":
			require "$base_path/list_videos.php";
			break;			
	}
	
	return true;
}

//Callback to add menu item to group block
function svg_owner_block_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'group') && $params['entity']->video_enable != "no") {
		$url="videos/group/".$params['entity']->guid."/all";
		$item=new ElggMenuItem('group_videos', elgg_echo('svg:group_videos'), $url);
		$return[] = $item;
	}
	return $return;
}

//Callback to handle videos urls
function video_url_override($entity) {
	$title=$entity->title;
	$title=elgg_get_friendly_title($title);
	return "videos/view/".$entity->guid."/".$title;
}

//Callback to change icon
function video_icon_url_override($hook, $type, $returnvalue, $params) {
	$video = $params['entity'];
	$size = $params['size'];
	if (elgg_instanceof($video, 'object', 'video')) {
		
		if ($size == 'large') {
			$ext = '_lrg';
		} else {
			$ext = '';
		}
		
		$url = "mod/file/graphics/icons/video{$ext}.gif";
		$url = elgg_trigger_plugin_hook('file:icon:url', 'override', $params, $url);
		return $url;
	}
}

//Defines custom roles and redefines the behaviour of guests and members
function roles_config($hook_name, $entity_type, $return_value, $params) {
	
	$roles=array(
		VISITOR_ROLE_ROLE => array(
			'title' => 'roles:role:VISITOR_ROLE',
			'extends' => array(),
			'permissions' => array(
				'menus' => array(
					'title::upload'=>array('rule'=>'deny'),
					),
				'pages'=>array(
					'videos/group/{$pageowner_guid}/upload'=>array(
						'rule'=>'deny',
						'forward'=>'videos/group/{$pageowner_guid}/all'
					),
				),
			)
		),
			
		DEFAULT_ROLE => array(
			'title' => 'roles:role:DEFAULT_ROLE',
			'extends' => array(),
			'permissions' => array(
				'menus' => array(
					'title::upload'=>array('rule'=>'deny'),
					),
				'pages'=>array(
					'videos/group/{$pageowner_guid}/upload'=>array(
						'rule'=>'deny',
						'forward'=>'videos/group/{$pageowner_guid}/all'
					),
				),
			)
		),
		
		'group_owner'=>array(
			'title' => 'svg:role:group_owner',
			'extends' => array(ADMIN_ROLE),
			'permissions' => array(
				'menus' => array(
					'topbar::administration' => array('rule' => 'deny'),
				),
				'actions' => array(
					'regexp(/^admin\/((?!user\/ban|user\/unban).)*$/)' => array('rule' => 'deny')
				),		
			)
		),
		
//		'group_moderator'=>array(
//			'title' => 'svg:role:group_moderator',
//			'extends' => array(ADMIN_ROLE),
//			'permissions' => array(
//				'menus'=>array(
//					'topbar::administration' => array('rule' => 'deny'),
//				),
//				'pages' => array(
//					'regexp(/^(settings\/user\/((?!{$self_username}).)+)$/)'=>array(
//						'rule'=>'deny',
//						'forward'=>'dashboard',
//					)
//				),				
//				'actions' => array(
//					'regexp(/^admin\/((?!user\/ban|user\/unban).)*$/)' => array('rule' => 'deny')
//				),
//			)
//		)
	);
	
	if (!is_array($return_value)) {
		return $roles;
	} else {
		return array_merge($return_value, $roles);
	}

}

//Sets the group owner role for group creators, except the admin
function group_owner_role_init($event, $object_type, $object) {
	$user=elgg_get_logged_in_user_entity();
	
	$role1=roles_get_role()->title;
	$role2=elgg_echo('roles:role:ADMIN_ROLE');
	
	if (strcasecmp($role1, $role2)!= 0) {
		$user->makeAdmin();
		roles_set_role(roles_get_role_by_name('group_owner'));		
	}
}

//Revoke group owner role from the logged user
function group_owner_role_disable($event, $object_type, $object) {
	$user=elgg_get_logged_in_user_entity();
	
	$role1=roles_get_role()->title;
	$role2=elgg_echo('roles:role:ADMIN_ROLE');
	
	if (strcasecmp($role1, $role2)!= 0) {
		$user->removeAdmin();
		roles_set_role(roles_get_role_by_name(elgg_echo('roles:role:DEFAULT_ROLE')));	
	}	
}