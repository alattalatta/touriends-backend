<?php

namespace Touriends\Backend\AJAX;

use Touriends\Backend\User;

class Conversation extends Base {
	public static function init() {
		parent::registerAction('read_check', [__CLASS__, 'read_check']);
		parent::registerAction('sendMessage', [__CLASS__, 'sendMessage']);
		parent::registerAction('getConversation', [__CLASS__, 'getConversation']);
	}

	/**
	 * 메시지 읽음 체크
	 */
	public static function read_check() {
		$user_id = User\Utility::getCurrentUser()->ID; // 현재 user_id
		$you_id = $_POST['other'];

		global $wpdb;
		$table_name = $wpdb->prefix . 'message';

		$statement = <<<SQL
UPDATE $table_name SET read_ck = 1 WHERE re_id = $user_id AND se_id = $you_id
SQL;

		$wpdb->get_col($statement);

		die(json_encode([
			'success' => true
		]));
	}

	/**
	 * 메세지 보내기(DB 저장)
	 */
	public static function sendMessage() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'message';
		$user_id = User\Utility::getCurrentUser()->ID; // 현재 user_id

		$data = [
			're_id'   => $_POST['other'],
			'se_id'   => $user_id,
			'note'    => htmlspecialchars($_POST['note']),
			'read_ck' => 0
		];
		$wpdb->insert($table_name, $data);

		die(json_encode([
			'success' => true
		]));
	}

	/**
	 * 대화내용 불러오기
	 */
	public static function getConversation() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'message';

		$user_id = User\Utility::getCurrentUser()->ID; // 현재 user_id
		$you_id = $_POST['other'];

		$statement = <<<SQL
SELECT mid, se_id, note, (se_id = $user_id) AS is_mine
FROM $table_name
WHERE
	(re_id = $user_id AND se_id = $you_id) OR 
	(re_id = $you_id AND se_id = $user_id)
SQL;
		$messages = $wpdb->get_results($statement, ARRAY_A);

		die(json_encode([
			'success'  => true,
			'messages' => stripslashes_deep($messages)
		]));
	}
}
