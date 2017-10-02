<?php

namespace Touriends\Backend\AJAX;
use Touriends\Backend\Match;

class Matching extends Base
{
    public static function init()
    {
        parent::registerAction('getMatching', [__CLASS__, 'getMatching']);
    }

    public static function getMatching()
    {
        global $wpdb;
        $user_id = get_current_user_id();
        $user_language = get_user_meta($user_id, 'user_language');
        $user_theme = get_user_meta($user_id, 'user_theme', true);
        $user_fromDate = get_user_meta($user_id, 'user_fromDate', true);
        $user_toDate = get_user_meta($user_id, 'user_toDate', true);
        $cnt_theme = $wpdb->get_var("SELECT count(DISTINCT user_id) FROM $wpdb->usermeta WHERE (meta_value = '$user_theme')");

        // Language Filter
        $clause_where = '';
        for ($i = 0; $i < count($user_language); $i++) {
            $lang = $user_language[$i];
            if ($i !== 0)
                $clause_where .= ' OR ';
            $clause_where .= "meta_value = '${lang}'";
        }

        $statement = <<<SQL
SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE $clause_where LIMIT 12
SQL;
        $ret_language = $wpdb->get_col($statement);

        // Theme Filter
        // $ret_theme = $wpdb->get_col("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE (meta_value = '$user_theme')");
        // result의 id를 보면서 from - to 까지 보이게 한다
        date_default_timezone_set('Asia/Seoul');
        #매칭을 한 사용자의 출발일 도착일
        //update_user_meta($user_id, 'stand1', $user_fromDate);
        //update_user_meta($user_id, 'stand2', $user_toDate);

        $theArray = [];
        foreach ($ret_language as $tour_id) {
            $tour_fromDate = get_user_meta($tour_id, 'user_fromDate', true);
            $tour_toDate = get_user_meta($tour_id, 'user_toDate', true);
            //update_user_meta($user_id, 'comp1', $tour_fromDate);
            //update_user_meta($user_id, 'comp2', $tour_toDate);
            //update_user_meta($user_id, 'touriden', $tour_id);
            //update_user_meta($user_id, 'matching_test', $tour_id);
            #검색될 내용
            $src1 = $user_fromDate;
            $dst1 = $user_toDate;
            $src2 = $tour_fromDate;
            $dst2 = $tour_toDate;
            $srcdate1 = date_create($src1);
            $dstdate1 = date_create($dst1);
            $srcdate2 = date_create($src2);
            $dstdate2 = date_create($dst2);
            $days = 0;
            if ($srcdate1 > $dstdate2 || $srcdate2 > $dstdate1) {#안 겹치는 case
                $days = 0;
                //update_user_meta($user_id, 'diff2', $days);
            } else if ($srcdate1 > $srcdate2 && $dstdate1 > $dstdate2) {#1번 case
                $days = date_diff($srcdate1, $dstdate2)->days + 1;
                //update_user_meta($user_id, 'diff2', $days);
            } else if ($srcdate2 > $srcdate1 && $dstdate2 > $dstdate1) {#2번 case
                $days = date_diff($srcdate2, $dstdate1)->days + 1;
                //update_user_meta($user_id, 'ans2', $days);
            } else if ($srcdate1 > $srcdate2 && $dstdate2 > $dstdate1) {#3번 case
                $days = date_diff($srcdate1, $dstdate1)->days + 1;
                //update_user_meta($user_id, 'diff2', $days);
            } else if ($srcdate1 < $srcdate2 && $dstdate2 < $dstdate1) {#4번 case
                $days = date_diff($srcdate2, $dstdate2)->days + 1;
                //update_user_meta($user_id, 'ans', $days);
            }
            if ($days > 0) {
              // $theArray[$tour_id . '_day'] = $days;
                $theme = get_user_meta($tour_id, 'user_theme', true);
                $user = new Match\UserData($tour_id, $days, $theme);
                $theArray[] = $user;
            }
        }
        usort($theArray, function($a, $b) {
          $res = 0;
          if ($a->schedule > $b->schedule) {
            return 1;
          }
          else if ($b->schedule > $a->schedule) {
            return -1;
          }

          if ($a->schedule === $b->schedule) {
            $a_theme_same = $a->theme === $user_theme;
            $b_theme_same = $b->theme === $user_theme;
            $res = $a_theme_same > $b_theme_same ? 1 : 0;
          }
        });
        die(json_encode($theArray));
    }
}
