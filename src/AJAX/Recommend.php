<?php
namespace Touriends\Backend\AJAX;
class Recommend extends Base {
    public static function init() {
        parent::registerAction('getTheme', [__CLASS__, 'getTheme']);
        parent::registerAction('getCalender', [__CLASS__, 'getCalender']);
        parent::registerAction('getLanguage', [__CLASS__, 'getLanguage']);
    }
    /**
    * 투렌즈 추천 
    */
    public static function getTheme() {
      $user_id  = get_current_user_id();
      $user_theme = $wpdb->get_var("SELECT meta_value FROM $wpdb->usermeta WHERE (meta_key = 'user_theme' AND user_id = $user_id)");
    //   console_log($user_id);
    //   console_log($user_theme);
    }    
    public static function getLanguae() {
        $user_id  = get_current_user_id();
        $user_Language = $wpdb->get_var("SELECT meta_value FROM $wpdb->usermeta WHERE (meta_key = 'user_language' AND user_id = $user_id)");
      }  
    public static function  getCalender() {
        $user_id  = get_current_user_id();
        $user_fromDate = $wpdb->get_var("SELECT meta_value FROM $wpdb->usermeta WHERE (meta_key = 'user_fromDate' AND user_id = $user_id)");
        $user_toDate = $wpdb->get_var("SELECT meta_value FROM $wpdb->usermeta WHERE (meta_key = 'user_toDate' AND user_id = $user_id)");
      }
    //   function console_log( $data ){
    //     echo '<script>';
    //     echo 'console.log('. json_encode( $data ) .')';
    //     echo '</script>';
    //   }
}
?>
