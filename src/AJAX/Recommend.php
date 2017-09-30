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
        add_user_meta($user_id, 'user_test', $user_theme );
        $count =  $wpdb->get_var("SELECT count(DISTINCT user_id) FROM $wpdb->usermeta WHERE (meta_value = '$user_theme')");
        $result = $wpdb->get_row("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE (meta_value = '$user_theme')",ARRAY_N);
        add_user_meta($user_id, 'user_test1', $result);
        add_user_meta($user_id, 'user_count', $count );
    }    
    /**
    * 언어 받아오기
    */
    public static function getLanguage() {
        global $wpdb;
        $user_id  = get_current_user_id();
        $user_language = get_user_meta($user_id,'user_language',true);
        $user_theme = $wpdb->get_var("SELECT user_id FROM $wpdb->usermeta WHERE (meta_value = $user_theme)");
    }
    /**
    * 일정 받아오기
    */
    public static function  getCalender() {
        global $wpdb;
        $user_id  = get_current_user_id();
        $user_fromDate =  get_user_meta($user_id,'user_fromDate',true);
        $user_toDate =  get_user_meta($user_id,'user_toDate',true);
        $user_theme = $wpdb->get_var("SELECT user_id FROM $wpdb->usermeta WHERE (meta_value = $user_theme)");
      }
}
