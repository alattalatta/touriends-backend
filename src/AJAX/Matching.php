<?php
namespace Touriends\Backend\AJAX;
class Matching extends Base {
    public static function init() {
      parent::registerAction('getMatching', [__CLASS__, 'getMatching']);
    }
    /**
    * 매칭 배열 생성
    */
    public static function getMatching() {
      global $wpdb;
      $user_id  = get_current_user_id();
      $user_language = get_user_meta($user_id,'user_language',true);
      $user_theme = get_user_meta($user_id,'user_theme',true);
      $user_fromDate =  get_user_meta($user_id,'user_fromDate',true);
      $user_toDate =  get_user_meta($user_id,'user_toDate',true);
  //  $cnt_theme =  $wpdb->get_var("SELECT count(DISTINCT user_id) FROM $wpdb->usermeta WHERE (meta_value = '$user_theme')");

      # Language filter
      $statement = <<<SQL
SELECT DISTINCT user_id FROM $wpdb->usermeta where user_id <> $user_id
SQL;

      $tour_db = $wpdb->get_col($statement);
      # Flat

      //$ret_theme = $wpdb->get_col("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE (meta_value = '$user_theme')");

      date_default_timezone_set('Asia/Seoul');//기준시간 세팅

      #매칭을 비교할 사용자의 출발일 도착일

###############################배열 추가########################################
      function array_push_associative(&$arr){
        $args = func_get_args();
        foreach($args as $arg){
          if(is_array($arg)){
            foreach($arg as $key => $value){
              $arr[$key] = $value;
              $ret++;
            }
          }else{
            arr[$arg] = "";
          }
        }
        return $ret;
      }
      $id = "noasand";
      $theArray = array("id_temp" => "0");
      $push = array($id => date_diff($srcdate2,$dstdate2)->days+1);
      array_push_associative($theArray,$push);
###############################배열 추가########################################

      var_dump($tour_db);
      foreach($tour_db as $tour_id){

        $tour_fromDate = get_user_meta(10,'user_fromDate',true);
        $tour_toDate = get_user_meta(10,'user_toDate',true);

        #검색될 내용
        $src1 = $user_fromDate;
        $dst1 = $user_toDate;
        $src2 = $tour_fromDate;
        $dst2 = $tour_toDate;

        //날짜 포멧으로 바꿔줌
        $srcdate1 = date_create($src1);
        $dstdate1 = date_create($dst1);
        $srcdate2 = date_create($src2);
        $dstdate2 = date_create($dst2);
        update_user_meta($user_id, '$srcdate1', $srcdate1);
        update_user_meta($user_id, '$dstdate1', $dstdate1);
        update_user_meta($user_id, '$srcdate2', $srcdate2);
        update_user_meta($user_id, '$dstdate2', $dstdate2);


        $days = 0;

        if($srcdate1>$dstdate2 || $srcdate2 > $dstdate1){#안 겹치는 case
          $days = 0;
          update_user_meta($user_id, 'days_test_case0', $days);
        }
        else if($srcdate1>$srcdate2 && $dstdate1>$dstdate2){#1번 case
          $days = date_diff($srcdate1,$dstdate2)->days+1;
          update_user_meta($user_id, 'days_test_case1', $days);
        }
        else if($srcdate2>$srcdate1 && $dstdate2>$dstdate1){#2번 case
          $chk = date_diff($srcdate2,$dstdate1)->days+1;
          update_user_meta($user_id, 'days_test_case2', $days);
        }
        else if($srcdate1>$srcdate2 && $dstdate2>$dstdate1){#3번 case
          $days = date_diff($srcdate1,$dstdate1)->days+1;
          update_user_meta($user_id, 'days_test_case3', $days);
        }
        else if($srcdate1<$srcdate2 && $dstdate2<$dstdate1){#4번 case
          $days = date_diff($srcdate2,$dstdate2)->days+1;
          update_user_meta($user_id, 'days_test_case4', $days."why");
        }

        if($days > 0){
          update_user_meta($user_id, 'tour_id_test', $tour_id);
          update_user_meta($user_id, 'tour_fromDate_test', $tour_fromDate);
          update_user_meta($user_id, 'tour_toDate_test', $tour_toDate);
        }
      }

      die(json_encode([
          'success' => false,
          'error'   => 'getMatching_duplicate'
        ]));
    }
}
