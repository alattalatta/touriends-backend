<?php

namespace Touriends\Backend\AJAX;

class Matching extends Base {
    public static function init() {
        parent::registerAction('getMatching', [__CLASS__, 'getMatching']);
    }

    public static function getMatching() {
        global $wpdb;
        $user_id = get_current_user_id();
        $user_language = get_user_meta($user_id, 'user_language');
        $user_theme = get_user_meta($user_id, 'user_theme', true);
        $user_fromDate = get_user_meta($user_id, 'user_fromDate', true);
        $user_toDate = get_user_meta($user_id, 'user_toDate', true);

        // 현재 사용자 언어 다 가져옴
        $clause_where = '';
        for ($i = 0; $i < count($user_language); $i++) {
            $lang = $user_language[$i];
            if ($i !== 0)
                $clause_where .= ' OR ';
            $clause_where .= "meta_value = '${lang}'";
        }

        // 12명 까지만 (LIMIT 12)
        $statement = <<<SQL
SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE $clause_where LIMIT 12
SQL;
        $ret_language = $wpdb->get_col($statement);

        date_default_timezone_set('Asia/Seoul');

        $result = [];
        foreach ($ret_language as $tour_id) {
            $tour_fromDate = get_user_meta($tour_id, 'user_fromDate', true);
            $tour_toDate = get_user_meta($tour_id, 'user_toDate', true);

            #검색될 내용
            $my_from = date_create($user_fromDate);
            $my_to = date_create($user_toDate);
            $your_from = date_create($tour_fromDate);
            $your_to = date_create($tour_toDate);
            $days = 0;
            if ($my_from > $your_to || $your_from > $my_to) {#안 겹치는 case
                $days = 0;
            } else if ($my_from > $your_from && $my_to > $your_to) { // 내꺼가 네꺼 다 감쌈 
                $days = date_diff($my_from, $your_to)->days + 1;
            } else if ($your_from > $my_from && $your_to > $my_to) { // 네꺼가 내꺼 다 감쌈
                $days = date_diff($your_from, $my_to)->days + 1;
            } else if ($my_from > $your_from && $your_to > $my_to) { // 내꺼 먼저 네꺼 나중
                $days = date_diff($my_from, $my_to)->days + 1;
            } else if ($my_from < $your_from && $your_to < $my_to) { // 네꺼 먼저 내꺼 나중
                $days = date_diff($your_from, $your_to)->days + 1;
            }
            if ($days > 0) {
                // $theArray[$tour_id . '_day'] = $days;
                $theme = get_user_meta($tour_id, 'user_theme', true);
                $result[] = [
                    'uid' => $tour_id,
                    'theme' => $theme,
                    'days' => $days
                ];
            }
        }

        usort($result, function ($a, $b) {
            // 스케쥴 다르면 잘 맞는 사람 앞으로
            if ($a['days'] !== $b['days']) {
                return $a['days'] > $b['days'] ? -1 : 1;
            }

            // 둘 다 스케쥴 같은데...
            // 테마가 둘 다 나랑 다르면 스킵
            if ($a['theme'] !== $user_theme && $b['theme'] !== $user_theme) {
                return 0;
            }

            // 테마가 같은 사람이 앞으로
            if ($a['theme'] !== $b['theme']) {
                return $a['theme'] === $user_theme ? 1 : -1;
            }
            return 0;
        });
        die(json_encode([
                'success' => true,
                'data' => $result]
        ));
    }
}
