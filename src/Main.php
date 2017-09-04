<?php

namespace Touriends\Backend;

class Main {
	public $classes = [];
	public function main() {
		add_action('init', function() {
			Post\DemoAlpha::init();
			Post\DemoBeta::init();
		});
		add_action('rest_api_init', function() {
			REST\Demo::init();
		});
	}
}