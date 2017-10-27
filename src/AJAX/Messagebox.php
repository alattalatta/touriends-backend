<?php

namespace Touriends\Backend\AJAX;

use Touriends\Backend\User;

class Messagebox extends Base {
    public static function init() {
        parent::registerAction('showMessage', [__CLASS__, 'showMessage']);
    }
    /**
     * 대화나눈 사람들 목록 보여주는 함수
     */
    public static function showMessage() {
        global $wpdb;
        $user_id = User\Utility::getCurrentUser()->ID;//현재user_id
        $table_name = $wpdb->prefix . 'message';
        $statement = <<<SQL
    select b.* from( select max(mid) as mid, read_ck, re_id as 'who', 0 as 'test' from (select * from  $table_name where se_id=$user_id order by mid desc) a group by re_id UNION select max(mid) as mid, read_ck, se_id as 'who', 1 as 'test' from (select * from  $table_name where re_id=$user_id order by mid desc) a group by se_id ORDER BY `mid` DESC) b group by who
SQL;
        $messagebox = $wpdb->get_results($statement); //나랑 대화 나눈 사람 ... 대화 번호/읽음여부/상대아이디/내가받은건지 테이블생성

        foreach ($messagebox as $msb) {
            if ($msb->test == 1) {
                $not_new = $msb->read_ck;

                $newck[] = [
                    'newmsg' => $not_new
                ];
            } else {
                $newck[] = [
                    'newmsg' => '0'
                ];
            }
        }
        die(json_encode([
            'success' => true,
            'box' => $messagebox,
            'new' => $newck
        ]));
    }
}
