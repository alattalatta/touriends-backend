<?php

namespace Touriends\Backend\Post;

class Activity extends Base {
	public static function init() {
		register_post_type('activity', [
			'label'         => 'Activity',
			'public'        => true,
			'menu_position' => 11
		]);
	}
}
