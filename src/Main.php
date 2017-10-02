<?php

namespace Touriends\Backend;

class Main {
	public $classes = [];
	public function main() {
		add_action('init', function() {
			AJAX\Member::init();
			AJAX\Tour::init();
			AJAX\Matching::init();
		});
	}
}
