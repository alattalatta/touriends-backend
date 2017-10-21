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
        $table_name = $wpdb->prefix . 'message';

        //$wpdb->update($table_name, 1, 'read_ck', $format = null, 're_id' == $user_id AND 'se_id' == $you_id);
        //$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->$table_name SET read_ck = 1 WHERE re_id == $user_id AND se_id == $you_id);

        $statement = <<<SQL
UPDATE $table_name SET read_ck = 1 WHERE re_id = $user_id AND se_id = $you_id
SQL;

        $wpdb->get_col($statement);

        die(json_encode([
            'success' => true
        ]));
    }

    /**
     * 메세지 보내기(DB저장)
     */
    public static function sendMessage() {
        $user_id = User\Utility::getCurrentUser()->ID; // 현재 user_id
        // $you_id = $_POST['other'];

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
        $table_name = $wpdb->prefix . 'message';
        //$messages[] = $wpdb->get_results("SELECT * FROM $table_name WHERE (re_id = $user_id AND se_id = $you_id) OR (re_id = $you_id AND se_id = $user_id)");

        $messages = $wpdb->get_col("SELECT mid FROM $table_name WHERE (re_id = $user_id AND se_id = $you_id) OR (re_id = $you_id AND se_id = $user_id)");

        date_default_timezone_set('Asia/Seoul');

        $text = [];

        foreach ($messages as $msg) {
            $se_id = $wpdb->get_var("SELECT se_id FROM $table_name WHERE mid = $msg");
            $time = $wpdb->get_var("SELECT sendTime FROM $table_name WHERE mid = $msg");
            $note = $wpdb->get_var("SELECT note FROM $table_name WHERE mid = $msg");

            if ($se_id == $user_id) {
                $text[$msg] = [
                    'time' => $time,
                    'note' => $note,
                    'sort' => 'right'
                ];
            } else {
                $text[$msg] = [
                    'time' => $time,
                    'note' => $note,
                    'sort' => 'left'
                ];
            }
        }
        
        function cmp($a, $b) {
            return strcmp($a["time"], $b["time"]);
        }
        usort($text[], "cmp");

        die(json_encode([
            'success' => true,
            'sort' => $text,
            'message' => $messages
        ]));
    }
}
