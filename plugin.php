<?php

/*
Plugin Name: Backend
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: ENN
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

require_once('vendor/autoload.php');

use Touriends\Backend\Main;

$entry = new Main();
$entry->main();