<?php


//Initialize video subtype
if (get_subtype_id('object', 'video')) {
	update_subtype('object', 'video', 'GroupVideo');
} else {
	add_subtype('object', 'video', 'GroupVideo');
}