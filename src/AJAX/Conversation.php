<?php

namespace Touriends\Backend\AJAX;

use Touriends\Backend\User;

class Conversation extends Base {
    public static function init() {
        parent::registerAction('readCheck', [__CLASS__, 'readCheck']);
        parent::registerAction('sendMessage', [__CLASS__, 'sendMessage']);
        parent::registerAction('conversation', [__CLASS__, 'conversation']);
    }

    /**
     * 메시지 읽음 체크
     */
    public static function readCheck() {
        $user_id = User\Utility::getCurrentUser()->ID; // 현재 user_id
        $you_id = $_POST['other'];

        global $wpdb;
        $wpdb->update(mesaage, 1, read_ck, $format = null, re_id == $user_id AND se_id == $you_id);
//        $statement = <<<SQL
//UPDATE mesaage SET Read_ck = 1 WHERE re_id = $my_id AND se_id = $you_id;
//SQL;

        die(json_encode([
            'success' => true
        ]));
    }

    /**
     * 메세지 보내기(DB저장)
     */
    public static function sendMessage() {
        $user_id = User\Utility::getCurrentUser()->ID; // 현재 user_id
        $you_id = $_POST['other'];

        global $wpdb;

        if (isset($_POST['submit'])) {
            $table_name = $wpdb->prefix . 'message';

            $data = array(
                're_id' => $_POST['other'],
                'se_id' => $user_id,
                'note' => $_POST['note'],
                'read_ck' => 0
            );
            $wpdb->insert($table_name, $data);
        }
        die(json_encode([
            'success' => true
        ]));
    }

    /**
     * 대화내용 불러오기
     */
    public static function conversation() {
        $user_id = User\Utility::getCurrentUser()->ID; // 현재 user_id
        $you_id = $_POST['other'];

        global $wpdb;

        $statement = <<<SQL
SELECT * FROM $wpdb->wp_message WHERE (re_id = $user_id AND se_id = $you_id) OR (re_id = $you_id AND se_id = $user_id);
SQL;
        $messages = $wpdb->get_col($statement);

        date_default_timezone_set('Asia/Seoul');

        $note = [];

        foreach ($messages as $msg) {
            if ($msg->se_id == $user_id) {
                $note[] = [
                    'time' => $msg->time,
                    'note' => $msg->note,
                    'sort' => 'right'
                ];
            } else {
                $note[] = [
                    'time' => $msg->time,
                    'note' => $msg->note,
                    'sort' => 'left'
                ];
            }
        }

        function cmp($a, $b) {
            return strcmp($a["time"], $b["time"]);
        }

        usort($time, "cmp");

        die(json_encode([
            'success' => true
        ]));
    }
}
