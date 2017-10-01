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
SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE meta_value = '$user_language' AND user_id <> $user_id
SQL;
      $ret_language_raw = $wpdb->get_col($statement);
      $ret_language = [];

      # Flat
  		foreach ($ret_language_raw as $row) {
  		  $ret_language[] = $row[0];
  		}

  //  $ret_theme = $wpdb->get_col("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE (meta_value = '$user_theme')");
      // result의 id를 보면서 from - to 까지 보이게 한다
      date_default_timezone_set('Asia/Seoul');//기준시간 세팅

      #매칭을 비교할 사용자의 출발일 도착일
      foreach($ret_language as $tour_id){
        $tour_fromDate = get_user_meta($tour_id,'user_fromDate',true);
        $tour_toDate = get_user_meta($tour_id,'user_toDate',true);

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
        $user_arr = array("temp","test");

        if($srcdate1>$dstdate2 || $srcdate2 > $dstdate1){#안 겹치는 case
          array_push($user_arr,$tour_id,0);
          update_user_meta($user_id, 'matching_test', $user_arr[0]);
        }
        else if($srcdate1>$srcdate2 && $dstdate1>$dstdate2){#1번 case
          array_push($user_arr,$tour_id,date_diff($srcdate1,$dstdate2)->days+1);
          update_user_meta($user_id, 'matching_test',  $user_arr[0]);
        }
        else if($srcdate2>$srcdate1 && $dstdate2>$dstdate1){#2번 case
          array_push($user_arr,$tour_id,date_diff($srcdate2,$dstdate1)->days+1);
          update_user_meta($user_id, 'matching_test',  $user_arr[0]);
        }
        else if($srcdate1>$srcdate2 && $dstdate2>$dstdate1){#3번 case
          array_push($user_arr,$tour_id,date_diff($srcdate1,$dstdate1)->days+1);
          update_user_meta($user_id, 'matching_test',  $user_arr[0]);
        }
        else{#4번 case
          array_push($user_arr,$tour_id,date_diff($srcdate2,$dstdate2)->days+1);
          update_user_meta($user_id, 'matching_test',  $user_arr[0]);
        }
      }
      if (get_user_by('getMatching', $getMatching)) {
        die(json_encode([
          'success' => false,
          'error'   => 'getMatching_duplicate'
        ]));
      }
    }
}
