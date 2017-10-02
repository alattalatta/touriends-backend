<?php
namespace Touriends\Backend\AJAX;
class Tour extends Base {
    public static function init() {
        parent::registerAction('calendar', [__CLASS__, 'calendar']);
        parent::registerAction('theme', [__CLASS__, 'theme']);
        parent::registerAction('language', [__CLASS__, 'language']);
        parent::registerAction('place', [__CLASS__, 'place']);
    }
    /**
    * 달력
    */
    public static function calendar() {
      $fromDate = $_POST['fromDate']; // MM/DD/YYYY
      $toDate   = $_POST['toDate']; // MM/DD/YYYY
      $user_id = User\Utility::getCurrentUser()->ID;
      update_user_meta($user_id, 'user_fromDate', $fromDate );
      update_user_meta($user_id, 'user_toDate', $toDate );
    }

    public static function theme() {
        $theme = $_POST['theme'];
        $user_id = User\Utility::getCurrentUser()->ID;
        update_user_meta($user_id, 'user_theme', $theme );
      }
      public static function language() {
        $language = $_POST['language'];
        $user_id = User\Utility::getCurrentUser()->ID;
        update_user_meta($user_id, 'user_language', $language );
      }
      public static function place() {
        $place = $_POST['place'];
        $user_id = User\Utility::getCurrentUser()->ID;
        update_user_meta($user_id, 'user_place', $place );
      }
}
