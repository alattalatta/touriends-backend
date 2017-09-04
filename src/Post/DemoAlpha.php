<?php

namespace Touriends\Backend\Post;

class DemoAlpha extends Base {
	public static function init() {
		register_post_type('demo-alpha', [
			'label'         => 'Demo Alpha',
			'public'        => true,
			'menu_position' => 11
		]);
	}
}