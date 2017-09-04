<?php

namespace Touriends\Backend\Post;

class DemoBeta extends Base {
	public static function init() {
		register_post_type('demo-beta', [
			'label'         => 'Demo Beta',
			'public'        => true,
			'menu_position' => 11
		]);
	}
}