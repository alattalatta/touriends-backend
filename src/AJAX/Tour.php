<?php
namespace Touriends\Backend\AJAX;
class Tour extends Base {
    public static function init() {
      parent::registerAction('calender', [__CLASS__, 'calender']);
      parent::registerAction('theme', [__CLASS__, 'theme']);
      parent::registerAction('language', [__CLASS__, 'language']);
      parent::registerAction('place', [__CLASS__, 'place']);
    }

    /**
    * 달력
    */
    public static function calender() {
      $fromDate = $_POST['fromDate']; // MM/DD/YYYY
      $toDate   = $_POST['toDate']; // MM/DD/YYYY
      $user_id  = get_current_user_id();
      update_user_meta($user_id, 'user_fromDate', $fromDate );
      update_user_meta($user_id, 'user_toDate', $toDate );

      if (get_user_by('calender', $calender)) {
  			die(json_encode([
  				'success' => false,
  				'error'   => 'calender_duplicate'
  			]));
  		}
    }

    /**
    *  테마
    */
    public static function theme() {
      $theme = $_POST['theme'];
      $user_id  = get_current_user_id();
      update_user_meta($user_id, 'user_theme', $theme );

      if (get_user_by('theme', $theme)) {
        die(json_encode([
          'success' => false,
          'error'   => 'theme_duplicate'
        ]));
      }
    }

    /**
    * 언어
    */
    public static function language() {
      $language = $_POST['language'];
      $user_id  = get_current_user_id();
      update_user_meta($user_id, 'user_language', $language );

      if (get_user_by('language', $language)) {
        die(json_encode([
          'success' => false,
          'error'   => 'language_duplicate'
          ]));
      }
    }

    /**
    * 장소
    */
    public static function place() {
      $place = $_POST['place'];
      $user_id  = get_current_user_id();
      update_user_meta($user_id, 'user_place', $place );

      if (get_user_by('place', $place)) {
        die(json_encode([
          'success' => false,
          'error'   => 'place_duplicate'
          ]));
      }
    }
}
