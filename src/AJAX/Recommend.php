<?php
namespace Touriends\Backend\AJAX;
class Recommend extends Base {
    public static function init() {
        parent::registerAction('getTheme', [__CLASS__, 'getTheme']);
        parent::registerAction('getLanguage', [__CLASS__, 'getLanguage']);
        parent::registerAction('getCalender', [__CLASS__, 'getCalender']);
    }
    /**
    * 테마 받아오기
    */
    public static function getTheme() {
        global $wpdb;
        $user_id  = get_current_user_id();
        $user_theme = get_user_meta($user_id,'user_theme',true);
        $count = $wpdb->get_var("SELECT count(DISTINCT user_id) FROM $wpdb->usermeta WHERE (meta_value = '$user_theme')");
        $result = $wpdb->get_results("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE (meta_value = '$user_theme')",ARRAY_N);
    }
    /**
    * 언어 받아오기
    */
    public static function getLanguage() {
        global $wpdb;
        $user_id  = get_current_user_id();
        $user_language = get_user_meta($user_id,'user_language',true);
        $count = $wpdb->get_var("SELECT count(DISTINCT user_id) FROM $wpdb->usermeta WHERE (meta_value = '$user_language')");
        $result = $wpdb->get_results("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE (meta_value = '$user_language')",ARRAY_N);
        // result의 id를 보면서 from - to 까지 보이게 한다

        for($idx =0;$idx<$count;$idx++){
          $tour_id = $result[$idx];
          $tour_fromDate = get_user_meta($tour_id,'user_fromDate',true);
          $tour_toDate = get_user_meta($tour_id,'user_toDate',true);
        }


    }
    /**
    * 일정 받아오기
    */
    public static function  getCalender() {
        global $wpdb;
        $user_id  = get_current_user_id();
        $user_fromDate =  get_user_meta($user_id,'user_fromDate',true);
        $user_toDate =  get_user_meta($user_id,'user_toDate',true);
        $result = $wpdb->get_results("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE (meta_value = '$user_language')",ARRAY_N);
      }
}
