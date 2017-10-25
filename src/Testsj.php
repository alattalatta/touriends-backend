<?php

namespace Touriends\Backend\AJAX;

use Touriends\Backend\Table;
use Touriends\Backend\User;

class Testsj extends Base {
    public static function init() {
        parent::registerAction('Testsj', [__CLASS__, 'Testsj']);
    }
    public static function Testsj() {
        
        $a = "타이틀";
        $b = "url";
        $c = "ad";
        die(json_encode([
            'success'  => true,
            'title' => $a,
            'url' => $b,
            'ad' => $c
        ]));
        }
}
