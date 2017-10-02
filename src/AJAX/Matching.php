<?php

namespace Touriends\Backend\AJAX;
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

        $clause_where = '';
        for ($i = 0; $i < count($user_language); $i++) {
            $lang = $user_language[$i];
            if ($i !== 0)
                $clause_where .= ' OR ';
            $clause_where .= "meta_value = ${lang}";
        }

        // Language filter
        $statement = <<<SQL
SELECT DISTINCT user_id
FROM $wpdb->usermeta
WHERE '$clause_where'
SQL;
        $ret_language_raw = $wpdb->get_col($statement);
        die(json_encode($ret_language_raw));
        $ret_language = [];
        // Flat
        foreach ($ret_language_raw as $row) {
            $ret_language[] = $row[0];
        }
        $ret_theme = $wpdb->get_col("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE (meta_value = '$user_theme')");
        // result의 id를 보면서 from - to 까지 보이게 한다
        date_default_timezone_set('Asia/Seoul');
        #매칭을 한 사용자의 출발일 도착일
        //update_user_meta($user_id, 'stand1', $user_fromDate);
        //update_user_meta($user_id, 'stand2', $user_toDate);
        function array_push_associative(&$arr)
        {
            $ret = 0;
            $args = func_get_args();
            foreach ($args as $arg) {
                if (is_array($arg)) {
                    foreach ($arg as $key => $value) {
                        $arr[$key] = $value;
                        $ret++;
                    }
                } else {
                    $arr[$arg] = "";
                }
            }
            return $ret;
        }
        $chk = 0;
        foreach ($ret_language_raw as $tour_id) {
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
            if ($days > 0 && $chk == 0) {
                $theArray = array($tour_id . "_day" => $days);
                $chk++;
            } else if ($days > 0) {
                $push = array($tour_id . "_day" => $days);
                array_push_associative($theArray, $push);
            }
        }
        update_user_meta($user_id, 'arr_test', $theArray);
    }
}
