<?php

namespace Touriends\Backend\AJAX;

use Touriends\Backend\Action\IInit;

abstract class Base implements IInit {
    protected static function registerAction(string $handle, callable $callable) {
        add_action("wp_ajax_${handle}", $callable);
        add_action("wp_ajax_nopriv_${handle}", $callable);
    }
}