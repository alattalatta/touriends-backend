<?php

namespace Touriends\Backend;

class Main {
	public $classes = [];
	public function main() {
		AJAX\Table::init();
		add_action('init', function() {
			AJAX\Member::init();
			AJAX\Tour::init();
			AJAX\Matching::init();
			AJAX\Like::init();
		});
	}
}
