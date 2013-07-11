<?php
/*
 * @users $videos Array of videos
 */


	$video_url=elgg_get_site_url() . "mod/file/download.php?file_guid={$vars['guid']}";
	$mime_type=$vars['mime_type'];
		
	$video=get_entity($vars['guid']);
	$mime_type=$video->getMimetype();
	
	//echo "<video width='320' height='240' controls><source src=\"".$video->getFilenameOnFilestore()."\" type=\"".$video->getMimetype()."; codecs=\"avc1.42E01E, mp4a.40.2"."\">Your browser does not support the video tag.</video>";
	echo "<div><video controls id='myvideo'width=640' height='480' controls><source src=\"".$video_url."\" type=$mime_type>Your browser does not support the video tag.</video></div>";