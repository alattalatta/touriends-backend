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
select b.* from( select max(mid) as mid, re_id as 'other' from (select * from $table_name where se_id=$user_id order by mid desc) a group by a.re_id UNION select max(mid) as mid, se_id as 'other'from (select * from $table_name where re_id=$user_id order by mid desc) a group by a.se_id ORDER BY `mid` DESC ) b group by b.other ORDER BY b.mid DESC
SQL;
        $messagebox = $wpdb->get_results($statement); //나랑 대화 나눈 사람 ... 대화 번호/읽음여부/상대아이디/내가받은건지 테이블생성

        $statement = <<<SQL
select max(mid) as mid, read_ck from wp_message where re_id = $user_id group by se_id
SQL;
        $readcheck = $wpdb->get_results($statement);

        $newck = [];
        
        foreach ($readcheck as $msb) {
            if ($msb->read_ck == 0) {
                $new_ck = $msb->mid;
                $newck[] = $new_ck;
            }
        }
        die(json_encode([
            'success' => true,
            'box'     => $messagebox,
            'new'     => $newck
        ]));
    }
}
