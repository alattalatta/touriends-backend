<?php
namespace Touriends\Backend\AJAX;
class Recommend extends Base {
    public static function init() {
        parent::registerAction('getTheme', [__CLASS__, 'getTheme']);
        parent::registerAction('getLanguage', [__CLASS__, 'getLanguage']);
        parent::registerAction('getCalender', [__CLASS__, 'getCalender']);        
    }
    /**
    * 투렌즈 추천 
    */
    public static function getTheme() {
      $user_id  = get_current_user_id();
      $user_theme = get_user_meta($user_id,'user_theme',true);
      add_user_meta($user_id, 'user_test', $user_theme );
    }    
    public static function getLanguage() {
        $user_id  = get_current_user_id();
        $user_language = get_user_meta($user_id,'user_language',true);
    }
    public static function  getCalender() {
        $user_id  = get_current_user_id();
        $user_fromDate =  get_user_meta($user_id,'user_fromDate',true);
        $user_toDate =  get_user_meta($user_id,'user_toDate',true);
      }
}
