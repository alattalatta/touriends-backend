<?php
namespace Touriends\Backend\AJAX;
use Touriends\Backend\Table;
use Touriends\Backend\User;
class Testsj extends Base {
    public static function init() {
        parent::registerAction('testsj', [__CLASS__, 'testsj']);
    }
    public static function testsj() {
        
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
