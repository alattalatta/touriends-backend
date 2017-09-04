<?php

namespace Touriends\Backend\REST;

class Demo extends Base {
	public static function init() {
		$route = 'demo-api/v1';

		register_rest_route($route, '/demo-alpha', [
			[
				'methods' => 'GET',
				'callback' => [__CLASS__, 'getAlpha']
			]
		]);
		register_rest_route($route, 'demo-beta', [
			[
				'methods' => 'GET',
				'callback' => [__CLASS__, 'getBeta']
			]
		]);
	}

	public static function getAlpha($request) {
		$args = ['post_type' => 'demo-alpha'];
		if (isset($request['id'])) {
			$args['p'] = $request['id'];
		}
		return get_posts($args);
	}

	public static function getBeta($request) {
		return get_posts(['post_type' => 'demo-beta']);
	}
}