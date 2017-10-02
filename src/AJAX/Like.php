<?php

namespace Touriends\Backend\AJAX;
class Like extends Base
{
    public static function init()
    {
        parent::registerAction('getBookMark', [__CLASS__, 'getBookMark']);
    }

    public static function getBookMark () {
        $like = $_POST['like'];

        $user_id = get_current_user_id();

        add_user_meta($user_id, 'user_like', $like );
    }

    public static function setBookMark () {

        $user_id = get_current_user_id();

        $statement = <<<SQL
        SELECT DISTINCT * FROM $wpdb->usermeta where (meta_value = 'like'  or  meta_value = $lan[1] or meta_value = $lan[2]
SQL;

    }
}

